import axios from 'axios'
import moment from 'moment'
import {refreshToken} from '../api/auth'

const API_URL = process.env.API_URL || 'http://localhost:3000/api'

let instance = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Authorization': {
      toString () {
        return `Bearer ${localStorage.getItem('token')}`
      }
    },
    'Accept': 'application/json'
  }
})

instance.interceptors.response.use(function (response) {
  return response
}, function (error) {
  const originalRequest = error.config

  if (error.response.status === 401 && !originalRequest._retry) {
    originalRequest._retry = true

    if (moment() < moment(localStorage.getItem('refresh_expire_at'))) {
      return refreshToken(localStorage.getItem('refresh_token')).then(function (response) {
        storeTokens(response.data)
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('token')
        originalRequest.headers['Authorization'] = 'Bearer ' + localStorage.getItem('token')
        return axios(originalRequest)
      })
    } else {
      removeTokens()
    }
  }

  return Promise.reject(error)
})

function storeTokens (data) {
  localStorage.setItem('token', data.token)
  localStorage.setItem('refresh_token', data.refresh_token)
  localStorage.setItem('refresh_expire_at', data.refresh_expire_at)
}

function removeTokens () {
  localStorage.removeItem('token')
  localStorage.removeItem('refresh_token')
  localStorage.removeItem('refresh_expire_at')
}

export default instance
