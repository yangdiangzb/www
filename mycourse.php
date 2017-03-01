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

	$cid = $_GET['cid'];

	error_reporting(E_ALL ^ E_DEPRECATED);
	$db = new PDO("mysql:host=localhost;dbname=fyp", "root", "");

	$statement = $db->prepare("SELECT name FROM course WHERE cid = :cid");
	$statement->execute(array(':cid' => $cid));
	$result = $statement->fetch();
	$name = $result['name'];
	$statement->closeCursor();

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
    <title>My course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    <?php echo $name;?></br>
</header>

<body>
<nav class="navigation">
        	<a href="index.php">Homepage</a>
	       	<a href="allcourse.php">All Courses</a>
			<a href="usercourse.php">My Courses</a>
			<a href="userprofile.php">My profile</a>
</nav>
<div id="courses">
	<section class="eachcourse"><ul><li style="margin-left:-12px; display: block; text-align:center">
		<?php
				//find learned chapters
				$num = 0;
				$statement2 = $db->prepare("SELECT C.num FROM chapter C natural join learn L WHERE L.uid = :uid AND L.cid = :cid");
				$statement2->execute(array(':uid' => $uid, ':cid' => $cid));
				if($row2 = $statement2->fetch())
					$num = $row2['num'];//passed chapter

				$statement3 = $db->prepare("SELECT C.name, C.chid, C.num FROM chapter C WHERE C.num <= :num+1 AND C.cid = :cid");
				$statement3->execute(array(':num' => $num, ':cid' => $cid));

				while($row3 = $statement3->fetch()){
					//show chapter name and link
					echo "<a href='mychapter.php?chid={$row3['chid']}'><h3>Chapter ".$row3['num']." ".$row3['name']." </h3></a><br>";
				}

				$statement4 = $db->prepare("SELECT COUNT(*) AS count FROM chapter WHERE cid = :cid");
				$statement4->execute(array(':cid' => $cid));
				$result = $statement4->fetch();
				if($num == $result['count']) echo "You have finished this course! <br>";
				else {
					$statement5 = $db->prepare("SELECT name, chid, num FROM chapter WHERE cid = :cid AND num > :num+1");
					$statement5->execute(array(':cid' => $cid, ':num' => $num));
					while($row5 = $statement5->fetch()){
						//show chapter name without link
						echo "<p><img src=images/lock.png width=15px style=\"background-color:white\"/>";
						echo "Chapter ".$row5['num']." ".$row5['name']."</p><br>";
					}
				}

				$statement4->closeCursor();
				$statement3->closeCursor();
				$statement2->closeCursor();
				echo "<br>";
		?>
	</section>
</div>

</body>
</html>
