<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Add Transfer</h2>
    </v-layout>
    <transfer-form></transfer-form>
  </v-container>
</template>

<script>
  import TransferForm from './TransferForm'

  export default {
    data () {
      return {}
    },
    components: {
      TransferForm
    }
  }
</script>
