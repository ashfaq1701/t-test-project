import axios from '../backend/axios'

export function searchCountries (search) {
  return axios.get('countries', {params: {query: search}})
}
