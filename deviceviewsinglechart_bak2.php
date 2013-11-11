<?php
include "database.php";
//shows a single chart that can be customized
session_start();
$deviceName = $_POST['deviceName'];
$deviceMetric = $_POST['deviceMetric'];
$deviceID = $_POST['deviceID'];
$metricID = $_POST['metricID'];
date_default_timezone_set('America/Los_Angeles');

if ($_POST['fromX'] == ""){ //if we didn't get a -X from our 'last X' form, default to 1 day
        $fromX = "-1 day";}
else {
    $fromX = $_POST['fromX'];
}
if ($_POST['chartFrom'] != "") {
    $chartFrom = $_POST['chartFrom'];}
else { //create a chartFrom variable from Yesterday
    
    $yesterday = strtotime($fromX);
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
            <script language="javascript" type="text/javascript" src="jqplot/jquery.jqplot.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jpqlot/plugins/jqplot.cursor.min.js"></script>
            <script language="javascript" type="text/javascript" src="jpqlot/plugins/jqplot.cursor.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>

            <link rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.css" />

        </head>
        <body>
            
        <header>
        <h1>Chart for Device: $deviceName</h1>
            <p>$chartTo </p>
        </header>
        <section id="main">
        
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
      
$dataPointArray = getDatapointArrayByDateRange($deviceID, $metricID, $chartFrom, $chartTo);
    //var_dump($dataPointArray);
    $html = makeChart($dataPointArray);
    echo $html;
    $html = makeChartForm($deviceMetric, $metricID, $deviceName, $deviceID);
    echo $html;
    echo "<br><a href=\"index.php\">back to devices list</a></section></body></html>";

    
function makeChart($datapoints) {
    global $deviceMetric;
    $stringDatapoints = "[";
    $datapointArray = array();
    $timeArray = array();
    foreach ($datapoints as $datapoint) {
        $stringDatapoints .= "[" . $datapoint["timestamp"] . "," . $datapoint["datapoint"] . "],";
        array_push($datapointArray, $datapoint["datapoint"]);
        array_push($timeArray, $datapoint["timestamp"]);
    }
    $stringDatapoints = substr($stringDatapoints, 0, strlen($$stringDatapoints)-1); //strip the last comma
    $min = min($datapointArray);
    $max = max($datapointArray);
    $minTime = min($timeArray);
    $maxTime = max($timeArray);
    
    $stringDatapoints .= "]";
    $html = "<div id=\"chart\" style=\"height:" . "500" . "px;width:" .
            "800" . "px; \"></div>" . "\r\n" .
            "<script>$(document).ready(function(){
                $.jqplot.config.enablePlugins = true;
                var line1=$stringDatapoints;   
                 var plot = $.jqplot('chart$id', [line1], {
                    
                    cursor:{
                        show:true,
                        zoom:true,
                        showTooltip:false,
                        followMouse: true,
                        constrainOutsideZoom: false,
                        clickReset: true
            
                    },
                    axes:{
                        xaxis: {
                            renderer:$.jqplot.DateAxisRenderer,
                            min: $minTime,
                            max: $maxTime,
                            
                            tickOptions:{showLabel: true, formatString:'%T'}
                            },
                        yaxis: {min:$min, max:$max}
                    }
                    
                   });
                });</script>";
    return $html;
    
    
}
function makeChartForm($deviceMetric, $deviceMetricID, $deviceName, $deviceID) { //returns the html for the form that brings us to the deviceviewsinglechart.php page
   /*$html =  "<form name=\"makeChartFor$deviceName\" action=\"deviceviewsinglechart.php\" method=\"post\" class=\"chartForm\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"makeChartDefault\">" .
        "<input type=\"hidden\" name=\"deviceName\" value=\"" . $deviceName . "\">" .
        "<input type=\"hidden\" name=\"deviceMetric\" value=\"" . $deviceMetricName . "\">" .
        "<input type=\"hidden\" name=\"deviceID\" value=\"" . $deviceID . "\">" .
        "<input type=\"hidden\" name=\"metricID\" value=\"" . $deviceMetricID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"Refresh This Chart\"></form>" ;
    */  $html .=  "<form name=\"showLastX$deviceName\" action=\"deviceviewsinglechart.php\" method=\"post\" class=\"chartForm\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"makeChartLastX\">" .
        "<input type=\"hidden\" name=\"deviceName\" value=\"" . $deviceName . "\">" .
        "<input type=\"hidden\" name=\"deviceMetric\" value=\"" . $deviceMetric . "\">" .
        "<input type=\"hidden\" name=\"deviceID\" value=\"" . $deviceID . "\">" .
        "<input type=\"hidden\" name=\"metricID\" value=\"" . $deviceMetricID . "\">" .
        "<select id=\"fromX\" name=\"fromX\">" .
              "<option value=\"-1 minute\">Show me the data since...</option>" .
              "<option value=\"-1 minute\">Last 1 minute</option>" .
              "<option value=\"-5 minutes\">Last 5 minutes</option>" .
              "<option value=\"-30 minutes\">Last 30 minutes</option>" .
              "<option value=\"-1 hour\">Last 1 hour</option>" .
              "<option value=\"-12 hours\">Last 12 hours</option>" .
              "<option value=\"-1 day\">Last day</option>" .
              "<option value=\"-1 week\">Last week</option>" .
              "<option value=\"-1 month\">Last month</option>" .
              "</select>" .
       "</form>" ;
      $html .="<form name=\"show$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"viewDevice\">" .
        "<input type=\"hidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"go back to device page\"></form>";
      $html .= "<script>$(function() {
                $('#fromX').change(function() {
                    this.form.submit();
                });
             });</script>";
   return $html;
}
?>
