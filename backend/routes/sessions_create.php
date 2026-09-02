<?php
// POST /sessions -- cree une nouvelle session. Reserve aux admins connectes.

require_admin();

$body = read_json_body();
$title = trim((string)($body['title'] ?? ''));

if ($title === '') {
    json_error('Le titre est obligatoire.', 422);
}

$pdo = get_db();
$id = generate_session_id($pdo);

$insert = $pdo->prepare(
    'INSERT INTO sessions (id, title, info, gameMaster, image, dates, day, frequency, location, time, players, maxPlayers, tags, special)
     VALUES (:id, :title, :info, :gameMaster, :image, :dates, :day, :frequency, :location, :time, :players, :maxPlayers, :tags, :special)'
);

$insert->execute([
    'id' => $id,
    'title' => $title,
    'info' => $body['info'] ?? null,
    'gameMaster' => $body['gameMaster'] ?? null,
    'image' => $body['image'] ?? null,
    'dates' => json_encode($body['dates'] ?? []),
    'day' => $body['day'] ?? null,
    'frequency' => isset($body['frequency']) ? (int)$body['frequency'] : null,
    'location' => $body['location'] ?? null,
    'time' => $body['time'] ?? null,
    'players' => isset($body['players']) ? (int)$body['players'] : 0,
    'maxPlayers' => isset($body['maxPlayers']) ? (int)$body['maxPlayers'] : 0,
    'tags' => json_encode($body['tags'] ?? []),
    'special' => !empty($body['special']) ? 1 : 0,
]);

$select = $pdo->prepare('SELECT * FROM sessions WHERE id = ?');
$select->execute([$id]);

json_response(format_session_row($select->fetch()), 201);
