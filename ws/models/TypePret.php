<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        try {
            $db = getDB();
            $stmt = $db->query("SELECT * FROM types_pret ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur BD getAll TypePret: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération des types de prêt");
        }
    }

    public static function getById($id) {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT * FROM types_pret WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur BD getById TypePret: " . $e->getMessage());
            throw new Exception("Erreur lors de la récupération du type de prêt");
        }
    }

    public static function create($data) {
        try {
            $db = getDB();
            
            // Validation des données
            if (empty($data->nom)) {
                throw new Exception("Le nom du type de prêt est requis");
            }
            
            if ($data->taux_annuel < 0 || $data->taux_annuel > 100) {
                throw new Exception("Le taux annuel doit être entre 0 et 100%");
            }
            
            if ($data->duree_max_mois <= 0) {
                throw new Exception("La durée maximale doit être supérieure à 0");
            }
            
            if ($data->montant_min < 0 || $data->montant_max < 0) {
                throw new Exception("Les montants ne peuvent pas être négatifs");
            }
            
            if ($data->montant_min >= $data->montant_max) {
                throw new Exception("Le montant minimum doit être inférieur au montant maximum");
            }
            
            // Vérifier si un type avec le même nom existe déjà
            $stmt = $db->prepare("SELECT COUNT(*) FROM types_pret WHERE nom = ?");
            $stmt->execute([$data->nom]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Un type de prêt avec ce nom existe déjà");
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
        } catch (PDOException $e) {
            error_log("Erreur BD create TypePret: " . $e->getMessage());
            throw new Exception("Erreur lors de la création du type de prêt");
        }
    }

    public static function update($id, $data) {
        try {
            $db = getDB();
            
            // Validation des données
            if (empty($data->nom)) {
                throw new Exception("Le nom du type de prêt est requis");
            }
            
            if ($data->taux_annuel < 0 || $data->taux_annuel > 100) {
                throw new Exception("Le taux annuel doit être entre 0 et 100%");
            }
            
            if ($data->duree_max_mois <= 0) {
                throw new Exception("La durée maximale doit être supérieure à 0");
            }
            
            if ($data->montant_min < 0 || $data->montant_max < 0) {
                throw new Exception("Les montants ne peuvent pas être négatifs");
            }
            
            if ($data->montant_min >= $data->montant_max) {
                throw new Exception("Le montant minimum doit être inférieur au montant maximum");
            }
            
            // Vérifier que l'enregistrement existe
            $exists = self::getById($id);
            if (!$exists) {
                throw new Exception("Type de prêt non trouvé");
            }
            
            // Vérifier si un autre type avec le même nom existe déjà
            $stmt = $db->prepare("SELECT COUNT(*) FROM types_pret WHERE nom = ? AND id != ?");
            $stmt->execute([$data->nom, $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Un autre type de prêt avec ce nom existe déjà");
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
                throw new Exception("Aucune modification effectuée ou données identiques");
            }
        } catch (PDOException $e) {
            error_log("Erreur BD update TypePret: " . $e->getMessage());
            throw new Exception("Erreur lors de la modification du type de prêt");
        }
    }

    public static function delete($id) {
        try {
            $db = getDB();
            
            // Vérifier que l'enregistrement existe
            $exists = self::getById($id);
            if (!$exists) {
                throw new Exception("Type de prêt non trouvé");
            }
            
            // Vérifier s'il y a des prêts associés (contrainte d'intégrité)
            $stmt = $db->prepare("SELECT COUNT(*) FROM pret WHERE type_pret_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                throw new Exception("Impossible de supprimer ce type de prêt car $count prêt(s) lui sont associés");
            }
            
            $stmt = $db->prepare("DELETE FROM types_pret WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception("Impossible de supprimer le type de prêt");
            }
        } catch (PDOException $e) {
            error_log("Erreur BD delete TypePret: " . $e->getMessage());
            
            // Vérifier si c'est une erreur de contrainte d'intégrité
            if ($e->getCode() == '23000') {
                throw new Exception("Impossible de supprimer ce type de prêt car des prêts lui sont associés");
            }
            
            throw new Exception("Erreur lors de la suppression du type de prêt");
        }
    }
}
?>