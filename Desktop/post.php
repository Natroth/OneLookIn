<?php 
include("includes/header.php");

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}

else {
	$id = 0;
}

?>

<div class="user_details column"> 
<a href="<?php echo $userLoggedIn; ?>"> 

<img src= "<?php echo $user['profile_pic'] ; ?>">
</a>
<br>

<a href="<?php echo $userLoggedIn; ?>"> 



<div class= "user_details_left_right">
	<?php
	echo $user['username'];
	  ?>
	</a>
	<br>
	<?php 
	echo "Songs:" . "&nbsp" .$user['num_songs'] . "<br>";
	echo "Likes:" . "&nbsp" .$user['num_likes'];
	 ?>
	</div>
</div>


<div class= "main_column_post" id="main_column_post">
	<div class="posts_area">
		<?php 
			$post = new Post($con, $userLoggedIn);
			$post->getSinglePost($id);
		 ?>
	</div>
	
</div>