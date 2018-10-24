import Country from './Country'
import Player from './Player'

export default class Team {
  constructor ({id, name, fund, country, user_id, user_name, players, team_value}) {
    this.id = id
    this.name = name
    this.fund = fund
    this.country = null
    if (country !== null) {
      this.country = new Country(country)
    }
    this.user_id = user_id
    this.user_name = user_name
    this.players = []
    this.team_value = team_value
    for (let i = 0; i < players.length; i++) {
      this.players.push(new Player(players[i]))
    }
  }
}
