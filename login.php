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
 
//login
if(isset($_POST['Login'])){
		$username = format_input($_POST['username']);
		$pwd = format_input($_POST['password']);

		$sth = $db->prepare("SELECT * FROM user WHERE username = :username LIMIT 1");
		$sth->bindParam(':username', $username);
		$sth->execute();
		$user = $sth->fetch(PDO::FETCH_OBJ);

		// Hashing the password with its hash as the salt returns the same hash
		if ( hash_equals($user->password, crypt($pwd, $user->password)) ) {
			$token= md5(uniqid());
			$_SESSION['token'] = $token;
			$_SESSION['userid'] = $user->uid;
			$_SESSION['admin'] = $user->admin;
			setcookie('auth', $token, time()+3*24*60*60, NULL, NULL, NULL, 1);

			if($user->admin == 3) header("Location: adminpanel.php");
			else header("Location: usercourse.php");
		}
		else echo "<script type='text/javascript'>alert('Wrong username or password. Please retry!')</script>";
}

?>

<div class = "header">
<img src="images/logo.png" width=12% height=12% float:left>
</div>

<header class="header">
	<meta charset="UTF-8" />
    <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
    <title>Log in to Course Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    Welcome to e-learning system!</br>
</header>

<html>	
  <body>
	<div id="login">
	      <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<h2>Log in to Course Website</h2><hr>
			
			<label>Username :</label>
			<input name="username" type="text" id="username" required>
			
			<label>Password :</label>
			<input name="password" type="password" required><br>
			
			<input id="register" type="submit" name="Login" value="Login">
            <br/>
            <br/>
            <h3>Do not have an account?</h3>
            <a href="register.php">Register here!</a>
		  </form>
   </div>

  </body>
</html>

<footer id="footer"> 
        <hr>
        IEEE Acamedic E-learning System<br>
        The Chinese University of Hong Kong<br>
</footer>