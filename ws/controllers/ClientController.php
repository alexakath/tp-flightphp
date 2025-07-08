<?php
require_once __DIR__ . '/../models/Client.php';

class ClientController {
    public static function getAll() {
        try {
            $clients = Client::getAll();
            Flight::json($clients);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }
}