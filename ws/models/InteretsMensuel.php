<?php
require_once __DIR__ . '/../db.php';

class InteretsMensuel {
    
    /**
     * Calcule les intérêts mensuels pour une période donnée
     * @param string $moisDebut Format YYYY-MM
     * @param string $moisFin Format YYYY-MM
     * @return array
     */
    public static function calculerInteretsMensuels($moisDebut, $moisFin) {
        $db = getDB();
        $resultats = [];
        
        // Créer une liste de tous les mois entre les dates de début et de fin
        $moisCourant = new DateTime($moisDebut . '-01');
        $moisFinDate = new DateTime($moisFin . '-01');
        
        while ($moisCourant <= $moisFinDate) {
            $annee = $moisCourant->format('Y');
            $mois = $moisCourant->format('m');
            
            // Calculer les intérêts pour ce mois
            $interetsMois = self::calculerInteretsPourMois($annee, $mois);
            
            if ($interetsMois) {
                $resultats[] = $interetsMois;
            }
            
            // Passer au mois suivant
            $moisCourant->add(new DateInterval('P1M'));
        }
        
        return $resultats;
    }
    
    /**
     * Calcule les intérêts pour un mois donné
     * @param string $annee
     * @param string $mois
     * @return array|null
     */
    private static function calculerInteretsPourMois($annee, $mois) {
        $db = getDB();
        
        // Premier jour du mois
        $premierJour = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '-01';
        
        // Dernier jour du mois
        $dernierJour = date('Y-m-t', strtotime($premierJour));
        
        // Récupérer tous les prêts approuvés qui sont actifs pendant ce mois
        $stmt = $db->prepare("
            SELECT 
                p.id,
                p.montant,
                p.date_debut,
                p.duree,
                tp.taux_annuel,
                tp.nom as type_nom,
                c.nom as client_nom,
                DATEDIFF(LEAST(?, DATE_ADD(p.date_debut, INTERVAL p.duree MONTH)), 
                         GREATEST(?, p.date_debut)) + 1 as jours_actifs_mois
            FROM pret p
            JOIN types_pret tp ON p.type_pret_id = tp.id
            JOIN client c ON p.client_id = c.id
            WHERE p.statut = 'approuve'
            AND p.date_debut <= ?
            AND DATE_ADD(p.date_debut, INTERVAL p.duree MONTH) >= ?
            ORDER BY p.date_debut ASC
        ");
        
        $stmt->execute([$dernierJour, $premierJour, $dernierJour, $premierJour]);
        $pretsActifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($pretsActifs)) {
            return null;
        }
        
        $totalInterets = 0;
        $totalCapital = 0;
        $nombreJoursMois = date('t', strtotime($premierJour));
        
        foreach ($pretsActifs as $pret) {
            $joursActifs = max(0, $pret['jours_actifs_mois']);
            
            if ($joursActifs > 0) {
                // Calculer les intérêts pour ce prêt pour ce mois
                $tauxMensuel = $pret['taux_annuel'] / 12 / 100;
                $interetsMensuel = $pret['montant'] * $tauxMensuel;
                
                // Ajuster selon le nombre de jours actifs dans le mois
                $interetsAjustes = $interetsMensuel * ($joursActifs / $nombreJoursMois);
                
                $totalInterets += $interetsAjustes;
                $totalCapital += $pret['montant'];
            }
        }
        
        return [
            'mois' => (int)$mois,
            'annee' => (int)$annee,
            'nombre_prets_actifs' => count($pretsActifs),
            'capital_total' => $totalCapital,
            'interets_gagnes' => $totalInterets
        ];
    }
    
    /**
     * Calcule les statistiques générales
     * @param array $interets
     * @return array
     */
    public static function calculerStatistiques($interets) {
        if (empty($interets)) {
            return [
                'total_interets' => 0,
                'nombre_mois' => 0,
                'moyenne_mensuelle' => 0,
                'mois_plus_rentable' => null,
                'mois_moins_rentable' => null
            ];
        }
        
        $totalInterets = array_sum(array_column($interets, 'interets_gagnes'));
        $nombreMois = count($interets);
        $moyenneMensuelle = $totalInterets / $nombreMois;
        
        // Trouver le mois le plus et le moins rentable
        $interetsSeuls = array_column($interets, 'interets_gagnes');
        $indexMax = array_search(max($interetsSeuls), $interetsSeuls);
        $indexMin = array_search(min($interetsSeuls), $interetsSeuls);
        
        return [
            'total_interets' => $totalInterets,
            'nombre_mois' => $nombreMois,
            'moyenne_mensuelle' => $moyenneMensuelle,
            'mois_plus_rentable' => $interets[$indexMax] ?? null,
            'mois_moins_rentable' => $interets[$indexMin] ?? null
        ];
    }
    
    /**
     * Récupère les prêts actifs pour un mois donné (pour debug)
     * @param string $annee
     * @param string $mois
     * @return array
     */
    public static function getPretsActifsPourMois($annee, $mois) {
        $db = getDB();
        
        $premierJour = $annee . '-' . str_pad($mois, 2, '0', STR_PAD_LEFT) . '-01';
        $dernierJour = date('Y-m-t', strtotime($premierJour));
        
        $stmt = $db->prepare("
            SELECT 
                p.id,
                p.montant,
                p.date_debut,
                p.duree,
                tp.taux_annuel,
                tp.nom as type_nom,
                c.nom as client_nom,
                DATE_ADD(p.date_debut, INTERVAL p.duree MONTH) as date_fin_prevue
            FROM pret p
            JOIN types_pret tp ON p.type_pret_id = tp.id
            JOIN client c ON p.client_id = c.id
            WHERE p.statut = 'approuve'
            AND p.date_debut <= ?
            AND DATE_ADD(p.date_debut, INTERVAL p.duree MONTH) >= ?
            ORDER BY p.date_debut ASC
        ");
        
        $stmt->execute([$dernierJour, $premierJour]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}