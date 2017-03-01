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
csrfguard_start();
if(isset($_POST['addchapter'])){
	$name = test_input($_POST['name']);
	$num = test_input($_POST['num']);
	if(empty($name))
	{
		$error='Your name is not valid!';
	}
	else{
		$stmt = $dbh->prepare('INSERT INTO chapter (cid, name, num, url) VALUES (:cid, :name, :num, :url)');
		$stmt->bindParam(':name', $name);
		$stmt->bindParam(':cid', $_SESSION["cid"]);
		$stmt->bindParam(':url', $_POST['u']);
		$stmt->bindParam(':num', $num);
		$stmt->execute();
		unset($stmt);
		$error = "Chapter added!";
	}
}
if(isset($_POST['editchapter'])){
	if(empty($_POST['name']))
	{
		$error='Your name is not valid!';
	}
	else{
		$stmt = $dbh->prepare('UPDATE chapter SET name = :name, num = :num, url = :url WHERE chid = :chid');
		$stmt->bindParam(':name', $_POST['name']);
		$stmt->bindParam(':chid', $_POST['chid']);
		$stmt->bindParam(':url', $_POST['u']);
		$stmt->bindParam(':num', $_POST['num']);
		$stmt->execute();
		unset($stmt);
		$error = "Chapter edited!";
	}	
}
if(isset($_POST['removechapter'])){
	$stmt = $dbh->prepare('DELETE FROM chapter WHERE chid = :chid');
	$stmt->bindParam(':chid', $_POST['chid']);
	$stmt->execute();
	unset($stmt);
	$error = "Chapter removed!";
}
if(isset($_POST['addquiz'])){
	$stmt = $dbh->prepare('INSERT INTO quiz (chid, question1,question2,question3,question4,question5,choice1,choice2,choice3,choice4,choice5,choice6,choice7,choice8,choice9,choice10,choice11,choice12,choice13,choice14,choice15,choice16,choice17,choice18,choice19,choice20,answer1,answer2,answer3,answer4,answer5) values (:chid, :question1,:question2,:question3,:question4,:question5,:choice1,:choice2,:choice3,:choice4,:choice5,:choice6,:choice7,:choice8,:choice9,:choice10,:choice11,:choice12,:choice13,:choice14,:choice15,:choice16,:choice17,:choice18,:choice19,:choice20,:answer1,:answer2,:answer3,:answer4,:answer5)');
	$stmt->bindParam(':chid', $_POST['chid']);
	for($x =1;$x<=20;$x++){
		$stmt->bindParam(':choice'.$x, $_POST['c'.$x]);
	}
	for($x =1;$x<=5;$x++){
		$stmt->bindParam(':question'.$x, $_POST['question'.$x]);
		$stmt->bindParam(':answer'.$x, $_POST['a'.$x]);
	}
	$stmt->execute();
	unset($stmt);
	$error = "Quiz added!";
}
if(isset($_POST['removequiz'])){
	$stmt = $dbh->prepare('DELETE FROM quiz WHERE chid = :chid');
	$stmt->bindParam(':chid', $_POST['chid']);
	$stmt->execute();
	unset($stmt);
	$error = "Quiz removed!";
}
if(isset($_POST['addhomework'])){
	if(empty($_POST['u']))
	{
		$error='Your url is not valid!';
	}
	else{
		$stmt = $dbh->prepare('INSERT INTO homework (chid, url) VALUES (:chid, :url)');
		$stmt->bindParam(':chid', $_POST['chid']);
		$stmt->bindParam(':url', $_POST['u']);
		$stmt->execute();
		unset($stmt);
		$error = "Homework added!";
	}
}
if(isset($_POST['removehomework'])){
	$stmt = $dbh->prepare('DELETE FROM homework WHERE chid = :chid');
	$stmt->bindParam(':chid', $_POST['chid']);
	$stmt->execute();
	unset($stmt);
	$error = "Homework removed!";
}
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
			$stmt = $dbh->prepare('SELECT name FROM course WHERE cid = :cid');
			$stmt->bindParam(':cid', $_SESSION["cid"]);
			$stmt->execute();
			$row = $stmt->fetch();
			echo '<h2>'.$row['name'].'</h2>';
			unset($stmt);
		?>
		<a href="adminpanel.php"><h3>Return to adminpanel</h3></a>
		<fieldset>
			<legend>New Chapter</legend>
			<form method="POST">
				<label>Name * </label></br>
				<input type="text" name="name"/></br>
				<label>Number *</label><br>
				<input type="number" name="num" required pattern="[0-9]+"/></br>
				Url *<br>
				<textarea name="u"></textarea><br>
				<button type="submit" name="addchapter">Add</button>
			</form>
		</fieldset></br>
        <fieldset>
			<legend>Edit a Chapter</legend>
			<form method="POST">
				<label>Chapter * </label></br>
				<select name="chid">
				<?php 
				$stmt = $dbh->prepare('SELECT chid, name FROM chapter WHERE cid = :cid');
				$stmt->bindParam(':cid', $_SESSION["cid"]);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["chid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select></br>			
				<label>Name * </label></br>
				<input type="text" name="name" required /></br>
				<label>Number *</label><br>
				<input type="number" name="num" required pattern="[0-9]+"/></br>
				Url *<br>
				<textarea name="u"></textarea><br>
				<button type="submit" name="editchapter">Update</button>
			</form>
		</fieldset></br>
        <fieldset>
			<legend>Remove a Chapter</legend>
			<form method="POST">
				<label>Chapter * </label> 
				<select name="chid">
				<?php 
				$stmt = $dbh->prepare('SELECT chid, name FROM chapter WHERE cid = :cid');
				$stmt->bindParam(':cid', $_SESSION["cid"]);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["chid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select>
				<button type="submit" name="removechapter">Remove</button>
			</form>
		</fieldset></br>
		<fieldset>
			<legend>New Quiz</legend>
			<form method="POST">
				<label>Chapter * </label> 
				<select name="chid">
				<?php 
				$stmt = $dbh->prepare('SELECT chid, name FROM chapter WHERE cid = :cid');
				$stmt->bindParam(':cid', $_SESSION["cid"]);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["chid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select><br>
				<label>Question1 * </label><br>
				<textarea name="question1"></textarea><br>
				choice A <input type="text" name="c1" /><br>
				choice B <input type="text" name="c2" /><br>
				choice C <input type="text" name="c3" /><br>
				choice D <input type="text" name="c4" /><br>
				answer <select name="a1">
				<option value="1">A</option>
				<option value="2">B</option>
				<option value="3">C</option>
				<option value="4">D</option>
				</select><br>
				<label>Question2 * </label><br>
				<textarea name="question2"></textarea><br>
				choice A <input type="text" name="c5" /><br>
				choice B <input type="text" name="c6" /><br>
				choice C <input type="text" name="c7" /><br>
				choice D <input type="text" name="c8" /><br>
				answer <select name="a2">
				<option value="1">A</option>
				<option value="2">B</option>
				<option value="3">C</option>
				<option value="4">D</option>
				</select><br>
				<label>Question3 * </label><br>
				<textarea name="question3"></textarea><br>
				choice A <input type="text" name="c9" /><br>
				choice B <input type="text" name="c10" /><br>
				choice C <input type="text" name="c11" /><br>
				choice D <input type="text" name="c12" /><br>
				answer <select name="a3">
				<option value="1">A</option>
				<option value="2">B</option>
				<option value="3">C</option>
				<option value="4">D</option>
				</select><br>
				<label>Question4 * </label><br>
				<textarea name="question4"></textarea><br>
				choice A <input type="text" name="c13" /><br>
				choice B <input type="text" name="c14" /><br>
				choice C <input type="text" name="c15" /><br>
				choice D <input type="text" name="c16" /><br>
				answer <select name="a4">
				<option value="1">A</option>
				<option value="2">B</option>
				<option value="3">C</option>
				<option value="4">D</option>
				</select><br>
				<label>Question5 * </label><br>
				<textarea name="question5"></textarea><br>
				choice A <input type="text" name="c17" /><br>
				choice B <input type="text" name="c18" /><br>
				choice C <input type="text" name="c19" /><br>
				choice D <input type="text" name="c20" /><br>
				answer <select name="a5">
				<option value="1">A</option>
				<option value="2">B</option>
				<option value="3">C</option>
				<option value="4">D</option>
				</select><br>
				<button type="submit" name="addquiz">Add</button>
			</form>
		</fieldset><br>
		<fieldset>
			<legend>Remove a Quiz</legend>
			<form method="POST">
				<label>Chapter * </label> 
				<select name="chid">
				<?php 
				$stmt = $dbh->prepare('SELECT Q.chid, name FROM quiz Q, chapter C WHERE Q.chid = C.chid AND cid = :cid');
				$stmt->bindParam(':cid', $_SESSION["cid"]);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["chid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select>
				<button type="submit" name="removequiz">Remove</button>
			</form>
		</fieldset><br>	
		<fieldset>
			<legend>New Homework</legend>
			<form method="POST">
				<label>Chapter * </label> 
				<select name="chid">
				<?php 
				$stmt = $dbh->prepare('SELECT chid, name FROM chapter WHERE cid = :cid');
				$stmt->bindParam(':cid', $_SESSION["cid"]);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["chid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select><br>
				Url *<br>
				<textarea name="u"></textarea><br>
				<button type="submit" name="addhomework">Add</button>
			</form>
		</fieldset></br>
		<fieldset>
			<legend>Remove a Homework</legend>
			<form method="POST">
				<label>Chapter * </label> 
				<select name="chid">
				<?php 
				$stmt = $dbh->prepare('SELECT h.chid, name FROM homework H, chapter C WHERE H.chid = C.chid AND cid = :cid');
				$stmt->bindParam(':cid', $_SESSION["cid"]);
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["chid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select>
				<button type="submit" name="removequiz">Remove</button>
			</form>
		</fieldset><br>	
	</body>
</html>