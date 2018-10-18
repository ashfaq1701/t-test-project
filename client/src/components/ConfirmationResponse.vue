<template>
  <v-container fluid>
    <v-layout row wrap class="text-xs-center">
      <v-flex xs12>
        <v-alert type="error" v-model="alert">
          {{ error }}
        </v-alert>
        <v-alert type="success" v-model="success">
          {{ message }}
        </v-alert>
      </v-flex>
      <v-flex xs12>
        <p v-if="success">
          <span>Please click </span>
          <a @click="$router.push('/signin')">here</a>
          <span> to go to login page</span>
        </p>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import {confirmAccount} from '../api/auth'

  export default {
    data () {
      return {
        alert: false,
        success: false,
        email: '',
        token: ''
      }
    },
    created: function () {
      this.email = decodeURI(this.$route.query.email)
      this.token = this.$route.query.confirmation_token
      let self = this
      confirmAccount(this.email, this.token).then(function (response) {
        self.alert = false
        self.$store.commit('setMessage', response.data.message)
      }).catch(function (error) {
        self.success = false
        self.$store.commit('setError', error.response.data.message)
      })
    },
    computed: {
      error () {
        return this.$store.state.error
      },
      message () {
        return this.$store.state.message
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
      }
    }
  }
</script>