<script lang="ts">
  import { team, matches, stats } from '../stores'
  import StatCard from '../components/StatCard.svelte'
  import { calculateWinRate } from '../utils'

  $: teamStats = $stats?.team || null
  $: playerStats = $stats?.players || []
  $: topPlayers = playerStats.slice(0, 5)
</script>

<div class="space-y-2">
  <!-- Hero Section -->
  <div class="card p-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
    <h2 class="text-4xl font-bold mb-2">⚽ {$team?.name}</h2>
    <p class="text-blue-100">Kicker Statistiken & Spielergebnisse</p>
  </div>

  <!-- Team Stats Overview -->
  {#if teamStats}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <StatCard label="Gesamt Spiele" value={teamStats.matches} />
      <StatCard label="Einzel Siege" value={teamStats.singles.wins} />
      <StatCard label="Doppel Siege" value={teamStats.doubles.wins} />
      <StatCard
        label="Sieg Quote"
        value="{calculateWinRate(
          teamStats.singles.wins + teamStats.doubles.wins,
          teamStats.singles.losses + teamStats.doubles.losses,
          teamStats.singles.ties + teamStats.doubles.ties
        )}%"
      />
    </div>

    <!-- Detailed Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Singles Stats -->
      <div class="card p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">📍 Einzel</h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-600">Siege</span>
            <span class="text-2xl font-bold text-green-600">{teamStats.singles.wins}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-600">Niederlagen</span>
            <span class="text-2xl font-bold text-red-600">{teamStats.singles.losses}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Unentschieden</span>
            <span class="text-2xl font-bold text-yellow-600">{teamStats.singles.ties}</span>
          </div>
        </div>
      </div>

      <!-- Doubles Stats -->
      <div class="card p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">👥 Doppel</h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-600">Siege</span>
            <span class="text-2xl font-bold text-green-600">{teamStats.doubles.wins}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-600">Niederlagen</span>
            <span class="text-2xl font-bold text-red-600">{teamStats.doubles.losses}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-gray-600">Unentschieden</span>
            <span class="text-2xl font-bold text-yellow-600">{teamStats.doubles.ties}</span>
          </div>
        </div>
      </div>
    </div>
  {/if}

  <!-- Top Players -->
  {#if topPlayers.length > 0}
    <div class="card p-6">
      <h3 class="text-2xl font-bold text-gray-900 mb-6">⭐ Top Spieler</h3>
      <div class="space-y-4">
        {#each topPlayers as player, i}
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex items-center gap-4">
              <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full font-bold">
                {i + 1}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{player.name}</p>
                <p class="text-sm text-gray-600">
                  Effizienz: <span class="font-bold">{player.efficiency}%</span>
                </p>
              </div>
            </div>
            <div class="text-right">
              <p class="text-2xl font-bold text-blue-600">{player.efficiency}</p>
              <p class="text-xs text-gray-500">Punkte: {(player.singles.pointsEarned + player.doubles.pointsEarned).toFixed(1)}</p>
            </div>
          </div>
        {/each}
      </div>
    </div>
  {/if}

  <!-- Recent Matches -->
  {#if $matches.length > 0}
    <div class="card p-6">
      <h3 class="text-2xl font-bold text-gray-900 mb-6">🏆 Letzte Spiele</h3>
      <div class="space-y-3">
        {#each $matches.slice(0, 5) as match (match.id)}
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div>
              <p class="font-semibold text-gray-900">{match.opponent}</p>
              <p class="text-sm text-gray-600">
                {new Date(match.date).toLocaleDateString('de-DE', {
                  weekday: 'short',
                  year: 'numeric',
                  month: 'short',
                  day: 'numeric'
                })}
                {match.home ? '🏠 (Heim)' : '🚗 (Auswärts)'}
              </p>
            </div>
            <div class="text-right">
              <div class={String(match.result).startsWith('2:') ? 'badge-win' : String(match.result).endsWith(':2') ? 'badge-loss' : 'badge-tie'}>
                {match.result}
              </div>
              <p class="text-xs text-gray-600 mt-2">{match.gameCount} Spiele</p>
            </div>
          </div>
        {/each}
      </div>
    </div>
  {/if}
</div>
