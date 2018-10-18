import {getUser} from '../api/auth'
import Role from './Role'
import Permission from './Permission'
import Photo from './Photo'

export default class User {
  static from (token) {
    try {
      if (token !== undefined) {
        return getUser()
      } else {
        return null
      }
    } catch (_) {
      return null
    }
  }

  constructor ({ id, name, email, password_update_required, roles, profile_photo }) {
    this.id = id
    this.name = name
    this.email = email
    this.password_update_required = password_update_required
    this.profile_photo = null
    if (profile_photo !== null) {
      this.profile_photo = new Photo(profile_photo)
    }
    this.roles = []
    this.permissions = []

    for (let i = 0; i < roles.length; i++) {
      let role = roles[i]
      let roleObj = new Role(role)
      this.roles.push(roleObj)
      for (let j = 0; j < role.permissions.length; j++) {
        let permissionObj = new Permission(role.permissions[j])
        this.permissions.push(permissionObj)
      }
    }
  }

  hasPermission (name) {
    for (let i = 0; i < this.permissions.length; i++) {
      if (this.permissions[i].name === name) {
        return true
      }
    }
    return false
  }

  hasRole (name) {
    for (let i = 0; i < this.roles.length; i++) {
      if (this.roles[i].name === name) {
        return true
      }
    }
    return false
  }
}
