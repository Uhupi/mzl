<script lang="ts">
  import { stats } from '../stores'
  import StatCard from '../components/StatCard.svelte'

  $: playerStats = $stats?.players || []
  $: teamData = $stats?.team || null

  function getEfficiencyBadgeColor(efficiency: number): string {
    if (efficiency >= 75) return 'bg-green-100 text-green-800'
    if (efficiency >= 60) return 'bg-blue-100 text-blue-800'
    if (efficiency >= 45) return 'bg-yellow-100 text-yellow-800'
    return 'bg-red-100 text-red-800'
  }
</script>

<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-bold text-gray-900 mb-2">📊 Statistiken</h2>
    <p class="text-gray-600">Detaillierte Analyse der Saisonleistung</p>
  </div>

  {#if teamData}
    <!-- Team Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
