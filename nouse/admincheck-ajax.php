<?php

    include_once("../connect.php");
    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/json; charset=UTF-8");
    // header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization");
    
    

    $username = mysqli_real_escape_string($con, $_REQUEST['username_lg']);
    $password = mysqli_real_escape_string($con, $_REQUEST['password_lg']);
    
    if(empty($username)){
        $php_errormsg[] = "กรุณาป้อนชื่อผู้ใช้ของคุณ";
    } else if(empty($password)){
        $php_errormsg[] = "กรุณาป้อนรหัสผ่านของคุณ";
    } else {
        try {
            
            $user_query = "SELECT * FROM member_registration WHERE reg_username = '$username' LIMIT 1";
            $query = mysqli_query($con, $user_query);
            $result = mysqli_fetch_assoc($query);
            $passwordhash = md5($password);
            $password_db = $result['reg_password'];
            if(!$php_errormsg){
                if($username == $result['reg_username']){
                    if($passwordhash == $password_db){
                        $loginMsg = "กำลังเข้าสู่ระบบ";
                    } else{
                        $php_errormsg[] = "รหัสผ่านไม่ถูกต้อง";
                    }
                } else{
                    $php_errormsg[] = "ชื่อผู้ใช้ไม่ถูกต้อง";
                }
            } else{
            }
        }catch(Exception $e){
            $php_errormsg[] = $e->getMessage();
        }
    }

    if(isset($php_errormsg)){
        echo json_encode(['<div class="text-danger">'.$php_errormsg[0].'</div>', '0'], JSON_UNESCAPED_UNICODE);
    } else if($loginMsg){
        echo json_encode(['<div class="text-success">'.$loginMsg.'</div>', '1'], JSON_UNESCAPED_UNICODE);
    }

?>

