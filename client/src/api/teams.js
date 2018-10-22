import axios from '../backend/axios'

export function getTeam (id) {
  return axios.get('teams/' + id)
}
