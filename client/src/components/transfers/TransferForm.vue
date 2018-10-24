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
                  v-model="transfer.asking_price"
                  type="number"
                  label="Asking Price"
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
  import {createTransfer, editTransfer} from '../../api/transfers'

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
        },
        valid: false,
        error: '',
        alert: false,
        isLoading: false,
        requiredRules: [
          v => !!v || 'This field is required'
        ]
      }
    },
    methods: {
      open: function (transfer) {
        if (transfer !== null && typeof transfer !== 'undefined') {
          this.transfer = transfer
        } else {
          this.transfer = this.emptyTransfer
        }
        this.passedTransfer = transfer
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
          asking_price: self.transfer.asking_price
        }
        let promise = null
        if (self.passedTransfer === null) {
          promise = createTransfer(data)
        } else {
          promise = editTransfer(self.passedTransfer.id, data)
        }
        promise.then(function (response) {
          self.isLoading = false
          self.$emit('transferUpdated')
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
      emptyTransfer: function () {
        return {
          id: '',
          asking_price: ''
        }
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
