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


		$data_query = mysqli_query($con, "SELECT * FROM posts WHERE deleted= 'no' AND id= '$post_id'");
		$row= mysqli_fetch_array($data_query);
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$user_to = $row['user_to'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];
				$genre = $row['genre'];
		
				$body = strip_tags($body); //removes html tags
				$body = mysqli_real_escape_string($con, $body);
			$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces	
				

	 //share button
	 if(isset($_POST['share_button'])) {
	 
	 
	 		$data_query = mysqli_query($con, "SELECT * FROM posts WHERE deleted= 'no' AND id= '$post_id'");
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$user_to = $row['user_to'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];
				$genre = $row['genre'];
				
				$body = strip_tags($body); //removes html tags		
				$body = mysqli_real_escape_string($con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces		
	 
	 	$insert_post = mysqli_query($con, "INSERT INTO posts VALUES ('', '$body', '$userLoggedIn', '$added_by', '$date_time', 'no', 'no', '0','$songPath', '$genre', '$post_id')");


	 	}

	 

	 //check for previous share
	 $check_query = mysqli_query($con, "SELECT * FROM posts WHERE added_by='$userLoggedIn' AND repost_id='$post_id'");
	 $num_rows = mysqli_num_rows($check_query);

	 if($num_rows > 0) {
	 	echo '<form action="share.php?post_id=' . $post_id . '" method="POST">
	 			<p class="comment_like" name="shared_button" data-toggle="tooltip" title="Shared" style="background-color: #fff; width: 60px; color: #696969; font-size: 26px;">&#x2713 </p>

	 			</form> ';
	 

	 }
	 else {
	 	echo '<form action="share.php?post_id=' . $post_id . '" method="POST">
	 			<input type="submit" class="comment_like" name="share_button" value="  &#8631;"  data-toggle="tooltip" title="Share" style="background-color: #fff; width: 74px; color: #696969; font-size: 26px;  margin-left: -19px;"">

	 			</form> ';

	 		 }

	 ?>
	 <head>
	 		<link rel="stylesheet" type="text/css" href="assets/css/style_2.css">
</head>
<style type="text/css">
	body {
		background-color: #fff;
	}
	form {
	position: absolute;
	top: 0;
}



</style>



</body>
