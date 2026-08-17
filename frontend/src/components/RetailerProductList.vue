<template>

<div class="container">
<Header />
</div>


<section style="padding-top: 50px;">



<div class="container">

  <div>
    <router-link to="/retailers" class="btn"> <i class="bi bi-arrow-left"></i> Go back</router-link>
  </div>

<h6>Retailer products</h6>

<div class="alert alert-warning row shadow-lg" v-if="this.$store.state.product_cart.length>0">
  <div class="col-md-8 col-6">{{this.$store.state.product_cart.length}} items in cart</div>
  <div class="col-md-4 col-6" style="text-align:right;" @click="view_cart()"> <button class="btn theme-btn-2">View cart</button> </div>
</div>

<div>

  <div class="row">
    <div class="col-md-2 col-6" v-for="p in products" style="text-align:center;">

      <div class="shadow p-3 rounded">
          <div>
        <img :src="this.$store.state.api_url+'assets/product-images/'+p.image" style="height:100px;"  v-if="p.image!=''">
      </div>

        <div>
       <h6>{{p.product_name}}</h6> 
       <p>{{p.description}}</p>
       <h6 class="text-dark"> Ksh.{{p.selling_price}} </h6>
      </div>
      <div>
        <hr>
        <button class="btn theme-btn-2" @click="add_to_cart(p.id)">Add to cart</button>
      </div>
        
      </div>

  
    </div>
    
  </div>


<p v-if="!products.length">No records found </p>

    
</div>


</div>

</section>

<div style="position:fixed; right:0; top:0; width:100%; background-color:#fff; height:100%;" v-if="show_modal">
  
  <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6" style="padding-top:20px;">

      <div class="row">
        <div class="col-md-6">
          <h6> <i class="bi bi-cart icon"></i> Cart</h6>
        </div>
        <div class="col-md-6" style="text-align:right;">
          <button class="btn theme-btn-2" @click="show_modal=false"> <i class="bi bi-x-lg"></i> </button>
        </div>
      </div>
      
      <table class="table">
        <tbody v-for="c in cart_data">
          <tr> <td> {{c.productName}} </td> <td> x 1 </td> <td> Ksh.{{c.sellingPrice}} </td> <td> <button class="btn text-danger" @click="delete_item(c.productId)"> <i class="bi bi-x-circle"></i> </button> </td> </tr>
        </tbody>
      </table>
      <p v-if="cart_data.length==0" class="text-danger">No items in the cart</p>
      <p>Total amount : Ksh.{{total_amount}}</p>

      <div>
        <button class="btn theme-btn-1" @click="clear_cart()">Clear cart</button>
        <button class="btn theme-btn-2" @click="pay()">Make order</button>
      </div>

    </div>
    <div class="col-md-3"></div>

  </div>

</div>


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
business_id : '',
product_cart : [],
show_modal : false,
total_amount : 0,
cart_data : []
}
},

methods : {

  async pay(){
            const res  = await axios.post(this.$store.state.api_url+'api/pay-website-order',{
                user_id : this.$store.state.user_id,
                cart : this.$store.state.product_cart
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)  
            })
            if(res=='success'){
                this.$store.state.product_cart = []
                localStorage.setItem('product_cart',[])
                this.cart_data = []
                this.total_amount = 0
                this.product_cart = []
                this.user_details()
                this.$swal('Payment successful')
                this.show_modal = false

            }else{
                this.$swal('You have insufficient balance in your account')
            }
        },

  clear_cart(){
    this.$store.state.product_cart = []
    this.product_cart = []
    this.view_cart()
  },

  delete_item(id){
    this.$store.state.product_cart.pop(id)
    this.view_cart()
  },

async fetch_retailer_products(){

const res = await axios.get(this.$store.state.api_url+'api/fetch-retailer-products/'+this.business_id).then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.products = res
},

add_to_cart(product_id){
this.product_cart.push(product_id)
localStorage.setItem('product_cart',this.product_cart) 
this.$store.state.product_cart = localStorage.getItem('product_cart')
this.$store.state.product_cart = this.product_cart
},

async view_cart(){
  const res = await axios.post(this.$store.state.api_url+'api/view-website-cart',{
    cart : this.$store.state.product_cart
  }).then(function(response){
    return response.data
  }).catch(function(error){
    console.log(error)
  })
  this.cart_data = res
  this.total_amount = 0
  res.forEach((data)=>{
    this.total_amount += data.sellingPrice
  })
  this.show_modal = true
},

async user_details(){
    
            const res = await axios.post(this.$store.state.api_url+'api/user-details',{
                email : this.$store.state.email,
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            
            res.forEach(data => {
                this.$store.state.email = data.email
                localStorage.setItem('email' ,data.email)
                this.$store.state.phone = data.phone
                localStorage.setItem('phone' ,data.phone)
                this.$store.state.balance = data.balance
                localStorage.setItem('balance', data.balance)
                this.$store.state.fullname = data.fullName
                localStorage.setItem('fullname', data.fullName)

                this.$store.state.user_id = data.id
                localStorage.setItem('user_id', data.id)

                this.$store.state.logged_in = true
                localStorage.setItem('logged_in', true)

                //this.$router.push('/dashboard')
              

            });
            
            
        }

},
created (){
window.scrollTo({ top: 0, behavior: 'smooth' });
this.business_id = this.$route.params.business_id
this.fetch_retailer_products()
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
