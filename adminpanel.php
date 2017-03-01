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
if(isset($_POST['changepassword'])){
	$stmt = $dbh->prepare('SELECT password from user where uid = :uid ');
	$stmt->bindParam(':uid', $_SESSION["userid"]);
	$stmt->execute();
	$row = $stmt->fetch();
	$password = $row["password"];
	unset($stmt);
	if ( hash_equals($password, crypt($_POST["currentPassword"], $password)) ) {
		if(empty($_POST["newPassword"]))
		{
			$error='The new password is not valid!';
		}
		else{
			$stmt = $dbh->prepare('update user set password = :password where uid = :uid ');
			$salt = sprintf("$2a$%02d$", 10).
			strtr(base64_encode(mcrypt_create_iv(16)), '+', '.');
			$hash = crypt($_POST["newPassword"], $salt);
			$stmt->bindParam(':password', $hash);
			$stmt->bindParam(':uid', $_SESSION["userid"]);
			$stmt->execute();
			unset($_COOKIE['auth']);
			setcookie('auth', null, -1);
			session_unset();
			session_destroy();
			header("Location: login.php");	
		}	
	}
	else{
		$error = "Wrong password!";
	}
}
if(isset($_POST['addcourse'])){
	if(!empty($_FILES["file"]["tmp_name"])){
		if ((($_FILES["file"]["type"] == "image/gif")||($_FILES["file"]["type"] == "image/jpeg")||($_FILES["file"]["type"] =="image/png"))&&($_FILES["file"]["size"] < 1000000)){
			if ($_FILES["file"]["error"] > 0){
				$error = "Error: " . $_FILES["file"]["error"] . "<br />";
			}
			else{
				$name = test_input($_POST['name']);
				$description = test_input($_POST['description']);
				if(empty($name))
				{
					$error='Your name is not valid!';
				}
				else if(empty($description))
				{
					$error='Your description is not valid!';
				}
				else{
					$stmt = $dbh->prepare('INSERT INTO course (name, description) VALUES (:name, :description)');
					$stmt->bindParam(':name', $_POST['name']);
					$stmt->bindParam(':description', $_POST['description']);
					$stmt->execute();
					unset($stmt);
					$stmt = $dbh->prepare("SELECT MAX(cid) AS m FROM course");
					$stmt->execute();
					while ($row = $stmt->fetch()) {
						$max = $row["m"];
					}
					unset($stmt);
					move_uploaded_file($_FILES["file"]["tmp_name"],"images/".$max);
					$error = "course added!";
				}
			}
		}
		else{
			$error = "Invalid file";
		}	
	}
	else{
		$error = "please upload the image.";
	}
}
if(isset($_POST['editcourse'])){
	if(!empty($_FILES["file"]["tmp_name"])){
		if ((($_FILES["file"]["type"] == "image/gif")||($_FILES["file"]["type"] == "image/jpeg")||($_FILES["file"]["type"] =="image/png"))&&($_FILES["file"]["size"] < 1000000)){
			if ($_FILES["file"]["error"] > 0){
				$error= "Error: " . $_FILES["file"]["error"] . "<br />";
			}
			else{
				move_uploaded_file($_FILES["file"]["tmp_name"],"images/".$_POST["cid"]);
			}
		}
		else{
			$error= "Invalid file";
		}	
	}
	$name = test_input($_POST['name']);
	if(empty($name))
	{
		$error='Your name is not valid!';
	}
	else if(!empty($_POST['description'])){
		$stmt = $dbh->prepare('UPDATE course SET name = :name, description = :description WHERE cid = :cid');
		$stmt->bindParam(':cid', $_POST['cid']);
		$stmt->bindParam(':name', $_POST['name']);
		$stmt->bindParam(':description', $_POST['description']);
		$stmt->execute();
		unset($stmt);
	}
	else{
		$stmt = $dbh->prepare('UPDATE course SET name = :name WHERE cid = :cid');
		$stmt->bindParam(':cid', $_POST['cid']);
		$stmt->bindParam(':name', $_POST['name']);
		$stmt->execute();
		unset($stmt);
	}
	$error = "Course edited!";
}
if(isset($_POST['removecourse'])){
	$stmt = $dbh->prepare('DELETE FROM course WHERE cid = :cid');
	$stmt->bindParam(':cid', $_POST['cid']);
	$stmt->execute();
	unset($stmt);
	$error = "course removed!";
}
if(isset($_POST['selectcourse'])){
	$_SESSION['cid'] = $_POST['cid'];
	header("Location: admincourse.php");
}
if(isset($_POST['selectstudent'])){
	$_SESSION['sid'] = $_POST['sid'];
	header("Location: adminstudent.php");
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
		<fieldset>
			<legend>Change password</legend>
			<form method="post">
				<label>Current Password</label>
				<input type="password" name="currentPassword"/><br>
				<label>New Password</label>
				<input type="password" name="newPassword"/><br>
				<button type="submit" name="changepassword">Change</button>
			</form>
		</fieldset><br>
		<fieldset>
			<legend>New course</legend>
			<form enctype="multipart/form-data" method="POST">
				<label>Name * </label>
				<input type="text" name="name"/><br>
				Description *<br>
				<textarea name="description"></textarea><br>
				Image *<br>
				<input type="file" name="file" /><br>
				<button type="submit" name="addcourse">Add</button>
			</form>
		</fieldset><br>
		<fieldset>
			<legend>Edit a course</legend>
			<form enctype="multipart/form-data" method="POST">
				<label>Course * </label> 
				<select name="cid">
				<?php 
				$stmt = $dbh->prepare('SELECT cid, name FROM course');
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["cid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select></br>
				<label>Name * </label><input type="text" name="name"/></br>
				Description <br>
				<textarea name="description"></textarea><br>
				Image <br>
				<input type="file" name="file" /><br>
				<button type="submit" name="editcourse">Update</button>
			</form>
		</fieldset></br>
		<fieldset>
			<legend>Remove a course</legend>
			<form method="POST">
				<label>Course * </label> 
				<select name="cid">
				<?php 
				$stmt = $dbh->prepare('SELECT cid, name FROM course');
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["cid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select>
				<button type="submit" name="removecourse">Remove</button>
			</form>
		</fieldset></br>
        <fieldset>
			<legend>Edit Course Content</legend>
			<form method="POST">
				<label>Course * </label></br>
				<select name="cid">
				<?php 
				$stmt = $dbh->prepare('SELECT cid, name FROM course');
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["cid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select></br>
				<button type="submit" name="selectcourse">Select</button>
			</form>
		</fieldset><br>
		<fieldset>
			<legend>Students</legend>
			<form method="POST">
				<select name="sid">
				<?php 
				$stmt = $dbh->prepare('SELECT uid, name FROM user WHERE admin = 1');
				$stmt->execute();
				while ($row = $stmt->fetch()) {
					echo '<option value="'.$row["uid"].'">'.$row["name"].'</option>';
				}
				unset($stmt);
				?>
				</select></br>
				<button type="submit" name="selectstudent">Select</button>
			</form>
		</fieldset></br>
	</body>
</html>