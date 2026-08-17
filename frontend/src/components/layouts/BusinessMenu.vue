<template>



<section style="padding-top: 20px;">

<div class="container">

    <div class="row">
        <div class="col-md-12 col-12">
            <small> <i class="bi bi-bag icon"></i> Business</small>
            <h6>{{ business_name }}</h6>
            <span class="badge bg-secondary">{{ this.$store.state.account_type }}</span>
        </div>

       
    </div>

</div>

</section>


    <section>

        <div class="container">

            <div class="row">


                <div class="col-md-2 col-6" style="padding-top: 15px;" v-if="this.$store.state.account_type=='Manufacturer' || this.$store.state.account_type=='Distributor' || this.$store.state.account_type=='Retailer' || this.$store.state.account_type=='Salon' ">
                    <div class="dropdown">
  <button class="btn theme-btn-2 dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" style="width: 100%;">
    <i class="bi bi-grid icon"></i> Products
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

    <li v-if="this.$store.state.account_type=='Manufacturer'"><router-link :to="{name:'new-product',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-plus"></i>  Add new product</router-link></li>

    <!-- Add distributor products -->
    <li v-if="this.$store.state.account_type=='Distributor'"><router-link :to="{name:'add-products',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-plus"></i>  Add  products</router-link></li>

    <!-- Add retailer products -->
    <li v-if="this.$store.state.account_type=='Retailer' || this.$store.state.account_type=='Salon'"><router-link :to="{name:'add-retailer-product',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-plus"></i> Add  products</router-link></li>

    <li v-if="this.$store.state.account_type=='Manufacturer'"><router-link :to="{name:'business-products',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> List products</router-link></li>
    
    <li v-if="this.$store.state.account_type=='Distributor'"><router-link :to="{name:'d-products',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> List products</router-link></li>

    <!-- list retailer products -->
     <li v-if="this.$store.state.account_type=='Retailer' || this.$store.state.account_type=='Salon'"><router-link :to="{name:'r-products',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> List products</router-link></li>
   
  </ul>
</div>
</div>

<div class="col-md-2 col-6" style="padding-top: 15px;">
                    <div class="dropdown">
  <button class="btn theme-btn-2 dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" style="width: 100%;">
    <i class="bi bi-card-checklist icon"></i> Orders
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">

    <li v-if="this.$store.state.account_type=='Retailer' || this.$store.state.account_type=='Distributor'  "><router-link :to="{name:'create-order',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-plus"></i> Create order to manufacturer </router-link></li>

    <li v-if="this.$store.state.account_type=='Retailer'"><router-link :to="{name:'create-retailer-order',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-plus"></i> Create order to distributor </router-link></li>

    <li v-if="this.$store.state.account_type=='Manufacturer'"><router-link :to="{name:'purchase-orders',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> Purchase orders</router-link></li>

    <li v-if="this.$store.state.account_type=='Distributor'"><router-link :to="{name:'retailer-orders',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> Orders from retailers</router-link></li>

    <li v-if="this.$store.state.account_type=='Retailer' || this.$store.state.account_type=='Distributor'  "><router-link :to="{name:'outgoing-orders',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> Outgoing orders to manufacturers</router-link></li>

    <li v-if="this.$store.state.account_type=='Retailer'"><router-link :to="{name:'retailer-outgoing-orders',params : { 'business_id' : business_id }}" class="dropdown-item">  <i class="bi bi-list"></i> Outgoing orders to distributors</router-link></li>

   
  </ul>
</div>
</div>





<div class="col-md-2 col-6" style="padding-top: 15px;">
                    <div class="dropdown">
  <button class="btn theme-btn-2 dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" style="width: 100%;">
    <i class="bi bi-cart-check icon"></i> Inventory
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
    <li v-if="this.$store.state.account_type=='Manufacturer'"><router-link :to="{name:'manufacturer-inventory',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-list-ol"></i> Manufacturer inventory</router-link></li>

    <li v-if="this.$store.state.account_type=='Distributor'"><router-link :to="{name:'distributor-inventory',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-list-ol"></i> Distributor inventory</router-link></li>

      <li v-if="this.$store.state.account_type=='Retailer'"><router-link :to="{name:'retailer-inventory',params : { 'business_id' : business_id }}" class="dropdown-item"> <i class="bi bi-list-ol"></i> Retailer inventory</router-link></li>
    
  </ul>
</div>
</div>



<div class="col-md-2 col-6" style="padding-top: 15px;">

    <router-link to="/dashboard" class="btn theme-btn-2" style="width: 100%;"> <i class="bi bi-house icon"></i> Dashboard</router-link>

</div>





            </div>

        </div>

    </section>


</template>


<script>

export default{
    props : {
        business_name:String,
        business_id:String
    }
}

</script>
