<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="close">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">
          Transfer Player
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
                <v-text-field
                  v-model="transfer.asking_price"
                  label="Asking Price"
                  type="number"
                  :rules="priceRules"
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
  import {globals} from '../mixins/globals'
  import {createTransfer} from '../../api/transfers'

  export default Vue.component('transfer_player', {
    data () {
      return {
        valid: false,
        dialog: false,
        error: '',
        alert: false,
        isLoading: false,
        options: {
          color: 'primary',
          width: 600
        },
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
        transfer: {
          asking_price: ''
        },
        requiredRules: [
          v => !!v || 'This field is required'
        ],
        priceRules: [
          v => !!v || 'This field is required',
          v => parseInt(v) > 0 || 'Price must be greater than zero'
        ]
      }
    },
    methods: {
      open: function (player) {
        this.$refs.form.reset()
        this.player = player
        this.error = ''
        this.alert = false
        this.dialog = true
      },
      save: function () {
        if (!this.valid) {
          return false
        }
        let self = this
        self.isLoading = true
        let data = {
          asking_price: this.transfer.asking_price,
          player_id: this.player.id
        }
        createTransfer(data).then(function (response) {
          self.isLoading = false
          self.$emit('transferCreated')
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
    mixins: [globals]
  })
</script>
