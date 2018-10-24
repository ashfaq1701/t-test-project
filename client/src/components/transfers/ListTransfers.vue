<template>
  <v-container>
    <v-layout row justify-center>
      <h2>Transfer List</h2>
    </v-layout>
    <div class="text-xs-center is-loading" v-if="isLoading">
      <v-progress-circular
        indeterminate
        color="primary"
        size="80"
      ></v-progress-circular>
    </div>
    <v-layout row wrap>
      <v-flex xs12>
        <v-alert type="error" dismissible v-model="alert">
          {{ error }}
        </v-alert>
      </v-flex>
      <v-flex xs12>
        <v-alert type="success" dismissible v-model="success">
          {{ message }}
        </v-alert>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12>
        <h2>Filters</h2>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    data () {
      return {
        isLoading: false,
        alert: false,
        success: false,
        page: 1,
        totalPage: 1,
        transfers: []
      }
    },
    computed: {
      loading () {
        return this.$store.state.loading
      },
      currentUser () {
        return this.$store.getters.currentUser
      },
      error () {
        return this.$store.state.error
      },
      message () {
        return this.$store.state.message
      }
    },
    watch: {
      loading (value) {
        this.isLoading = value
      },
      isLoading (value) {
        this.$store.commit('setLoading', value)
      },
      error (value) {
        if (value) {
          this.alert = true
        }
      },
      message (value) {
        if (value) {
          this.success = true
        }
      },
      alert (value) {
        if (!value) {
          this.$store.commit('setError', null)
        }
      },
      success (value) {
        if (!value) {
          this.$store.commit('setMessage', null)
        }
      }
    }
  }
</script>
