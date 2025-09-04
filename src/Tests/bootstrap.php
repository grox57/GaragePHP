<?php

// Tests/bootstrap.php

// Charger l'autoloader de Composer
$autoloadPath = dirname(__DIR__) . '/vendor/autoload.php';

require_once $autoloadPath;

use App\Config\Config;

// Charger un fichier .env spécifique aux tests pour utiliser une BDD séparée, 
// cela évite de polluer la BDD de développement
Config::load(dirname(__DIR__) . '/.env.testing');
