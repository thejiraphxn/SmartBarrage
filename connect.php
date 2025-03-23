<?php
    $servername_database = "localhost"; //ใช้ web server ของตัวเอง
    // $port = "8889";
    $username_database = "root"; // ชื่อผู้ใช้ฐานข้อมูล
    $password_database = "root"; // รหัสฐานข้อมูล
    $name_database = "iot_project"; //ชื่อฐานข้อมูล
    $HTTP_PROTOCOL_IP = "http://192.168.8.159"; // เลข IP ของบอร์ดหลัก ที่ใช้รับส่งข้อมูล (Arduino)

    // Create connection funtion สร้างการเชื่อมต่อฐานข้อมูล
    $con = mysqli_connect($servername_database, $username_database, $password_database, $name_database);

    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    } 
    

?>