<?php 
	require 'config/config.php';
	include("includes/classes/User.php");
	include("includes/classes/Post.php");
	include("includes/classes/Notification.php");



	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM Users WHERE username= '$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);

	}
	else {
		header("Location: register.php");
	}

 
	 //get id of post
	 if(isset($_GET['post_id'])) {
	 	$post_id = $_GET['post_id'];
	 }


	 if(isset($_POST['fave_button'])) {
	 	
	 	$insert_user = mysqli_query($con, "INSERT INTO faves VALUES('', '$userLoggedIn', '$post_id')");


	 }

	 //unfave button
	 if(isset($_POST['unfave_button'])) {

	 	$insert_user = mysqli_query($con, "DELETE FROM faves WHERE username='$userLoggedIn' AND post_id='$post_id'"); 

	 	
	 }


	 $check_query = mysqli_query($con, "SELECT * FROM faves WHERE username='$userLoggedIn' AND post_id='$post_id'");
	 $num_rows = mysqli_num_rows($check_query);

	 if($num_rows > 0) {
	 	echo '<form action="fave.php?post_id=' . $post_id . '" method="POST">
	 			<input type="submit" class="comment_unfave" name="unfave_button" value="*" alt="submit"  data-toggle="tooltip" title="Remove Save">
	 
	 			</form> ';
	 

	 }
	 else {
	 	echo '<form action="fave.php?post_id=' . $post_id . '" method="POST">
	 			<input type="submit" class="comment_fave" name="fave_button" value="*" alt="submit"  data-toggle="tooltip" title="Save">
	 			</form> ';

	 		 }

	 ?>
<head>

	 		<link rel="stylesheet" type="text/css" href="assets/css/style_2.css">
</head>

