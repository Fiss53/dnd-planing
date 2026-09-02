<?php
// POST /logout  -- ferme la session admin cote serveur.

start_session();
$_SESSION = [];
session_destroy();

json_response(['ok' => true]);
