<template>
  <v-container fluid>
    <v-layout row justify-center>
      <h2>Manage Roles</h2>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12>
        <v-alert type="error" dismissible v-model="alert">
          {{ error }}
        </v-alert>
      </v-flex>
    </v-layout>
    <v-btn color="info" class="right" v-on:click="addRole()">Add Role</v-btn>
    <confirm ref="confirm"></confirm>
    <v-data-table
      :headers="headers"
      :items="roles"
      hide-actions
      class="elevation-1"
    >
      <template slot="items" slot-scope="props">
        <td>{{ props.item.id }}</td>
        <td>{{ props.item.name }}</td>
        <td>
          <v-btn fab dark small color="primary" v-on:click="editRole(props.item.id)">
            <v-icon dark>edit</v-icon>
          </v-btn>
          <v-btn fab dark small color="red" v-on:click="deleteRole(props.item.id)">
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
  import {getRoles, deleteRole} from '../../api/roles'
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
            text: 'Actions',
            value: 'actions'
          }
        ],
        roles: [],
        page: 1,
        totalPage: 1,
        alert: false
      }
    },
    created () {
      this.getRoles()
    },
    methods: {
      getRoles: function () {
        let self = this
        getRoles(self.page).then(function (resp) {
          self.roles = resp.data.data
          self.page = resp.data.meta.current_page
          self.totalPage = resp.data.meta.last_page
        })
      },
      editRole: function (roleId) {
        this.$router.push('/roles/' + roleId + '/edit')
      },
      addRole: function () {
        this.$router.push('/roles/add')
      },
      deleteRole: function (roleId) {
        let self = this
        self.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' }).then((confirm) => {
          if (confirm === true) {
            deleteRole(roleId).then(function () {
              for (let i = 0; i < self.roles.length; i++) {
                if (self.roles[i].id === roleId) {
                  self.roles.splice(i, 1)
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
      }
    },
    watch: {
      page () {
        this.getRoles()
      },
      error (value) {
        if (value) {
          this.alert = true
        }
      },
      alert (value) {
        if (!value) {
          this.$store.commit('setError', null)
        }
      }
    },
    components: {
      Confirm
    }
  }
</script>
