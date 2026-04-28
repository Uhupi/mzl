<script lang="ts">
  import { selectedMatch, currentPage } from '../stores'
  import PlayerStatRow from '../components/PlayerStatRow.svelte'

  const gameTypeLabel = {
    single: '📍 Einzel',
    double: '👥 Doppel'
  }

  const resultColors = {
    0: { class: 'badge-loss', text: 'Niederlage' },
    1: { class: 'badge-tie', text: 'Unentschieden' },
    2: { class: 'badge-win', text: 'Sieg' }
  }
</script>

{#if $selectedMatch}
  <div class="space-y-6">
    <!-- Header -->
    <button
      on:click={() => currentPage.set('matches')}
      class="text-blue-600 hover:text-blue-700 font-medium mb-4"
    >
      ← Zurück zu Spielen
    </button>

    <div class="card p-8 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-4xl font-bold mb-2">
            Mut zur Lücke vs. {$selectedMatch.opponent}
          </h1>
          <p class="text-blue-100">
            {new Date($selectedMatch.date).toLocaleDateString('de-DE', {
              weekday: 'long',
              year: 'numeric',
              month: 'long',
              day: 'numeric'
            })}
            · {$selectedMatch.home ? '🏠 Heimspiel' : '🚗 Auswärtsspiel'}
          </p>
        </div>
        <div class="text-right">
          <div class="text-5xl font-bold">{$selectedMatch.result}</div>
          <p class="text-blue-100 mt-2">Endergebnis</p>
        </div>
      </div>
    </div>

    <!-- Player Statistics -->
    <div class="card p-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">👥 Spielerstatistiken</h2>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b-2 border-gray-200">
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Spieler</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Spiele</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Siege</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Niederlagen</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Punkte</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Effizienz</th>
            </tr>
          </thead>
          <tbody>
            {#each $selectedMatch.players as player (player.id)}
              <PlayerStatRow {player} />
            {/each}
          </tbody>
        </table>
      </div>
    </div>

    <!-- Games Detail -->
    <div class="card p-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">🎯 Spieldetails</h2>
      <div class="space-y-4">
        {#each $selectedMatch.games as game, idx (idx)}
          <div class="border rounded-lg p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-3">
              <span class="font-semibold text-gray-900">
                {gameTypeLabel[game.type] || game.type}
              </span>
              <span class={resultColors[game.result]?.class || ''}>
                {resultColors[game.result]?.text || 'Unbekannt'}
              </span>
            </div>
            <div class="flex flex-wrap gap-2">
              {#each game.players as player (player.id)}
                <span class="inline-block bg-white border border-gray-300 rounded-full px-3 py-1 text-sm">
                  {player.name}
                </span>
              {/each}
            </div>
          </div>
        {/each}
      </div>
    </div>
  </div>
{:else}
  <div class="text-center py-8">
    <p class="text-gray-600">Spiel nicht gefunden</p>
    <button
      on:click={() => currentPage.set('matches')}
      class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
    >
      Zurück zu Spielen
    </button>
  </div>
{/if}
