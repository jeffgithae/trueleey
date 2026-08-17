<template>
  <div class="container">
    <Header />
  </div>

  <section class="auth-section">
    <div class="container">
      <div class="row g-0 shadow rounded overflow-hidden" style="min-height:560px;">

        <!-- Form side -->
        <div class="col-md-7 p-4 bg-white d-flex flex-column justify-content-center">

          <!-- Step indicator -->
          <div class="d-flex align-items-center gap-2 mb-4">
            <span class="step-dot" :class="{ active: step >= 1 }">1</span>
            <div class="step-line"></div>
            <span class="step-dot" :class="{ active: step >= 2 }">2</span>
            <div class="step-line"></div>
            <span class="step-dot" :class="{ active: step >= 3 }">3</span>
          </div>

          <div class="mb-3">
            <h4 class="fw-bold">
              <span v-if="step === 1">Choose account type</span>
              <span v-if="step === 2">Your details</span>
              <span v-if="step === 3">Set your password</span>
            </h4>
            <p class="text-muted small mb-0">
              <span v-if="step === 1">Are you joining as an individual or a business?</span>
              <span v-if="step === 2">Tell us a bit about yourself</span>
              <span v-if="step === 3">Choose a strong password to secure your account</span>
            </p>
          </div>

          <!-- Error banner -->
          <div class="alert alert-danger py-2 small" v-if="error_msg">
            <i class="bi bi-exclamation-circle me-1"></i>{{ error_msg }}
          </div>

          <!-- ── STEP 1: Account type ────────────────────────────── -->
          <div v-if="step === 1">
            <div class="row g-3">
              <div class="col-6">
                <button
                  type="button"
                  class="btn account-card w-100 h-100 p-3 text-start"
                  :class="{ selected: account_type === '0' }"
                  @click="account_type = '0'"
                >
                  <i class="bi bi-person-fill fs-3 d-block mb-2 icon"></i>
                  <strong>Personal</strong>
                  <p class="small text-muted mb-0 mt-1">Shop, send money &amp; access loans</p>
                </button>
              </div>
              <div class="col-6">
                <button
                  type="button"
                  class="btn account-card w-100 h-100 p-3 text-start"
                  :class="{ selected: account_type === '1' }"
                  @click="account_type = '1'"
                >
                  <i class="bi bi-building fs-3 d-block mb-2 icon"></i>
                  <strong>Business</strong>
                  <p class="small text-muted mb-0 mt-1">Manage products, orders &amp; supply chain</p>
                </button>
              </div>
            </div>

            <div class="mt-4 d-flex gap-2 align-items-center" v-if="account_type === '1'">
              <div class="form-group flex-grow-1">
                <label class="form-label small fw-semibold">Company / business name</label>
                <input
                  type="text"
                  class="form-control input-design"
                  placeholder="e.g. Acme Ltd"
                  v-model="company_name"
                />
              </div>
            </div>

            <button
              class="btn theme-btn-1 mt-4 w-100 py-2"
              @click="step1_next"
            >
              Continue <i class="bi bi-arrow-right ms-1"></i>
            </button>

            <p class="text-center mt-3 mb-0 small text-muted">
              Already have an account?
              <router-link to="/login" class="text-warning fw-semibold">Sign in</router-link>
            </p>
          </div>

          <!-- ── STEP 2: Personal details ────────────────────────── -->
          <div v-if="step === 2">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold">First name</label>
                <input type="text" class="form-control input-design" placeholder="First name" v-model="first_name" />
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">Last name</label>
                <input type="text" class="form-control input-design" placeholder="Last name" v-model="last_name" />
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-semibold">Email address</label>
                <div class="input-group">
                  <input
                    type="email"
                    class="form-control input-design"
                    :class="{ 'is-invalid': email_error, 'is-valid': email_valid }"
                    placeholder="you@example.com"
                    v-model="email"
                    @input="on_email_input"
                    @blur="check_email_exists"
                  />
                  <span v-if="email_checking" class="input-group-text text-muted">
                    <span class="spinner-border spinner-border-sm"></span>
                  </span>
                  <span v-else-if="email_valid" class="input-group-text text-success">
                    <i class="bi bi-check-lg"></i>
                  </span>
                </div>
                <div class="invalid-feedback d-block" v-if="email_error">{{ email_error }}</div>
              </div>

              <div class="col-md-6">
                <label class="form-label small fw-semibold">Phone number</label>
                <div class="input-group">
                  <span class="input-group-text">+254</span>
                  <input
                    type="text"
                    class="form-control input-design"
                    :class="{ 'is-invalid': phone_error, 'is-valid': phone_valid }"
                    placeholder="7XXXXXXXX"
                    v-model="phone_input"
                    @input="validate_phone"
                    maxlength="9"
                  />
                </div>
                <div class="invalid-feedback d-block" v-if="phone_error">{{ phone_error }}</div>
              </div>

              <div class="col-12">
                <label class="form-label small fw-semibold">Address / location</label>
                <input type="text" class="form-control input-design" placeholder="City or area" v-model="address" />
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-outline-secondary py-2 flex-shrink-0" @click="step = 1">
                <i class="bi bi-arrow-left"></i> Back
              </button>
              <button class="btn theme-btn-1 py-2 flex-grow-1" @click="step2_next">
                Continue <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- ── STEP 3: Password ─────────────────────────────────── -->
          <div v-if="step === 3">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                  <input
                    :type="show_password ? 'text' : 'password'"
                    class="form-control input-design"
                    :class="{ 'is-invalid': password_error, 'is-valid': password_valid }"
                    placeholder="Min 6 chars, 1 uppercase, 1 number"
                    v-model="password"
                    @input="validate_password"
                    autocomplete="new-password"
                  />
                  <button type="button" class="btn btn-outline-secondary" tabindex="-1" @click="show_password = !show_password">
                    <i :class="show_password ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
                  </button>
                </div>
                <div class="invalid-feedback d-block" v-if="password_error">{{ password_error }}</div>
              </div>

              <!-- Password strength bar -->
              <div class="col-12" v-if="password">
                <div class="d-flex gap-1 mt-1">
                  <div
                    v-for="n in 4"
                    :key="n"
                    class="flex-grow-1 rounded"
                    style="height:4px;"
                    :style="{ backgroundColor: n <= password_strength ? strength_color : '#e9ecef' }"
                  ></div>
                </div>
                <small :style="{ color: strength_color }">{{ strength_label }}</small>
              </div>

              <!-- Summary card -->
              <div class="col-12">
                <div class="border rounded p-3 bg-light small">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-person-circle icon"></i>
                    <strong>{{ first_name }} {{ last_name }}</strong>
                    <span class="badge ms-auto" :class="account_type === '1' ? 'bg-primary' : 'bg-secondary'">
                      {{ account_type === '1' ? 'Business' : 'Personal' }}
                    </span>
                  </div>
                  <div class="text-muted">{{ email }}</div>
                  <div class="text-muted" v-if="company_name">{{ company_name }}</div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button class="btn btn-outline-secondary py-2 flex-shrink-0" @click="step = 2" :disabled="loading">
                <i class="bi bi-arrow-left"></i> Back
              </button>
              <button class="btn theme-btn-1 py-2 flex-grow-1" @click="submit_register" :disabled="loading">
                <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                {{ loading ? 'Creating account…' : 'Create account' }}
              </button>
            </div>

            <p class="text-center mt-3 mb-0 small text-muted">
              By creating an account you agree to our terms of service.
            </p>
          </div>

        </div>

        <!-- Image side -->
        <div
          class="col-md-5 d-none d-md-block"
          style="background-image:url('/assets/images/website-images/image-8.jpg');
                 background-size:cover; background-position:center;"
        >
          <div class="h-100 d-flex flex-column justify-content-end p-4"
               style="background:rgba(0,0,0,0.45);">
            <h5 class="text-white fw-bold">Join Trueleey</h5>
            <p class="text-white-50 small mb-0">
              Your account works across both Trueleey and Beauty Express — one login, both platforms.
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
  name: 'register-page',
  components: { Header, Footer },

  data() {
    return {
      step          : 1,
      // Step 1
      account_type  : '',
      company_name  : '',
      // Step 2
      first_name    : '',
      last_name     : '',
      email         : '',
      email_error   : '',
      email_valid   : false,
      email_checking: false,
      email_timer   : null,
      phone_input   : '',
      phone_error   : '',
      phone_valid   : false,
      address       : '',
      // Step 3
      password      : '',
      password_error: '',
      password_valid: false,
      show_password : false,
      // UI state
      loading   : false,
      error_msg : '',
    }
  },

  computed: {
    password_strength() {
      const p = this.password
      if (!p) return 0
      let score = 0
      if (p.length >= 6)  score++
      if (p.length >= 10) score++
      if (/[A-Z]/.test(p) && /[0-9]/.test(p)) score++
      if (/[^A-Za-z0-9]/.test(p)) score++
      return score
    },
    strength_color() {
      return ['#e9ecef','#dc3545','#fd7e14','#ffc107','#28a745'][this.password_strength]
    },
    strength_label() {
      return ['','Weak','Fair','Good','Strong'][this.password_strength]
    },
  },

  methods: {
    // ── Step 1 ────────────────────────────────────────────────────
    step1_next() {
      this.error_msg = ''
      if (!this.account_type) {
        this.error_msg = 'Please select an account type.'
        return
      }
      if (this.account_type === '1' && !this.company_name.trim()) {
        this.error_msg = 'Please enter your company name.'
        return
      }
      this.step = 2
    },

    // ── Step 2 ────────────────────────────────────────────────────
    validate_email_format() {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      const em = this.email.trim()
      if (!em)          { this.email_error = ''; this.email_valid = false; return false }
      if (!re.test(em)) { this.email_error = 'Enter a valid email address'; this.email_valid = false; return false }
      return true
    },

    on_email_input() {
      this.email_valid = false
      clearTimeout(this.email_timer)
      if (!this.validate_email_format()) { this.email_checking = false; return }
      this.email_error   = ''
      this.email_checking = true
      this.email_timer = setTimeout(() => this.check_email_exists(), 500)
    },

    async check_email_exists() {
      clearTimeout(this.email_timer)
      if (!this.validate_email_format()) { this.email_checking = false; return }
      this.email_checking = true
      this.email_error    = ''
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/check-email',
          { email: this.email.trim().toLowerCase() }
        ).then(r => r.data)

        if (res === 'available') {
          this.email_error = ''
          this.email_valid = true
        } else {
          this.email_error = res
          this.email_valid = false
        }
      } catch {
        this.email_error = 'Unable to verify email. Please try again.'
        this.email_valid = false
      } finally {
        this.email_checking = false
      }
    },

    validate_phone() {
      const p = this.phone_input.replace(/\D/g, '')
      if (!p)              { this.phone_error = '';                              this.phone_valid = false }
      else if (p.length !== 9) { this.phone_error = 'Enter exactly 9 digits after +254'; this.phone_valid = false }
      else                 { this.phone_error = '';                              this.phone_valid = true  }
    },

    async step2_next() {
      this.error_msg = ''
      if (!this.first_name.trim()) { this.error_msg = 'First name is required.'; return }
      if (!this.last_name.trim())  { this.error_msg = 'Last name is required.'; return }
      if (this.email_checking)     { this.error_msg = 'Checking email…'; return }
      if (!this.email_valid)       { this.error_msg = this.email_error || 'Enter a valid email address.'; return }
      this.validate_phone()
      if (!this.phone_valid)       { this.error_msg = this.phone_error || 'Phone number is required.'; return }
      if (!this.address.trim())    { this.error_msg = 'Address / location is required.'; return }

      // Re-check email one more time before advancing
      await this.check_email_exists()
      if (!this.email_valid) { this.error_msg = this.email_error; return }

      this.step = 3
    },

    // ── Step 3 ────────────────────────────────────────────────────
    validate_password() {
      const p = this.password
      if (p.length < 6)         { this.password_error = 'At least 6 characters required'; this.password_valid = false }
      else if (!/[A-Z]/.test(p)) { this.password_error = 'Include at least one uppercase letter'; this.password_valid = false }
      else if (!/[0-9]/.test(p)) { this.password_error = 'Include at least one number'; this.password_valid = false }
      else                       { this.password_error = ''; this.password_valid = true }
    },

    async submit_register() {
      this.error_msg = ''
      this.validate_password()
      if (!this.password_valid) { this.error_msg = this.password_error; return }

      this.loading = true
      try {
        const res = await axios.post(
          this.$store.state.api_url + 'api/register',
          {
            email        : this.email.trim().toLowerCase(),
            password     : this.password,
            first_name   : this.first_name.trim(),
            last_name    : this.last_name.trim(),
            address      : this.address.trim(),
            company_name : this.company_name.trim(),
            account_type : this.account_type,
            phone        : '254' + this.phone_input.replace(/\D/g, ''),
          }
        ).then(r => r.data)

        if (res === 'Account created successfully' || res === 'Account created suuccessfully') {
          this.$swal({
            icon : 'success',
            title: 'Account created!',
            text : 'Your account is ready. Please log in.',
          }).then(() => this.$router.push('/login'))
        } else {
          this.error_msg = res || 'Registration failed. Please try again.'
        }
      } catch (e) {
        this.error_msg = 'Registration failed. Please try again.'
      } finally {
        this.loading = false
      }
    },
  },

  created() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
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

.step-dot {
  width: 28px; height: 28px;
  border-radius: 50%;
  background: #e9ecef;
  color: #adb5bd;
  font-size: 12px;
  font-weight: 700;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: background 0.2s, color 0.2s;
}
.step-dot.active {
  background: #5C2A9D;
  color: #fff;
}
.step-line {
  flex: 1;
  height: 2px;
  background: #e9ecef;
}

.account-card {
  border: 2px solid #e9ecef;
  border-radius: 12px;
  transition: border-color 0.15s, background 0.15s;
}
.account-card:hover {
  border-color: #5C2A9D;
}
.account-card.selected {
  border-color: #5C2A9D;
  background: #f5f0ff;
}
</style>
