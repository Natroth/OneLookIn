<?php 
include("../../config/config.php");
include("../../includes/classes/User.php");


$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);
$usersReturned = mysqli_query($con, "SELECT * FROM Users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");

if($query != "") {

	while($row = mysqli_fetch_array($usersReturned)) {
		$user = new User($con, $userLoggedIn);

		if($row['username'] != $userLoggedIn)
			$mutual_friends = $user->getMutualFriends($row['username']). " friends in common";
		else
			$mutual_friends = "";

		echo "<div class='resultDisplay'>
			<a href='profile.php?profile_username=" . $row['username']. "' style='color: #1485BD'>
			<div class='liveSearchProfilePic'>
				<img src='" . $row['profile_pic'] . "'>
			</div>

			 <div class='liveSearchText'>
			 <p>" . $row['username'] ."</p>
			 <p id='gray'>" . $mutual_friends ."</p> 
			 </div>
			 </a>
			 </div>";

	}

}

 ?>