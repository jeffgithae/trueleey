<template>
  <div class="container">
    <Header />
  </div>

  <section style="padding-top:50px; padding-bottom:60px;">
    <div class="container">

      <div class="mb-3">
        <router-link to="/dashboard" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Back to dashboard
        </router-link>
      </div>

      <div class="row g-4">

        <!-- ── Apply panel ────────────────────────────────────────── -->
        <div class="col-md-4">
          <div class="shadow rounded p-4">
            <h6 class="fw-bold mb-1">Apply for a loan</h6>
            <p class="text-muted small mb-3">Current limit: <strong>Ksh. 1,000</strong></p>

            <!-- Balance pill -->
            <div class="rounded p-2 mb-3 text-center small" style="background:#f0fdf4; border:1px solid #86efac;">
              Balance: <strong class="text-success">Ksh. {{ $store.state.balance }}</strong>
            </div>

            <div class="alert alert-success py-2 small" v-if="apply_success">
              <i class="bi bi-check-circle me-1"></i>{{ apply_success }}
            </div>
            <div class="alert alert-danger py-2 small" v-if="apply_error">
              <i class="bi bi-exclamation-circle me-1"></i>{{ apply_error }}
            </div>

            <form @submit.prevent="apply_loan">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Amount (Ksh.)</label>
                <input
                  type="number"
                  min="1"
                  max="1000"
                  class="form-control input-design"
                  placeholder="Max 1,000"
                  v-model="amount"
                  :disabled="applying"
                />
              </div>
              <button class="btn theme-btn-1 w-100 py-2" :disabled="applying">
                <span v-if="applying" class="spinner-border spinner-border-sm me-2"></span>
                {{ applying ? 'Requesting…' : 'Request loan' }}
              </button>
            </form>
          </div>
        </div>

        <!-- ── Loan history ────────────────────────────────────────── -->
        <div class="col-md-8">
          <h6 class="fw-bold mb-3">Your loan history</h6>

          <div v-if="loading" class="text-muted small">
            <span class="spinner-border spinner-border-sm me-2"></span>Loading…
          </div>

          <div v-else-if="loans.length === 0" class="text-muted small">
            No loan requests yet.
          </div>

          <div v-else>
            <div
              v-for="loan in loans"
              :key="loan.id"
              class="rounded border p-3 mb-2 d-flex flex-wrap align-items-center gap-3"
            >
              <!-- Amount + status -->
              <div class="flex-grow-1">
                <strong>Ksh. {{ loan.amount }}</strong>
                <span
                  class="badge ms-2"
                  :class="{
                    'bg-success' : loan.approved == 1 && loan.paid == 1,
                    'bg-warning text-dark': loan.approved == 1 && loan.paid == 0,
                    'bg-secondary': loan.approved == 0,
                  }"
                >
                  <span v-if="loan.approved == 0">Pending approval</span>
                  <span v-else-if="loan.paid == 0">Active — unpaid</span>
                  <span v-else>Paid</span>
                </span>
              </div>

              <!-- Dates -->
              <div class="small text-muted">
                <div>Requested: {{ loan.createdAt }}</div>
                <div v-if="loan.dueDate">Due: {{ loan.dueDate }}</div>
              </div>

              <!-- Pay button -->
              <button
                v-if="loan.approved == 1 && loan.paid == 0"
                class="btn btn-sm theme-btn-2"
                @click="pay_loan(loan.amount, loan.id)"
                :disabled="paying_id === loan.id"
              >
                <span v-if="paying_id === loan.id" class="spinner-border spinner-border-sm me-1"></span>
                Pay now
              </button>
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
import axios  from 'axios'

export default {
  name: 'loans-page',
  components: { Header, Footer },

  data() {
    return {
      loans        : [],
      amount       : '',
      loading      : false,
      applying     : false,
      paying_id    : null,
      apply_success: '',
      apply_error  : '',
    }
  },

  methods: {
    async load_loans() {
      this.loading = true
      try {
        const res = await axios.get(
          this.$store.state.api_url + 'api/my-loans/' + this.$store.state.user_id
        ).then(r => r.data)
        this.loans = res || []
      } catch (e) {
        console.warn('load_loans failed', e.message)
      } finally {
        this.loading = false
      }
    },

    async apply_loan() {
      this.apply_success = ''
      this.apply_error   = ''

      if (!this.amount || this.amount < 1)    { this.apply_error = 'Enter a valid amount.'; return }
      if (this.amount > 1000)                 { this.apply_error = 'Maximum loan amount is Ksh. 1,000.'; return }

      this.applying = true
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/apply-loan',
          { user_id: this.$store.state.user_id, amount: this.amount }
        ).then(r => r.data)

        this.apply_success = res || 'Loan request submitted.'
        this.amount = ''
        await this.load_loans()
      } catch (e) {
        this.apply_error = 'Request failed. Please try again.'
      } finally {
        this.applying = false
      }
    },

    async pay_loan(amount, loan_id) {
      if (!confirm(`Ksh.${amount} will be deducted from your balance. Proceed?`)) return

      this.paying_id = loan_id
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/pay-loan',
          { amount, user_id: this.$store.state.user_id, loan_id }
        ).then(r => r.data)

        this.$swal({ icon: 'success', title: res, timer: 1500, showConfirmButton: false })
        await this.load_loans()
        await this.$store.dispatch('refreshUser')   // refresh balance
      } catch (e) {
        this.$swal({ icon: 'error', title: 'Payment failed. Please try again.' })
      } finally {
        this.paying_id = null
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    if (!this.$store.getters.isLoggedIn) {
      this.$router.push('/login')
      return
    }
    this.load_loans()
  },
}
</script>
