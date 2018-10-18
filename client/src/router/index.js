import Vue from 'vue'
import Router from 'vue-router'
import {store} from '../store'

const routerOptions = [
  {
    path: '/',
    name: 'Landing',
    component: 'Landing'
  },
  {
    path: '/signin',
    name: 'Signin',
    component: 'Signin'
  },
  {
    path: '/signup',
    name: 'Signup',
    component: 'Signup'
  },
  {
    path: '/confirmation-sent/:email',
    name: 'ConfirmationSent',
    component: 'ConfirmationSent',
    props: true
  },
  {
    path: '/confirm',
    name: 'ConfirmationResponse',
    component: 'ConfirmationResponse'
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: 'ForgotPassword'
  },
  {
    path: '/password/reset/:token',
    name: 'FrontUpdatePassword',
    component: 'UpdatePassword',
    props: true
  },
  {
    path: '/home',
    name: 'Home',
    component: 'Home',
    meta: {requiresAuth: true, requireUpdatedPassword: true}
  },
  {
    path: '/roles',
    name: 'ListRoles',
    component: 'roles/ListRoles',
    meta: {requiresAuth: true, requireUpdatedPassword: true}
  },
  {
    path: '/roles/add',
    name: 'AddRole',
    component: 'roles/AddRole',
    meta: {requiresAuth: true, requireUpdatedPassword: true}
  },
  {
    path: '/roles/:id/edit',
    name: 'EditRole',
    component: 'roles/EditRole',
    meta: {requiresAuth: true, requireUpdatedPassword: true},
    props: true
  },
  {
    path: '/users',
    name: 'ListUsers',
    component: 'users/ListUsers',
    meta: {requiresAuth: true, requireUpdatedPassword: true}
  },
  {
    path: '/users/add',
    name: 'AddUser',
    component: 'users/AddUser',
    meta: {requiresAuth: true, requireUpdatedPassword: true}
  },
  {
    path: '/users/:id/edit',
    name: 'EditUser',
    component: 'users/EditUser',
    meta: {requiresAuth: true, requireUpdatedPassword: true},
    props: true
  },
  {
    path: '/profile',
    name: 'UserProfile',
    component: 'users/UserProfile',
    meta: {requiresAuth: true, requireUpdatedPassword: true},
    props: true
  },
  {
    path: '/update-password',
    name: 'UpdatePassword',
    component: 'users/UpdatePassword',
    meta: {requiresAuth: true}
  },
  {
    path: '*',
    name: 'NotFound',
    component: 'NotFound'
  }
]

const routes = routerOptions.map(route => {
  return {
    ...route,
    component: () => import(`@/components/${route.component}.vue`)
  }
})

Vue.use(Router)

const router = new Router({
  mode: 'history',
  routes
})

router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requireUpdatedPassword = to.matched.some(record => record.meta.requireUpdatedPassword)
  const recordName = to.name
  const isAuthenticated = store.getters.isAuthenticated
  const isPasswordUpdateRequired = store.getters.isPasswordUpdateRequired
  if (requiresAuth && !isAuthenticated) {
    next('/signin?path=' + encodeURI(to.path))
  } else if (isAuthenticated && requireUpdatedPassword && isPasswordUpdateRequired) {
    next('/update-password')
  } else if (isAuthenticated && (recordName === 'Signin' || recordName === 'Signup')) {
    next('/home')
  } else {
    next()
  }
})

export default router
