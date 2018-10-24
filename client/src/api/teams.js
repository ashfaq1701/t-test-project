import axios from '../backend/axios'

export function getTeam (id) {
  return axios.get('teams/' + id)
}

export function searchTeamsByName (search) {
  return axios.get('teams', {params: {query: search}})
}

export function searchTeams (data) {
  return axios.get('teams', {params: data})
}

export function editTeam (id, data) {
  return axios.put('teams/' + id, data)
}
