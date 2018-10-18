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
        v-model="role.name"
        label="Name"
        :rules="nameRules"
        required
    ></v-text-field>
    <v-autocomplete
        v-model="role.permissions"
        :items="permissions"
        item-text="name"
        item-value="id"
        chips
        label="Permissions"
        return-object
        multiple></v-autocomplete>
    <v-btn @click="submit">submit</v-btn>
  </v-form>
</template>

<script>
  import Vue from 'vue'
  import {getRole, createRole, editRole} from '../../api/roles'
  import {getPermissions} from '../../api/permissions'
  import Role from '../../models/Role'

  export default Vue.component('role_form', {
    props: ['id'],
    data () {
      return {
        isLoading: false,
        valid: false,
        role: {
          name: '',
          permissions: []
        },
        alert: false,
        permissions: [],
        nameRules: [
          v => !!v || 'Name is required',
          v => v.length <= 50 || 'Name must be less than 50 characters'
        ]
      }
    },
    created: function () {
      let self = this
      if (typeof this.id !== 'undefined') {
        getRole(this.id).then(function (response) {
          self.role = new Role(response.data.data)
        })
      }
      getPermissions().then(function (response) {
        self.permissions = response.data.data
      })
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
        let permissionIds = []
        self.role.permissions.forEach(function (permission) {
          if (typeof permission === 'object') {
            permissionIds.push(permission.id)
          } else {
            permissionIds.push(permission)
          }
        })
        let postData = {
          name: self.role.name,
          permissions: permissionIds
        }
        if (typeof this.id === 'undefined') {
          promise = createRole(postData)
        } else {
          promise = editRole(self.id, postData)
        }
        promise.then(function () {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.$router.push('/roles')
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
