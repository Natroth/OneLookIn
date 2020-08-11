<?php 
include("includes/header.php");

$profile_id = $user['username'];




//upload.php

if(isset($_POST["image"]))
{
 $data = $_POST["image"];

 $image_array_1 = explode(";", $data);

 $image_array_2 = explode(",", $image_array_1[1]);

 $data = base64_decode($image_array_2[1]);

 $imageName = 'assets/images/profile_pics/' . uniqid() . '.png';

 $proPicQuery = mysqli_query($con, "UPDATE users SET profile_pic = '$imageName' WHERE username = '$profile_id'");

 file_put_contents($imageName, $data); 


// echo '<img src="'.$imageName.'" class="img-thumbnail" />';


}



?>