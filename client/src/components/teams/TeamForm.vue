<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">
          <span v-if="passedTeam === null">Add </span>
          <span v-else>Edit </span>
          Team
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
  import {getUser} from '../../api/users'
  import User from '../../models/User'

  export default Vue.component('team_form', {
    data () {
      return {
        dialog: false,
        passedTeam: null,
        team: {
          id: '',
          name: '',
          fund: '',
          country: null,
          user: null
        },
        options: {
          color: 'primary',
          width: 600
        }
      }
    },
    methods: {
      open: function (team) {
        this.team = team
        this.passedTeam = team
        let self = this
        if (team !== null && team.user_id !== null &&
          this.currentUser !== null && typeof this.currentUser !== 'undefined' &&
          this.currentUser.hasPermission('manage_users')) {
          getUser(team.user_id).then(function (response) {
            self.team.user = new User(response.data.data)
          })
        }
        this.dialog = true
      },
      save: function () {

      },
      close: function () {
        this.dialog = false
      }
    },
    computed: {
      currentUser () {
        return this.$store.getters.currentUser
      }
    }
  })
</script>
