import axios from '../backend/axios'

export function searchPlayers (data) {
  return axios.get('players', {params: data})
}

export function createPlayer (data) {
  return axios.post('players', data)
}

export function editPlayer (id, data) {
  return axios.put('players/' + id, data)
}

export function deletePlayer (id) {
  return axios.delete('players/' + id)
}
