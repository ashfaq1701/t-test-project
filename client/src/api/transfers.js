import axios from '../backend/axios'

export function createTransfer (data) {
  return axios.post('transfers', data)
}
