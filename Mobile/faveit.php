<head>
  <script src="https://kit.fontawesome.com/fa009d607d.js"></script>
  
  
 <style media="screen">


  body {
    position: relative;
display: flex;
flex-flow: column;
margin: 0;
  }

button {
  background: none;
  border: none;
  cursor: pointer;
}

.fa-star {
  font-size: 3rem !important;
  color: #000 !important;
}

</style>
 
  
</head>

<?php
include("includes/config.php");
include("includes/classes/User.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");
include("includes/classes/Playlist.php");


if($userLoggedIn = $_SESSION['Guest'])
{
  echo	"<i class='far fa-star'></i>";
}
else {
    $post_id = $_GET['id'];
    if ($post_id != "undefined") {
        $username = $_SESSION['userLoggedIn'];
        $faveQuery = mysqli_query($con, "SELECT * FROM faves WHERE username = '$username' AND post_id = '$post_id'");
    //    $getFaves = mysqli_fetch_array($faveQuery);
    //    $faveValue = $getFaves['id'];
      }
    else {
      $post_id =$_SESSION['post_id'];
      $username = $_SESSION['userLoggedIn'];
      $faveQuery = mysqli_query($con, "SELECT * FROM faves WHERE username = '$username' AND post_id = '$post_id'");
  //    $getFaves = mysqli_fetch_array($faveQuery);
  //    $faveValue = $getFaves['id'];
    }


        if (mysqli_num_rows($faveQuery) > 0) {
        echo  '<form method="POST">
        <button type="submit" id="unfave_button" name="unfave_button"><i class="fas fa-star"></i></button>
	 			</form> ';


        }
        else {
          echo  '<form method="POST">
          <button type="submit" id="fave_button" name="fave_button"><i class="far fa-star"></i></button>
  	 			</form> ';


        }

        //unfave button
      	 if(isset($_POST['unfave_button'])) {

      	 	$insert_user = mysqli_query($con, "DELETE FROM faves WHERE username='$username' AND post_id='$post_id'");
      // header("Refresh:0");
      echo "<script>
window.location.reload()
</script>";

        }
        if(isset($_POST['fave_button'])) {

          $insert_user = mysqli_query($con, "INSERT INTO faves VALUES('', '$username', '$post_id')");
     //   header("Refresh:0");
      echo "<script>
   window.location.reload()
      </script>";
         }


}

?>
