<?php

$con = mysqli_connect('aa25dqkbwfsa09.cuzbw4369xvy.us-west-2.rds.amazonaws.com', 'edible', 'edible001', 'blue_cheese', 3306);	

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}



$result = mysqli_query($con,"SELECT * FROM ");

while($result != "" && $row = mysqli_fetch_array($result)) {
  echo $row['title'];
  echo "\r\n";
}

mysqli_close($con);
?> 	
