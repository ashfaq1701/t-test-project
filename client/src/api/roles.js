import axios from '../backend/axios'

export function getAllRoles () {
  return axios.get('roles')
}

export function getRoles (page) {
  return axios.get('roles', {params: {page: page}})
}

export function searchRoles (search) {
  return axios.get('roles', {params: {query: search}})
}

export function getRole (id) {
  return axios.get('roles/' + id)
}

export function createRole (data) {
  return axios.post('roles', data)
}

export function editRole (id, data) {
  return axios.put('roles/' + id, data)
}

export function deleteRole (id) {
  return axios.delete('roles/' + id)
}
