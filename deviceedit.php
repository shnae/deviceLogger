<?php
session_start();

$deviceMetrics = getDeviceMetrics($deviceID);
$deviceInfo = getDeviceInfo($deviceID);
$deviceName = $deviceInfo["Name"];
$deviceHostName = $deviceInfo["HostName"];
$deviceDescription = $deviceInfo["Description"];
$deviceMake = $deviceInfo["Make"];
$deviceModel = $deviceInfo["Model"];
$deviceUri = $deviceInfo["Url"];
include_once 'inc_htmlBoilerplate.php';
//header:
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Devices View</title>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <link rel="stylesheet" href="css/jqueryui1.8.16/base/jquery.ui.all.css">
            <script src="js/jquery-1.10.2.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/jqueryui1.8.16/jquery-ui.js"></script>


        </head>
        <body>
        $html_AllPagesNavigation
        <div class="container">
        <header>
        <h1>Edit Device: $deviceName</h1>
        </header>
        
        <section id="deviceInfo">
            <div class="deviceUpdateForm">
                <form action="index.php" method="post">
                <input type="hidden" name="action" value="editDeviceSettings">
                <input type="hidden" name="device" value="$deviceID">
                <span>Device Name:</span>
                <input type="text" name="deviceName" value="$deviceName"><br>
                <span>Description:</span>
                <input type="text" name="deviceDescription" value="$deviceDescription"><br>
                <span>Host Name/IP address:</span>
                <input type="text" name="deviceHostName" value="$deviceHostName"><br>
                <span>Make: </span>
                <input type="text" name="deviceMake" value="$deviceMake"><br>
                <span>Model: </span>
                <input type="text" name="deviceModel" value="$deviceModel"><br>
                <span>URI: </span>
                <input type="text" name="deviceUri" value="$deviceUri"><br>
                <input type="submit" value="update device settings"><br>
                </form>
            </div>
        </section>
        <section id="datapoints"><h2>Device Metrics:</h2><br></section>
END;

 $DeviceMetrics = getDeviceMetrics($deviceID);
    foreach ($DeviceMetrics as $metric) { //iterate through each device metric 
      $html .= "<div class=\"deviceMetric\">" . 
              "<form class=\"formUpdateMetric\" name=\"deviceMetricsEdit\" action=\"index.php\" method=\"post\" id=\"metric" . $metric["iddevicemetrics"] . "\">" .
              "<input type=\"hidden\" name=\"action\" value=\"editDeviceMetric\">" .
              "<input type=\"hidden\" name=\"iddevicemetrics\" value=\"" . $metric["iddevicemetrics"] . "\">" . 
              "<span>Name:</span>" .
              "<input type=\"text\" name=\"metricName\" value=\"" . $metric["name"] . "\">" .
              "<span>xmlTag:</span>" . 
              "<input type=\"text\" name=\"xmlTag\" value=\"" . $metric["xmlTag"] . "\">" .
              "<input type=\"hidden\" name=\"device\" value=\"$deviceID\">" .
              "<input type=\"submit\" value=\"update\"></form>" . 
              "<form  name=\"deleteMetric\" action=\"index.php\" method=\"post\">" .
              "<input type=\"hidden\" name=\"action\" value=\"deviceDeleteMetric\">" .
              "<input type=\"hidden\" name=\"device\" value=\"$deviceID\">" .
              "<input type=\"hidden\" name=\"metricID\" value=\"" . $metric["iddevicemetrics"] . "\">" .
              "<input type=\"submit\" value=\"delete\">" .
              "</form></div>";

    }
    echo $html . "<br><br>";
    echo "<a id=\"newMetricLink\" href=\"#\">create a new metric?</a><br>";
    $html = "<div id=\"hiddenForm\">" .
            "<form name=\"newMetric\" action=\"index.php\" id=\"theHiddenForm\" method=\"post\">" .
            "<input type=\"hidden\" name=\"action\" value=\"deviceAddMetric\">" .
            "<input type=\"hidden\" name=\"device\" value=\"$deviceID\">" .
            "<span>Name:</span>" .
            "<input type=\"text\" name=\"metricName\">" .
            "<span>xmlTag:</span>" .
            "<input type=\"text\" name=\"xmlTag\">" .
            "<input type=\"submit\" value=\"add\">" .
            "</form>" .
            "</div>";
    echo $html;
    echo "<br><br><a href=\"index.php\">back to devices list</a>";
    echo <<<END
        <form id="deleteDeviceForm" action="index.php" method="post">
            <input type="hidden" name="action" value="deleteDevice">
            <input type="hidden" name="deviceID" value="$deviceID">
        </form>
        <a id="linkDeleteDevice" href="#" >delete this device</a>
        <script>
            function confirmSubmit() {
                var yes=confirm("Are you sure you want to delete this device? This will delete the device, it's metrics, and all associated data")
                if (yes)
                    return true ;
                else
                    return false;
            }
        </script>
        <script>
            $('#linkDeleteDevice').click(function() {
                var submitIt = confirmSubmit()
                if (submitIt == true) 
                    $('#deleteDeviceForm').submit();
                else
                    return false;
            });
        </script>
END;
    
    echo "<script>$(\"#hiddenForm\").hide();</script>";
    echo "<script>$(\"#newMetricLink\").click(function() {
            $(\"#hiddenForm\").show('slow')});</script>";

    echo "<br></div></body></html>";


?>
