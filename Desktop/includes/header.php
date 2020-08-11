<?php 
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");
include("includes/classes/Song.php");
include("includes/classes/Artist.php");



if (isset($_SESSION['username'])) {
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username= '$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
	$profilePic = $user['profile_pic'];
	$numSongs = $user['num_songs'];
	
	$getAllPlays = mysqli_query($con, "SELECT SUM(plays) As totalPlays FROM posts WHERE added_by= '$userLoggedIn'");
	$allPlays = mysqli_fetch_array($getAllPlays);
	$numTotalPlays = $allPlays['totalPlays'];

}
else {
	header("Location: register.php");
}


 ?>

<html>
<head>
	<title>OLI</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/popper.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.3.2/bootbox.min.js"></script>
	<script src="assets/js/Lynxic.js" ></script>
	<script src="assets/js/jquery.Jcrop.js"></script>
	<script src="assets/js/jcrop_bits.js"></script>
<script src="https://rawgit.com/moment/moment/2.2.1/min/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.js"></script>


	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">
	<script src="https://kit.fontawesome.com/fa009d607d.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.4/croppie.min.css">
	<link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />

  <link rel="shortcut icon" type="image/x-icon" href="assets/images/icons/simplelogoturquoise2.png" />
  
   <meta name="viewport" content="width=device-width, initial-scale=.2">



<style type="text/css">
	* {
		font-size: 16px;
		font-family: brandon-grotesque, sans-serif;
	}


</style>



<!-- Mobile redirect script by https://pagecrafter.com -->
<script type="text/javascript" src="../assets/js/redirection-mobile.js"></script><script type="text/javascript">// <![CDATA[
SA.redirection_mobile ({
 mobile_url : "m.onelookin.com",
tablet_redirection: "true",
tablet_host: "m.onelookin.com",
 });
 
// ]]>
</script>



</head>
<body>

<div class= 'top_bar'>	
	<div class="headerInfo">
		<div class='proPicHolder'>
			<img class="headerPic" src="<?php echo $user['profile_pic']; ?>" alt="">
		</div>
		<div class="headerName">
			<?php echo $userLoggedIn; ?>
		</div>
	</div>
	<div>
		<div class="headerButtons">
			<a href="settings.php">	
			<i class="fas fa-cogs thisButton"></i>
			</a>
			<a href="index.php">
			<i class="fas fa-home thisButton"></i>
			</a>
		</div>	
	</div>
</div>	

<div class= "wrapper">

