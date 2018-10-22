const state = {
  menuItems: [
    {
      label: 'Users',
      permission: 'manage_users',
      icon: 'person',
      route: '/users'
    },
    {
      label: 'Teams',
      permission: 'get_teams',
      icon: 'group',
      route: '/teams'
    },
    {
      label: 'Players',
      permission: 'get_players',
      icon: 'games',
      route: '/players'
    },
    {
      label: 'Transfers',
      permission: 'get_transfers',
      icon: 'swap_horiz',
      route: '/transfers'
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
