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
	
	function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
	}
	if(isset($_POST['submit_comment'])){
		$comment=test_input($_POST['comment']);
		if(!empty($comment)){
			$stmt6=$dbh->prepare("insert into question values(null,?,?,?,0);");
				if($stmt6->execute(array($_GET['chid'],$uid,$comment)))
				{
					echo "<script> alert('Comment submitted'); </script>";
				}
		}
	}
	if(isset($_POST['submit_answer'])){
		$answer=test_input($_POST['answer']);
		if(!empty($answer)){
			$stmt8=$dbh->prepare("insert into answer values(null,?,?,?);");
				if($stmt8->execute(array($_POST['qid'],$uid,$answer)))
				{
					echo "<script> alert('Reply submitted'); </script>";
				}
		}
	}
	if(isset($_POST['submit_hw'])&&isset($_FILES['hw'])){
		
	    $hid=$_POST['hid'];
		
		$target_dir ="homework/";
		$target_file = $target_dir . basename($_FILES['hw']['name']);
		$fileType = pathinfo($target_file,PATHINFO_EXTENSION);
		$stmt11=$dbh->prepare("select * from `hw_scores` where `hid`=? and `uid`=?");
		if($stmt11->execute(array($hid,$uid))){
			if($row11=$stmt11->fetch()){
				
				 $target_name = $target_dir.$hid."_".$uname.".".$fileType;
				 
				 if (file_exists($target_name)) {
					  if(unlink($target_name)){
    					if (move_uploaded_file($_FILES['hw']['tmp_name'], $target_name)) {
						echo "<script> alert('Homework resubmission success'); </script>";
    					} else {
						echo "<script> alert('Homework resubmission failed'); </script>";
    					}
					  }
				}
				else {
					if (move_uploaded_file($_FILES['hw']['tmp_name'], $target_name)) {
						echo "<script> alert('Homework submission success'); </script>";
    					} else {
						echo "<script> alert('Homework submission failed'); </script>";
    					}
				}
			}
			else {
				$target_name = $target_dir .$hid."_".$uname.".".$fileType;
				$stmt12=$dbh->prepare("insert into `hw_scores` values(?,?,NULL)");
				if($stmt12->execute(array($hid,$uid))){
					if (move_uploaded_file($_FILES['hw']['tmp_name'], $target_name)) {
					echo "<script> alert('Homework submission success'); </script>";
    				} else {
					echo "<script> alert('Homework submission failed'); </script>";
    				}
				}
			}
		}
		
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
    <title>My chapter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <meta name="keywords" content="FYP,WIRELESS COMMUNICATION,CUHK,IEEE">
    <!-- include css file here-->
    <link rel="stylesheet" type="text/css" href="css/demo.css" />
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
      <script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/login.js"></script>
    <?php 
    	$cur_chid = $_GET['chid'];
		$stmt=$dbh->prepare("SELECT * from chapter where chid=?");
		if($stmt->execute(array($cur_chid))){
			$row=$stmt->fetch();
		}
		echo "Course".$row['cid']."  Chapter".$row['num'];
	?>
</header>
   
<body>
<nav class="navigation">
        <?php
        echo "<a href=allcourse.php>All Courses</a>";
		echo "<a href=usercourse.php>My Courses</a>";
		echo "<a href=userprofile.php>My profile</a>";
		echo "</nav>";
		?>
	<nav class="category" id="nav_bar">Navigation
		<ul>
		<li><a href="#video">Video</a>
		<li><a href="#quiz">Quiz</a>
		<?php
			$stmt1000=$dbh->prepare("select * from homework where chid=?");
			if($stmt1000->execute(array($cur_chid))){
				if($stmt1000->fetch()){
					echo "<li><a href=\"#homework\">Homework</a>";
				}
			}
		?>
		<li><a href="#forum">Q&ampA forum</a>
	</nav>

	<div id="courses"><h1>Video</h1>
		<section class="eachcourse" id="video"><ul><li>
			<?php
			$cur_num=$row['num'];
	        $cur_cid=$row['cid'];
			//
			$stmt2=$dbh->prepare("select * from learn where uid = ? and cid = ?");
			if($stmt2->execute(array($uid,$cur_cid))){
				$row2=$stmt2->fetch();
			}
			$std_chid = $row2['chid'];
			//
			$stmt3=$dbh->prepare("SELECT * from chapter where chid=?");
			if($stmt3->execute(array($std_chid))){
				$row3=$stmt3->fetch();
			}
			$std_num=$row3['num'];
			if(($std_num + 1) >= $cur_num){
				//show video
				$stmt4=$dbh->prepare("SELECT * FROM chapter WHERE chid = ?");
				if($stmt4->execute(array($cur_chid))){
					$row4=$stmt4->fetch();
				}
				echo "<br>";
	  			echo "<video contorls controls=controls width=650>";
	    		echo "<source src=".$row4['url']." type=video/mp4>";
	    		echo "Your browser does not support HTML5 video.";
	  			echo "</video>";
				echo "<br><a style=\"text-decoration:underline\" href=".$row4['url']." download>Click to download this video</a>";
			?>
		</section>

		<h1 id="quiz">Quiz</h1>
		<section class="eachcourse" ><ul><li>
		<?php	
			//quiz
			$stmt9=$dbh->prepare("SELECT * FROM quiz WHERE chid = ?");
			if($stmt9->execute(array($cur_chid))){
				$row9=$stmt9->fetch();
			}
			$quizid=$row9['quizid'];
			?>
            <form method="post" style="background-color:white">
            <?php
                $statement1 = $dbh->prepare("SELECT * FROM quiz Q WHERE Q.quizid = :quizid");
                $statement1->bindParam(':quizid', $quizid);
                $statement1->execute();
                while($row1 = $statement1->fetch()){
                    echo "<h3>1. ".$row1['question1']." </h3>";
                    echo "<input type='radio' name='answer1' value=1 style=\"width:15px;background-color:white\"> A. ".$row1['choice1']."<br>";
                    echo "<input type='radio' name='answer1' value=2 style=\"width:15px;background-color:white\"> B. ".$row1['choice2']."<br>";
                    echo "<input type='radio' name='answer1' value=3 style=\"width:15px;background-color:white\"> C. ".$row1['choice3']."<br>";
                    echo "<input type='radio' name='answer1' value=4 style=\"width:15px;background-color:white\"> D. ".$row1['choice4']."<br>";

                    echo "<h3>2. ".$row1['question2']." </h3>";
                    echo "<input type='radio' name='answer2' value=1 style=\"width:15px;background-color:white\"> A. ".$row1['choice5']."<br>";
                    echo "<input type='radio' name='answer2' value=2 style=\"width:15px;background-color:white\"> B. ".$row1['choice6']."<br>";
                    echo "<input type='radio' name='answer2' value=3 style=\"width:15px;background-color:white\"> C. ".$row1['choice7']."<br>";
                    echo "<input type='radio' name='answer2' value=4 style=\"width:15px;background-color:white\"> D. ".$row1['choice8']."<br>";

                    echo "<h3>3. ".$row1['question3']." </h3>";
                    echo "<input type='radio' name='answer3' value=1 style=\"width:15px;background-color:white\"> A. ".$row1['choice9']."<br>";
                    echo "<input type='radio' name='answer3' value=2 style=\"width:15px;background-color:white\"> B. ".$row1['choice10']."<br>";
                    echo "<input type='radio' name='answer3' value=3 style=\"width:15px;background-color:white\"> C. ".$row1['choice11']."<br>";
                    echo "<input type='radio' name='answer3' value=4 style=\"width:15px;background-color:white\"> D. ".$row1['choice12']."<br>";

                    echo "<h3>4. ".$row1['question4']." </h3>";
                    echo "<input type='radio' name='answer4' value=1 style=\"width:15px;background-color:white\"> A. ".$row1['choice13']."<br>";
                    echo "<input type='radio' name='answer4' value=2 style=\"width:15px;background-color:white\"> B. ".$row1['choice14']."<br>";
                    echo "<input type='radio' name='answer4' value=3 style=\"width:15px;background-color:white\"> C. ".$row1['choice15']."<br>";
                    echo "<input type='radio' name='answer4' value=4 style=\"width:15px;background-color:white\"> D. ".$row1['choice16']."<br>";

                    echo "<h3>5. ".$row1['question5']." </h3>";
                    echo "<input type='radio' name='answer5' value=1 style=\"width:15px;background-color:white\"> A. ".$row1['choice17']."<br>";
                    echo "<input type='radio' name='answer5' value=2 style=\"width:15px;background-color:white\"> B. ".$row1['choice18']."<br>";
                    echo "<input type='radio' name='answer5' value=3 style=\"width:15px;background-color:white\"> C. ".$row1['choice19']."<br>";
                    echo "<input type='radio' name='answer5' value=4 style=\"width:15px;background-color:white\"> D. ".$row1['choice20']."<br>";
                }
                $statement1->closeCursor();
            ?>
            <br><br>
            <input id="submit" type="submit" name="Submit" value="Submit" style="width:660px">
        </form>
    </section>

            <?php
			//Homework
			$stmt10=$dbh->prepare("select * from homework where chid=?");
			if($stmt10->execute(array($cur_chid))){
				if($row10=$stmt10->fetch()){
					$hwnum=1;
					echo "<h1 id=\"homework\">Homework</h1>";
					echo "<section class=\"eachcourse\"><ul><li>";
					echo "<a href=".$row10['url']." download style=\"text-decoration:underline\">Click to download homework ".$hwnum."</a>";
					echo "<br><br>";
					echo "<h3>Submit your homework</h3>";
					echo "<form method=post enctype=multipart/form-data style=\"background-color:white\">";
					echo "<label style=\"background-color:white\">Homework *</label>";
					echo "<input required type='file' name='hw' style=\"background-color:white\" />";
					echo "<input type='hidden' name='hid' value=".$row10['hid']." />";
					echo "<input id='submit' type=submit name='submit_hw' value='Submit' style=\"width:660px\" />";
					echo "</form>";
				}
			}
			?>
			</section>
			
			<h1 id="forum">Q&ampA Forum</h1>
			<section class="eachcourse"><ul><li>
			<?php
			//q&A
			$stmt5=$dbh->prepare("select * from question where chid=?");
			
			if($stmt5->execute(array($cur_chid))){
				$quenum=1;
				while($row5=$stmt5->fetch()){
					
					echo "<h2 style=\"text-transform:none\">Question ".$quenum.": ".$row5['content']."</h2>";
					//echo "<button class='fold_reply'>show/unshow reply</button>";
					//echo "<div class='reply'>";
					echo "<div class=entry style=\"background-color:white\">";
                	echo "<a href=#expand>Show replies</a>";
                	echo "<div class=description>";
            
					$stmt7=$dbh->prepare("select * from answer where qid=?");
					if($stmt7->execute(array($row5['qid']))){
						$ansnum=1;
						while($row7=$stmt7->fetch()){
							echo "<h3>Answer ".$ansnum.": ".$row7['content']."</h3>";
							
							$ansnum++;
						}
					}
					
					$qid=$row5['qid'];
					echo "<form method='post' style=\"background-color:white\">";
					echo "<textarea name='answer' id='comment' style=\"background-color:white; width:660px; height: 70px; text-transform:none\"></textarea>";
					echo "<input type='hidden' name='qid' value=$qid>";
					echo "<br><br><input type='submit' name='submit_answer' value='Reply' style=\"width:660px\"><br>";
					echo "</form>";
					echo "</div>";
					echo "</div>";
					$quenum++;
					echo "<br><hr>";
				}
			}
			?>
            <br>
            <form method='post' style="background-color:white">
  				<h3>Ask questions:</h3>
                <textarea name='comment' id='comment' style="background-color:white; width:660px; height: 70px; text-transform:none"></textarea><br>
                <br><input type='submit' name = 'submit_comment' value='Submit' style="width:660px"/>  
             </form>

            <?php
			
			
		}else{
			//show nothing
			echo "You have to finish previous chapter";
		}
		echo "</div>";
		?>
		<?php
    if(isset($_POST['Submit'])) {
        //check if all questions are answered
        if((!isset($_POST['answer1'])) || (!isset($_POST['answer2'])) || (!isset($_POST['answer3'])) || (!isset($_POST['answer4'])) || (!isset($_POST['answer5']))){
			echo "<script> alert('Please answer all the qustions!'); </script>";
             
        }
        else{
            $answer1 = $_POST['answer1'];
            $answer2 = $_POST['answer2'];
            $answer3 = $_POST['answer3'];
            $answer4 = $_POST['answer4'];
            $answer5 = $_POST['answer5'];
            $score = 0;

            $statement2 = $dbh->prepare("SELECT * FROM quiz Q WHERE Q.quizid = :quizid");
            $statement2->bindParam(':quizid', $quizid);
            $statement2->execute();
            while($row2 = $statement2->fetch()){
                if($answer1 == $row2['answer1']) $score += 20;
                if($answer2 == $row2['answer2']) $score += 20;
                if($answer3 == $row2['answer3']) $score += 20;
                if($answer4 == $row2['answer4']) $score += 20;
                if($answer5 == $row2['answer5']) $score += 20;
            }

            //check if have done this quiz before
            $done = 0;
            $score_old = 0;
			$statement3 = $dbh->prepare("SELECT score, COUNT(*) AS count FROM quiz_scores WHERE uid = :uid AND quizid = :quizid GROUP BY score");
            $statement3->execute(array(':uid' => $uid, ':quizid' => $quizid));
            if($result = $statement3->fetch()) {
                $done = $result['count']; 
                $score_old = $result['score'];
            }
			//store score into DB
            if($done == 0){//new
                $statement4 = $dbh->prepare("INSERT INTO quiz_scores VALUES(:uid, :chid, :quizid, :score)");
                $statement4->execute(array('uid' => $uid, ':chid' => $cur_chid, ':quizid' => $quizid, ':score' => $score));
                $statement4->fetch();
                $statement4->closeCursor();
            }
            else{//done before
                if($score > $score_old){
                    $statement5 = $dbh->prepare("UPDATE quiz_scores SET score = :score WHERE uid = :uid AND quizid = :quizid");
                    $statement5->execute(array(':score' => $score, ':uid' => $uid, ':quizid' => $quizid));
                    $statement5->fetch();
                    $statement5->closeCursor();
                }
            }

            if($score > 60){
                echo "<script> alert('Your score is:".$score.". You have passed this chapter. Please continue to learn.'); window.location.href='userprofile.php';</script>";
                
                //pass and update learn table
                $statement6 = $dbh->prepare("UPDATE learn SET chid=:chid WHERE uid=:uid AND cid=:cid");
                $statement6->execute(array('uid' => $uid, ':cid' =>$cur_cid, ':chid' => $cur_chid));
                $statement6->fecth();
                $statement6->closeCursor();
            }
            else{
                echo "<script> alert('Your score is:".$score.". You did not pass the quiz. Please review and redo.');</script>";
            }
            
            $statement3->closeCursor();
            $statement2->closeCursor();
        }
    }
?>


 
</body>
<script type="text/javascript">
	$(document).ready(function(){
    	$(".entry a").click(function() {
       		$(this).parents('.entry').find('.description').slideToggle(1000);
        return false;
    });
});
</script>

</html>
<footer id="footer"> 
        <hr>
        IEEE Acamedic E-learning System<br>
        The Chinese University of Hong Kong<br>
</footer>

<?php
$dbh=null;
?>