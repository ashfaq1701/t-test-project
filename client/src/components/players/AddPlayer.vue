<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Add Player</h2>
    </v-layout>
    <player-form></player-form>
  </v-container>
</template>

<script>
  import PlayerForm from './PlayerForm'

  export default {
    data () {
      return {}
    },
    components: {
      PlayerForm
    }
  }
</script>
