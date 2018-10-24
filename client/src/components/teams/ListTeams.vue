<template>
  <v-container fluid>
    <v-layout row justify-center>
      <h2>Team List</h2>
    </v-layout>
    <div class="text-xs-center is-loading" v-if="isLoading">
      <v-progress-circular
        indeterminate
        color="primary"
        size="80"
      ></v-progress-circular>
    </div>
    <v-layout row wrap>
      <v-flex xs12>
        <v-alert type="error" dismissible v-model="alert">
          {{ error }}
        </v-alert>
      </v-flex>
      <v-flex xs12>
        <v-alert type="success" dismissible v-model="success">
          {{ message }}
        </v-alert>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12>
        <h2>Filters</h2>
      </v-flex>
      <v-flex xs12>
        <v-text-field
          v-model="name"
          label="Search By Name"
        ></v-text-field>
      </v-flex>
      <v-flex xs6>
        <v-autocomplete
          v-model="country"
          :items="countries"
          :loading="countryIsLoading"
          no-filter
          :search-input.sync="searchCountries"
          item-text="name"
          item-value="id"
          chips
          label="Country"
          return-object></v-autocomplete>
      </v-flex>
      <v-flex xs6>
        <v-autocomplete
          v-if="canManageUsers"
          v-model="user"
          :items="users"
          :loading="userIsLoading"
          no-filter
          :search-input.sync="searchOwners"
          item-text="name"
          item-value="id"
          chips
          label="Owner"
          return-object></v-autocomplete>
      </v-flex>
      <v-flex xs2>
        <v-btn @click="search">Search</v-btn>
      </v-flex>
    </v-layout>
    <team_form
      ref="teamForm"
      @teamUpdated="teamUpdated"></team_form>
    <v-data-table
      :headers="headers"
      :items="teams"
      hide-actions
      class="elevation-1"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.id }}</td>
        <td>{{ props.item.name }}</td>
        <td>
          <span
            v-if="props.item.country !== null && typeof props.item.country !== 'undefined'">
            {{ props.item.country.alpha_2 }}
          </span>
        </td>
        <td>
          ${{ formatMoney(props.item.team_value) }}
        </td>
        <td v-if="canManageUsers">
          <a
            v-if="props.item.user_id !== null && typeof props.item.user_id !== 'undefined'"
            v-on:click="$router.push('/users/' + props.item.user_id + '/edit')">
            {{ props.item.user_name }}
          </a>
        </td>
        <td>
          <v-btn fab dark small color="primary" v-on:click="$router.push('/teams/' + props.item.id + '/view')">
            <v-icon dark>view_headline</v-icon>
          </v-btn>
          <v-btn
            fab dark small color="primary"
            v-on:click="editTeam(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' && currentUser.hasPermission('edit_teams')"
          >
            <v-icon dark>edit</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>
    <v-layout row justify-center>
      <v-pagination
        v-model="page"
        :length="totalPage"
      ></v-pagination>
    </v-layout>
  </v-container>
</template>

<script>
  import {searchCountries} from '../../api/countries'
  import {searchUsers} from '../../api/users'
  import {searchTeams} from '../../api/teams'
  import TeamForm from './TeamForm'
  import {globals} from '../mixins/globals'
  import Team from '../../models/Team'

  export default {
    data () {
      return {
        isLoading: false,
        alert: false,
        success: false,
        name: null,
        countries: [],
        users: [],
        country: null,
        user: null,
        countryIsLoading: false,
        searchCountries: false,
        userIsLoading: false,
        searchOwners: false,
        page: 1,
        totalPage: 1
      }
    },
    computed: {
      loading () {
        return this.$store.state.loading
      },
      currentUser () {
        return this.$store.getters.currentUser
      },
      canManageUsers () {
        return this.currentUser !== null && typeof this.currentUser !== 'undefined' && this.currentUser.hasPermission('manage_users')
      },
      headers () {
        let data = [
          {
            text: '',
            value: 'id'
          },
          {
            text: 'Name',
            value: 'name'
          },
          {
            text: 'Country',
            value: 'country'
          },
          {
            text: 'Team Value',
            value: 'team_value'
          }
        ]
        if (this.canManageUsers) {
          data.push({
            text: 'User',
            value: 'user'
          })
        }
        data.push({
          text: 'Actions',
          value: 'actions'
        })
        return data
      },
      searchData () {
        let self = this
        let data = {}
        data.query = self.name
        if (self.country !== null && typeof self.country !== 'undefined') {
          data.country = self.country.id
        } else {
          data.country = null
        }
        if (self.user !== null && typeof self.user !== 'undefined') {
          data.user = self.user.id
        } else {
          data.user = null
        }
        data.page = 1
        return data
      },
      error () {
        return this.$store.state.error
      },
      message () {
        return this.$store.state.message
      }
    },
    created: function () {
      this.searchTeams(this.searchData)
    },
    methods: {
      searchTeams: function (params) {
        let self = this
        self.$store.commit('setLoading', true)
        searchTeams(params).then(function (response) {
          let data = response.data.data
          let teams = []
          for (let i = 0; i < data.length; i++) {
            teams.push(new Team(data[i]))
          }
          self.teams = teams
          self.page = response.data.meta.current_page
          self.totalPage = response.data.meta.last_page
          self.$store.commit('setLoading', false)
        })
      },
      search: function () {
        this.searchTeams(this.searchData)
      },
      teamUpdated: function () {
        this.alert = false
        this.$store.commit('setMessage', 'Team updated successfully.')
        let data = this.searchData
        data.page = 1
        this.searchTeams(data)
      },
      editTeam: function (team) {
        team = new Team(JSON.parse(JSON.stringify(team)))
        this.$refs.teamForm.open(team)
      }
    },
    watch: {
      page (val) {
        let data = this.searchData
        data.page = val
        this.searchTeams(data)
      },
      loading (value) {
        this.isLoading = value
      },
      isLoading (value) {
        this.$store.commit('setLoading', value)
      },
      searchCountries (value) {
        if (value === null || value === '') {
          return
        }
        this.countryIsLoading = true
        let self = this
        searchCountries(value).then(function (response) {
          let data = response.data.data
          if (self.country !== null && typeof self.country !== 'undefined') {
            data = data.concat([self.country])
          }
          self.countries = data
        }).catch(function (err) {
          console.log(err)
        }).finally(function () {
          self.countryIsLoading = false
        })
      },
      searchOwners (value) {
        if (value === null || value === '') {
          return
        }
        this.userIsLoading = true
        let self = this
        searchUsers(value).then(function (response) {
          let data = response.data.data
          if (self.user !== null && typeof self.user !== 'undefined') {
            data = data.concat([self.user])
          }
          self.users = data
        }).catch(function (err) {
          console.log(err)
        }).finally(function () {
          self.userIsLoading = false
        })
      },
      error (value) {
        if (value) {
          this.alert = true
        }
      },
      message (value) {
        if (value) {
          this.success = true
        }
      },
      alert (value) {
        if (!value) {
          this.$store.commit('setError', null)
        }
      },
      success (value) {
        if (!value) {
          this.$store.commit('setMessage', null)
        }
      }
    },
    components: {
      TeamForm
    },
    mixins: [globals]
  }
</script>


<style>
  div.is-loading {
    position:fixed;
    top: 35%;
    z-index: 1000;
    left: 50%;
  }
</style>
