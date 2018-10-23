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

  export default Vue.component('team_form', {
    data () {
      return {
        dialog: false,
        passedTeam: null,
        team: {
          id: '',
          name: '',
          fund: '',
          country: null
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
