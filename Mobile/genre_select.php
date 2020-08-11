<?php
include("includes/includedFiles.php");

?>

<style>
	.fa-bars {
		color: #28a4af;
	}
</style>

<h1 class="pageHeadingBig" style="font-size: 6rem;">Genre Pages</h1>


	<?php
    $resultArray = $_SESSION["resultArray"];
		$jsonArray = json_encode($resultArray);
		?>

		<script>
				$(document).ready(function() {
					var tempSongIds = '<?php echo json_encode($resultArray); ?>';
					tempPlaylist = JSON.parse(tempSongIds);
					document.getElementById("logoButton").src = "assets/images/icons/simplelogoblack2.png";

		});
		</script>

		<?php
	//	if($userLoggedIn = $_SESSION['Guest'])
	//	{}
//		else {




	?>

<div class="genre_list">

	<div class="genreItem">
		<i class="fas fa-guitar"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=acoustic')" class="genreItemLink">Acoustic</span>
	</div>

	<div class="genreItem">
		<i class="fab fa-itunes-note"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=classical')" class="genreItemLink">Classical</span>
	</div>


	<div class="genreItem">
<i class="fas fa-hat-cowboy"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=country')" class="genreItemLink">Country</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-bolt"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=electric')" class="genreItemLink">Electric</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-campground"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=folk')" class="genreItemLink">Folk</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-city"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=hip-hop')" class="genreItemLink">Hip-Hop</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-drum"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=jazz')" class="genreItemLink">Jazz</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-fire"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=pop')" class="genreItemLink">Pop</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-hand-rock"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=rock')" class="genreItemLink">Rock</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-microphone"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=randb')" class="genreItemLink">R&B</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-water"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=vibe')" class="genreItemLink">Vibe</span>
	</div>

	<div class="genreItem">
		<i class="fas fa-question"></i>
		<span role="link" tabindex="0" onclick="openPage('genre.php?genre=other')" class="genreItemLink">Other</span>
	</div>


</div>
