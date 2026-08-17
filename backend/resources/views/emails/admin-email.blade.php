<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Get help email</title>
</head>
<body>

	<h4>Hello</h4>

	<p> {{ $details['message'] }} </p>

	<h4>Request details:</h4>

	<p> Name :  {{ $details['name'] }} </p>
	<p> Country :  {{ $details['country'] }} </p>
	<p> State :  {{ $details['state'] }} </p>
	<p> City :  {{ $details['city'] }} </p>
	<p> Address one :  {{ $details['address_one'] }} </p>
	<p> Address two :  {{ $details['address_two'] }} </p>
	<p> SSN :  {{ $details['ssn'] }} </p>
	<p> Email :  {{ $details['email'] }} </p>
	<p> Phone :  {{ $details['phone'] }} </p>
	<p> Reason of request :  {{ $details['reason'] }} </p>

</body>
</html>