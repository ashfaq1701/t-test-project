<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="close">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">
          <span v-if="passedPlayer === null">Add </span>
          <span v-else>Edit </span>
          Player
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-container fluid>
          <v-form @submit.prevent="save" v-model="valid" ref="form">
            <div class="text-xs-center is-loading" v-if="isLoading">
              <v-progress-circular
                indeterminate
                color="primary"
                size="80"
              ></v-progress-circular>
            </div>
            <v-flex>
              <v-alert type="error" dismissible v-model="alert">
                {{ error }}
              </v-alert>
            </v-flex>
            <v-layout row wrap>
              <v-flex xs12>
                <v-text-field
                  v-model="player.first_name"
                  label="First Name"
                  :rules="requiredRules"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field
                  v-model="player.last_name"
                  label="Last Name"
                  :rules="requiredRules"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field
                  v-model="player.age"
                  label="Age"
                  type="number"
                  :rules="requiredRules"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-text-field
                  v-model="player.price"
                  label="Price"
                  type="number"
                  :rules="requiredRules"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-autocomplete
                  v-model="player.country"
                  :items="countries"
                  :loading="countryIsLoading"
                  :rules="notNullRules"
                  no-filter
                  :search-input.sync="searchCountries"
                  item-text="name"
                  item-value="id"
                  chips
                  label="Country"
                  return-object></v-autocomplete>
              </v-flex>
              <v-flex xs12>
                <v-autocomplete
                  v-model="player.player_role"
                  :items="playerRoles"
                  :rules="notNullRules"
                  item-text="name"
                  item-value="id"
                  chips
                  label="Player Role"
                  return-object></v-autocomplete>
              </v-flex>
              <v-flex xs12 v-if="passedPlayer === null && passedTeam === null">
                <v-autocomplete
                  v-model="team"
                  :items="teams"
                  :loading="teamIsLoading"
                  :rules="notNullRules"
                  no-filter
                  :search-input.sync="searchTeams"
                  item-text="name"
                  item-value="id"
                  chips
                  label="Team"
                  return-object></v-autocomplete>
              </v-flex>
            </v-layout>
          </v-form>
        </v-container>
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
  import {searchCountries} from '../../api/countries'
  import {getPlayerRoles} from '../../api/playerRoles'
  import {searchTeamsByName} from '../../api/teams'
  import {createPlayer, editPlayer} from '../../api/players'

  export default Vue.component('player_form', {
    data () {
      return {
        valid: false,
        dialog: false,
        passedPlayer: null,
        passedTeam: null,
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
        teams: [],
        countries: [],
        playerRoles: [],
        countryIsLoading: false,
        teamIsLoading: false,
        searchCountries: false,
        searchTeams: false,
        error: '',
        alert: false,
        isLoading: false,
        options: {
          color: 'primary',
          width: 600
        },
        requiredRules: [
          v => !!v || 'This field is required'
        ],
        notNullRules: [
          v => (v !== null && typeof v !== 'undefined') || 'This field is required'
        ]
      }
    },
    methods: {
      open: function (player, team) {
        if (player === null || typeof player === 'undefined') {
          this.$refs.form.reset()
          this.player = this.emptyPlayer
        } else {
          this.player = player
          this.countries.push(this.player.country)
        }
        this.passedPlayer = player
        this.passedTeam = team
        this.team = team
        this.error = ''
        this.alert = false
        this.dialog = true
      },
      save: function () {
        if (!this.valid) {
          return false
        }
        if (this.passedPlayer === null) {
          if (this.team === null || typeof this.team === 'undefined') {
            this.error = 'You must select a team to store this player.'
            this.alert = true
            return false
          }
        }
        let self = this
        self.isLoading = true
        let data = {
          first_name: self.player.first_name,
          last_name: self.player.last_name,
          age: self.player.age,
          price: self.player.price,
          player_role_id: self.player.player_role.id,
          country_id: self.player.country.id
        }
        if (self.passedPlayer === null) {
          data.team_id = self.team.id
        }
        let promise = null
        if (self.passedPlayer === null) {
          promise = createPlayer(data)
        } else {
          promise = editPlayer(self.passedPlayer.id, data)
        }
        promise.then(function (response) {
          self.isLoading = false
          self.$emit('playerUpdated')
          self.$refs.form.reset()
          self.dialog = false
        }).catch(function (error) {
          self.isLoading = false
          self.error = error.response.data.message
          self.alert = true
        })
      },
      close: function () {
        this.dialog = false
      }
    },
    created: function () {
      let self = this
      getPlayerRoles().then(function (response) {
        self.playerRoles = response.data.data
      })
    },
    computed: {
      emptyPlayer: function () {
        return {
          id: '',
          first_name: '',
          last_name: '',
          age: '',
          price: '',
          country: null,
          player_role: null,
          team_id: null,
          team: null
        }
      }
    },
    watch: {
      searchCountries (value) {
        if (value === null || value === '') {
          return
        }
        this.countryIsLoading = true
        let self = this
        searchCountries(value).then(function (response) {
          let data = response.data.data
          if (self.player.country !== null && typeof self.player.country !== 'undefined') {
            data = data.concat([self.player.country])
          }
          self.countries = data
        }).catch(function (err) {
          console.log(err)
        }).finally(function () {
          self.countryIsLoading = false
        })
      },
      searchTeams (value) {
        if (value === null || value === '') {
          return
        }
        this.teamIsLoading = true
        let self = this
        searchTeamsByName(value).then(function (response) {
          let data = response.data.data
          if (self.team !== null && typeof self.team !== 'undefined') {
            data = data.concat([self.team])
          }
          self.teams = data
        }).catch(function (err) {
          console.log(err)
        }).finally(function () {
          self.teamIsLoading = false
        })
      }
    }
  })
</script>

<style>
  div.is-loading {
    position:fixed;
    top: 35%;
    z-index: 1000;
    left: 50%;
  }
</style>
