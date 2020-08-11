<?php
include("../../config.php");

if(isset($_POST['songId'])) {
	$songId = $_POST['songId'];

	$query = mysqli_query($con, "UPDATE posts SET plays = plays + 1 WHERE id='$songId'");
}
?>
