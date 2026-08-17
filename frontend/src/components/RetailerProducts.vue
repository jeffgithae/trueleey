<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">
            <div>
                <h6> <i class="bi bi-grid icon"></i> Products</h6>
            </div>
            <div class="row">

                <div class="col-md-3">
                    <div style="padding-bottom: 10px;">
                <router-link to="/business-dashboard" class="btn theme-btn-1" style="width: 100%;"> <i class="bi bi-arrow-left"></i> Go back</router-link>
               
            </div>
                </div>

                <div class="col-md-9" style="padding-bottom: 10px;">

                </div>

            </div>

          

            <div class="row">

                <div class="col-md-12">
                <div class="table-responsive shadow-lg p-3 rounded">

                    <table class="table">

                        <thead> 
                        <tr>
                         
                            <th>Product</th>
                            <th>Selling price</th>
                            <th>Reorder level</th>
                            <th>Action...</th>
                           
                        </tr>
                        </thead>

                        <tbody v-for="product in products">
                            <tr>
                            <td> {{ product.productName }}  </td>
                            <td> Ksh.{{ product.sellingPrice }} </td>
                            <td> {{ product.reorderLevel }} </td>
                            <td> <button class="btn" @click="product_details(product.id)"> <i class="bi bi-three-dots"></i> </button> </td>
                            
                            </tr>
                        </tbody>

                    </table>

                </div>
                </div>

            </div>

        </div>
    </section>


    <div style="position: fixed; top:0; right: 0; background-color: #fff; padding:10px;" class="shadow-lg rounded m-3" v-if="show_modal">

        <h6>{{product_name}}</h6>
        
    <form @submit.prevent="edit_r_product()">
        <div class="row">

            <div class="col-md-6">
                <label>Selling price</label>
                <input class="form-control input-design" v-model="selling_price" placeholder="">
            </div>

            <div class="col-md-6">
                 <label>Reorder level</label>
                <input class="form-control input-design" v-model="reorder_level" placeholder="">
            </div>

            <div class="col-md-6" style="padding-top:20px;">
                <button type="button" class="btn theme-btn-2" @click="show_modal=false">Close</button>
                <button class="btn theme-btn-2">Save changes</button>
            </div>
            
        </div>
    </form>

    </div>


<Footer />
</template>

<script>
import Header from './layouts/Header'
import Footer from './layouts/Footer'
import BusinessMenu from './layouts/BusinessMenu'
import axios from 'axios'
export default{
    name : 'home',
    components : {Header,Footer,BusinessMenu},
    data () {
        return { 
           products : [],
           business_id : '',
           details : [],
           product_name : '',
           selling_price : '',
           reorder_level : '',
           product_id : '',
           show_modal : false
         

        }
    },
    methods : {

        async business_products(){
            const res = await axios.get(this.$store.state.api_url+'api/r-products/'+this.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.products = res
          
        },

        async product_details(id){
            this.product_id = id
            const res = await axios.get(this.$store.state.api_url+'api/r-product-details/'+id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.details = res

            res.forEach((data)=>{
                this.product_name = data.productName
                this.selling_price = data.sellingPrice
                this.reorder_level = data.reorderLevel
            })
            this.show_modal = true
          
        },

         async edit_r_product(id){
           
            if(!this.selling_price){
                this.$swal('Selling price cannot be blank')
                return
            }

             if(!this.reorder_level){
                this.$swal('Reorder level cannot be blank')
                return
            }
            const res = await axios.post(this.$store.state.api_url+'api/edit-r-product',{
                selling_price : this.selling_price,
                reorder_level : this.reorder_level,
                product_id : this.product_id 
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
           
            this.$swal('Changes saved successfully')
            this.business_products()
            
        }

      
        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.business_products()
    }
}

</script>
