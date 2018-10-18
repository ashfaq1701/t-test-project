import axios from '../backend/axios'

export function login (email, password) {
  return axios.post('login', {email: email, password: password})
}

export function logout () {
  return axios.get('logout')
}

export function register (name, email, password) {
  return axios.post('register', {name: name, email: email, password: password, password_confirmation: password})
}

export function getUser () {
  return axios.get('user')
}

export function refreshToken (refreshToken) {
  return axios.get('refresh', { headers: {'Refresh-Token': refreshToken} })
}

export function requestPasswordReset (email) {
  return axios.post('password/email', {email: email})
}

export function resetPassword (data) {
  return axios.post('password/reset', data)
}

export function resendConfirmationEmail (email) {
  return axios.get('confirmation/resend', {params: {email: email}})
}

export function confirmAccount (email, token) {
  return axios.get('confirmation', {params: {email: email, confirmation_token: token}})
}
