<?php
// GET /sessions -- liste toutes les sessions, publiques (pas d'auth requise).

$pdo = get_db();
$rows = $pdo->query('SELECT * FROM sessions ORDER BY createdAt ASC')->fetchAll();

json_response(array_map('format_session_row', $rows));
