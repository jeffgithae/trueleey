<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">

            <div>
                <h6> <i class="bi bi-card-checklist icon"></i> Outgoing orders</h6>
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
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Supplier</th>
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
            <h6> <i class="bi bi-list icon"></i> Assigned boxes</h6>
            <table class="table">
                <thead>
                    <tr> <th>Box code</th> <th>Quantity</th> <th>Product name</th> <th>Selling price</th> <th>Received</th>  </tr>
                </thead>
                <tbody v-for="box in boxes_assigned">
                    <tr> <td> <i class="bi bi-qr-code icon"></i> ****** </td> <td> {{ box.qty }} </td> <td> {{ box.product_name }} </td> <td> Ksh. {{box.selling_price}}/item </td>
                        <td>
                             <div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" :disabled="box.received=='Yes'" :checked="box.received=='Yes'" @change="mark_box_received(box.box_code)">
  <label class="form-check-label" for="flexSwitchCheckChecked"> {{box.received}} </label>
</div>
</td>

                       </tr>
                </tbody>
            </table>
        </div>
    </div>


    <div class="col-md-6">
        <div class="p-3">
           
          <div style="border:solid 1px lightgrey; border-radius: 10px; padding: 20px; margin-top: 20px;" class="shadow-lg">
            <h6>Order status</h6>
            
            <div class="row">

                    <div class="col-md-6">
                        <p class="text-success"><i class="bi bi-check-circle"></i> Received</p>


                    <p v-if="status==0" class="text-danger"><i class="bi bi-x-circle"></i> Processing</p>
                    <p v-if="status>0 && status<=2" class="text-success"><i class="bi bi-check-circle"></i> Processing</p>

                    <p v-if="status<2" class="text-danger"><i class="bi bi-x-circle"></i> Dispatched</p>
                    <p v-if="status>1 && status<3" class="text-success"><i class="bi bi-check-circle"></i> Dispatched</p>  
                    </div>

                    <!-- <div class="col-md-6">
                        <p v-if="status<2">This order is not yet dispacthed. <button class="btn text-danger" @click="change_status(2)">Dispatch now <i class="bi bi-arrow-right"></i> </button> </p>
                        <p v-if="status<2" class="text-warning">Note: This action cannot be undone</p>
                    </div> -->
                  

                </div>  

          </div>


          <div class="table-responsive shadow-lg rounded mt-3 p-3">
            <h6>Supplier details</h6>
            <table class="table">
                <thead>
                    <tr> <th>Name</th> <th>Email</th> <th>Location</th> </tr>
                </thead>
                <tbody v-for="supplier in suppliers">
                    <tr>
                        <td> {{ supplier.name }} </td>
                        <td> {{ supplier.email }} </td>
                        <td> {{ supplier.location }} </td>
                    </tr>
                </tbody>
            </table>
          </div>

          <div style="margin-top:50px;" class="shadow rounded p-3">
              <p>Make payment</p>
              <h6>Current balance : Ksh.{{this.$store.state.balance}}</h6>
              <h6>Total amount :  Ksh.{{total_price}}</h6>
              <button @click="pay_order()" class="btn theme-btn-2" :disabled="paid=='Paid'">Pay</button>
          </div>


          <div style="padding-top:30px;">
              <button class="btn theme-btn-2" @click="show_camera=true"> <i class="bi bi-camera"></i> Scan boxes</button>
          </div>

         
          <div style="padding-top: 50px;">
        <button class="btn theme-btn-2" style="margin-right:10px;" @click="show_modal=false">Close window</button>
        <button class="btn theme-btn-1" @click="delete_order()">Delete order</button>
    </div>


        </div>
    </div>

</div>

</section>


<!-- selling price form -->
<div class="moda" v-if="show_input_form">

    <div class="moda-content" style="background-color: #fff;">

      <form @submit.prevent='save_price()'>
            <input type="number" min="0" v-model="selling_price" class="form-control" placeholder="Enter selling price">

        <div style="padding-top:20px;">
            <button class="btn theme-btn-2" style="margin-right:10px;" @click="show_input_form=false">Close</button>
            <button type="submit" class="btn theme-btn-2">Save changes</button>
        </div>
      </form>
        
    </div>
    
</div>


 <div class="moda" v-if="show_camera">

                <div class="moda-content">
                     <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4" style="text-align:center;">
                         <qrcode-stream @detect="on_detect" class="camera" ></qrcode-stream> 

                         <div style="padding-top:20px;">
                             <button class="btn theme-btn-1" style="margin-right:10px;" @click="manage_camera()"> <i class="bi bi-x-circle"></i></button>
                         </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                </div>
                   
               </div> 


<Footer />
</template>

<script>
import Header from './layouts/Header'
import Footer from './layouts/Footer'
import BusinessMenu from './layouts/BusinessMenu'
import axios from 'axios'

import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'


export default{
    name : 'home',
    components : {Header,Footer,BusinessMenu,QrcodeStream,QrcodeDropZone,QrcodeCapture},
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
           boxes_assigned : [],
           supplier_id : '',
           suppliers : [],
           selling_price : '',
           show_input_form : false,
           selected_box : '',
           show_camera : false,
           text : ''


        }
    },
    methods : {

        async outgoing_orders(){
            const res = await axios.get(this.$store.state.api_url+'api/retailer-outgoing-orders/'+this.business_id).then(function(response){
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
                this.created  = data.created
                this.supplier_id = data.supplier_id
            })
            this.assigned_boxes()
            this.supplier_details()
            this.show_modal = true
        },


        async change_payment(value){
            const res = await axios.post(this.$store.state.api_url+'api/change-order-payment',{
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
            const res = await axios.post(this.$store.state.api_url+'api/change-order-status',{
                status : value,
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal('Order status updated to '+value)
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
            this.$swal('Order removed from list')
            this.distributor_orders()
            this.show_modal = false
           
        },


        async assigned_boxes(){
            const res = await axios.get(this.$store.state.api_url+'api/assigned-boxes/'+this.business_id+'/'+this.order_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.boxes_assigned = res
          
        },


        async supplier_details(){
            const res = await axios.get(this.$store.state.api_url+'api/supplier-details/'+this.supplier_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.suppliers = res
          
        },


        set_selling_price(box_code){
            this.show_input_form = true
            this.selected_box = box_code
        },


        async pay_order(){
            const res = await axios.post(this.$store.state.api_url+'api/pay-retailer-order',{
                sender : this.$store.state.user_id,
                supplier_id : this.supplier_id,
                amount : this.total_price,
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal(res)
            this.user_details()
            this.order_details(this.order_id)

        },

        async user_details(){
    
            const res = await axios.post(this.$store.state.api_url+'api/user-details',{
                email : this.$store.state.email,
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            
            res.forEach(data => {
                
                this.$store.state.balance = data.balance
                localStorage.setItem('balance', data.balance)  

            });
            
            
        },

        async mark_box_received(box_code){
            const res  = await axios.post(this.$store.state.api_url+'api/mark-box-received',{
                box_code : box_code,
                order_id : this.order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.assigned_boxes()

        },

          on_detect(qr_code){
            qr_code.forEach(data=>{
                this.scan_box(data.rawValue)
                
            })
        },
        manage_camera(){
            this.show_camera = !this.show_camera
            this.text = 'Scanning...'
            if(this.show_camera==false){
                this.text = 'Scan boxes'
            }
        },

        async scan_box(box_code){
            const res = await axios.post(this.$store.state.api_url+'api/scan-box',{
                box_code : box_code,
                order_id : this.order_id,
                business_id : this.business_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal(res)
            this.assigned_boxes()
        }


        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.outgoing_orders()
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

    .camera{
     border: solid 1px #65F8E4; 
    }
</style>
