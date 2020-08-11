<div id="navBarContainer">



	<nav class="navBar">

		<div class="navItem">
		<span role="link" tabindex="0" ontouchstart="openPage('index.php')" class="logo">
			<img id="logoButton" src="assets/images/icons/simplelogoblack2" alt="" style="width: 4.5rem;">

		</span>
</div>

		<div class="group">

			<div class="navItem">
				<span role='link' tabindex='0' ontouchstart='openPage("search.php")' class="navItemLink">
					<i class="fa fa-search"></i>
				</span>
			</div>

		</div>

		<div class="group">
			<div class="navItem">
				<span role="link" tabindex="0" ontouchstart="openPage('genre_select.php')" class="navItemLink"><i class="fa fa-bars"></i></span>
			</div>
		</div>
<?php
	if($userLoggedIn = $_SESSION['Guest'])
	{

		echo
		'<div class="navItem">
			<a tabindex="0" id="navStarButton" href="#ex1" rel="modal:open" class="navItemLink"><i class="fa fa-star"></i></a>
		</div>';

	}
		else {


		echo
			'<div class="navItem">
				<span role="link" style="color: #000 !important;" tabindex="0" ontouchstart="openPage(\'' . "yourMusic.php" . '\')" class="navItemLink"><i class="fas fa-star"></i></span>

			</div>';
		}
?>
			<div class="navItem">
				<span role="link" tabindex="0" ontouchstart="openPage('upload_intro.php')" class="navItemLink"><i class="fas fa-upload"></i></span>
			</div>


	</nav>
</div>
<style>
.fa-star {
	font-size: 4rem !important;
}

</style>
