<?php
include "database.php";
include_once "inc_htmlBoilerplate.php";
//shows a single chart that can be customized
session_start();
switch($_POST['action']){
    case 'makeChartFromTo': //build chartFrom and chartTo variables if user selected a set from and to date/time
        $chartFrom = $_POST['datetimepickerFrom'];
        $chartTo = $_POST['datetimepickerTo'];
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
$dataPointArray = getDatapointArrayByDateRange($deviceID, $metricID, $chartFrom, $chartTo);
?>



<!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devices View</title>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <link rel="stylesheet" href="css/jquery.datetimepicker.css">
            <link rel="stylesheet" href="css/jqueryui1.8.16/base/jquery.ui.all.css">
            <link rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.css" />
            
            
            <script src="js/jquery-1.10.2.min.js"></script>
            <script src="js/bootstrap.min.js"></script>
            <script src="js/jqueryui1.8.16/jquery-ui.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.cursor.min.js"></script>
            <script language="javascript" type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
            <script type="text/javascript" src="jqplot/plugins/jqplot.highlighter.min.js"></script>
            <script language="javascript" type="text/javascript" src="js/jquery.datetimepicker.js"></script>

        </head>
        <body>
        <?php echo $html_AllPagesNavigation; ?>
            <div class="container">
        <header>
        <h1 class="well">Chart for Device: <?php echo $deviceName ?></h1>
        <h4>Showing all data from <?php echo $chartFrom ?> to <?php echo $chartTo ?></h4>
        </header>
        <section id="main">


 <?php     



   
    $dataPointArray = getDatapointArrayByDateRange($deviceID, $metricID, $chartFrom, $chartTo);
    $html = makeChart($dataPointArray);
    echo $html;
?>
<h4>Make a new chart showing all data from:</h4>

    <form id=showChartFromTo action="deviceviewsinglechart.php" method="post" class="chartFromToForm">
        <input type="hidden" name="action"  value="makeChartFromTo">
        <input type="hidden" name="deviceName" value="<?php echo $deviceName ?>">
        <input type="hidden" name="deviceMetric" value="<?php echo $deviceMetric ?>">
        <input type="hidden" name="deviceID" value="<?php echo $deviceID ?>">
        <input type="hidden" name="metricID" value="<?php echo $metricID ?>">
        



        <input name ="datetimepickerFrom" id="datetimepickerFrom" type="text" >
        <input name ="datetimepickerTo" id="datetimepickerTo" type="text" >
        <button id="btnMakeChartDateRange"type="button" class="btn btn-sm btn-default">Go!</button>
    </form>
        <h4>Alternatively, counting back from right now...</h4>
<?php    
    $html = makeChartForm($deviceMetric, $metricID, $deviceName, $deviceID);
    echo $html;
?>
<br><br><a href=index.php>back to devices list</a>
        </section>


        <script>$('#datetimepickerFrom,#datetimepickerTo').datetimepicker({
            format:'Y-m-d H:m:s'
        });
        </script>
        <script>
            $(document).ready(function() {
                //placeholder
            });
        </script>
        <script>

        $("#btnMakeChartDateRange").click( function()
           {
             $('#showChartFromTo').submit();
           }
        );
    </script>



            </div>
            <?php echo $html_AllPagesFooter ?>
        </body>
   
   </html>

<?php    
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
    $html = "<div id=\"chart\" ></div>" . "\r\n" .
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


// $html .=  "<form name=\"showFromTo$deviceName\" action=\"deviceviewsinglechart.php\" method=\"post\" class=\"chartFromToForm\">" .

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
