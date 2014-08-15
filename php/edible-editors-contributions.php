<?php 

/*
Template Name: editor-contributions
*/
header("Content-Type: text/html;charset=utf-8");
get_header(); ?>

<?php 
	/*SQL connection*/
	$con = mysqli_connect('aa25dqkbwfsa09.cuzbw4369xvy.us-west-2.rds.amazonaws.com', 'edible', 'edible001', 'blue_cheese', 3306);	

	// Check connection
	if (mysqli_connect_errno()) {
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

?>



	<div id="primary" class="site-content">
		<div id="content" role="main">
			
			<?php 
				sqlRequest("ceo@1keji.com","Ruihao Min",$con);
				sqlRequest("gcte2010@gmail.com","Charlie Gao",$con);
				sqlRequest("xiranzheng1992@gmail.com","xiranzheng1992",$con);
				sqlRequest("wangyimillet@gmail.com","wangyimillet",$con);
				sqlRequest("muyujie1028@163.com","muyujie1028",$con);
				sqlRequest("ziqsu43@gmail.com","ziqsu43",$con);
				sqlRequestTotal($con);
				mysqli_close($con);
			?>
			

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_sidebar( 'front' ); ?>
<?php get_footer(); ?>






<?php


function sqlRequest($email,$showName,$con){
	echo "<b>".$showName."</b><br>";
	
	
	
	$result = mysqli_query($con,"select distinct * from foods_CN join users where users.email= '".$email."' and users.uid = foods_CN.uid and privilege = 1 and users.uid != 0 order by last_edit_time Desc");
	
	//$result = mysqli_query($con,"SELECT * FROM foods_CN where fid >1790");
	$counter = 0;
	while($result != "" && $row = mysqli_fetch_array($result)) {
		$counter++;
	  echo " ".$row['title'].""."<br>";	  
	  
	}
	echo "<b>Total: ".$counter."</b><br><br>";
	
	
	
}

function sqlRequestTotal($con){
	
	
	$result = mysqli_query($con,"select distinct count(*) from foods_CN join users where users.uid = foods_CN.uid and privilege = 1 and users.uid != 0 order by last_edit_time Desc");
	
	//$result = mysqli_query($con,"SELECT * FROM foods_CN where fid >1790");

	while($result != "" && $row = mysqli_fetch_array($result)) {
		
	  echo " <b>Over All Total: ".$row[0]."</b><br><br>"."<br>";	  
	  
	}
	
	
	
	
}

?>