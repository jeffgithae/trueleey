<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 30px;">
<div class="container">
<div>
<h6> <i class="bi bi-hdd-stack icon"></i> New batch</h6>
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
New batches are added to your list <router-link :to="{name:'business-batches',params : { 'business_id' : business_id }}" class="btn theme-btn-1">Click to view</router-link>
</div>
</section>

</div>



</div>



<div class="row">

<div class="col-md-12">


<div class="shadow-lg p-3 rounded">

<form @submit.prevent="new_batch()">
<div class="row">


<div class="col-md-6">
<div class="form-floating mt-3">
<select class="form-select"  v-model="product_id" style="text-transform: uppercase;">
<option value="" selected>Choose a product to add</option>
<option :value="product.id" v-for="product in products">
{{ product.productName}}
</option>
</select>
<label>Products</label>
</div>
</div>


<div class="col-md-6">
<div class="form-floating mt-3">
<input type="text" class="form-control" v-model="batch_name">
<label>Batch name</label>
</div>
</div>

<div class="col-md-6">
<div class="form-floating mt-3">
<input type="number" min="1" class="form-control"  placeholder="Boxes" v-model="boxes">
<label>Number of boxes</label>
</div>
</div>

<div class="col-md-6">
<div class="form-floating mt-3">
<input type="number" min="1" class="form-control"  placeholder="Bread" v-model="quantity_per_box">
<label>Quantity per box</label>
</div>
</div>




<div class="col-md-6 mt-3" style="text-align: left;">
<button class="btn theme-btn-1">Create batch</button>
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
products : [],
product_id : '',
batch_name : '',
boxes : '',
quantity_per_box : ''

}
},
methods : {

async new_batch(){
if(!this.product_id){
this.$swal('Product cannot be blank')
return
}

if(!this.batch_name){
this.$swal('Batch name cannot be blank')
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


const res = await axios.post(this.$store.state.api_url+'api/new-batch',{
product_id : this.product_id,
business_id : this.business_id,
batch_name : this.batch_name,
boxes : this.boxes,
quantity : this.quantity_per_box
}).then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.$swal(res)

this.batch_name = ''
this.boxes = ''
this.quantity_per_box = ''
this.product_id = ''
},

async get_products(){
const res  = await axios.get(this.$store.state.api_url+'api/get-products/'+this.business_id).then(function(response){
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

},

mounted () {
        if(this.$store.state.account_type!='Manufacturer'){
        this.$store.state.message = 'Forbidden access.Only manufacturers allowed'
        this.$router.push('/message')
    }
    }
}

</script>
