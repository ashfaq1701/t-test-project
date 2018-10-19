const state = {
  menuItems: [
    {
      label: 'Manage Users',
      permission: 'manage_users',
      icon: 'person',
      route: '/users'
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
