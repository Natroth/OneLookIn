<?php 
include("includes/header.php"); 

if(isset($_POST['cancel'])) {
	header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
	$close_query = mysqli_query($con, "UPDATE Users SET user_closed='yes' WHERE username='$userLoggedIn'");
	session_destroy();
	header("Location: register.php");
}

 ?>

<div class="main_colum column">

	<h4>Close Account</h4>

	Are you sure you want to close this account?<br>
	You may re-open this account simply by logging in.<br><hr>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="close_account" value="Yes" class="default">
		<input type="submit" name="cancel" id="cancel" value="Nevermind!" class="default" style="margin-left: 10px;">
		
	</form>
	
</div>