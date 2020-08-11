<?php
include("includes/includedFiles.php");

if(isset($_GET['id'])) {
	$artistId = $_GET['id'];
}
else {
	header("Location: index.php");
}

$artist = new Artist($con, $artistId);
$artistName = $artist->getName();
$faveQuery = mysqli_query($con, "SELECT * FROM faves WHERE username= '$artistName'");
$array = array();
while($row = mysqli_fetch_array($faveQuery)) {
  array_push($array, $row['post_id']);
}?>

<script type="text/javascript">
$(document).ready(function() {
  document.getElementById("logoButton").src = "assets/images/icons/simplelogoblack2.png";
});
</script>

<div class="entityInfo" style="padding-top: 7rem;">

	<div class="centerSection">

		<div class="artistInfo">
			<span>
				<img src="http://onelookin.com/<?php echo $artist->getProfilePic(); ?>" alt="" class="artistPagePic" style="width: 20%;">
			</span>
			<h1 class="artistName"><?php echo $artist->getName(); ?>'s Favorite Songs:</h1>



		</div>

	</div>

</div>



<div class="tracklistContainer">
	<ul class="tracklist" style="display: flex;">

		<?php
		$songIdArray = $artist->getSongIds();

		foreach($array as $songId) {



			$albumSong = new Song($con, $songId);
			$albumArtist = $albumSong->getArtist();

			echo "<div class='gridViewItem' style='width: 28%;'>
					<span role='link' tabindex='0' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<img src='http://onelookin.com/" . $albumSong->getArtworkPath() . "'>

						<div class='gridViewInfo'>"
							. $albumSong->getTitle() .
						"</div>
					</span>

				</div>";


		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>


<nav class="optionsMenu">
	<input type="hidden" class="songId">
	<?php echo Playlist::getPlaylistsDropdown($con, $userLoggedIn->getUsername()); ?>
</nav>
