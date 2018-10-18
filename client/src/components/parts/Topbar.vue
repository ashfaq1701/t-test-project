<template>
  <v-toolbar app>
    <v-toolbar-side-icon @click="invertSidebar" v-if="isAdmin"></v-toolbar-side-icon>
    <v-toolbar-title>
      <router-link to="/" tag="span" style="cursor: pointer">
        {{ appTitle }}
      </router-link>
    </v-toolbar-title>
    <v-spacer></v-spacer>
    <v-toolbar-items class="hidden-xs-only">
      <v-btn
        flat
        v-for="item in menuItems"
        v-if="viewItem(item)"
        :key="item.title"
        :to="item.path">
        <v-icon left dark>{{ item.icon }}</v-icon>
        {{ item.title }}
      </v-btn>
      <v-menu offset-y v-if="isAuthenticated" z-index="50" v-model="userMenu">
        <v-btn
          slot="activator"
          flat
        >
          <img :src="profilePicture" class="profile-img" alt="Avatar">
          {{ user.name }}
          <v-icon v-if="userMenu">arrow_drop_down</v-icon>
          <v-icon v-if="!userMenu">arrow_right</v-icon>
        </v-btn>
        <v-list>
          <v-list-tile @click="userSignOut">
            <v-list-tile-title>
              <v-icon left>exit_to_app</v-icon>
              Sign Out
            </v-list-tile-title>
          </v-list-tile>
          <v-list-tile @click="clicked('/profile')" v-if="hasPermission('manage_profile')">
            <v-list-tile-title>
              <v-icon left>person</v-icon>
              Profile
            </v-list-tile-title>
          </v-list-tile>
        </v-list>
      </v-menu>
    </v-toolbar-items>
  </v-toolbar>
</template>

<script>
  import Vue from 'vue'
  import {logout} from '../../api/auth'

  export default Vue.component('topbar', {
    data () {
      return {
        userMenu: false
      }
    },
    computed: {
      isAuthenticated () {
        return this.$store.getters.isAuthenticated
      },
      menuItems () {
        if (this.isAuthenticated) {
          return [
            { title: 'Home', path: '/home', icon: 'home' }
          ]
        } else {
          return [
            { title: 'Sign Up', path: '/signup', icon: 'face' },
            { title: 'Sign In', path: '/signin', icon: 'lock_open' }
          ]
        }
      },
      appTitle () {
        return this.$store.state.appTitle
      },
      user () {
        return this.$store.getters.currentUser
      },
      isAdmin () {
        return this.user !== null && this.user.hasRole('admin')
      },
      profilePicture () {
        let fileName = 'user.png'
        if (this.user.profile_photo !== null) {
          fileName = this.user.profile_photo.file_name
        }
        return '/images/' + fileName
      }
    },
    props: ['sidebar'],
    methods: {
      userSignOut () {
        let self = this
        logout().then(function () {
          self.$store.dispatch('logout').then(function () {
            localStorage.removeItem('token')
            localStorage.removeItem('refresh_token')
            localStorage.removeItem('refresh_expire_at')
            self.$router.push('/')
          })
        })
      },
      hasPermission: function (permission) {
        return (this.isAuthenticated && this.user.hasPermission(permission))
      },
      invertSidebar () {
        this.sidebar = !this.sidebar
        this.$emit('sidebar', this.sidebar)
      },
      viewItem (item) {
        if (item.role === null || typeof item.role === 'undefined') {
          return true
        }
        return this.user.hasRole(item.role)
      },
      clicked: function (route) {
        this.$router.push(route)
      }
    }
  })
</script>

<style>
  .profile-img {
    height: 22px;
    width: 22px;
    border-radius: 50%;
    margin-right: 10px;
  }
</style>
