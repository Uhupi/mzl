# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Running the Scraper

```bash
php scratch.php
```

Requires PHP with cURL and DOM extensions (standard in most PHP installations). Output is written to `data/5951.json`. The `data/` directory must exist before running.

## Architecture

This is a single-file PHP web scraper ([scratch.php](scratch.php)) that fetches volleyball match data for team "Mut zur Lücke" (ID 5951) from the TFVB website and exports structured JSON.

### Data Pipeline

1. **Team page fetch** — Loads the team page via cURL, extracts team name, logo URL, player roster (names + IDs), and a list of match URLs.

2. **Match page fetch** — For each match URL, fetches the match page and extracts:
   - Opponent name (from breadcrumb navigation)
   - Date (DD.MM.YYYY format → `YYYY-MM-DD HH:MM:SS`)
   - Overall score (regex on score header)
   - Whether the team played home or away (by comparing team name against breadcrumb)
   - Per-game results: which players participated, match type (single/double), and result encoded as `2=win`, `1=tie`, `0=loss`

3. **Output** — Writes `data/{team_id}.json` with a nested structure: `team` (name, logo, players array) and `matches` (array of match objects each containing `games`).

### Key Implementation Details

- HTML parsing uses `DOMDocument` + `DOMXPath` with `@` error suppression for malformed HTML
- Match tables come in two formats (8-column vs 10-column); the parser handles both
- Player names are resolved to IDs using a lookup table built from the roster
- Home/away status affects which column in the match table maps to the local team's players
- Debug output is appended to `debug_*.txt`/`debug_match.html` files in the project root
