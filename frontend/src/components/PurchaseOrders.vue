<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">

            <div class="row">

               <div class="col-md-4">
                    <div>
                <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back</router-link>
               
            </div>

            <div>
                <h6> <i class="bi bi-list icon"></i> Purchase orders</h6>
            </div>
               </div>

               <div class="col-md-4">
                   <div style="padding-top:5px; border-left:solid 1px lightgrey; padding-left:10px;">
                       <h6>Order source : {{order_source}}</h6>
                       <h6>Total orders : {{orders.length}}</h6>
                   </div>
               </div>

                <div class="col-md-4">
                    <div style="padding-top:5px; padding-bottom:5px;">
                        <label>Pick source</label>
                        <select class="form-select input-design" v-model="order_source" @change="get_order_source()">

                            <option selected="" value="All">All</option>
                            <option value="Distributor">Distributors</option>
                            <option value="Retailer">Retailers</option>
                        </select>
                    </div>
                </div>


            </div>

          

            <div class="row">

                <div class="col-md-12">
                <div class="table-responsive shadow-lg p-3 rounded">

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
    <section style="background-color: #fff; position: fixed; width: 100%; top: 0; z-index: 900; padding: 20px; overflow: scroll; height: 100%;" class="shadow-lg" v-if="show_modal">


    <div class="row">

        <div class="col-md-6">
            <h6>Track this order</h6>
            <div class="shadow-lg rounded p-3 table-responsive">
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

            <div style="margin-top: 20px;" class="shadow-lg rounded p-3 table-responsive">
                <h6> <i class="bi bi-list icon"></i> Assigned batches</h6>
                <table class="table">
                    <thead>
                        <tr> <th>Batch code</th> <th>Batch name</th> <th>Action...</th> </tr>
                    </thead>
                    <tbody v-for="batch in batches_assigned">
                        <tr> <td> <i class="bi bi-qr-code icon"></i> ****** </td> <td> {{ batch.batch_name }} </td> <td> <button class="btn text-danger" @click="delete_batch(batch.batch_code)"> <i class="bi bi-x-circle"></i> </button> </td> </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-md-6">
            <div class="p-3">
                
              <div style="border-radius: 10px; padding: 20px; margin-top: 20px;" class="shadow">
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

              <!-- Create batch on the go -->


<div class="shadow p-3 rounded mt-3">
<h6>Create batch</h6>
<form @submit.prevent="new_batch()">
<div class="row">


<div class="col-md-6">
<div class="form-floating mt-3">
<select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example" v-model="product_id" style="text-transform: uppercase;">
<option value="" selected>Choose a product to add</option>
<option :value="product_id">
{{ product_name}}
</option>
</select>
<label for="floatingSelectGrid">Products</label>
</div>
</div>


<div class="col-md-6">
<div class="form-floating mt-3">
<input type="text" class="form-control" id="floatingInput" placeholder="Bread" v-model="batch_name">
<label for="floatingInput">Batch name</label>
</div>
</div>

<div class="col-md-6">
<div class="form-floating mt-3">
<input type="number" min="1" class="form-control" id="floatingInput" placeholder="Boxes" v-model="boxes">
<label for="floatingInput">Number of boxes</label>
</div>
</div>

<div class="col-md-6">
<div class="form-floating mt-3">
<input type="number" min="1" class="form-control" id="floatingInput" placeholder="Bread" v-model="quantity_per_box">
<label for="floatingInput">Quantity per box</label>
</div>
</div>




<div class="col-md-6 mt-3" style="text-align: left;">
<button class="btn theme-btn-1">Create batch</button>
</div>


</div>
</form>


</div>


              <div class="shadow p-3 mt-3 rounded">
                <h6>Assign a batch</h6>
                
                <form @submit.prevent="add_batch()">
                    <div class="form-floating">
  <select class="form-select"  v-model="selected_batch">
   <option v-for="batch in batches" :value="batch.batch_qr_code">{{ batch.batch_name }} - {{ batch.product_name  }}</option>
  </select>
  <label>Pick a batch</label>
</div>

<div style="padding-top: 20px;">
    <button class="btn theme-btn-2">Add batch</button>
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
           batches : [],
           selected_batch : '',
           owner_id : '',
           product_id : '',
           batches_assigned : [],
           order_source : 'All',

            batch_name : '',
            boxes : '',
            quantity_per_box : ''

        }
    },

    methods : {

        async new_batch(){
if(!this.product_id){
this.$swal('Product cannot be blank')
return
}

if(!this.batch_name){
this.$swal('Batch name cannot be blank')
return
}

if(!this.boxes){
this.$swal('Boxes cannot be blank')
return
}

if(!this.quantity_per_box){
this.$swal('Quantity cannot be blank')
return
}


const res = await axios.post(this.$store.state.api_url+'api/new-batch',{
product_id : this.product_id,
business_id : this.business_id,
batch_name : this.batch_name,
boxes : this.boxes,
quantity : this.quantity_per_box
}).then(function(response){
return response.data
}).catch(function(error){
console.log(error)
})
this.$swal(res)

this.batch_name = ''
this.boxes = ''
this.quantity_per_box = ''
this.business_batches()
},

        async distributor_orders(){
            const res = await axios.get(this.$store.state.api_url+'api/distributor-orders/'+this.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.orders = res
            this.search_item = ''
        },

        async order_details(id){
            this.order_id = id
            const res = await axios.post(this.$store.state.api_url+'api/distributor-order-details',{
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

            this.business_batches()
            this.assigned_batches()
            this.show_modal = true
        },

        

     

        async change_status(value){

            if(this.status<1){
                this.$swal('Order cannot be dispatched before processed')
                return
            }

            const res = await axios.post(this.$store.state.api_url+'api/change-order-status',{
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
            this.distributor_orders()
            this.show_modal = false
           
        },

        async business_batches(){
            const res = await axios.get(this.$store.state.api_url+'api/batches-to-assigned/'+this.business_id+'/'+this.product_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.batches = res
          
        },

        async add_batch(){
            
            if(!this.selected_batch){
                this.$swal('Batch cannot be empty')
                return
            }

            const res = await axios.post(this.$store.state.api_url+'api/add-batch-to-order',{
                order_id : this.order_id,
                batch_code: this.selected_batch,
                sender : this.business_id,
                receiver : this.owner_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
        this.$swal('Batch added to order')
        this.business_batches()
        this.assigned_batches()
        this.order_details(this.order_id)
        },


        async assigned_batches(){
            const res = await axios.get(this.$store.state.api_url+'api/assigned-batches/'+this.business_id+'/'+this.order_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.batches_assigned = res
          
        },

        async delete_batch(batch_code){
            const res = await axios.post(this.$store.state.api_url+'api/delete-assigned-batch',{
                batch_code : batch_code
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
        
            this.assigned_batches()
            this.business_batches()
        },

        async filter_order(order_source){
            const res = await axios.post(this.$store.state.api_url+'api/get-order-source',{
                business_id : this.business_id,
                order_source : order_source
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.orders = res
        },


       get_order_source(){
            if(this.order_source=='All'){
                this.distributor_orders()
            }else if(this.order_source=='Distributor'){
                this.filter_order(this.order_source)
            }else{
                this.filter_order(this.order_source)
            }
        }


        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.distributor_orders()
       
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
