<template>
  <v-app>
    <sidebar
      :sidebar="sidebar"
    ></sidebar>
    <topbar
      :sidebar="sidebar"
      @sidebar="sidebar = $event"
    ></topbar>

    <v-content>
      <router-view></router-view>
    </v-content>

  </v-app>
</template>

<script>
  import Sidebar from './components/parts/Sidebar'
  import Topbar from './components/parts/Topbar'

  export default {
    data () {
      return {
        sidebar: true
      }
    },
    methods: {},
    components: {
      Sidebar,
      Topbar
    }
  }
</script>
