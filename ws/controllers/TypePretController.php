<?php
require_once __DIR__ . '/../models/TypePret.php';

class TypePretController {
    public static function getAll() {
        try {
            $typesPret = TypePret::getAll();
            Flight::json($typesPret);
        } catch (Exception $e) {
            error_log("Erreur getAll TypePret: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $typePret = TypePret::getById($id);
            if (!$typePret) {
                Flight::json(['error' => 'Type de prêt non trouvé'], 404);
                return;
            }
            Flight::json($typePret);
        } catch (Exception $e) {
            error_log("Erreur getById TypePret: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = self::getRequestData();
            
            // Debug: log des données reçues
            error_log("Données reçues pour création: " . print_r($data, true));
            
            $id = TypePret::create($data);
            Flight::json(['message' => 'Type de prêt ajouté avec succès', 'id' => $id], 201);
        } catch (Exception $e) {
            error_log("Erreur création TypePret: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function update($id) {
        try {
            $data = self::getRequestData();
            
            // Debug: log des données reçues
            error_log("Données reçues pour modification ID $id: " . print_r($data, true));
            
            TypePret::update($id, $data);
            Flight::json(['message' => 'Type de prêt modifié avec succès'], 200);
        } catch (Exception $e) {
            error_log("Erreur modification TypePret ID $id: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public static function delete($id) {
        try {
            // Debug: log de la tentative de suppression
            error_log("Tentative de suppression TypePret ID: $id");
            
            // Vérifier si le type de prêt existe
            $typePret = TypePret::getById($id);
            if (!$typePret) {
                error_log("Type de prêt ID $id non trouvé");
                Flight::json(['error' => 'Type de prêt non trouvé'], 404);
                return;
            }
            
            // Vérifier s'il y a des prêts associés
            $db = getDB();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM pret WHERE type_pret_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                error_log("Impossible de supprimer le type de prêt ID $id: des prêts sont associés");
                Flight::json(['error' => 'Impossible de supprimer ce type de prêt car des prêts lui sont associés'], 400);
                return;
            }
            
            TypePret::delete($id);
            error_log("Type de prêt ID $id supprimé avec succès");
            Flight::json(['message' => 'Type de prêt supprimé avec succès'], 200);
            
        } catch (Exception $e) {
            error_log("Erreur suppression TypePret ID $id: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Fonction utilitaire pour récupérer les données de la requête
     * Compatible avec POST et PUT
     */
    private static function getRequestData() {
        $data = new stdClass();
        
        // Pour POST, utiliser $_POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data->nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
            $data->taux_annuel = isset($_POST['taux_annuel']) ? floatval($_POST['taux_annuel']) : 0;
            $data->duree_max_mois = isset($_POST['duree_max_mois']) ? intval($_POST['duree_max_mois']) : 0;
            $data->montant_min = isset($_POST['montant_min']) ? floatval($_POST['montant_min']) : 0;
            $data->montant_max = isset($_POST['montant_max']) ? floatval($_POST['montant_max']) : 0;
        }
        // Pour PUT, parser le body de la requête
        else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $putData = [];
            parse_str(file_get_contents('php://input'), $putData);
            
            $data->nom = isset($putData['nom']) ? trim($putData['nom']) : '';
            $data->taux_annuel = isset($putData['taux_annuel']) ? floatval($putData['taux_annuel']) : 0;
            $data->duree_max_mois = isset($putData['duree_max_mois']) ? intval($putData['duree_max_mois']) : 0;
            $data->montant_min = isset($putData['montant_min']) ? floatval($putData['montant_min']) : 0;
            $data->montant_max = isset($putData['montant_max']) ? floatval($putData['montant_max']) : 0;
        }
        
        return $data;
    }
}
?>