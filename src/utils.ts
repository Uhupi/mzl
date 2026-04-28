export function formatDate(dateStr: string): string {
  try {
    const date = new Date(dateStr)
    return date.toLocaleDateString('de-DE', {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch {
    return dateStr
  }
}

export function formatTime(dateStr: string): string {
  try {
    const date = new Date(dateStr)
    return date.toLocaleTimeString('de-DE', {
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return ''
  }
}

export function getResultBadgeClass(result: string): string {
  if (result === '2:0' || result === '2:1') return 'badge-win'
  if (result === '0:2' || result === '1:2') return 'badge-loss'
  return 'badge-tie'
}

export function getResultText(result: string): string {
  if (result === '2:0' || result === '2:1') return 'Sieg'
  if (result === '0:2' || result === '1:2') return 'Niederlage'
  return 'Unentschieden'
}

export function calculateWinRate(wins: number, losses: number, ties: number): number {
  const total = wins + losses + ties
  if (total === 0) return 0
  return Math.round((wins / total) * 100)
}

export function calculateEfficiency(reachPoints: number, contribution: number): number {
  return Math.round((reachPoints + contribution) / 2)
}

export function getEfficiencyColor(efficiency: number): string {
  if (efficiency >= 75) return 'text-green-600'
  if (efficiency >= 60) return 'text-blue-600'
  if (efficiency >= 45) return 'text-yellow-600'
  return 'text-red-600'
}
