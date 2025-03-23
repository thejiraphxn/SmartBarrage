<?php
    include("../connect.php");


    function date_bangkok(){
        $BKKDate = date_default_timezone_set("Asia/Bangkok"); //Set default TimeZone return boolean
        $BKKDate = date_default_timezone_get(); //return string for set timezone like Asia/Bangkok
        $BKKDate_2 = date("Y-m-d", strtotime($BKKDate)); //return string date Asia/Bangkok [Y-m-d] format 2023-11-23
        return $BKKDate_2; //return this string back to function
    }

    function date_bangkok_7days_previous(){
        $BKKDate = date_default_timezone_set("Asia/Bangkok");
        $BKKDate_2 = date("Y-m-d", strtotime('-7 days'));
        return $BKKDate_2;
    }

    function time_bangkok(){
        $BKKTimeZone = date_default_timezone_set("Asia/Bangkok");
        $BKKTimeZone = date_default_timezone_get();
        $BKKTime = date("H:i:s", strtotime($BKKTimeZone)); //return string time Asia/Bangkok [H:i:s] format 23:40:20
        return $BKKTime;
    }

    
    $BeforeCurrent7Day = date_bangkok_7days_previous();
    $SQLSelect7days = "SELECT * FROM data_log WHERE data_action_date >= '$BeforeCurrent7Day' ORDER BY data_id ASC"; 
    // เลือกข้อมูลจากฐานข้อมูลในตาราง data_log ที่คอลลัมน์ data_action_date ที่มีวันที่มากกว่า 7 วันก่อนหน้า ORDER BY โดยเลือกเรียงข้อมูลจากลำดับ id ASC จากน้อยไปหามาก
    $QUERY7days = mysqli_query($con, $SQLSelect7days);
    // คิวรี่ข้อมูลจากคำสั่งภาษา SQL
    
    $i = 0;
    while($RESULT7days = mysqli_fetch_assoc($QUERY7days)){ //นำข้อมูลมาวนลูปเพื่อเก็บลงตัวแปร Data เป็นอาเรย์ 2 มิติ
        $Data['7Days'][] = $RESULT7days['data_action_date'];
        // $Data['7Days']["2023-01-01", "2023-01-01", "2023-01-01", ..., n] ข้อมูลจะถูกต่อเรื่อย ๆ จนถึง n นับอาเรย์เป็น 0, 1, 2, 3, 4
        $i++;
    }

    $Data['7Days']['revalue'] = array_values(array_unique($Data['7Days']));
    // array_unique เลือกเก็บเฉพาะอาเรย์ที่ซ้ำกันแค่ 1 ค่า และใช้ array_values เพื่อคืนค่าอาเรย์ที่มีชื่อให้เป็นอาเรย์แบบลำดับ $arr = [0, 1, 2, 3, ..., n]

    $AllDay;
    for($n=0; $n<count($Data['7Days']['revalue']); $n++){
        // ลูปจำนวนวันที่ที่เก็บในอาเรย์ 
        $DataSelect = "SELECT * FROM data_log WHERE data_action_date = '".$Data['7Days']['revalue'][$n]."' ORDER BY data_id ASC"; //ASC เรียงจากข้อมูลน้อยไปมาก
        $DataQuery = mysqli_query($con, $DataSelect);
        // ดึงข้อมูลจาก data_log
        while($DataResult = mysqli_fetch_assoc($DataQuery)){
            // ลูปข้อมูลมาเก็บในตัวแปร $AllDay ขนาด 2 มิติ โดยอาเรย์มิติที่ 1 เก็บค่าวันที่และมิติที่ 2 เก็บค่ารวมของระดับน้ำและนับจำนวนของข้อมูล
            $AllDay[$DataResult['data_action_date']]['Data'] += $DataResult['data_water_level'];
            $AllDay[$DataResult['data_action_date']]['Round']++;
        }
        // $AllDay[$DataResult['data_action_date']]['Round']++;

        // AllDay = 
        // [ 
        //     "2023-01-01" =>
        //     [
        //         "Data" => 100,
        //         "Round" => 10
        //     ],
        //     "2023-01-02" =>
        //     [
        //         "Data" => 100,
        //         "Round" => 10
        //     ]
        // ]
    }

    // $Data['7Days']['revalue'][] = ["2023-01-01", "2023-01-02"];
    // $Data['7Days']['revalue'][0]
    for($j=0; $j<count($AllDay); $j++){
        // ลูปวันที่ตามจำนวนรอบในอาเรย์
        $Average[] = round($AllDay[$Data['7Days']['revalue'][$j]]['Data'] / $AllDay[$Data['7Days']['revalue'][$j]]['Round'], 2);
        // นำค่ามาเฉลี่ยแล้วปัดเศษทศนิยม 2 ตำแหน่ง แล้วนำค่ามาเก็บในอาเรย์ $Average จะได้ค่าเฉลี่ยของแต่ละวัน $Average = [10, 20 , 90, 60];
    }
    // print_r($Average);

    $JSON_Chart = "
    {
        type: 'line',                                
        data: {
            labels: ".json_encode($Data['7Days']['revalue']).",   
            datasets: 
              [
                  {
                      label: 'Average',
                      backgroundColor: 'rgba(255, 99, 132, 0.5)',
                      borderColor: 'rgb(255, 99, 132)',
                      borderWidth: 1,                         
                      data: ".json_encode($Average)."          
                  }
              ]
          }
    }
    ";
    // 

    // นำอาเรย์มาเข้ารหัส $Average = "{[10, 20 , 90, 60]}";

    echo json_encode('<img src='."https://quickchart.io/chart?c=".urlencode($JSON_Chart).' alt="" class="img-fluid">');
    

?>

