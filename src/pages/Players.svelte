<script lang="ts">
  import { players } from '../stores'
</script>

<div class="space-y-6">
  <div>
    <h2 class="text-3xl font-bold text-gray-900 mb-2">👥 Spieler</h2>
    <p class="text-gray-600">Team der Saison</p>
  </div>

  {#if $players.length === 0}
    <div class="card p-8 text-center">
      <p class="text-gray-600">Keine Spieler gefunden</p>
    </div>
  {:else}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      {#each $players as player (player.id)}
        <div class="card p-6 hover:shadow-lg transition-shadow">
          <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
              {player.name.charAt(0).toUpperCase()}
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">{player.name}</h3>
              <p class="text-xs text-gray-500">ID: {player.id}</p>
            </div>
          </div>
        </div>
      {/each}
    </div>
  {/if}
</div>
