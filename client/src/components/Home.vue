<template>
  <v-container fluid>
    <v-container
      v-if="currentUser !== null &&
      typeof currentUser !== 'undefined' &&
      currentUser.hasPermission('maintain_own_team')">
      <view_team :team="team" v-if="hasTeam"></view_team>
      <no_team v-else></no_team>
    </v-container>
  </v-container>
</template>

<script>
  import ViewTeam from './teams/ViewTeam'
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
      ViewTeam,
      NoTeam
    }
  }
</script>
