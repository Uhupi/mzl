<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dataFile = __DIR__ . '/data/5951.json';

if (!file_exists($dataFile)) {
    http_response_code(404);
    echo json_encode(['error' => 'Data file not found']);
    exit;
}

$data = json_decode(file_get_contents($dataFile), true);

// Support both URL routing (/api/team) and query params (?endpoint=team&param=0)
$endpoint = $_GET['endpoint'] ?? null;
$param = $_GET['param'] ?? null;

if (!$endpoint) {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $pathSegments = array_filter(explode('/', $requestUri));

    $route = array_slice($pathSegments, -2);

    if (empty($route) || $route[0] !== 'api') {
        http_response_code(400);
        echo json_encode(['error' => 'Missing endpoint. Use /api.php?endpoint=team or /api/team']);
        exit;
    }

    $endpoint = $route[1] ?? null;
    $param = $route[2] ?? null;
}

if (!$endpoint) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing endpoint parameter']);
    exit;
}

try {
    match ($endpoint) {
        'team' => outputTeam($data),
        'players' => outputPlayers($data),
        'matches' => outputMatches($data, $param),
        'stats' => outputStats($data),
        'all' => outputAll($data),
        default => throw new Exception('Endpoint not found', 404)
    };
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['error' => $e->getMessage()]);
}

function outputTeam($data) {
    echo json_encode([
        'name' => $data['team']['name'],
        'logo' => $data['team']['logo'],
        'playerCount' => count($data['team']['players'])
    ]);
}

function outputPlayers($data) {
    echo json_encode([
        'players' => $data['team']['players'],
        'count' => count($data['team']['players'])
    ]);
}

function outputMatches($data, $matchIndex) {
    if ($matchIndex !== null) {
        if (!is_numeric($matchIndex) || !isset($data['matches'][$matchIndex])) {
            throw new Exception('Match not found', 404);
        }

        $match = $data['matches'][$matchIndex];
        $match['playerNames'] = enrichPlayersWithNames($match['games'], $data['team']['players']);
        echo json_encode(['match' => $match]);
        return;
    }

    $matches = array_map(function ($match, $idx) use ($data) {
        return [
            'id' => $idx,
            'opponent' => $match['opponent'],
            'date' => $match['date'],
            'result' => $match['result'],
            'home' => $match['home'],
            'gameCount' => count($match['games'])
        ];
    }, $data['matches'], array_keys($data['matches']));

    echo json_encode([
        'matches' => $matches,
        'count' => count($matches)
    ]);
}

function outputStats($data) {
    $matches = $data['matches'];
    $wins = 0;
    $ties = 0;
    $losses = 0;

    foreach ($matches as $match) {
        if ($match['result'] > 13) $wins++;
        elseif ($match['result'] === 13) $ties++;
        else $losses++;
    }

    $playerStats = [];
    foreach ($data['team']['players'] as $player) {
        $playerStats[$player['id']] = [
            'name' => $player['name'],
            'id' => $player['id'],
            'singles' => 0,
            'doubles' => 0,
            'wins' => 0,
            'losses' => 0,
            'ties' => 0
        ];
    }

    foreach ($matches as $match) {
        foreach ($match['games'] as $game) {
            foreach ($game['players'] as $playerId) {
                if (!isset($playerStats[$playerId])) continue;

                if ($game['type'] === 'single') {
                    $playerStats[$playerId]['singles']++;
                } else {
                    $playerStats[$playerId]['doubles']++;
                }

                if ($game['result'] === 2) {
                    $playerStats[$playerId]['wins']++;
                } elseif ($game['result'] === 1) {
                    $playerStats[$playerId]['ties']++;
                } else {
                    $playerStats[$playerId]['losses']++;
                }
            }
        }
    }

    echo json_encode([
        'team' => [
            'matches' => count($matches),
            'wins' => $wins,
            'losses' => $losses,
            'ties' => $ties
        ],
        'players' => array_values($playerStats)
    ]);
}

function outputAll($data) {
    echo json_encode($data);
}

function enrichPlayersWithNames($games, $players) {
    $playerMap = array_column($players, 'name', 'id');

    return array_map(function ($game) use ($playerMap) {
        return [
            'players' => array_map(function ($id) use ($playerMap) {
                return [
                    'id' => $id,
                    'name' => $playerMap[$id] ?? 'Unknown'
                ];
            }, $game['players']),
            'type' => $game['type'],
            'result' => $game['result'],
            'resultText' => ['loss', 'tie', 'win'][$game['result']]
        ];
    }, $games);
}
