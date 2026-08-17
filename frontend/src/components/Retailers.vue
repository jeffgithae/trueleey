<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 50px;">



<div class="container">


<div class="row">

  <div class="col-md-4">
    <h6>Retailers</h6>
  </div>

  <div class="col-md-4"></div>

  <div class="col-md-4" style="text-align:right;">
  <div class="mb-3">
    <div class="input-group">
  <span class="input-group-text"> <i class="bi bi-search"></i> </span>
  <input type="search" class="form-control" placeholder="Search" @input="search_business()" v-model="search_text">
</div>
   
  </div>
  

  </div>
  
</div>

<div class="shadow p-3">

  <div v-for="r in retailers">
    <router-link :to="{ name : 'retailer-product-list', params :{ 'business_id' : r.id} }" class="btn shadow p-2 m-1 m-design"> <i class="bi bi-bag-check icon"></i> {{r.name}} - {{r.location}} </router-link> 
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
export default{
name : 'manufacturer',
components : {Header,Footer},
data () {
return { 
retailers : [],
search_text : ''
}
},

methods : {
async fetch_retailers(){

const res = await axios.get(this.$store.state.api_url+'api/fetch-retailers').then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.retailers = res
},

async search_business(){
const res = await axios.post(this.$store.state.api_url+'api/search-business',{
  search_text : this.search_text
}).then(function(response){
  return response.data
}).catch(function(error){
  console.log(error)
})
if(this.search_text==''){
this.fetch_retailers()
}else{
this.retailers = res  
}

}

},
created (){
window.scrollTo({ top: 0, behavior: 'smooth' });
this.fetch_retailers()

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
