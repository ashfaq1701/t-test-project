import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import menuItems from './modules/menu-items'

Vue.use(Vuex)

export const store = new Vuex.Store({
  modules: {
    auth,
    menuItems
  },
  state: {
    appTitle: 'Football Manager',
    error: null,
    loading: false,
    message: null
  },
  mutations: {
    setError (state, payload) {
      state.error = payload
    },
    setLoading (state, payload) {
      state.loading = payload
    },
    setMessage (state, payload) {
      state.message = payload
    }
  },
  actions: {
    setLoading ({commit}, payload) {
      commit('setLoading', payload)
    }
  },
  getters: {
    getLoading (state) {
      return state.loading !== false
    }
  }
})
