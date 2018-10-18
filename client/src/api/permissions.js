import axios from '../backend/axios'

export function getPermissions () {
  return axios.get('permissions')
}
