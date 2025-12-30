<?php

use App\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// Vérifier si composer install a été exécuté
if (!is_file(dirname(__DIR__).'/vendor/autoload.php')) {
    http_response_code(500);
    echo '<html><head><title>Erreur Installation</title><style>body{font-family:Arial,sans-serif;margin:50px;background:#f5f5f5;}
    .box{background:white;padding:30px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:600px;margin:auto;}
    h1{color:#c00;}code{background:#f0f0f0;padding:10px;display:block;margin:10px 0;border-radius:5px;}</style></head>
    <body><div class="box"><h1>⚠️ Installation requise</h1>
    <p>Les dépendances ne sont pas installées.</p>
    <p><strong>Ouvrez un terminal dans le dossier du projet et exécutez :</strong></p>
    <code>composer install</code>
    <p>Puis relancez le serveur.</p></div></body></html>';
    exit(1);
}

require_once dirname(__DIR__).'/vendor/autoload.php';

// Charger les variables d'environnement depuis .env
if (is_file(dirname(__DIR__).'/.env')) {
    $lines = file(dirname(__DIR__).'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, '"\'');
            if (!isset($_ENV[$name]) && !isset($_SERVER[$name])) {
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

$_SERVER['APP_ENV'] = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? ('prod' !== $_SERVER['APP_ENV']);
$_SERVER['APP_DEBUG'] = filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
    if (class_exists(Debug::class)) {
        Debug::enable();
    }
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
