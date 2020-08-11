<?php 
include("includes/header.php");


 ?>


 <div class="main_colum column">
 
 	<form action="" method="POST">
					 							
				<textarea name='about_body' id='about_textarea' placeholder=' Write Something about yourself...'></textarea>
			<br>	<input type='submit' name='post_about'  id='about_submit' class="ignore_button" value='Submit' style="position: absolute;bottom: 18px;right: 9%;border-radius: 8px;width: 18%;padding: 5;color: #28a4af; cursor: pointer;"> <br>
 	</form>

 </div>

 <?php 
	if(isset($_POST['about_body'])) {
		$about = $_POST['about_body'];
		$about_query = mysqli_query($con, "UPDATE Users SET about='$about' WHERE username='$userLoggedIn'");
		header("Location: index.php");
	}

  ?>