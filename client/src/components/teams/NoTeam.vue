<template>
  <v-flex>
    <v-alert type="error" v-model="alert">
      You don't have any assigned team. Please contact with admin to assign you a team.
    </v-alert>
  </v-flex>
</template>

<script>

  import Vue from 'vue'

  export default Vue.component('no_team', {
    data () {
      return {
        alert: true
      }
    }
  })
</script>
