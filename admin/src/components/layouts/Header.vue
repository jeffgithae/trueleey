<template>
<nav class="header-style navbar navbar-expand-sm navbar-light shadow-lg">
  <div class="container-fluid">

    <router-link to="/dashboard" class="navbar-brand d-flex align-items-center gap-2">
      <img src="/assets/images/file.svg" class="logo-design" alt="">
      <img src="/assets/images/trueleey logo.png" style="height:50px;" alt="Trueleey">
    </router-link>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <router-link to="/dashboard" class="nav-link">Home</router-link>
        </li>
      
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="accountsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Accounts
          </a>
          <ul class="dropdown-menu" aria-labelledby="accountsDropdown">
            <li><router-link class="dropdown-item" to="/personal-accounts">Personal accounts</router-link></li>
            <li><router-link class="dropdown-item" to="/business-accounts">Business accounts</router-link></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="businessesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Businesses
          </a>
          <ul class="dropdown-menu" aria-labelledby="businessesDropdown">
            <li><router-link class="dropdown-item" to="/retailers">Retailers</router-link></li>
            <li><router-link class="dropdown-item" to="/distributors">Distributors</router-link></li>
            <li><router-link class="dropdown-item" to="/manufacturers">Manufacturers</router-link></li>
            <li><router-link class="dropdown-item" to="/salons">Salons</router-link></li>
          </ul>
        </li>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="loansDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Loans
          </a>
          <ul class="dropdown-menu" aria-labelledby="loansDropdown">
            <li><router-link class="dropdown-item" to="/loan-applications">Loan applications</router-link></li>
          </ul>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <button @click="logout()" class="btn theme-btn-1">
          <i class="bi bi-power me-1"></i>Log Out
        </button>
      </div>
    </div>

  </div>
</nav>
</template>



<script>
  
  export default {

    name : 'header',

    methods : {
      logout(){
        this.$router.push('/')
      }
    }
  }

</script>


