<?php

    include_once("connect.php");
    // ini_set('display_errors', '1');
    // ini_set('display_startup_errors', '1');
    // error_reporting(E_ALL);
    header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	// header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
	// header("Access-Control-Max-Age: 3600");
	// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    
    function date_bangkok(){
        $BKKDate = date_default_timezone_set("Asia/Bangkok");
        $BKKDate = date_default_timezone_get();
        $BKKDate_2 = date("Y-m-d", strtotime($BKKDate));
        return $BKKDate_2;
    }

    function time_bangkok(){
        $BKKTimeZone = date_default_timezone_set("Asia/Bangkok");
        $BKKTimeZone = date_default_timezone_get();
        $BKKTime = date("H:i:s", strtotime($BKKTimeZone));
        return $BKKTime;
    }


    if($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET"){
        if(isset($_REQUEST['RealtimeData'])){
            $username = base64_decode(mysqli_real_escape_string($con, $_REQUEST['Username']));
            $password = base64_decode(mysqli_real_escape_string($con, $_REQUEST['Password']));
            $ResultData['WaterLevel'] = floatval(mysqli_real_escape_string($con, $_REQUEST['WaterLevel']));
            $ResultData['Valve_First'] = mysqli_real_escape_string($con, $_REQUEST['Valve_First']);
            $ResultData['Valve_Second'] = mysqli_real_escape_string($con, $_REQUEST['Valve_Second']);
            $ResultData['ActionTime'] = time_bangkok();
            $ResultData['ActionDate'] = date_bangkok();
            $ResultData['ActionUserGenid'] = mysqli_real_escape_string($con, $_REQUEST['ActionUserGenid']); //System User
            $ResultData['ActionDetail'] = mysqli_real_escape_string($con, $_REQUEST['ActionDetail']); //TurnOn TurnOff 
            $ResultData['ActionGenidVerification'] = strval(mysqli_real_escape_string($con, $_REQUEST['ActionGenidVerification']));
              
            if(empty($username)){
                $responseData = "Please Type your username.";
            } else if(empty($password)){
                $responseData = "Please Type your password.";
            } else {
                try {
                    $UserSQL = "SELECT * FROM member_registration WHERE reg_username = '$username' LIMIT 1";
                    $UserQuery = mysqli_query($con, $UserSQL);
                    $UserResult = mysqli_fetch_assoc($UserQuery);
                    $passwordhash = md5($password);

                    $SystemSQL = "SELECT * FROM member_registration WHERE reg_username = 'system_iot_project' LIMIT 1";
                    $SystemQuery = mysqli_query($con, $SystemSQL);
                    $SystemResult = mysqli_fetch_assoc($SystemQuery);
                    
                    if(!isset($responseData)){
                        if($username == $UserResult['reg_username']){
                            if($passwordhash == $UserResult['reg_password']){
                               if($SystemResult['reg_genid'] == $ResultData['ActionGenidVerification']){
                                    $GenidSQL = "SELECT * FROM member_registration WHERE reg_username = 'system_iot_project' LIMIT 1";
                                    $GenidQuery = mysqli_query($con, $GenidSQL);
                                    $GenidResult = mysqli_fetch_assoc($GenidQuery);

                                    if($GenidResult['reg_genid'] == $ResultData['ActionGenidVerification']){
                                        $DataInsertSql = "INSERT INTO data_log 
                                        (data_water_level, data_valve_status_1, data_valve_status_2, data_action_by, 
                                            data_action_detail, data_action_date, data_action_time) VALUES
                                        ('".$ResultData['WaterLevel']."', '".$ResultData['Valve_First']."', '".$ResultData['Valve_Second']."', '".$ResultData['ActionUserGenid']."',
                                            '".$ResultData['ActionDetail']."', '".$ResultData['ActionDate']."', '".$ResultData['ActionTime']."' )";
                                        if(mysqli_query($con, $DataInsertSql)){
                                            $responseData = "Inserted success.";     
                                            $responseStatus = "Success";
                                        } else{
                                            $responseData = "Cannot insert data to database.";     
                                            $responseStatus = "Failed";
                                        }
                                    } else{
                                        $responseData = "Verification was wrong!.";     
                                        $responseStatus = "Failed";
                                    }
                               } else{
                                    $responseData = "System's genid was wrong!";
                                    $responseStatus = "Failed";
                                }
                            } else{
                                $responseData = "Password was wrong!";
                                $responseStatus = "Failed";
                            }
                        } else{
                            $responseData = "Username was wrong!";
                            $responseStatus = "Failed";
                        }
                    } else{
                        $responseData = "Please try this again.";
                        $responseStatus = "Failed";
                    }
                } catch(Exception $e){
                    $responseData = $e->getMessage();
                    $responseStatus = "Failed";
                }
            }

            
            $ResponseDataClient['status'] = $responseStatus;
            // $ResponseDataClient['data'] = $responseData;
            $ResponseDataClient['data'] = $responseData;
            // $ResponseDataClient['']
            print_r(json_encode($ResponseDataClient, JSON_UNESCAPED_UNICODE));
        }
    }

    

?>
