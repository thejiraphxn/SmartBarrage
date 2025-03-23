<?php 
    session_start();
    header("location: index.php"); //เปลี่ยนเส้นทางไป index.php
    session_destroy(); //ล้างค่าใน $_SESSION ทั้งหมด
?>