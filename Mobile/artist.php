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
?>

<div class="entityInfo" style="padding-top: 7rem;">

	<div class="centerSection">

		<div class="artistInfo">
			<span>
				<img src="http://onelookin.com/<?php echo $artist->getProfilePic(); ?>" alt="" class="artistPagePic">
			</span>
			<h1 class="artistName"><?php echo $artist->getName(); ?></h1>



		</div>

	</div>

</div>


<div class="tracklistContainer">
	<h2 style="font-size: 3rem;">TOP SONGS</h2>
	<ul class="tracklist" style="display: flex;">

		<?php
		$songIdArray = $artist->getSongIds();

		foreach($songIdArray as $songId) {



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
<div style="display: flex; flex-flow: row; justify-content: center;">
<span class='searchArtistInfo' style='color: #28a4af;' role='link' tabindex='0' onclick='openPage("user_faves.php?id= <?php echo $artistId; ?> ")'>See <?php echo $artistName; ?>'s Favorite Songs</span>
</div>
</div>

<div class="gridViewContainer">
	<h2 style="font-size: 3rem;">COLLECTIONS</h2>
	<?php
		$albumQuery = mysqli_query($con, "SELECT * FROM collections WHERE added_by ='$artistName'");

		while($row = mysqli_fetch_array($albumQuery)) {




			echo "<div class='gridViewItemCollections'>
					<span role='link' tabindex='0' onclick='openPage(\"collection.php?collection_id=" . $row['id'] . "\")'>

						<div class='gridViewInfo' style='color: #28a4af'>"
							. $row['name'] .
						"</div>

					</span>

				</div>";




		}
	?>

</div>

<nav class="optionsMenu">
	<input type="hidden" class="songId">
	<?php echo Playlist::getPlaylistsDropdown($con, $userLoggedIn->getUsername()); ?>
</nav>
