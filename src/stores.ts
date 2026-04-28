import { writable, derived } from 'svelte/store'

const API_BASE = import.meta.env.VITE_API_BASE || 'https://mzl.test/api.php'

interface Team {
  name: string
  logo: string
  playerCount: number
}

interface Player {
  id: string
  name: string
}

interface Match {
  id: number
  opponent: string
  date: string
  result: string
  home: boolean
  gameCount: number
}

interface GamePlayer {
  id: string
  name: string
}

interface Game {
  type: string
  result: number
  resultText: string
  players: GamePlayer[]
}

interface MatchDetail {
  opponent: string
  date: string
  result: string
  home: boolean
  games: Game[]
  players: any[]
}

interface PlayerStats {
  name: string
  id: string
  singles: {
    played: number
    wins: number
    losses: number
    ties: number
    pointsEarned: number
    pointsPossible: number
  }
  doubles: {
    played: number
    wins: number
    losses: number
    ties: number
    pointsEarned: number
    pointsPossible: number
  }
  reachPointsPercentage: number
  teamContributionPercentage: number
  efficiency: number
}

// Stores
export const team = writable<Team | null>(null)
export const players = writable<Player[]>([])
export const matches = writable<Match[]>([])
export const stats = writable<any>(null)
export const currentPage = writable<string>('dashboard')
export const selectedMatch = writable<MatchDetail | null>(null)
export const loading = writable<boolean>(false)
export const error = writable<string | null>(null)

// API Functions
async function fetchAPI<T>(endpoint: string, param?: string | number): Promise<T> {
  let url = `${API_BASE}?endpoint=${endpoint}`
  if (param !== undefined) {
    url += `&param=${param}`
  }
  const response = await fetch(url)
  if (!response.ok) throw new Error(`API error: ${response.statusText}`)
  return response.json()
}

export async function loadTeam() {
  try {
    loading.set(true)
    error.set(null)
    const data = await fetchAPI<Team>('team')
    team.set(data)
  } catch (err) {
    error.set(err instanceof Error ? err.message : 'Unknown error')
  } finally {
    loading.set(false)
  }
}

export async function loadPlayers() {
  try {
    loading.set(true)
    error.set(null)
    const data = await fetchAPI<{ players: Player[]; count: number }>('players')
    players.set(data.players)
  } catch (err) {
    error.set(err instanceof Error ? err.message : 'Unknown error')
  } finally {
    loading.set(false)
  }
}

export async function loadMatches() {
  try {
    loading.set(true)
    error.set(null)
    const data = await fetchAPI<{ matches: Match[]; count: number }>('matches')
    matches.set(data.matches)
  } catch (err) {
    error.set(err instanceof Error ? err.message : 'Unknown error')
  } finally {
    loading.set(false)
  }
}

export async function loadMatchDetail(id: number) {
  try {
    loading.set(true)
    error.set(null)
    const data = await fetchAPI<{ match: MatchDetail }>('matches', id)
    selectedMatch.set(data.match)
  } catch (err) {
    error.set(err instanceof Error ? err.message : 'Unknown error')
  } finally {
    loading.set(false)
  }
}

export async function loadStats() {
  try {
    loading.set(true)
    error.set(null)
    const data = await fetchAPI<any>('stats')
    stats.set(data)
  } catch (err) {
    error.set(err instanceof Error ? err.message : 'Unknown error')
  } finally {
    loading.set(false)
  }
}

export async function loadAll() {
  try {
    loading.set(true)
    error.set(null)
    await Promise.all([loadTeam(), loadPlayers(), loadMatches(), loadStats()])
  } catch (err) {
    error.set(err instanceof Error ? err.message : 'Unknown error')
  } finally {
    loading.set(false)
  }
}
