<?php
include "database.php";
//shows a single chart that can be customized
session_start();
$deviceName = $_POST['deviceName'];
$deviceMetric = $_POST['deviceMetric'];
$deviceID = $_POST['deviceID'];
$metricID = $_POST['metricID'];

if ($_POST['chartFrom'] != "") {
    $chartFrom = $_POST['chartFrom'];}
else { //create a chartFrom variable from Yesterday
    $yesterday = strtotime("-1 day");
    $chartFrom = date("Y-m-d H:i:s", $yesterday);
    }


if ($_POST['chartTo'] != "") {
    $chartTo = $_POST['chartTo'];}
else {$chartTo = date("Y-m-d H:i:s");}

$action = $POST['action'];

//header:
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Devices View</title>
            <link rel="stylesheet" type="text/css" href="stylesheet.css" />
            <link rel="stylesheet"
                href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery.ui.all.css">
            <script 
                src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js">
            </script>
            <script
                src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js">
            </script>

            <script language="javascript" type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.js"></script>
            <link rel="stylesheet" type="text/css" href="jquplot/jquery.jqplot.css" />

        </head>
        <body>
            
        <header>
        <h1>Chart for Device: $deviceName</h1>
        <h2>Metric: $deviceMetric</h2>
        </header>
        <section id="main">
        <div id="chart">
            

        </div>
END;
/*
    $DeviceDataPoints = getDeviceDataPointArray($deviceID);
    foreach ($DeviceDataPoints as $dp) { //iterate through each device metric 
        //and list the latest datapoint
       echo   "<div>" . $dp["name"] . ": " . $dp["datapoint"] . 
               " timestamp: " . $dp["timestamp"] . "<br></div>";
        $points = array_reverse(getDatapointArray($deviceID, $dp["iddevicemetrics"], $chartSamples));
        //and draw a chart of each device metric:
        $html = makeChart($points, $dp["iddevicemetrics"], $dp["name"], $chartHeight, $chartWidth);
        echo $html;
        echo "<p>here</p>";
    }
 */
      echo $deviceID . "    "  . $metricID . "    "  . $chartFrom . "     " . $chartTo;
$dataPointArray = getDatapointArrayByDateRange($deviceID, $metricID, $chartFrom, $chartTo);
    //var_dump($dataPointArray);
    $html = makeChart($dataPointArray);
    echo $html;
    echo "<a href=\"index.php\">back to devices list</a></section></body></html>";

    
function makeChart($datapoints) {

    $stringDatapoints = "[[";
    $datapointArray = array();
    
    foreach ($datapoints as $datapoint) {
        $formattedDate = 
        $stringDatapoints .= $datapoint["datapoint"] . ",";
        array_push($datapointArray, $datapoint["datapoint"]);
    }
    $min = min($datapointArray);
    $max = max($datapointArray);
    $stringDatapoints = substr($stringDatapoints, 0, strlen($$stringDatapoints)-1); //strip the last comma
    $stringDatapoints .= "]]";
    $html = "<div id=\"chart$id\" style=\"height:" . "500" . "px;width:" .
            "800" . "px; \"></div>" . "\r\n" .
            "<script>$(document).ready(function(){ var plot$id = 
                $.jqplot('chart$id', $stringDatapoints, {
                    title:'chart',
                    axes:{
                        xaxis: {
                            renderer: \$jqplot.DateAxisRenderer,
                            tickOptions: {formatString: '%Y-%m-%d %H:%i:%s'}
                            },
                        yaxis: {min:$min, max:$max}
                    }
                   });
                });</script>";
    return $html;
    
    
}

?>
