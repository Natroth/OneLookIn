<?php
include("includes/includedFiles.php");

if(isset($_GET['genre'])) {
	$genreName = $_GET['genre'];
}
else {
	header("Location: index.php");
}

$query = mysqli_query($con, "SELECT id FROM posts WHERE deleted='no' AND genre ='$genreName'");
$array = array();
while($row = mysqli_fetch_array($query)) {
	array_push($array, $row['id']);
}

	if ($genreName == 'randb') {
		$genreName = 'R&B';
	}


?>

<style>
	.fa-bars {
		color: #28a4af;
	}
</style>

<div class="entityInfo ">

	<div class="centerSection">
		<span role="link" tabindex="0" onclick="openPage('genre_select.php')"><i class="far fa-arrow-alt-circle-left" style="font-size: 3rem; margin:0 2rem !important;"></i></span>

		<div class="artistInfo">

			<h1 class="artistName" style="font-size: 4rem;  text-transform: uppercase;"><?php echo $genreName; ?></h1>


		</div>

	</div>

</div>


<div class="tracklistContainer">

	<ul class="tracklist" style="display: flex;">

		<?php
	//	if($userLoggedIn = $_SESSION['Guest'])
	//	{}
//		else {

		foreach($array as $row) {
			$albumSong = new Song($con, $row);
			$albumArtist = $albumSong->getArtist();

			echo "<div class='gridViewItem'>
					<span role='link' tabindex='0' onclick='setTrack(\"" . $albumSong->getId() . "\", tempPlaylist, true)'>
						<img src='http://onelookin.com/" . $albumSong->getArtworkPath() . "'>

						<div class='gridViewInfo'>"
							. $albumSong->getTitle() .
						"</div>
						<span class='songArtistName'>" . $albumArtist->getName() . "</span>

					</span>

				</div>";


}


	?>

		<script>
			var tempSongIds = '<?php echo json_encode($array); ?>';
			tempPlaylist = JSON.parse(tempSongIds);
		</script>

	</ul>
</div>
