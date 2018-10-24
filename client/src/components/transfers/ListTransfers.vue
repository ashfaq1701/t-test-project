<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Transfer List</h2>
    </v-layout>
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
          v-model="playerName"
          label="Search By Player Name"
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
    <transfer_form
      ref="transferForm"
      @transferUpdated="transferUpdated">
    </transfer_form>
    <confirm ref="confirm"></confirm>
    <buy_player_confirm ref="buyPlayerConfirm"></buy_player_confirm>
    <v-data-table
      :headers="headers"
      :items="transfers"
      hide-actions
      class="elevation-1"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.id }}</td>
        <td v-if="props.item.player !== null">{{ props.item.player.first_name }}</td>
        <td v-if="props.item.player !== null">{{ props.item.player.last_name }}</td>
        <td v-if="props.item.player !== null">{{ props.item.player.age }}</td>
        <td>
          <span
            v-if="props.item.player !== null &&
            props.item.player.country !== null &&
            typeof props.item.player.country !== 'undefined'">
            {{ props.item.player.country.alpha_2 }}
          </span>
        </td>
        <td>
          <a
            v-on:click="$router.push('/teams/' + props.item.placed_from.id + '/view')"
            v-if="props.item.placed_from !== null">
            {{ props.item.placed_from.name }}
          </a>
        </td>
        <td>
          <span v-if="props.item.player !== null && props.item.player.player_role !== null">
            {{ props.item.player.player_role.name }}
          </span>
        </td>
        <td>${{ formatMoney(props.item.asking_price) }}</td>
        <td>
          <v-btn
            fab dark small color="primary"
            v-on:click="editTransfer(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' && currentUser.hasPermission('edit_transfers')"
          >
            <v-icon dark>edit</v-icon>
          </v-btn>
          <v-btn
            fab dark small color="primary"
            v-on:click="acceptTransfer(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' &&
            (currentUser.hasPermission('accept_transfer_player') && (currentUser.team !== null) &&
            (props.item.placed_from !== null) && (props.item.placed_from.id !== currentUser.team.id) &&
            (currentUser.team.fund > props.item.asking_price))"
          >
            <v-icon dark>shopping_cart</v-icon>
          </v-btn>
          <v-btn
            fab dark small color="red"
            v-on:click="deleteTransfer(props.item)"
            v-if="currentUser !== null && typeof currentUser !== 'undefined' &&
            currentUser.hasPermission('delete_transfers')"
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
  import {searchCountries} from '../../api/countries'
  import {searchTeamsByName} from '../../api/teams'
  import {searchTransfers, deleteTransfer, editTransfer} from '../../api/transfers'
  import {getPlayerRoles} from '../../api/playerRoles'
  import {getUser} from '../../api/auth'
  import Confirm from '../parts/Confirm'
  import BuyPlayerConfirm from './BuyPlayerConfirm'
  import TransferForm from './TransferForm'
  import {globals} from '../mixins/globals'
  import Transfer from '../../models/Transfer'
  import User from '../../models/User'
  import * as MutationTypes from '../../store/mutation-types'

  export default {
    data () {
      return {
        isLoading: false,
        alert: false,
        success: false,
        page: 1,
        totalPage: 1,
        transfers: [],
        playerName: null,
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
        searchTeams: false
      }
    },
    computed: {
      loading () {
        return this.$store.state.loading
      },
      currentUser () {
        return this.$store.getters.currentUser
      },
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
            text: 'Age',
            value: 'age'
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
            text: 'Asking Price',
            value: 'asking_price'
          },
          {
            text: 'Actions',
            value: 'actions'
          }
        ]
      },
      error () {
        return this.$store.state.error
      },
      message () {
        return this.$store.state.message
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
        data.player_name = self.playerName
        if (self.playerRole !== null && typeof self.playerRole !== 'undefined') {
          data.player_role = self.playerRole.id
        } else {
          data.player_role = null
        }
        data.min_price = self.min_price
        data.max_price = self.max_price
        data.type = 'incomplete'
        data.page = 1
        return data
      }
    },
    methods: {
      searchTransfers: function (params) {
        let self = this
        self.$store.commit('setLoading', true)
        searchTransfers(params).then(function (response) {
          let data = response.data.data
          let transfers = []
          for (let i = 0; i < data.length; i++) {
            transfers.push(new Transfer(data[i]))
          }
          self.transfers = transfers
          self.page = response.data.meta.current_page
          self.totalPage = response.data.meta.last_page
          self.$store.commit('setLoading', false)
        })
      },
      transferUpdated: function () {
        this.alert = false
        this.$store.commit('setMessage', 'Transfer updated successfully.')
        let data = this.searchData
        data.page = 1
        this.searchTransfers(data)
      },
      deleteTransfer: function (transfer) {
        let self = this
        self.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' }).then((confirm) => {
          if (confirm === true) {
            self.$store.commit('setLoading', true)
            deleteTransfer(transfer.id).then(function () {
              self.alert = false
              self.$store.commit('setLoading', false)
              self.$store.commit('setMessage', 'Transfer deleted successfully.')
              let data = self.searchData
              data.page = 1
              self.searchTransfers(data)
            }).catch(function (error) {
              self.$store.commit('setLoading', false)
              self.success = false
              self.$store.commit('setError', error.response.data.message)
            })
          }
        })
      },
      acceptTransfer: function (transfer) {
        let self = this
        let team = this.currentUser.team
        if (team === null || typeof team === 'undefined') {
          self.$store.commit('setError', 'You do not have own team to buy this player.')
          return false
        }
        if (team.fund < transfer.asking_price) {
          self.$store.commit('setError', 'You do not have enough fund to buy this player.')
          return false
        }
        if ((transfer.placed_from !== null) && (team.id === transfer.placed_from.id)) {
          self.$store.commit('setError', 'This player is already in your team.')
          return false
        }
        self.$refs.buyPlayerConfirm.open(transfer).then(function (confirm) {
          if (confirm === true) {
            self.$store.commit('setLoading', true)
            editTransfer(transfer.id, {}).then(function () {
              self.alert = false
              self.$store.commit('setLoading', false)
              self.$store.commit('setMessage', 'You bought this player successfully')
              getUser().then(function (response) {
                let user = new User(response.data.data)
                self.$store.commit(MutationTypes.LOGIN, user)
              })
              let data = self.searchData
              data.page = 1
              self.searchTransfers(data)
            }).catch(function (error) {
              self.$store.commit('setLoading', false)
              self.success = false
              self.$store.commit('setError', error.response.data.message)
            })
          }
        })
      },
      editTransfer: function (transfer) {
        transfer = new Transfer(JSON.parse(JSON.stringify(transfer)))
        this.$refs.transferForm.open(transfer)
      },
      search: function () {
        this.searchTransfers(this.searchData)
      }
    },
    created: function () {
      let self = this
      getPlayerRoles().then(function (response) {
        self.playerRoles = response.data.data
      })
      this.searchTransfers(this.searchData)
    },
    watch: {
      page (val) {
        let data = this.searchData
        data.page = val
        this.searchTransfers(data)
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
      loading (value) {
        this.isLoading = value
      },
      isLoading (value) {
        this.$store.commit('setLoading', value)
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
      Confirm,
      TransferForm,
      BuyPlayerConfirm
    },
    mixins: [globals]
  }
</script>
