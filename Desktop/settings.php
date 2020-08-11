<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>


<div id="uploadimageModalPro" class="modal" role="dialog"  data-keyboard="false" data-backdrop="static">
 <div class="modal-dialog">
  <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload & Crop Image</h4>
        </div>
        <div class="modal-body">
          <div class="row">
       <div class="col-md-8 text-center">
        <div id="image_demoPro" style="width:350px; margin-top:30px"></div>
       </div>
       <div class="col-md-4" style="padding-top:30px;">
        <br />
        <br />
        <br/>
        <button class="btn btn-success crop_image">Crop & Upload Image</button>
     </div>
    </div>
        </div>
<br>
     </div>
    </div>
</div>	



<div class="Main_column request_column">
	<div class="settingsTopBar">
		<h4>Account Settings</h4>
		<a href="includes/handlers/logout.php" class="logout">Logout</a>
</div>	
<hr>
	<?php
		echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pics'>"; 
	 ?>
	 <br>
	 <form class="updateProPic" id="updateProPic" method="post" enctype="multipart/form-data">
	 <input type="file" name="proPicToUpload" id="proPicToUpload" class="picToUpload" style="display: none;">
	 <label for="proPicToUpload" class="custom-picToUpload changePicButton" name="custom-proPicToUpload" id="custom-proPicToUpload">
	Update Profile Picture
	</label>
	</form>	 
<hr>

	<?php 
	$user_data_query = mysqli_query($con, "SELECT email FROM users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($user_data_query);

	$email = $row['email'];
	 ?>
<!--
	 <form action="settings.php" method="POST">
	 	Email: <input type="email" name="email" value="<?php echo $email; ?>" id="settings_input">
	 	<?php echo $message; ?>
	 	<input type="submit" name="update_details" id="save_details" value="Update Email" class="default">

	 </form>
<hr>
-->
	 <h4>Change Password</h4>
	 <form action="settings.php" method="POST">
	 	Old Password: <input type="password" name="old_password"  id="settings_input"><br><br>
	 	New Password: <input type="password" name="new_password_1"  id="settings_input"><br><br>
	 	Confirm New Password: <input type="password" name="new_password_2"  id="settings_input"><br><br>

	 	<?php echo $password_message; ?>

	 	<input type="submit" name="update_password" id="save_details" value="Update Password" class="default">

	 </form>
<!--	 
<hr>
	 <h4>Close Account?</h4>
	 <form action="settings.php" method="POST">
	 	<input type="submit" name="close_account" id="close_account" value="Close account" class="default">
	 </form>
-->
</div>
<br><br>
<script>

$(document).ready(function(){

$image_crop = $('#image_demoPro').croppie({
   enableExif: true,
   viewport: {
	 width:200,
	 height:200,
	 type:'square' //circle
   },
   boundary:{
	 width:300,
	 height:300
   }
 });

 $('#proPicToUpload').on('change', function(){
   var reader = new FileReader();
   reader.onload = function (event) {
	 $image_crop.croppie('bind', {
	   url: event.target.result
	 }).then(function(){
	   console.log('jQuery bind complete');
	 });
   }
   reader.readAsDataURL(this.files[0]);
   $('#uploadimageModalPro').modal('show');
 });

 $('.crop_image').click(function(event){
   $image_crop.croppie('result', {
	 type: 'canvas',
	 size: 'viewport'
   }).then(function(response){
	   console.log(response);
	$.ajax({
        url:"upload.php",
        type: "POST",
        data:{"image": response},
        success:function(data)
        {
      //    $('#uploadimageModalPro').modal('hide');
      //    $('#uploaded_image').html(data);
		  location.reload();
        }
      });  
   })
 });

});  
</script>

<style>

.modal-backdrop {
	position: fixed;
}

.modal-dialog {
    max-width: 600px;
	margin: 30px auto;
}

.btn-success {
    color: #fff;
    background-color: #28a4af;
    border-color: #dcdcdc;
    margin-top: 3rem;
}
.alert {
    font-family: brandon-grotesque, sans-serif;

    position: relative;
    padding: 0.75rem 1.25rem;
    margin-top: 8rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
    width: 39rem;
	margin: auto;
	top: 31rem;
}

</style>