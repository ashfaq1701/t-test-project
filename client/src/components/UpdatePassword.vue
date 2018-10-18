<template>
  <v-container fluid>
    <v-layout row class="text-xs-center">
      <v-flex xs12>
        <h2>Set your new password</h2>
      </v-flex>
    </v-layout>
    <v-form @submit.prevent="submit" v-model="valid">
      <div class="text-xs-center is-loading" v-if="isLoading">
        <v-progress-circular
          indeterminate
          color="primary"
          size="80"
        ></v-progress-circular>
      </div>
      <v-layout row wrap>
        <v-flex xs12>
          <v-alert type="error" dismissible v-model="alert">
            {{ error }}
          </v-alert>
          <v-alert type="success" dismissible v-model="success">
            {{ message }}
          </v-alert>
        </v-flex>
        <v-flex xs12>
          <v-text-field
            v-model="password"
            type="password"
            label="Password"
            :rules="passwordRules"
            required
          ></v-text-field>
        </v-flex>
        <v-flex xs12>
          <v-text-field
            v-model="confirmPassword"
            type="password"
            label="Confirm Password"
            :rules="[comparePasswords]"
            required
          ></v-text-field>
        </v-flex>
        <v-flex xs12>
          <v-btn @click="submit">submit</v-btn>
        </v-flex>
      </v-layout>
    </v-form>
  </v-container>
</template>

<script>
  import { resetPassword } from '../api/auth'

  export default {
    props: ['token'],
    data () {
      return {
        isLoading: false,
        props: ['token'],
        password: '',
        confirmPassword: '',
        email: '',
        valid: false,
        alert: false,
        success: false,
        passwordRules: [
          v => (!v) || v.length >= 8 || 'Password must be more than 8 characters',
          v => (!v) || (v.match(/^(?=.*[a-zA-Z])(?=.*[0-9])/) !== null) || 'Password must contain alphabets and numbers'
        ]
      }
    },
    created: function () {
      this.email = this.$route.query.email
    },
    methods: {
      submit: function () {
        if (!this.valid) {
          return false
        }
        let self = this
        if (this.password === '' || this.password === null || typeof this.password === 'undefined') {
          return false
        }
        self.$store.commit('setLoading', true)
        self.valid = false
        let postData = {
          email: self.email,
          password: self.password,
          password_confirmation: self.confirmPassword,
          token: self.token
        }
        resetPassword(postData).then(function (response) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.alert = false
          self.$store.commit('setMessage', response.data.status)
        }).catch(function (error) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.success = false
          if (error.response.data.hasOwnProperty('email')) {
            self.$store.commit('setError', error.response.data.email)
          } else {
            self.$store.commit('setError', 'Could not reset password')
          }
        })
      }
    },
    computed: {
      comparePasswords: function () {
        return this.password === this.confirmPassword ? true : 'Passwords don\'t match'
      },
      error () {
        return this.$store.state.error
      },
      message () {
        return this.$store.state.message
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
      message (value) {
        if (value) {
          this.success = true
        }
      },
      alert (value) {
        if (!value) {
          this.$store.commit('setError', null)
        }
      },
      success (value) {
        if (!value) {
          this.$store.commit('setMessage', null)
        }
      },
      loading (value) {
        this.isLoading = value
      },
      isLoading (value) {
        this.$store.commit('setLoading', value)
      }
    },
    components: {
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
