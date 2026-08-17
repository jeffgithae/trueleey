<template>
  <div class="container">
    <Header />
  </div>

  <div class="container mt-2">
    <router-link to="/dashboard" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-arrow-left me-1"></i>Back to dashboard
    </router-link>
  </div>

  <!-- Loading state while fetching business -->
  <div v-if="loading" class="container mt-5 text-center text-muted">
    <div class="spinner-border spinner-border-sm me-2"></div>Loading business…
  </div>

  <template v-else-if="business_id">
    <BusinessMenu :business_name="name" :business_id="business_id" />

    <section style="padding-top:10px; padding-bottom:60px;">
      <div class="container">
        <div class="row g-3">

          <!-- ── Batches card ─────────────────────────────────────── -->
          <div class="col-md-4">
            <div class="rounded p-4 shadow-sm h-100" style="background:#FEABB8;">
              <h6 class="fw-semibold mb-3">
                <i class="bi bi-boxes me-1"></i>Batches
              </h6>

              <router-link
                v-if="account_type === 'Retailer' || account_type === 'Salon'"
                :to="{ name:'received-batches', params:{ business_id } }"
                class="btn btn-sm btn-light w-100 mb-2"
              >
                <i class="bi bi-list icon me-1"></i>
                {{ account_type === 'Salon' ? 'Salon batches' : 'Retailer batches' }}
              </router-link>

              <router-link
                v-if="account_type === 'Distributor'"
                :to="{ name:'d-received-batches', params:{ business_id } }"
                class="btn btn-sm btn-light w-100 mb-2"
              >
                <i class="bi bi-list icon me-1"></i>Distributor batches
              </router-link>

              <router-link
                v-if="account_type === 'Manufacturer'"
                :to="{ name:'business-batches', params:{ business_id } }"
                class="btn btn-sm btn-light w-100 mb-2"
              >
                <i class="bi bi-list me-1"></i>List batches
              </router-link>

              <!-- New batch (Manufacturer only) -->
              <router-link
                v-if="account_type === 'Manufacturer'"
                :to="{ name:'new-batch', params:{ business_id } }"
                class="btn btn-sm btn-outline-dark w-100"
              >
                <i class="bi bi-plus me-1"></i>New batch
              </router-link>
            </div>
          </div>

          <!-- ── Suppliers card (non-manufacturer) ─────────────────── -->
          <div
            class="col-md-4"
            v-if="account_type === 'Retailer' || account_type === 'Distributor' || account_type === 'Salon'"
          >
            <div class="rounded p-4 shadow-sm h-100" style="background:#EAEAEA;">
              <h6 class="fw-semibold mb-3">
                <i class="bi bi-truck me-1"></i>Suppliers
              </h6>
              <router-link :to="{ name:'new-supplier', params:{ business_id } }" class="btn btn-sm btn-light w-100 mb-2">
                <i class="bi bi-plus icon me-1"></i>Add supplier
              </router-link>
              <router-link :to="{ name:'my-suppliers', params:{ business_id } }" class="btn btn-sm btn-light w-100">
                <i class="bi bi-list icon me-1"></i>My suppliers
              </router-link>
            </div>
          </div>

          <!-- ── Sales card (Retailer / Salon) ──────────────────────── -->
          <div class="col-md-4" v-if="account_type === 'Retailer' || account_type === 'Salon'">
            <div class="rounded p-4 shadow-sm h-100" style="background:#E6FDFB;">
              <h6 class="fw-semibold mb-3">
                <i class="bi bi-graph-up me-1"></i>Sales
              </h6>
              <router-link
                :to="{ name:'retailer-sales', params:{ business_id } }"
                class="btn btn-sm theme-btn-3 w-100 mb-2"
              >
                <i class="bi me-1" :class="account_type === 'Salon' ? 'bi-scissors' : 'bi-list'"></i>
                {{ account_type === 'Salon' ? 'Salon sales' : 'Retailer sales' }}
              </router-link>
              <router-link
                :to="{ name:'retailer-website-orders', params:{ business_id } }"
                class="btn btn-sm theme-btn-3 w-100"
              >
                <i class="bi bi-globe me-1"></i>Website orders
              </router-link>
            </div>
          </div>

          <!-- ── Salon: Beauty Express quick link ────────────────────── -->
          <div class="col-12" v-if="account_type === 'Salon'">
            <div class="rounded p-3 d-flex align-items-center gap-3"
                 style="background:#f5f0ff; border:1px solid #5C2A9D33;">
              <i class="bi bi-scissors fs-4 icon"></i>
              <div class="flex-grow-1">
                <strong>Beauty Express</strong>
                <p class="small text-muted mb-0">
                  Your salon is registered on Beauty Express. Manage bookings, clients and stylists there.
                </p>
              </div>
              <a href="/" class="btn btn-sm theme-btn-1" target="_blank" rel="noopener">
                Open Beauty Express <i class="bi bi-box-arrow-up-right ms-1"></i>
              </a>
            </div>
          </div>

        </div>
      </div>
    </section>
  </template>

  <Footer />
</template>

<script>
import Header       from './layouts/Header'
import Footer       from './layouts/Footer'
import BusinessMenu from './layouts/BusinessMenu'
import axios        from 'axios'

export default {
  name: 'business-dashboard-page',
  components: { Header, Footer, BusinessMenu },

  data() {
    return {
      name         : '',
      business_id  : '',
      account_type : '',
      loading      : true,
    }
  },

  methods: {
    async load_business() {
      this.loading = true

      // Use store data first (set during login / Dashboard load)
      if (this.$store.state.business_id) {
        this.business_id  = this.$store.state.business_id
        this.account_type = this.$store.state.account_type
        this.name         = this.$store.state.business_name
        this.loading      = false
        return
      }

      // Not in store — fetch from API (page refresh / deep link)
      try {
        const res = await axios.get(
          this.$store.state.api_url + 'api/business-details/' + this.$store.state.user_id
        ).then(r => r.data)

        if (res && res.length > 0) {
          const d = res[0]
          this.$store.commit('SET_BUSINESS', d)
          this.business_id  = d.id
          this.account_type = d.accountType
          this.name         = d.name
        } else if (this.$store.state.user_type === '1') {
          // Business account but no shop yet
          this.$router.push('/create-shop')
        } else {
          this.$store.state.message = 'This section is for business accounts only.'
          this.$router.push('/message')
        }
      } catch (e) {
        console.warn('load_business failed', e.message)
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
    this.load_business()
  },
}
</script>
