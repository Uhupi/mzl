<script lang="ts">
  import { currentPage, team } from '../stores'

  const navItems = [
    { id: 'matches', label: '🎯 Spiele', icon: '🎯' },
    { id: 'players', label: '👥 Spieler', icon: '👥' },
    { id: 'statistics', label: '📈 Statistiken', icon: '📈' }
  ]
</script>

<header class="bg-white shadow-md sticky top-0 z-50">
  <nav class="container mx-auto px-4 py-4">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-3">
        <span class="text-3xl">⚽</span>
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {$team?.name || 'Kicker Statistiken'}
          </h1>
          {#if $team?.logo}
            <img src={$team.logo} alt="Team Logo" class="h-8 mt-1 opacity-70" />
          {/if}
        </div>
      </div>
    </div>

    <div class="flex gap-2 overflow-x-auto">
      {#each navItems as item (item.id)}
        <button
          on:click={() => currentPage.set(item.id)}
          class={`whitespace-nowrap px-4 py-2 rounded-lg font-medium transition-all ${
            $currentPage === item.id
              ? 'bg-blue-600 text-white shadow-md'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          }`}
        >
          {item.label}
        </button>
      {/each}
    </div>
  </nav>
</header>

<style>
  :global(body) {
    padding-top: 0;
  }
</style>
