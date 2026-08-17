<template>

    <div class="container">
        <Header />
    </div>

    
    <section style="padding-top: 50px;">

        <div class="container">

            <div>
                <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back </router-link>
            </div>

            <h6> <i class="bi bi-graph-up-arrow icon"></i> Retailer inventory</h6>
            <small>Based on assigned boxes</small>

            <div class="shadow-lg rounded p-2">
            
              <table class="table">
                  <thead>
                      <tr> <th>Product name</th> <th>Buying price</th> <th>Selling price</th> <th>Profit</th>  <th>Available</th> <th>Inventory value</th> <th>Reorder level</th> </tr>
                  </thead>
                  <tbody v-for="product in products">
                   <tr>
                       <td> {{product.product_name}} </td>
                       <td> Ksh.{{product.buying_price}} </td>
                       <td> Ksh.{{product.selling_price}} </td>
                       <td> Ksh.{{product.profit}} </td>
                       <td> {{product.total_products}} </td>
                        <td> Ksh.{{product.inventory_value}} </td>
                        <td> {{product.reorder_level}} </td>

                   </tr>   
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
          products : [] 
        }
    },
    methods : {
     async retailer_inventory(){
        const res  = await axios.get(this.$store.state.api_url+'api/retailer-inventory/'+this.business_id).then(function(response){
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
        this.retailer_inventory()
    },
    mounted () {
        if(this.$store.state.account_type!='Retailer' && this.$store.state.account_type!='Salon'){
        this.$store.state.message = 'Forbidden access.Only retailers allowed'
        this.$router.push('/message')
    }
    }
}

</script>
