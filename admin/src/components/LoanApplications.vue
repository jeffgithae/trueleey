<template>
	<div class="container">
		<Header />
	<div>

		<div style="padding-top:20px;">
			<h6>Loan applications</h6>
		</div>
	<div class="table-responsive">
	<table class="table">
		<thead>
			<tr> <th> ID# </th> <th>Amount</th> <th>Date borrowed</th> <th>Due date</th> <th>Name</th> <th>Email</th> <th>Phone</th> <th>Approved</th> <th>Paid</th> </tr>
		</thead>


		<tbody v-for="loan in loans">
			<tr> <td> {{loan.id}} </td> <td> Ksh.{{loan.amount}} </td> <td> {{loan.created}} </td> <td> {{loan.due_date}} </td> <td> {{loan.full_name}} </td> <td>{{loan.email}}</td> <td>{{loan.phone}}</td> <td>
				<div class="btn-group">
  <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
    <span v-if="loan.approved==1" class="text-success"> <i class="bi bi-check-circle"></i> Yes</span>
    <span v-if="loan.approved==0" class="text-danger"> <i class="bi bi-x-circle"></i> No</span>
  </button>
  <ul class="dropdown-menu">
  	<li><button @click="approve_loan(loan.id,loan.approved,loan.user_id,loan.amount)" class="dropdown-item"> <i class="bi bi-check icon"></i> Change to yes</button></li>
  	<li><button @click="approve_loan(loan.id,loan.approved,loan.user_id,loan.amount)" class="dropdown-item"> <i class="bi bi-x text-danger"></i> Change to no</button></li>
    
  </ul>
</div>
			</td>

			<td>
			 <span v-if="loan.paid==1" class="text-success">  Yes</span>
    <span v-if="loan.paid==0" class="text-danger"> No</span>	
			</td>

			  </tr>
		</tbody>
	</table>


<div style="text-align:center;" v-if="loans.length==0">
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
				loans : []
			}
		},

		methods : {
			async loan_applications(){
				const res = await axios.get(this.$store.state.api_url+'api/loan-applications').then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				this.loans = res
			},


			async approve_loan(id,status,user_id,amount){
				const res = await axios.post(this.$store.state.api_url+'api/approve-loan',{
					id : id,
					status : status,
					user_id : user_id,
					amount: amount
				}).then(function(response){
					return response.data
				}).catch(function(error){
					console.log(error)
				})
				this.loan_applications()
			},

		
		},

		created(){
			this.loan_applications()
		}
	}

</script>
