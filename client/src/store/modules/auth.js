import User from '@/models/User'
import * as MutationTypes from './../mutation-types'

const state = {
  user: null
}

const mutations = {
  [MutationTypes.LOGIN] (state, payload) {
    state.user = payload
  },
  [MutationTypes.LOGOUT] (state) {
    state.user = null
  }
}

const getters = {
  currentUser (state) {
    return state.user
  },
  isAuthenticated (state) {
    return state.user !== null && typeof state.user !== 'undefined'
  }
}

const actions = {
  login ({commit}) {
    return new Promise((resolve, reject) => {
      User.from(localStorage.token).then(function (response) {
        let user = new User(response.data.data)
        commit(MutationTypes.LOGIN, user)
        commit('setLoading', false)
        resolve()
      }).catch(function (error) {
        console.log('Error')
        reject(error)
      })
    })
  },
  logout ({commit}) {
    commit(MutationTypes.LOGOUT)
    commit('setLoading', false)
  }
}

export default {
  state,
  mutations,
  getters,
  actions
}
