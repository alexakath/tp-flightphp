<?php
require_once __DIR__ . '/../models/Fond.php';
require_once __DIR__ . '/../helpers/Utils.php';

class FondController {
    public static function getAll() {
        try {
            $fonds = Fond::getAll();
            Flight::json($fonds);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getById($id) {
        try {
            $fond = Fond::getById($id);
            Flight::json($fond);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getSoldeTotal() {
        try {
            $solde = Fond::getSoldeTotal();
            Flight::json(['solde_total' => number_format($solde, 2, '.', '')]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage(), 'solde_total' => '0.00'], 500);
        }
    }

    public static function create() {
        try {
            // Récupérer les données POST correctement
            $data = self::getRequestData();
            
            $result = Fond::create($data);
            Flight::json([
                'message' => 'Fond ajouté', 
                'id' => $result['id'],
                'nouveau_solde' => number_format($result['nouveau_solde'], 2, '.', '')
            ]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function update($id) {
        try {
            // Récupérer les données PUT correctement
            $data = self::getRequestData();
            
            $nouveauSolde = Fond::update($id, $data);
            Flight::json([
                'message' => 'Fond modifié',
                'nouveau_solde' => number_format($nouveauSolde, 2, '.', '')
            ]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function delete($id) {
        try {
            $nouveauSolde = Fond::delete($id);
            Flight::json([
                'message' => 'Fond supprimé',
                'nouveau_solde' => number_format($nouveauSolde, 2, '.', '')
            ]);
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
            $data->montant = isset($_POST['montant']) ? floatval($_POST['montant']) : 0;
            $data->date_ajout = isset($_POST['date_ajout']) ? $_POST['date_ajout'] : '';
            $data->description = isset($_POST['description']) ? $_POST['description'] : '';
        }
        // Pour PUT, parser le body de la requête
        else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            $putData = [];
            parse_str(file_get_contents('php://input'), $putData);
            
            $data->montant = isset($putData['montant']) ? floatval($putData['montant']) : 0;
            $data->date_ajout = isset($putData['date_ajout']) ? $putData['date_ajout'] : '';
            $data->description = isset($putData['description']) ? $putData['description'] : '';
        }
        
        return $data;
    }
}