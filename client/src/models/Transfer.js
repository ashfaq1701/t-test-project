import Player from './Player'
import Team from './Team'

export default class Transfer {
  constructor ({id, asking_price, player, placed_from, transfer_completed_at, transferred_to, is_notified}) {
    this.id = id
    this.asking_price = asking_price
    this.player = null
    if (player !== null) {
      this.player = new Player(player)
    }
    this.placed_from = null
    if (placed_from !== null) {
      this.placed_from = new Team(placed_from)
    }
    this.transfer_completed_at = transfer_completed_at
    this.transferred_to = null
    if (transferred_to !== null) {
      this.transferred_to = new Team(transferred_to)
    }
    this.is_notified = is_notified
  }
}
