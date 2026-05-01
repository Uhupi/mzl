<script lang="ts">
  import { stats, doublePairs, matches, selectedDate } from '../stores'
  import { loadStats, loadDoublePairs, getAvailableDates } from '../stores'
  import StatCard from '../components/StatCard.svelte'

  let availableDates: string[] = []

  $: playerStats = $stats?.players || []
  $: teamData = $stats?.team || null
  $: pairs = ($doublePairs || []).filter((p: any) => (p.netWins || 0) >= 0)
  $: if ($matches.length > 0) availableDates = getAvailableDates()

  function getEfficiencyBadgeColor(efficiency: number): string {
    if (efficiency >= 75) return 'bg-green-100 text-green-800'
    if (efficiency >= 60) return 'bg-blue-100 text-blue-800'
    if (efficiency >= 45) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
  }

  async function handleDateChange(e: Event) {
    const target = e.target as HTMLSelectElement
    const date = target.value || null
    selectedDate.set(date)

    if (date) {
      await Promise.all([loadStats(date), loadDoublePairs(date)])
    } else {
      await Promise.all([loadStats(), loadDoublePairs()])
    }
  }
</script>

<div class="space-y-6">
  <div class="flex flex-col gap-4">
    <div>
      <h2 class="text-3xl font-bold text-gray-900 mb-2">📊 Statistiken</h2>
      <p class="text-gray-600">Detaillierte Analyse der Saisonleistung</p>
    </div>

    {#if availableDates.length > 0}
      <div class="card p-4">
        <label for="date-select" class="block text-sm font-medium text-gray-700 mb-2">
          Statistiken ab Spiel:
        </label>
        <select
          id="date-select"
          on:change={handleDateChange}
          value={$selectedDate || ''}
          class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
        >
          <option value="">Alle Spiele</option>
          {#each availableDates as date}
            <option value={date}>{date}</option>
          {/each}
        </select>
      </div>
    {/if}
  </div>

  {#if teamData}
    <!-- Team Overview -->
    <div class="grid grid-cols-1 grid-cols-3 gap-4">
      <StatCard label="Gesamt Spiele" value={teamData.matches} />
      <StatCard label="Gesamt Siege" value={teamData.singles.wins + teamData.doubles.wins} />
      <StatCard label="Sieg-Quote" value="{Math.round((
        (teamData.singles.wins + teamData.doubles.wins) /
        (teamData.singles.wins + teamData.singles.losses + teamData.singles.ties +
         teamData.doubles.wins + teamData.doubles.losses + teamData.doubles.ties)
      ) * 100)}%" />
    </div>

    <!-- Singles vs Doubles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="card p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">📍 Einzel Bilanz</h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center pb-3 border-b">
            <span class="text-gray-600 font-medium">Siege</span>
            <span class="text-3xl font-bold text-green-600">{teamData.singles.wins}</span>
          </div>
          <div class="flex justify-between items-center pb-3 border-b">
            <span class="text-gray-600 font-medium">Niederlagen</span>
            <span class="text-3xl font-bold text-red-600">{teamData.singles.losses}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600 font-medium">Unentschieden</span>
            <span class="text-3xl font-bold text-yellow-600">{teamData.singles.ties}</span>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">👥 Doppel Bilanz</h3>
        <div class="space-y-4">
          <div class="flex justify-between items-center pb-3 border-b">
            <span class="text-gray-600 font-medium">Siege</span>
            <span class="text-3xl font-bold text-green-600">{teamData.doubles.wins}</span>
          </div>
          <div class="flex justify-between items-center pb-3 border-b">
            <span class="text-gray-600 font-medium">Niederlagen</span>
            <span class="text-3xl font-bold text-red-600">{teamData.doubles.losses}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600 font-medium">Unentschieden</span>
            <span class="text-3xl font-bold text-yellow-600">{teamData.doubles.ties}</span>
          </div>
        </div>
      </div>
    </div>
  {/if}

  <!-- Best Double Pairs -->
  {#if pairs && pairs.length > 0}
    <div class="card p-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">👥 Top Doppel-Paarungen</h2>
      <div class="space-y-4">
        {#each pairs as pair, idx (idx)}
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center gap-4">
              <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-bold">
                {idx + 1}
              </div>
              <div>
                <p class="font-semibold text-gray-900">
                  {pair.pair ? `${pair.pair[0]} & ${pair.pair[1]}` : pair.players?.map(p => p.name).join(' & ') || 'Unbekannt'}
                </p>
                <p class="text-sm text-gray-600">
                  {pair.wins || 0}W - {pair.losses || 0}L {pair.ties && pair.ties > 0 ? `- ${pair.ties}T` : ''}
                </p>
              </div>
            </div>
            <div class="text-right">
              <div class="text-3xl font-bold mb-1" class:text-green-600={(pair.netWins || 0) > 0} class:text-red-600={(pair.netWins || 0) < 0} class:text-gray-600={(pair.netWins || 0) === 0}>
                {(pair.netWins || 0) >= 0 ? '+' : ''}{pair.netWins ?? 0}
              </div>
              <p class="text-xs text-gray-500">Netto Punkte</p>
            </div>
          </div>
        {/each}
      </div>
    </div>
  {/if}

  <!-- Player Rankings -->
  <div class="card p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">⭐ Spieler Ranking</h2>

    {#if playerStats.length === 0}
      <p class="text-gray-600">Keine Daten verfügbar</p>
    {:else}
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b-2 border-gray-200">
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Rang</th>
              <th class="text-left py-3 px-4 font-semibold text-gray-700">Spieler</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Einzel</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Doppel</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Punkte %</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Beitrag %</th>
              <th class="text-center py-3 px-4 font-semibold text-gray-700">Effizienz</th>
            </tr>
          </thead>
          <tbody>
            {#each playerStats as player, idx (player.id)}
              <tr class="border-b hover:bg-gray-50 transition">
                <td class="py-3 px-4">
                  <div class="flex items-center justify-center w-8 h-8 bg-gray-200 rounded-full font-bold text-sm">
                    {idx + 1}
                  </div>
                </td>
                <td class="py-3 px-4 font-semibold text-gray-900">{player.name}</td>
                <td class="py-3 px-4 text-center text-sm">
                  <span class="text-gray-700">{player.singles.played}</span>
                  <span class="text-gray-500 text-xs"> ({player.singles.wins}S)</span>
                </td>
                <td class="py-3 px-4 text-center text-sm">
                  <span class="text-gray-700">{player.doubles.played}</span>
                  <span class="text-gray-500 text-xs"> ({player.doubles.wins}S)</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="font-semibold text-gray-900">{player.reachPointsPercentage}%</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class="font-semibold text-gray-900">{player.teamContributionPercentage}%</span>
                </td>
                <td class="py-3 px-4 text-center">
                  <span class={`font-bold px-3 py-1 rounded-full text-sm ${getEfficiencyBadgeColor(player.efficiency)}`}>
                    {player.efficiency}%
                  </span>
                </td>
              </tr>
            {/each}
          </tbody>
        </table>
      </div>
    {/if}
  </div>

  <!-- Performance Legend -->
  <div class="card p-6 bg-gray-50">
    <h3 class="font-semibold text-gray-900 mb-4">📖 Legende</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
      <div>
        <p><strong>Punkte %:</strong> Anteil der erreichten Punkte von möglichen Punkten</p>
        <p class="mt-2"><strong>Beitrag %:</strong> Prozentuale Beteiligung an Team-Gesamtpunkten</p>
      </div>
      <div>
        <p><strong>Effizienz:</strong> Durchschnitt aus Punkte% und Beitrag%</p>
        <p class="mt-2"><strong>Einzel/Doppel:</strong> (Spiele gespielt) und Siege</p>
      </div>
    </div>
  </div>
</div>
