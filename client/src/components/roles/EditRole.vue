<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Edit Role</h2>
    </v-layout>
    <role-form :id="id"></role-form>
  </v-container>
</template>

<script>
  import RoleForm from './RoleForm'

  export default {
    props: ['id'],
    data () {
      return {}
    },
    components: {
      RoleForm
    }
  }
</script>