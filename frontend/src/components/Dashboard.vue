<template>
  <div class="container">
    <Header />
  </div>

  <section style="padding-top:50px; padding-bottom:60px;">
    <div class="container">

      <!-- Welcome row -->
      <div class="row align-items-center mb-4">
        <div class="col">
          <h5 class="fw-bold mb-0">
            <i class="bi bi-person-circle icon me-2"></i>
            Welcome, {{ $store.getters.userDisplay }}
          </h5>
          <small class="text-muted">
            {{ $store.state.user_type === '1' ? 'Business account' : 'Personal account' }}
          </small>
        </div>
        <div class="col-auto">
          <button class="btn btn-sm btn-outline-danger" @click="logout">
            <i class="bi bi-power me-1"></i> Logout
          </button>
        </div>
      </div>

      <div class="row g-3">

        <!-- ── Left column: main actions ─────────────────────────── -->
        <div class="col-md-4">

          <!-- Business account prompt -->
          <div v-if="$store.getters.isBusiness && !$store.getters.hasShop"
               class="alert alert-info d-flex align-items-start gap-2 mb-3">
            <i class="bi bi-info-circle-fill mt-1"></i>
            <div>
              <strong>Set up your shop</strong>
              <p class="mb-2 small">You have a business account but no shop yet.</p>
              <router-link to="/create-shop" class="btn btn-sm theme-btn-2">
                <i class="bi bi-plus me-1"></i>Create your shop
              </router-link>
            </div>
          </div>

          <!-- Quick links grid -->
          <div class="row g-2">
            <div class="col-6" v-if="$store.getters.isBusiness">
              <router-link to="/business-dashboard" class="btn menu-btn shadow w-100 py-3">
                <i class="bi bi-bag d-block mb-1 fs-5"></i>
                <small>Business</small>
              </router-link>
            </div>
            <div class="col-6">
              <router-link to="/wallet" class="btn menu-btn-3 shadow w-100 py-3">
                <i class="bi bi-wallet2 d-block mb-1 fs-5"></i>
                <small>Wallet</small>
              </router-link>
            </div>
            <div class="col-6">
              <router-link to="/shop" class="btn menu-btn shadow w-100 py-3">
                <i class="bi bi-cart d-block mb-1 fs-5"></i>
                <small>Shop</small>
              </router-link>
            </div>
            <div class="col-6">
              <router-link to="/loans" class="btn shadow w-100 py-3">
                <i class="bi bi-cash-stack d-block mb-1 fs-5"></i>
                <small>Loans</small>
              </router-link>
            </div>
          </div>
        </div>

        <!-- ── Middle column: transactions ──────────────────────── -->
        <div class="col-md-3">
          <div class="border rounded p-3 h-100">
            <h6 class="fw-semibold mb-3" style="color:#5C2A9D;">
              <i class="bi bi-receipt me-1"></i>Transactions
            </h6>

            <router-link to="/user-transactions" class="btn d-block text-start mb-1 small">
              <i class="bi bi-list icon me-1"></i> View all transactions
            </router-link>
            <router-link to="/send-money" class="btn d-block text-start mb-1 small">
              <i class="bi bi-send icon me-1"></i> Send money
            </router-link>
            <router-link to="/my-shopping-orders" class="btn d-block text-start mb-1 small">
              <i class="bi bi-cart-check icon me-1"></i> Shopping orders
            </router-link>

            <hr />

            <button class="btn d-block text-start small w-100" @click="show_balance = !show_balance">
              <i class="bi bi-eye icon me-1"></i>
              {{ show_balance ? 'Hide balance' : 'Show balance' }}
            </button>
            <transition name="fade">
              <div v-if="show_balance" class="mt-2 p-2 rounded text-center"
                   style="background:#f0fdf4; border:1px solid #86efac;">
                <small class="text-muted d-block">Current balance</small>
                <strong class="text-success">Ksh. {{ $store.state.balance }}</strong>
              </div>
            </transition>
          </div>
        </div>

        <!-- ── Right column: activity + settings ────────────────── -->
        <div class="col-md-5">
          <div class="border rounded p-3 h-100 d-flex flex-column">
            <h6 class="fw-semibold mb-3" style="color:#5C2A9D;">
              <i class="bi bi-bell me-1"></i>Recent activity
            </h6>

            <div class="flex-grow-1" style="max-height:160px; overflow-y:auto;">
              <div
                v-for="(m, i) in monies" :key="i"
                class="d-flex justify-content-between align-items-center py-1 border-bottom small"
              >
                <span>
                  <i class="bi bi-arrow-up-right text-danger me-1"></i>
                  Sent <strong>Ksh.{{ m.amount }}</strong> to {{ m.receiver }}
                </span>
                <span class="text-muted ms-2 text-nowrap" style="font-size:10px;">{{ m.created }}</span>
              </div>
              <p v-if="monies.length === 0" class="text-muted small mb-0">No recent activity</p>
            </div>

            <div class="mt-3 pt-2 border-top">
              <router-link to="/account" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-gear me-1"></i>Account settings
              </router-link>
            </div>
          </div>
        </div>

      </div>

      <!-- Banner -->
      <div class="row mt-4">
        <div class="col-12">
          <div
            class="rounded text-center py-5 shadow"
            style="background-image:url('/assets/images/website-images/image-8.jpg');
                   background-size:cover;background-color:rgba(0,0,0,0.65);
                   background-blend-mode:darken;"
          >
            <h6 style="color:#65F8E4;">Send money to any account for free</h6>
            <router-link to="/send-money" class="btn theme-btn-1 mt-2">Try it now</router-link>
          </div>
        </div>
      </div>

    </div>
  </section>

  <Footer />
</template>

<script>
import Header from './layouts/Header'
import Footer from './layouts/Footer'
import axios from 'axios'

export default {
  name: 'dashboard-page',
  components: { Header, Footer },

  data() {
    return {
      show_balance: false,
      monies      : [],
    }
  },

  methods: {
    async load_money_sent() {
      if (!this.$store.state.email) return
      try {
        const res = await axios.get(
          this.$store.state.api_url + 'api/money-sent/' + this.$store.state.email
        )
        this.monies = res.data || []
      } catch (e) {
        console.warn('money_sent fetch failed', e.message)
      }
    },

    logout() {
      this.$store.commit('LOGOUT')
      this.$router.push('/')
    },
  },

  async created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })

    if (!this.$store.getters.isLoggedIn) {
      this.$router.push('/login')
      return
    }

    // Refresh user data and business info in parallel
    await Promise.all([
      this.$store.dispatch('refreshUser'),
      this.$store.dispatch('refreshBusiness'),
    ])

    this.load_money_sent()
  },
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
