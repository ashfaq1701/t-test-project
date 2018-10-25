<template>
  <v-container fluid>
    <v-layout row wrap>
      <v-flex xs12 class="text-xs-center" mt-5>
        <h1>Sign Up</h1>
      </v-flex>
      <v-flex xs12 sm6 offset-sm3 mt-3>
        <v-form @submit.prevent="userSignUp" v-model="valid">
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
                name="name"
                label="Name"
                id="name"
                type="text"
                v-model="name"
                :rules="nameRules"
                required></v-text-field>
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
            <v-flex>
              <v-text-field
                name="confirmPassword"
                label="Confirm Password"
                id="confirmPassword"
                type="password"
                required
                v-model="passwordConfirm"
                :rules="[comparePasswords]"
                ></v-text-field>
            </v-flex>
            <v-flex class="text-xs-center" mt-5>
              <v-btn color="primary" type="submit" :disabled="loading">Sign Up</v-btn>
            </v-flex>
          </v-layout>
        </v-form>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>

import {register} from '../api/auth'

export default {
  data () {
    return {
      isLoading: false,
      valid: false,
      name: '',
      email: '',
      password: '',
      passwordConfirm: '',
      alert: false,
      nameRules: [
        v => !!v || 'Name is required',
        v => v.length <= 100 || 'Name must be less than 100 characters'
      ],
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
  computed: {
    comparePasswords () {
      return this.password === this.passwordConfirm ? true : 'Passwords don\'t match'
    },
    error () {
      return this.$store.state.error
    },
    loading () {
      return this.$store.state.loading
    }
  },
  methods: {
    userSignUp () {
      if (!this.valid) {
        return false
      }
      let self = this
      self.$store.commit('setLoading', true)
      self.valid = false
      register(this.name, this.email, this.password).then(function () {
        self.$store.commit('setLoading', false)
        self.valid = true
        self.$router.push('/confirmation-sent/' + encodeURI(self.email))
      }).catch(function (error) {
        self.$store.commit('setLoading', false)
        self.valid = true
        if (error.response.data.errors !== null && typeof error.response.data.errors !== 'undefined') {
          if (error.response.data.errors.email !== null && typeof error.response.data.errors.email !== 'undefined' &&
            error.response.data.errors.email.length > 0) {
            self.$store.commit('setError', error.response.data.errors.email[0])
          } else {
            self.$store.commit('setError', error.response.data.message)
          }
        } else {
          self.$store.commit('setError', error.response.data.message)
        }
      })
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
