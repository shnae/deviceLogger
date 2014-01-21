<?php
//     
include_once 'database.php';
include_once 'inc_htmlBoilerplate.php';
exec("sudo lsof -c python | grep devicereader", $output, $return); 
if (sizeof($output) !==0 ) { //python is running and it's using files in the devicereader directory, we can assume it's running the service.
    $deviceLoggerProcessIsRunning = "";
}
else $deviceLoggerProcessIsRunning = "Warning: The device reader service does not appear to be running!"; 
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
        <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Device Logger: Devices List</title>
            <link rel="stylesheet" href="css/bootstrap.min.css">
            <link rel="stylesheet" href="css/bootstrap.css">
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/jquery-1.10.2.min.js"></script> 
            <script src="js/bootstrap.min.js"></script>
            <script src="js/bootstrap.js"></script>
   
        </head>
        <body>
       $html_AllPagesNavigation

        <div class="container">
        <header>
            <h5>$deviceLoggerProcessIsRunning</h5>
        <h1>Devices: <small>all devices</small></h1>
       
        
        </header>
        <section id="devices">
        <div id="deviceMetricTableForm"></div>
            
END;
//Begin listing the individual Devices:
$allDevices = getDevices();
foreach ($allDevices as $device){
    echo '<div class="row well">';
    echo '<div class="col-xs-6 col-md-4">
                <button type="button" class="btn btn-primary pull-left btnDevice" navBarDeviceID=' . $device["iddevices"] .'>' . $device["Name"] . '</button>
                <h4>&nbsp' . $device["Description"] . '</h4>';
    $deviceID = $device["iddevices"];
    

    
    

        $DeviceDataPoints = getDeviceDataPointArray($deviceID);
        if($DeviceDataPoints != ""){
            echo '<table class="table table-striped"><thead><tr><th>Metric</th><th>Last Value</th></tr></thead><tbody>';
            foreach ($DeviceDataPoints as $dp) { //iterate through each device metric and list the latest datapoint
                echo '<tr varDeviceName="' . $device["Name"] . '" varDeviceMetricName="' . $dp["name"] .  
                        '"varDeviceID="' . $deviceID . '" varDeviceMetricID="' . $dp["iddevicemetrics"] . 
                        '" class="deviceMetricList"><td>' . $dp["name"] . '</td><td>' . $dp["datapoint"] . 
                      '</td></tr>';                
            }
            
      

            echo '</tbody></table>';
        }
        echo '</div></div>';
}

?>
           </tbody></table>
       </section>
       
       </div>
        <script>$('.deviceMetricList').click(function(){
            var deviceName = $(this).attr("varDeviceName");
            var deviceID = $(this).attr("varDeviceID");
            var deviceMetricName = $(this).attr("varDeviceMetricName");
            var deviceMetricID = $(this).attr("varDeviceMetricID");
            $("#deviceMetricTableForm").html(
                    '<form action="deviceviewsinglechart.php" method="post">' +
                    '<input type="hidden" name="action" value="makeChartDefault">' +
                    '<input type="hidden" name="deviceName" value="' + deviceName + '">' +
                        '<input type="hidden" name="deviceMetric" value="' + deviceMetricName + '">' +
                        '<input type="hidden" name="deviceID" value="' + deviceID + '">' +
                        '<input type="hidden" name="metricID" value="' + deviceMetricID + '">' +
                        '</form>'
                );
            $("#deviceMetricTableForm form").submit();
            });
        </script>
       </body>
       

       <?php echo $html_AllPagesFooter; ?>
       </html>

<?php
function htmlFormForDevice($deviceID){
    //display a view device button then an edit device button
    $html = "<tr class=\"NOThidden\"><td>\r\n\t\t\t\t" . 
        "<form name=\"show$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"viewDevice\">" .
        "<input type=\"hidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"go to device page\"></form>" .
        
        "<form name=\"edit$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"editDevice\">" .
        "<input type=\"hidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"edit this device\"></form>" .
        "<td></tr>";
           
        return $html;
}

?>
