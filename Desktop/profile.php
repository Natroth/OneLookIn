<?php
ob_start();
include("includes/header.php");

					$message_obj = new Message($con, $userLoggedIn);


if(isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM Users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query); 

	$num_friends = (substr_count($user_array['friend_array'], ",")) -1;

}

if(isset($_POST['remove_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->removeFriend($username);
}

if(isset($_POST['add_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->sendRequest($username);
}

if(isset($_POST['respond_request'])) {
	header("Location: requests.php");
}

if(isset($_POST['post_message'])) {
	if(isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($username, $body, $date);

	}

	$link = '#profileTabs a[href="#messages_div"]';
	echo "<script>
			$(function() {
				$('" . $link . "').tab('show');
					});
					</script>";
}


 ?>


<style type="text/css">

	.wrapper {
	margin-left: 40px;
	 

}

</style>

<div class="profile_left">
	<img src="<?php echo $user_array['profile_pic']; ?>">

<div class="profile_info">
	<p><?php echo "Songs: " . $user_array['num_songs']; ?></p>
	<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
	<p><?php echo "Friends: " . $num_friends; ?></p>
</div>




<form method="POST">
	<?php

	$profile_user_obj= new User($con,$username);
	if($profile_user_obj->isClosed()){
		header("Location: user_closed.php");
	}

		$logged_in_user_obj = new User($con, $userLoggedIn);

		if($userLoggedIn != $username) {

			if($logged_in_user_obj->isFriend($username)) {
				echo '<input type="submit" name="remove_friend" class="default" value="-1 Friend"><br>';
			}
			else if ($logged_in_user_obj->didReceiveRequest($username)) {
				echo '<input type="submit" name="respond_request" class="default" value="Respond to Request"><br>';				
			}
			else if ($logged_in_user_obj->didSendRequest($username)) {
				echo '<input type="submit" name="" class="default" value="Request Sent"><br>';				
			}
			else 
				echo '<input type="submit" name="add_friend" id="add_friend" class="default" value="+1 Friend"><br>';		
		}
	
			if($userLoggedIn != $username) {
				echo '<div class="profile_info_bottom">';
					echo $logged_in_user_obj->getMutualFriends($username) . " Mutual friends ";
					echo '</div>';
			}

	?>

</form>


</div>



	<ul class="nav nav-tabs" role="tablist" id="profileTabs">
	  <li role="presentation" class="active shape"><a href="#newsfeed_div" aria-controls="newsfeed_div" role="tab" data-toggle="tab" style="font-size: 20px;color: #e8e8e8;text-decoration: none;">Songs</a></li>
	  <li role="presentation" class="shape"><a href="#about_div" aria-controls="about_div" role="tab" data-toggle="tab" style="font-size: 20px;color: #e8e8e8;text-decoration: none;">About</a></li>
	  <li role="presentation" class="shape"><a href="#messages_div" aria-controls="messages_div" role="tab" data-toggle="tab" style="font-size: 20px;color: #e8e8e8;text-decoration: none;">Message</a></li>
	</ul>


<div class= "main_column_profile">

	<div class="tab-content">

		<div role="tabpanel" class="tab-pane fade in active" id ="newsfeed_div">

 <?php echo "<h4><a href='" . $username ."' style='font-size: 24;'></a>" . $profile_user_obj->getUsername() . "'s Songs" . "</h4><hr><br>"; ?>

			 <div class="posts_area"></div>
			 <center><img id="loading" src= "assets/images/icons/loading.gif"> </center>

		</div>


		<div role="tabpanel" class="tab-pane fade" id ="about_div" style="font-size: 19px;">

		<?php 

if(isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM Users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query); 

}
echo "<h4><a href='" . $username ."' style='font-size: 24;'></a>" . $profile_user_obj->getUsername() . "'s Bio" . "</h4><hr><br>";
	if($userLoggedIn != $username) {
		if ($user_array['about'] == "") {
			echo "<center><h4>Nothing to Show!</h4></center>";
		}
		else {
			echo $user_array['about'];
		}
}
	else {
		 
		 	echo $user_array['about'];

			echo "<br><center><a href='about.php'><br> Want to Upload or Change your Bio? Click Here!</a></center>";
		

				}	

		 ?>


		</div>



		<div role="tabpanel" class="tab-pane fade" id ="messages_div">

					<?php 


						echo "<h4>You and <a href='" . $username ."' style='font-size: 24;'>" . $profile_user_obj->getUsername() . "</a></h4><hr><br>";
						echo "<div class='loaded_messages' id='scroll_messages'>";
							echo $message_obj->getMessages($username);
						echo "</div>";


					 ?>
		<hr>
					 <div class="message_post">
					 	<form action="" method="POST">
					 		
					
					 		<textarea name='message_body' id='message_textarea' placeholder=' . . . '></textarea>
					 		<input type='submit' name='post_message' class='info' id='message_submit' value='^' style="background-color: #f8f8f8">
					 		
					 

					 		 
						</form>


					 </div>

				 <script>
				 	var div = document.getElementById("scroll_messages");
				 	if(div != null) {
				 	div.scrollTop = div.scrollHeight;
				 }
				</script>



		</div>




		</div>



</div>
		


		
	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	var profileUsername = '<?php echo $username; ?>';

	$(document).ready(function() {

		$('#loading').show();

		//Loading first posts
		$.ajax ({
			url: "includes/handlers/ajax_load_profile_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
			cache:false,

			success: function(data) { 
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function() {
			var height =  $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop +window.innerHeight) && noMorePosts =='false') {
				$('#loading').show();

			
				var ajaxReq = $.ajax ({
					url: "includes/handlers/ajax_load_profile_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
					cache:false,

					success: function(response) { 
						$('.posts_area').find('.nextPage').remove(); //removes current .nextpage						
						$('.posts_area').find('.noMorePosts').remove(); //removes current .nextpage

						$('#loading').hide();
						$('.posts_area').append(response);
					}
		});




			} //End if

			return false;


		}); //End (window).scroll(function())




	});
	</script>



</div>
</body>
</html>