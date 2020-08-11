<?php
include("includes/includedFiles.php");

if(isset($_GET['term'])) {
	$term = urldecode($_GET['term']);
}
else {
	$term = "";
}
?>

<script type="text/javascript">
$(document).ready(function() {
	document.getElementById("logoButton").src = "assets/images/icons/simplelogoblack2.png";
});
</script>

<div class="searchContainer">

	<input type="text" class="searchInput"
	 value="<?php echo $term; ?>"
	 placeholder="Search Up..."
	onfocus="var temp_value=this.value;
	this.value=''; this.value=temp_value">

</div>


<style>
	.fa-search {
		color: #28a4af;
	}
</style>

<script>

$(".searchInput").focus();

$(function() {

	$(".searchInput").keyup(function() {
		clearTimeout(timer);

		timer = setTimeout(function() {
			var val = $(".searchInput").val();
			openPage("search.php?term=" + val);
		}, 2000);




	})

})



</script>

<?php if($term == "") exit(); ?>

<div class="tracklistContainer borderBottom">
	<h2 style="color: #28a4af; font-size: 2rem;">SONGS</h2>
	<ul class="tracklist" style="display: flex;">

		<?php
		$songsQuery = mysqli_query($con, "SELECT id FROM posts WHERE deleted='no' AND body LIKE '%$term%' LIMIT 15");

		if(mysqli_num_rows($songsQuery) == 0) {
			echo "<span class='noResults' style='font-size: 1.3rem;'>No songs found matching " . $term . "</span>";
		}



		$songIdArray = array();

		$i = 1;
		while($row = mysqli_fetch_array($songsQuery)) {

			if($i > 15) {
				break;
			}

			array_push($songIdArray, $row['id']);

			$albumSong = new Song($con, $row['id']);
			$albumArtist = $albumSong->getArtist();

			echo "<div class='gridViewItem' style='width: 28%;'>
					<span role='link' tabindex='0' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<img src='http://onelookin.com/" . $albumSong->getArtworkPath() . "'>

						<div class='gridViewInfo'>"
							. $albumSong->getTitle() .
						"</div>
						<span class='songArtistName'>" . $albumArtist->getName() . "</span>
					</span>

				</div>";


			$i = $i + 1;
		}

		?>

		<script>
			var tempSongIds = '<?php echo json_encode($songIdArray); ?>';
			tempPlaylist = JSON.parse(tempSongIds);


		</script>

	</ul>
</div>


<div class="artistsContainer borderBottom">

	<h2 style="color: #28a4af; font-size: 2rem;">ARTISTS</h2>

	<?php
	$artistsQuery = mysqli_query($con, "SELECT id FROM users WHERE num_songs != 0 AND username != 'Guest' AND username LIKE '%$term%' LIMIT 10");

	if(mysqli_num_rows($artistsQuery) == 0) {
		echo "<span class='noResults'>No artists found matching " . $term . "</span>";
	}

	while($row = mysqli_fetch_array($artistsQuery)) {

		$artistFound = new Artist($con, $row['id']);

		echo "<div class='searchResultRow'>
				<div class='artistName'>

					<span class='searchArtistInfo' role='link' tabindex='0' onclick='openPage(\"artist.php?id=" . $artistFound->getId() ."\")'>

					<img class='searchProfilePic' src='http://onelookin.com/" 		. $artistFound->getProfilePic() . "'>
					<div class='searchArtistName'>
					"
					. $artistFound->getName() .
					"
					</div>
					</span>

				</div>

			</div>";

	}


	?>

</div>

<div class="collectionsContainer borderBottom">

	<h2 style="color: #28a4af; font-size: 2rem;">COLLECTIONS</h2>

	<?php

	$albumQuery = mysqli_query($con, "SELECT * FROM collections WHERE name LIKE '%$term%' LIMIT 10");

		if(mysqli_num_rows($albumQuery) == 0) {
			echo "<span class='noResults'>No albums found matching " . $term . "</span>";
		}

		while($row = mysqli_fetch_array($albumQuery)) {

			echo "<div class='gridViewItemCollections'>
					<span role='link' tabindex='0' onclick='openPage(\"collection.php?collection_id=" . $row['id'] . "\")'>

						<div class='gridViewInfo' style=>"
							. $row['name'] .
						"</div>
						<div class='gridViewInfo' style='color: #96a4af; font-size: 2rem'>"
							. $row['added_by'] .
						"</div>
					</span>

				</div>";

		}

	?>

</div>

<div class="artistsContainer">

	<h2 style="color: #28a4af; font-size: 2rem;">FIND YOUR FRIEND'S FAVORITE SONGS</h2>

	<?php
	$artistsQuery = mysqli_query($con, "SELECT id FROM users WHERE num_songs = 0 AND username != 'Guest' AND username LIKE '%$term%' LIMIT 10");

	if(mysqli_num_rows($artistsQuery) == 0) {
		echo "<span class='noResults'>No users found matching " . $term . "</span>";
	}

	while($row = mysqli_fetch_array($artistsQuery)) {

		$artistFound = new Artist($con, $row['id']);

		echo "<div class='searchResultRow'>
				<div class='artistName'>

					<span class='searchArtistInfo' role='link' tabindex='0' onclick='openPage(\"user_faves.php?id=" . $artistFound->getId() ."\")'>

					<img class='searchProfilePic' src='http://onelookin.com/" 		. $artistFound->getProfilePic() . "'>
					<div class='searchArtistName'>
					"
					. $artistFound->getName() .
					"
					</div>
					</span>

				</div>

			</div>";

	}


	?>

</div>




<nav class="optionsMenu">
	<input type="hidden" class="songId">
	<?php echo Playlist::getPlaylistsDropdown($con, $userLoggedIn->getUsername()); ?>
</nav>
