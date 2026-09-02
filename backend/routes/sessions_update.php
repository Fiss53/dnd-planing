<?php
// PUT /sessions/{id} -- met a jour une session existante. Reserve aux admins connectes.
// $sessionId est fourni par index.php (extrait de l'URL).

require_admin();

$body = read_json_body();
$pdo = get_db();

$select = $pdo->prepare('SELECT * FROM sessions WHERE id = ?');
$select->execute([$sessionId]);
$existing = $select->fetch();

if (!$existing) {
    json_error('Session introuvable.', 404);
}

$update = $pdo->prepare(
    'UPDATE sessions SET title = :title, info = :info, gameMaster = :gameMaster, image = :image,
     dates = :dates, day = :day, frequency = :frequency, location = :location, time = :time,
     players = :players, maxPlayers = :maxPlayers, tags = :tags, special = :special
     WHERE id = :id'
);

$update->execute([
    'id' => $sessionId,
    'title' => $body['title'] ?? $existing['title'],
    'info' => $body['info'] ?? $existing['info'],
    'gameMaster' => $body['gameMaster'] ?? $existing['gameMaster'],
    'image' => $body['image'] ?? $existing['image'],
    'dates' => isset($body['dates']) ? json_encode($body['dates']) : $existing['dates'],
    'day' => $body['day'] ?? $existing['day'],
    'frequency' => isset($body['frequency']) ? (int)$body['frequency'] : $existing['frequency'],
    'location' => $body['location'] ?? $existing['location'],
    'time' => $body['time'] ?? $existing['time'],
    'players' => isset($body['players']) ? (int)$body['players'] : $existing['players'],
    'maxPlayers' => isset($body['maxPlayers']) ? (int)$body['maxPlayers'] : $existing['maxPlayers'],
    'tags' => isset($body['tags']) ? json_encode($body['tags']) : $existing['tags'],
    'special' => array_key_exists('special', $body) ? (!empty($body['special']) ? 1 : 0) : $existing['special'],
]);

$select->execute([$sessionId]);
json_response(format_session_row($select->fetch()));
