<?php

session_start();

//connect to DB
//error_reporting(E_ALL ^ E_DEPRECATED);
$db = new PDO("mysql:host=localhost; dbname=fyp", "root", "");

function format_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

//register
if(isset($_POST['Register'])){
		$success = 1;

		$name = format_input($_POST['name']);
		$username = format_input($_POST['username']);
		$pwd = format_input($_POST['password']);
		$salt = sprintf("$2a$%02d$", 10).strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.'); 
		$hashed_password = crypt($pwd, $salt);

		//check if user already exists
		$exist_user = $db->prepare("SELECT * FROM user WHERE username = :username");
		$exist_user->execute(array(':username'=>$username));
		if($exist_user->fetchColumn()) {
			echo "<script type='text/javascript'>alert('This username already exists!')</script>";
			$success = 0;
		}

		if ($success)
		{
			$statement = $db->prepare("INSERT INTO user(name, username, password, admin) VALUES (:name, :username, :password, 1)");
			$statement->execute(array(':name' => $name, ':username' => $username, ':password' => $hashed_password));

			// Hashing the password with its hash as the salt returns the same hash
			if ($statement) {
				echo ("<script type='text/javascript'>alert('Registered successfully! Please login.')</script>");
				header("Location: login.php");
			}
			else echo "<script type='text/javascript'>alert('Please retry!')</script>";
		}
}
 
//connection closed
//mysql_close ($connection);
?>

<div class = "header">
<img src="images/logo.png" width=12% height=12% float:left>
</div>
<html>
<header class="header">
	<meta charset="UTF-8" />
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
    <title>Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    Welcome to e-learning system!</br>
</header>

<body>
	<div id="login">
	      <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<h2>Registeration form</h2><hr/>
			<label>Name: </label>
			<input name="name" type="text" id="name" pattern="\w+$" title="Letters and spaces only" required>
			
			<label>Username: </label>
			<input name="username" type="text" id="username" pattern="[a-zA-Z0-9]+$" title="Letters and numbers only" required>
			
			<label>Password: </label>
			<input name="password" type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required><br>
			
			<label>Confirm Password: </label>
			<input name="password" type="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required><br>
			
			<input type="submit" name="Register" id="register" value="Register">
            <br/>
            <br/>
            <h3> Already have an account?</h3>
            <a href="login.php">Log in here!</a>
		  </form>
   </div>

  </body>
</html>

<footer id="footer"> 
        <hr>
        IEEE Acamedic E-learning System<br>
        The Chinese University of Hong Kong<br>
</footer>