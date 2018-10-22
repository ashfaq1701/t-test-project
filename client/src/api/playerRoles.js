import axios from '../backend/axios'

export function getPlayerRoles () {
  return axios.get('player-roles')
}
