<?php
	class Artist {

		private $con;
		private $id;

		public function __construct($con, $id) {
			$this->con = $con;
			$this->id = $id;
		}

		public function getId() {
			return $this->id;
		}

		public function getName() {
			$artistQuery = mysqli_query($this->con, "SELECT username FROM users WHERE id='$this->id'");
			$artist = mysqli_fetch_array($artistQuery);
			return $artist['username'];
		}

		public function getProfilePic() {
			$artistQuery = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE id='$this->id'");
			$artist = mysqli_fetch_array($artistQuery);
			return $artist['profile_pic'];
		}

		public function getSongIds() {

			$query = mysqli_query($this->con, "SELECT id FROM posts WHERE artist ='$this->id' ORDER BY likes ASC LIMIT 6");

			$array = array();

			while($row = mysqli_fetch_array($query)) {
				array_push($array, $row['id']);
			}

			return $array;

		}
	}
?>
