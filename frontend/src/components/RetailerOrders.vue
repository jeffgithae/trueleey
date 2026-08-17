<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">

            <div>
                <h6> <i class="bi bi-card-checklist icon"></i> Purchase orders</h6>
            </div>

            <div class="row">

                <div class="col-md-3">
                    <div style="padding-bottom: 10px;">
                <router-link to="/business-dashboard" class="btn theme-btn-1" style="width: 100%;"> <i class="bi bi-arrow-left"></i> Go back</router-link>
               
            </div>
                </div>

                <div class="col-md-9" style="padding-bottom: 10px;">

                    <div class="input-group">
  <input type="search" class="form-control" placeholder="Search order by item name" aria-label="Recipient's username with two button addons" v-model="search_item">
  <button class="btn theme-btn-2" type="button" @click="retailer_orders()">Clear</button>
  <button class="btn theme-btn-1" type="button" @click="search_order()">Search</button>
</div>

                </div>

            </div>

          

            <div class="row">

                <div class="col-md-12">
                <div class="table-responsive shadow p-3 rounded">
                    
                    <table class="table">

                        <thead> 
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Owner</th>
                            <th>Email</th>
                            <th>Delivery date</th>
                            <th>Action...</th>
                        </tr>
                        </thead>

                       <tbody v-for="order in orders">
                        <tr>
                            <td> {{ order.order_id }} </td>
                            <td> {{ order.product_name }} </td>
                            <td> {{ order.quantity }} </td>
                            <td> Ksh.{{ order.total_price }} </td>
                            <td> {{ order.distributor_name }} </td>
                            <td> {{ order.email }} </td>
                            <td> {{ order.delivery_date }} </td>
                            <td> <button class="btn" @click="order_details(order.order_id)"> <i class="bi bi-three-dots"></i> </button> </td>
                        </tr>
                       </tbody>

                    </table>
                 
                </div>
                </div>

            </div>

        </div>
    </section>

    <!-- Orders details modal -->
    <section style="background-color: #fff; position: fixed; width: 100%; top: 0; z-index: 900; padding: 20px; overflow: scroll; height: 100%;" class="shadow" v-if="show_modal">


    <div class="row">

        <div class="col-md-6">
            <h6>Track this order</h6>
            <div class="shadow rounded p-3 table-responsive">
                <table class="table">
                <tbody>
                    <tr> <th> <i class="bi bi-check-circle icon"></i> Item name</th> <td>{{ product_name }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Item quantity</th> <td>{{ quantity }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Total price</th> <td>{{ total_price }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i>  Owner</th> <td>{{ distributor_name }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Email</th> <td>{{ email }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Delivery date</th> <td>{{ delivery_date }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Delivery location</th> <td>{{ delivery_location }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Specifications</th> <td>{{ specifications }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Instructions</th> <td>{{ instructions }}</td> </tr>
                    <tr> <th><i class="bi bi-check-circle icon"></i> Created</th> <td>{{ created }}</td> </tr>
                    
                    <tr> <th><i class="bi bi-check-circle icon"></i> Paid</th> <td>{{ paid }}</td> </tr>
                </tbody>
            </table>
            </div>

            <div style="margin-top: 20px;" class="shadow rounded p-3 table-responsive">
                <h6> <i class="bi bi-list icon"></i> Assigned boxes</h6>
                <table class="table">
                    <thead>
                        <tr> <th>Box code</th> <th>Product quantity</th> <th>Action...</th> </tr>
                    </thead>
                    <tbody v-for="box in boxes_assigned">
                        <tr> <td> <i class="bi bi-qr-code icon"></i> **** </td> <td> {{ box.qty }} </td> <td> <button class="btn text-danger" @click="delete_box(box.box_code)"> <i class="bi bi-x-circle"></i> </button> </td> </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-6">
            <div class="p-3">
               


              <div style="border:solid 1px lightgrey; border-radius: 10px; padding: 20px; margin-top: 20px;" class="shadow">
                <h6>Order status</h6>
               
               <div class="row">

                    <div class="col-md-6">
                        <p class="text-success"><i class="bi bi-check-circle"></i> Received</p>


                    <p v-if="status==0" class="text-danger"><i class="bi bi-x-circle"></i> Processing</p>
                    <p v-if="status>0 && status<=2" class="text-success"><i class="bi bi-check-circle"></i> Processing</p>

                    <p v-if="status<2" class="text-danger"><i class="bi bi-x-circle"></i> Dispatched</p>
                    <p v-if="status>1 && status<3" class="text-success"><i class="bi bi-check-circle"></i> Dispatched</p>  
                    </div>

                    <div class="col-md-6">
                        <p v-if="status<2">This order is not yet dispacthed. <button class="btn text-danger" @click="change_status(2)">Dispatch now <i class="bi bi-arrow-right"></i> </button> </p>
                        <p v-if="status<2" class="text-warning">Note: This action cannot be undone</p>
                    </div>
                  

                </div>  
                
              </div>


              <div style="padding-top: 20px;">
                <h6>Assign a boxes</h6>
                
                <form @submit.prevent="add_box()">
                    <div class="form-floating">
  <select class="form-select"  v-model="selected_box">
   <option v-for="box in boxes" :value="box.box_code">{{ box.product_name }}, Quantity = {{ box.qty }} </option>
  </select>
  <label>Pick a box</label>
</div>

<div style="padding-top: 20px;">
    <button class="btn theme-btn-2">Add box</button>
</div>
                </form>
              </div>


              <div style="padding-top: 50px;">
            <button class="btn theme-btn-2" style="margin-right:10px;" @click="show_modal=false">Close window</button>
            <button class="btn theme-btn-1" @click="delete_order()">Delete order</button>
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
           orders : [],
           business_id : '',
           search_item : '',
           details : [],
           show_modal : false,
           product_name : '',
           quantity : '',
           total_price : '',
           distributor_name : '',
           email : '',
           delivery_date : '',
           status : '',
           paid : '',
           instructions : '',
           specifications  : '',
           delivery_location : '',
           created : '',
           order_id : '',
           boxes : [],
           selected_box : '',
           owner_id : '',
           product_id : '',
           boxes_assigned : []

        }
    },

    methods : {

        async retailer_orders(){
            const res = await axios.get(this.$store.state.api_url+'api/retailer-orders/'+this.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.orders = res
            this.search_item = ''
        },

        async order_details(id){
            this.order_id = id
            const res = await axios.post(this.$store.state.api_url+'api/retailer-order-details',{
                id : id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.details = res

            this.details.forEach(data=>{
                this.product_name = data.product_name
                this.quantity = data.quantity
                this.email = data.email
                this.distributor_name = data.distributor_name
                this.total_price = data.total_price
                this.paid = data.paid
                this.status = data.status
                this.instructions = data.instructions
                this.specifications = data.specifications
                this.delivery_date = data.delivery_date
                this.delivery_location = data.delivery_location
                this.created  = data.created,
                this.owner_id = data.owner_id,
                this.product_id = data.product_id
            })

            this.business_boxes()
            this.assigned_boxes()
            this.show_modal = true
        },

        async search_order(){
            const res = await axios.post(this.$store.state.api_url+'api/search-order',{
                business_id : this.business_id,
                search_item : this.search_item
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.orders = res
        },

        async change_payment(value){
            const res = await axios.post(this.$store.state.api_url+'api/change-retailer-order-payment',{
                status : value,
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal('Payment status updated to '+value)
            this.order_details(this.order_id)
           
        },

        async change_status(value){
            if(this.status<1){
                this.$swal('Order cannot be dispatched before processed')
                return
            }
            const res = await axios.post(this.$store.state.api_url+'api/change-retailer-order-status',{
                status : value,
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal('Order status updated')
            this.order_details(this.order_id)
           
        },

        async delete_order(){
            const res = await axios.post(this.$store.state.api_url+'api/delete-order',{
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal(res)
            this.retailer_orders()
            this.show_modal = false
           
        },

        async business_boxes(){
            const res = await axios.get(this.$store.state.api_url+'api/boxes-to-assign/'+this.business_id+'/'+this.product_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.boxes = res
          
        },

        async add_box(){
            
            if(!this.selected_box){
                this.$swal('Box cannot be empty')
                return
            }

            const res = await axios.post(this.$store.state.api_url+'api/add-box-to-order',{
                order_id : this.order_id,
                box_code: this.selected_box,
                sender : this.business_id,
                receiver : this.owner_id,
                product_id : this.product_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
        this.$swal('Box added to order')
        this.business_boxes()
        this.assigned_boxes()
         this.order_details(this.order_id)
            
       
        },


        async assigned_boxes(){
            const res = await axios.get(this.$store.state.api_url+'api/assigned-boxes/'+this.business_id+'/'+this.order_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.boxes_assigned = res
          
        },

        async delete_box(box_code){
            const res = await axios.post(this.$store.state.api_url+'api/delete-assigned-box',{
                box_code : box_code
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
        
            this.assigned_boxes()
            this.business_boxes()
        },


        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.retailer_orders()
       
       
    }
}

</script>



<style scoped>
.details{
    border: solid 1px lightgrey;
    padding: 5px;
}

.status-btn{
    margin-top:10px;
}
</style>
