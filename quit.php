<div class = "header">
<img src="images/logo.png" width=12% height=12% float:left>
</div>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<header class="header">
	<meta charset="UTF-8" />
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
    <title>Quit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    Welcome to e-learning system!</br>
</header>
<body>
<div class="home">
		<?php
		//check cookie and php session
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

		//get cid from url parameter
		$cid = $_GET['cid'];


		error_reporting(E_ALL ^ E_DEPRECATED);
		$connection = mysql_connect("127.0.0.1", "root", "");
		$db = mysql_select_db("fyp", $connection);
	        
		$result = mysql_query("DELETE FROM learn WHERE uid='$uid' AND cid='$cid'");
		$result1 = mysql_query("DELETE FROM scores WHERE uid='$uid' AND cid='$cid'");
		if($result)
		{
			echo "<h1>You have Successfully Quit this Course!</h1>";
		   }
		else
		{
			echo $result;
		}
		echo "<br><br><br><br><br><br>";
		echo "<a href=usercourse.php><h1>Go to My Courses<h1></a>";
		?>
 </div>
</body>
</html>
<footer id="footer"> 
        <hr>
        IEEE Acamedic E-learning System<br>
        The Chinese University of Hong Kong<br>
</footer>