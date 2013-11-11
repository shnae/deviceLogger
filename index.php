<?php
    $lifetime = 1209600; //2 weeks
    session_set_cookie_params($lifetime);
    session_start();
    require_once 'database.php';
    
    switch ($_POST['action']) {
        case 'allDevicesView': //fall through
        case '':
            include 'devices.php';
            break;
        case 'viewDevice':
            $deviceID = $_POST['device'];
            include 'deviceview.php';
            break;
        case 'setSystemDefaults':
            //
            $devicePingInterval = $_POST['devicePingInterval'];
            $reInitializationInterval = $_POST['reInitializeInterval'];
            setSystemOptions($devicePingInterval, $reInitializationInterval);
            include 'options.php';
            break;
        case 'setChartDefaults':
            $_SESSION['chartWidth'] = $_POST['chartWidth'];
            $_SESSION['chartHeight'] = $_POST['chartHeight'];
            $_SESSION['chartSamples'] = $_POST['chartSamples'];
            include 'options.php';
            break;
        case 'editDevice':
            $deviceID = $_POST['device'];
            include 'deviceedit.php';
            break;
        case 'editDeviceMetric':
            $iddevicemetrics = $_POST['iddevicemetrics'];
            $metricXmlTag = $_POST['xmlTag'];
            $metricName = $_POST['metricName'];
            $result = updateDeviceMetrics($iddevicemetrics, $metricName, $metricXmlTag);
            $deviceID = $_POST['device'];
            include 'deviceedit.php';
            break;
        case 'editDeviceSettings':
            $deviceID = $_POST['device'];
            $name = $_POST['deviceName'];
            $description = $_POST['deviceDescription'];
            $hostname = $_POST['deviceHostName'];
            $make = $_POST['deviceMake'];
            $model = $_POST['deviceModel'];
            $uri = $_POST['deviceUri'];
            $result = updateDeviceSettings($deviceID, $name, $description, $hostname, $make, $model, $uri);
            include 'deviceedit.php';
            break;
        case 'deviceAddMetric':
            $deviceID = $_POST['device'];
            $metricName = $_POST['metricName'];
            $xmlTag = $_POST['xmlTag'];
            $result = addDeviceMetric($deviceID, $metricName, $xmlTag);
            include 'deviceedit.php';
            break;
        case 'deviceDeleteMetric':
            $deviceID = $_POST['device'];
            $metricID = $_POST['metricID'];
            //echo $deviceID;
            //echo $metricID;
            $result = deleteDeviceMetric($metricID);
            //echo $result;
            include 'deviceedit.php';
            break;
        case 'showNewDeviceForm':
            include 'devicenew.php';
            break;
        case 'createNewDevice':
            $name = $_POST['deviceName'];
            $description = $_POST['deviceDescription'];
            $hostname = $_POST['deviceHostname'];
            $make = $_POST['deviceMake'];
            $model = $_POST['deviceModel'];
            $uri = $_POST['deviceUri'];
            $deviceID = addDevice($name, $description, $hostname, $make, $model, $uri);
            //echo $deviceID;
            include 'deviceview.php';
            break;
        case 'deleteDevice':
            $deviceID = $_POST['deviceID'];
            $result = deleteDevice($deviceID);
            include 'devices.php';
            break;
            
    }
        
        
    

?>