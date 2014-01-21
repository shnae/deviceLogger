<?php
include "database.php";
//shows a single chart that can be customized
session_start();
switch($_POST['action']){
    case 'makeChartFromTo': //build chartFrom and chartTo variables if user selected a set from and to date/time
        $chartFrom = $_POST['fromYear'] . '-' .
            $_POST['fromMonth'] . '-' .
            $_POST['fromDay'] . ' ' .
            $_POST['fromHour'] . ':' .
            $_POST['fromMinute'] . ':00' ;
        $chartTo = $_POST['toYear'] . '-' .
            $_POST['toMonth'] . '-' .
            $_POST['toDay'] . ' ' .
            $_POST['toHour'] . ':' .
            $_POST['toMinute'] . ':00' ;
            
        break;
        
}
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
elseif ($chartFrom == ""){ //create a chartFrom variable from Yesterday
    
    $yesterday = strtotime($fromX);
    $chartFrom = date("Y-m-d H:i:s", $yesterday);
    }


if ($_POST['chartTo'] != "") {
    $chartTo = $_POST['chartTo'];}
elseif ($chartTo == "") {$chartTo = date("Y-m-d H:i:s");}



//header:
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devices View</title>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <script src="js/jquery-1.10.2.min.js"></script> 
            <link rel="stylesheet"
                href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery.ui.all.css">
            <script 
                src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js">
            </script>
            <script
                src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js">
            </script>

            <script language="javascript" type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>

            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.cursor.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
            <script type="text/javascript" src="jqplot/plugins/jqplot.highlighter.min.js"></script>
            <link rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.css" />

        </head>
        <body>
            
        <header>
        <h1>Chart for Device: $deviceName</h1>
        <h3>Showing all data from $chartFrom to $chartTo</h3>
        
        </header>
        <section id="main">
        
END;

      
$dataPointArray = getDatapointArrayByDateRange($deviceID, $metricID, $chartFrom, $chartTo);
   
    
    $html = makeChart($dataPointArray);
    echo $html;
    echo "<h4>Make a new chart showing all data from:</h4>";
    $html = makeChartFormFromTo($deviceMetric, $metricID, $deviceName, $deviceID);
    echo $html;
    echo "<h4>Alternatively, counting back from right now...</h4>";
    $html = makeChartForm($deviceMetric, $metricID, $deviceName, $deviceID);
    echo $html;

    echo "<br><br><a href=\"index.php\">back to devices list</a></section></body></html>";

    
function makeChart($datapoints) {
    global $deviceMetric;
    $stringDatapoints = "[";
    $datapointArray = array();
    $timeArray = array();
    foreach ($datapoints as $datapoint) {
        $stringDatapoints .= "['" . $datapoint["timestamp"] . "'," . $datapoint["datapoint"] . "],";
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
                        constrainOutsideZoom: true,
                        clickReset: false,
                        looseZoom: true,
                        showVerticalLine: false,
                        showHorizontalLine: false
            
                    },
                    axes:{
                        xaxis: {
                            renderer:$.jqplot.DateAxisRenderer,
                            min: '$minTime',
                            max: '$maxTime',
                            
                            tickOptions:{showLabel: true, formatString:'%T'}
                            },
                        yaxis: {min:$min, max:$max}
                    }
                    
                   });
                });</script>";
    return $html;
    
    
}

function makeChartFormFromTo($deviceMetric, $deviceMetricID, $deviceName, $deviceID) {
 $html .=  "<form name=\"showFromTo$deviceName\" action=\"deviceviewsinglechart.php\" method=\"post\" class=\"chartFromToForm\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"makeChartFromTo\">" .
        "<input type=\"hidden\" name=\"deviceName\" value=\"" . $deviceName . "\">" .
        "<input type=\"hidden\" name=\"deviceMetric\" value=\"" . $deviceMetric . "\">" .
        "<input type=\"hidden\" name=\"deviceID\" value=\"" . $deviceID . "\">" .
        "<input type=\"hidden\" name=\"metricID\" value=\"" . $deviceMetricID . "\">" .
        "<select id=\"fromMonth\" name=\"fromMonth\">" .
              "<option value=\"1\">Month</option>" .
              "<option value=\"1\">January</option>" .
              "<option value=\"2\">February</option>" .
              "<option value=\"3\">March</option>" .
              "<option value=\"4\">April</option>" .
              "<option value=\"5\">May</option>" .
              "<option value=\"6\">June</option>" .
              "<option value=\"7\">July</option>" .
              "<option value=\"8\">Augusth</option>" .
              "<option value=\"9\">September</option>" .
              "<option value=\"10\">October</option>" .
              "<option value=\"11\">November</option>" .
              "<option value=\"12\">December</option>" .
              "</select>" .
         
         "<select id=\"fromDay\" name=\"fromDay\">" .
                "<option value =\"1\">Day...</option>";
              for($x=0; $x<32; $x++) {
                  $html .= "<option value=\"$x\">$x</option>" ;
              }
         $html .=  "</select>" .
         
         "<select id=\"fromHour\" name=\"fromHour\">" .
              "<option value=\"1\">Hour...</option>" .
              "<option value=\"1\">1 AM</option>" .
              "<option value=\"2\">2</option>" .
              "<option value=\"3\">3</option>" .
              "<option value=\"4\">4</option>" .
              "<option value=\"5\">5</option>" .
              "<option value=\"6\">6</option>" .
              "<option value=\"7\">7</option>" .
              "<option value=\"8\">8</option>" .
              "<option value=\"9\">9</option>" .
              "<option value=\"10\">10</option>" .
              "<option value=\"11\">11</option>" .
              "<option value=\"12\">12 PM</option>" .
              "<option value=\"13\">1</option>" .
              "<option value=\"14\">2</option>" .
              "<option value=\"15\">3</option>" .
              "<option value=\"16\">4</option>" .
              "<option value=\"17\">5</option>" .
              "<option value=\"18\">6</option>" .
              "<option value=\"19\">7</option>" .
              "<option value=\"20\">8</option>" .
              "<option value=\"21\">9</option>" .
              "<option value=\"22\">10</option>" .
              "<option value=\"23\">11</option>" .
              "<option value=\"24\">12 AM</option>" .
              "</select>" .
          "<select id=\"fromMinute\" name=\"fromMinute\">" .
                "<option value =\"1\">Minute...</option>";
              for($x=0; $x<61; $x++) {
                  $html .= "<option value=\"$x\">$x</option>" ;
              }
         $html .=  "</select>" .
                 
          "<select id=\"fromYear\" name=\"fromYear\">" .
          "<option value =\"2012\">Year...</option>";
          for($x=2012; $x<2050; $x++) {
             $html .= "<option value=\"$x\">$x</option>" ;
              }
         $html .=  "</select>" .
          "<p>to...</p>" .
              
                 
                 "<select id=\"toMonth\" name=\"toMonth\">" .
              "<option value=\"1\">Month</option>" .
              "<option value=\"1\">January</option>" .
              "<option value=\"2\">February</option>" .
              "<option value=\"3\">March</option>" .
              "<option value=\"4\">April</option>" .
              "<option value=\"5\">May</option>" .
              "<option value=\"6\">June</option>" .
              "<option value=\"7\">July</option>" .
              "<option value=\"8\">Augusth</option>" .
              "<option value=\"9\">September</option>" .
              "<option value=\"10\">October</option>" .
              "<option value=\"11\">November</option>" .
              "<option value=\"12\">December</option>" .
              "</select>" .
         
         "<select id=\"toDay\" name=\"toDay\">" .
                "<option value =\"1\">Day...</option>";
              for($x=0; $x<32; $x++) {
                  $html .= "<option value=\"$x\">$x</option>" ;
              }
         $html .=  "</select>" .
         
         "<select id=\"toHour\" name=\"toHour\">" .
              "<option value=\"1\">Hour...</option>" .
              "<option value=\"1\">1 AM</option>" .
              "<option value=\"2\">2</option>" .
              "<option value=\"3\">3</option>" .
              "<option value=\"4\">4</option>" .
              "<option value=\"5\">5</option>" .
              "<option value=\"6\">6</option>" .
              "<option value=\"7\">7</option>" .
              "<option value=\"8\">8</option>" .
              "<option value=\"9\">9</option>" .
              "<option value=\"10\">10</option>" .
              "<option value=\"11\">11</option>" .
              "<option value=\"12\">12 PM</option>" .
              "<option value=\"13\">1</option>" .
              "<option value=\"14\">2</option>" .
              "<option value=\"15\">3</option>" .
              "<option value=\"16\">4</option>" .
              "<option value=\"17\">5</option>" .
              "<option value=\"18\">6</option>" .
              "<option value=\"19\">7</option>" .
              "<option value=\"20\">8</option>" .
              "<option value=\"21\">9</option>" .
              "<option value=\"22\">10</option>" .
              "<option value=\"23\">11</option>" .
              "<option value=\"24\">12 AM</option>" .
              "</select>" .
          "<select id=\"toMinute\" name=\"toMinute\">" .
                "<option value =\"1\">Minute...</option>";
              for($x=0; $x<61; $x++) {
                  $html .= "<option value=\"$x\">$x</option>" ;
              }
         $html .=  "</select>" .
                 
          "<select id=\"toYear\" name=\"toYear\">" .
          "<option value =\"2012\">Year...</option>";
          for($x=2012; $x<2050; $x++) {
             $html .= "<option value=\"$x\">$x</option>" ;
              }
         $html .=  "</select>" .
                 
         "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"go!\">" .
                 
       "</form>" ;
       return $html;

}
function makeChartForm($deviceMetric, $deviceMetricID, $deviceName, $deviceID) { //returns the html for the form that brings us to the deviceviewsinglechart.php page
       $html .=  "<form name=\"showLastX$deviceName\" action=\"deviceviewsinglechart.php\" method=\"post\" class=\"chartForm\">" .
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
        "<br>" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"go back to device page\"></form>";
      $html .= "<script>$(function() {
                $('#fromX').change(function() {
                    this.form.submit();
                });
             });</script>";
   return $html;
}
?>
