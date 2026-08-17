<template>
  <div class="container">
    <Header />
  </div>

  <section class="auth-section">
    <div class="container">
      <div class="row g-0 shadow rounded overflow-hidden" style="min-height:480px;">

        <!-- Form side -->
        <div class="col-md-6 p-4 bg-white d-flex flex-column justify-content-center">
          <div class="mb-4">
            <h4 class="fw-bold">Welcome back</h4>
            <p class="text-muted mb-0">Log in to your Trueleey account</p>
          </div>

          <form @submit.prevent="login">

            <!-- Error banner -->
            <div class="alert alert-danger py-2 small" v-if="error_msg">
              <i class="bi bi-exclamation-circle me-1"></i>{{ error_msg }}
            </div>

            <div class="form-group mb-3">
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

            <div class="form-group mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label small fw-semibold mb-0">Password</label>
                <router-link to="/forgot-password" class="small text-warning">Forgot password?</router-link>
              </div>
              <div class="input-group">
                <input
                  :type="show_password ? 'text' : 'password'"
                  class="form-control input-design"
                  placeholder="••••••••"
                  v-model="password"
                  autocomplete="current-password"
                  :disabled="loading"
                />
                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  tabindex="-1"
                  @click="show_password = !show_password"
                >
                  <i :class="show_password ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                </button>
              </div>
            </div>

            <button class="btn theme-btn-1 w-100 py-2" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ loading ? 'Signing in…' : 'Sign in' }}
            </button>

            <p class="text-center mt-3 mb-0 small text-muted">
              Don't have an account?
              <router-link to="/register" class="text-warning fw-semibold">Create one</router-link>
            </p>

          </form>
        </div>

        <!-- Image side -->
        <div
          class="col-md-6 d-none d-md-block"
          style="background-image:url('/assets/images/website-images/image-8.jpg');
                 background-size:cover; background-position:center;"
        >
          <div class="h-100 d-flex flex-column justify-content-end p-4"
               style="background:rgba(0,0,0,0.45);">
            <h5 class="text-white fw-bold">Trueleey</h5>
            <p class="text-white-50 small mb-0">
              Manage your supply chain, verify products and make payments — all in one place.
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

export default {
  name: 'login-page',
  components: { Header, Footer },

  data() {
    return {
      email         : '',
      password      : '',
      show_password : false,
      loading       : false,
      error_msg     : '',
    }
  },

  methods: {
    async login() {
      this.error_msg = ''

      if (!this.email.trim()) {
        this.error_msg = 'Email is required.'
        return
      }
      if (!this.password) {
        this.error_msg = 'Password is required.'
        return
      }

      this.loading = true

      let result
      try {
        result = await this.$store.dispatch('login', {
          email    : this.email.trim().toLowerCase(),
          password : this.password,
        })
      } catch (err) {
        this.error_msg = 'Wrong email or password. Please try again.'
        this.loading = false
        return
      }

      this.loading = false

      if (!result.success) {
        this.error_msg = 'Wrong email or password. Please try again.'
        return
      }

      // Decide redirect — honour ?redirect param first
      const redirectTo = this.$route.query.redirect
      if (redirectTo) {
        this.$router.push(redirectTo)
      } else if (this.$store.state.user_type === '1' && this.$store.getters.hasShop) {
        this.$router.push('/business-dashboard')
      } else {
        this.$router.push('/dashboard')
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    // Already logged in — send to dashboard
    if (this.$store.getters.isLoggedIn) {
      this.$router.push('/dashboard')
    }
  },
}
</script>

<style scoped>
.auth-section {
  padding-top: 60px;
  padding-bottom: 60px;
}
</style>
