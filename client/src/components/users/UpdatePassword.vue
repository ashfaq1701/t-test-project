<template>
  <v-container fluid>
    <v-layout row class="text-xs-center">
      <v-flex xs12>
        <h2>Update Your Password</h2>
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
            v-model="oldPassword"
            type="password"
            label="Current Password"
            :rules="passwordRules"
            required
          ></v-text-field>
        </v-flex>
        <v-flex xs12>
          <v-text-field
            v-model="password"
            type="password"
            label="New Password"
            :rules="newPasswordRules"
            required
          ></v-text-field>
        </v-flex>
        <v-flex xs12>
          <v-text-field
            v-model="confirmPassword"
            type="password"
            label="Confirm New Password"
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
  import { updatePassword } from '../../api/users'
  import User from '../../models/User'
  import * as MutationTypes from '../../store/mutation-types'

  export default {
    data () {
      let self = this
      return {
        isLoading: false,
        password: '',
        oldPassword: '',
        confirmPassword: '',
        valid: false,
        alert: false,
        success: false,
        passwordRules: [
          v => (!v) || v.length >= 8 || 'Password must be more than 8 characters',
          v => (!v) || (v.match(/^(?=.*[a-zA-Z])(?=.*[0-9])/) !== null) || 'Password must contain alphabets and numbers'
        ],
        newPasswordRules: [
          v => (!v) || (v !== self.oldPassword) || 'New password must be different than old password',
          v => (!v) || v.length >= 8 || 'Password must be more than 8 characters',
          v => (!v) || (v.match(/^(?=.*[a-zA-Z])(?=.*[0-9])/) !== null) || 'Password must contain alphabets and numbers'
        ]
      }
    },
    methods: {
      submit: function () {
        if (!this.valid) {
          return false
        }
        let self = this
        self.$store.commit('setLoading', true)
        self.valid = false
        updatePassword(this.password, this.confirmPassword, this.oldPassword).then(function (response) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.$store.commit(MutationTypes.LOGIN, new User(response.data.data))
          self.alert = false
          self.$store.commit('setMessage', 'Password updated successfully.')
        }).catch(function (error) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.success = false
          self.$store.commit('setError', error.response.data.message)
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
