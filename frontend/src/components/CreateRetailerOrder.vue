<template>

    <div class="container">
    <Header />
    </div>
    
    
    <section style="padding-top: 30px;">
    <div class="container">
    <div>
    <h6> <i class="bi bi-plus icon"></i> Create order</h6>
    </div>
    <div class="row">
    
    <div class="col-md-3">
    <div style="padding-bottom: 10px;">
    <router-link to="/business-dashboard" class="btn theme-btn-1" style="width: 100%;"> <i class="bi bi-arrow-left"></i> Go back</router-link>
    
    </div>
    </div>
    
    <div class="col-md-9">
    
    <section>
    <div class="container" style="padding-bottom: 10px; text-align: right;">
    New orders are added to your list <router-link :to="{name:'outgoing-orders',params : { 'business_id' : business_id }}" class="btn theme-btn-1">Click to view</router-link>
    </div>
    </section>
    
    </div>
    
    
    
    </div>
    
    
    
    <div class="row">
    
    <div class="col-md-12">
    <div class="shadow-lg p-3 rounded">
    
    <form @submit.prevent="create_order()">
    <div class="row">
    
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <select class="form-select" aria-label="Floating label select example" v-model="supplier_id" style="text-transform: uppercase;" @change="supplier_products()">
    <option value="" selected>Choose a supplier to add</option>
    <option :value="sup.id" :key="sup.id" v-for="sup in suppliers">
        {{ sup.name }}
    </option>
    </select>
    <label for="floatingSelectGrid">Businesses</label>
    </div>
    </div>
    
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example" v-model="product_id" style="text-transform: uppercase;">
    <option value="" selected>Choose a product to add</option>
    <option :value="product.productId" v-for="product in products">
    {{ product.productName }}
    </option>
    </select>
    <label for="floatingSelectGrid">Businesses</label>
    </div>
    </div>
    
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <input type="number" min="1" class="form-control" id="floatingInput" placeholder="Bread" v-model="boxes">
    <label for="floatingInput">Number of boxes</label>
    </div>
    </div>
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <input type="number" min="1" class="form-control" id="floatingInput" placeholder="Bread" v-model="quantity_per_box">
    <label for="floatingInput">Quantity per box</label>
    </div>
    </div>
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <input type="date" class="form-control" id="floatingInput" placeholder="Bread" v-model="delivery_date">
    <label for="floatingInput">Delivery date</label>
    </div>
    </div>
    
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <input type="text" class="form-control" id="floatingInput" placeholder="Bread" v-model="delivery_location">
    <label for="floatingInput">Delivery location</label>
    </div>
    </div>
    
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <input type="text" class="form-control" id="floatingInput" placeholder="Bread" v-model="specifications">
    <label for="floatingInput">Specifications</label>
    </div>
    </div>
    
    <div class="col-md-6">
    <div class="form-floating mt-3">
    <input type="text" class="form-control" id="floatingInput" placeholder="Bread" v-model="instructions">
    <label for="floatingInput">Instructions</label>
    </div>
    </div>
    
    
    
    
    <div class="col-md-6 mt-3" style="text-align: left;">
    <button class="btn theme-btn-1">Create order</button>
    </div>
    
    
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
    import BusinessMenu from './layouts/BusinessMenu'
    import axios from 'axios'
    export default{
    name : 'home',
    components : {Header,Footer,BusinessMenu},
    data () {
    return { 
    business_id : '',
    supplier_id : '',
    suppliers : [],
    products : [],
    product_id : '',
    boxes : 1,
    quantity_per_box : 1,
    delivery_location : '',
    delivery_date : '',
    specifications : '',
    instructions : ''
    }
    },
    methods : {
    
    async create_order(){
    if(!this.supplier_id){
    this.$swal('Supplier cannot be blank')
    return
    }
    
    if(!this.product_id){
    this.$swal('Product cannot be blank')
    return
    }
    
    if(!this.boxes){
    this.$swal('Boxes cannot be blank')
    return
    }
    
    if(!this.quantity_per_box){
    this.$swal('Quantity cannot be blank')
    return
    }
    
    if(!this.delivery_date){
    this.$swal('Date cannot be blank')
    return
    }
    
    if(!this.delivery_location){
    this.$swal('Location cannot be blank')
    return
    }
    
    
    
    const res = await axios.post(this.$store.state.api_url+'api/create-retailer-order',{
    supplier_id : this.supplier_id,
    business_id : this.business_id,
    boxes : this.boxes,
    quantity_per_box : this.quantity_per_box,
    delivery_date : this.delivery_date,
    delivery_location : this.delivery_location,
    specifications : this.specifications,
    instructions : this.instructions,
    product_id : this.product_id,
    account_type : this.$store.state.account_type
    }).then(function(response){
    return response.data
    }).catch(function(error){
    console.log(error)
    })
    this.$swal(res)
    
    },
    
    async my_suppliers(){
    const res  = await axios.get(this.$store.state.api_url+'api/my-suppliers/'+this.business_id).then(function(response){
    return response.data
    }).catch(function(error){
    console.log(error)
    })
    this.suppliers = res
    },
    
    async supplier_products(){
    const res  = await axios.get(this.$store.state.api_url+'api/d-supplier-products/'+this.supplier_id).then(function(response){
    return response.data
    }).catch(function(error){
    console.log(error)
    })
    this.products = res
    }
    
    },
    created (){
    window.scrollTo({ top: 0, behavior: 'smooth' });
    this.business_id = this.$route.params.business_id
    this.my_suppliers()
    
    }
    }
    
    </script>
