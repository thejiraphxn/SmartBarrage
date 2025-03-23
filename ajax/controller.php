<?php
    include_once("../connect.php");
    // header("Access-Control-Allow-Origin: *");
    // header("Content-Type: application/html; charset=UTF-8");
    // header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    // header("Access-Control-Max-Age: 3600");
    // header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $headers = apache_request_headers();
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($headers['Authorization'])){
            if(isset($_REQUEST['RequestControl'])){
                $HeadersData['Authorization'] = explode("Basic ", $headers['Authorization']);
                $HeadersData['Base64Decode'] = base64_decode($HeadersData['Authorization'][1], false);
                $HeadersData['UsernameGenidPassword'] = explode(":", $HeadersData['Base64Decode']);
                $username = strval($HeadersData['UsernameGenidPassword'][0]);
                $genid = strval($HeadersData['UsernameGenidPassword'][1]);
                if(empty($username)){
                    $responseData = "Please type your username.";
                    $responseStatus = "Failed";
                } else if(empty($genid)){
                    $responseData = "Please type your genid.";
                    $responseStatus = "Failed";
                } else {
                    try {
                        $UserSQL = "SELECT * FROM member_registration WHERE reg_username = '$username' LIMIT 1";
                        $UserQuery = mysqli_query($con, $UserSQL);
                        $UserResult = mysqli_fetch_assoc($UserQuery);

                        $SystemSQL = "SELECT * FROM member_registration WHERE reg_username = 'system_iot_project' LIMIT 1";
                        $SystemQuery = mysqli_query($con, $SystemSQL);
                        $SystemResult = mysqli_fetch_assoc($SystemQuery);

                        if(!isset($responseData)){
                            if($username == $UserResult['reg_username']){
                                if($genid == $UserResult['reg_genid']){
                                    $DataStatusSolenoidSQL = "SELECT * FROM data_log ORDER BY data_id DESC LIMIT 1";
                                    $DataStatusSolenoidQuery = mysqli_query($con, $DataStatusSolenoidSQL);
                                    $DataStatusSolenoidResult = mysqli_fetch_assoc($DataStatusSolenoidQuery);

                                    if($DataStatusSolenoidResult['data_valve_status_1'] == "On" && $DataStatusSolenoidResult['data_valve_status_2'] == "On"){
                                        $DATA_FROM_API = file_get_contents($HTTP_PROTOCOL_IP."/control?username=".base64_encode($UserResult['reg_username'])."&password=".base64_encode($HeadersData['UsernameGenidPassword'][2])."&usergenid=".$UserResult['reg_genid']."&genidverification=".$SystemResult['reg_genid']."&valve_control=Off", true);
                                        if(empty($DATA_FROM_API)){
                                            $responseData = "";
                                            $responseStatus = "Failed";
                                        } else{
                                            $responseData = "TurnOff";
                                            $responseStatus = "Success";
                                        }
                                        // $responseData = "TurnOff";
                                        // $responseStatus = "Success";
                                    } else{
                                        $DATA_FROM_API = file_get_contents($HTTP_PROTOCOL_IP."/control?username=".base64_encode($UserResult['reg_username'])."&password=".base64_encode($HeadersData['UsernameGenidPassword'][2])."&usergenid=".$UserResult['reg_genid']."&genidverification=".$SystemResult['reg_genid']."&valve_control=On", true);
                                        if(empty($DATA_FROM_API)){
                                            $responseData = "";
                                            $responseStatus = "Failed";
                                        } else{
                                            $responseData = "TurnOn";
                                            $responseStatus = "Success";
                                        }
                                        
                                    }

                                } else{ 
                                    $responseData = "Genid was wrong!";
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
                $ResponseDataClient['data'] = $responseData;
                // $ResponseDataClient['more'] = $DATA_FROM_API;
                print_r(json_encode($ResponseDataClient, JSON_UNESCAPED_UNICODE));
            }




            
        }
            if(isset($_REQUEST['RequestAuto'])){
                $DATA_FROM_API = file_get_contents($HTTP_PROTOCOL_IP."/auto", true);
                if($DATA_FROM_API){
                    $responseData = $DATA_FROM_API;
                    $responseStatus = "Success"; 
                }
                $ResponseDataClient['status'] = $responseStatus;
                $ResponseDataClient['data'] = $responseData;
                // $ResponseDataClient['more'] = $DATA_FROM_API;
                print_r(json_encode($ResponseDataClient, JSON_UNESCAPED_UNICODE));
            }

    } 
    

?>
