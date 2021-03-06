<?php
error_reporting(0);
session_start();
	session_regenerate_id();
	$login = 1;
	if(!isset($_SESSION['userid'])) $login = 0;
	if(!isset($_COOKIE['auth'])) $login = 0;
	if($_COOKIE['auth'] != $_SESSION['token']) $login = 0;
	if(isset($_SESSION['userid'])) $uid = $_SESSION['userid'];

$dsn = 'mysql:dbname=fyp;host=127.0.0.1';
$user = 'root';
$password = '';
try {
	$dbh = new PDO($dsn,$user,$password);
} catch (PDOException $e) {
	printf("DatabaseError: %s", $e->getMessage());
}

if($login == 1){
		$statement = $dbh->prepare("SELECT name FROM user WHERE uid = :uid");
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
    <title>Course</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    <?php 
    $stmt3 = $dbh->prepare("select * from course where cid=?;");
	     if($stmt3->execute(array($_GET['cid']))){
		    while ($row = $stmt3->fetch()) {
				echo $row['name'];
			}
		 }
		 ?>
</header>
<body>
<nav class="navigation">
	    <?php
	        	echo "<a href=index.php>Home page</a>";
				echo "<a href=allcourse.php >All courses</a>";
	        	if($login==1){
					echo "<a href=usercourse.php>My courses</a>";
		 			echo "<a href=userprofile.php>My profile</a>";
	    		}
	  	?>
</nav>
<nav class="course">
    	<?php
		 $stmt3 = $dbh->prepare("select * from course where cid=?;");
	     if($stmt3->execute(array($_GET['cid']))){
		    while ($row = $stmt3->fetch()) {
				echo "<h2>".$row['name']."</h2>";
				echo "<img src=images/".$row['cid']." height=360 width=500 /><br><br>";
				echo "<p>".$row['description']."</p><br>";
				echo "<form method=post action=enroll.php?cid=".$row['cid'].">";
				echo "<input type=submit name=submit value=Enroll>";
				echo "</form>";
			}
		 }
		?>
</nav>
</body>
</html>

<footer id="footer"> 
        <hr>
        IEEE Acamedic E-learning System<br>
        The Chinese University of Hong Kong<br>
</footer>
<?php
$dbh=null;
?>