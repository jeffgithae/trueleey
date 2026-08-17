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

      <div class="row">
        <div class="col-md-7 col-lg-6">

          <div class="shadow rounded p-4">
            <h5 class="fw-bold mb-1">Create your shop</h5>
            <p class="text-muted small mb-4">
              Set up your business profile on Trueleey. This links directly with your Beauty Express account.
            </p>

            <!-- Error banner -->
            <div class="alert alert-danger py-2 small" v-if="error_msg">
              <i class="bi bi-exclamation-circle me-1"></i>{{ error_msg }}
            </div>

            <form @submit.prevent="create_shop">

              <div class="mb-3">
                <label class="form-label small fw-semibold">Shop / business type</label>
                <select class="form-select input-design" v-model="account_type">
                  <option value="" disabled>Select type…</option>
                  <option value="Manufacturer">Manufacturer</option>
                  <option value="Distributor">Distributor</option>
                  <option value="Retailer">Retailer</option>
                  <option value="Salon">Salon</option>
                </select>

                <!-- Contextual hint for Salon -->
                <div v-if="account_type === 'Salon'" class="mt-2 p-2 rounded small"
                     style="background:#f5f0ff; border:1px solid #5C2A9D33;">
                  <i class="bi bi-info-circle me-1 icon"></i>
                  Selecting <strong>Salon</strong> will also grant you Salon Admin access on
                  <strong>Beauty Express</strong> — letting you manage bookings and clients there.
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Shop name</label>
                <input
                  type="text"
                  class="form-control input-design"
                  placeholder="e.g. Zippy Collections"
                  v-model="company_name"
                  :disabled="loading"
                />
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Business email</label>
                  <input
                    type="email"
                    class="form-control input-design"
                    placeholder="shop@example.com"
                    v-model="email"
                    :disabled="loading"
                  />
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Location</label>
                  <input
                    type="text"
                    class="form-control input-design"
                    placeholder="City or area"
                    v-model="location"
                    :disabled="loading"
                  />
                </div>
              </div>

              <div class="mt-4">
                <button class="btn theme-btn-1 w-100 py-2" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ loading ? 'Creating shop…' : 'Create shop' }}
                </button>
              </div>

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
import axios from 'axios'

export default {
  name: 'create-shop-page',
  components: { Header, Footer },

  data() {
    return {
      account_type : '',
      company_name : '',
      email        : '',
      location     : '',
      loading      : false,
      error_msg    : '',
    }
  },

  methods: {
    async create_shop() {
      this.error_msg = ''

      if (!this.account_type)      { this.error_msg = 'Please select a shop type.'; return }
      if (!this.company_name.trim()) { this.error_msg = 'Shop name is required.'; return }
      if (!this.email.trim())        { this.error_msg = 'Business email is required.'; return }
      if (!this.location.trim())     { this.error_msg = 'Location is required.'; return }

      this.loading = true
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/create-shop',
          {
            email        : this.email.trim().toLowerCase(),
            location     : this.location.trim(),
            company_name : this.company_name.trim(),
            account_type : this.account_type,
            user_id      : this.$store.state.user_id,
          }
        ).then(r => r.data)

        if (res === 'Account created successfully' || res === 'Account created suuccessfully') {
          // Refresh business info in store then navigate
          await this.$store.dispatch('refreshBusiness')
          this.$swal({
            icon : 'success',
            title: 'Shop created!',
            text : `${this.company_name} is now live on Trueleey.`,
            timer: 1800,
            showConfirmButton: false,
          }).then(() => this.$router.push('/business-dashboard'))
        } else {
          this.error_msg = res || 'Something went wrong. Please try again.'
        }
      } catch (e) {
        this.error_msg = 'Could not create shop. Please try again.'
      } finally {
        this.loading = false
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
    // Pre-fill email from logged-in user
    if (this.$store.state.email) {
      this.email = this.$store.state.email
    }
  },
}
</script>
