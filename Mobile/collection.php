<?php
include("includes/includedFiles.php");

if(isset($_GET['collection_id'])) {
	$collectionId = $_GET['collection_id'];
}
else {
	header("Location: index.php");
}

$query = mysqli_query($con, "SELECT * FROM posts WHERE deleted='no' AND collection ='$collectionId'");
$array = array();
while($row = mysqli_fetch_array($query)) {
	array_push($array, $row['id']);
	$artist = $row['artist'];
}


$collection_name = mysqli_query($con, "SELECT * FROM collections WHERE id ='$collectionId'");
$nameRow = mysqli_fetch_array($collection_name);

?>

<script type="text/javascript">
var artist = "<?php echo $artist ?>";

</script>

<div class="entityInfo ">

	<div class="centerSection">

		<div class="artistInfo">

			<h1 class="artistName" style="font-size: 4rem;"><?php echo $nameRow['name']; ?></h1>
			<h1 class="artistName" onclick="openPage('artist.php?id=' + artist )"><?php echo $nameRow['added_by']; ?></h1>



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
