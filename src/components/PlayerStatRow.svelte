<script lang="ts">
  export let player

  $: pointsEarned = player.pointsEarned || 0
  $: pointsPossible = player.pointsPossible || 0
  $: efficiency = player.efficiency || 0
</script>

<tr class="border-b hover:bg-gray-50 transition">
  <td class="py-3 px-4 font-semibold text-gray-900">{player.name}</td>
  <td class="py-3 px-4 text-center">{player.gamesPlayed || 0}</td>
  <td class="py-3 px-4 text-center text-green-600 font-semibold">{player.wins || 0}</td>
  <td class="py-3 px-4 text-center text-red-600 font-semibold">{player.losses || 0}</td>
  <td class="py-3 px-4 text-center">
    <div class="font-semibold text-gray-900">{Math.round(pointsEarned)}</div>
    <div class="text-xs text-gray-500">von {Math.round(pointsPossible)}</div>
  </td>
  <td class="py-3 px-4 text-center">
    <span class={`inline-block px-3 py-1 rounded-full text-sm font-bold ${
      efficiency >= 75 ? 'bg-green-100 text-green-800' :
      efficiency >= 60 ? 'bg-blue-100 text-blue-800' :
      efficiency >= 45 ? 'bg-yellow-100 text-yellow-800' :
      'bg-red-100 text-red-800'
    }`}>
      {efficiency}%
    </span>
  </td>
</tr>
