<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Edit Transfer</h2>
    </v-layout>
    <transfer-form :id="id"></transfer-form>
  </v-container>
</template>

<script>
  import TransferForm from './TransferForm'

  export default {
    props: ['id'],
    data () {
      return {}
    },
    components: {
      TransferForm
    }
  }
</script>
