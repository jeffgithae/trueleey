<template>
	<div class="container">
		<Header />
	<div>

		<div style="padding-top:20px;">
			<h6>Account details</h6>
		</div>

		<div class="row">
			<div class="col-md-4">


		<div class="table-responsive">
			<table class="table">

				<tbody>
					<tr> <th>Name</th> <td> {{full_name}} </td> </tr>
					<tr> <th>Email</th> <td> {{email}} </td> </tr>
					<tr> <th>Phone</th> <td> {{phone}} </td> </tr>
					<tr> <th>Address</th> <td> {{address}} </td> </tr>
					<tr> <th>Expertise</th> <td> {{expertise}} </td> </tr>
				</tbody>
				
			</table>
		</div>
				
			</div>

			<div class="col-md-4">

				<div class="table-responsive">
			<table class="table">

				<tbody>
					<tr> <th>Acc balance</th> <td> Ksh.{{balance}} </td> </tr>
					<tr> <th>Acc type</th> <td> <span class="badge bg-1">Personal</span> </td> </tr>
				</tbody>
				
			</table>
		</div>		
			</div>

			<div class="col-md-4"></div>


			<div class="col-md-4">

				<div style="padding-top:50px;">
			
<form @submit.prevent="change_password()">
<div class="form-floating">
  <input type="password" class="form-control"  placeholder="Password" v-model="password">
  <label for="floatingPassword">Password</label>
</div>

<div style="text-align:left; padding-top: 10px;">
	<button class="btn btn-1">Change password</button>
</div>

</form>
		</div>
				
			</div>


		</div>




		<div style="padding-top:50px;">
			<router-link to="/personal-accounts" class="btn text-danger"> <i class="bi bi-arrow-left"></i> Go back to accounts</router-link>
		</div>



	</div>

	</div>


	<Footer />


</template>


<script>
	import Header from './layouts/Header'
	import Footer from './layouts/Footer'
	import axios from 'axios'

import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

	export default{
		name : 'dashboard',
		components : {Header,Footer},
		data (){
			return {
				details : [],
				user_id : '',
				full_name : '',
				email : '',
				phone : '',
				address : '',
				expertise : '',
				balance : 0,
				password : ''
			}
		},

		methods : {
			async account_details(){
				const res = await axios.get(this.$store.state.api_url+'api/account-details/'+this.user_id).then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				this.details = res
				res.forEach((data)=>{
					this.full_name = data.fullName
					this.email = data.email
					this.phone = data.phoneNumber
					this.address = data.address
					this.expertise = data.expertise
					this.balance = data.balance
				})
			},

			async change_password(){
				if(!this.password){
					 toast.error("Password is required!", {
        autoClose: 3000,
        theme : 'dark',
        position:'top-center'
      })
					return
				}
				const res = await axios.post(this.$store.state.api_url+'api/change-account-password',{
					id : this.user_id,
					password : this.password
				}).then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				 toast.success("Password updated", {
        autoClose: 3000,
        theme : 'dark',
        position:'top-center'
      })
				 this.password = ''
			}
		},

		created(){
			this.user_id = this.$route.params.id
			this.account_details()
		}
	}

</script>
