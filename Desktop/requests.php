<?php 
include("includes/header.php");
?>

<div class="main_column request_column" id="main_column">

	<h4>Friend Requests</h4>

	<?php 

	$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
	if(mysqli_num_rows($query) == 0)
		echo "No Friend Requests to Show";
	else {

		while($row = mysqli_fetch_array($query)) {
			$user_from = $row['user_from'];
			$user_from_obj = new User($con, $user_from);


		$profile_pic_query = mysqli_query($con, "SELECT profile_pic FROM Users WHERE username= '$user_from'");
	 	while ($row = mysqli_fetch_array($profile_pic_query)) {
	 	$profile_pic = $row['profile_pic'];
	 	$profile_pic_obj = new User($con, $profile_pic);



				echo "<img src= '$profile_pic' width='50px' style=' border-radius: 15px;'>" . "<a href= '$user_from'> $user_from </a>"  . " sent you a friend request!";









			$user_from_friend_array = $user_from_obj->getFriendArray();

			if(isset($_POST['accept_request' . $user_from])) {
				$add_friend_query = mysqli_query($con, "UPDATE Users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
				$add_friend_query = mysqli_query($con, "UPDATE Users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

				$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
				echo "Request Accepted!";
				header("Location: requests.php");

			}

			if(isset($_POST['ignore_request' . $user_from])) {
				$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
				echo "Request Ignored";
				header("Location: requests.php");				
			
			}

?>

	 <form action ="requests.php" method="POST">
	 	<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value= "Accept">
	 	<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value= "Ignore">
	 	
	 </form>
<hr>
<?php


		}
	}
}

	 ?>
	



</div>