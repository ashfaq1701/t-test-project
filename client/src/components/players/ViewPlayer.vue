<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">View Player Information</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-container fluid>
          <v-layout row wrap>
            <v-flex xs12>
              <strong>First Name: </strong>
              <span v-if="player !== null">{{ player.first_name }}</span>
            </v-flex>
            <v-flex xs12>
              <strong>Last Name: </strong>
              <span v-if="player !== null">{{ player.last_name }}</span>
            </v-flex>
            <v-flex xs12>
              <strong>Player Role: </strong>
              <span v-if="player !== null && player.player_role !== null">{{ player.player_role.name }}</span>
            </v-flex>
            <v-flex xs12>
              <strong>Country: </strong>
              <span v-if="player !== null && player.country !== null">{{ player.country.name }}</span>
            </v-flex>
            <v-flex xs12>
              <strong>Price: </strong>
              <span v-if="player !== null && player.price !== null">${{ formatMoney(player.price) }}</span>
            </v-flex>
            <v-flex xs12>
              <strong>Team: </strong>
              <a v-if="team !== null" v-on:click="$router.push('/teams/' + team.id + '/view')">{{ team.name }}</a>
            </v-flex>
          </v-layout>
        </v-container>
      </v-card-text>
      <v-card-actions class="pt-0">
        <v-spacer></v-spacer>
        <v-btn color="primary darken-1" flat="flat" @click.native="close">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>

  import Vue from 'vue'
  import {globals} from '../mixins/globals'
  import {getTeam} from '../../api/teams'

  export default Vue.component('view_player', {
    data () {
      return {
        dialog: false,
        options: {
          color: 'primary',
          width: 400
        },
        player: null,
        team: null
      }
    },
    methods: {
      open: function (player) {
        let self = this
        this.player = player
        if (player.team_id !== null) {
          getTeam(player.team_id).then(function (response) {
            self.team = response.data.data
          })
        } else {
          self.team = null
        }
        this.dialog = true
      },
      close () {
        this.dialog = false
      }
    },
    mixins: [
      globals
    ]
  })
</script>
