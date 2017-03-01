<?php
	session_start();
	session_regenerate_id();
	$login = 1;
	if(!isset($_SESSION['userid'])) $login = 0;
	if(!isset($_COOKIE['auth'])) $login = 0;
	if($_COOKIE['auth'] != $_SESSION['token']) $login = 0;
	
	if(!$login)
	{
		$_SESSION = array();
		unset($_COOKIE['auth']); 
		setcookie('auth', null, -1); 
		session_unset(); 
		session_destroy();
		echo "<script> alert('Please login first!'); window.location.href='login.php';</script>";
		exit;
	}
	if(isset($_SESSION['userid'])) $uid = $_SESSION['userid'];

	error_reporting(E_ALL ^ E_DEPRECATED);
	$db = new PDO("mysql:host=localhost;dbname=fyp", "root", "");
	if($login == 1){
		$statement = $db->prepare("SELECT name FROM user WHERE uid = :uid");
		$statement->execute(array(':uid' => $uid));
		$user = $statement->fetch();
		$uname = $user['name'];
	}
	
	//logout
	if(isset($_POST['Logout'])){
		session_start();
		$_SESSION = array();

		unset($_COOKIE['auth']); 
		setcookie('auth', null, -1); 
		session_unset(); 
		session_destroy();

		header("Location: index.php");
	}
?>
<div class = "header">
<img src="images/logo.png" width=12% height=12% float:left>
	<div class="user">
		<?php
			if($login == 1){
				echo "User: ".$uname;
				echo "<form method='post'>";
					echo "<input type='submit' name='Logout' value='Logout'>";
				echo "</form>";
			}
		?>
		<?php
		if($login==0){
			echo "<a href=login.php >Login     </a>";
			echo "<button onclick=\"location.href='Register.php'\">Sign Up</button>";
		}
		?>
	</div>
</div>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<header class="header">
	<meta charset="UTF-8" />
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
    <title>My Courses</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    My courses</br>
</header>

<body>
		<nav class="navigation">
	        <?php
	        	if($login==1){
	        		echo "<a href=\"index.php\">Homepage</a>";
	        		echo "<a href=\"allcourse.php\">All Courses</a>";
					echo "<a href=\"usercourse.php\">My Courses</a>";
					echo "<a href=\"userprofile.php\">My profile</a>";
	    		}
	    	?>
		</nav>

		<nav class="img">
		<?php
			$statement1 = $db->prepare("SELECT C.name, C.cid FROM course C natural join learn L WHERE L.uid = :uid GROUP BY C.cid");
			$statement1->bindParam(':uid', $uid);
			$statement1->execute();
			while($row1 = $statement1->fetch()){
				//show course name
				$cid = $row1['cid'];
				echo "<a href=mycourse.php?cid=".$row1['cid']."><img src=images/".$row1['cid']. " height=180 width=250 /></a>";
                echo "<a href=mycourse.php?cid=".$row1['cid']."><p>".$row1['name']."</p></a><br>";
			}
		?>
		</nav>
		<br><br>
		<button id="enroll" onclick="location.href='allcourse.php'">Enroll new courses</button>
</body>
</html>
<footer id="footer"> 
        <hr>
        IEEE Acamedic E-learning System<br>
        The Chinese University of Hong Kong<br>
</footer>
