<template>
  <v-container fluid>
  </v-container>
</template>

<script>
  export default {
    computed: {
      user: function () {
        return this.$store.getters.currentUser
      }
    },
    components: {
    }
  }
</script>
