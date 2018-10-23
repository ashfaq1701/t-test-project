import Country from './Country'
import PlayerRole from './PlayerRole'

export default class Player {
  constructor ({id, first_name, last_name, age, price, country, player_role, team_id, team_name}) {
    this.id = id
    this.first_name = first_name
    this.last_name = last_name
    this.age = age
    this.price = price
    this.country = null
    if (country !== null) {
      this.country = new Country(country)
    }
    this.player_role = null
    if (player_role !== null) {
      this.player_role = new PlayerRole(player_role)
    }
    this.team_id = team_id
    this.team_name = team_name
  }
}
