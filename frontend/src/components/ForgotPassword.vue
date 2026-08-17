<template>
  <div class="container">
    <Header />
  </div>

  <section class="auth-section">
    <div class="container">
      <div class="row g-0 shadow rounded overflow-hidden" style="min-height:360px;">

        <div class="col-md-6 p-4 bg-white d-flex flex-column justify-content-center">
          <div class="mb-4">
            <h4 class="fw-bold">Reset your password</h4>
            <p class="text-muted small mb-0">
              Enter your email and we'll send you a temporary password.
            </p>
          </div>

          <!-- Success state -->
          <div v-if="sent" class="alert alert-success py-3 text-center">
            <i class="bi bi-envelope-check fs-3 d-block mb-2"></i>
            <strong>Email sent!</strong>
            <p class="small mb-2">Check your inbox for a temporary password, then log in and change it.</p>
            <router-link to="/login" class="btn theme-btn-1 btn-sm">Go to login</router-link>
          </div>

          <form v-else @submit.prevent="reset_password">

            <div class="form-group mb-4">
              <label class="form-label small fw-semibold">Email address</label>
              <input
                type="email"
                class="form-control input-design"
                placeholder="you@example.com"
                v-model="email"
                autocomplete="email"
                :disabled="loading"
              />
            </div>

            <button class="btn theme-btn-1 w-100 py-2" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
              {{ loading ? 'Sending…' : 'Send temporary password' }}
            </button>

            <div class="d-flex justify-content-center gap-3 mt-3">
              <router-link to="/login"    class="small text-warning">Back to login</router-link>
              <router-link to="/register" class="small text-muted">Create account</router-link>
            </div>

          </form>
        </div>

        <div
          class="col-md-6 d-none d-md-block"
          style="background-image:url('/assets/images/website-images/image-8.jpg');
                 background-size:cover; background-position:center;"
        >
          <div class="h-100 d-flex flex-column justify-content-end p-4"
               style="background:rgba(0,0,0,0.45);">
            <p class="text-white-50 small mb-0">
              Your password works across Trueleey and Beauty Express — resetting it updates both.
            </p>
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
  name: 'forgot-password-page',
  components: { Header, Footer },

  data() {
    return {
      email  : '',
      loading: false,
      sent   : false,
    }
  },

  methods: {
    async reset_password() {
      if (!this.email.trim()) {
        this.$swal('Please enter your email address.')
        return
      }

      this.loading = true
      try {
        await axios.post(this.$store.state.api_url + 'api/forgot-password', {
          email: this.email.trim().toLowerCase(),
        })
        // Always show success to avoid email enumeration
        this.sent = true
      } catch (e) {
        this.$swal('Something went wrong. Please try again.')
      } finally {
        this.loading = false
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  },
}
</script>

<style scoped>
.auth-section {
  padding-top: 60px;
  padding-bottom: 60px;
}
</style>
