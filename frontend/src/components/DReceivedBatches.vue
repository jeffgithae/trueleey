<template>

    <div class="container">
    <Header />
    </div>
    
    
    <section style="padding-top: 30px;">
    <div class="container">
    <div>
    <h6> <i class="bi bi-grid icon"></i> Received batches</h6>
    </div>
    <div class="row">
    
    <div class="col-md-3">
    <div style="padding-bottom: 10px;">
    <router-link to="/business-dashboard" class="btn theme-btn-1" style="width: 100%;"> <i class="bi bi-arrow-left"></i> Go back</router-link>
    
    </div>
    </div>
    
    <div class="col-md-9">
    
    
    </div>
    
    
    
    </div>
    
    
    
    <div class="row">
    
    <div class="col-md-12">
    <div class="shadow-lg p-3 rounded table-responsive">
    
    <table class="table">
    <thead>
    <tr> <th> Batch name  </th> <th>Batch code</th> <th>Boxes</th> <th>Product name</th> <th>Price</th> <th>Quantity per box</th> <th>Received</th>  </tr>
    </thead>
    
    <tbody v-for="batch in batches">
    <tr> <td> {{ batch.batch_name }} </td> <td> {{ batch.batch_code.slice(0,6) }}****** </td> <td>{{ batch.boxes }}</td> <td>{{ batch.product_name }}</td> <td>Ksh.{{ batch.price}}</td> <td> {{ batch.quantity_per_box }} </td>  <td>

    <div class="form-check form-switch">
  <input class="form-check-input" type="checkbox" :disabled="batch.received=='Yes'" :checked="batch.received=='Yes'" @change="mark_batch_received(batch.batch_code,batch.order_id)">
  <label class="form-check-label" for="flexSwitchCheckChecked"> {{batch.received}} </label>
</div>

 </td>

  </tr>
    </tbody>
    
    </table>
    
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
    business_id : '',
    batches : [],
    boxes : [],
    selling_price : '',
    selected_batch : '',
    price : '',
    disabled : false
    
    }
    },
    methods : {
    
    async received_batches(){
    const res  = await axios.get(this.$store.state.api_url+'api/d-received-batches/'+this.business_id).then(function(response){
    return response.data
    }).catch(function(error){
    console.log(error)
    })
    this.batches = res
    },
    
  
    
     async mark_batch_received(batch_code,order_id){
            const res  = await axios.post(this.$store.state.api_url+'api/mark-batch-received',{
                batch_code : batch_code,
                order_id : order_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.received_batches()
        }
    
   
    
    
    },
    created (){
    window.scrollTo({ top: 0, behavior: 'smooth' });
    this.business_id = this.$route.params.business_id
    this.received_batches()
    },

    mounted () {
        if(this.$store.state.account_type!='Distributor'){
        this.$store.state.message = 'Forbidden access.Only Distributors allowed'
        this.$router.push('/message')
    }
    }

    }
    
    </script>
