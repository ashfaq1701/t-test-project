<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Add Role</h2>
    </v-layout>
    <role-form></role-form>
  </v-container>
</template>

<script>
  import RoleForm from './RoleForm'

  export default {
    data () {
      return {}
    },
    components: {
      RoleForm
    }
  }
</script>