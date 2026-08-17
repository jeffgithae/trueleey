<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 30px;">
<div class="container">
<div>
<h6> <i class="bi bi-grid icon"></i>Add products</h6>
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
New products are added to your list <router-link :to="{name:'business-products',params : { 'business_id' : business_id }}" class="btn theme-btn-1">Click to view</router-link>
</div>
</section>

</div>



</div>



<div class="row">

<div class="col-md-12">
<div class="shadow-lg p-3 rounded">

<form @submit.prevent="new_product()">
<div class="row">
    <div class="col-md-6">
        <div class="form-floating mt-3">
<select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example" v-model="product_id">
<option value="" selected>Pick product to add</option>
<option :value="product.id" v-for="product in products">
{{ product.productName }}
</option>
</select>
<label for="floatingSelectGrid">Products</label>
</div>
    </div>


    <div class="col-md-6">
    <div class="form-floating mt-3">
  <input type="number" class="form-control" id="floatingInput" placeholder="Bread" v-model="selling_price" min="0">
  <label for="floatingInput">Selling price</label>
</div>
                            </div>



    <div class="col-md-12 mt-3" style="text-align: left;">
        <button class="btn theme-btn-1">Save product</button>
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
product_id : '',
products : [],
selling_price : ''

}
},
methods : {

async new_product(){
if(!this.product_id){
this.$swal('Product name cannot be blank')
return
}

if(!this.selling_price){
this.$swal('Selling price cannot be blank')
return
}

const res = await axios.post(this.$store.state.api_url+'api/add-retailer-product',{
product_id : this.product_id,
business_id : this.business_id,
selling_price : this.selling_price
}).then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.$swal(res)


},

async get_products(){
const res  = await axios.get(this.$store.state.api_url+'api/get-products-to-add').then(function(response){
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
this.get_products()

}
}

</script>
