<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 50px;">



<div class="container">

<div class="row">

  <div class="col-md-4">
    <h6>Manufacturers</h6>
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


  <div v-for="manufacturer in manufacturers">
    <router-link :to="{ name : 'manufacturer-products', params :{ 'business_id' : manufacturer.id} }" class="btn shadow p-2 m-1 m-design"> <i class="bi bi-bag-check icon"></i> {{manufacturer.name}} - {{manufacturer.location}} </router-link> 
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
manufacturers : [],
current_page : 1,
search_text : ''
}
},

methods : {
async fetch_manufacturers(){

const res = await axios.get(this.$store.state.api_url+'api/fetch-manufacturers').then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.manufacturers = res
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
this.fetch_manufacturers()
}else{
this.manufacturers = res  
}

}

},
created (){
window.scrollTo({ top: 0, behavior: 'smooth' });
this.fetch_manufacturers()

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
