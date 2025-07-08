<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/Fond.php';
require_once __DIR__ . '/../models/TypePret.php';

class PretController {
    public static function getAll() {
        try {
            $prets = Pret::getAll();
            Flight::json($prets);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function create() {
        try {
            $data = Flight::request()->data;

            // Validation des données
            if (!isset($data->client_id) || !isset($data->type_pret_id) || !isset($data->montant) || !isset($data->duree) || !isset($data->date_debut)) {
                Flight::json(['error' => 'Tous les champs sont obligatoires'], 400);
                return;
            }

            // Vérifier si le type de prêt existe et si les conditions sont respectées
            $typePret = TypePret::getById($data->type_pret_id);
            if (!$typePret) {
                Flight::json(['error' => 'Type de prêt non trouvé'], 404);
                return;
            }

            if ($data->montant < $typePret['montant_min'] || $data->montant > $typePret['montant_max']) {
                Flight::json(['error' => 'Montant hors des limites du type de prêt'], 400);
                return;
            }

            if ($data->duree > $typePret['duree_max_mois']) {
                Flight::json(['error' => 'Durée dépasse la durée maximale du type de prêt'], 400);
                return;
            }

            if ($data->montant <= 0) {
                Flight::json(['error' => 'Le montant doit être positif'], 400);
                return;
            }

            $id = Pret::create($data);
            Flight::json(['message' => 'Demande de prêt soumise', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function update($id) {
        try {
            $data = Flight::request()->data;
            if (!isset($data->statut) || !in_array($data->statut, ['approuve', 'rejete'])) {
                Flight::json(['error' => 'Statut invalide'], 400);
                return;
            }

            $pret = Pret::getById($id);
            if (!$pret) {
                Flight::json(['error' => 'Prêt non trouvé'], 404);
                return;
            }

            if ($data->statut === 'approuve') {
                $soldeTotal = Fond::getSoldeTotal();
                if ($pret['montant'] > $soldeTotal) {
                    Flight::json(['error' => 'Fonds insuffisants pour approuver ce prêt'], 400);
                    return;
                }

                // Déduire le montant du prêt du solde
                $fondData = (object)[
                    'montant' => -$pret['montant'],
                    'date_ajout' => date('Y-m-d'),
                    'description' => "Prêt approuvé ID {$id}"
                ];
                Fond::create($fondData);
            }

            Pret::update($id, $data);
            Flight::json(['message' => 'Statut du prêt mis à jour']);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }
}