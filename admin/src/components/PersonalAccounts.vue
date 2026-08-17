<template>
	<div class="container">
		<Header />
	<div>

		<div style="padding-top:20px;">
			<h6>Personal accounts</h6>
		</div>
	<div class="table-responsive">
	<table class="table">
		<thead>
			<tr> <th> ID# </th> <th>Name</th> <th>Email</th> <th>Phone</th> <th>Acc balance</th> <th></th> </tr>
		</thead>


		<tbody v-for="account in accounts">
			<tr> <td> {{account.id}} </td> <td> {{account.fullName}} </td> <td> {{account.email}} </td> <td> {{account.phoneNumber}} </td> <td> Ksh.{{account.balance}} </td> <td>
<div class="btn-group">
  <button type="button" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
    <i class="bi bi-three-dots"></i>
  </button>
  <ul class="dropdown-menu">
    <li><router-link :to="{name:'view-personal-account',params :{id:account.id}}" class="dropdown-item"> <i class="bi bi-eye"></i> View account</router-link></li>
     <li><button @click="delete_account(account.id)" class="dropdown-item"> <i class="bi bi-x"></i> Delete account</button></li>
  </ul>
</div> </td> </tr>
		</tbody>
	</table>


<div style="text-align:center;" v-if="accounts.length==0">
	<div class="spinner-border text-warning" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
</div>


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
				accounts : []
			}
		},

		methods : {
			async personal_accounts(){
				const res = await axios.get(this.$store.state.api_url+'api/personal-accounts').then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				this.accounts = res
			},

			async delete_account(id){
				const res = await axios.post(this.$store.state.api_url+'api/delete-account',{
					id : id 
				}).then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				 toast.success("Account deleted", {
        autoClose: 3000,
        theme : 'dark',
        position:'top-center'
      })
				this.personal_accounts()
			}
		},

		created(){
			this.personal_accounts()
		}
	}

</script>
