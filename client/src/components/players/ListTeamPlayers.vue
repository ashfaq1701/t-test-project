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
          :search-input.sync="searchCountries"
          item-text="name"
          item-value="id"
          chips
          label="Country"
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
          <span v-if="props.item.country !== null">{{ props.item.country.alpha_2 }}</span>
        </td>
        <td>
          <span v-if="props.item.player_role !== null">{{ props.item.player_role.name }}</span>
        </td>
        <td>{{ props.item.price }}</td>
        <td>
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
  import {searchPlayers} from '../../api/players'
  import Player from '../../models/Player'

  export default Vue.component('list_team_players', {
    props: ['team'],
    data () {
      return {
        name: null,
        countries: [],
        country: null,
        min_price: null,
        max_price: null,
        playerRoles: [],
        playerRole: null,
        countryIsLoading: false,
        searchCountries: false,
        players: [],
        page: 1,
        totalPage: 1,
        searchData: {},
        isLoading: false
      }
    },
    created: function () {
      let self = this
      getPlayerRoles().then(function (response) {
        self.playerRoles = response.data.data
      })
      this.searchPlayers({team: self.team.id, page: 1})
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
      search: function () {
        let self = this
        let data = {}
        if (self.country !== null && typeof self.country !== 'undefined') {
          data.country = self.country.id
        }
        if (self.name !== null && typeof self.name !== 'undefined' && self.name !== '') {
          data.query = self.name
        }
        if (self.playerRole !== null && typeof self.playerRole !== 'undefined') {
          data.player_role = self.playerRole.id
        }
        if (self.min_price !== null && self.min_price !== 'undefined' && parseInt(self.min_price) !== 0) {
          data.min_price = self.min_price
        }
        if (self.max_price !== null && self.max_price !== 'undefined' && parseInt(self.max_price) !== 0) {
          data.max_price = self.max_price
        }
        data.team = self.team.id
        data.page = 1
        self.searchData = data
        this.searchPlayers(data)
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
      }
    },
    watch: {
      page (val) {
        this.searchData.page = val
        this.searchData.team = this.team.id
        this.searchPlayers(this.searchData)
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
          if (self.country !== null) {
            data = data.concat([self.country])
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
