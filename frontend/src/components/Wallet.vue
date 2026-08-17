<template>
  <div class="container">
    <Header />
  </div>

  <section style="padding-top:50px; padding-bottom:60px;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-5">

          <div class="mb-3">
            <router-link to="/dashboard" class="btn btn-sm btn-outline-secondary">
              <i class="bi bi-arrow-left me-1"></i>Back
            </router-link>
          </div>

          <div class="shadow rounded p-4 text-center">
            <i class="bi bi-wallet2 fs-1 icon mb-3 d-block"></i>
            <h5 class="fw-bold mb-1">My Wallet</h5>
            <p class="text-muted small mb-4">{{ $store.state.email }}</p>

            <div class="rounded p-4 mb-4" style="background:#f0fdf4; border:1px solid #86efac;">
              <small class="text-muted d-block mb-1">Current balance</small>
              <span class="fs-3 fw-bold text-success">Ksh. {{ $store.state.balance }}</span>
            </div>

            <div class="d-grid gap-2">
              <router-link to="/send-money" class="btn theme-btn-2 py-2">
                <i class="bi bi-send me-1"></i>Send money
              </router-link>
              <router-link to="/user-transactions" class="btn btn-outline-secondary py-2">
                <i class="bi bi-list me-1"></i>View transactions
              </router-link>
            </div>
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

export default {
  name: 'wallet-page',
  components: { Header, Footer },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    if (!this.$store.getters.isLoggedIn) {
      this.$router.push('/login')
    }
  },
}
</script>
