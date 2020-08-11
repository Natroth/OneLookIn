<?php
include("includes/includedFiles.php");
?>

<style>
	.fa-star {
		color: #28a4af;
	}
</style>

<div class="playlistsContainer">

	<div class="gridViewContainer">
		<h2 style="font-size: 6rem;">Your Favorite Songs</h2>

		<script type="text/javascript">
		$(document).ready(function() {
			document.getElementById("logoButton").src = "assets/images/icons/simplelogoblack2.png";
		});
		</script>



		<?php
			$username = $userLoggedIn->getUsername();

			$playlistsQuery = mysqli_query($con, "SELECT * FROM faves WHERE username='$username'");

			$resultArray = array();

			while($row = mysqli_fetch_array($playlistsQuery)) {
				array_push($resultArray, $row['post_id']);
			}

			$jsonArray = json_encode($resultArray);
			?>

			<script>
					$(document).ready(function() {
						var tempSongIds = '<?php echo json_encode($resultArray); ?>';
						tempPlaylist = JSON.parse(tempSongIds);
				});
			});
			</script>
<div class="tracklist" style="display: flex;">
			<?php
		//	if($userLoggedIn = $_SESSION['Guest'])
		//	{}
	//		else {

			foreach($resultArray as $row) {
				$albumSong = new Song($con, $row);
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

				}



			?>
</div>

		</ul>
	</div>






	</div>

</div>
