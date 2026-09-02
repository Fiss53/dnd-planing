<?php

function json_response($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function json_error($message, $status = 400, $debug = null)
{
    $payload = ['error' => $message];
    if ($debug !== null) {
        $payload['debug'] = $debug;
    }
    json_response($payload, $status);
}

function read_json_body()
{
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function start_session()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']),
            'httponly' => true,
        ]);
        session_start();
    }
}

function is_admin()
{
    start_session();
    return !empty($_SESSION['is_admin']);
}

function require_admin()
{
    if (!is_admin()) {
        json_error('Non authentifie.', 401);
    }
}

// Met en forme une ligne de la table `sessions` pour qu'elle corresponde
// exactement a ce que le front attend (voir src/App.vue: loadSessions()).
function format_session_row($row)
{
    return [
        'id' => $row['id'],
        'title' => $row['title'],
        'info' => $row['info'],
        'gameMaster' => $row['gameMaster'],
        'image' => $row['image'],
        'dates' => $row['dates'] !== null ? $row['dates'] : '[]',
        'day' => $row['day'],
        'frequency' => $row['frequency'] !== null ? (int)$row['frequency'] : null,
        'location' => $row['location'],
        'time' => $row['time'],
        'players' => (int)$row['players'],
        'maxPlayers' => (int)$row['maxPlayers'],
        'tags' => $row['tags'] !== null ? $row['tags'] : '[]',
        'special' => (bool)$row['special'],
    ];
}

// Genere un identifiant hexadecimal court et verifie son unicite en base.
function generate_session_id($pdo)
{
    $stmt = $pdo->prepare('SELECT 1 FROM sessions WHERE id = ?');

    do {
        $id = strtoupper(bin2hex(random_bytes(2))); // ex: "9F3A"
        $stmt->execute([$id]);
        $exists = $stmt->fetchColumn();
    } while ($exists);

    return $id;
}
