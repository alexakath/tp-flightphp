<?php
require_once __DIR__ . '/../models/TypePret.php';

class TypePretController {
    public static function getAll() {
        try {
            $typesPret = TypePret::getAll();
            Flight::json($typesPret);
        } catch (Exception $e) {
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
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = self::getRequestData();
            
            // Debug: log des données reçues
            error_log("Données reçues pour création: " . print_r($data, true));
            
            $id = TypePret::create($data);
            Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
        } catch (Exception $e) {
            error_log("Erreur création: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function update($id) {
        try {
            $data = self::getRequestData();
            
            // Debug: log des données reçues
            error_log("Données reçues pour modification: " . print_r($data, true));
            
            TypePret::update($id, $data);
            Flight::json(['message' => 'Type de prêt modifié']);
        } catch (Exception $e) {
            error_log("Erreur modification: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function delete($id) {
        try {
            TypePret::delete($id);
            Flight::json(['message' => 'Type de prêt supprimé']);
        } catch (Exception $e) {
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