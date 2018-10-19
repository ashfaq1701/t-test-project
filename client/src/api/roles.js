import axios from '../backend/axios'

export function getAllRoles () {
  return axios.get('roles')
}

export function getRoles (page) {
  return axios.get('roles', {params: {page: page}})
}
