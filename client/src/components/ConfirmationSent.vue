<template>
  <v-container fluid>
    <v-layout row wrap class="text-xs-center">
      <v-flex xs12>
        <v-alert type="error" dismissible v-model="alert">
          {{ error }}
        </v-alert>
        <v-alert type="success" dismissible v-model="success">
          {{ message }}
        </v-alert>
      </v-flex>
      <v-flex xs12>
        <p>Confirmation email sent to {{ email }}</p>
      </v-flex>
      <v-flex xs12>
        <v-btn color="primary" @click="resend">Resend Confirmation Email</v-btn>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import { resendConfirmationEmail } from '../api/auth'

  export default {
    props: ['email'],
    data () {
      return {
        alert: false,
        success: false
      }
    },
    methods: {
      resend: function () {
        let self = this
        resendConfirmationEmail(this.email).then(function (response) {
          self.alert = false
          self.$store.commit('setMessage', response.data.message)
        }).catch(function (error) {
          self.success = false
          self.$store.commit('setError', error.response.data.message)
        })
      }
    },
    created: function () {
      this.email = decodeURI(this.email)
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
    },
    components: {
    }
  }
</script>