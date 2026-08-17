import { createRouter, createWebHistory } from 'vue-router'
import store from '../store'

import Login              from '../components/Login'
import Dashboard          from '../components/Dashboard'
import PersonalAccounts   from '../components/PersonalAccounts'
import BusinessAccounts   from '../components/BusinessAccounts'
import ViewPersonalAccount from '../components/ViewPersonalAccount'
import ViewBusinessAccount from '../components/ViewBusinessAccount'
import Retailers          from '../components/Retailers'
import Distributors       from '../components/Distributors'
import Manufacturers      from '../components/Manufacturers'
import LoanApplications   from '../components/LoanApplications'
import Salons             from '../components/Salons'

const routes = [
  { path: '/',                          name: 'Login',                 component: Login },
  { path: '/dashboard',                 name: 'dashboard',             component: Dashboard,           meta: { requiresAuth: true } },
  { path: '/personal-accounts',         name: 'personal-accounts',     component: PersonalAccounts,    meta: { requiresAuth: true } },
  { path: '/view-personal-account/:id', name: 'view-personal-account', component: ViewPersonalAccount, meta: { requiresAuth: true } },
  { path: '/view-business-account/:id', name: 'view-business-account', component: ViewBusinessAccount, meta: { requiresAuth: true } },
  { path: '/business-accounts',         name: 'business-accounts',     component: BusinessAccounts,    meta: { requiresAuth: true } },
  { path: '/retailers',                 name: 'retailers',             component: Retailers,           meta: { requiresAuth: true } },
  { path: '/distributors',              name: 'distributors',          component: Distributors,        meta: { requiresAuth: true } },
  { path: '/manufacturers',             name: 'manufacturers',         component: Manufacturers,       meta: { requiresAuth: true } },
  { path: '/loan-applications',         name: 'loan-applications',     component: LoanApplications,    meta: { requiresAuth: true } },
  { path: '/salons',                    name: 'salons',                component: Salons,              meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
  scrollBehavior() { return { top: 0 } },
})

router.beforeEach((to, _from, next) => {
  if (to.meta.requiresAuth && !store.getters.isLoggedIn) {
    return next({ name: 'Login' })
  }
  next()
})

export default router
