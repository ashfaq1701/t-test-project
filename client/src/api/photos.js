import axios from '../backend/axios'

export function uploadPhoto (photo) {
  let formData = new FormData()
  formData.append('file', photo)
  return axios.post('photos', formData)
}

export function deletePhoto (photoId) {
  return axios.delete('photos/' + photoId)
}
