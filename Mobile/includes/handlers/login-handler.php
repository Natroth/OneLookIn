<?php
if(isset($_POST['loginButton'])) {
	//Login button was pressed
	$username = $_POST['loginUsername'];
	$password = $_POST['loginPassword'];

	$result = $account->login($username, $password);

	if($result == true) {
		$getUname = mysqli_query($con, "SELECT username FROM users Where email = '$username'");
		$unameArray = mysqli_fetch_array($getUname);
		$uname = $unameArray['username'];

		$_SESSION['userLoggedIn'] = $uname;
		header("Location: index.php");
	}


}
?>
