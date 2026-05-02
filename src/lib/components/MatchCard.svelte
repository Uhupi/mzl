<script lang="ts">
  export let match
  export let onClick: () => void = () => {}
</script>

<button
  on:click={onClick}
  class="card p-6 hover:shadow-xl transition-all text-left"
>
  <div class="flex items-center justify-between">
    <div class="flex-1">
      <h3 class="text-xl font-bold text-gray-900 mb-2">
        {match.opponent}
      </h3>
      <p class="text-sm text-gray-600">
        {new Date(match.date).toLocaleDateString('de-DE', {
          weekday: 'short',
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        })}
      </p>
    </div>
    <div class="text-right">
      <div class="text-2xl font-bold">{match.result}</div>
      <span class="text-xs text-gray-600">{match.gameCount} Spiele</span>
    </div>
  </div>
</button>
