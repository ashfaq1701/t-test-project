import axios from '../backend/axios'

export function createTransfer (data) {
  return axios.post('transfers', data)
}

export function editTransfer (id, data) {
  return axios.put('transfers/' + id, data)
}

export function searchTransfers (data) {
  return axios.get('transfers', {params: data})
}

export function deleteTransfer (id) {
  return axios.delete('transfers/' + id)
}
