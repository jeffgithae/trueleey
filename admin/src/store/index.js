import { createStore } from 'vuex'

export default createStore({
  state: {
    api_url   : (process.env.VUE_APP_API_URL || 'http://localhost:8001/').replace(/\/?$/, '/'),
    logged_in : localStorage.getItem('admin_logged_in') === 'true',
    username  : localStorage.getItem('admin_username') || null,
  },

  getters: {
    isLoggedIn: (state) => state.logged_in === true,
  },

  mutations: {
    LOGIN(state, username) {
      state.logged_in = true
      state.username  = username
      localStorage.setItem('admin_logged_in', 'true')
      localStorage.setItem('admin_username',   username)
    },
    LOGOUT(state) {
      state.logged_in = false
      state.username  = null
      localStorage.removeItem('admin_logged_in')
      localStorage.removeItem('admin_username')
    },
  },

  actions: {},
  modules: {},
})
