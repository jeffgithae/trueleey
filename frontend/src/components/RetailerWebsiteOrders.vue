<template>

    <div class="container">
        <Header />
    </div>

    
<section>
    
    <div class="container">

        <div style="padding-top:20px;">
            <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back </router-link>
            <h6>Retailer sales</h6>
        </div>

        <div class="shadow-lg rounded p-3">
           <div class="table-responsive-lg">

            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <input type="search" placeholder="Search" v-model="search_text" class="form-control input-design" @input="search_order()">
                </div>
                
            </div>

             <table class="table">

                <thead>
                    <tr> <th> Order# </th> <th>Total amount</th> <th>Date</th> <th>Buyer</th> <th>Action...</th> </tr>
                </thead>

                <tbody v-for="sale in sales">
                    <tr> <td> {{ sale.order_id }} </td> <td> Ksh.{{ sale.total_amount }} </td> <td> {{sale.date_bought}} </td> <td>{{sale.full_name}}</td> <td> <button class="btn" @click="order_details(sale.order_id,sale.buyer)"> <i class="bi bi-three-dots"></i> </button> </td>  </tr>
                </tbody>
                
            </table>

            <p v-if="sales.length==0">No records found</p>
               
           </div>
        </div>
        
    </div>

</section>

<div v-if="show_modal" style="position:fixed; top:0; right:0; width:100%; height: 100%; background-color: #fff; padding-top:20px;">
   
   <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
    <div class="row">
        <div class="col-md-6">
            <h6>Order details</h6>
        </div>
        <div class="col-md-6" style="text-align:right;">
        <button class="btn theme-btn-2" @click="show_modal=false"> <i class="bi bi-x"></i> </button>
        </div>
    </div>
    <table class="table">
        <thead>
            <tr> <th>Product name</th>  <th>Price</th> <th>Created</th> </tr>
        </thead>
        <tbody v-for="detail in details">
            <tr> <td> {{detail.product_name}} </td> <td> Ksh.{{detail.price}} </td> <td>{{detail.created}}</td> </tr>
        </tbody>
    </table>

    <div>
        <button class="btn text-danger" @click="delete_website_order(order_id)">Delete</button>
    </div>

    <div style="text-align:center;">
        <h6>Scan items</h6>
        <button class="btn btn-lg" @click="show_camera=true" v-if="!show_camera"> <i class="bi bi-camera-fill icon"></i> </button>
    </div>


    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">
             <div v-if="show_camera" style="text-align:center;">
             <qrcode-stream @detect="on_detect"></qrcode-stream>
             <button class="btn text-danger" @click="show_camera=false"> <i class="bi bi-x-circle"></i> </button>
         </div>
        </div>
        <div class="col-md-4"></div>
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
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'
export default{
    name : 'home',
    components : {Header,Footer,QrcodeStream,QrcodeDropZone,QrcodeCapture},
    data () {
        return { 
           sales : [],
           details : [],
           show_modal : false,
           search_text : '',
           show_camera : false,
           order_id : '',
           buyer : ''

        }
    },
    methods : {

        async delete_website_order(order_id){
            const res = await axios.post(this.$store.state.api_url+'api/delete-website-order',{
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.retailer_sales()
            this.show_modal =false
        },

         on_detect(qr_code){
            qr_code.forEach(data=>{
                this.check_qr(data.rawValue)

            })
        },

        async check_qr(qr){
            const res = await axios.post(this.$store.state.api_url+'api/add-item-to-order',{
                qr : qr,
                order_id : this.order_id,
                buyer : this.buyer,
                retailer : this.$store.state.business_id
            }).then(function(response){
                return response.data 
            }).catch(function(error){
                console.log(error)
            })
        
            if(res=='success'){
                this.$swal('Product added to order')
            }else{
                this.$swal('Product does not exist')
            }
        },

        async retailer_sales(){
            const res = await axios.get(this.$store.state.api_url+'api/retailer-website-orders/'+this.$store.state.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.sales = res
        },

        async order_details(order_id,buyer){
            this.order_id = order_id
            this.buyer = buyer

           const res = await axios.get(this.$store.state.api_url+'api/website-order-details/'+order_id+'/'+this.$store.state.business_id).then(function(response){
            return response.data
           }).catch(function(error){
            console.log(error)
           })

           this.details = res
           this.show_modal = true
        },

         async search_order(){
           const res = await axios.post(this.$store.state.api_url+'api/search-order',{
            search_text : this.search_text,
            business_id : this.$store.state.business_id
           }).then(function(response){
            return response.data
           }).catch(function(error){
            console.log(error)
           })

           this.sales = res
          
        }
       
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.retailer_sales()
    }
}

</script>

