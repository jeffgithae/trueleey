<template>

    <div class="container">
        <Header />
    </div>


    <section style="padding-top: 30px;">
        <div class="container">
            <div>
                <h6> <i class="bi bi-grid icon"></i>New products</h6>
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
            New products are added to your list <router-link :to="{name:'business-products',params : { 'business_id' : business_id }}" class="btn theme-btn-1">Click to view</router-link>
        </div>
    </section>

                </div>

                

            </div>

          

            <div class="row">

                <div class="col-md-12">
                <div class="shadow-lg p-3 rounded">

                    <form @submit.prevent="new_product()" enctype="multipart/form-data">
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="Bread" v-model="product_name">
  <label for="floatingInput">Product name</label>
</div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="file" class="form-control" placeholder="Image" @change="get_image">
  <label for="floatingInput">Product image</label>
</div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-floating">
      <select class="form-select" id="floatingSelectGrid" aria-label="Floating label select example" v-model="category">
        <option value="" selected>Choose a category</option>
        <option :value="cat.id" v-for="cat in categories">
        {{ cat.name }}
        </option>
      </select>
      <label for="floatingSelectGrid">Category</label>
    </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="100" v-model="production_cost">
  <label for="floatingInput">Production cost</label>
</div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="100" v-model="price">
  <label for="floatingInput">Selling price</label>
</div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="date" class="form-control" id="floatingInput" placeholder="" v-model="date_manufactured">
  <label for="floatingInput">Date manufactured</label>
</div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="date" class="form-control" id="floatingInput" placeholder="" v-model="expiry_date">
  <label for="floatingInput">Expiry date</label>
</div>
                            </div>


                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="100" v-model="weight">
  <label for="floatingInput">Weight</label>
</div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="100" v-model="shipping_cost">
  <label for="floatingInput">Shipping cost per item</label>
</div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="100" v-model="description">
  <label for="floatingInput">Description</label>
</div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating mb-3">
  <input type="text" class="form-control" id="floatingInput" placeholder="100" v-model="reorder_level">
  <label for="floatingInput">Reorder level</label>
</div>
                            </div>

                            <div class="col-md-12" style="text-align: left;">
                                <button class="btn theme-btn-1">Save product</button>
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
           product_name : '',
           price : '',
           category : '',
           date_manufactured : '',
           expiry_date : '',
           weight : '',
           description : '',
           shipping_cost : '',
           categories : [],
           production_cost : '',
           reorder_level : 0,
           image : ''
        
        }
    },
    methods : {

         get_image(e){
            this.image = e.target.files[0] 
        },

        async new_product(){
            if(!this.product_name){
                this.$swal('Product name cannot be blank')
                return
            }
            if(!this.category){
                this.$swal('Category cannot be blank')
                return
            }
            if(!this.price){
                this.$swal('Price cannot be blank')
                return
            }
             if(!this.production_cost){
                this.$swal('Production cost cannot be blank')
                return
            }
            if(!this.description){
                this.$swal('Description cannot be blank')
                return
            }
            if(!this.date_manufactured){
                this.$swal('Date manufactured cannot be blank')
                return
            }
            if(!this.expiry_date){
                this.$swal('Expiry cannot be blank')
                return
            }
            if(!this.weight){
                this.$swal('Weight cannot be blank')
                return
            }
            if(!this.shipping_cost){
                this.$swal('Shipping cost cannot be blank')
                return
            }
            if(!this.reorder_level){
                this.$swal('Reorder level cannot be blank')
                return
            }

             if(!this.image){
                this.$swal('Product image cannot be blank')
                return
            }

            //upload a file
const config = {
       headers: { 'content-type': 'multipart/form-data' }
   }
   //upload a file

            const res = await axios.post(this.$store.state.api_url+'api/new-product',{
                name : this.product_name,
                price : this.price,
                category : this.category,
                date_manufactured : this.date_manufactured,
                expiry_date : this.expiry_date,
                weight: this.weight,
                description : this.description,
                shipping_cost : this.shipping_cost,
                owner : this.business_id,
                production_cost : this.production_cost,
                reorder_level : this.reorder_level,
                image : this.image
            },config).then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.$swal('Product added to list')
            this.product_name = ''
            this.price = ''
            this.shipping_cost = ''
            this.weight = ''
            this.category = ''
            this.description = ''
            this.date_manufactured = ''
            this.expiry_date = ''
           
        },

        async product_categories(){
            const res  = await axios.get(this.$store.state.api_url+'api/product-categories').then(function(response){
                return response.data
            }).catch(function(error){
                console.log(error)
            })
            this.categories = res
        }
        
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        this.business_id = this.$route.params.business_id
        this.product_categories()
        
    }
}

</script>
