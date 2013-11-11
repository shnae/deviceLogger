<?php 
  include 'database.php';
  $array = getDeviceDataPointArray($_POST['deviceID']);
  echo json_encode($array);
?>