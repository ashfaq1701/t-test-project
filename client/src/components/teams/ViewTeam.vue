<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Team Name: {{ team.name }}</h2>
    </v-layout>
    <v-layout row justify-center>
      <h3>Team Value: ${{ formatMoney(team.team_value) }}</h3>
    </v-layout>
  </v-container>
</template>

<script>

  import Vue from 'vue'
  import ListTeamPlayers from '../players/ListTeamPlayers'
  import {globals} from '../mixins/globals'

  export default Vue.component('view_team', {
    props: ['team'],
    data () {
      return {}
    },
    computed: {
      error () {
        return this.$store.state.error
      }
    },
    components: {
      ListTeamPlayers
    },
    mixins: [
      globals
    ]
  })
</script>
