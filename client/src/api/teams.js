import axios from '../backend/axios'

export function getTeam (id) {
  return axios.get('teams/' + id)
}

export function searchTeamsByName (search) {
  return axios.get('teams', {params: {query: search}})
}

export function createTeam (data) {
  return axios.post('teams', data)
}

export function editTeam (id, data) {
  return axios.put('teams/' + id, data)
}

export function deleteTeam (id) {
  return axios.delete('teams/' + id)
}
