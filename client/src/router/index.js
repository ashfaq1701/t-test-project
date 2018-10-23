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
    meta: {requiresAuth: true}
  },
  {
    path: '/users',
    name: 'ListUsers',
    component: 'users/ListUsers',
    meta: {requiresAuth: true}
  },
  {
    path: '/users/add',
    name: 'AddUser',
    component: 'users/AddUser',
    meta: {requiresAuth: true}
  },
  {
    path: '/users/:id/edit',
    name: 'EditUser',
    component: 'users/EditUser',
    meta: {requiresAuth: true},
    props: true
  },
  {
    path: '/profile',
    name: 'UserProfile',
    component: 'users/UserProfile',
    meta: {requiresAuth: true},
    props: true
  },
  {
    path: '/update-password',
    name: 'UpdatePassword',
    component: 'users/UpdatePassword',
    meta: {requiresAuth: true}
  },
  {
    path: '/players',
    name: 'ListPlayers',
    component: 'players/ListPlayers',
    meta: {requiresAuth: true}
  },
  {
    path: '/teams',
    name: 'ListTeams',
    component: 'teams/ListTeams',
    meta: {requiresAuth: true}
  },
  {
    path: '/teams/:id/view',
    name: 'ViewTeam',
    component: 'teams/ViewTeam',
    meta: {requiresAuth: true},
    props: true
  },
  {
    path: '/transfers',
    name: 'ListTransfers',
    component: 'transfers/ListTransfers',
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
  const recordName = to.name
  const isAuthenticated = store.getters.isAuthenticated
  if (requiresAuth && !isAuthenticated) {
    next('/signin?path=' + encodeURI(to.path))
  } else if (isAuthenticated && (recordName === 'Signin' || recordName === 'Signup')) {
    next('/home')
  } else {
    next()
  }
})

export default router
