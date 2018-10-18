<template>
  <v-container fluid>
    <v-layout row>
      <confirm ref="confirm"></confirm>
      <v-flex xs3>
        <profile_image_uploader
          @uploaded_profile_photo="uploadedProfilePhoto"
          @photo_upload_started="photoUploadStarted"
          @upload_failed_profile_photo="photoUploadFailed"
          @upload_profile_photo_cancelled="photoUploadCancelled"
        ></profile_image_uploader>
      </v-flex>
      <v-flex xs9>
        <v-form v-model="valid">
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
                v-model="user.name"
                label="Name"
                :rules="nameRules"
                required
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="user.email"
                label="Email"
                type="email"
                :rules="emailRules"
                required
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="oldPassword"
                type="password"
                label="Old Password"
                :rules="passwordRules"
                required
              ></v-text-field>
            </v-flex>
            <v-flex xs6>
              <v-text-field
                v-model="password"
                type="password"
                label="Password"
                :rules="newPasswordRules"
                required
              ></v-text-field>
            </v-flex>
            <v-flex xs6>
              <v-text-field
                v-model="confirmPassword"
                type="password"
                label="Confirm Password"
                :rules="[comparePasswords]"
                required
              ></v-text-field>
            </v-flex>
            <v-flex xs2>
              <v-btn @click="save" color="primary">Save</v-btn>
            </v-flex>
            <v-flex xs3>
              <v-btn @click="deleteUser" color="error">Delete Account</v-btn>
            </v-flex>
          </v-layout>
        </v-form>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import Vue from 'vue'
  import ProfileImageUploader from '../parts/ProfileImageUploader'
  import { updateProfile, deleteOwnUser } from '../../api/users'
  import User from '../../models/User'
  import * as MutationTypes from '../../store/mutation-types'
  import Confirm from '../parts/Confirm'

  export default Vue.component('user_profile', {
    data () {
      let self = this
      return {
        isLoading: false,
        valid: false,
        alert: false,
        success: false,
        user: {
          name: '',
          email: ''
        },
        nameRules: [
          v => !!v || 'Name is required',
          v => v.length <= 100 || 'Name must be less than 100 characters'
        ],
        emailRules: [
          v => !!v || 'E-mail is required',
          v => /.+@.+/.test(v) || 'E-mail must be valid'
        ],
        passwordRules: [
          v => (!v) || v.length >= 8 || 'Password must be more than 8 characters',
          v => (!v) || (v.match(/^(?=.*[a-zA-Z])(?=.*[0-9])/) !== null) || 'Password must contain alphabets and numbers'
        ],
        newPasswordRules: [
          v => (!v) || (v !== self.oldPassword) || 'New password must be different than old password',
          v => (!v) || v.length >= 8 || 'Password must be more than 8 characters',
          v => (!v) || (v.match(/^(?=.*[a-zA-Z])(?=.*[0-9])/) !== null) || 'Password must contain alphabets and numbers'
        ],
        oldPassword: '',
        password: '',
        confirmPassword: '',
        profilePhotoId: null
      }
    },
    created: function () {
      if (this.isAuthenticated) {
        this.user = new User(JSON.parse(JSON.stringify(this.currentUser)))
        if (this.user.profile_photo !== null) {
          this.profilePhotoId = this.user.profile_photo.id
        }
      }
    },
    methods: {
      save: function () {
        if (!this.valid) {
          return false
        }
        let self = this
        self.$store.commit('setLoading', true)
        self.valid = false
        let payload = {
          name: self.user.name,
          email: self.user.email,
          profile_photo_id: self.profilePhotoId
        }
        if (self.password !== '') {
          payload.old_password = self.oldPassword
          payload.password = self.password
          payload.password_confirmation = self.confirmPassword
        }
        updateProfile(payload).then(function (response) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.$store.commit(MutationTypes.LOGIN, new User(response.data.data))
          self.alert = false
          self.$store.commit('setMessage', 'Profile updated successfully.')
        }).catch(function (error) {
          self.$store.commit('setLoading', false)
          self.valid = true
          self.success = false
          self.$store.commit('setError', error.response.data.message)
        })
      },
      uploadedProfilePhoto: function (profilePhotoId) {
        this.$store.commit('setLoading', false)
        this.valid = true
        this.profilePhotoId = profilePhotoId
      },
      photoUploadStarted: function () {
        this.$store.commit('setLoading', true)
        this.valid = false
      },
      photoUploadCancelled: function () {
        this.$store.commit('setLoading', false)
        this.valid = true
      },
      photoUploadFailed: function (error) {
        self.success = false
        this.$store.commit('setLoading', false)
        this.valid = true
        this.$store.commit('setError', error.response.data.message)
        this.profilePhotoId = null
      },
      deleteUser: function () {
        let self = this
        let confirmMessage = 'Are you sure that you want to delete your account?'
        confirmMessage += '<br/>All of your resources will be deleted from the system.'
        confirmMessage += '<br/> After confirming you will be logged out from the system.'
        self.$refs.confirm.open('Delete', confirmMessage, { color: 'red' }).then((confirm) => {
          if (confirm === true) {
            self.$store.commit('setLoading', true)
            self.valid = false
            deleteOwnUser().then(function () {
              self.$store.commit('setLoading', false)
              self.valid = true
              self.$store.commit(MutationTypes.LOGOUT)
              localStorage.removeItem('token')
              localStorage.removeItem('refresh_token')
              localStorage.removeItem('refresh_expire_at')
              self.$router.push('/')
            }).catch(function (error) {
              self.$store.commit('setLoading', false)
              self.valid = true
              self.success = false
              self.$store.commit('setError', error.response.data.message)
            })
          }
        })
      }
    },
    computed: {
      comparePasswords: function () {
        return this.password === this.confirmPassword ? true : 'Passwords don\'t match'
      },
      isAuthenticated () {
        return this.$store.getters.isAuthenticated
      },
      currentUser () {
        return this.$store.getters.currentUser
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
      ProfileImageUploader,
      Confirm
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
