<?php 
include("includes/header.php");


if(isset($_POST['post'])){
	

$baseFromJavascript = $_POST['myHiddenField']; // $_POST['base64']; //your data in base64 'data:image/png....';
// We need to remove the "data:image/png;base64,"
$base_to_php = explode(',', $baseFromJavascript);
// the 2nd item in the base_to_php array contains the content of the image
$data = base64_decode($base_to_php[1]);
// here you can detect if type is png or jpg if you want

// Save the image in a defined path

	$uploadOk = 1;
	$songName = $_FILES['fileToUpload']['name'];
	$picName =  $_FILES['picToUpload']['name'];
	$errorMessage = "";


	if($songName != "") {
		$targetDir = "assets/audios/posts/";
		$songName = $targetDir . uniqid() . basename($songName);
		$songFileType = pathinfo($songName, PATHINFO_EXTENSION);

		if($_FILES['fileToUpload']['size'] > 100000000) {
			$errorMessage = "Audio file is to large";
			$uploadOk = 0;

		}
	

		if(strtolower($songFileType) != "mp3" && strtolower($songFileType) != "wav" && strtolower($songFileType) != "flac" && strtolower($songFileType) != "ogg") {
			$errorMessage = "File type not supported. Try mp3, wav or flac!";
			$uploadOk = 0;
		}		
		

		if($uploadOk) {
			if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $songName)) {
				//song uploaded
			}
			else {
				$uploadOk = 0;
			}
		}
		}

	if ($picName != "") {
		$targetDirPic = "assets/images/posts/";
		$picName = $targetDirPic . uniqid() . basename($picName);
		$picFileType = pathinfo($picName, PATHINFO_EXTENSION);
		
		

		if(strtolower($picFileType) != "jpg" && strtolower($picFileType) != "png" && strtolower($picFileType) != "tiff" && strtolower($picFileType) != "gif") {
			$errorMessage = "File type not supported. Try jpg, png or gif!";
		}
		
		if($uploadOk) {
			if(file_put_contents($picName, $data)) {
				//song uploaded
			}
			else {
				$uploadOk = 0;
				$errorMessage = "Picture didn't upload, Try Again!";
			}
		}

}
		if($uploadOk == 1) {
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'],'none', $songName, $_POST['genre'], $picName, $_POST['duration'], $_POST['displayValue']);
		}
		else {
			echo "<div style='text-align:center;' class='alert alert-danger'>
			$errorMessage
			</div>";
		}
 
	}
 

 ?>





<div class= "main_column column" style="margin-top: 7rem !important; width: 77%; margin: auto;">

<h1 class="uploadHeader">Upload A Song</h1>
<br>
<form class="post_form" id="post_form" method="post" enctype="multipart/form-data">

	<textarea name="post_text" class="post_text" id="post_text" placeholder="Song Title" maxlength="180" required></textarea>
	<br>	

	<div class="filesSkin">
		<input type="file" name="fileToUpload" id="fileToUpload" style="display: none;" required>
		<label for="fileToUpload" class="custom-fileToUpload unset-button" name="custom-fileToUpload" id="custom-fileToUpload">
		Upload Track
		</label>
		<audio id="audio10" style="display:none"></audio>
		<div id="picUploadForm">
		<input type="file" name="picToUpload" id="picToUpload" class="picToUpload" style="display: none;" required> 
		<label for="picToUpload" class="custom-picToUpload unset-button" name="custom-picToUpload" id="custom-picToUpload">
		Upload Picture
		</label>	
		</div>
	</div>

   <img id="uploaded_image" class="uploaded_image" style="display: none">
	<input type="hidden" name="myHiddenField">
  <input id="duration" name="duration" type="text" style="display: none;">

<br>
	<div class="filesSkin">
		<select style="width: 15rem;" class="genreSelect" name="genre" onmousedown="if(this.options.length>5){this.size=5;}" onchange="this.blur()"  onblur="this.size=0;" required=s>
		<option value="" disabled selected>Genre</option>	
			<option value="acoustic">Acoustic</option>
			<option value="classical">Classical</option>
			<option value="country">Country</option>
			<option value="electric">Electric</option>	
			<option value="folk">Folk</option>		
			<option value="hip-hop">Hip-Hop</option>
			<option value="jazz">Jazz</option>
			<option value="pop">Pop</option>		
			<option value="rock">Rock</option>
			<option value="randb">R&B</option>
			<option value="vibe">Vibe</option>
			<option value="other">Other</option>	
			
		</select>

		<?php 

			$collectionQuery2 = mysqli_query($con, "SELECT * FROM collections WHERE added_by = '$userLoggedIn' AND name != ''");


		?>



		<div style="position:relative;width:15rem;height:25px;border:0;padding:0;margin:0;">
		<select style="position:absolute;top:0px;left:0px;width:15rem; height:25px;line-height:20px;margin:0;padding:0;"
				onchange="document.getElementById('displayValue').value=this.options[this.selectedIndex].text; document.getElementById('idValue').value=this.options[this.selectedIndex].value;">
			<option></option>

			<?php 


		while ($row2 = mysqli_fetch_array($collectionQuery2)) {
			$fruit = $row2['name'];
			echo  "
			<option value = $fruit >
				$fruit  	 
			</option>
			";
			}

			?>	
		</select>
		<input required type="text" name="displayValue" id="displayValue" 
				placeholder="Collection" onfocus="this.select()"
				style="position:absolute;top:0px;left:0px;width:183px;width:14rem;#width:14rem;height:25px; height:21px\9;#height:20px;"  >
		<input name="idValue" id="idValue" type="hidden">
		</div>
	</div>

<br>
<div style="display: flex; align-items: center;">
<input type="checkbox" name="promise" value="promise" required><div class="promise-text">&nbsp I have complete ownership/rights for all content being uploaded<br></div>
</div>
<br>
<input type="submit" class="unset-button" style="border: none; background-color: #28a4af; color:#fff;" name="post" id="post_button" value="Post" onclick="warning() uploading()">
	
	</form>
<br><br>


</div>
		

<div id="uploadimageModal" class="modal" role="dialog"  data-keyboard="false" data-backdrop="static">
 <div class="modal-dialog">
  <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload & Crop Image</h4>
        </div>
        <div class="modal-body">
          <div class="row">
       <div class="col-md-8 text-center">
        <div id="image_demo" style="width:350px; margin-top:30px"></div>
       </div>
       <div class="col-md-4" style="padding-top:30px;">
        <br />
        <br />
        <br/>
        <button class="btn btn-success crop_image">Crop & Upload Image</button>
     </div>
    </div>
        </div>
<br>
     </div>
    </div>
</div>		


<style>

.modal-backdrop {
	position: fixed;
}

.modal-dialog {
    max-width: 600px;
	margin: 30px auto;
}

.btn-success {
    color: #fff;
    background-color: #28a4af;
    border-color: #dcdcdc;
    margin-top: 3rem;
}
.alert {
    font-family: brandon-grotesque, sans-serif;

    position: relative;
    padding: 0.75rem 1.25rem;
    margin-top: 8rem;
    border: 1px solid transparent;
    border-radius: 0.25rem;
    width: 39rem;
	margin: auto;
	top: 31rem;
}

</style>

	<script>

	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	
	function warning() {
    document.getElementById("stay_here").style.visibility = "visible";
}
	function uploading() {
	document.body.style.cursor='wait'; return true;
	}
	
	function move() {
    var elem = document.getElementById("myBar"); 
    var width = 1;
    var id = setInterval(frame, 220);
    function frame() {
        if (width >= 100) {
            clearInterval(id);
        } else {
            width++; 
            elem.style.width = width + '%'; 
        }
    }
}



   $(function(){
 
       var userLoggedIn = '<?php echo $userLoggedIn; ?>';
       var inProgress = false;
 
 
       //Check if the element is in view
       function isElementInView (el) {
             if(el == null) {
                return;
            }
 
           var rect = el.getBoundingClientRect();
 
           return (
               rect.top >= 0 &&
               rect.left >= 0 &&
               rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
               rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
           );
       }
   });
 

var objectUrl;

$("#audio10").on("canplaythrough", function(e){
    var seconds = e.currentTarget.duration;
    var duration = moment.duration(seconds, "seconds");
    
    var time = "";
    var hours = duration.hours();
    if (hours > 0) { time = hours + ":" ; }
    
    time = time + duration.minutes() + ":" + duration.seconds();
    $("#duration").val(time);
    
    URL.revokeObjectURL(objectUrl);
});

$("#fileToUpload").change(function(e){
    var file = e.currentTarget.files[0];
   
    
    objectUrl = URL.createObjectURL(file);
    $("#audio10").prop("src", objectUrl);
});




$(document).ready(function(){

 $image_crop = $('#image_demo').croppie({
    enableExif: true,
    viewport: {
      width:200,
      height:200,
      type:'square' //circle
    },
    boundary:{
      width:300,
      height:300
    }
  });

  $('#picToUpload').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
        url: event.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
    }
    reader.readAsDataURL(this.files[0]);
    $('#uploadimageModal').modal('show');
  });

  $('.crop_image').click(function(event){
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
	var image = new Image();
	image.src = response;
//	document.body.appendChild(image);   
	console.log(image);

	$('input[name=myHiddenField]').val(response);
	$('input[name=picToUpload]').attr('value', response);

    $('#uploaded_image').attr('src', response);    	
       $('#uploadimageModal').modal('hide');
   		
    })
  });

});  


$('input[name=picToUpload]').change(function(ev) {

	$("#custom-picToUpload").prop('disabled', true).css({backgroundColor:'#28a4af'});
	$("#custom-picToUpload").prop('disabled', true).css({color:'#fff'});

	$("#custom-picToUpload").text("Picture Selected");

});

$('input[name=fileToUpload]').change(function(ev) {

$("#custom-fileToUpload").prop('disabled', true).css({backgroundColor:'#28a4af'});
$("#custom-fileToUpload").prop('disabled', true).css({color:'#fff'});

$("#custom-fileToUpload").text("Track Selected");

});



   </script>




</div>



</body>
</html>