<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">

              <div>
                <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back </router-link>
            </div>

            <div>
                <h6> <i class="bi bi-arrow-clockwise icon"></i> Manage product stock</h6>
            </div>


            <div class="shadow rounded p-3">
                
               
                    <div class="row">
                        <div class="col-md-4">
                             <form @submit.prevent="update_stock()">
                            <label>Quantity</label>
                            <input type="number" min="1" v-model="qty" class="form-control" placeholder="Enter quantity to add" required>
                            <div style="padding-top:20px;">
                                <button class="btn theme-btn-2">Submit</button>
                            </div>
                            <p style="padding-top:20px;">Update stock to help you create batches</p>
                             </form>
                        </div>

                        <div class="col-md-8" style="padding-top:20px; border-left: solid 1px lightgrey;">
                           <h6> <i class="bi bi-arrow-down"></i> Stock update history</h6> 
                           <div class="table-responsive">
                           <table class="table">
                               <thead>
                                   <tr> <th>Quantity</th> <th>Updated on</th> <th>Action...</th> </tr>
                               </thead>
                               <tbody v-for="s in stock">

                                   <tr>
                                       <td> <i class="bi bi-check icon"></i> {{s.amount}} </td>
                                       <td> {{s.updated_on}} </td>
                                       <td> <button class="btn text-danger" @click="delete_stock(s.hash)"> <i class="bi bi-x-circle"></i> </button> </td>
                                   </tr>
                               </tbody>
                           </table>
                       </div>
                       <h6>Total stock : {{total_stock}}</h6>
                        </div>
                    </div>
               

            </div>
        

          

        

        </div>
    </section>


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
           product_id : '',
            qty : 1,
            stock : [],
            total_stock : 0
        }
    },
    methods : {

        async update_stock(){
            const res = await axios.post(this.$store.state.api_url+'api/update-stock',{
                product_id : this.product_id,
                qty : this.qty,
                business_id : this.$store.state.business_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal('Stock updated')
            this.stock_history()
        },


        async stock_history(){
            const res = await axios.get(this.$store.state.api_url+'api/stock-history/'+this.product_id+'/'+this.$store.state.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
           this.stock = res
           res.forEach((data)=>{
            this.total_stock += data.amount
           })
        },

          async delete_stock(hash){
            const res = await axios.post(this.$store.state.api_url+'api/delete-stock',{
                hash  : hash
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
           this.stock_history()
           
        }

      
        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.product_id = this.$route.params.product_id
        this.stock_history()
        
    }
}

</script>
