<?php
session_start();
date_default_timezone_set('America/Los_Angeles');
if ($_SESSION['chartHeight'] != "") {
    $chartHeight = $_SESSION['chartHeight'];
}
else $chartHeight = 300;
if ($_SESSION['chartWidth'] != "") {
    $chartWidth = $_SESSION['chartWidth'];
}
else $chartWidth = 500;
if ($_SESSION['chartSamples'] != "") {
    $chartSamples = $_SESSION['chartSamples'];
}
else $chartSamples = 25;
$deviceMetrics = getDeviceMetrics($deviceID);
$deviceInfo = getDeviceInfo($deviceID);
$deviceName = $deviceInfo["Name"];
$deviceHostName = $deviceInfo["HostName"];
$deviceDescription = $deviceInfo["Description"];
$deviceMake = $deviceInfo["Make"];
$deviceModel = $deviceInfo["Model"];
$deviceUrl = $deviceInfo["Url"];
//header:
include_once 'inc_htmlBoilerplate.php';
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devices View</title>
                       <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Device Logger: Devices List</title>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/bootstrap.css">
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/jquery-1.10.2.min.js"></script> 
            <script src="js/bootstrap.min.js"></script>
            <script src="js/bootstrap.js"></script>
            <script
                src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js">
            </script>
            <script language="javascript" type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>

            <script language="javascript" type="text/javascript" src="jqplot/jquery.jqplot.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.cursor.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.cursor.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
            <link rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.css" />

        </head>
        <body>
        $html_AllPagesNavigation
        <div class="container">
        <header>
        <h1>$deviceName <small>$deviceDescription</small></h1>
        
        </header>
        <section id="deviceInfo">
            <div class="row well">
                <div class="col-xs-6 col-md-4">
                <span>Host Name/IP address: <a href="http://$deviceHostName">$deviceHostName</a></span><br>
                <span>Make: $deviceMake</span><br>
                <span>Model: $deviceModel</span><br>
                <span>URI: <a href="http://$deviceHostName/$deviceUrl">$deviceUrl</a></span><br>
                <form name="frmEditDevice" action="index.php" method="post">
                    <button onclick="document.frmEditDevice.submit()" type="button" class="btn btn-success btn-xs">Edit Device</button>
                    <input type="hidden" name="action" value="editDevice">
                    <input type="hidden" name="device" value="$deviceID">
                </form>
                
                </div>
            </div>
        </section>
        <section id="datapoints"><h2>Datapoints:</h2><br></section>
END;
/*
 *         case 'editDevice':
            $deviceID = $_POST['device'];
            include 'deviceedit.php';
 */


$DeviceDataPoints = getDeviceDataPointArray($deviceID);
    foreach ($DeviceDataPoints as $dp) { //iterate through each device metric 
        //and list the latest datapoint
       echo   '<div class="well"><h4>' . $dp["name"] . ": <small>" . $dp["datapoint"] . 
               "<br>timestamp: " . $dp["timestamp"] . "</small><br></h4>";
        $points = array_reverse(getDatapointArray($deviceID, $dp["iddevicemetrics"], $chartSamples));
        //and draw a chart of each device metric:
        $html = makeChart($points, $dp["iddevicemetrics"], $dp["name"], $chartHeight, $chartWidth);
        echo $html;
        $html = makeChartForm($dp["name"],$dp["iddevicemetrics"], $deviceName, $deviceID);
        echo $html;
        echo "</div>";
    }
 
       echo "<a href=\"index.php\">back to devices list</a></div></body>$html_AllPagesFooter</html>";


function makeChart($datapoints, $id, $title, $height, $width) {
    $stringDatapoints = "[[";
    $datapointArray = array();
    foreach ($datapoints as $datapoint) {
        $stringDatapoints .= $datapoint["datapoint"] . ",";
        array_push($datapointArray, $datapoint["datapoint"]);
    }
    $min = min($datapointArray);
    $max = max($datapointArray);
    $stringDatapoints = substr($stringDatapoints, 0, strlen($$stringDatapoints)-1); //strip the last comma
    $stringDatapoints .= "]]";
    $html = "<div id=\"chart$id\" style=\"height:" . $height . "px;width:" .
            $width . "px; \"></div>" . "\r\n" .
            "<script>$(document).ready(function(){ plot$id = $.jqplot('chart$id', $stringDatapoints, {title:'$title', cursor:{show: true, zoom:true, showTooltip:false}, axes:{yaxis: {min:$min, max:$max}}});});</script>";
    return $html;
}
function makeChartForm($deviceMetricName, $deviceMetricID, $deviceName, $deviceID) { //returns the html for the form that brings us to the deviceviewsinglechart.php page
   $html =  "<form name=\"makeChartFor$deviceName\" action=\"deviceviewsinglechart.php\" method=\"post\" class=\"chartForm\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"makeChartDefault\">" .
        "<input type=\"hidden\" name=\"deviceName\" value=\"" . $deviceName . "\">" .
        "<input type=\"hidden\" name=\"deviceMetric\" value=\"" . $deviceMetricName . "\">" .
        "<input type=\"hidden\" name=\"deviceID\" value=\"" . $deviceID . "\">" .
        "<input type=\"hidden\" name=\"metricID\" value=\"" . $deviceMetricID . "\">" .
        "<input type=\"submit\" class=\"btn btn-sm btn-primary\" name=\"submit\" value=\"make Custom Chart\"></form>" ;
   return $html;
}
?>
