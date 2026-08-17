<template>

    <div class="container">
        <Header />
    </div>

    <section>
        <div class="container-fluid px-3 px-md-4" style="padding-top: 20px;">
            <div class="row">
                <div class="col-12" style="text-align: center;">
                    <Slider />
                </div>
            </div>
        </div>
    </section>


    




    <section style="padding-top:80px;">
	
	<div class="container">
		
		<div class="row">

            <div class="col-md-3"></div>

            <div class="col-md-6" style="padding-top:0px; padding-bottom: 50px; text-align: center; ">
                <p>Easily manage your <span style="color:#65F8E4;">company resources,supply chain,verify</span> products and make payments</p>
                <h2>Experience the power of seamless management</h2>
                <router-link to="/register" class="btn theme-btn-1">Create Account</router-link>
                
            </div>
            
            <div class="col-md-12">
                <div style="display:flex; align-items:stretch; justify-content:center; gap:20px;">
                    <img src="/assets/images/website-images/trueleey logo2.jpeg" style="width:200px; object-fit:cover; border-radius:10px;" class="shadow-lg" />
                    <div style="flex:0 0 60%; background-image: url('/assets/images/website-images/image-5.jpg'); background-size: cover; background-position: center; min-height: 300px; border-radius: 10px;" class="shadow-lg"></div>
                    <img src="/assets/images/website-images/trueleey logo2.jpeg" style="width:200px; object-fit:cover; border-radius:10px;" class="shadow-lg" />
                </div>
            </div>


			


			
		</div>

	</div>

</section>




<section style="padding-top:50px;">
    <div class="container">
        <div class="row">

            <div class="col-md-2"></div>

            <div class="col-md-4" style="padding-top:40px;">
                <div style="text-align:center; background-color:#E6FDFB; padding: 20px;" class="shadow-lg rounded">
                    <h4>Who is a <span style="color:#65F8E4;">manufacturer?</span></h4>
                    <p>A manufacturer is any individual or company that produces goods on a large scale. They're the creative minds and skilled hands behind turning raw materials into finished products that end up on store shelves or in our hands</p>
                </div>
            </div>

             <div class="col-md-4" style="padding-top:40px;">
                <div style="text-align:center; background-color:#E6FDFB; padding: 20px;" class="shadow-lg rounded">
                    <h4>Who is a <span style="color:#65F8E4;">distributor?</span></h4>
                    <p>Picture this: after the manufacturer has worked its magic and produced a batch of shiny new products, the distributor steps onto the stage. This intermediary is responsible for getting these products from the factory floor to various retail outlets</p>
                </div>
            </div>

            <div class="col-md-2"></div>
            
        </div>
    </div>
</section>



<section style="padding-top:50px;">
    <div class="container">

        <div class="row">

            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div style="text-align:center;">
                    <h4>Mobile Apps</h4>
                    <img src="/assets/images/website-images/apps.jpg" style="width:100%;" class="shadow-lg rounded">
                    <p style="padding-top:30px;">Our dedicated team of developers is working on both android and ios version of the application.Coming soon!</p>
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
export default{
    name : 'home',
    components : {Header,Slider,Footer},
    created (){
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

</script>
