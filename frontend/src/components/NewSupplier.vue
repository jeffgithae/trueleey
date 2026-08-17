<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">
            <div>
                <h6> <i class="bi bi-grid icon"></i>New supplier</h6>
            </div>
            <div class="row">

                <div class="col-md-3">
                    <div style="padding-bottom: 10px;">
                <router-link to="/business-dashboard" class="btn theme-btn-1" style="width: 100%;"> <i class="bi bi-arrow-left"></i> Go back</router-link>
               
            </div>
                </div>

                <div class="col-md-9">

                    <section>
        <div class="container" style="padding-bottom: 10px; text-align: right;">
            New suppliers are added to your list <router-link :to="{name:'my-suppliers',params : { 'business_id' : business_id }}" class="btn theme-btn-1">Click to view</router-link>
        </div>
    </section>

                </div>

                

            </div>

          

            <div class="row">

                <div class="col-md-12">
                <div class="shadow-lg p-3 rounded">

                    <form @submit.prevent="new_supplier()">
                        <div class="row">


                            <div class="col-md-6">
                                <div class="form-floating">
      <select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example" v-model="supplier_id" style="text-transform: uppercase;">
        <option value="" selected>Choose a supplier to add</option>
        <option :value="sup.id" v-for="sup in suppliers">
        {{ sup.name }}
        </option>
      </select>
      <label for="floatingSelectGrid">Businesses</label>
    </div>
                            </div>


            

                            <div class="col-md-6" style="text-align: left;">
                                <button class="btn theme-btn-1">Add supplier</button>
                            </div>


                        </div>
                    </form>
                    

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
           supplier_id : '',
           suppliers : []
        
        }
    },
    methods : {

        async new_supplier(){
            if(!this.supplier_id){
                this.$swal('Supplier cannot be blank')
                return
            }
           
            
            const res = await axios.post(this.$store.state.api_url+'api/new-supplier',{
                supplier_id : this.supplier_id,
                business_id : this.business_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal(res)
            
        },

        async get_suppliers(){
            const res  = await axios.get(this.$store.state.api_url+'api/get-suppliers').then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.suppliers = res
        }
        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.get_suppliers()
        
    }
}

</script>
