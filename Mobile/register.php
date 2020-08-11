<?php
	include("includes/config.php");
	include("includes/classes/Account.php");
	include("includes/classes/Constants.php");

	$account = new Account($con);

	include("includes/handlers/register-handler.php");
	include("includes/handlers/login-handler.php");

	function getInputValue($name) {
		if(isset($_POST[$name])) {
			echo $_POST[$name];
		}
	}
?>

<html>
<head>

	<script src="https://kit.fontawesome.com/fa009d607d.js"></script>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	
<script type="text/javascript">


if (screen.width > 699) {
  document.location = "http://onelookin.com/index.php";
}

</script>	

</head>
<body>
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
	<a tabindex="0" href="browse.php"><i class="far fa-times-circle" style="font-size: 3rem; margin: 5rem !important;"></i></a>

<div class="fakeModal">

	<div id="background">

		<div id="loginContainer">

			<div id="inputContainer">
				<form id="loginForm" action="register.php" method="POST">
					<h2>Login to your account</h2>
					<p>
						<?php echo $account->getError(Constants::$loginFailed); ?>
						<input id="loginUsername" class="registerInput" name="loginUsername" type="text" placeholder="Email" value="<?php getInputValue('loginUsername') ?>" required autocomplete="off">
					</p>
					<p>
						<input id="loginPassword" class="registerInput" name="loginPassword" type="password" placeholder="Password" required>
					</p>

					<button type="submit" class="registerSubmit" name="loginButton">Login</button>
<br>
					<div class="hasAccountText">
						<br>
						<span id="hideLogin">Don't have an account yet? <span style="color: #28a4af;">Signup here.</span></span>
					</div>

				</form>



				<form id="registerForm" action="register.php" method="POST">
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
						<span id="hideRegister">Already have an account?<span style="color: #28a4af;"> Log in here.</span></span>
					</div>

				</form>


			</div>



		</div>
	</div>
</div>
</body>
</html>
