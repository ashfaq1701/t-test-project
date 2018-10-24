import axios from '../backend/axios'

export function createTransfer (data) {
  return axios.post('transfers', data)
}

export function editTransfer (id, data) {
  return axios.put('transfers/' + id, data)
}
