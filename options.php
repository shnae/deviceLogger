<?php

session_start();
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
include_once 'database.php';
$systemOptions = getSystemOptions();
//var_dump($systemOptions);
$systemOptionsArray = array();
$timesThrough = 0;
foreach($systemOptions as $option){
    $systemOptionsArray[$option[0]] = $option[1];
    //var_dump ($option);
    $timesThrough+=1;

}
//echo $systemOptionsArray[devicePingInterval];
//echo $systemOptionsArray[reInitializeInterval];
//$devicePingInterval = array_search("devicePingInterval",$systemOptions);
//var_dump($systemOptionsArray);

//header:
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devices View</title>
                        <!-- Latest compiled and minified CSS -->
            <link rel="stylesheet" href="css/bootstrap.min.css">

            <!-- Optional theme -->
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">

            <!-- Latest compiled and minified JavaScript -->
            <script src="js/bootstrap.min.js"></script>

            <script 
                src="js/jquery-1.10.2.min.js">
            </script>
        </head>
        <body>
            <div class="container">
<div class="span8">
        <header>
        <h1>Device: Options</h1>
        <h2>Description:Set General Options:</h2>
        </header>
        <section id="chart defaults">
            <div class="chartForm">
                
                <form name="chartDefaults" action="index.php" method="post" id="chartDefaults">
                    <span>Default Chart Size:</span><br>
                    <input type="text" name="chartWidth" value="$chartWidth">
                    <span class="chartDefaultsFormLabels">wide by </span>
                    <input type="text" name="chartHeight" value="$chartHeight">
                    <span class="chartDefaultsFormLabels">high</span><br>
                    <span class="chartDefaultsFormLabels">Number of samples to include:</span>
                    <input type="text" name="chartSamples" value="$chartSamples"><br>
                    <input type="hidden" name="action" value="setChartDefaults">
                    <input type="submit" value="set">
              </form>
                    
            </div>
        </section>
        <section id="system defaults">
            <div class="chartForm">
                
                <form name="systemDefaults" action="index.php" method="post" id="systemDefaults">
                    <span>Device logging interval (devicePingInterval):</span>
                    <input type="text" name="devicePingInterval" value="$systemOptionsArray[devicePingInterval]">
                    <span>seconds</span>
                    <br>
                    <span class="chartDefaultsFormLabels">Re-initialize Interval (reInitializeInterval):</span>
                    <input type="text" name="reInitializeInterval" value="$systemOptionsArray[reInitializeInterval]">
                    <input type="hidden" name="action" value="setSystemDefaults">
                    <span>iterations</span><br><br>
                    <input type="submit" value="set">
              </form>
                    
            </div>
        </section>
        
       <section id="metrics">
        
END;


echo "</section></div></div></body></html>";

?>
