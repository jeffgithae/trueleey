<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 50px;">



<div class="container">

<div class="row">

  <div class="col-md-4">
    <h6>Distributors</h6>
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

  <div v-for="d in distributors">
    <router-link :to="{ name : 'distributor-products', params :{ 'business_id' : d.id} }" class="btn shadow p-2 m-1 m-design"> <i class="bi bi-bag-check icon"></i> {{d.name}} - {{d.location}} </router-link> 
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
distributors : [],
search_text : ''
}
},

methods : {
async fetch_distributors(){

const res = await axios.get(this.$store.state.api_url+'api/fetch-distributors').then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.distributors = res
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
this.fetch_distributors()
}else{
this.distributors = res  
}

}

},
created (){
window.scrollTo({ top: 0, behavior: 'smooth' });
this.fetch_distributors()

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
