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

    $quizid = $_GET['quizid'];

    error_reporting(E_ALL ^ E_DEPRECATED);
    $db = new PDO("mysql:host=localhost;dbname=fyp", "root");

    $statement = $db->prepare("SELECT chid FROM quiz WHERE quizid = :quizid");
    $statement->execute(array(':quizid' => $quizid));
    $result = $statement->fetch();
    $chid = $result['chid']; 
    $statement->closeCursor();

    $statement = $db->prepare("SELECT cid FROM chapter WHERE chid = :chid");
    $statement->execute(array(':chid' => $chid));
    $result = $statement->fetch();
    $cid = $result['cid']; 
    $statement->closeCursor();
?>

<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6 lt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7 lt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8 lt8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="UTF-8" />
        <!-- <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  -->
        <title>Course Home</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="shortcut icon" href="../favicon.ico"> 
        <link rel="stylesheet" type="text/css" href="css/demo.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <link rel="stylesheet" type="text/css" href="css/animate-custom.css" />
    </head>
<body>
<div class="container">
    <header>
        <h1>Quiz</h1>
        <form class="form" action="<?php echo htmlspecialchars("quiz.php?quizid=$quizid");?>" method="post">

            <?php
                $statement1 = $db->prepare("SELECT * FROM quiz Q WHERE Q.quizid = :quizid");
                $statement1->bindParam(':quizid', $quizid);
                $statement1->execute();
                while($row1 = $statement1->fetch()){
                    echo "<h2>1. ".$row1['question1']." </h2>";
                    echo "<input type='radio' name='answer1' value=1> A. ".$row1['choice1']."<br>";
                    echo "<input type='radio' name='answer1' value=2> B. ".$row1['choice2']."<br>";
                    echo "<input type='radio' name='answer1' value=3> C. ".$row1['choice3']."<br>";
                    echo "<input type='radio' name='answer1' value=4> D. ".$row1['choice4']."<br>";

                    echo "<h2>2. ".$row1['question2']." </h2>";
                    echo "<input type='radio' name='answer2' value=1> A. ".$row1['choice5']."<br>";
                    echo "<input type='radio' name='answer2' value=2> B. ".$row1['choice6']."<br>";
                    echo "<input type='radio' name='answer2' value=3> C. ".$row1['choice7']."<br>";
                    echo "<input type='radio' name='answer2' value=4> D. ".$row1['choice8']."<br>";

                    echo "<h2>3. ".$row1['question3']." </h2>";
                    echo "<input type='radio' name='answer3' value=1> A. ".$row1['choice9']."<br>";
                    echo "<input type='radio' name='answer3' value=2> B. ".$row1['choice10']."<br>";
                    echo "<input type='radio' name='answer3' value=3> C. ".$row1['choice11']."<br>";
                    echo "<input type='radio' name='answer3' value=4> D. ".$row1['choice12']."<br>";

                    echo "<h2>4. ".$row1['question4']." </h2>";
                    echo "<input type='radio' name='answer4' value=1> A. ".$row1['choice13']."<br>";
                    echo "<input type='radio' name='answer4' value=2> B. ".$row1['choice14']."<br>";
                    echo "<input type='radio' name='answer4' value=3> C. ".$row1['choice15']."<br>";
                    echo "<input type='radio' name='answer4' value=4> D. ".$row1['choice16']."<br>";

                    echo "<h2>5. ".$row1['question5']." </h2>";
                    echo "<input type='radio' name='answer5' value=1> A. ".$row1['choice17']."<br>";
                    echo "<input type='radio' name='answer5' value=2> B. ".$row1['choice18']."<br>";
                    echo "<input type='radio' name='answer5' value=3> C. ".$row1['choice19']."<br>";
                    echo "<input type='radio' name='answer5' value=4> D. ".$row1['choice20']."<br>";
                }
                $statement1->closeCursor();
            ?>
            <input id="submit" type="submit" name="Submit" value="Submit" size="50">
        </form>
    </div>
</body>
</html>

<?php
    if(isset($_POST['Submit'])) {
        //check if all questions are answered
        if((!isset($_POST['answer1'])) || (!isset($_POST['answer2'])) || (!isset($_POST['answer3'])) || (!isset($_POST['answer4'])) || (!isset($_POST['answer5']))){
            echo "<div align=center> <p style='color:red;'> Please answer all the qustions!<br></div>";
        }
        else{
            $answer1 = $_POST['answer1'];
            $answer2 = $_POST['answer2'];
            $answer3 = $_POST['answer3'];
            $answer4 = $_POST['answer4'];
            $answer5 = $_POST['answer5'];
            $score = 0;

            $statement2 = $db->prepare("SELECT * FROM quiz Q WHERE Q.quizid = :quizid");
            $statement2->bindParam(':quizid', $quizid);
            $statement2->execute();
            while($row2 = $statement2->fetch()){
                if($answer1 == $row2['answer1']) $score += 20;
                if($answer2 == $row2['answer2']) $score += 20;
                if($answer3 == $row2['answer3']) $score += 20;
                if($answer4 == $row2['answer4']) $score += 20;
                if($answer5 == $row2['answer5']) $score += 20;
            }

            echo "<div align=center> <p style='color:red;'>Your score is: ".$score."<br></div>";

            //check if have done this quiz before
            $done = 0;
            $score_old = 0;
            $statement3 = $db->prepare("SELECT score, COUNT(*) AS count FROM scores WHERE uid = :uid AND quizid = :quizid GROUP BY score");
            $statement3->execute(array(':uid' => $uid, ':quizid' => $quizid));
            if($result = $statement3->fetch()) {
                $done = $result['count']; 
                $score_old = $result['score'];
            }

            //store score into DB
            if($done == 0){//new
                $statement4 = $db->prepare("INSERT INTO scores VALUES(:uid, :chid, :quizid, :score)");
                $statement4->execute(array('uid' => $uid, ':chid' => $chid, ':quizid' => $quizid, ':score' => $score));
                $statement4->fetch();
                $statement4->closeCursor();
            }
            else{//done before
                if($score > $score_old){
                    $statement5 = $db->prepare("UPDATE scores SET score = :score WHERE uid = :uid AND quizid = :quizid");
                    $statement5->execute(array(':score' => $score, ':uid' => $uid, ':quizid' => $quizid));
                    $statement5->fetch();
                    $statement5->closeCursor();
                }
            }

            if($score > 60){
                echo "<div align=center> <p style='color:red;'>You have passed this chapter. Please continue to learn.<br></div>";
                echo "<div align=center> <a href=userprofile.php>Go back</a></div>";
                
                //pass and update learn table
                $statement6 = $db->prepare("INSERT INTO learn VALUES(:uid, :cid, :chid)");
                $statement6->execute(array('uid' => $uid, ':cid' =>$cid, ':chid' => $chid));
                $statement6->fecth();
                $statement6->closeCursor();
            }
            else{
                echo "<div align=center> <p style='color:red;'>You did not pass the quiz. Please review and redo.<br></div>";
            }
            
            $statement3->closeCursor();
            $statement2->closeCursor();
        }
    }
?>

