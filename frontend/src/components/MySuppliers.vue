<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">
            <div>
                <h6> <i class="bi bi-people-fill icon"></i> My suppliers</h6>
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
             <router-link :to="{name:'new-supplier',params : { 'business_id' : business_id }}" class="btn theme-btn-1"> <i class="bi bi-plus"></i> New supplier</router-link>
        </div>
    </section>
                </div>
            </div>

          

    <div class="row">

        <div class="col-md-12">
        <div class="shadow-lg p-3 rounded table-responsive">

        <table class="table">
            <thead>
                <tr> <th>Business name</th> <th>Email</th> <th>Location</th> <th>Account type</th> </tr>
            </thead>
            <tbody v-for="supplier in suppliers">
                <tr>
                <td>{{ supplier.name }}</td>
                <td>{{ supplier.email }}</td>
                <td>{{ supplier.location }}</td>
                <td>{{ supplier.account_type }}</td>
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
           suppliers : []
        
        }
    },
    methods : {
        async my_suppliers(){
            const res  = await axios.get(this.$store.state.api_url+'api/my-suppliers/'+this.business_id).then(function(response){
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
        this.my_suppliers()
        
    }
}

</script>
