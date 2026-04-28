# Kicker Statistiken - SPA Frontend

Eine elegante, responsive Single-Page-Application (SPA) für Tischfussball/Kicker Statistiken und Spielergebnisse.

## Features

- 📊 **Übersichtliches Dashboard** mit Team- und Spielerstatistiken
- 🎯 **Spielverlauf** - alle Spiele mit Details und Ergebnissen
- 👥 **Spielerverwaltung** - Team-Übersicht aller Spieler
- 📈 **Detaillierte Statistiken** - Leistungsmetriken und Rankings
- 📱 **Responsive Design** - optimiert für Desktop, Tablet und Mobile
- 🎨 **Modernes UI** - gebaut mit Tailwind CSS
- ⚡ **Schnelle Performance** - Vite + Svelte

## Technologie Stack

- **Svelte 4.2** - Modernes Frontend-Framework
- **Vite 5.0** - Blitzschneller Build-Tool
- **Tailwind CSS 3.4** - Utility-first CSS Framework
- **TypeScript 5.3** - Type-safe Development
- **Chart.js 4.4** - Datenvisualisierung (optional)

## Installation & Setup

### Voraussetzungen
- Node.js 16+ und npm/yarn

### Installation

```bash
cd frontend
npm install
```

### Development Server starten

```bash
npm run dev
```

Der Server läuft dann auf `http://localhost:5173`

Der API-Proxy ist konfiguriert unter:
- API Base: `http://localhost:8000`

### Production Build

```bash
npm run build
npm run preview
```

## Verfügbare API-Endpunkte

Die App konsumiert folgende Endpunkte von der PHP API:

- `GET /api/team` - Team-Info (Name, Logo, Spieleranzahl)
- `GET /api/players` - Liste aller Spieler
- `GET /api/matches` - Liste aller Spiele
- `GET /api/matches/{id}` - Details eines spezifischen Spiels
- `GET /api/stats` - Gesamtstatistiken (Team & Spieler)

## Projektstruktur

```
src/
├── App.svelte              # Main App Component
├── app.css                 # Tailwind & Global Styles
├── main.ts                 # Entry Point
├── stores.ts               # Svelte Stores & API Functions
├── utils.ts                # Utility Functions
│
├── pages/                  # Page Components
│   ├── Dashboard.svelte
│   ├── Matches.svelte
│   ├── MatchDetail.svelte
│   ├── Players.svelte
│   └── Statistics.svelte
│
└── components/             # Reusable Components
    ├── Navigation.svelte
    ├── StatCard.svelte
    ├── MatchCard.svelte
    └── PlayerStatRow.svelte
```

## Seiten & Features

### Dashboard
- Team-Übersicht mit Logo
- Gesamt-Statistiken (Spiele, Siege, Quote)
- Einzel- & Doppel-Bilanz
- Top 5 Spieler Rankings
- Letzte 5 Spiele

### Spiele
- Sortierte Liste aller Spiele (neueste zuerst)
- Ergebnis, Gegner, Datum & Ort
- Click-through zu Spieldetails

### Spieldetails
- Detaillierte Spielinformationen
- Spielerstatistiken pro Match
- Effizienzbewertung
- Einzelne Spiel-Einträge

### Spieler
- Team-Rosette in Kartenlayout
- Initials-Avatar
- Spieler-IDs

### Statistiken
- Team-Bilanz (Einzel & Doppel)
- Alle Spieler nach Effizienz gereiht
- Detaillierte Metriken pro Spieler:
  - Punkte-Prozentatz
  - Team-Beitrag
  - Effizienz-Score
  - Einzel & Doppel Records

## Styling & Design

Das Design nutzt:
- **Tailwind CSS** für Responsive Grid-Layouts
- **Gradient Backgrounds** für visuelle Hierarchie
- **Color-coded Badges**:
  - 🟢 Grün für Siege
  - 🔴 Rot für Niederlagen
  - 🟡 Gelb für Unentschieden
- **Hover Effects** & Transitions für bessere UX
- **Mobile-first** Responsive Design

## API-Verbindung

Die App verwendet Svelte Stores für State Management:
- `team` - Team-Informationen
- `players` - Spielerliste
- `matches` - Spieleliste
- `stats` - Detaillierte Statistiken
- `currentPage` - Navigation State

API-Calls werden automatisch beim App-Start durchgeführt.

## Development Tips

### Svelte Best Practices
- Komponenten sind reaktiv durch Store Subscriptions (`$variable`)
- Props werden mit `export let` definiert
- Styling mit `<style>` block scoped ist Standard

### Adding New Features
1. Neue Page? → `src/pages/NewPage.svelte`
2. Neue Component? → `src/components/NewComponent.svelte`
3. State Management? → Update `src/stores.ts`
4. Styling? → Nutze Tailwind Classes + `src/app.css`

## Fehlerbehandlung

- Loading-State während API-Calls
- Error-Handling mit visuellen Fehlermeldungen
- Fallback-Content wenn API-Daten fehlschlagen

## Performance Optimierung

- Svelte's reactivity ist automatisch optimiert
- CSS wird minimiert beim Build
- Code-Splitting durch Vite
- Lazy Loading möglich für Components

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Lizenz

MIT

## Support

Fragen oder Bugs? Bitte in den Issues melden!
