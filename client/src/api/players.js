import axios from '../backend/axios'

export function searchPlayers (data) {
  return axios.get('players', {params: data})
}
