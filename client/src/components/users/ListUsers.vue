<template>
  <v-container fluid>
    <v-layout row justify-center>
      <h2>Manage Users</h2>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12>
        <v-alert type="error" dismissible v-model="alert">
          {{ error }}
        </v-alert>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs11>
        <v-text-field
          v-model="query"
          label="Search By Name or Email"
        ></v-text-field>
      </v-flex>
      <v-flex xs1>
        <v-btn @click="search">Search</v-btn>
      </v-flex>
    </v-layout>
    <v-btn color="info" class="right" v-on:click="addUser()">Add User</v-btn>
    <confirm ref="confirm"></confirm>
    <v-data-table
      :headers="headers"
      :items="users"
      hide-actions
      class="elevation-1"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.id }}</td>
        <td>{{ props.item.name }}</td>
        <td>{{ props.item.email }}</td>
        <td>
          <span v-for="role in props.item.roles">
            {{ role.name }}
          </span>
        </td>
        <td>
          <v-btn fab dark small color="primary" v-on:click="editUser(props.item.id)">
            <v-icon dark>edit</v-icon>
          </v-btn>
          <v-btn fab dark small color="red" v-on:click="deleteUser(props.item.id)">
            <v-icon dark>remove</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>
    <v-layout row justify-center>
      <v-pagination
        v-model="page"
        :length="totalPage"
      ></v-pagination>
    </v-layout>
  </v-container>
</template>

<script>
  import {getUsers, deleteUser, searchUsersPaginated} from '../../api/users'
  import Confirm from '../parts/Confirm'

  export default {
    data () {
      return {
        headers: [
          {
            text: '',
            value: 'id'
          },
          {
            text: 'Name',
            value: 'name'
          },
          {
            text: 'Email',
            value: 'email'
          },
          {
            text: 'Role',
            value: 'role'
          },
          {
            text: 'Actions',
            value: 'actions'
          }
        ],
        users: [],
        page: 1,
        totalPage: 1,
        query: '',
        alert: false
      }
    },
    created () {
      this.getUsers()
    },
    methods: {
      getUsers: function () {
        let self = this
        getUsers(self.page).then(function (resp) {
          self.users = resp.data.data
          self.page = resp.data.meta.current_page
          self.totalPage = resp.data.meta.last_page
        })
      },
      search: function () {
        let self = this
        searchUsersPaginated(self.query, self.page).then(function (resp) {
          self.users = resp.data.data
          self.page = resp.data.meta.current_page
          self.totalPage = resp.data.meta.last_page
        })
      },
      editUser: function (userId) {
        this.$router.push('/users/' + userId + '/edit')
      },
      addUser: function () {
        this.$router.push('/users/add')
      },
      deleteUser: function (userId) {
        let self = this
        self.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' }).then((confirm) => {
          if (confirm === true) {
            deleteUser(userId).then(function () {
              for (let i = 0; i < self.users.length; i++) {
                if (self.users[i].id === userId) {
                  self.users.splice(i, 1)
                }
              }
            }).catch(function (error) {
              self.$store.commit('setError', error.response.data.message)
            })
          }
        })
      }
    },
    computed: {
      error () {
        return this.$store.state.error
      },
      currentUser () {
        return this.$store.getters.currentUser
      }
    },
    watch: {
      error (value) {
        if (value) {
          this.alert = true
        }
      },
      alert (value) {
        if (!value) {
          this.$store.commit('setError', null)
        }
      },
      page () {
        this.getUsers()
      }
    },
    components: {
      Confirm
    }
  }
</script>
