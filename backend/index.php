<?php
// Point d'entree unique de l'API. Toutes les requetes sont redirigees ici par
// .htaccess, quel que soit le sous-dossier ou ce backend est deploye (/api,
// /backend, etc.) : on route uniquement sur la fin du chemin.

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = rtrim($path, '/');

try {
    if ($method === 'POST' && preg_match('#/login$#', $path)) {
        require __DIR__ . '/routes/login.php';
    } elseif ($method === 'POST' && preg_match('#/logout$#', $path)) {
        require __DIR__ . '/routes/logout.php';
    } elseif ($method === 'GET' && preg_match('#/sessions$#', $path)) {
        require __DIR__ . '/routes/sessions_list.php';
    } elseif ($method === 'POST' && preg_match('#/sessions$#', $path)) {
        require __DIR__ . '/routes/sessions_create.php';
    } elseif ($method === 'PUT' && preg_match('#/sessions/([A-Za-z0-9]+)$#', $path, $m)) {
        $sessionId = $m[1];
        require __DIR__ . '/routes/sessions_update.php';
    } else {
        json_error('Route inconnue.', 404);
    }
} catch (PDOException $e) {
    json_error('Erreur base de donnees.', 500);
} catch (Throwable $e) {
    json_error('Erreur serveur.', 500);
}
