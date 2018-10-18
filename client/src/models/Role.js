import Permission from './Permission'

export default class Role {
  constructor ({ id, name, permissions }) {
    this.id = id
    this.name = name
    this.permissions = []
    for (let i = 0; i < permissions.length; i++) {
      let permissionObj = new Permission(permissions[i])
      this.permissions.push(permissionObj)
    }
  }
}
