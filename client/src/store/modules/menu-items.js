const state = {
  menuItems: [
    {
      label: 'Manage Users',
      permission: 'manage_users',
      icon: 'person',
      route: '/users'
    },
    {
      label: 'Manage Roles',
      permission: 'manage_roles',
      icon: 'lock',
      route: '/roles'
    }
  ]
}

const getters = {
  menuItems (state) {
    return state.menuItems
  }
}

export default {
  state,
  getters
}
