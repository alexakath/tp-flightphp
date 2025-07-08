<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/fond_routes.php';
require 'routes/typepret_routes.php';
require 'routes/pret_routes.php';
require 'routes/client_routes.php';

// Gérer les fichiers HTML statiques
Flight::route('GET /(*.html)', function($path) {
    $file = __DIR__ . '/' . $path;
    if (file_exists($file)) {
        header('Content-Type: text/html');
        readfile($file);
        exit;
    } else {
        Flight::notFound();
    }
});

Flight::start();