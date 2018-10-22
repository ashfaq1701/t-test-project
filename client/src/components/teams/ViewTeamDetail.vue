<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Team Name: {{ team.name }}</h2>
    </v-layout>
    <v-layout row justify-center>
      <h3 v-if="team.country !== null">{{ team.country.name }}</h3>
    </v-layout>
    <v-layout row justify-center>
      <h3>Team Value: ${{ formatMoney(team.team_value) }}</h3>
      <h3 v-if="isOwnTeam">, Team Fund: ${{ formatMoney(team.fund) }}</h3>
    </v-layout>
    <v-layout row justify-center v-if="hasUserAccess && user !== null">
      <h3>Owner: <a v-on:click="$router.push('/users/' + user.id + '/edit')">{{ user.name }}</a></h3>
    </v-layout>
  </v-container>
</template>

<script>

  import Vue from 'vue'
  import ListTeamPlayers from '../players/ListTeamPlayers'
  import {globals} from '../mixins/globals'
  import {getUser} from '../../api/users'
  import User from '../../models/User'

  export default Vue.component('view_team_detail', {
    props: ['team', 'id'],
    data () {
      return {
        user: null
      }
    },
    computed: {
      error () {
        return this.$store.state.error
      },
      currentUser () {
        return this.$store.getters.currentUser
      },
      isOwnTeam () {
        if (this.currentUser !== null && typeof this.currentUser !== 'undefined' &&
          this.currentUser.hasPermission('maintain_own_team') && this.currentUser.team !== null &&
          this.currentUser.team.id === this.team.id) {
          return true
        }
        return false
      },
      hasUserAccess () {
        if (this.currentUser !== null && typeof this.currentUser !== 'undefined' &&
          this.currentUser.hasPermission('manage_users')) {
          return true
        }
        return false
      }
    },
    created: function () {
      let self = this
      if (this.team.user_id !== null && this.hasUserAccess) {
        getUser(this.team.user_id).then(function (response) {
          self.user = new User(response.data.data)
        })
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
