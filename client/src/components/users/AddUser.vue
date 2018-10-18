<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Add User</h2>
    </v-layout>
    <user-form></user-form>
  </v-container>
</template>

<script>
  import UserForm from './UserForm'

  export default {
    data () {
      return {}
    },
    components: {
      UserForm
    }
  }
</script>