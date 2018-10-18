<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Edit User</h2>
    </v-layout>
    <user-form :id="id"></user-form>
  </v-container>
</template>

<script>
  import UserForm from './UserForm'

  export default {
    props: ['id'],
    data () {
      return {}
    },
    components: {
      UserForm
    }
  }
</script>