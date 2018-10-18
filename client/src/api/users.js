import axios from '../backend/axios'

export function getUsers (page) {
  return axios.get('users', {params: {page: page}})
}

export function getUser (id) {
  return axios.get('users/' + id)
}

export function createUser (data) {
  return axios.post('users', data)
}

export function editUser (id, data) {
  return axios.put('users/' + id, data)
}

export function deleteUser (id) {
  return axios.delete('users/' + id)
}

export function updateProfile (data) {
  return axios.post('profile', data)
}

export function updatePassword (password, confirmPassword, oldPassword) {
  let payload = {
    password: password,
    password_confirmation: confirmPassword,
    old_password: oldPassword
  }
  return axios.post('profile/update-password', payload)
}

export function deleteOwnUser () {
  return axios.get('profile/delete-user')
}

export function searchUsersPaginated (query, page) {
  return axios.get('users', {params: {page: page, query: query}})
}
