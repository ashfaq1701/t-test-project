<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">
          <span v-if="passedPlayer === null">Add </span>
          <span v-else>Edit </span>
          Player
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
      </v-card-text>
      <v-card-actions class="pt-0">
        <v-spacer></v-spacer>
        <v-btn color="primary darken-1" @click.native="save">Save</v-btn>
        <v-btn color="primary darken-1" flat="flat" @click.native="close">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import Vue from 'vue'

  export default Vue.component('player_form', {
    data () {
      return {
        dialog: false,
        passedPlayer: null,
        player: {
          id: '',
          first_name: '',
          last_name: '',
          age: '',
          price: '',
          country: null,
          player_role: null,
          team_id: null
        },
        team: null,
        options: {
          color: 'primary',
          width: 600
        }
      }
    },
    methods: {
      open: function (player, team) {
        this.player = player
        this.passedPlayer = player
        this.team = team
        this.dialog = true
      },
      save: function () {

      },
      close: function () {
        this.dialog = false
      }
    }
  })
</script>
