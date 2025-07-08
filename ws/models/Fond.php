<?php
require_once __DIR__ . '/../db.php';

class Fond {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM fonds ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM fonds WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getSoldeTotal() {
        $db = getDB();
        $stmt = $db->query("SELECT COALESCE(SUM(montant), 0) as solde_total FROM fonds");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['solde_total'];
    }

    public static function create($data) {
        $db = getDB();
        
        // Validation des données
        if (empty($data->montant) || $data->montant <= 0) {
            throw new Exception("Le montant doit être supérieur à 0");
        }
        
        if (empty($data->date_ajout)) {
            throw new Exception("La date d'ajout est requise");
        }
        
        // Récupérer le solde actuel
        $soldeActuel = self::getSoldeTotal();
        
        // Calculer le nouveau solde
        $nouveauSolde = $soldeActuel + $data->montant;
        
        $stmt = $db->prepare("INSERT INTO fonds (montant, date_ajout, description, solde_total) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->montant, 
            $data->date_ajout, 
            $data->description ?? '', 
            $nouveauSolde
        ]);
        
        // Recalculer tous les soldes pour maintenir la cohérence
        self::recalculerSoldes();
        
        return [
            'id' => $db->lastInsertId(),
            'nouveau_solde' => self::getSoldeTotal()
        ];
    }

    public static function update($id, $data) {
        $db = getDB();
        
        // Validation des données
        if (empty($data->montant) || $data->montant <= 0) {
            throw new Exception("Le montant doit être supérieur à 0");
        }
        
        if (empty($data->date_ajout)) {
            throw new Exception("La date d'ajout est requise");
        }
        
        // Vérifier que l'enregistrement existe
        $exists = self::getById($id);
        if (!$exists) {
            throw new Exception("Fond non trouvé");
        }
        
        // Mettre à jour le fond
        $stmt = $db->prepare("UPDATE fonds SET montant = ?, date_ajout = ?, description = ? WHERE id = ?");
        $stmt->execute([
            $data->montant, 
            $data->date_ajout, 
            $data->description ?? '', 
            $id
        ]);
        
        // Vérifier si la mise à jour a réussi
        if ($stmt->rowCount() === 0) {
            throw new Exception("Aucune modification effectuée");
        }
        
        // Recalculer tous les soldes
        self::recalculerSoldes();
        
        return self::getSoldeTotal();
    }

    public static function delete($id) {
        $db = getDB();
        
        // Vérifier que l'enregistrement existe
        $exists = self::getById($id);
        if (!$exists) {
            throw new Exception("Fond non trouvé");
        }
        
        $stmt = $db->prepare("DELETE FROM fonds WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Impossible de supprimer le fond");
        }
        
        // Recalculer tous les soldes
        self::recalculerSoldes();
        
        return self::getSoldeTotal();
    }
    
    private static function recalculerSoldes() {
        $db = getDB();
        $stmt = $db->query("SELECT id, montant FROM fonds ORDER BY created_at ASC");
        $fonds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $soldeTotal = 0;
        foreach ($fonds as $fond) {
            $soldeTotal += $fond['montant'];
            $updateStmt = $db->prepare("UPDATE fonds SET solde_total = ? WHERE id = ?");
            $updateStmt->execute([$soldeTotal, $fond['id']]);
        }
    }

    public static function deduireMontant($montant, $date, $description) {
    $db = getDB();
    
    // Insérer une entrée négative
    $montant = -$montant; // Montant négatif pour déduction
    $soldeActuel = self::getSoldeTotal();
    $nouveauSolde = $soldeActuel + $montant;
    
    $stmt = $db->prepare("INSERT INTO fonds (montant, date_ajout, description, solde_total) VALUES (?, ?, ?, ?)");
    $stmt->execute([$montant, $date, $description, $nouveauSolde]);
    
    // Recalculer les soldes pour cohérence
    self::recalculerSoldes();
    
    return $nouveauSolde;
}
}