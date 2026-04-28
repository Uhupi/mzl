<script lang="ts">
  export let label: string
  export let value: string | number
</script>

<div class="stat-card">
  <div class="stat-value">{value}</div>
  <div class="stat-label">{label}</div>
</div>
