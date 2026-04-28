# 🎯 Kicker Statistiken - Schnellstart Guide

## Installation (5 Minuten)

### 1. Dependencies installieren
```bash
cd frontend
npm install
```

### 2. Development Server starten
```bash
npm run dev
```

Der Browser öffnet sich automatisch auf `http://localhost:5173`

## 🏗️ Backend Setup

Die SPA benötigt die PHP API im Hintergrund. Stelle sicher, dass:

1. **PHP Server läuft** auf `http://localhost:8000`
   ```bash
   cd ..  # Parent directory mit den PHP-Dateien
   php -S localhost:8000
   ```

2. **Daten vorhanden** - Schraper muss ausgeführt worden sein:
   ```bash
   php scratch.php
   ```
   Dies erstellt `data/5951.json` mit den Spieldaten.

## 📋 API Endpoints

Die App nutzt diese Endpoints:

```
GET /api/team                 → Team Info (Name, Logo, Spieleranzahl)
GET /api/players              → Liste aller Spieler
GET /api/matches              → Liste aller Spiele
GET /api/matches/{id}         → Details für Spiel mit ID
GET /api/stats                → Alle Statistiken
```

## ✅ Checkliste vor dem Start

- [ ] `npm install` ausgeführt
- [ ] `php scratch.php` ausgeführt (um Daten zu generieren)
- [ ] PHP Server läuft auf `localhost:8000`
- [ ] Dev Server läuft auf `localhost:5173`
- [ ] Browser zeigt die App ohne Fehler

## 🚀 Production Deployment

### Build erstellen
```bash
npm run build
```

Dies erstellt einen `dist/` Ordner mit optimiertem Code.

### Produktive Version testen
```bash
npm run preview
```

## 📱 Features der App

### Dashboard
- Team-Übersicht mit Statistiken
- Top 5 Spieler
- Letzte 5 Spiele
- Einzel- & Doppel-Bilanz

### Spiele
- Alle Spiele sortiert nach Datum
- Klick auf Spiel = Detailansicht
- Gegner, Ergebnis, Ort (Heim/Auswärts)

### Spieler
- Team-Rosette
- Alle registrierten Spieler

### Statistiken
- Detaillierte Spieler Rankings
- Team-Bilanz (Einzel vs. Doppel)
- Effizienz-Scores
- Punkte-Prozentsätze

## 🎨 Styling & Anpassungen

Das Design nutzt **Tailwind CSS**. Um Farben oder Layouts zu ändern:

1. **Colors** → `frontend/tailwind.config.js`
2. **Global Styles** → `frontend/src/app.css`
3. **Component Styles** → `<style>` in `.svelte` Dateien

## 🔧 Troubleshooting

### API 404 Fehler?
- Stelle sicher, dass PHP Server auf `localhost:8000` läuft
- Verifiziere, dass `data/5951.json` existiert
- Führe `php scratch.php` aus

### App lädt nicht?
- Refresh Browser (Ctrl+Shift+R für Hard Refresh)
- Prüfe Browser Console auf Fehler (F12)
- Stelle sicher npm Server läuft (`npm run dev`)

### Daten werden nicht angezeigt?
- Öffne Browser DevTools (F12)
- Gehe zu Network Tab
- Prüfe ob API Requests erfolgreich sind (Status 200)

## 📚 Weitere Ressourcen

- [Svelte Dokumentation](https://svelte.dev/docs)
- [Tailwind CSS Dokumentation](https://tailwindcss.com)
- [Vite Dokumentation](https://vitejs.dev)

## 💡 Development Tips

### Hot Module Replacement (HMR)
Änderungen werden automatisch im Browser aktualisiert - kein Refresh nötig!

### TypeScript
Die App ist TypeScript-ready. Nutze `export let variable: Type` für Props.

### Console Logging
```svelte
<script>
  console.log('Debug Info:', $storeName)
</script>
```

## 🐛 Bugs oder Fragen?

Prüfe:
1. Browser Console (F12 → Console Tab)
2. Network Tab für API Fehler
3. Logs in PHP Server Terminal

---

**Viel Erfolg mit der Kicker Statistik App! ⚽**
