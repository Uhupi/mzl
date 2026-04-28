<script lang="ts">
  import { onMount } from 'svelte'
  import { currentPage, loadAll } from './stores'

  import Dashboard from './pages/Dashboard.svelte'
  import Matches from './pages/Matches.svelte'
  import MatchDetail from './pages/MatchDetail.svelte'
  import Players from './pages/Players.svelte'
  import Statistics from './pages/Statistics.svelte'
  import Navigation from './components/Navigation.svelte'

  let isLoading = true

  onMount(async () => {
    await loadAll()
    isLoading = false
  })
</script>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
  <Navigation />

  <main class="container mx-auto px-4 py-8">
    {#if isLoading}
      <div class="flex items-center justify-center h-64">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
          <p class="text-gray-600">Daten werden geladen...</p>
        </div>
      </div>
    {:else}
      {#if $currentPage === 'dashboard'}
        <Dashboard />
      {:else if $currentPage === 'matches'}
        <Matches />
      {:else if $currentPage === 'match-detail'}
        <MatchDetail />
      {:else if $currentPage === 'players'}
        <Players />
      {:else if $currentPage === 'statistics'}
        <Statistics />
      {/if}
    {/if}
  </main>
</div>

<style>
  :global(html, body) {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
      Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
  }
</style>
