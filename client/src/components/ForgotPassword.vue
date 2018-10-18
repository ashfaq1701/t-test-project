<template>
  <v-container>
    <v-layout row class="text-xs-center">
      <v-flex xs12>
        <h2>Enter your email address</h2>
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
            v-model="email"
            label="Email"
            type="email"
            :rules="emailRules"
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
  import { requestPasswordReset } from '../api/auth'

  export default {
    data () {
      return {
        isLoading: false,
        valid: false,
        alert: false,
        success: false,
        email: '',
        emailRules: [
          v => !!v || 'E-mail is required',
          v => /.+@.+/.test(v) || 'E-mail must be valid'
        ]
      }
    },
    methods: {
      submit: function () {
        let self = this
        if (!this.valid) {
          return false
        }
        self.$store.commit('setLoading', true)
        self.valid = false
        requestPasswordReset(this.email).then(function (response) {
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
            self.$store.commit('setError', 'Could not send password reset email')
          }
        })
      }
    },
    computed: {
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
