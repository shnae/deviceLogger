<?php
    $dsn = 'mysql:host=127.0.0.1;dbname=devicelogger';
    $db_username = 'devicelogger';
    $db_password = 'devicelogger';
    
    try {
        $db = new PDO($dsn, $db_username, $db_password);
    } catch (PDOException $e) {
        $error_message = $e->getMessage();
        include('database_error.php');
        exit();
    }
    
   function getDevices() { //returns list of all devices
        global $db;
        $query = "SELECT * FROM devices";
        $result = $db->query($query);
        $devicesArray = $result->fetchAll();
        return $devicesArray;
    }
    function getDeviceInfo($deviceID) { //returns all device info from device table for a specified device
        global $db;
        $query = "SELECT *
            FROM devices
            WHERE iddevices = $deviceID";
        $result = $db->query($query);
        $deviceInfo = $result->fetch();
        return $deviceInfo;
    }
    function getDeviceMetrics($deviceID) { //returns array of metrics for a device
        global $db;
        $query = "SELECT *
            FROM devicemetrics
            WHERE device = '$deviceID'";
        $result = $db->query($query);
        $metricArray = $result->fetchAll();
        return $metricArray;
    }
    function getDatapointArray($deviceID, $metricID, $datapointsCount){ //returns array of datapoints for graphing
        global $db;
        $query = "SELECT timestamp, datapoint
            FROM datapoints
            WHERE device = '$deviceID'
            AND metric = '$metricID'
            ORDER BY timestamp DESC
            LIMIT $datapointsCount";
        $result = $db->query($query);
        $datapointArray = $result->fetchAll();
        return $datapointArray;
    }
    function getDatapointArrayByDateRange($deviceID, $metricID, $from, $to){ //returns array of datapoints for graphing by date range
        global $db;
        $query = "SELECT timestamp AS timestamp, datapoint
            FROM datapoints
            WHERE device = '$deviceID'
            AND metric = '$metricID'
            AND timestamp BETWEEN '$from' AND '$to'
            ORDER BY timestamp ASC";
        try {
        $result = $db->query($query);
        $datapointArray = $result->fetchAll();
        }
        catch (Exception $e) {
            $datapointArray = "problem";
            echo "uh oh";
        }
        return $datapointArray;
    }
    function getDeviceDataPointArray($deviceID) { //returns array of all datapoints and their current value
        global $db;
        $datapointArray = array();
        $metrics = getDeviceMetrics($deviceID);
        foreach($metrics as $metric){
            $thisDeviceMetricIndex = $metric['iddevicemetrics'];
        $query = "SELECT   dm.device, dm.name, dm.iddevicemetrics, d.timestamp, d.datapoint
            FROM devicelogger.datapoints AS d 
            JOIN devicelogger.devicemetrics AS dm
            ON d.device = dm.device
            WHERE dm.iddevicemetrics = '$thisDeviceMetricIndex'
            AND d.metric = '$thisDeviceMetricIndex'
			AND d.timestamp >= TIMESTAMP(CURDATE())
            ORDER BY d.timestamp DESC
            LIMIT 1";
        $result = $db->query($query);
        $datapoint = $result->fetch();
        
        array_push($datapointArray, $datapoint);
       
        }
        return $datapointArray;
    }
    function updateDeviceMetrics($iddevicemetrics, $metricName, $metricXmlTag) { //updates individual device metrics
        global $db;
        $command = "UPDATE devicemetrics
            SET name= '$metricName' , xmlTag= '$metricXmlTag'
            WHERE iddevicemetrics = $iddevicemetrics;";
        $result = $db->exec($command);
        return $result;
    }
    function updateDeviceSettings($deviceID, $name ,$description, $hostname, $make, $model, $uri) { //updates the main device settings
        global $db;
        $command = "UPDATE devices
            SET Name= '$name' , Description = '$description', HostName = '$hostname', Make= '$make', Model='$model', Url='$uri'
            WHERE iddevices= $deviceID;";
        $result = $db->exec($command);
        return $result;
    }
    function addDevice($name, $description, $hostname, $make, $model, $uri) { //make a new device in devices table and return the iddevices index
        global $db;
        $command = "INSERT INTO devices (Name, Description, HostName, Make, Model, Url)
            VALUES ('$name', '$description', '$hostname', '$make',  '$model', '$uri');";
        $result = $db->exec($command);
        $query = "SELECT * from devices order by iddevices DESC LIMIT 1";
        $result = $db->query($query);
        $fetched = $result->fetch();
        $newDeviceID = $fetched['iddevices'];
        return $newDeviceID;
    }
    function addDeviceMetric($deviceID, $name, $xmlTag) { //adds a metric to devicemetrics table for a device
        global $db;
        $command = "INSERT INTO devicemetrics (device, name, xmlTag)
            VALUES ('$deviceID', '$name', '$xmlTag');";
        $result = $db->exec($command);
        return $result;
    }
    function deleteDeviceMetric($iddevicemetric) { //deletes a metric from devicemetrics table
        global $db;
        $command = "DELETE FROM devicemetrics
            WHERE iddevicemetrics = $iddevicemetric;";
        $result = $db->exec($command);
        return $result;
    }
    function deleteDevice($deviceID) { //delete a device and all of it's data
        global $db;
        $command = "DELETE FROM datapoints
            WHERE device = '$deviceID';";
        $deletedDatapoints = $db->exec($command);
        $command = "DELETE FROM devicemetrics
            WHERE device = '$deviceID';";
        $deletedMetrics = $db->exec($command);
        $command = "DELETE FROM devices
            WHERE iddevices = '$deviceID';";
        $deletedDevice = $db->exec($command);
        return $deletedDevice;
    }
    function getSystemOptions() {
        global $db;
        $query = "SELECT * FROM settings";
        $result = $db->query($query);
        $systemOptions = $result->fetchAll();
        return $systemOptions;
    }
    function setSystemOptions($devicePingInterval, $reInitializationInterval){
        global $db;
            $command = "UPDATE settings
            SET value= $reInitializationInterval 
            WHERE name= 'reInitializeInterval';";
        $result = $db->exec($command);
        $command = "UPDATE settings
            SET value= $devicePingInterval 
            WHERE name= 'devicePingInterval';";
        $result = $db->exec($command);
          $command = "UPDATE settings
            SET value= $reInitializationInterval 
            WHERE name= 'reInitializeInterval';";
        $result = $db->exec($command);
        return $result;
    }