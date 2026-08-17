<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 30px;">
<div class="container">
<div>
<h6> <i class="bi bi-grid icon"></i> Received batches</h6>
</div>
<div class="row">

<div class="col-md-3">
<div style="padding-bottom: 10px;">
<router-link to="/business-dashboard" class="btn theme-btn-1" style="width: 100%;"> <i class="bi bi-arrow-left"></i> Go back</router-link>

</div>
</div>

<div class="col-md-9">


</div>



</div>



<div class="row">

<div class="col-md-12">
<div class="shadow-lg p-3 rounded table-responsive">

<table class="table">
<thead>
<tr> <th> Batch name  </th> <th>Batch code</th> <th>Boxes</th> <th>Product name</th> <th>Price</th> <th>Quantity per box</th> <th>Received</th> <th>Action...</th> </tr>
</thead>

<tbody v-for="batch in batches">
<tr> <td> {{ batch.batch_name }} </td> <td> {{ batch.batch_code.slice(0,6) }}****** </td> <td>{{ batch.boxes }}</td> <td>{{ batch.product_name }}</td> <td>Ksh.{{ batch.price}}</td> <td> {{ batch.quantity_per_box }} </td> <td>

    <div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" :disabled="batch.received=='Yes'" :checked="batch.received=='Yes'" @change="mark_batch_received(batch.batch_code,batch.order_id)">
  <label class="form-check-label" for="flexSwitchCheckChecked"> {{batch.received}} </label>
</div>

 </td> <td> <button class="btn" @click="batch_boxes(batch.batch_code,batch.price)"> <i class="bi bi-three-dots"></i> </button> </td> </tr>
</tbody>

</table>

</div>
</div>

</div>

</div>
</section>



<div style="position: fixed; top: 0; background-color: #fff; right: 0;" class="shadow-lg p-3 m-3 rounded" v-if="show_modal">

<h6> <i class="bi bi-plus icon"></i> Add batch to shelf</h6>
<small class="text-danger">Once a batch has been added to shelf,action cannot be undone</small>
<form @submit.prevent="add_to_shelf()">

<div class="row">

<div class="col-md-12">

<p class="alert alert-warning mt-3" v-if="disabled"> <i class="bi bi-check"></i> This batch is added to shelf</p>

</div> 


<div class="col-md-12">
<div class="mt-3">
<button type="button" class="btn theme-btn-2" style="margin-right: 2px;" @click="show_modal=false">Close</button>
<button class="btn theme-btn-1" :disabled="disabled">Add to shelf</button>
</div>
</div>


</div>

</form>

</div>




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
batches : [],
boxes : [],
selected_batch : '',
show_modal : false,
price : '',
disabled : false

}
},
methods : {

async received_batches(){
const res  = await axios.get(this.$store.state.api_url+'api/received-batches/'+this.business_id).then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.batches = res
},

async batch_boxes(batch_code,price){
    this.selected_batch = batch_code
    this.price = price
    
    if(await this.check_batch(batch_code)==1){
        this.disabled = true
    }else{
        this.disabled = false
    }
    this.show_modal = true
},

async add_to_shelf(){
    
    const res = await axios.post(this.$store.state.api_url+'api/add-batch-to-shelf',{
        batch_code : this.selected_batch,
        selling_price : 0,
        price : this.price
    }).then(function(response){
        return response.data
    }).catch(function(error){
        console.log(error)
    })
    this.$swal('Batch added to shelf')
    this.disabled = true
},

async check_batch(batch_code){
    const res = await axios.get(this.$store.state.api_url+'api/check-batch/'+batch_code).then(function(response){
        return response.data
    }).catch(function(error){
        console.log(error)
    })
    
    return res;
},

 async mark_batch_received(batch_code){
            const res  = await axios.post(this.$store.state.api_url+'api/mark-batch-received',{
                batch_code : batch_code,
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.received_batches()

        }


},
created (){
window.scrollTo({ top: 0, behavior: 'smooth' });
this.business_id = this.$route.params.business_id
this.received_batches()

},
mounted () {

        if(this.$store.state.account_type!='Retailer' && this.$store.state.account_type!='Salon'){
        this.$store.state.message = 'Forbidden access.Only retailers allowed'
        this.$router.push('/message')
    }
    
    }
}

</script>
