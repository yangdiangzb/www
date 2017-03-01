<?php
error_reporting(0);
$dsn = 'mysql:dbname=fyp;host=127.0.0.1';
$user = 'root';
$password = '';
try {
	$dbh = new PDO($dsn,$user,$password);
} catch (PDOException $e) {
	printf("DatabaseError: %s", $e->getMessage());
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
    <title>Search</title>
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
    <nav class="navigation">
            <a href="index.php" >Home page</a>
            <a href="allcourse.php" >All courses</a>
    </nav>
	<h1>Search Result</h1></br>
    <?php
    	$search=$_POST['tt'];
    	$stmt=$dbh->prepare("SELECT cid,name FROM course WHERE cid = ? OR name LIKE ?;");
    	if($stmt->execute(array($search,"%".$search."%"))){
    		while($row = $stmt->fetch()){
    			echo "<a href=course.php?cid=".$row['cid']."><h2>".$row['name']."</h2></a>";
    		}
    	}
    ?>   
 </div>
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