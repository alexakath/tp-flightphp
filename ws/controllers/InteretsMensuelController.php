<?php
require_once __DIR__ . '/../models/InteretsMensuel.php';

class InteretsMensuelController {
    
    public static function getInteretsMensuels() {
        try {
            $moisDebut = $_GET['mois_debut'] ?? '';
            $moisFin = $_GET['mois_fin'] ?? '';

            if (empty($moisDebut) || empty($moisFin)) {
                Flight::json(['error' => 'Les paramètres mois_debut et mois_fin sont requis'], 400);
                return;
            }

            // Validation du format des dates
            if (!preg_match('/^\d{4}-\d{2}$/', $moisDebut) || !preg_match('/^\d{4}-\d{2}$/', $moisFin)) {
                Flight::json(['error' => 'Format de date invalide. Utilisez YYYY-MM'], 400);
                return;
            }

            // Vérifier que la date de début n'est pas postérieure à la date de fin
            if ($moisDebut > $moisFin) {
                Flight::json(['error' => 'La date de début ne peut pas être postérieure à la date de fin'], 400);
                return;
            }

            $interets = InteretsMensuel::calculerInteretsMensuels($moisDebut, $moisFin);
            $stats = InteretsMensuel::calculerStatistiques($interets);

            Flight::json([
                'data' => $interets,
                'stats' => $stats,
                'periode' => [
                    'debut' => $moisDebut,
                    'fin' => $moisFin
                ]
            ]);

        } catch (Exception $e) {
            error_log("Erreur dans getInteretsMensuels: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function exporterCSV() {
        try {
            $moisDebut = $_GET['mois_debut'] ?? '';
            $moisFin = $_GET['mois_fin'] ?? '';

            if (empty($moisDebut) || empty($moisFin)) {
                Flight::json(['error' => 'Les paramètres mois_debut et mois_fin sont requis'], 400);
                return;
            }

            $interets = InteretsMensuel::calculerInteretsMensuels($moisDebut, $moisFin);
            
            // Préparer les headers pour le téléchargement CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="interets_mensuels_' . date('Y-m-d') . '.csv"');
            
            // Ouvrir le flux de sortie
            $output = fopen('php://output', 'w');
            
            // Écrire le BOM UTF-8 pour Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Écrire les en-têtes
            fputcsv($output, ['Mois', 'Année', 'Nombre de Prêts Actifs', 'Capital Total (€)', 'Intérêts Gagnés (€)'], ';');
            
            // Écrire les données
            foreach ($interets as $item) {
                $nomMois = self::getNomMois($item['mois']);
                fputcsv($output, [
                    $nomMois,
                    $item['annee'],
                    $item['nombre_prets_actifs'],
                    number_format($item['capital_total'], 2, ',', ' '),
                    number_format($item['interets_gagnes'], 2, ',', ' ')
                ], ';');
            }
            
            fclose($output);
            exit;

        } catch (Exception $e) {
            error_log("Erreur dans exporterCSV: " . $e->getMessage());
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    private static function getNomMois($numMois) {
        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août', 
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $mois[$numMois] ?? 'Inconnu';
    }
}