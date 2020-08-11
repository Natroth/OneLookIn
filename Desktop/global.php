<?php 
include ("includes/header.php");
 ?>

 <h3 class="top_songs"> Top Songs</h3>

 <div class="user_details column"> 
<a href="<?php echo $userLoggedIn; ?>"> 

<img src= "<?php echo $user['profile_pic'] ; ?>">
</a>
<br>

<a href="<?php echo $userLoggedIn; ?>" style="font-size:24px; color: #484848;"> 




<div class= "user_details_left_right">
	<?php
	echo $user['username'];
	  ?>
	</a>
	<br>
	<?php 
	echo "Songs:" . "&nbsp" .$user['num_songs'] . "<br>";
	echo "Likes:" . "&nbsp" .$user['num_likes'];
	 ?>
	</div>
</div>


<div class=" column group_box">
	<h4 style="color: #315454; font-size: 25px; font-family: Raleway-Medium;">Groups</h4>
	<hr>
	<div class="genre_names">
		<a href="acoustic.php" style="font-size: 23px;">Acoustic</a><br><br>
		<a href="classical.php" style="font-size: 23px;">Classical</a><br><br>
		<a href="electric.php" style="font-size: 23px;">Electric</a><br><br>
		<a href="folk.php" style="font-size: 23px;">Folk</a><br><br>								
		<a href="hip_hop.php" style="font-size: 23px;">Hip-Hop</a><br><br>
		<a href="jazz.php" style="font-size: 23px;">Jazz</a><br><br>		
		<a href="pop.php" style="font-size: 23px;">Pop</a><br><br>
		<a href="rock.php" style="font-size: 23px;">Rock</a><br><br>
		<a href="randb.php" style="font-size: 23px;">R&B</a><br><br>		
	
		<a href="other.php" style="font-size: 23px;">Other</a>					
	</div>
</div>


<div class= "main_column_global">




	 <div class="posts_area"></div>
	 <center><img id="loading" src= "assets/images/icons/loading.gif"> </center>


		
<script>
   $(function(){
 
       var userLoggedIn = '<?php echo $userLoggedIn; ?>';
       var inProgress = false;
 
       loadPosts(); //Load first posts
 
       $(window).scroll(function() {
           var bottomElement = $(".status_post").last();
           var noMorePosts = $('.posts_area').find('.noMorePosts').val();
 
           // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
           if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
               loadPosts();
           }
       });
 
       function loadPosts() {
           if(inProgress) { //If it is already in the process of loading some posts, just return
               return;
           }
          
           inProgress = true;
           $('#loading').show();
 
           var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
 
           $.ajax({
               url: "includes/handlers/ajax_load_global_posts.php",
               type: "POST",
               data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
               cache:false,
 
               success: function(response) {
                   $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage
 
                   $('#loading').hide();
                   $(".posts_area").append(response);
 
                   inProgress = false;
               }
           });
       }
 
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
 
   </script>

</div>

</div>
</body>
</html>