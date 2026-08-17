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

          <div class="shadow rounded p-4">
            <h5 class="fw-bold mb-1">Send Money</h5>
            <p class="text-muted small mb-3">Transfer funds to any Trueleey account instantly.</p>

            <div class="rounded p-3 mb-4 text-center" style="background:#f0fdf4; border:1px solid #86efac;">
              <small class="text-muted d-block">Your balance</small>
              <strong class="text-success fs-5">Ksh. {{ $store.state.balance }}</strong>
            </div>

            <!-- Feedback -->
            <div class="alert alert-success py-2 small" v-if="success_msg">
              <i class="bi bi-check-circle me-1"></i>{{ success_msg }}
            </div>
            <div class="alert alert-danger py-2 small" v-if="error_msg">
              <i class="bi bi-exclamation-circle me-1"></i>{{ error_msg }}
            </div>

            <form @submit.prevent="send_money">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Recipient email</label>
                <input
                  type="email"
                  class="form-control input-design"
                  placeholder="recipient@example.com"
                  v-model="receiver_email"
                  :disabled="loading"
                />
              </div>

              <div class="mb-4">
                <label class="form-label small fw-semibold">Amount (Ksh.)</label>
                <input
                  type="number"
                  min="1"
                  class="form-control input-design"
                  placeholder="0"
                  v-model="amount"
                  :disabled="loading"
                />
              </div>

              <button class="btn theme-btn-1 w-100 py-2" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Sending…' : 'Send money' }}
              </button>
            </form>
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
import axios  from 'axios'

export default {
  name: 'send-money-page',
  components: { Header, Footer },

  data() {
    return {
      receiver_email: '',
      amount        : '',
      loading       : false,
      success_msg   : '',
      error_msg     : '',
    }
  },

  methods: {
    async send_money() {
      this.success_msg = ''
      this.error_msg   = ''

      if (!this.receiver_email.trim()) { this.error_msg = 'Recipient email is required.'; return }
      if (this.receiver_email.toLowerCase() === this.$store.state.email?.toLowerCase()) {
        this.error_msg = 'You cannot send money to yourself.'
        return
      }
      if (!this.amount || this.amount < 1) { this.error_msg = 'Enter a valid amount.'; return }

      this.loading = true
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/send-money',
          {
            receiver : this.receiver_email.trim().toLowerCase(),
            amount   : this.amount,
            sender   : this.$store.state.email,
          }
        ).then(r => r.data)

        if (res === 'insufficient') {
          this.error_msg = 'Insufficient balance in your account.'
        } else if (res === 'no-email') {
          this.error_msg = 'No account found with that email address.'
        } else if (res === 'success') {
          this.success_msg = `Ksh.${this.amount} sent to ${this.receiver_email} successfully.`
          this.receiver_email = ''
          this.amount         = ''
          // Refresh balance in store
          await this.$store.dispatch('refreshUser')
        } else {
          this.error_msg = res || 'Something went wrong.'
        }
      } catch (e) {
        this.error_msg = 'Transfer failed. Please try again.'
      } finally {
        this.loading = false
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    if (!this.$store.getters.isLoggedIn) {
      this.$router.push('/login')
    }
  },
}
</script>
