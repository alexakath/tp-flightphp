<?php
require_once __DIR__ . '/../controllers/FondController.php';

Flight::route('GET /fonds', ['FondController', 'getAll']);
Flight::route('GET /fonds/solde-total', ['FondController', 'getSoldeTotal']); // Déplacer avant /fonds/@id
Flight::route('GET /fonds/@id', ['FondController', 'getById']);
Flight::route('POST /fonds', ['FondController', 'create']);
Flight::route('PUT /fonds/@id', ['FondController', 'update']);
Flight::route('DELETE /fonds/@id', ['FondController', 'delete']);