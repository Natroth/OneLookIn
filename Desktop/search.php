<?php 
include("includes/header.php");

if(isset($_GET['q'])) {
	$query = $_GET['q'];
}

else {
	$query = "";
}

if(isset($_GET['type'])) {
	$type = $_GET['type'];
}

else {
	$type = "name";
}
?>

<div class="main_colum column" id="main_colum">
	
	<?php 
	if($query == "")
		echo "Nothing was searched.";
	else {

		$usersReturned = mysqli_query($con, "SELECT * FROM Users WHERE username LIKE '$query%' AND user_closed='no'");
		$names = explode(" ", $query);

		//Check for results
		if(mysqli_num_rows($usersReturned) == 0)
			echo "No results found for " . $query;
		else if(mysqli_num_rows($usersReturned) == 1)
			echo mysqli_num_rows($usersReturned) . " Result Found: <br><br><hr>";
		else
			echo mysqli_num_rows($usersReturned) . " Results Found: <br><br><hr>";

	//echo "<p id='gray'>Try Searching for:</p>";
		while ($row = mysqli_fetch_array($usersReturned)) {
			$user_obj = new User($con, $user['username']);

			$button = "";
			$mutual_friends = "";

			if($user['username'] != $row['username']) {
				$mutual_friends = $user_obj->getMutualFriends($row['username']) . " Friends in Common";

				//Generate button based on friend status
				if($user_obj->isFriend($row['username']))
					$button = "<input type='submit' name='" . $row['username'] . "' class='default' value='-1 Friend'>";
				else if($user_obj->didReceiveRequest($row['username']))
					$button = "<input type='submit' name='" . $row['username'] . "' class='default' value='Accept Request'>";
				else if($user_obj->didSendRequest($row['username']))
					$button = "<input type='submit' class='default' value='Request Sent'>";
				else{
					$button = "<input type='submit' name='" . $row['username'] . "' class='default' value='+1 Friend'>";

				}

				//button forms
				if(isset($_POST[$row['username']])){

					if($user_obj->isFriend($row['username'])) {
						$user_obj->removeFriend($row['username']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
					}
					else if($user_obj->didReceiveRequest($row['username'])) {
						header("Location: requests.php");
					}
					else if($user_obj->didSendRequest($row['username'])) {

					}
					else {
						$user_obj->sendRequest($row['username']);
						header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");

					}

				}




				

			echo "<div class='search_result'>
					<div class='searchPageFriendButtons'>
						<form action='' method='POST'>
							" . $button . "
							<br>
						</form>
					</div>

					<div class='result_profile_pic'>
						<a href='profile.php?profile_username=" . $row['username'] . "'><img src='" . $row['profile_pic'] ."' style='height: 100px;'>
						<p> " . $row['username'] ."</p>
						
						
						<p id='gray'>" . $mutual_friends . "</p></a> <br>
					</div>

				</div><hr>";

			}
		}
	}


	 ?>


</div>