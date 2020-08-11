<?php
	ob_start();
	error_reporting(0);

	    if(!isset($_SESSION))
	    {
	        session_start();
	    }

	$timezone = date_default_timezone_set("Europe/London");

$con = mysqli_connect("localhost","nathanroth","12Hilltop.","nathanro_onelookin"); //connection variable

	if(mysqli_connect_errno()) {
		echo "Failed to connect: " . mysqli_connect_errno();
	}
?>
