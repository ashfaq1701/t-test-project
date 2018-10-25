<template>
  <v-toolbar app>
    <v-toolbar-side-icon @click="invertSidebar" v-if="hasSidebar"></v-toolbar-side-icon>
    <v-toolbar-title>
      <router-link to="/" tag="span" style="cursor: pointer">
        {{ appTitle }}
      </router-link>
    </v-toolbar-title>
    <v-spacer></v-spacer>
    <v-toolbar-items class="hidden-xs-only">
      <v-btn flat v-if="hasOwnTeam">${{ formatMoney(user.team.fund) }}</v-btn>
      <v-menu offset-y v-if="hasOwnTeam" z-index="50" v-model="notificationMenu">
        <v-btn
          slot="activator"
          flat
          @click="markNotified"
        >
          <v-icon v-if="isRead === false && unreadTransfers.length > 0">notifications_active</v-icon>
          <v-icon v-else-if="unreadTransfers.length > 0">notifications</v-icon>
          <v-icon v-else>notifications_none</v-icon>
        </v-btn>
        <v-list>
          <v-list-tile v-for="transfer in unreadTransfers">
            <v-list-tile-content>
              <v-list-tile-sub-title>
                <span>Your player </span>
                <span>{{ [transfer.player.first_name, transfer.player.last_name].join(' ') }} </span>
                <span>was transferred to </span>
                <span>{{ transfer.transferred_to.name }} </span>
                <span>on {{ formatDate(transfer.transfer_completed_at) }}.</span>
              </v-list-tile-sub-title>
            </v-list-tile-content>
          </v-list-tile>
        </v-list>
      </v-menu>
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
  import {globals} from '../mixins/globals'
  import {getUnnotifiedTransfers, markNotifyTransfers} from '../../api/transfers'
  import Transfer from '../../models/Transfer'

  export default Vue.component('topbar', {
    data () {
      return {
        userMenu: false,
        notificationMenu: false,
        unreadTransfers: [],
        isRead: false
      }
    },
    computed: {
      isAuthenticated () {
        return this.$store.getters.isAuthenticated
      },
      menuItems () {
        if (this.isAuthenticated) {
          return [
            { title: 'Home', path: '/home', icon: 'home' },
            { title: 'Transfer List', path: '/transfers', icon: 'swap_horiz' }
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
      hasOwnTeam () {
        if (this.user !== null && typeof this.user !== 'undefined' &&
          this.user.hasPermission('maintain_own_team') && this.user.team !== null) {
          return true
        }
        return false
      },
      hasSidebar () {
        return this.user !== null && (this.user.hasRole('admin') || this.user.hasRole('league_manager'))
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
    },
    watch: {
      user (val) {
        if (val !== null && typeof val !== 'undefined') {
          if (val.hasPermission('maintain_own_team')) {
            let self = this
            getUnnotifiedTransfers().then(function (response) {
              let transfers = []
              let data = response.data.data
              for (let i = 0; i < data.length; i++) {
                transfers.push(new Transfer(data[i]))
              }
              self.unreadTransfers = transfers
              if (self.unreadTransfers.length > 0) {
                self.isRead = false
              }
            })
          }
        }
      },
      notificationMenu (val) {
        if (val === true) {
          let self = this
          if (self.isRead === false && self.unreadTransfers.length > 0) {
            markNotifyTransfers().then(function () {
              self.isRead = true
            })
          }
        }
      }
    },
    mixins: [
      globals
    ]
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
