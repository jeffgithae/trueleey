<template>
<nav class="header-style navbar navbar-expand-sm navbar-light shadow-lg">
  <div class="container-fluid">

    <router-link to="/" class="navbar-brand d-flex align-items-center gap-2">
      <img src="/assets/images/website-images/file.svg" class="logo-design" alt="">
      <img src="/assets/images/website-images/trueleey logos/trueleey logo.png" style="height:50px;" alt="Trueleey">
    </router-link>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <router-link to="/" class="nav-link">Home</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/manufacturers" class="nav-link">Manufacturers</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/distributors" class="nav-link">Distributors</router-link>
        </li>
        <li class="nav-item">
          <router-link to="/retailers" class="nav-link">Retailers</router-link>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <template v-if="$store.getters.isLoggedIn">
          <!-- Show business dashboard link if they have a shop -->
          <router-link
            v-if="$store.getters.hasShop"
            to="/business-dashboard"
            class="btn btn-sm btn-outline-secondary d-none d-md-inline-flex"
          >
            <i class="bi bi-bag me-1"></i>Business
          </router-link>
          <router-link to="/dashboard" class="btn theme-btn-1">
            <i class="bi bi-grid me-1"></i>Dashboard
          </router-link>
        </template>
        <template v-else>
          <router-link to="/login"    class="btn theme-btn-1">Login</router-link>
          <router-link to="/register" class="btn theme-btn-2">Register</router-link>
        </template>
      </div>
    </div>

  </div>
</nav>
</template>

<script>
export default {
  name: 'app-header',
}
</script>
