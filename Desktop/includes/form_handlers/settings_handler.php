<?php 
if(isset($_POST['update_details'])) {
	$email = $_POST['email'];

	$email_check = mysqli_query($con, "SELECT * FROM Users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if($matched_user == "" || $matched_user == $userLoggedIn) {
		$message = "Email updated<br><br>";

		$query = mysqli_query($con, "UPDATE Users SET email='$email' WHERE username='$userLoggedIn'");
	}
	else 
		$message = "Email already in use!<br><br>";

}
	
else 
	$message = "";

//***************************************************

if(isset($_POST['update_password'])) {

	$old_password = strip_tags($_POST['old_password']);
	$new_password_1 = strip_tags($_POST['new_password_1']);
	$new_password_2 = strip_tags($_POST['new_password_2']);

	$password_query = mysqli_query($con, "SELECT password FROM Users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['password'];

	if(md5($old_password) == $db_password) {

		if($new_password_1 == $new_password_2) {


			if(strlen($new_password_1) < 5) {
				$password_message = "Your password mut be more than 4 characters<br><br>";
			}
			else {
				$new_password_md5 = md5($new_password_1);
				$password_query = mysqli_query($con, "UPDATE Users SET password='$new_password_md5' WHERE username='$userLoggedIn'");
				$password_message = "Password succesfully changed!<br><br>";
			}

		}
		else {
			$password_message = "New passwords don't match<br><br>";
		}
	} 
	else {
		$password_message = "Old password is incorrect <br><br>";
	}
}
else {
	$password_message = "";
}


if (isset($_POST['close_account'])) {
	header("Location: close_account.php");
}

 ?>