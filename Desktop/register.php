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



<!-- Mobile redirect script by https://pagecrafter.com -->
<script type="text/javascript" src="assets/js/redirection-mobile.js"></script><script type="text/javascript">// <![CDATA[
SA.redirection_mobile ({
 mobile_url : "m.onelookin.com",
tablet_redirection: "true",
tablet_host: "m.onelookin.com",
 });
 
// ]]>
</script>

<style>
body {
    background-image: url("../images/backgrounds/dust_scratches.png");

}

</style>


</head>




<body style="overflow: hidden;">
	<?php

	if(isset($_POST['registerButton'])) {
		echo '<script>
				$(document).ready(function() {
					$("#loginForm").hide();
					$("#registerForm").show();
				});
			</script>';
	}
	else {
		echo '<script>
				$(document).ready(function() {
					$("#loginForm").show();
					$("#registerForm").hide();
				});
			</script>';
	}

	?>

<div class="fakeModal">

	<div id="background">

		<div id="loginContainer">
			<div id="inputContainer">
			<h1 class="registerHeader">One Look In <span class="forCreators">for creators</span></h1>
			<br>
				<form id="loginForm" class="loginForm" action="register.php" method="POST">
					<h2>Login to your account</h2>
					<p>
						<input id="loginUsername" class="registerInput" name="loginUsername" type="text" placeholder="Email" value="<?php getInputValue('loginUsername') ?>" required autocomplete="off">
					</p>
					<p>
						<input id="loginPassword" class="registerInput" name="loginPassword" type="password" placeholder="Password" required>
					</p>
					<?php echo $account->getError(Constants::$loginFailed) . ""; ?>
					
					<button type="submit" class="registerSubmit" name="loginButton">Login</button>
<br>
					<div class="hasAccountText">
						<br>
						<span id="hideLogin">Don't have an account yet?<br> <span class="hoverPointer" style="color: #28a4af;">Signup here.</span></span>
					</div>

				</form>



				<form id="registerForm" class="loginForm" action="register.php" method="POST">
					<h2>Create your free account</h2>
					<p>
						<?php echo $account->getError(Constants::$usernameCharacters); ?>
						<?php echo $account->getError(Constants::$usernameTaken); ?>
						<input id="username" class="registerInput" name="username" type="text" placeholder="Username" value="<?php getInputValue('username') ?>" required>
					</p>


					<p>

						<?php echo $account->getError(Constants::$emailInvalid); ?>
						<?php echo $account->getError(Constants::$emailTaken); ?>
						<input id="email" class="registerInput" name="email" type="email" placeholder="Email" value="<?php getInputValue('email') ?>" required>
					</p>



					<p>
						<?php echo $account->getError(Constants::$passwordsDoNoMatch); ?>
						<?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
						<?php echo $account->getError(Constants::$passwordCharacters); ?>
						<input id="password" class="registerInput" name="password" type="password" placeholder="Password" required>
					</p>

					<p>
						<input id="password2" name="password2" class="registerInput" type="password" placeholder="Confirm Password" required>
					</p>

					<button type="submit" class="registerSubmit" name="registerButton">Sign Up</button>
<br>
					<div class="hasAccountText">
						<br>
						<span id="hideRegister">Already have an account?<br><span class="hoverPointer" style="color: #28a4af;"> Log in here.</span></span>
					</div>

				</form>


			</div>
		
	<a href="moreInfo.php" class="moreInfo">Learn More</a>
	<br><br>
	<a href="terms-of-service.pdf" class="moreInfo">Terms of Service</a>


</div>
	</div>
	<img src="assets/images/icons/onelookinlogo3.png" class="registerImage" alt="">
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
