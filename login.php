<?php
    require_once('connect.php'); // เรียกไฟล์ connect.php มารวม
    session_start(); // เริ่มเซสชั่น

    if(isset($_SESSION['loggedin'])){ // หากมีการเก็บค่าใน $_SESSION['loggedin]
        header("location:index.php"); // เปลี่ยนหน้า index.php
    }

    if(isset($_REQUEST['login'])) {
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
                if(!isset($php_errormsg)){
                    if($username == $result['reg_username']){
                        if($passwordhash == $result['reg_password']){
                            $_SESSION['loggedin'] = $result['reg_username'];
                            $_SESSION['loggedin_key'] = $result['reg_genid'];
                            $_SESSION['loggedin_password'] = $password;
                            $loginMsg = "กำลังเข้าสู่ระบบ";
                            header("location: index.php");
                        } else{
                            $php_errormsg[] = "รหัสผ่านไม่ถูกต้อง";
                            header("refresh:2;");
                        }
                    } else{
                        $php_errormsg[] = "ชื่อผู้ใช้ไม่ถูกต้อง";
                        header("refresh:2;");
                    }
                } else{
                    $php_errormsg[] = "มีบางอย่างผิดพลาดโปรดลองอีกครั้ง";
                    header("refresh:2;login.php");
                }
            } catch(Exception $e){
                $php_errormsg[] = $e->getMessage();
                header("refresh:2;login.php");
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/thai.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- <link rel="icon" type="image/png" href="asset/picture/smklogo.png" /> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <title>Login</title>
</head>
<body class="bg-res font">

    <div class="container-fluid">
        <div style="padding-top: 6rem;"></div> 
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center" id="smklogo">
                <h1 class="fw-blod text-center">Smart Barrage</h1>
                <!-- <img onClick="Go();" class="logo-smk cursor-pointer" src='asset/picture/smk.png' align='center' height='100'> -->
            </div>
            <div class="col-md-4"></div>
        </div>


        <!-- FORM -->    
        <div class="container">
            <!-- <br> -->
            <form class="row" action="" id="form" method="post">
                <div class="col-md-4"></div>
                <div class="col-md-4 col frame" >
                    <strong>
                        <h4 class="text-center" id="alert-message">
                            <?php
                            if(isset($php_errormsg)){
                                foreach($php_errormsg as $error){
                                    echo '<div class="text-danger">'.$error.'</div>';
                                    }
                            } else if(isset($loginMsg)){
                                echo '<div class="text-success">'.$loginMsg.'</div>';
                            } else{
                                echo '<div class="text-dark">กรุณาเข้าสู่ระบบ</div>';
                            }
                                ?>
                        </h4>
                    </strong>
                    <input style="margin-top: 1.5rem !important;" value="" type="text" class="form-control" onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode >= 48 && event.charCode <= 57)" id="username_lg" name="username_lg" placeholder="ชื่อผู้ใช้">
                    <input style="margin-top: 1rem !important;" type="password" class="form-control" id="password_lg" name="password_lg" placeholder="รหัสผ่าน">
                    <div class="row" style="margin-top: 1rem !important;">
                        <div class="col-6">
                            <!-- <button type="submit" class="btn btn-secondary col-12" id="login">เข้าสู่ระบบ</button> -->
                            <input type="submit" class="btn btn-secondary col-12" id="loginsubmit" name="login" value="เข้าสู่ระบบ">
                        </div>
                        <div class="col-6">
                            <button type="button" onclick="location.href='register.php'" class="btn btn-outline-secondary col-12" name="">สมัครสมาชิก</button>
                        </div>
                        
                    </div>
                </div>
                <div class="col-md-4"></div>
                </div>
            </form>
        </div>









        <!-- <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script> -->
        <script>
           


                // $("#login").click(function(){
                //     $.ajax({
                //         type: "POST",
                //         url: "ajax/admincheck-ajax.php",
                //         data: {
                //             username_lg: $("#username_lg").val(),
                //             password_lg: $("#password_lg").val()
                //         },
                //         beforeSend: function (xhr) {
                //             xhr.setRequestHeader ("Authorization", "Basic " + btoa($("#username_lg").val() + ":" + $("#password_lg").val()));
                //         },
                //         success: function(data){
                //             data = JSON.parse(data);
                //             $("#alert-message").html(data[0]);
                //             if(data[1] == "1"){
                //                 $("#form").prop("onsubmit", "true");
                //                 $("#loginsubmit").click();
                //             } 
                //         }
                //     });
                // });

        </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>