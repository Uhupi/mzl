import { loadMatchDetail } from '$lib/stores'

export async function load({ params }) {
  await loadMatchDetail(Number(params.id))
  return {}
}
