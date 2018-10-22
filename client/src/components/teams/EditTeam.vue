<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Edit Team</h2>
    </v-layout>
    <team-form :id="id"></team-form>
  </v-container>
</template>

<script>
  import TeamForm from './TeamForm'

  export default {
    props: ['id'],
    data () {
      return {}
    },
    components: {
      TeamForm
    }
  }
</script>
