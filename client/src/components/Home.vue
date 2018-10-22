<template>
  <v-container fluid>
    <v-container
      v-if="currentUser !== null &&
      typeof currentUser !== 'undefined' &&
      currentUser.hasPermission('maintain_own_team')">
      <view_team_detail :team="team" v-if="hasTeam"></view_team_detail>
      <no_team v-else></no_team>
    </v-container>
  </v-container>
</template>

<script>
  import ViewTeamDetail from './teams/ViewTeamDetail'
  import NoTeam from './teams/NoTeam'
  export default {
    data () {
      return {}
    },
    computed: {
      currentUser () {
        return this.$store.getters.currentUser
      },
      hasTeam () {
        if ((this.currentUser.team === null) || (typeof this.currentUser.team === 'undefined')) {
          return false
        }
        return true
      },
      team () {
        return this.currentUser.team
      }
    },
    components: {
      ViewTeamDetail,
      NoTeam
    }
  }
</script>
