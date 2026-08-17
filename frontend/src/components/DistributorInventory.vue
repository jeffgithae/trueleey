<template>

    <div class="container">
        <Header />
    </div>

    
    <section style="padding-top: 50px;">

        <div class="container">

            <div>
                <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back </router-link>
            </div>

            <h6> <i class="bi bi-graph-up-arrow icon"></i> Distributor inventory</h6>
            <small>Based on assigned batches and boxes</small>

            <div class="shadow-lg rounded p-2">
             
              <table class="table">
                  <thead>
                      <tr> <th>Product name</th> <th>Buying price</th> <th>Selling price</th> <th>Profit</th> <th>Products available</th> <th>Boxes Available</th> <th>Inventory value</th> <th>Re-order level</th> </tr>
                  </thead>
                  <tbody v-for="box in boxes">
                      <tr> <td> {{box.product_name}} </td> <td> Ksh.{{box.buying_price}} </td> <td> Ksh.{{box.selling_price}} </td> <td> Ksh.{{box.profit}} </td> <td> {{box.total_products}} </td> <td> {{box.total_boxes}} </td> <td> Ksh.{{box.inventory_value}} </td> <td> {{box.reorder_level}} </td> </tr>
                  </tbody>
              </table>

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
          business_id : '',
          boxes : [] 
        }
    },
    methods : {
     async distributor_inventory(){
        const res  = await axios.get(this.$store.state.api_url+'api/distributor-inventory/'+this.business_id).then(function(response){
            return response.data
        }).catch(function(error){
            console.log(error)
        })
        this.boxes = res
     }  
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.distributor_inventory()
    },

    mounted () {
        if(this.$store.state.account_type!='Distributor'){
        this.$store.state.message = 'Forbidden access.Only manufacturers allowed'
        this.$router.push('/message')
    }
    }
}

</script>
