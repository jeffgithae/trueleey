<template>

    <div class="container">
        <Header />
    </div>


<section style="padding-top:50px;">
    
    <div class="container">

        <div class="row">
           

            <div class="col-md-12">
                <div style="background-image:url('/assets/images/website-images/image-8.jpg');  background-size: cover; padding-top:20px; text-align:center; background-color: rgba(0, 0, 0, 0.5); background-blend-mode: darken; border-radius: 10px; background-position: center;">
                    
                <div style="padding-top:100px; padding-bottom: 100px;">
                    <h6 style="color:#65F8E4;">Scan to shop.</h6>
                    <button class="btn theme-btn-1 btn-lg" style="margin-right:10px;" @click="manage_camera()"> <i class="bi bi-camera"></i> </button>
                    <button class="btn theme-btn-2 btn-lg" style="margin-right:10px;" @click="show_cart()"> <i class="bi bi-cart-check"></i></button>
                    <router-link to="/dashboard" class="btn theme-btn-1 btn-lg"> <i class="bi bi-house"></i> </router-link>
                </div>
                

                </div>

            </div>
             
            
        </div>
        
    </div>

</section> 



  <div class="moda" v-if="show_camera">

                <div class="moda-content">
                     <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4" style="text-align:center;">
                         <qrcode-stream @detect="on_detect" class="camera" ></qrcode-stream> 

                         <div style="padding-top:20px;">
                             <button class="btn theme-btn-1" style="margin-right:10px;" @click="manage_camera()"> <i class="bi bi-x-circle"></i></button>
                         </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                </div>
                   
               </div>   


<div class="moda" v-if="cart_on">
    
    <div class="moda-content" style="background-color:#fff;">

           <h6> <i class="bi bi-cart-check icon"></i> Shopping cart</h6>
           Your current balance is : Ksh. <span>{{this.$store.state.balance}}</span>
       
       <table class="table">

        <thead>
            <tr>
                <th>Product name</th> <th>Price</th> <th>Action..</th>
            </tr>
        </thead>

        <tbody v-for="data in cart_data">
         
            <tr> <td> {{ data.product_name }} </td> <td> Ksh.{{ data.selling_price }} </td> <td> <button class="btn text-danger" @click="delete_item(data.product_code)"> <i class="bi bi-x-circle"></i> </button> </td> </tr>
            
            
        </tbody>
           
       </table>

    

       <div style="padding-top:20px;">
        <button class="btn theme-btn-2" @click="cart_on=false" style="margin-right:10px;">Close</button>
           <button class="btn theme-btn-1" @click="pay()">Pay</button>
       </div>

    </div>

</div>


    

<Footer />
</template>

<script>
import Header from './layouts/Header'
import Slider from './layouts/Slider'
import Footer from './layouts/Footer'
import axios from 'axios'
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'



export default{
    name : 'home',
    components : {Header,Slider,Footer,QrcodeStream,QrcodeDropZone,QrcodeCapture},
    data () {
        return { 
           show_camera : false,
           text : 'Scan to shop',
           cart : [],
           cart_on : false,
           cart_data : [],
           total_amount : 0
        }
    },
    methods : {

        on_detect(qr_code){
            qr_code.forEach(data=>{
                this.check_qr(data.rawValue)
            })
        },
        manage_camera(){
            this.show_camera = !this.show_camera
            this.text = 'Scanning...'
            if(this.show_camera==false){
                this.text = 'Scan to shop'
            }
        },

        show_cart(){
             if(this.$store.state.cart.length==0){
                this.$swal('Your cart is empty,scan to buy products')
                return
            }
            this.cart_on = !this.cart_on
            this.cart_details()
            this.cart_on = true
        },

        async cart_details(){
           
            const res = await axios.post(this.$store.state.api_url+'api/cart-details',{
                cart : this.$store.state.cart
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$store.state.cart_data = res
            this.cart_data = this.$store.state.cart_data
            
        },

         async check_qr(qr){
            const res = await axios.post(this.$store.state.api_url+'api/check-qr',{
                qr : qr
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
          if(res=='fail'){
            this.$swal('Product does not exist!')
          }else{
            this.cart.push(qr)
            localStorage.setItem('cart',this.cart) 
            this.$store.state.cart = localStorage.getItem('cart')
            this.$store.state.cart = this.cart
            this.cart_details()
           this.$swal('Product added to cart') 
          } 
        },

        async pay(){
            const res  = await axios.post(this.$store.state.api_url+'api/pay-shopping',{
                user_id : this.$store.state.user_id,
                cart : this.$store.state.cart
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)  
            })
            if(res=='success'){
                this.$store.state.cart = []
                localStorage.setItem('cart',[])
                this.cart_data = []
                this.cart_on = false
                this.user_details()
                this.$swal('Payment successful')
            }else{
                this.$swal('You have insufficient balance in your account')
            }
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

                this.$router.push('/dashboard')
              

            });
            
            
        },

    delete_item(product_code){
        this.$store.state.cart = this.$store.state.cart.filter((value)=>{
            return value != product_code
        }) 

         localStorage.setItem('cart',this.$store.state.cart) 
        this.cart = this.$store.state.cart
        this.cart_details()
    }
   
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

</script>



<style scoped>
    .camera{
     border: solid 1px #65F8E4; 
    }
</style>
