<?php
include("includes/includedFiles.php");

?>

<style>
	.fa-headphones {
		color: #28a4af;
	}
</style>

<h1 class="pageHeadingBig">Featured Songs</h1>

<div class="gridViewContainer songGrid">

	<?php
    $resultArray = $_SESSION["resultArray"];
		$jsonArray = json_encode($resultArray);
		?>

		<script>
				$(document).ready(function() {
					var tempSongIds = '<?php echo json_encode($resultArray); ?>';
					tempPlaylist = JSON.parse(tempSongIds);
					history.pushState({}, null, "browse.php?id=" + currentPlaylist[currentIndex]);
					document.getElementById("logoButton").src = "assets/images/icons/simplelogoturquoise2.png";

		})
		$(window).on("unload", function(e) {
			$("#logoButton").attr("src","assets/images/icons/logo3-blue.png");
		});


		</script>

		<?php
	//	if($userLoggedIn = $_SESSION['Guest'])
	//	{}
//		else {

		foreach($resultArray as $row) {
			$albumSong = new Song($con, $row);
			$albumArtist = $albumSong->getArtist();

			echo "<div class='gridViewItem' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
					<span role='link' tabindex='0'>
						<img src='http://onelookin.com/" . $albumSong->getArtworkPath() . "'>

						<div class='gridViewInfo'>"
							. $albumSong->getTitle() .
						"</div>
						<span class='songArtistName'>" . $albumSong->getUsername() . "</span>
					</span>

				</div>";


}


	?>

</div>
