<!DOCTYPE html>
<?php
define('INCLUDE_CHECK',true);
include_once("config.php");
require 'functions.php';
require 'csrf.php';

session_start();
session_regenerate_id();
$login = 1;
if(isset($_POST['logout'])){
	unset($_COOKIE['auth']);
	setcookie('auth', null, -1);
	session_unset();
	session_destroy();
	header("Location: login.php");
}
if(!isset($_SESSION['admin']) || $_SESSION['admin'] != 3) $login = 0;
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
if(isset($_SESSION['sid'])) $uid = $_SESSION['sid'];
csrfguard_start();
?>
<html>
<head>
	<title>Admin Panel</title>

	<style type="text/css">
	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}
	#error
	{
	color: brown;
	font-family:Verdana, Geneva, sans-serif;
	font-weight:bolder;
	text-transform:capitalize;
	}
	</style>
</head>
	<body>
		<header>
			<h1>Admin Panel</h1>
		</header>
		<form method="POST">
			<button type="submit" name="logout">Logout</button>
		</form><br>
		<?php
		if(isset($error))
		{
			echo '<div id="error">'.$error.'<br></div>';
		}
		?>
		<?php 
			$stmt = $dbh->prepare('SELECT name FROM user WHERE uid = :uid');
			$stmt->bindParam(':uid', $_SESSION["sid"]);
			$stmt->execute();
			$row = $stmt->fetch();
			echo '<h2>'.$row['name'].'</h2>';
			unset($stmt);
		?>
		<a href="adminpanel.php"><h3>Return to adminpanel</h3></a>
		<?php
			$stmt1 = $dbh->prepare("SELECT C.name, C.cid FROM course C natural join learn L WHERE L.uid = :uid GROUP BY C.cid");
			$stmt1->bindParam(':uid', $_SESSION["sid"]);
			$stmt1->execute();
			while($row1 = $stmt1->fetch()){
				$cid = $row1['cid'];
				echo "<fieldset><legend>".$row1['name']."</legend>";
				$stmt2 = $dbh->prepare("SELECT C.num FROM chapter C natural join learn L WHERE L.uid = :uid AND L.cid = :cid");
				$stmt2->execute(array(':uid' => $_SESSION["sid"], ':cid' => $cid));
				$row2 = $stmt2->fetch();
				$num = $row2['num'];

				$stmt3 = $dbh->prepare("SELECT C.name, C.chid, C.num FROM chapter C WHERE C.num <= :num AND C.cid = :cid");
				$stmt3->execute(array(':num' => $num, ':cid' => $cid));

				while($row3 = $stmt3->fetch()){
					//show chapter name
					echo "<a href='mychapter.php?chid={$row3['chid']}'><h3>Chapter ".$row3['num']." ".$row3['name']." </h3></a>";

					//show quiz score
					$statement4 = $dbh->prepare("SELECT S.score FROM quiz_scores S WHERE S.uid = :uid AND S.chid = :chid");
					$statement4->execute(array(':uid' => $uid, ':chid' => $row3['chid']));
					if($row4 = $statement4->fetch()){
						//show chapter's quiz score
						echo "Quiz score: ".$row4['score']."<br>";
						$statement4->closeCursor();
					}

					//show hw score
					$statement7 = $dbh->prepare("SELECT S.score FROM hw_scores S natural join homework H WHERE S.uid = :uid AND H.chid = :chid");
					$statement7->execute(array(':uid' => $uid, ':chid' => $row3['chid']));
					if($row7 = $statement7->fetch()){
						if(empty($row7['score']))
							echo "Homework pendding.<br>";
						else echo "Homework score: ".$row7['score']."<br>";
						$statement7->closeCursor();
					}
					echo "<br>";
				}

				$stmt5 = $dbh->prepare("SELECT COUNT(*) AS count FROM chapter WHERE cid = :cid");
				$stmt5->execute(array(':cid' => $cid));
				$result = $stmt5->fetch();
				if($num == $result['count']) echo "Have finished this course! <br>";
				else echo "<button onclick=\"location.href='quit.php?cid=$cid'\">Quit Course</button>";

				$stmt5->closeCursor();
				$stmt3->closeCursor();
				$stmt2->closeCursor();
				echo "</fieldset><br>";
			}
			$stmt1->closeCursor();
		
		?>
	</body>
</html>