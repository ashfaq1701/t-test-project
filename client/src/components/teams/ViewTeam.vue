<template>
  <v-container fluid>
    <view_team_detail :team="team" v-if="team !== null"></view_team_detail>
  </v-container>
</template>

<script>
  import Vue from 'vue'
  import ViewTeamDetail from './ViewTeamDetail'
  import {getTeam} from '../../api/teams'
  import Team from '../../models/Team'
  export default Vue.component('view_team', {
    props: ['id'],
    data () {
      return {
        team: null
      }
    },
    created: function () {
      let self = this
      getTeam(this.id).then(function (response) {
        self.team = new Team(response.data.data)
      })
    },
    components: {
      ViewTeamDetail
    }
  })
</script>
