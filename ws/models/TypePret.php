<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM types_pret ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM types_pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        
        // Validation des données
        if (empty($data->nom)) {
            throw new Exception("Le nom du type de prêt est requis");
        }
        
        if ($data->taux_annuel < 0 || $data->taux_annuel > 100) {
            throw new Exception("Le taux annuel doit être entre 0 et 100%");
        }
        
        if ($data->montant_min >= $data->montant_max) {
            throw new Exception("Le montant minimum doit être inférieur au montant maximum");
        }
        
        $stmt = $db->prepare("INSERT INTO types_pret (nom, taux_annuel, duree_max_mois, montant_min, montant_max) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom, 
            $data->taux_annuel, 
            $data->duree_max_mois, 
            $data->montant_min, 
            $data->montant_max
        ]);
        
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        
        // Validation des données
        if (empty($data->nom)) {
            throw new Exception("Le nom du type de prêt est requis");
        }
        
        if ($data->taux_annuel < 0 || $data->taux_annuel > 100) {
            throw new Exception("Le taux annuel doit être entre 0 et 100%");
        }
        
        if ($data->montant_min >= $data->montant_max) {
            throw new Exception("Le montant minimum doit être inférieur au montant maximum");
        }
        
        // Vérifier que l'enregistrement existe
        $exists = self::getById($id);
        if (!$exists) {
            throw new Exception("Type de prêt non trouvé");
        }
        
        $stmt = $db->prepare("UPDATE types_pret SET nom = ?, taux_annuel = ?, duree_max_mois = ?, montant_min = ?, montant_max = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([
            $data->nom, 
            $data->taux_annuel, 
            $data->duree_max_mois, 
            $data->montant_min, 
            $data->montant_max, 
            $id
        ]);
        
        // Vérifier si la mise à jour a réussi
        if ($stmt->rowCount() === 0) {
            throw new Exception("Aucune modification effectuée");
        }
    }

    public static function delete($id) {
        $db = getDB();
        
        // Vérifier que l'enregistrement existe
        $exists = self::getById($id);
        if (!$exists) {
            throw new Exception("Type de prêt non trouvé");
        }
        
        $stmt = $db->prepare("DELETE FROM types_pret WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Impossible de supprimer le type de prêt");
        }
    }
}
?>