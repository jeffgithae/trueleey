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

            <h5 class="fw-bold mb-1">Account settings</h5>
            <p class="text-muted small mb-4">Update your profile. Changes sync across Trueleey and Beauty Express.</p>

            <!-- Feedback -->
            <div class="alert alert-success py-2 small" v-if="success_msg">
              <i class="bi bi-check-circle me-1"></i>{{ success_msg }}
            </div>
            <div class="alert alert-danger py-2 small" v-if="error_msg">
              <i class="bi bi-exclamation-circle me-1"></i>{{ error_msg }}
            </div>

            <form @submit.prevent="save_changes">
              <div class="row g-3">

                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Full name</label>
                  <input
                    type="text"
                    class="form-control input-design"
                    placeholder="Full name"
                    v-model="full_name"
                    :disabled="loading"
                  />
                </div>

                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Email</label>
                  <input
                    type="email"
                    class="form-control input-design bg-light"
                    :value="$store.state.email"
                    disabled
                  />
                  <small class="text-muted">Email cannot be changed</small>
                </div>

                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Phone number</label>
                  <input
                    type="text"
                    class="form-control input-design"
                    placeholder="Phone"
                    v-model="phone"
                    :disabled="loading"
                  />
                </div>

                <div class="col-md-6">
                  <label class="form-label small fw-semibold">Address / location</label>
                  <input
                    type="text"
                    class="form-control input-design"
                    placeholder="Address"
                    v-model="address"
                    :disabled="loading"
                  />
                </div>

                <!-- Password change section -->
                <div class="col-12">
                  <hr />
                  <p class="small fw-semibold mb-2">Change password <span class="text-muted fw-normal">(leave blank to keep current)</span></p>
                </div>

                <div class="col-md-6">
                  <label class="form-label small fw-semibold">New password</label>
                  <div class="input-group">
                    <input
                      :type="show_pw ? 'text' : 'password'"
                      class="form-control input-design"
                      placeholder="Min 6 chars"
                      v-model="password"
                      autocomplete="new-password"
                      :disabled="loading"
                    />
                    <button
                      type="button"
                      class="btn btn-outline-secondary"
                      tabindex="-1"
                      @click="show_pw = !show_pw"
                    >
                      <i :class="show_pw ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                    </button>
                  </div>
                </div>

              </div>

              <div class="mt-4">
                <button class="btn theme-btn-1 py-2 px-4" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                  {{ loading ? 'Saving…' : 'Save changes' }}
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
  name: 'account-page',
  components: { Header, Footer },

  data() {
    return {
      full_name  : '',
      phone      : '',
      address    : '',
      password   : '',
      show_pw    : false,
      loading    : false,
      success_msg: '',
      error_msg  : '',
    }
  },

  methods: {
    async load_profile() {
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/user-details',
          { email: this.$store.state.email }
        ).then(r => r.data)

        if (res && res.length > 0) {
          const d = res[0]
          this.full_name = d.fullName    || ''
          this.phone     = d.phoneNumber || ''
          this.address   = d.address     || ''
        }
      } catch (e) {
        console.warn('load_profile failed', e.message)
      }
    },

    async save_changes() {
      this.success_msg = ''
      this.error_msg   = ''

      if (!this.full_name.trim()) { this.error_msg = 'Full name is required.';  return }
      if (!this.phone.trim())     { this.error_msg = 'Phone number is required.'; return }
      if (!this.address.trim())   { this.error_msg = 'Address is required.';    return }

      if (this.password && this.password.length < 6) {
        this.error_msg = 'Password must be at least 6 characters.'
        return
      }

      this.loading = true
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/edit-account',
          {
            full_name : this.full_name.trim(),
            phone     : this.phone.trim(),
            address   : this.address.trim(),
            password  : this.password,
            email     : this.$store.state.email,
          }
        ).then(r => r.data)

        this.success_msg = res || 'Changes saved.'
        this.password    = ''

        // Refresh store so header / dashboard reflect new name
        await this.$store.dispatch('refreshUser')
      } catch (e) {
        this.error_msg = 'Failed to save changes. Please try again.'
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
    this.load_profile()
  },
}
</script>
