<script lang="ts">
  import { page } from '$app/stores'
  import { team } from '../stores'

  const navItems = [
    { path: '/matches', label: '🎯 Spiele', icon: '🎯' },
    { path: '/players', label: '👥 Spieler', icon: '👥' },
    { path: '/statistics', label: '📈 Statistiken', icon: '📈' }
  ]
</script>

<header class="bg-white shadow-md sticky top-0 z-50">
  <nav class="container mx-auto px-2 py-2">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center gap-2">
            {#if $team?.logo}
                <img src={$team.logo} alt="Team Logo" class="h-8 mt-1 opacity-70" />
            {/if}
            <h1 class="text-2xl font-bold text-gray-900">
                {$team?.name || 'Kicker Statistiken'}
            </h1>
        </div>
    </div>

    <div class="flex gap-2 overflow-x-auto">
      {#each navItems as item (item.path)}
        <a
          href={item.path}
          data-sveltekit-reload={false}
          class={`whitespace-nowrap px-2 py-2 rounded-lg font-medium transition-all ${
            $page.url.pathname === item.path
              ? 'bg-blue-600 text-white shadow-md'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
          }`}
        >
          {item.label}
        </a>
      {/each}
    </div>
  </nav>
</header>

<style>
  :global(body) {
    padding-top: 0;
  }
</style>
