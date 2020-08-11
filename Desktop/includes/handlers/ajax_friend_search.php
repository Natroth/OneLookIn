<?php 
include("../../config/config.php");
include("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);
 
	$usersReturned = mysqli_query($con, "SELECT * FROM Users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");


if($query != "") {
	while($row = mysqli_fetch_array($usersReturned)) {

		$user = new User($con, $userLoggedIn);

		if($row['username'] != $userLoggedIn) {
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		}
		else {
			$mutual_friends = "";
		}

		if($user->isFriend($row['username'])) {
			echo "<div class='resultDisplay'>
					<a href='messages.php?u=" . $row['username'] . "' style='color: #000'>
						<div class='liveSearchProfilePic'>
						<img src='" . $row['profile_pic'] . "'>
						</div>

						<div class='liveSearchText'>
							".$row['username'] . "
							<p id='gray'>".$mutual_friends . "</p>
							</div>
						</a>
					</div>";
		}


	}
}

 ?>