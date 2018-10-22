<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Add Team</h2>
    </v-layout>
    <team-form></team-form>
  </v-container>
</template>

<script>
  import TeamForm from './TeamForm'

  export default {
    data () {
      return {}
    },
    components: {
      TeamForm
    }
  }
</script>
