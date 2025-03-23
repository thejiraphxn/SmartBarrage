<?php
    include_once("../connect.php");

    // $headers = apache_request_headers();
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($headers['Authorization'])){
            if(isset($_REQUEST['RequestDataLog'])){
                $HeadersData['Authorization'] = explode("Basic ", $headers['Authorization']);
                $HeadersData['Base64Decode'] = base64_decode($HeadersData['Authorization'][1], false);
                $HeadersData['UsernameGenid'] = explode(":", $HeadersData['Base64Decode']);
                $username = strval($HeadersData['UsernameGenid'][0]);
                $genid = strval($HeadersData['UsernameGenid'][1]);
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

                        if(!isset($responseData)){
                            if($username == $UserResult['reg_username']){
                                if($genid == $UserResult['reg_genid']){
                                    $DataSQL = "SELECT * FROM data_log ORDER BY data_id DESC LIMIT 10";
                                    $DataQuery = mysqli_query($con, $DataSQL);
                                    $responseData = '
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <div class="row text-center">
                                                    <div class="col-2">
                                                        ระดับน้ำ %
                                                    </div>
                                                    <div class="col-2">
                                                        วาล์ว
                                                    </div>
                                                    <div class="col-2">
                                                        ผู้ควบคุม
                                                    </div>
                                                    <div class="col-2">
                                                        วันและเวลา
                                                    </div>
                                                    <div class="col-4">
                                                        หมายเหตุ
                                                    </div>
                                                </div>
                                            </li>
                                        ';
                                    while($DataResult = mysqli_fetch_array($DataQuery)){
                                        $ResponseDataClient['water_level'][] = $DataResult['data_water_level'];
                                        $ResponseDataClient['valve_1'][] = $DataResult['data_valve_status_1'];
                                        $ResponseDataClient['valve_2'][] = $DataResult['data_valve_status_2'];
                                        $ResponseDataClient['date'][] = $DataResult['data_action_date'];
                                        $ResponseDataClient['time'][] = $DataResult['data_action_time'];
                                        $UserControlSQL = "SELECT * FROM member_registration WHERE reg_genid = '".$DataResult['data_action_by']."' LIMIT 1";
                                        $UserControlQuery = mysqli_query($con, $UserControlSQL);
                                        $UserControlResult = mysqli_fetch_assoc($UserControlQuery);
                                        $responseData .= '
                                            <li class="list-group-item">
                                                <div class="row text-center">
                                                    <div class="col-2">
                                                        '.$DataResult['data_water_level'].'
                                                    </div>
                                                    <div class="col-2">
                                                        '.$DataResult['data_valve_status_1'].'
                                                    </div>
                                                    <div class="col-2">
                                                        '.$UserControlResult['reg_fname'].' '.$UserControlResult['reg_lname'].'
                                                    </div>
                                                    <div class="col-2">
                                                        '.$DataResult['data_action_date'].' '.$DataResult['data_action_time'].'
                                                    </div>
                                                    <div class="col-4">
                                                        '.$DataResult['data_action_detail'].'
                                                    </div>
                                                </div>
                                            </li>';
                                    }
                                    $responseData .= '</ul>';
                                    $responseStatus = "Success";
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
                echo json_encode($ResponseDataClient, JSON_UNESCAPED_UNICODE);
            }
        }
    } 
    

?>

