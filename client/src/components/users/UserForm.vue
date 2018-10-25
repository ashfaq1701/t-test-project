<template>
  <v-form @submit.prevent="submit" v-model="valid">
    <div class="text-xs-center is-loading" v-if="isLoading">
      <v-progress-circular
        indeterminate
        color="primary"
        size="80"
      ></v-progress-circular>
    </div>
    <v-flex>
      <v-alert type="error" dismissible v-model="alert">
        {{ error }}
      </v-alert>
    </v-flex>
    <v-text-field
        v-model="user.name"
        label="Name"
        :rules="nameRules"
        required
    ></v-text-field>
    <v-text-field
        v-model="user.email"
        label="Email"
        type="email"
        :rules="emailRules"
        required
    ></v-text-field>
    <v-autocomplete
        v-model="user.roles"
        :items="roles"
        item-text="name"
        item-value="id"
        chips
        label="Roles"
        return-object
        multiple></v-autocomplete>
    <v-btn @click="submit">submit</v-btn>
  </v-form>
</template>

<script>
  import Vue from 'vue'
  import {getUser, createUser, editUser} from '../../api/users'
  import {getAllRoles} from '../../api/roles'
  import User from '../../models/User'

  export default Vue.component('user_form', {
    props: ['id'],
    data () {
      return {
        isLoading: false,
        valid: false,
        user: {
          name: '',
          email: '',
          roles: []
        },
        searchRoles: false,
        alert: false,
        roles: [],
        nameRules: [
          v => !!v || 'Name is required',
          v => v.length <= 100 || 'Name must be less than 100 characters'
        ],
        emailRules: [
          v => !!v || 'E-mail is required',
          v => /.+@.+/.test(v) || 'E-mail must be valid'
        ]
      }
    },
    created: function () {
      let self = this
      getAllRoles().then(function (response) {
        self.roles = response.data.data
      })
      if (typeof this.id !== 'undefined') {
        getUser(this.id).then(function (response) {
          self.user = new User(response.data.data)
        })
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
        let promise = null
        let roleIds = []
        self.user.roles.forEach(function (role) {
          if (typeof role === 'object') {
            roleIds.push(role.id)
          } else {
            roleIds.push(role)
          }
        })
        let postData = {
          name: self.user.name,
          email: self.user.email,
          roles: roleIds
        }
        if (typeof this.id === 'undefined') {
          promise = createUser(postData)
        } else {
          promise = editUser(self.id, postData)
        }
        promise.then(function (response) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.$router.push('/users')
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
  })
</script>

<style>
  div.is-loading {
    position:fixed;
    top: 35%;
    z-index: 1000;
    left: 50%;
  }
</style>
