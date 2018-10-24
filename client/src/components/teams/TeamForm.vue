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
                  v-model="team.name"
                  label="Name"
                  :rules="requiredRules"
                ></v-text-field>
              </v-flex>
              <v-flex xs12>
                <v-autocomplete
                  v-model="team.country"
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
              <v-flex xs12 v-if="isAdmin">
                <v-text-field
                  v-model="team.fund"
                  label="Fund"
                  :rules="requiredRules"
                ></v-text-field>
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
  import {editTeam} from '../../api/teams'

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
        },
        valid: false,
        error: '',
        alert: false,
        isLoading: false,
        countries: [],
        countryIsLoading: false,
        searchCountries: false,
        requiredRules: [
          v => !!v || 'This field is required'
        ],
        notNullRules: [
          v => (v !== null && typeof v !== 'undefined') || 'This field is required'
        ]
      }
    },
    methods: {
      open: function (team) {
        this.team = team
        this.passedTeam = team
        this.dialog = true
        if (this.team.country !== null && typeof this.team.country !== 'undefined') {
          this.countries.push(this.team.country)
        }
      },
      save: function () {
        if (!this.valid) {
          return false
        }
        let self = this
        self.isLoading = true
        let data = {
          name: self.team.name,
          country_id: self.team.country.id
        }
        if (self.isAdmin) {
          data.fund = self.team.fund
        }
        editTeam(self.team.id, data).then(function (response) {
          self.isLoading = false
          self.$emit('teamUpdated')
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
    computed: {
      currentUser () {
        return this.$store.getters.currentUser
      },
      isAdmin () {
        return this.currentUser !== null && typeof this.currentUser !== 'undefined' && this.currentUser.hasRole('admin')
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
          if (self.team.country !== null && typeof self.team.country !== 'undefined') {
            data = data.concat([self.team.country])
          }
          self.countries = data
        }).catch(function (err) {
          console.log(err)
        }).finally(function () {
          self.countryIsLoading = false
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
