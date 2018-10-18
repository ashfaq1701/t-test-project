import Vue from 'vue'
import App from './App'
import router from './router'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import { store } from './store'

Vue.use(Vuetify)

Vue.config.productionTip = false

/* eslint-disable no-new */
createApp()

function createApp () {
  createVueApp()
  if (localStorage.getItem('token') != null) {
    store.dispatch('login').then(function () {
      let dest = router.currentRoute.query.path
      if (typeof dest !== 'undefined' && dest !== null) {
        router.push(decodeURI(dest))
      } else {
        router.push('/home')
      }
    }).catch(function (error) {
      store.commit('setError', error.response.statusText)
      router.push(router.currentRoute.path)
    })
  }
}

function createVueApp () {
  new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App),
    created () {
    }
  })
}
