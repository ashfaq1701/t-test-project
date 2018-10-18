<template>
  <v-navigation-drawer
    v-if="user !== null && user.hasRole('admin')"
    v-model="sidebar" app>
    <v-list>
      <v-list-tile
        v-for="menuObj in sideMenuItems"
        v-if="user !== null && user.hasPermission(menuObj.permission)"
        @click="clicked(menuObj.route)"
        :key="menuObj.label">
        <v-list-tile-title>
          <v-icon left>{{ menuObj.icon }}</v-icon>
          {{ menuObj.label }}
        </v-list-tile-title>
      </v-list-tile>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
  import Vue from 'vue'

  export default Vue.component('sidebar', {
    data () {
      return {}
    },
    props: ['sidebar'],
    methods: {
      clicked: function (route) {
        this.$router.push(route)
      }
    },
    computed: {
      user: function () {
        return this.$store.getters.currentUser
      },
      sideMenuItems: function () {
        return this.$store.getters.menuItems
      }
    }
  })
</script>
