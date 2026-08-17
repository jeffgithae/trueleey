import { createStore } from 'vuex'
import axios from 'axios'

export default createStore({
  state: {
    api_url       : (process.env.VUE_APP_API_URL || 'http://localhost:8001/').replace(/\/?$/, '/'),
    email         : localStorage.getItem('email')      || null,
    phone         : localStorage.getItem('phone')      || null,
    balance       : localStorage.getItem('balance')    || '0',
    fullname      : localStorage.getItem('fullname')   || null,
    user_id       : localStorage.getItem('user_id')    || null,
    logged_in     : localStorage.getItem('logged_in') === 'true',
    account_type  : localStorage.getItem('account_type')  || null,
    business_id   : localStorage.getItem('business_id')   || null,
    user_type     : localStorage.getItem('user_type')     || null,
    business_name : localStorage.getItem('business_name') || null,
    cart          : [],
    cart_data     : [],
    message       : '',
    product_cart  : [],
  },

  getters: {
    isLoggedIn : (state) => state.logged_in === true,
    isBusiness : (state) => state.user_type === '1',
    hasShop    : (state) => !!state.business_id,
    userDisplay: (state) => state.fullname || state.email || 'User',
  },

  mutations: {
    /**
     * Hydrate store from a login/register API response.
     * Handles VP-native users (source:'vp') and BE-mirrored users (source:'be').
     */
    SET_USER(state, data) {
      state.email     = data.email       || null
      state.fullname  = data.fullName    || null
      state.phone     = data.phoneNumber || data.phone || null
      state.balance   = data.balance     || '0'
      state.user_id   = String(data.id   || '')
      state.user_type = String(data.userType || '0')
      state.logged_in = true

      localStorage.setItem('email',     state.email      || '')
      localStorage.setItem('fullname',  state.fullname   || '')
      localStorage.setItem('phone',     state.phone      || '')
      localStorage.setItem('balance',   state.balance    || '0')
      localStorage.setItem('user_id',   state.user_id    || '')
      localStorage.setItem('user_type', state.user_type  || '0')
      localStorage.setItem('logged_in', 'true')
    },

    SET_BUSINESS(state, data) {
      state.business_id   = String(data.id || '')
      state.account_type  = data.accountType || null
      state.business_name = data.name        || null

      localStorage.setItem('business_id',   state.business_id   || '')
      localStorage.setItem('account_type',  state.account_type  || '')
      localStorage.setItem('business_name', state.business_name || '')
    },

    CLEAR_BUSINESS(state) {
      state.business_id   = null
      state.account_type  = null
      state.business_name = null
      localStorage.removeItem('business_id')
      localStorage.removeItem('account_type')
      localStorage.removeItem('business_name')
    },

    UPDATE_BALANCE(state, balance) {
      state.balance = balance
      localStorage.setItem('balance', balance)
    },

    LOGOUT(state) {
      state.email         = null
      state.fullname      = null
      state.phone         = null
      state.balance       = '0'
      state.user_id       = null
      state.user_type     = null
      state.logged_in     = false
      state.business_id   = null
      state.account_type  = null
      state.business_name = null
      state.cart          = []
      state.product_cart  = []

      const keys = [
        'email','fullname','phone','balance','user_id','user_type',
        'logged_in','business_id','account_type','business_name',
      ]
      keys.forEach(k => localStorage.removeItem(k))
    },
  },

  actions: {
    /** Full login flow: call API, hydrate store, load business if applicable. */
    async login({ commit, dispatch }, { email, password }) {
      const res = await axios.post(
        (process.env.VUE_APP_API_URL || 'http://localhost:8001/').replace(/\/?$/, '/') + 'api/login',
        { email, password }
      )
      const data = res.data

      if (!data || data.length === 0) return { success: false }

      const user = data[0]
      commit('SET_USER', user)

      // Load business details in the background
      await dispatch('refreshBusiness')

      return { success: true, user }
    },

    /** Refresh user profile from API (safe to call on any page load). */
    async refreshUser({ state, commit }) {
      if (!state.email) return
      try {
        const res = await axios.post(state.api_url + 'api/user-details', { email: state.email })
        if (res.data && res.data.length > 0) {
          commit('SET_USER', res.data[0])
        }
      } catch (e) {
        console.warn('[store] refreshUser failed', e.message)
      }
    },

    /** Fetch business (shop) details for the logged-in user. */
    async refreshBusiness({ state, commit }) {
      if (!state.user_id) return
      try {
        const res = await axios.get(state.api_url + 'api/business-details/' + state.user_id)
        if (res.data && res.data.length > 0) {
          commit('SET_BUSINESS', res.data[0])
        } else {
          commit('CLEAR_BUSINESS')
        }
      } catch (e) {
        console.warn('[store] refreshBusiness failed', e.message)
      }
    },
  },

  modules: {},
})
