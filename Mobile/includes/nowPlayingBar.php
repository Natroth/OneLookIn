<?php
include("config.php");

$songQuery = mysqli_query($con, "SELECT id FROM posts WHERE deleted='no' ORDER BY plays DESC LIMIT 10");

$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) {
	array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);

$_SESSION["resultArray"] = $resultArray;

?>

<head>
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="../assets/js/script.js"></script>
</head>

<script>

$(document).ready(function() {
	var newPlaylist = <?php echo $jsonArray; ?>;
	audioElement = new Audio();
	setTrack(newPlaylist[0], newPlaylist, false);
	updateVolumeProgressBar(audioElement.audio);


	$("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e) {
		e.preventDefault();
	});


	$(".playbackBar .progressBar").mousedown(function() {
		mouseDown = true;
	});

	$(".playbackBar .progressBar").mousemove(function(e) {
		if(mouseDown == true) {
			//Set time of song, depending on position of mouse
			timeFromOffset(e, this);
		}
	});

	$(".playbackBar .progressBar").mouseup(function(e) {
		timeFromOffset(e, this);
	});


	$(".volumeBar .progressBar").mousedown(function() {
		mouseDown = true;
	});

	$(".volumeBar .progressBar").mousemove(function(e) {
		if(mouseDown == true) {

			var percentage = e.offsetX / $(this).width();

			if(percentage >= 0 && percentage <= 1) {
				audioElement.audio.volume = percentage;
			}
		}
	});

	$(".volumeBar .progressBar").mouseup(function(e) {
		var percentage = e.offsetX / $(this).width();

		if(percentage >= 0 && percentage <= 1) {
			audioElement.audio.volume = percentage;
		}
	});

	$(document).mouseup(function() {
		mouseDown = false;
	});




});

function timeFromOffset(mouse, progressBar) {
	var percentage = mouse.offsetX / $(progressBar).width() * 100;
	var seconds = audioElement.audio.duration * (percentage / 100);
	audioElement.setTime(seconds);
}

function prevSong() {
	if(audioElement.audio.currentTime >= 3 || currentIndex == 0) {
		audioElement.setTime(0);
	}
	else {
		currentIndex = currentIndex - 1;
		setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
	}
}

function nextSong() {
	if(repeat == true) {
		audioElement.setTime(0);
		playSong();
		return;
	}

	if(currentIndex == currentPlaylist.length - 1) {
		currentIndex = 0;
	}
	else {
		currentIndex++;
			}

	var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
	setTrack(trackToPlay, currentPlaylist, true);
	//$("#iframe").prop("src", "faveit.php?id=" + currentPlaylist[currentIndex]);


}

function setRepeat() {
	repeat = !repeat;
	var imageName = repeat ? "fa fa-repeat shuffleColor" : "fa-repeat";

		if(repeat == true) {
			$(".controlButton.repeat i").attr("class", "fa fa-repeat shuffleColor");
		}
		else {
			$(".controlButton.repeat i").attr("class", "fa fa-repeat");
		}
}

function setMute() {
	audioElement.audio.muted = !audioElement.audio.muted;
	var imageName = audioElement.audio.muted ? "fas fa-volume-mute" : "fas fa-volume-up";
	if(audioElement.audio.muted == true) {
		$(".controlButton.volume i").attr("class", "fas fa-volume-mute");

	}
	else {
		$(".controlButton.volume i").attr("class", "fas fa-volume-up");

	}


}

function setShuffle() {
	shuffle = !shuffle;
	var imageName = shuffle ? "fa fa-random shuffleColor" : "fa fa-random";

	if(shuffle == true) {
		//Randomize playlist
		$(".controlButton.shuffle i").attr("class", "fa fa-random shuffleColor");

		shuffleArray(shufflePlaylist);
		currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
	}
	else {
		//shuffle has been deactivated
		//go back to regular playlist
		$(".controlButton.shuffle i").attr("class", "fa fa-random");

		currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
	}

}

function shuffleArray(a) {
    var j, x, i;
    for (i = a.length; i; i--) {
        j = Math.floor(Math.random() * i);
        x = a[i - 1];
        a[i - 1] = a[j];
        a[j] = x;
    }
}


function setTrack(trackId, newPlaylist, play) {

	if(newPlaylist != currentPlaylist) {
		currentPlaylist = newPlaylist;
		shufflePlaylist = currentPlaylist.slice();
		shuffleArray(shufflePlaylist);
	}

	if(shuffle == true) {
		currentIndex = shufflePlaylist.indexOf(trackId);

	}
	else {
		currentIndex = currentPlaylist.indexOf(trackId);
	}
	pauseSong();

	$.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, function(data) {

		var track = JSON.parse(data);

		$(".trackName span").text(track.body);
			$(".content .albumLink img").attr("src", "http://onelookin.com/" + track.picture);
			$(".content .albumLink img").attr("ontouchstart", "openPage('song_page.php?id=' + currentPlaylist[currentIndex])");
			$(".trackInfo .trackName span").attr("ontouchstart", "openPage('song_page.php?id=' + currentPlaylist[currentIndex])");

			$("#iframe").prop("src", "faveit.php?id=" + track.id);
			history.pushState({}, null, "song_page.php?id=" + track.id);
			document.title = track.body + " - " +track.added_by;


		$.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist }, function(data) {
			var artist = JSON.parse(data);
			$(".trackInfo .artistName span").text(artist.username);
			$(".trackInfo .artistName span").attr("ontouchstart", "openPage('artist.php?id=" + artist.id + "')");
		});

	$.post("includes/handlers/ajax/getSongJson.php", { songId: trackId }, function(data) {
			var album = JSON.parse(data);
			$(".content .albumLink img").attr("src", "http://onelookin.com/" + track.picture);
	//		$(".content .albumLink img").attr("onclick", "openPage('album.php?id=" + album.id + "')");
	//		$(".trackInfo .trackName span").attr("onclick", "openPage('album.php?id=" + album.id + "')");
		});


		audioElement.setTrack(track);

		if(play == true) {
			playSong();
		}
	});

}

function playSong() {

	if(audioElement.audio.currentTime == 0) {
		$.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id });
	}

	$(".controlButton.play").hide();
	$(".controlButton.pause").show();
	audioElement.play();
}

function pauseSong() {
	$(".controlButton.play").show();
	$(".controlButton.pause").hide();
	audioElement.pause();
}


</script>


<div id="nowPlayingBarContainer">

	<div id="nowPlayingBar" class="nowPlayingBar">
		<span id="nowPlayingLeft" class="nowPlayingLeft">
			<span role="link" tabindex="0"></span>
			<div class="content">
				<span class="albumLink">
					<img role="link" tabindex="0" src="" class="albumArtwork">
				</span>

				<div class="trackInfo" style="display: flex; flex-flow: row; justify-content: space-evenly;">

					<span class="trackName" style="font-size: 2.2rem;">
						<span role="link" tabindex="0"></span>
					</span>
<i class="fas fa-circle"></i>
					<span class="artistName">
						<span role="link" tabindex="0"></span>
					</span>

				</div>

</div>
		</span>
	<div id="barButtonHolder">
		<div class="directionButtons">

						<button class="controlButton previous" style="display: none;" title="Previous button" ontouchstart="prevSong()">
								<i class="fa fa-chevron-left"></i>
						</button>

						<button class="controlButton next" title="Next button" ontouchstart="nextSong();">
							<i class="fa fa-chevron-right"></i>
						</button>
					</div>

		<div id="nowPlayingCenter">

			<div class="content playerControls">




				<div class="playbackBar" style="display: none;">

					<span class="progressTime current">0.00</span>

					<div class="progressBar">
						<div class="progressBarBg" style="display: flex; flex-flow: row; align-items: center">
							<div class="progress"></div>
							<div><i style="color: #28a4af; font-size: 20px; display: contents;" class="fas fa-circle"></i></div>
						</div>
					</div>

					<span class="progressTime remaining">0.00</span>


				</div>

				<div class="buttons">

					<button class="controlButton shuffle" title="Shuffle button" ontouchstart="setShuffle()" style="display: none;">
						<i class="fa fa-random"></i>
					</button>


					<button class="controlButton play" title="Play button" ontouchstart="playSong()">
						<i class="fa fa-play"></i>
					</button>

					<button class="controlButton pause" title="Pause button" style="display: none;" ontouchstart="pauseSong()">
						<i class="fa fa-pause"></i>
					</button>



					<button class="controlButton repeat" title="Repeat button" ontouchstart="setRepeat()" style="display: none;">
						<i class="fa fa-repeat"></i>
					</button>

				</div>


			</div>


		</div>

		<div id="nowPlayingRight">
			<div class="volumeBar">

				<button class="controlButton volume" title="Volume button" ontouchstart="setMute()" style="display: none;">
<i class="fas fa-volume-up"></i>
		</button>
<!--
				<div class="progressBar">
					<div class="progressBarBg">
						<div class="progress"></div>
					</div>

-->
				</div>



				<?php


				if($userLoggedIn = $_SESSION['Guest'])
				{
					echo	'<div>
					<a tabindex="0" id="manual-ajax"><i class="fas fa-star" style="font-size: 3rem !important; margin-top: -1.1rem; color: #000 !important;"></i></a>
					</div>';

		}
			else {

						$post_id = $_GET['id'];
						$_SESSION['post_id'] = $post_id;
						$username = $_SESSION['userLoggedIn'];
						$faveQuery = mysqli_query($con, "SELECT * FROM faves WHERE username = '$username' AND post_id = '$post_id'");
						$getFaves = mysqli_fetch_array($faveQuery);
						$faveValue = $getFaves['id'];

		echo				'<iframe src="faveit.php?id=" width="" height="" id="iframe" frameBorder="0" scrolling="no"></iframe>';

}
?>
			</div>
		</div>
		</div>




	</div>


</div>

<script type="text/javascript">

jQuery(document).ready(function($) {
  $("#manual-ajax").on("touchstart", function(event) {
      window.location.href = 'http://m.onelookin.com/register.php';
    });
 });

</script>



	<div id="ex1" class="modal">



		<?php
			include("includes/config.php");
			include("includes/classes/Account.php");
			include("includes/classes/Constants.php");

			$account = new Account($con);

			include("includes/handlers/register-handler.php");
			include("includes/handlers/login-handler.php");

			function getInputValue($name) {
				if(isset($_POST[$name])) {
					echo $_POST[$name];
				}
			}
		?>

		<html>
		<head>
			<title></title>


			<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
			<script src="assets/js/register.js"></script>
		</head>
		<body>
			<?php

			if(isset($_POST['registerButton'])) {
				echo '<script>
						$(document).ready(function() {
							$("#loginForm").hide();
							$("#registerForm").show();
						});
					</script>';
			}
			else {
				echo '<script>
						$(document).ready(function() {
							$("#loginForm").show();
							$("#registerForm").hide();
						});
					</script>';
			}

			?>


			<div id="background">

				<div id="loginContainer">

					<div id="inputContainer">
						<form id="loginForm" action="register.php" method="POST">
							<h2>Login to your account</h2>
							<p>
								<?php echo $account->getError(Constants::$loginFailed); ?>
								<input id="loginUsername" class="registerInput" name="loginUsername" type="text" placeholder="Email" value="<?php getInputValue('loginUsername') ?>" required autocomplete="off">
							</p>
							<p>
								<input id="loginPassword" class="registerInput" name="loginPassword" type="password" placeholder="Password" required>
							</p>

							<button type="submit" class="registerSubmit" name="loginButton">Login</button>
<br>
							<div class="hasAccountText">
								<br>
								<span id="hideLogin">Don't have an account yet? <span style="color: #28a4af;">Signup here.</span></span>
							</div>

						</form>



						<form id="registerForm" action="register.php" method="POST">
							<h2>Create your free account</h2>
							<p>
								<?php echo $account->getError(Constants::$usernameCharacters); ?>
								<?php echo $account->getError(Constants::$usernameTaken); ?>
								<input id="username" class="registerInput" name="username" type="text" placeholder="Username" value="<?php getInputValue('username') ?>" required>
							</p>


							<p>

								<?php echo $account->getError(Constants::$emailInvalid); ?>
								<?php echo $account->getError(Constants::$emailTaken); ?>
								<input id="email" class="registerInput" name="email" type="email" placeholder="Email" value="<?php getInputValue('email') ?>" required>
							</p>



							<p>
								<?php echo $account->getError(Constants::$passwordsDoNoMatch); ?>
								<?php echo $account->getError(Constants::$passwordNotAlphanumeric); ?>
								<?php echo $account->getError(Constants::$passwordCharacters); ?>
								<input id="password" class="registerInput" name="password" type="password" placeholder="Password" required>
							</p>

							<p>
								<input id="password2" name="password2" class="registerInput" type="password" placeholder="Confirm Password" required>
							</p>

							<button type="submit" class="registerSubmit" name="registerButton">Sign Up</button>
<br>
							<div class="hasAccountText">
								<br>
								<span id="hideRegister">Already have an account?<span style="color: #28a4af;"> Log in here.</span></span>
							</div>

						</form>


					</div>



				</div>
			</div>

		</body>
		</html>





	</div>



