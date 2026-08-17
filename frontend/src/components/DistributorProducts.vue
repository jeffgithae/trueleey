<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 50px;">



<div class="container">

  <div>
    <router-link to="/distributors" class="btn"> <i class="bi bi-arrow-left"></i> Go back</router-link>
  </div>

<h6>Manufacturer products</h6>

<div class="shadow p-3">

<table class="table">

  <thead>
    <tr> <th>Image</th> <th> Product name </th>  <th>Selling price</th> <th>Description</th>  </tr>
  </thead>

  <tbody v-for="p in products">
    <tr> <td style="text-align:left;"> <img :src="this.$store.state.api_url+'assets/product-images/'+p.image" style="max-height:50px;" class="shadow rounded" v-if="p.productPicture!=''"> </td> <td> {{p.product_name}} </td> <td> Ksh.{{p.selling_price}} </td>  <td> {{p.description}} </td> </tr>
  </tbody>
  
</table>
<p v-if="!products.length">No records found </p>

    
</div>


</div>

</section>




<Footer />
</template>

<script>
import Header from './layouts/Header'
import Footer from './layouts/Footer'
import axios from 'axios'
export default{
name : 'manufacturer-products',
components : {Header,Footer},
data () {
return { 
products : [],
business_id : ''
}
},

methods : {
async fetch_distributor_products(){

const res = await axios.get(this.$store.state.api_url+'api/fetch-distributor-products/'+this.business_id).then(function(response){
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
this.fetch_distributor_products()
}
}

</script>


<style scoped>
    .m-design{
        width: 100%;
        text-align: left;
        border-left: solid 1px #65F8E4;
    }
</style>
