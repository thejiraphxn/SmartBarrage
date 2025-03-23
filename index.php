<?php
    require_once('connect.php'); // เรียกไฟล์ connect.php มารวม
    session_start(); // เริ่มเซสชั่น

    if(!isset($_SESSION['loggedin'])){ 
        // หากไม่มีการเก็บค่าใน $_SESSION['loggedin]
        header("location: login.php"); 
        //เปลี่นนหน้าไป login.php
    }

    $UserLoggedInSQL = "SELECT * FROM member_registration WHERE reg_genid = '".$_SESSION['loggedin_key']."'"; 
    // SQL เลือกข้อมูลจากตาราง member_registration ที่คอลลัมน์ reg_genid
    $UserLoggedInQuery = mysqli_query($con, $UserLoggedInSQL);
    // คิวรี่ข้อมูลจาก SQL Command
    $UserLoggedInResult = mysqli_fetch_assoc($UserLoggedInQuery);
    // เก็บผลลัพธ์ (แบบ Array)
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/thai.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <title>Dashboard and Control Panel</title>
</head>
<body>

        <!-- FORM -->    
        <?php include("nav.php"); ?>
        <div class="container-fluid pt-3">
            <div class="row ms-4 me-4 mb-0">
                <div class="col-md-12">
                    <div class="row frame-content mt-4 me-1">
                        <div class="col-md-3">
                            <div class="row">
                                <h3 class="text-left">Control Panel</h3>
                                <h4 class="text-left">แผงควบคุม</h4>
                                <div class="col-md-12 pt-3">
                                    <button class="btn btn-primary col-12 btn-lg" id="switch">เปิดวาล์ว</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <h3 class="text-left">Dashboard</h3>
                                <h4 class="text-left">รายงานข้อมูล</h4>
                                <div class="col-md-12 pt-2">
                                    <label class="text-left">แสดงระดับน้ำ</label>
                                    <div class="progress" role="progressbar" aria-label="Example with label" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="height: 30px">
                                        <div class="progress-bar" style="width: 0%" id="waterlevel">0%</div>
                                    </div>
                                </div>
                                <div class="col-md-12 pt-3">
                                    <div class="frame-price text-center" id="actiondatetime">
                                        วันและเวลาล่าสุดของการเปิดและปิดวาล์ว
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 frame-content mt-4">
                    <div id="graph"></div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="row m-4">
                        <div class="col-md-12 p-3 frame-content">
                            <div data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-root-margin="0px 0px -40%" data-bs-smooth-scroll="true" class="scrollspy-example bg-body-tertiary rounded-2" tabindex="0">
                                <div id="ListReport"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>







        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        <script>
           $(document).ready( function(){
                setInterval(() => {
                    $.ajax({
                        url: "ajax/datalog.php", 
                        type: "POST",
                        data: {
                            RequestDataLog: true
                        },
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Basic " + "<?= base64_encode($_SESSION['loggedin'].":".$_SESSION['loggedin_key']); ?>");
                        },
                        success: function(data){
                            console.log(data);
                            data = JSON.parse(data);

                            $("#ListReport").html(data['data']); 
                            $("#waterlevel").css("width", parseInt(data['water_level'])+"%"); 
                            $("#waterlevel").html(parseInt(data['water_level'])+"%");
                            $("#actiondatetime").html(`<label>`+data['date'][0]+`</label>`+`<h2>`+data['time'][0]+`</h2>`);

                            if(data['valve_1'][0] == "On"){
                                $("#switch").removeClass("btn-primary");
                                $("#switch").addClass("btn-danger");
                                $("#switch").html("ปิดวาล์ว");
                            }
                               
                            if(data['valve_1'][0] == "Off"){
                                $("#switch").addClass("btn-primary"); 
                                $("#switch").removeClass("btn-danger");
                                $("#switch").html("เปิดวาล์ว");
                            }
                        }
                    });
                }, 500);
                

                // $("#switch").click(function(){
                //     $.ajax({ 
                //         url: "ajax/controller.php",
                //         type: "POST",
                //         data: {
                //             RequestControl: "true"
                //         },
                //         beforeSend: function (xhr) {
                //             xhr.setRequestHeader ("Authorization", "Basic " + "<=// base64_encode($_SESSION['loggedin'].":".$_SESSION['loggedin_key'].":".$_SESSION['loggedin_password']); ?>");
                //         },
                //         success: function(data){ 
                //             data = JSON.parse(data); 
                //             if(data['status'] == "Success" && data['data'] == "TurnOn"){
                //                 $("#switch").removeClass("btn-primary");
                //                 $("#switch").addClass("btn-danger");
                //                 $("#switch").html("ปิดวาล์ว");
                //             }
                               
                //             if(data['status'] == "Success" && data['data'] == "TurnOff"){
                //                 $("#switch").addClass("btn-primary");
                //                 $("#switch").removeClass("btn-danger");
                //                 $("#switch").html("เปิดวาล์ว");
                //             }
                //         }
                //     });
                // });

                // setInterval(() => { 
                //     $.ajax({
                //         url: "ajax/controller.php",
                //         type: "POST",
                //         data: {
                //             RequestAuto: "true"
                //         },
                //         success: function(data){
                //             console.log(data);
                //         }
                //     });
                // }, 3000);

                setTimeout(() => {
                    $.ajax({
                        url: "ajax/graph.php",
                        success: function(data){
                            data = JSON.parse(data);
                            $("#graph").html(data); 
                        }
                    });
                }, 5000);
           
        });

        </script>

        

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>