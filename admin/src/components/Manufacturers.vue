<template>
	<div class="container">
		<Header />
	<div>

		<div style="padding-top:20px;">
			<h6>Manufacturers</h6>
		</div>
	<div class="table-responsive">
	<table class="table">
		<thead>
			<tr> <th> ID# </th> <th>Name</th> <th>Email</th> <th>Location</th> </tr>
		</thead>


		<tbody v-for="account in accounts">
			<tr> <td> {{account.id}} </td> <td> {{account.name}} </td> <td> {{account.email}} </td> <td> {{account.location}} </td>  </tr>
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
			async fetch_manufacturers(){
				const res = await axios.get(this.$store.state.api_url+'api/fetch-manufacturers').then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				this.accounts = res
			},

		
		},

		created(){
			this.fetch_manufacturers()
		}
	}

</script>
