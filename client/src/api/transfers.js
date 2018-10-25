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

export function getUnnotifiedTransfers () {
  return axios.get('transfers', {params: {type: 'completed', 'not_notified': 1}})
}

export function markNotifyTransfers () {
  return axios.put('transfers/-1', {mark_notified: 1})
}
