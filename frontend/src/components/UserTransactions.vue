<template>
  <div class="container">
    <Header />
  </div>

  <section style="padding-top:40px; padding-bottom:60px;">
    <div class="container">

      <div class="mb-3">
        <router-link to="/dashboard" class="btn btn-sm btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i>Back
        </router-link>
      </div>

      <h6 class="fw-bold mb-3">Transaction history</h6>

      <div v-if="loading" class="text-muted small">
        <span class="spinner-border spinner-border-sm me-2"></span>Loading…
      </div>

      <div v-else class="shadow-sm rounded p-3">
        <div v-if="transactions.length === 0" class="text-muted small text-center py-4">
          No transactions yet.
        </div>
        <div class="table-responsive" v-else>
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Order #</th>
                <th>Amount</th>
                <th>Comment</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in transactions" :key="t.id">
                <td>{{ t.orderId }}</td>
                <td><strong>Ksh. {{ t.amount }}</strong></td>
                <td>{{ t.comment }}</td>
                <td class="text-muted small">{{ t.created }}</td>
              </tr>
            </tbody>
          </table>
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
  name: 'user-transactions-page',
  components: { Header, Footer },

  data() {
    return {
      transactions: [],
      loading     : false,
    }
  },

  methods: {
    async load_transactions() {
      this.loading = true
      try {
        const res = await axios.get(
          this.$store.state.api_url + 'api/user-transactions/' + this.$store.state.user_id
        ).then(r => r.data)
        this.transactions = res || []
      } catch (e) {
        console.warn('load_transactions failed', e.message)
      } finally {
        this.loading = false
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    if (!this.$store.getters.isLoggedIn) {
      this.$router.push('/login')
      return
    }
    this.load_transactions()
  },
}
</script>
