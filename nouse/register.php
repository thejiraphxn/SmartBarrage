<?php
    echo "<h1 class='text-center'>Not use</h1>";
    require_once("connect.php"); //require all file from connect.php
    session_start(); //start the session for keep data in $_SESSION

    if(isset($_SESSION['loggedin'])){ //if has data in $_SESSION['loggedin]
        header("location:index.php"); //redirect to index.php
    }
    

    if(isset($_REQUEST["register"])){
        $firstname = mysqli_real_escape_string($con, $_REQUEST['firstname']); //Hook data from form box <form> keep in varrible mysqli_real_escape_string() return string
        $lastname = mysqli_real_escape_string($con, $_REQUEST['lastname']);
        $email = mysqli_real_escape_string($con, $_REQUEST['email']);
        $username = mysqli_real_escape_string($con, $_REQUEST['username']);
        $password = mysqli_real_escape_string($con, $_REQUEST['password']);
        $password2 = mysqli_real_escape_string($con, $_REQUEST['password2']);
        $genid = "gnr-".uniqid(); //Generate string 13 characters and + gnr-


        if(empty($firstname)){ //check empty in varriable empty() return True or False
            $php_errormsg[] = "โปรดใส่ชื่อ";
        } else if(empty($lastname)){
            $php_errormsg[] = "โปรดใส่นามสกุล";
        } else if(empty($email)){
            $php_errormsg[] = "โปรดใส่อีเมล";
        } else if(strlen($username) < 10){
            $php_errormsg[] = "ชื่อผู้ใช้ต้องมากกว่า 10 ตัวอักษร";
        } else if(empty($username)){
            $php_errormsg[] = "โปรดตั้งชื่อผู้ใช้";
        } else if(empty($password)){
            $php_errormsg[] = "โปรดตั้งรหัสผ่าน";
        } else if(empty($password2)){
            $php_errormsg[] = "โปรดใส่รหัสผ่านอีกครั้ง";
        } else if($password != $password2){
            $php_errormsg[] = "โปรดใส่รหัสผ่านให้ตรงกัน";
        } else if(strlen($password) < 8){
            $php_errormsg[] = "รหัสผ่านต้องมากกว่า 8 ตัวขึ้นไป";
        } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //filter string for validate email filter() return True or False
            $php_errormsg[] = "ต้องใส่เป็นอีเมลเท่านั้น";
        }
        else {
            try {
                $checker = "SELECT * FROM member_registration WHERE reg_username = '$username' AND reg_email = '$email' LIMIT 1"; //Read data from database by using SQL langauge
                $checkerquery = mysqli_query($con, $checker); //Query command from SQL $checker like SELECT (for read), UPDATE (for update), INSERT INTO (for add the new row), DELETE (for delete the row) mysqli_query() return string Array
                $checkerresult = mysqli_fetch_assoc($checkerquery); //return data Array format by column selected from SQL Command
                if($checkerresult){
                    if($checkerresult['reg_email'] == $email){
                        $php_errormsg[] = "อีเมลนี้มีการใช้งานแล้ว";
                    } else if($checkerresult['reg_username'] == $username){
                        $php_errormsg[] = "ชื่อผู้ใช้นี้มีการใช้งานแล้ว";
                    } 
                    
                }

                if(!isset($php_errormsg)){
                    $passwordhash = md5($password); //Encode input password from user md5() return string
                    $registerSQL = "INSERT INTO member_registration (reg_fname, reg_lname, reg_email, reg_username, reg_password, reg_role, reg_genid) VALUES ('$firstname', '$lastname', '$email', '$username', '$passwordhash', 'general', '$genid')";
                    if(mysqli_query($con, $registerSQL)){
                        $registerMsg = "เพิ่มข้อมูลสมบูรณ์";
                        header("refresh:2;index.php"); //refresh current page 2 second and redirect to index.php
                    }
                    else{
                        $php_errormsg[] = "ไม่สามารถเพิ่มลงฐานข้อมูลได้";
                    }
                    
                } 
                
                }catch(Exception $e){
                $php_errormsg[] = $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Register</title>
</head>
<body class="font">
<div class="container-fluid">
        <div class="" style="padding-top: 6rem"></div> 
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4 text-center">
                <h1 class="fw-blod text-center">Smart Barrage</h1>
                <!-- <img onClick="location.href='login.php'" src="asset/picture/smk.png" align="center" alt="" height="100"> -->
            </div>
            <div class="col-md-4"></div>
        </div>
        

        
        <!-- FORM -->    
        <div class="container">
            <form class="row" action="" method="post">
                <div class="col-md-4"></div>
                <div class="col-md-4 col frame" align="center">
                    <strong>
                        <h4 class="text-center">
                            <?php
                            if(isset($php_errormsg)){
                                foreach($php_errormsg as $error){
                                    echo '<div style="font-size: 18px;" class="text-danger">'.$error.'</div>';
                                }
                            } else if(isset($registerMsg)){
                                echo '<div style="font-size: 18px;" class="text-success">'.$registerMsg.'</div>';
                            } else{
                                echo '<div class="text-dark">ลงทะเบียน</div>';
                            }
                                ?>
                        </h4>
                        <div class="fw-normal" id="status"></div>
                    </strong>
                    <input style="margin-top: 1.0rem !important;" type="text" class="form-control" id="fname" onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122)" name="firstname" placeholder="ชื่อจริง">
                    <input style="margin-top: 1.0rem !important;" type="text" class="form-control" id="lname" onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122)" name="lastname" placeholder="นามสกุล">
                    <input style="margin-top: 1.0rem !important;" type="text" class="form-control" id="email" name="email" placeholder="อีเมล">
                    <input style="margin-top: 1.0rem !important;" type="text" class="form-control" id="username" onkeypress="return (event.charCode >= 65 && event.charCode <= 90) || (event.charCode >= 97 && event.charCode <= 122) || (event.charCode >= 48 && event.charCode <= 57)" name="username" placeholder="ชื่อผู้ใช้">
                    <input style="margin-top: 1.0rem !important;" type="password" class="form-control" id="password1" name="password" placeholder="รหัสผ่าน">
                    <input style="margin-top: 1.0rem !important;" type="password" class="form-control" id="password2" name="password2" placeholder="ยืนยันรหัสผ่าน" >
                    <div style="margin-top: 1.0rem !important;" class="form-check" align="left">
                        <input class="form-check-input" type="checkbox" value="" id="checker">
                        <label class="form-check-label" for="checker">
                            แสดงรหัสผ่าน
                        </label>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button style="margin-top: 1.0rem !important;" type="submit" class="btn btn-secondary col-12" name="register">ลงทะเบียน</button>
                        </div>
                        <div class="col-6">
                            <a href="login.php" style="margin-top: 1.0rem !important;" class="btn btn-outline-secondary col-12">เป็นสมาชิกแล้ว</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
                </div>
            </form>
        </div>



    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){
            $('#username').on('keyup', function(){
                var username = $(this).val();
                if(username != ""){
                    $.ajax({
                        type: 'POST',
                        url: "ajax/register-check.php",
                        data: {
                            username: username
                        },
                        success: function(result){
                            if(result == "true"){
                                $('#username').removeClass('form-correct');
                                $('#username').addClass('form-error');
                                $('#status').html("<div class='text-danger'>ชื่อผู้ใช้นี้ใช้งานไม่ได้</div>");
                            } else if(result == "false") {
                                $('#username').removeClass('form-error');
                                $('#username').addClass('form-correct');
                                $('#status').html("<div class='text-success'>ชื่อผู้ใช้นี้ใช้งานได้</div>");
                            } else if(result == "usernameerror"){
                                $('#username').removeClass('form-correct');
                                $('#username').addClass('form-error');
                                $('#status').html("<div class='text-danger'>ชื่อผู้ใช้ต้องมี 10 ตัวอักษรขึ้นไป</div>");
                            }
                        },
                        error: function(err){
                            alert("Error 1-1");
                        }
                    });
                } 
            });


            $('#email').on('keyup', function(){
                var email = $(this).val();
                if(email != ""){
                    $.ajax({
                        type: 'POST',
                        url: "ajax/register-check.php",
                        data: {
                            email: email
                        },
                        success: function(result){
                            // $('#itemlist').html("สถานะจำนวน "+result+" ชิ้น");
                            if(result == "true"){
                                $('#email').removeClass('form-correct');
                                $('#email').addClass('form-error');
                                $('#status').html("<div class='text-danger'>อีเมลนี้ใช้งานไม่ได้</div>");
                            } else if(result == "false") {
                                $('#email').removeClass('form-error');
                                $('#email').addClass('form-correct');
                                $('#status').html("<div class='text-success'>อีเมลนี้ใช้งานได้</div>");
                            } else if(result == "emailerror"){
                                $('#email').removeClass('form-correct');
                                $('#email').addClass('form-error');
                                $('#status').html("<div class='text-danger'>อีเมลผิดรูปแบบ</div>");
                            }
                        },
                        error: function(err){
                            alert("Error 1-2");
                        }
                    });
                } 
            });


            $('#checker').click( function(){
                myFunction().click();
            });

            function myFunction() {
                var pw1 = document.getElementById("password1");
                var pw2 = document.getElementById("password2");
                if (pw1.type === "password" && pw2.type === "password") {
                    pw1.type = "text";
                    pw2.type = "text";
                } else {
                    pw1.type = "password";
                    pw2.type = "password";
                }
            }


            $('#password2').on('keyup', function(){
                var password1 = $("#password1").val();
                var password2 = $(this).val();
                if(password2 != ""){
                    $.ajax({
                        type: 'POST',
                        url: "pos/ajax/register-check.php",
                        data: {
                            password: password1,
                            passwordcf: password2
                        },
                        success: function(result){
                            // $('#itemlist').html("สถานะจำนวน "+result+" ชิ้น");
                            if(result == "false") {
                                $('#password1').removeClass('form-error');
                                $('#password2').removeClass('form-error');
                                $('#password1').addClass('form-correct');
                                $('#password2').addClass('form-correct');
                                $('#status').html("<div class='text-success'>รหัสตรงกัน</div>");
                            } else if(result == "notsamepass"){
                                $('#password1').removeClass('form-correct');
                                $('#password2').removeClass('form-correct');
                                $('#password1').addClass('form-error');
                                $('#password2').addClass('form-error');
                                $('#status').html("<div class='text-danger'>รหัสผ่านยืนยันไม่ตรงกัน</div>");
                            }
                        }
                    });
                } 
            });
        });


        
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
<?php include("version.php"); ?>
</html>