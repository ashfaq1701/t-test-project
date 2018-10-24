<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">Confirm Buying Player</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <span>You are buying </span>
        <striong v-if="transfer.player !== null && typeof transfer.player !== 'undefined'">{{ playerName }} </striong>
        <span v-else>a player </span>
        <span v-if="transfer.placed_from !== null && typeof transfer.placed_from !== 'undefined'">
          from <strong>{{ transfer.placed_from.name }} </strong>
        </span>
        <span>with <strong>${{ formatMoney(transfer.asking_price) }}</strong>.</span>
        <br/>
        <span>Do you really want to buy this player?</span>
      </v-card-text>
      <v-card-actions class="pt-0">
        <v-spacer></v-spacer>
        <v-btn color="primary darken-1" flat="flat" @click.native="agree">Yes</v-btn>
        <v-btn color="grey" flat="flat" @click.native="cancel">Cancel</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  import Vue from 'vue'
  import {globals} from '../mixins/globals'

  export default Vue.component('buy_player_confirm', {
    data () {
      return {
        dialog: false,
        resolve: null,
        reject: null,
        transfer: {
          id: '',
          player: null,
          asking_price: '',
          placed_from: null
        },
        options: {
          color: 'primary',
          width: 500
        }
      }
    },
    methods: {
      open (transfer) {
        this.transfer = transfer
        this.dialog = true
        return new Promise((resolve, reject) => {
          this.resolve = resolve
          this.reject = reject
        })
      },
      agree () {
        this.resolve(true)
        this.dialog = false
      },
      cancel () {
        this.resolve(false)
        this.dialog = false
      }
    },
    computed: {
      playerName: function () {
        if (this.transfer.player === null || typeof this.transfer.player === 'undefined') {
          return ''
        } else {
          return [this.transfer.player.first_name, this.transfer.player.last_name].join(' ')
        }
      }
    },
    mixins: [globals]
  })
</script>
