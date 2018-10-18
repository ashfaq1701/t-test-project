<template>
  <v-container fluid>
    <v-layout row wrap>
      <v-flex xs12 class="text-xs-center" mt-5>
        <h1>Sign In</h1>
      </v-flex>
      <v-flex xs12 sm6 offset-sm3 mt-3>
        <v-form @submit.prevent="userSignIn" v-model="valid">
          <div class="text-xs-center is-loading" v-if="isLoading">
            <v-progress-circular
              indeterminate
              color="primary"
              size="80"
            ></v-progress-circular>
          </div>
          <v-layout column>
            <v-flex>
              <v-alert type="error" dismissible v-model="alert">
                {{ error }}
              </v-alert>
            </v-flex>
            <v-flex>
              <v-text-field
                name="email"
                label="Email"
                id="email"
                type="email"
                v-model="email"
                :rules="emailRules"
                required></v-text-field>
            </v-flex>
            <v-flex>
              <v-text-field
                name="password"
                label="Password"
                id="password"
                type="password"
                v-model="password"
                :rules="passwordRules"
                required></v-text-field>
            </v-flex>
            <v-flex class="text-xs-center" mt-5>
              <v-btn color="primary" type="submit" :disabled="loading">Sign In</v-btn>
            </v-flex>
          </v-layout>
        </v-form>
        <v-flex class="text-xs-center">
          <a @click="$router.push('/forgot-password')">Forgot your password?</a>
        </v-flex>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>

import {login} from '../api/auth'

export default {
  data () {
    return {
      isLoading: false,
      valid: false,
      email: '',
      password: '',
      alert: false,
      emailRules: [
        v => !!v || 'E-mail is required',
        v => /.+@.+/.test(v) || 'E-mail must be valid'
      ],
      passwordRules: [
        v => !!v || 'Password is required',
        v => (!v) || v.length >= 8 || 'Password must be more than 8 characters',
        v => (!v) || (v.match(/^(?=.*[a-zA-Z])(?=.*[0-9])/) !== null) || 'Password must contain alphabets and numbers'
      ]
    }
  },
  methods: {
    userSignIn () {
      let self = this
      if (!this.valid) {
        return false
      }
      self.$store.commit('setLoading', true)
      self.valid = false
      login(this.email, this.password).then(function (response) {
        localStorage.token = response.data.token
        localStorage.refresh_token = response.data.refresh_token
        localStorage.refresh_expire_at = response.data.refresh_expire_at
        self.$store.dispatch('login').then(function () {
          self.$store.commit('setLoading', false)
          self.valid = true
          let dest = self.$router.currentRoute.query.path
          if (typeof dest !== 'undefined' && dest !== null) {
            self.$router.push(decodeURI(dest))
          } else {
            self.$router.push('/home')
          }
        })
      }).catch(function (error) {
        self.$store.commit('setLoading', false)
        self.valid = true
        self.$store.commit('setError', error.response.data.message)
      })
    }
  },
  computed: {
    error () {
      return this.$store.state.error
    },
    loading () {
      return this.$store.state.loading
    }
  },
  watch: {
    error (value) {
      if (value) {
        this.alert = true
      }
    },
    alert (value) {
      if (!value) {
        this.$store.commit('setError', null)
      }
    },
    loading (value) {
      this.isLoading = value
    },
    isLoading (value) {
      this.$store.commit('setLoading', value)
    }
  }
}
</script>

<style>
  div.is-loading {
    position:fixed;
    top: 35%;
    z-index: 1000;
    left: 50%;
  }
</style>
