<?php
include("includes/includedFiles.php");

$resultArray = $_SESSION["resultArray"];
$jsonArray = json_encode($resultArray);
$id = $_GET['id'];
?>

  <span role="link" tabindex="0" onclick="openPage('browse.php')"><i class="far fa-times-circle" style="font-size: 3rem; margin: 5rem !important;"></i></span>


  <div class="">
    <a tabindex="0" href="#ex1" rel="modal:open" class="navItemLink"><i class="fa fa-star"></i></a>
  </div>

<script>

var id = <?php echo $id; ?>;


  $(document).ready(function() {

    $('#ex1').modal();

  var newPlaylist = <?php echo $jsonArray; ?>;

});


</script>

</script>
