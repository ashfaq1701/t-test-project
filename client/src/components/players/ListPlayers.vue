<template>
  <v-container fluid>
    <div class="text-xs-center is-loading" v-if="isLoading">
      <v-progress-circular
        indeterminate
        color="primary"
        size="80"
      ></v-progress-circular>
    </div>
    <v-divider></v-divider>
    <v-spacer></v-spacer>
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
      <v-flex xs12>
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
      <v-flex xs6>
        <v-autocomplete
          v-model="playerRole"
          :items="playerRoles"
          item-text="name"
          item-value="id"
          chips
          label="Player Role"
          return-object></v-autocomplete>
      </v-flex>
      <v-flex xs6>
        <v-text-field
          v-model="min_price"
          label="Min Price"
          type="number"></v-text-field>
      </v-flex>
      <v-flex xs6>
        <v-text-field
          v-model="max_price"
          label="Max Price"
          type="number"></v-text-field>
      </v-flex>
      <v-flex xs2>
        <v-btn @click="search">Search</v-btn>
      </v-flex>
    </v-layout>
    <v-divider></v-divider>
    <v-spacer></v-spacer>
    <view_player ref="viewPlayer"></view_player>
    <player_form
      ref="playerForm"
      @playerUpdated="playerUpdated"></player_form>
    <transfer_player
      ref="transferPlayer"
      @transferCreated="transferCreated"></transfer_player>
    <confirm ref="confirm"></confirm>
    <v-btn
      color="info"
      class="right"
      v-if="currentUser !== null && typeof currentUser !== 'undefined' && currentUser.hasPermission('create_new_player')"
      v-on:click="addPlayer(team)">Add Player</v-btn>
    <v-data-table
      :headers="headers"
      :items="players"
      hide-actions
      class="elevation-1"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.id }}</td>
        <td>{{ props.item.first_name }}</td>
        <td>{{ props.item.last_name }}</td>
        <td>
          <span
            v-if="props.item.country !== null && typeof props.item.country !== 'undefined'">
            {{ props.item.country.alpha_2 }}
          </span>
        </td>
        <td>
          <a
            v-on:click="$router.push('/teams/' + props.item.team_id + '/view')"
            v-if="props.item.team_id !== null && typeof props.item.team_id !== 'undefined'">
            {{ props.item.team_name }}
          </a>
        </td>
        <td>
          <v-autocomplete
            v-if="currentUser !== null && typeof currentUser !== 'undefined' &&
            currentUser.hasPermission('modify_player_role') && props.item.player_role !== null"
            v-model="props.item.player_role"
            :items="playerRoles"
            item-text="name"
            item-value="id"
            chips
            label="Player Role"
            @change="playerRoleUpdated(props.item, props.item.player_role)"
            return-object></v-autocomplete>
          <span v-else-if="props.item.player_role !== null">{{ props.item.player_role.name }}</span>
        </td>
        <td>${{ formatMoney(props.item.price) }}</td>
        <td>
          <v-btn fab dark small color="primary" v-on:click="viewPlayer(props.item)">
            <v-icon dark>view_headline</v-icon>
          </v-btn>
          <v-btn
            fab dark small color="primary"
            v-on:click="editPlayer(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' && currentUser.hasPermission('edit_players')"
          >
            <v-icon dark>edit</v-icon>
          </v-btn>
          <v-btn
            fab dark small color="primary"
            v-on:click="transferPlayer(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' &&
            (currentUser.hasPermission('transfer_own_player') || currentUser.hasPermission('create_new_transfer'))"
          >
            <v-icon dark>swap_horiz</v-icon>
          </v-btn>
          <v-btn
            fab dark small color="red"
            v-on:click="deletePlayer(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' && currentUser.hasPermission('delete_players')"
          >
            <v-icon dark>remove</v-icon>
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
  import Vue from 'vue'
  import {searchCountries} from '../../api/countries'
  import {getPlayerRoles} from '../../api/playerRoles'
  import {searchPlayers, deletePlayer, editPlayer} from '../../api/players'
  import {searchTeamsByName} from '../../api/teams'
  import Player from '../../models/Player'
  import {globals} from '../mixins/globals'
  import ViewPlayer from './ViewPlayer'
  import PlayerForm from './PlayerForm'
  import Confirm from '../parts/Confirm'
  import TransferPlayer from './TransferPlayer'

  export default Vue.component('list_players', {
    data () {
      return {
        alert: false,
        success: false,
        name: null,
        countries: [],
        teams: [],
        team: null,
        country: null,
        min_price: null,
        max_price: null,
        playerRoles: [],
        playerRole: null,
        countryIsLoading: false,
        searchCountries: false,
        teamIsLoading: false,
        searchTeams: false,
        players: [],
        page: 1,
        totalPage: 1,
        isLoading: false
      }
    },
    created: function () {
      let self = this
      getPlayerRoles().then(function (response) {
        self.playerRoles = response.data.data
      })
      this.searchPlayers(this.searchData)
    },
    methods: {
      searchPlayers: function (params) {
        let self = this
        self.$store.commit('setLoading', true)
        searchPlayers(params).then(function (response) {
          let data = response.data.data
          let players = []
          for (let i = 0; i < data.length; i++) {
            players.push(new Player(data[i]))
          }
          self.players = players
          self.page = response.data.meta.current_page
          self.totalPage = response.data.meta.last_page
          self.$store.commit('setLoading', false)
        })
      },
      addPlayer: function () {
        this.$refs.playerForm.open(null, null)
      },
      editPlayer: function (player) {
        player = new Player(JSON.parse(JSON.stringify(player)))
        this.$refs.playerForm.open(player)
      },
      deletePlayer: function (player) {
        let self = this
        self.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' }).then((confirm) => {
          if (confirm === true) {
            deletePlayer(player.id).then(function () {
              self.alert = false
              self.$store.commit('setMessage', 'Player deleted successfully.')
              let data = self.searchData
              data.page = 1
              self.searchPlayers(data)
            }).catch(function (error) {
              self.success = false
              self.$store.commit('setError', error.response.data.message)
            })
          }
        })
      },
      transferPlayer: function (player) {
        this.$refs.transferPlayer.open(player)
      },
      search: function () {
        this.searchPlayers(this.searchData)
      },
      viewPlayer: function (player) {
        this.$refs.viewPlayer.open(player)
      },
      playerUpdated: function () {
        this.alert = false
        this.$store.commit('setMessage', 'Player updated successfully.')
        let data = this.searchData
        data.page = 1
        this.searchPlayers(data)
      },
      transferCreated: function () {
        this.alert = false
        this.$store.commit('setMessage', 'Player placed in transfer list.')
      },
      playerRoleUpdated: function (player, playerRole) {
        editPlayer(player.id, {
          player_role_id: playerRole.id
        })
      }
    },
    computed: {
      headers () {
        return [
          {
            text: '',
            value: 'id'
          },
          {
            text: 'First Name',
            value: 'first_name'
          },
          {
            text: 'Last Name',
            value: 'last_name'
          },
          {
            text: 'Country',
            value: 'country'
          },
          {
            text: 'Team',
            value: 'team'
          },
          {
            text: 'Player Role',
            value: 'player_role'
          },
          {
            text: 'Price',
            value: 'price'
          },
          {
            text: 'Actions',
            value: 'actions'
          }
        ]
      },
      loading () {
        return this.$store.state.loading
      },
      currentUser () {
        return this.$store.getters.currentUser
      },
      searchData () {
        let self = this
        let data = {}
        if (self.country !== null && typeof self.country !== 'undefined') {
          data.country = self.country.id
        } else {
          data.country = null
        }
        if (self.team !== null && typeof self.team !== 'undefined') {
          data.team = self.team.id
        } else {
          data.team = null
        }
        data.query = self.name
        if (self.playerRole !== null && typeof self.playerRole !== 'undefined') {
          data.player_role = self.playerRole.id
        } else {
          data.player_role = null
        }
        data.min_price = self.min_price
        data.max_price = self.max_price
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
    watch: {
      page (val) {
        let data = this.searchData
        data.page = val
        this.searchPlayers(data)
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
    mixins: [
      globals
    ],
    components: {
      ViewPlayer,
      PlayerForm,
      Confirm,
      TransferPlayer
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
