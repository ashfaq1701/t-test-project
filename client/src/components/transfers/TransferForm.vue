<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">
          <span v-if="passedTransfer === null">Add </span>
          <span v-else>Edit </span>
          Transfer
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

  export default Vue.component('transfer_form', {
    data () {
      return {
        dialog: false,
        passedTransfer: null,
        transfer: {
          id: '',
          asking_price: ''
        },
        options: {
          color: 'primary',
          width: 600
        }
      }
    },
    methods: {
      open: function (transfer) {
        this.transfer = transfer
        this.passedTransfer = transfer
        this.dialog = true
      },
      save: function () {

      },
      close: function () {
        this.dialog = false
      }
    }
  })
</script>
