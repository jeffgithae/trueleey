<template>

    <div class="container">
        <Header />
    </div>

    
    <section style="padding-top: 50px;">

        <div class="container">

            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">

                    <div style="text-align:center;">

                        <h1 class="text-danger" style="font-size: 60px;"> <i class="bi bi-exclamation"></i> </h1>

                        <p> {{this.$store.state.message}} </p>

                        <div>
                            <router-link to="/dashboard" class="btn"> <i class="bi bi-arrow-left"></i> Go back</router-link>
                        </div>

                        
                    </div>

                </div>
                <div class="col-md-4"></div>
            </div>

        </div>

    </section>




<Footer />
</template>

<script>
import Header from './layouts/Header'
import Slider from './layouts/Slider'
import Footer from './layouts/Footer'
import axios from 'axios'
export default{
    name : 'home',
    components : {Header,Slider,Footer},
    data () {
        return { 
            
        }
    },
    methods : {
    
    },
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

</script>
