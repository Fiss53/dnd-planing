<?php
// POST /login  { password }
// Verifie le mot de passe admin partage et ouvre une session PHP si correct.

$body = read_json_body();
$password = isset($body['password']) ? (string)$body['password'] : '';

if ($password === '' || !ADMIN_PASSWORD_HASH || !password_verify($password, ADMIN_PASSWORD_HASH)) {
    json_error('Mot de passe incorrect.', 401);
}

start_session();
session_regenerate_id(true);
$_SESSION['is_admin'] = true;

json_response(['ok' => true]);
