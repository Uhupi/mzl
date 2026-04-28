<script lang="ts">
  import { matches, currentPage, loadMatchDetail } from '../stores'

  async function viewMatch(id: number) {
    await loadMatchDetail(id)
    currentPage.set('match-detail')
  }

  $: sortedMatches = [...($matches || [])].sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime())
</script>

<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-bold text-gray-900 mb-2">🎯 Spiele</h2>
    <p class="text-gray-600">Alle Turnier- und Ligaspiele im Überblick</p>
  </div>

  {#if sortedMatches.length === 0}
    <div class="card p-8 text-center">
      <p class="text-gray-600">Keine Spiele gefunden</p>
    </div>
  {:else}
    <div class="grid gap-4">
      {#each sortedMatches as match (match.id)}
        <button
          on:click={() => viewMatch(match.id)}
          class="card p-6 hover:shadow-xl transition-all text-left"
        >
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <h3 class="text-xl font-bold text-gray-900 mb-2">
                {match.opponent}
              </h3>
              <p class="text-sm text-gray-600 mb-2">
                {new Date(match.date).toLocaleDateString('de-DE', {
                  weekday: 'long',
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric'
                })} · {new Date(match.date).toLocaleTimeString('de-DE', {
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </p>
              <div class="flex gap-2 flex-wrap">
                <span class="inline-block bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                  {match.gameCount} {match.gameCount === 1 ? 'Spiel' : 'Spiele'}
                </span>
                <span class="inline-block bg-blue-200 text-blue-700 px-2 py-1 rounded text-xs">
                  {match.home ? '🏠 Heim' : '🚗 Auswärts'}
                </span>
              </div>
            </div>
            <div class="text-right">
              <div class={`text-3xl font-bold mb-2 ${
                String(match.result).startsWith('2:') ? 'text-green-600' :
                String(match.result).endsWith(':2') ? 'text-red-600' :
                'text-yellow-600'
              }`}>
                {match.result}
              </div>
              <span class="text-xs text-gray-600">Ergebnis</span>
            </div>
          </div>
        </button>
      {/each}
    </div>
  {/if}
</div>
