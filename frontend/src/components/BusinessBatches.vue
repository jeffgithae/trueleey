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
                <h6> <i class="bi bi-list icon"></i> Product batches</h6>
            </div>
        

          

            <div class="row">

                <div class="col-md-12">
                   
                <div class="table-responsive shadow-lg p-3 rounded">

                    <table class="table">

                        <thead> 
                        <tr>
                            <th>#</th>
                            <th>Batch name</th>
                            <th>Batch code</th>
                            <th>Product</th>
                            <th>Boxes</th>
                            <th>Qty/box</th>
                            <th>Total products</th>
                            <th>Assigned</th>
                            <th>Action...</th>
                        </tr>
                        </thead>

                        <tbody v-for="batch in batches">
                            <tr>
                                <td> {{ batch.batch_id }} </td>
                                <td> {{ batch.batch_name }} </td>
                                <td> {{ batch.batch_qr_code.slice(0,6) }}*** </td>
                                <td> {{ batch.product_name }} </td>
                                <td> {{ batch.boxes }} </td>
                                <td> {{ batch.quantity_per_box }} </td>
                                <td> {{ batch.number_of_product_codes }} </td>
                                <td> <span v-if="batch.assigned" class="badge bg-success">Yes</span> <span v-if="!batch.assigned" class="badge bg-warning">No</span> </td>
                                <td> <button class="btn" @click="generate_qr(batch.batch_qr_code)"> <i class="bi bi-three-dots"></i> </button> </td>
                            </tr>  
                        </tbody>

                    </table>

                </div>
                </div>

            </div>

        </div>
    </section>



<div style="position: fixed; width:100%; top: 0; overflow: scroll; background-color: #fff; height: 100%; z-index: 900;" class="shadow-lg p-3" v-if="show_modal_2">

     <div class="row">

        <div class="col-md-6 col-12">
            <h6>Generated QR codes</h6>
        </div>

        <div class="col-md-6 col-12" style="text-align:right;">
            <button class="btn theme-btn-2" v-print="'#page_2'" style="margin-right:10px;"> <i class="bi bi-printer" ></i> Print</button>
            <button class="btn theme-btn-2" @click="show_modal_2=false"> <i class="bi bi-x-circle"></i> Close </button>
        </div>
        
    </div>

     <div id="page_2">
        

       <div>
        <h6>Products QR code</h6>
        <button  v-for="(product, index) in products" v-if="products.length>0" class="btn shadow-lg qr">
            sn : <i class="bi bi-check-circle text-success"></i> {{index + 1}} <br />
        <qrcode-vue  :value="product.productQrCode" :size="size" level="H"></qrcode-vue>   
        </button>

          
      </div>


    </div>

</div>



      

<div style="position: fixed; width:100%; top: 0; overflow: scroll; background-color: #fff; height: 100%;" class="shadow-lg p-3" v-if="show_modal">

            <div class="row">

        <div class="col-md-6 col-12">
            <h6>Generated QR codes</h6>
        </div>

        <div class="col-md-6 col-12" style="text-align:right;">
            <button class="btn theme-btn-2" v-print="'#page'" style="margin-right:10px;"> <i class="bi bi-printer" ></i> Print</button>
            <button class="btn theme-btn-2" @click="show_modal=false"> <i class="bi bi-x-circle"></i> Close </button>
        </div>
        
    </div>

    <div id="page">
        

          <div>
        <h6>Batch QR code</h6>
      <qrcode-vue v-if="selected_batch" :value="selected_batch" :size="size" level="H" class="shadow-lg m-3 rounded qr" />
          
      </div>


       <div>
        <h6>Boxes QR code</h6>
        <button @click="generate_product_qr(box.boxQrCode)" v-for="(box, index) in boxes" v-if="boxes.length>0" class="btn shadow-lg qr">
            sn : <i class="bi bi-check-circle text-success"></i> {{index + 1}} <br />
        <qrcode-vue  :value="box.boxQrCode" :size="size" level="H"></qrcode-vue>   
        </button>

          
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
import QrcodeVue from 'qrcode.vue'
export default{
    name : 'home',
    components : {Header,Footer,BusinessMenu,QrcodeVue},
    data () {
        return { 
           batches : [],
           business_id : '',
           selected_batch : '',
           size : 200,
           boxes : [],
           show_modal : false,
           box_counter : 0,
           products : [],
           show_modal_2 : false
          

        }
    },
    methods : {

        async business_batches(){
            const res = await axios.get(this.$store.state.api_url+'api/business-batches/'+this.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.batches = res
          
        },

        generate_qr(batch_code){
            this.selected_batch = batch_code
            this.generate_box_qr(batch_code)
        },


           async generate_box_qr(batch_code){
            const res = await axios.post(this.$store.state.api_url+'api/generate-box-qr',{
                batch_code : batch_code
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.boxes = res
            this.show_modal = true
           
        },


          async generate_product_qr(box_code){
            const res = await axios.post(this.$store.state.api_url+'api/generate-product-qr',{
                box_code : box_code
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.products = res
            this.show_modal_2 = true
           
        },

        

       
        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.business_batches()
    },

    mounted () {
        if(this.$store.state.account_type!='Manufacturer'){
        this.$store.state.message = 'Forbidden access.Only manufacturers allowed'
        this.$router.push('/message')
    }
    }
}

</script>


<style scoped>

    .qr{
        border: solid 2px #65F8E4;
    }
    
</style>
