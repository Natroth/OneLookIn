<style type="text/css">
	* {
		font-size: 16px;
		font-family: Raleway-Regular; 
	}


</style>


<?php 
class Post {
	private $user_obj;
	private $con;




	public function __construct($con, $user){
			$this->con = $con;
			$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to, $songName, $genre, $picName, $duration, $collection) {
		$body = strip_tags($body); //removes html tags
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces

        $songName = 'http://onelookin.com/' . $songName;


		if($check_empty != "") {

				//Current date and time
				$date_added = date("m/d/Y");
				//Get username
				$added_by = $this->user_obj->getUsername();

                $artistId = $this->user_obj->getArtistId();

				//If user is on own profile, user_to is 'none'
				if($user_to == $added_by) {
					$user_to = "none";
					}

					//insert into collections
					$collectionCheck = mysqli_query($this->con, "SELECT id FROM collections WHERE added_by = '$added_by' AND name = '$collection'");
					$row2 = mysqli_fetch_array($collectionCheck);
					$collectionId = $row2['id'];
					if ($collectionId == "") {
					$colletionQuery = mysqli_query($this->con, "INSERT INTO collections VALUES ('', '$added_by', '$collection')");

					}

					//check collection pt 2
					$collectionCheck2 = mysqli_query($this->con, "SELECT id FROM collections WHERE added_by = '$added_by' AND name = '$collection'");
					$row3 = mysqli_fetch_array($collectionCheck2);
					$collectionId = $row3['id'];

					//insert post
					$query = mysqli_query($this->con, "INSERT INTO posts VALUES ('', '$body', '$added_by', '$artistId', '$user_to', '$date_added', 'no', 'no', '0', '$songName', '$genre', '', '$picName', '$duration', '$collectionId', '')");
					$returned_id = mysqli_insert_id($this->con);


					//insert notification
					if($user_to != 'none') {
						$notification = new Notification($this->con, $added_by);
						$notification->insertNotification($returned_id, $user_to, "like");
					}


					//update song count for user
					$num_songs = $this->user_obj->getNumSongs();
					$num_songs++;
					$update_query = mysqli_query($this->con, "UPDATE users SET num_songs= '$num_songs' WHERE username= '$added_by'");
					header("Location: upload_reciept.php?id=" . $returned_id);

		}

	}


	public function loadPostsFriends($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted= 'no' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$user_to = $row['user_to'];
				$date_time = $row['date_added'];
				$songPath = $row['path'];
				$genre = $row['genre'];
				$repost_id = $row['repost_id'];

				//prepare user_to string so it can be included when not posted to a user 

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)) {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];
					
								$user_to_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$user_to'");					
					$user_to_row = mysqli_fetch_array($user_to_details_query);
					$user_to = $user_to_row['username'];
					$to_profile_pic = $user_to_row['profile_pic'];
					


	

					?>
					<script>
						
						
$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
							
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				


				if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}
				
				if($user_to == "") { 

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> 
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 <div class='genre_tag' style='color:#95a4af; float:right; margin-right:21%;'> @$genre</div>
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br>
								$songDiv
								

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;

								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
								<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>							
							</div>


						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
						}
					
					
				else {
				
					$repost_details_query = mysqli_query($this->con, "SELECT * FROM posts WHERE id='$repost_id'");
					$repost_row = mysqli_fetch_array($repost_details_query);
					$re_id = $repost_row['id'];
					$re_body = $repost_row['body'];
					$re_added_by = $repost_row['added_by'];
					$re_user_to = $repost_row['user_to'];
					$re_date_time = $repost_row['date_added'];
					$re_songPath = $repost_row['path'];
					$re_genre = $repost_row['genre'];
													
				
								$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#96a4af;'>
								 $username  >

								 <a href= '$user_to'> $user_to </a>
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 <div class='genre_tag' style='color:#95a4af; float:right; margin-right:21%;'> @$genre</div>
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br>
								$songDiv
								

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$re_id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$re_id' style='width: 50px; margin-left: -115%;' scrolling='no'></iframe>		
							<div class='fave_frame'><iframe src='fave.php?post_id=$re_id' scrolling='no' style='margin-top: -39px;'></iframe></div>
						</div>														
							</div>	
					
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$re_id' id='comment_iframe'></iframe>
						</div>
						<hr>";
						}
				}		
					

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();


								});


							});


						});

					</script>


					<?php
			}



			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts to show, Go add some friends! </p>";

		}


	echo $str;

	}
	
	
	public function loadFavePosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return

		$fave_query = mysqli_query($this->con, "SELECT * FROM faves WHERE username='$userLoggedIn'");

		while ($row = mysqli_fetch_array($fave_query)) {
		$saved_post_id = $row['post_id'];


		if(mysqli_num_rows($fave_query) >0) {

		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted= 'no' AND id='$saved_post_id' AND user_to='none' ORDER BY id DESC");
			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];
				$genre = $row['genre'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
						
						
	$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
	
						
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								<div class='genre_tag' style='color:#95a4af; float:right; margin-right:21%;'> @$genre</div>
								 $delete_button
							</div>
							
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}
}
					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}

}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
		

	echo $str;

	}


	public function loadGlobalPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted= 'no' AND user_to='none' ORDER BY likes DESC");


		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$user_to = $row['user_to'];	
				$date_time = $row['date_added'];
				$songPath = $row['path'];
				$genre = $row['genre'];
                $picPath = $row['picture'];
                

				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> 
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								<div class='genre_tag' style='color:#95a4af; float:right; margin-right:21%;'> @$genre</div>

								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
								<img style='width: 30px;' src= $picPath >

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																	
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
											}
					

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}

	if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";


		}
			

	echo $str;

	}

	public function loadPopPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'pop') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>									
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>							
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
					
		
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}
			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}

	public function loadRockPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'rock')  AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
					
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>		
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>															
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
					
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
					
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}



		}
	
				if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}	

	public function loadHipHopPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'hip-hop') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
						
						$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>			
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>														
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
							
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}



		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}

	public function loadElectricPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'electric') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																	
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}		
	
		public function loadFolkPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'folk') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}		
		public function loadAcousticPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'acoustic') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['path'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>		
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>															
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}	
	
			public function loadRandBPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'r&b') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}



		}
		
			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}		

		public function loadJazzPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'jazz') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}	
	
			public function loadClassicalPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'classical') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}	

	public function loadOtherPosts($data, $limit) {

		$page= $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page -1) * $limit;



		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE (deleted= 'no' && genre= 'other') AND user_to='none' ORDER BY id DESC");


		if(mysqli_num_rows($data_query) >0) {


			$num_iterations = 0; //number resulrs checked
			$count = 1;


			while ($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];

				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn); {


					if($num_iterations++ < $start)
						continue;


					//once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}

					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
					
					$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

								if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> $user_to
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br><br>
								$songDiv
							

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
				
					}

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>


					<?php
			}




		}

			if($count > $limit)
				$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type= 'hidden' class= 'noMorePosts' value='false'>";

			else
				$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
	echo $str;

	}		




	public function loadProfilePosts($data, $limit) {

			$page= $data['page'];
			$profileUser = $data['profileUsername'];
			$userLoggedIn = $this->user_obj->getUsername();

			if($page == 1)
				$start = 0;
			else
				$start = ($page -1) * $limit;



			$str = ""; //String to return
			$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted= 'no' AND (added_by='$profileUser' AND user_to='none') ORDER BY id DESC");


			if(mysqli_num_rows($data_query) >0) {


				$num_iterations = 0; //number resulrs checked
				$count = 1;


				while ($row = mysqli_fetch_array($data_query)) {
					$id = $row['id'];
					$body = $row['body'];
					$added_by = $row['added_by'];
					$date_time = $row['date_added'];
					$songPath = $row['audio'];




						if($num_iterations++ < $start)
							continue;


						//once 10 posts have been loaded, break
						if($count > $limit) {
							break;
						}
						else {
							$count++;
						}

						if($userLoggedIn == $added_by)
							$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
						else
							$delete_button = "";



						$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
						$user_row = mysqli_fetch_array($user_details_query);
						$username = $user_row['username'];
						$profile_pic = $user_row['profile_pic'];

						?>
						<script>
						
						$(function(){
    $("audio").on("play", function() {
        $("audio").not(this).each(function(index, audio) {
            audio.pause();
        });
    });
});
			
							
								 	function toggle<?php echo $id; ?>() {

								 	var target= $(event.target);
								 	if (!target.is("a")) {

								 	
								 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

									 		if(element.style.display == "block") {
									 			element.style.display = "none";
									 		}
									 		else 
									 			element.style.display = "block";
								 		
								 	}
		 
								 }
						</script>
						<?php

						$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
						$comments_check_num = mysqli_num_rows($comments_check);


						//Timefrane
						$date_time_now = date("Y-m-d H:i:s");
						$start_date = new DateTime($date_time); //time of post
						$end_date = new DateTime($date_time_now); //current time
						$interval = $start_date->diff($end_date);
						if($interval->y >= 1) {
							if($interval == 1)
								$time_message = $interval->y . " year ago"; //1 year ago
							else
								$time_message = $interval->y . " years ago"; //1+ year ago
						}
							else if ($interval-> m >= 1) {
								if($interval->d == 0) {$days = " ago";}
								else if($interval->d == 1) {$days = $interval->d . " day ago";}
								else {$days = $interval->d . " days ago";}

								if($interval->m == 1) {$time_message = $interval->m . " month ago";}
								else {$time_message = $interval->m . " months ago";}
							}

							else if($interval->d >=1) {
								if($interval->d == 1) {$time_message = "Yesterday";}
								else {$time_message = $interval->d . " days ago";}
							}
							else if($interval->h >= 1) {
								if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
								else {$time_message = $interval->h . " hours ago";}
							}
							else if($interval->i >= 1) {
								if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
								else {$time_message = $interval->i . " minutes ago";}
							}
							else {
								if($interval->s < 30) {$time_message = "Now";}
								else {$time_message = $interval->s . " seconds ago";}
							}
					

	
				if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> 
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br>
								$songDiv
								

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>		
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>															
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";
					
						

						?>
						<script>
							
							$(document).ready(function() {

								$('#post<?php echo $id; ?>').on('click', function() {
									bootbox.confirm("Delete Post?", function(result) {

										$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

										if(result)
											location.reload();

									});
								});


							});

						</script>


						<?php
				}



				if($count > $limit)
					$str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
								<input type= 'hidden' class= 'noMorePosts' value='false'>";

				else
					$str .= "<input type= 'hidden' class= 'noMorePosts' value='true'><p style= 'text-align: center;'> No more posts! </p>";
			}

		echo $str;

		}

	public function getSinglePost($post_id) {

		$userLoggedIn = $this->user_obj->getUsername();

		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

		$str = ""; //String to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted= 'no' AND id= '$post_id'");


		if(mysqli_num_rows($data_query) >0) {


			$row = mysqli_fetch_array($data_query); 
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$songPath = $row['audio'];


				//prepare user_to string so it can be included when not posted to a user 
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getUsername();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					return;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)) {




					if($userLoggedIn == $added_by)
						$delete_button = "<button class='delete_button' id='post$id'>Delete</button>";
					else
						$delete_button = "";



					$user_details_query = mysqli_query($this->con, "SELECT username, profile_pic FROM Users WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$username = $user_row['username'];
					$profile_pic = $user_row['profile_pic'];

					?>
					<script>
						
							 	function toggle<?php echo $id; ?>() {

							 	var target= $(event.target);
							 	if (!target.is("a")) {

							 	
							 		var element = document.getElementById("toggleComment<?php echo $id; ?>");

								 		if(element.style.display == "block") {
								 			element.style.display = "none";
								 		}
								 		else 
								 			element.style.display = "block";
							 		
							 	}
	 
							 }
					</script>
					<?php

					$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
					$comments_check_num = mysqli_num_rows($comments_check);


					//Timefrane
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //time of post
					$end_date = new DateTime($date_time_now); //current time
					$interval = $start_date->diff($end_date);
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
						else if ($interval-> m >= 1) {
							if($interval->d == 0) {$days = " ago";}
							else if($interval->d == 1) {$days = $interval->d . " day ago";}
							else {$days = $interval->d . " days ago";}

							if($interval->m == 1) {$time_message = $interval->m . " month ago";}
							else {$time_message = $interval->m . " months ago";}
						}

						else if($interval->d >=1) {
							if($interval->d == 1) {$time_message = "Yesterday";}
							else {$time_message = $interval->d . " days ago";}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {$time_message = $interval->h . " hour ago";}
							else {$time_message = $interval->h . " hours ago";}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {$time_message = $interval->i . " minute ago";}
							else {$time_message = $interval->i . " minutes ago";}
						}
						else {
							if($interval->s < 30) {$time_message = "Now";}
							else {$time_message = $interval->s . " seconds ago";}
						}
				

	
				if($songPath != "") {
					$songDiv = "<div class='postedSong'>
						<audio controls='controls' controlsList ='nodownload' class='audio_bar'>
						<source src='$songPath' />
						</audio>
					</div>";
				}
				else {
					$songDiv = "";
				}

				$str .= "<div class= 'status_post' onClick='javascript:toggle$id()'>
							<div class= 'post_profile_pic'>
								<img src= '$profile_pic' width='50'>
							</div>

							<div class='posted_by' style='color:#CDCDCD;'>
								<a href= '$added_by'> $username </a> 
								 &nbsp;&nbsp;&nbsp;&nbsp;$time_message
								 $delete_button
							</div>
							<div id='post_body'>
								$body
								<br><br>
								$songDiv
								

							</div>

							<div class='newsfeedPostOptions'>
								<img src ='assets/images/icons/comment.png' alt=''/>&nbsp;&nbsp;&nbsp;
								<iframe src='like.php?post_id=$id' scrolling='no'></iframe>
								<iframe src='share.php?post_id=$id' style='width: 50px; margin-left: -122%;' scrolling='no'></iframe>	
						<div class='fave_frame'><iframe src='fave.php?post_id=$id' scrolling='no'></iframe></div>
						</div>																
							</div>

						<div class='post_comment' id='toggleComment$id' style='display:none;'>
							<iframe src='comment_frame.php?post_id=$id' id='comment_iframe'></iframe>
						</div>
						<hr>";

					?>
					<script>
						
						$(document).ready(function() {

							$('#post<?php echo $id; ?>').on('click', function() {
								bootbox.confirm("Delete Post?", function(result) {

									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

									if(result)
										location.reload();

								});
							});


						});

					</script>
					<?php
								}

						else{
							echo "<p>You are not yet friends with this user</p>";
							return;
						}

			}
			else {
				echo "<p>No post found, link may be broken</p>";
				return;
			}


	echo $str;

	}

}

?>