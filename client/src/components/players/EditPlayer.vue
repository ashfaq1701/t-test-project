<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Edit Player</h2>
    </v-layout>
    <player-form :id="id"></player-form>
  </v-container>
</template>

<script>
  import PlayerForm from './PlayerForm'

  export default {
    props: ['id'],
    data () {
      return {}
    },
    components: {
      PlayerForm
    }
  }
</script>
