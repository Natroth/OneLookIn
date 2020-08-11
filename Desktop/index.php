<?php 
include("includes/header.php");

$songQuery = mysqli_query($con, "SELECT * FROM posts WHERE added_by= '$userLoggedIn' ORDER BY plays DESC LIMIT 5");
$songRows = mysqli_num_rows($songQuery);
$array = array();
while($row = mysqli_fetch_array($songQuery)) {
  array_push($array, $row['id']);
}
$collectionQuery = mysqli_query($con, "SELECT * FROM collections WHERE added_by ='$userLoggedIn'");
$collectionRows = mysqli_num_rows($collectionQuery);
$array2 = array();
while($row2 = mysqli_fetch_array($collectionQuery)) {
    array_push($array2, $row2['id']);
  }

?>
	


<div class="firstLayer">
<div class="user_details column"> 
    <a class="uploadSongButton" href="upload_post.php">
    <i class="fas fa-upload" style="font-size: 4rem;"></i>
        <div class="UploadButtonText">Upload<br>Song</div>
    </a>

<br>
        <div class="statsInfo">
        <?php 
            echo "Songs:" . "&nbsp" . $numSongs . "<br>";
            echo "Total Plays:" . "&nbsp" . $numTotalPlays;
            ?>
        </div>   
	</div>



<div class= "main_column column">
    <h2 class="topSongsHeader">My Most Played Song</h2>
<?php


if( $songRows == 0) {
    echo 
    "<div class='nothingHere'>
    Nothing to show here, go add some songs!
    <br>
    <i class='far fa-hand-point-left'></i>
    </div>";
}

else {

		foreach($array as $songId) {

            
			$albumSong = new Song($con, $songId);
			$albumArtist = $albumSong->getArtist();

            echo "<div class='gridViewItem' style='width: 25rem;'>
                    <div style='display: flex;'>
						<img class='songPic' src='http://onelookin.com/" . $albumSong->getArtworkPath() . "'>
                        &nbsp
						<div class='gridViewInfo topSongsText' style='font-size: 1.5rem'>"
							. $albumSong->getTitle() .
                        "</div>
                    </div>
                        <div class='gridViewInfo'>
                        <i class='fas fa-play' style='font-size: 10px;'></i> &nbsp
                        "              
							. $albumSong->getSongPlays() .
						"</div>			

				</div>";


		}
    }
		?>

    </div>
    </div>
    <br><br>
    <div class="secondLayer column" style="padding-top: 1rem;">
    <h1 class="myCollections">My Collections</h1><br>
  <?php

if( $collectionRows == 0) {
    echo 
    "<div class='nothingHere'>
    Nothing to see here!
    <br><br>
    </div>";
}

else {
 

foreach($array2 as $collectionId) {
    $collectionNameQuery = mysqli_query($con, "SELECT * FROM collections WHERE id = '$collectionId'");
    $collectionNameArray = mysqli_fetch_array($collectionNameQuery);
    $collectionName =  $collectionNameArray['name'];

    $collectionSongQuery = mysqli_query($con, "SELECT * FROM posts WHERE collection = '$collectionId'");
    $array3 = array();
    while($row3 = mysqli_fetch_array($collectionSongQuery)) {
        array_push($array3, $row3['id']);
        
    }
    echo "<div class='collectionSkin'>";
    echo "<div class='collectionHeader'>"
    . $collectionName .
   "</div>";

    foreach($array3 as $songId) {

    $albumSong = new Song($con, $songId);
    $albumArtist = $albumSong->getArtist();
    $genre = $albumSong->getGenre();
    $delete_button = "<button class='delete_button' id='post$songId'>Delete</button>";
    
    echo"<script language='javascript'>

    $(document).ready(function() {

        $('#post$songId').on('click', function() {
            console.log('buuy');
            bootbox.confirm('Delete Song?', function(result) {
        
                $.post('includes/form_handlers/delete_post.php?post_id=$songId', {result:result});
        
                if(result)
                    location.reload();
        
        
            });
        
        
        });
        
        
        });

    </script>
    ";



    echo "<div class='gridViewItem' style='width: 90%; margin-left: auto; margin-right: auto;'>
            <div style='display: flex;'>
                <img class='songPic' style='width: 3rem;' src='http://onelookin.com/" . $albumSong->getArtworkPath() . "'>
                &nbsp
                <div class='gridViewInfo' style='font-size: 1.5rem'>
                <span class='titleOverflow'>"
                    . $albumSong->getTitle() .
                "</span>
                </div>               
            </div>
            <div class='gridViewInfo' style='font-size: 1rem'>Uploaded &nbsp"
            . $albumSong->getDatePosted() .
        "</div> 
        <div class='gridViewInfo' style='font-size: 1rem'>@"
        . $genre . 
    "</div>  
    <div class='gridViewInfo' style='font-size: 1rem'>"
    . $delete_button . 
"</div>                        
                <div class='gridViewInfo'>
                <i class='fas fa-play' style='font-size: 10px;'></i> &nbsp 
                "              
                    . $albumSong->getSongPlays() .
                "</div>			

        </div>";

        
    }
    echo "<hr style='width: 40rem;'>";
   echo "</div>";

}
}

?>


    </div>

</div>		




	<script>

$("#fileToUpload").change(function(e){
    var file = e.currentTarget.files[0];
   
    
    objectUrl = URL.createObjectURL(file);
    $("#audio10").prop("src", objectUrl);
});

					

					</script>



</div>
<br><br>


</body>
</html>