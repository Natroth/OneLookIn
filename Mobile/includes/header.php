<?php
include("includes/config.php");
include("includes/classes/User.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");
include("includes/classes/Playlist.php");

//session_destroy(); LOGOUT

if(isset($_SESSION['userLoggedIn'])) {
	$userLoggedIn = new User($con, $_SESSION['userLoggedIn']);
	$username = $userLoggedIn->getUsername();
	echo "<script>userLoggedIn = '$username';</script>";
	$_SESSION["Guest"] = "";

}
else {

	$userLoggedIn = new User($con, 'Guest');
	echo "<script>userLoggedIn = 'Guest';</script>";
	$_SESSION["Guest"] = $userLoggedIn;


}

?>

<html>
<head>
	<title>OLI</title>


	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="assets/js/script.js"></script>
	<script src="https://kit.fontawesome.com/fa009d607d.js"></script>

	<!-- jQuery Modal -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
	<link rel="shortcut icon" type="image/x-icon" href="assets/images/icons/simplelogoturquoise2.png" />


<script type="text/javascript">


if (screen.width > 699) {
  document.location = "http://onelookin.com/index.php";
}

</script>


</head>



<body>

	<div id="mainContainer">

		<div id="topContainer">

			<?php include("includes/navBarContainer.php"); ?>

			<div id="mainViewContainer">

				<div id="mainContent">
				    
				    
				    
				    
				    
