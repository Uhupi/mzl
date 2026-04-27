<?php

function fetch_html($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

$team_id = 5951;
$team_url = "https://www.tfvb.de/index.php/kreisliga?task=team_details&id=$team_id";
$html = fetch_html($team_url);
$html = html_entity_decode($html);

$dom = new DOMDocument();
@$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

// Team name
$team_name = '';
$headers = $xpath->query('//h1 | //h2');
foreach ($headers as $h) {
    if (strpos($h->textContent, 'Mut zur Lücke') !== false) {
        $team_name = trim($h->textContent);
        break;
    }
}

// Logo
$logo = '';
$imgs = $xpath->query('//img');
foreach ($imgs as $img) {
    $src = $img->getAttribute('src');
    if (strpos($src, "mannschaften/I{$team_id}") !== false) {
        if (strpos($src, 'http') === 0) {
            $logo = $src;
        } else {
            $logo = 'https://www.tfvb.de' . $src;
        }
        break;
    }
}

// Players
$players = [];
if (preg_match('/<td[^>]*>Spieler<\/td>(.*?)Vorübergehende Spieler/s', $html, $m)) {
    $spieler_html = $m[1];
    $spieler_dom = new DOMDocument();
    @$spieler_dom->loadHTML('<html><body>' . $spieler_html . '</body></html>');
    $xpath = new DOMXPath($spieler_dom);
    $links = $xpath->query('//a');
    foreach ($links as $a) {
        $href = $a->getAttribute('href');
        if (strpos($href, 'spieler_details&id=') !== false) {
            $name = trim($a->textContent);
            $small = $a->nextSibling;
            while ($small && ($small->nodeName == '#text' || $small->nodeName == 'br')) {
                $small = $small->nextSibling;
            }
            if ($small && $small->nodeName == 'small') {
                $id = trim($small->textContent);
                $players[] = ['name' => $name, 'id' => $id];
            }
        }
    }
}

// Get match links
$match_links = [];
preg_match_all('/href="([^"]*begegnung_spielplan[^"]*teamid=' . $team_id . '[^"]*)"/', $html, $link_matches);
foreach ($link_matches[1] as $href) {
    $full_url = 'https://www.tfvb.de' . $href;
    if (!in_array($full_url, $match_links)) {
        $match_links[] = $full_url;
    }
}

// Now process each match
$matches = [];

$player_map = [];
foreach ($players as $p) {
    $player_map[$p['name']] = $p['id'];
}

foreach ($match_links as $link) {
    $match_html = fetch_html($link);
    $match_dom = new DOMDocument();
    @$match_dom->loadHTML($match_html);
    $match_xpath = new DOMXPath($match_dom);

    // Title
    $title = '';
    $divs = $match_xpath->query('//div');
    foreach ($divs as $div) {
        if (strpos($div->textContent, 'vs.') !== false) {
            $title = trim($div->textContent);
            break;
        }
    }
    $opponent = '';
    $is_home = null;
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    @$dom->loadHTML($match_html);
    $xpath = new DOMXPath($dom);
    $th = $xpath->query("//th[@class='sectiontableheader' and @align='left']");
    if ($th->length > 0) {
        $breadcrumb = $th->item(0)->textContent;
        $parts = preg_split('/\s*>\s*/', $breadcrumb);
        $last = trim(end($parts));
        if (preg_match('/vs\./', $last)) {
            $vs_parts = explode('vs.', $last);
            if (count($vs_parts) == 2) {
                if (trim($vs_parts[0]) == $team_name) {
                    $opponent = trim($vs_parts[1]);
                    $is_home = true;
                } else {
                    $opponent = trim($vs_parts[0]);
                    $is_home = false;
                }
            }
        }
    }

    // Date
    $date = '';
    if (preg_match('/(\d{2})\.(\d{2})\.(\d{4}) (\d{2}:\d{2})/', $match_html, $m)) {
        // Convert "23.09.2025 19:00" to "2025-09-23 19:00:00"
        $day = $m[1];
        $month = $m[2];
        $year = $m[3];
        $time = $m[4];
        $date = "$year-$month-$day $time:00";
    }

    // Overall result
    $result_text = '';
    if (preg_match('/<h2[^>]*>(\d+:\d+)<\/h2>/', $match_html, $m)) {
        $result_text = $m[1];
    }

    // Games
    $games = [];
    $trs = $match_xpath->query("//tr[@class='sectiontableentry1' or @class='sectiontableentry2']");
    foreach ($trs as $tr) {
        $tds = $match_xpath->query(".//td", $tr);
        if ($tds->length >= 4) {
            $spielnr_text = trim($tds->item(0)->textContent);
            if (!is_numeric(trim($spielnr_text))) {
                continue;
            }
            
            // Debug all TDs
            $td_contents = [];
            for ($i = 0; $i < $tds->length; $i++) {
                $td_contents[] = trim($tds->item($i)->textContent);
            }
            
            // Determine column positions based on number of TDs
            if ($tds->length == 8) {
                $heim_td = $tds->item(3);
                $ergebnis_td = $tds->item(4);
                $gast_td = $tds->item(5);
            } elseif ($tds->length == 10) {
                $heim_td = $tds->item(4);
                $ergebnis_td = $tds->item(5);
                $gast_td = $tds->item(6);
            } else {
                continue;
            }
            
            $ergebnis = trim($ergebnis_td->textContent);
            $scores = explode(':', $ergebnis);
            if (count($scores) == 2) {
                $heim_score = (int)$scores[0];
                $gast_score = (int)$scores[1];
                if ($heim_score > $gast_score) {
                    $result_game = 2;
                } elseif ($heim_score == $gast_score) {
                    $result_game = 1;
                } else {
                    $result_game = 0;
                }
            } else {
                continue;
            }
            $players_game = [];
            // For home matches, players are in heim_td; for away matches, players are in gast_td
            $player_td = $is_home ? $heim_td : $gast_td;
            $opponent_td = $is_home ? $gast_td : $heim_td;
            $player_text = trim($player_td->textContent);
            if (!empty($player_text)) {
                // Split by newlines and clean up
                $game_players = array_map('trim', explode("\n", $player_text));
                $game_players = array_filter($game_players, function($p) { return !empty($p) && $p !== "\t\t\t\t\t\t\t\t"; });
                foreach ($game_players as $player_name) {
                    $clean_name = trim($player_name);
                    if (isset($player_map[$clean_name])) {
                        $players_game[] = $player_map[$clean_name];
                    } else {
                        // Use "00-0000" as fallback for missing player names
                        $players_game[] = '00-0000';
                    }
                }
            } else {
                // If player cell is empty, add fallback placeholder
                $players_game[] = '00-0000';
            }

            // Check opponent player count to determine game type
            $opponent_text = trim($opponent_td->textContent);
            $opponent_count = 0;
            if (!empty($opponent_text)) {
                $opponent_players = array_map('trim', explode("\n", $opponent_text));
                $opponent_players = array_filter($opponent_players, function($p) { return !empty($p) && $p !== "\t\t\t\t\t\t\t\t"; });
                $opponent_count = count($opponent_players);
            } else {
                $opponent_count = 1;
            }

            $type = (count($players_game) > 1 || $opponent_count > 1) ? "double" : "single";

            // If it's a double but we have less than 2 players, pad with 00-0000
            if ($type === "double" && count($players_game) < 2) {
                while (count($players_game) < 2) {
                    $players_game[] = '00-0000';
                }
            }
            $games[] = ['players' => $players_game, 'type' => $type, 'result' => $result_game];
        }
    }

    // Extract only the team's score from the result
    $team_score = null;
    if (!empty($result_text) && strpos($result_text, ':') !== false) {
        $scores = explode(':', $result_text);
        if (count($scores) == 2) {
            // If home: score is first number, if away: score is second number
            $team_score = (int)($is_home ? trim($scores[0]) : trim($scores[1]));
        }
    }

    $matches[] = [
        'opponent' => $opponent,
        'date' => $date,
        'result' => $team_score,
        'home' => $is_home,
        'games' => $games
    ];
}

$data = [
    'team' => [
        'name' => $team_name,
        'logo' => $logo,
        'players' => $players
    ],
    'matches' => $matches
];

file_put_contents("data/$team_id.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Data saved to data/$team_id.json\n";

?>