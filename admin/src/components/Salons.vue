<template>
	<div class="container">
		<Header />
	<div>

		<div style="padding-top:20px;">
			<h6>Salons</h6>
		</div>
	<div class="table-responsive">
	<table class="table">
		<thead>
			<tr> <th> ID# </th> <th>Name</th> <th>Email</th> <th>Location</th> </tr>
		</thead>

		<tbody v-for="account in accounts" :key="account.id">
			<tr> <td> {{account.id}} </td> <td> {{account.name}} </td> <td> {{account.email}} </td> <td> {{account.location}} </td> </tr>
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

	export default{
		name : 'salons',
		components : {Header,Footer},
		data (){
			return {
				accounts : []
			}
		},
		methods : {
			async get_salons(){
				const res = await axios.get(this.$store.state.api_url+'api/get-salons').then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				this.accounts = res
			}
		},
		created(){
			this.get_salons()
		}
	}
</script>
