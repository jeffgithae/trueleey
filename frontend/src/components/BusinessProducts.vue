<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">

              <div>
                <router-link to="/business-dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back </router-link>
            </div>

            <div class="row">

                <div class="col-md-4">
                 <div>
                <h6> <i class="bi bi-grid icon"></i> Products</h6>
            </div>
                </div>

                <div class="col-md-4"></div>

                <div class="col-md-4" style="text-align:right;">
                     <div class="mb-3">
    <div class="input-group">
  <span class="input-group-text"> <i class="bi bi-search"></i> </span>
  <input type="search" class="form-control" placeholder="Search" @input="search_product()" v-model="search_text">
</div>
   
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
                            <th>Image</th>
                            <th>Item</th>
                            <th>Price</th>
                            <th>M/date</th>
                            <th>Exp/date</th>
                            <th>Action...</th>
                        </tr>
                        </thead>

                        <tbody v-for="product in products">
                            <tr>
                            <td style="text-align:center;"> <img :src="this.$store.state.api_url+'assets/product-images/'+product.productPicture" style="max-height:50px;" class="shadow rounded" v-if="product.productPicture!=''"> </td>
                            <td> {{ product.id }}  </td>
                            <td> {{ product.productName }}  </td>
                            <td> Ksh.{{ product.price }} </td>
                            <td> {{ product.dateManufactured }} </td>
                            <td> {{ product.expiryDate }} </td>
                            <td>

    <div class="dropdown">
  <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
    Manage
  </button>
  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
    <li> <router-link class="dropdown-item" :to="{ name : 'edit-product', params : {'product_id' : product.id} }"> <i class="bi bi-pencil-square"></i> Edit </router-link> </li>

<li> <router-link class="dropdown-item" :to="{ name : 'product-stock', params : {'product_id' : product.id} }"> <i class="bi bi-arrow-clockwise"></i>Manage stock </router-link> </li>

<li> <button class="dropdown-item text-danger" @click="delete_product(product.id)"> <i class="bi bi-x"></i>Delete </button> </li>



  </ul>
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
           products : [],
           business_id : '',
           search_text : ''

        }
    },
    methods : {

        async business_products(){
            const res = await axios.get(this.$store.state.api_url+'api/business-products/'+this.business_id).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.products = res
            this.search_item = ''
        },

        async search_product(){
            const res = await axios.post(this.$store.state.api_url+'api/search-product',{
                search_text : this.search_text,
                business_id : this.business_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.products = res
        },

        async delete_product(id){
            const res = await axios.post(this.$store.state.api_url+'api/delete-product',{
                id : id,
                business_id : this.business_id
            }).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal(res)
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
