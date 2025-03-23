<?php
       
    include_once("../connect.php");
    header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    // include_once("../connect.php");  

    $username = $_REQUEST['username'];
    if(isset($_REQUEST['username'])){
        $checker = "SELECT * FROM member_registration WHERE reg_username = '$username' LIMIT 1";
        $checkerquery = mysqli_query($con, $checker);
        $checkerresult = mysqli_fetch_assoc($checkerquery);
        if($checkerresult){
            if($checkerresult['reg_username'] == $username){
                $php_errormsg[] = "true";
            } 
            
        } else if(strlen($username) < 10){
            $php_errormsg[] = "usernameerror";
        } else {
            $pageMsg = "false";
        }

    }

    $email = $_REQUEST['email'];
    if(isset($_REQUEST['email'])){
        $checker = "SELECT * FROM member_registration WHERE reg_email = '$email' LIMIT 1";
        $checkerquery = mysqli_query($con, $checker);
        $checkerresult = mysqli_fetch_assoc($checkerquery);
        if($checkerresult){
            if($checkerresult['reg_email'] == $email){
                $php_errormsg[] = "true";
            }
            
        } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $php_errormsg[] = "emailerror";
        } else {
            $pageMsg = "false";
        }

    }

    if(isset($_POST['passwordcf'])){
        $pw1 = $_POST['password'];
        $pw2 = $_POST['passwordcf'];
        if($pw2 != $pw1){
            $php_errormsg[] = "notsamepass";
        } else if($pw2 == $pw1){
            $pageMsg = "false";
        }
    }


    if(isset($pageMsg)){
        $status = $pageMsg;
        echo strval($status);
    } else if(isset($php_errormsg)){
        $status = $php_errormsg[0];
        echo strval($status);
    }
    


?>