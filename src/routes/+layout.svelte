<script lang="ts">
  import { onMount } from 'svelte'
  import { loadAll } from '$lib/stores'
  import Navigation from '$lib/components/Navigation.svelte'
  import '../lib/app.css'

  let isLoading = true

  onMount(async () => {
    await loadAll()
    isLoading = false
  })
</script>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
  <Navigation />

  <main class="container mx-auto px-2 py-2">
    {#if isLoading}
      <div class="flex items-center justify-center h-64">
        <div class="text-center">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
          <p class="text-gray-600">Daten werden geladen...</p>
        </div>
      </div>
    {:else}
      <slot />
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
