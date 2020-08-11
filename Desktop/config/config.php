<?php
ob_start(); //Turns on output buffering
session_start();


$timezone = date_default_timezone_set("America/Los_Angeles");

$con = mysqli_connect("localhost","nathanroth","12Hilltop.","nathanro_onelookin"); //connection variable

if (mysqli_connect_errno())
{


	echo "failed to connect" . mysqli_connect_errno();
}

?>
