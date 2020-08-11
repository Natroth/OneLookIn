<?php
	class Song {

		private $con;
		private $id;
		private $mysqliData;
		private $title;
		private $artistId;
		private $albumId;
		private $genre;
		private $duration;
		private $path;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;

			$query = mysqli_query($this->con, "SELECT * FROM posts WHERE id='$this->id'");
			$this->mysqliData = mysqli_fetch_array($query);
			$this->title = $this->mysqliData['body'];
			$this->artistId = $this->mysqliData['artist'];
			$this->songPlays = $this->mysqliData['plays'];

			$this->datePosted = $this->mysqliData['date_added'];	
			$this->genre = $this->mysqliData['genre'];
			$this->duration = $this->mysqliData['duration'];
			$this->path = $this->mysqliData['path'];
			$this->artworkPath = $this->mysqliData['picture'];
		}


		public function getTitle() {
			return $this->title;
		}
	
		public function getDatePosted() {
			return $this->datePosted;
		}		

		public function getSongPlays() {
			return $this->songPlays;
		}		

		public function getId() {
			return $this->id;
		}

		public function getArtist() {
			return new Artist($this->con, $this->artistId);
		}


		public function getPath() {
			return $this->path;
		}

		public function getDuration() {
			return $this->duration;
		}

		public function getArtworkPath() {
			return $this->artworkPath;
		}

		public function getMysqliData() {
			return $this->mysqliData;
		}

		public function getGenre() {
			return $this->genre;
		}

	}
?>
