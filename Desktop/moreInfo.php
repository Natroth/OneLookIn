<?php
	include("config/config.php");
	include("includes/classes/Account.php");
	include("includes/classes/Constants.php");

	$account = new Account($con);

	include("includes/form_handlers/register-handler.php");
	include("includes/form_handlers/login-handler.php");

	function getInputValue($name) {
		if(isset($_POST[$name])) {
			echo $_POST[$name];
		}
	}
?>

<html>
<head>

	<script src="https://kit.fontawesome.com/fa009d607d.js"></script>
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/icons/simplelogoturquoise2.png" />
	<title>OLI</title>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

</head>
<body style="overflow: hidden;">


<div class="fakeModal">

	<div id="background">

		<div id="loginContainer">
			<div id="inputContainer">
			<h1 class="registerHeader">One Look In <span class="forCreators">for creators</span></h1>
			<br>


		</div>
    </div>
    
    

</div>
</div>
        <div class="moreInfoMessage">
            Do you take your music seriously? Well you've come to the right place.<br><br>
          <span style="color: #28a4af;"> The premise is simple:</span><br>
             Here, you can create your profile and upload all the music you want.<br>
            Then, you send out your link to everyone you want to hear your song on their mobile devices.
            <br><br>
            Give 'em One Look In
            <br><br>
            <a href="register.php"><i class="fas fa-arrow-circle-left" style="color: #28a4af; font-size: 3rem;"></i></a>
        </div>

<style>

input {
	border: 1px solid #96a4af !important;
    font-size: 1.5rem;
    border-radius: 4px;
	padding: 3px;	
    font-family: brandon-grotesque, sans-serif;
	
}

input::-webkit-input-placeholder  {
	font-size: 1.5rem;
}

</style>

</body>
</html>
