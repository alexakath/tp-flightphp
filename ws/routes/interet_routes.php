<?php
require_once __DIR__ . '/../controllers/InteretsMensuelController.php';

// Route pour récupérer les intérêts mensuels
Flight::route('GET /interets-mensuels', ['InteretsMensuelController', 'getInteretsMensuels']);

// Route pour exporter les données en CSV
Flight::route('GET /interets-mensuels/export-csv', ['InteretsMensuelController', 'exporterCSV']);