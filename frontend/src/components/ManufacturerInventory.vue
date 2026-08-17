<template>

    <div class="container">
        <Header />
    </div>

    
    <section style="padding-top: 50px;">

        <div class="container">

            <div>
                <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back </router-link>
            </div>

            <h6> <i class="bi bi-graph-up-arrow icon"></i> Manufacturer inventory</h6>
            <small>Based on current updated stock</small>

            <div class="shadow-lg rounded p-2">
              
             <div class="table-responsive">

                 <table class="table">
                  <thead>
                      <tr> <th>Product name</th> <th>Production cost</th> <th>Selling price</th> <th>Profit</th>  <th>Available</th> <th>Inventory value</th> <th>Re-order level</th> </tr>
                  </thead>
                  <tbody v-for="product in products">
                      <tr> <td>  {{product.product_name}} </td> <td>Ksh.  {{product.production_cost}} </td> <td>Ksh.  {{product.price}} </td> <td>Ksh. {{product.profit}} </td>  <td>  {{product.total_available}} </td> <td>Ksh. {{product.inventory_value}} </td> <td> {{product.reorder_level}} </td>  </tr>
                  </tbody>
              </table>
                 
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
          business_id : '',
          products : [],
          stock : []
        }
    },
    methods : {
     async manufacturer_inventory(){
        const res  = await axios.get(this.$store.state.api_url+'api/manufacturer-inventory/'+this.business_id).then(function(response){
            return response.data
        }).catch(function(error){
            console.log(error)
        })
        this.products = res
     },
 

    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.manufacturer_inventory()
        
    },

    mounted () {
        if(this.$store.state.account_type!='Manufacturer'){
        this.$store.state.message = 'Forbidden access.Only manufacturers allowed'
        this.$router.push('/message')
    }
    }
}

</script>
