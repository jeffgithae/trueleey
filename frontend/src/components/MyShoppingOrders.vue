<template>

    <div class="container">
        <Header />
    </div>

    
    <section style="padding-top: 20px;">

        <div class="container">

             <router-link to="/dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back</router-link>

            <div>
                <h6>Shopping orders</h6>
            </div>


            <div>
                <h6>Physical shopping</h6>
                <div v-for="order in orders">
                    <button v-if="order.website_order==0"  class="btn shadow rounded p-2 m-2"> <span class="text-danger"><i>Order#{{order.order_id}}</i></span>, Total amount. Ksh.{{order.total_amount}}, <span class="text-success">Date of order : {{order.date_bought}}</span> </button>
                </div>


            </div>

              <div style="padding-top:40px;">
                <h6>Online shopping</h6>
                <div v-for="order in orders">
                    <button v-if="order.website_order==1" class="btn shadow rounded p-2 m-2"> <span class="text-danger"><i>Order#{{order.order_id}}</i></span>, Total amount. Ksh.{{order.total_amount}}, <span class="text-success">Date of order : {{order.date_bought}}</span> </button>
                </div>


            </div>

        </div>

    </section>




<Footer />
</template>

<script>
import Header from './layouts/Header'
import Slider from './layouts/Slider'
import Footer from './layouts/Footer'
import axios from 'axios'
export default{
    name : 'home',
    components : {Header,Slider,Footer},
    data () {
        return { 
            orders : []
        }
    },
    methods : {

        async my_orders(){
            const res  = await axios.get(this.$store.state.api_url+'api/my-shopping-orders/'+this.$store.state.user_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.orders = res
        },
       
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.my_orders()
    }
}

</script>
