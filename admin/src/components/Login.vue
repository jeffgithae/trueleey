<template>

<section>

<div class="container">

<div class="row">
<div class="col-md-4"></div>

<div class="col-md-4">

<div style="padding-top: 100px;">

<div style="text-align: center;">
<img src="/assets/images/trueleey logo.png" style="height: 70px; padding-bottom: 20px;">
<h6>Admin login</h6>
</div>

<form @submit.prevent="login()">
	
	<div class="form-floating mb-3">
  <input type="text" class="form-control"  placeholder="Username" v-model="username">
  <label for="floatingInput">Username</label>
</div>
<div class="form-floating">
  <input type="password" class="form-control"  placeholder="Password" v-model="password">
  <label for="floatingPassword">Password</label>
</div>

<div style="text-align:center; padding-top: 20px;">
	<button class="btn btn-1">Login</button>
</div>

</form>


</div>

</div>


<div class="col-md-4"></div>


</div>

</div>

</section>

<Footer />

</template>




<script>
import Footer from './layouts/Footer'
import axios from 'axios'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

export default {
name : 'login',
components : {Footer},

data (){
	return {
		username : '',
		password : ''
	}
},

methods : {
	async login(){

		if(!this.username){
	  toast.error("Username is required!", {
        autoClose: 3000,
        theme : 'dark',
        position:'top-center'
      })
      return
		}

		if(!this.password){
	  toast.error("Password is required!", {
        autoClose: 3000,
        theme : 'dark',
        position:'top-center'
      })
      return
		}

		const res = await axios.post(this.$store.state.api_url+'api/admin-login',{
			username : this.username,
			password : this.password
		}).then(function(response){
			return response.data
		}).catch(function(error){
			console.log(error)
		})
		if(res=='success'){
			this.$store.commit('LOGIN', this.username)
			this.$router.push('/dashboard')
		}else{
			toast.error("Invalid username or password!", {
        autoClose: 3000,
        theme : 'dark',
        position:'top-center'
      })
		}
	}
}

}
	
</script>





