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
        $playerMap = array_column($data['team']['players'], 'name', 'id');

        $games = array_map(function ($game) use ($playerMap) {
            return [
                'type' => $game['type'],
                'result' => $game['result'],
                'resultText' => ['loss', 'tie', 'win'][$game['result']],
                'players' => array_map(function ($id) use ($playerMap) {
                    return [
                        'id' => $id,
                        'name' => $playerMap[$id] ?? 'Unknown'
                    ];
                }, $game['players'])
            ];
        }, $match['games']);

        $matchPlayers = [];
        foreach ($match['games'] as $game) {
            $result = $game['result'];
            $pointValue = $result === 2 ? 2 : ($result === 1 ? 1 : 0);
            $pointsPerPlayer = $game['type'] === 'single' ? $pointValue : $pointValue / 2;

            foreach ($game['players'] as $playerId) {
                if (!isset($matchPlayers[$playerId])) {
                    $matchPlayers[$playerId] = [
                        'id' => $playerId,
                        'name' => $playerMap[$playerId] ?? 'Unknown',
                        'gamesPlayed' => 0,
                        'wins' => 0,
                        'losses' => 0,
                        'ties' => 0,
                        'pointsEarned' => 0,
                        'pointsPossible' => 0,
                        'gamesByType' => []
                    ];
                }
                $matchPlayers[$playerId]['gamesPlayed']++;
                $matchPlayers[$playerId]['pointsPossible'] += 2;
                $matchPlayers[$playerId]['pointsEarned'] += $pointsPerPlayer;

                if ($game['result'] === 2) {
                    $matchPlayers[$playerId]['wins']++;
                } elseif ($game['result'] === 1) {
                    $matchPlayers[$playerId]['ties']++;
                } else {
                    $matchPlayers[$playerId]['losses']++;
                }

                if (!isset($matchPlayers[$playerId]['gamesByType'][$game['type']])) {
                    $matchPlayers[$playerId]['gamesByType'][$game['type']] = [
                        'count' => 0,
                        'wins' => 0,
                        'losses' => 0,
                        'ties' => 0
                    ];
                }
                $matchPlayers[$playerId]['gamesByType'][$game['type']]['count']++;

                if ($game['result'] === 2) {
                    $matchPlayers[$playerId]['gamesByType'][$game['type']]['wins']++;
                } elseif ($game['result'] === 1) {
                    $matchPlayers[$playerId]['gamesByType'][$game['type']]['ties']++;
                } else {
                    $matchPlayers[$playerId]['gamesByType'][$game['type']]['losses']++;
                }
            }
        }

        foreach ($matchPlayers as &$player) {
            $player['reachPointsPercentage'] = $player['pointsPossible'] > 0 ? round(($player['pointsEarned'] / $player['pointsPossible']) * 100, 2) : 0;
        }

        usort($matchPlayers, function ($a, $b) {
            return $b['reachPointsPercentage'] <=> $a['reachPointsPercentage'];
        });

        echo json_encode([
            'match' => [
                'opponent' => $match['opponent'],
                'date' => $match['date'],
                'result' => $match['result'],
                'home' => $match['home'],
                'games' => $games,
                'players' => array_values($matchPlayers)
            ]
        ]);
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
    $singlesWins = 0;
    $singlesTies = 0;
    $singlesLosses = 0;
    $doublesWins = 0;
    $doublesTies = 0;
    $doublesLosses = 0;

    $playerStats = [];
    foreach ($data['team']['players'] as $player) {
        $playerStats[$player['id']] = [
            'name' => $player['name'],
            'id' => $player['id'],
            'singles' => [
                'played' => 0,
                'wins' => 0,
                'losses' => 0,
                'ties' => 0,
                'pointsEarned' => 0,
                'pointsPossible' => 0
            ],
            'doubles' => [
                'played' => 0,
                'wins' => 0,
                'losses' => 0,
                'ties' => 0,
                'pointsEarned' => 0,
                'pointsPossible' => 0
            ],
            'reachPointsPercentage' => 0
        ];
    }

    foreach ($matches as $match) {
        foreach ($match['games'] as $game) {
            $gameType = $game['type'] === 'single' ? 'singles' : 'doubles';
            $result = $game['result'];
            $pointValue = $result === 2 ? 2 : ($result === 1 ? 1 : 0);
            $pointsPerPlayer = $gameType === 'singles' ? $pointValue : $pointValue / 2;
            $maxPointsPerPlayer = $gameType === 'singles' ? 2 : 1;

            if ($gameType === 'singles') {
                if ($result === 2) $singlesWins++;
                elseif ($result === 1) $singlesTies++;
                else $singlesLosses++;
            } else {
                if ($result === 2) $doublesWins++;
                elseif ($result === 1) $doublesTies++;
                else $doublesLosses++;
            }

            foreach ($game['players'] as $playerId) {
                if (!isset($playerStats[$playerId])) continue;

                $playerStats[$playerId][$gameType]['played']++;
                $playerStats[$playerId][$gameType]['pointsPossible'] += $maxPointsPerPlayer;
                $playerStats[$playerId][$gameType]['pointsEarned'] += $pointsPerPlayer;

                if ($result === 2) {
                    $playerStats[$playerId][$gameType]['wins']++;
                } elseif ($result === 1) {
                    $playerStats[$playerId][$gameType]['ties']++;
                } else {
                    $playerStats[$playerId][$gameType]['losses']++;
                }
            }
        }
    }

    foreach ($playerStats as &$player) {
        $totalPointsEarned = $player['singles']['pointsEarned'] + $player['doubles']['pointsEarned'];
        $totalPointsPossible = $player['singles']['pointsPossible'] + $player['doubles']['pointsPossible'];
        $player['reachPointsPercentage'] = $totalPointsPossible > 0 ? round(($totalPointsEarned / $totalPointsPossible) * 100, 2) : 0;
    }

    usort($playerStats, function ($a, $b) {
        return $b['reachPointsPercentage'] <=> $a['reachPointsPercentage'];
    });

    echo json_encode([
        'team' => [
            'matches' => count($matches),
            'singles' => [
                'wins' => $singlesWins,
                'losses' => $singlesLosses,
                'ties' => $singlesTies
            ],
            'doubles' => [
                'wins' => $doublesWins,
                'losses' => $doublesLosses,
                'ties' => $doublesTies
            ]
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
