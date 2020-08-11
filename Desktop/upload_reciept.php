<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");

$id = $_GET['id'];

$picQuery = mysqli_query($con, "SELECT picture FROM posts WHERE id = '$id'");
$row = mysqli_fetch_array($picQuery);


?>

<div class="reciept_column column" style="padding: 1.5rem;">
<h1 class="congrats">Congrats, Your Song Has Been Uploaded!</h1>
<br>
<input type="text" class="myLink" id="myLink" readonly value="
http://m.onelookin.com/song_page.php?id=<?php echo $id; ?>
"
>

<button onclick="getLink()" class="getLink">Copy Link</button>

<p class="congratsText">
    Now is your time to shine! Go send that link to everyone you know - But remember it can only be 
    opened on a mobile device.
</p>

<a href="index.php"><i class="fas fa-arrow-circle-left" style="color: #28a4af; font-size: 3rem;"></i></a>


</div>

<script>

function getLink() {
  /* Get the text field */
  var copyText = document.getElementById("myLink");

  /* Select the text field */
  copyText.select();

  /* Copy the text inside the text field */
  document.execCommand("copy");

  /* Alert the copied text */
  alert("Copied the text: " + copyText.value);
}

</script>