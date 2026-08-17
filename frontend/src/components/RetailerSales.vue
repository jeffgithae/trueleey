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
                    <tr> <td> {{ sale.order_id }} </td> <td> Ksh.{{ sale.total_amount }} </td> <td> {{sale.date_bought}} </td> <td>{{sale.full_name}}</td> <td> <button class="btn" @click="order_details(sale.order_id)"> <i class="bi bi-three-dots"></i> </button> </td>  </tr>
                </tbody>
                
            </table>

            <p v-if="sales.length==0">No records found</p>
               
           </div>
        </div>
        
    </div>

</section>

<div class="shadow-lg rounded p-3 custom-modal m-3" v-if="show_modal">
    <div>
        <h6>Order details</h6>
    </div>
    <table class="table">
        <thead>
            <tr> <th>Product name</th> <th>Buying price</th> <th>Selling price</th> <th>Date bought</th> <th>Buyer</th> </tr>
        </thead>
        <tbody v-for="detail in details">
            <tr> <td> {{detail.product_name}} </td> <td> Ksh.{{detail.buying_price}} </td> <td> Ksh.{{detail.selling_price}} </td> <td> {{detail.date_bought}} </td> <td> {{detail.full_name}} </td> </tr>
        </tbody>
    </table>

    <div style="padding-top:10px;">
        <button class="btn theme-btn-2" @click="show_modal=false">Close</button>
    </div>

</div>



<Footer />
</template>

<script>
import Header from './layouts/Header'
import Footer from './layouts/Footer'
import axios from 'axios'
export default{
    name : 'home',
    components : {Header,Footer},
    data () {
        return { 
           sales : [],
           details : [],
           show_modal : false,
           search_text : ''
        }
    },
    methods : {

        async retailer_sales(){
            const res = await axios.get(this.$store.state.api_url+'api/retailer-sales/'+this.$store.state.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.sales = res
        },

        async order_details(order_id){
           const res = await axios.get(this.$store.state.api_url+'api/order-details/'+order_id+'/'+this.$store.state.business_id).then(function(response){
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
