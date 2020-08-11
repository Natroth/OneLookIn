<?php
include("includes/includedFiles.php");

$resultArray = $_SESSION["resultArray"];
$jsonArray = json_encode($resultArray);
$id = $_GET['id'];
?>

  <span role="link" tabindex="0" onclick="openPage('browse.php')"><i class="far fa-times-circle" style="font-size: 3rem; margin: 5% !important;"></i></span>


  <script type="text/javascript">
  $(document).ready(function() {
  	document.getElementById("logoButton").src = "assets/images/icons/simplelogoblack2.png";
  });
  </script>



<script>

var id = <?php echo $id; ?>;


  $(document).ready(function() {

if ( id == currentPlaylist[currentIndex]) {

  var newPlaylist = <?php echo $jsonArray; ?>;

}

else {


	var newPlaylist = <?php echo $jsonArray; ?>;

  audioElement = new Audio();
	setTrack(id, newPlaylist, false);
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


	$(".progressBar").mousedown(function() {
		mouseDown = true;
	});

	$(".progressBar").mousemove(function(e) {
		if(mouseDown == true) {

			var percentage = e.offsetX / $(this).width();

			if(percentage >= 0 && percentage <= 1) {
				audioElement.audio.volume = percentage;
			}
		}
	});

	$(".progressBar").mouseup(function(e) {
		var percentage = e.offsetX / $(this).width();

		if(percentage >= 0 && percentage <= 1) {
			audioElement.audio.volume = percentage;
		}
	});

	$(document).mouseup(function() {
		mouseDown = false;
	});




}});

</script>


<style media="screen">

.navBar {
    display: none !important;
}

#nowPlayingBarContainer {
  width: 100%;
    background-color: #fff !important;
    top: 0;
    padding-top: 2rem !important;
    min-width: 620px;
    position: relative !important;
    max-width: none !important;
    overflow: scroll;
-webkit-overflow-scrolling: touch;
}

#nowPlayingBar {
  display: flex;
      flex-flow: column;
      align-items: center;
      margin: auto;
      height: auto !important;
}

#nowPlayingLeft {
  width: 100% !important;
  text-align: center;
max-width: none !important;
font-size: 3rem;
padding-top: 1rem;
}

#nowPlayingRight {
  position: relative;
    margin-top: 13rem !important;
    width: 100% !important;
    display: flex;
    flex-flow: row;
    justify-content: space-evenly;
    font-size: 3rem;
}

#nowPlayingCenter {
  width: 95% !important;
  padding-top: 4rem;
  max-width: none !important;
}

#nowPlayingBar .content {
  width: 100%;
  height: auto !important;
  display: initial;
  flex-flow: column;
}

.playerControls .buttons {
  margin: 0 auto;
      display: flex !important;
      flex-flow: row;
      justify-content: space-around;
      position: absolute;
      width: 95% !important;
      margin: auto;
      text-align: center;
      padding-top: 5rem;
}

.controlButton {
  background-color: transparent;
    border: none;
    vertical-align: middle;
    font-size: 3rem;
}

.controlButton img {
  width: 20px;
    height: 20px;
}

.controlButton.play img,
.controlButton.pause img {
  width: 32px;
  height: 32px;
}

.controlButton:hover {
  cursor: pointer;
}

.progressTime {
  color: #a0a0a0;
    font-size: 22px !important;
    min-width: none !important;
    text-align: center;
    margin: 17px;
}

.playbackBar {
  display: flex !important;
}

.progressBar {
    height: 12px;
    display: inline-flex;
    cursor: pointer;
}

.progressBarBg {
  background-color: #404040;
    height: 7px !important;
    border-radius: 2px;
    width: 100%;
}

.progress {
  background-color: #a0a0a0;
    height: 7px !important;
    border-radius: 2px;
}

.playbackBar .progressBar {
  margin-top: 3px;
}

#nowPlayingLeft .albumArtwork {
  height: auto !important;

    margin-right: 0px !important;
    float: none !important;
    background-size: cover;
    width: 30vh !important;
  max-width: none !important;
}

#nowPlayingLeft .trackInfo {
  display: block !important;
  padding-top: 2rem;
}

#nowPlayingLeft .trackInfo .trackName {
  margin: 6px 0;
    display: inline-block !important;
    width: 100%;
    max-width: 300px !important;

}

#nowPlayingLeft .trackInfo .artistName span {
  font-size: 2rem !important;
    color: #a0a0a0;
    max-width: none !important;

}

.volumeBar {
  width: auto !important;
    position: relative !important;
    left: 0;
    margin-top: -1rem;
}

.volumeBar .progressBar {
  width: 125px;
}

.artistName {
  font-size: 2rem !important;
}

.fa-random, .fa-repeat, .fa-chevron-right, .fa-chevron-left {
  font-size: 3rem;
}

.fa-play, .fa-pause {
  font-size: 5rem;
}

.next, .previous {
  padding-bottom: 3rem;
}

.previous {
  display: flex !important;
}

.directionButtons {
  display: flex !important;
      width: 100%;
      flex-flow: row;
      justify-content: space-around;
    margin-bottom: -3rem;
    margin-top: -5rem;      
    }

#mainContainer {
  height: 0 !important;
}

#mainViewContainer {
		margin-bottom: 0rem !important;
}

.shuffle, .repeat, .volume {
  display: flex !important;
}

#barButtonHolder {
	display: contents !important;
  direction: ltr !important;

}

#iframe {
	display: flex;
    flex-flow: column;
    border: ;
    margin: 0;
		width: 3.2rem;
		overflow: hidden;
}

#iframe.fa-star {
  font-size: 3rem !important;
}

.fa-circle {
display: none;
}


</style>
