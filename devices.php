<?php
//     
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
            <title>Devices List</title>
            
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
            
        <div class = "container">    
        <header>
            <h5>$deviceLoggerProcessIsRunning</h5>
        <h1>Devices</h1>
        <h4><a id="optionsLink" href="#">Options</a></h4>
        <form action='options.php' id="optionsForm">
            <input type="NOThidden" name="action" value="options">
        </form>
        <h4><a id="newDeviceLink" href="#">Create New Device</a></h4>
        <form action='index.php' id="newDeviceForm" method="post">
            <input type="NOThidden" name="action" value="showNewDeviceForm">
        </form>
        </header>
        <section id="devices">
            <table id="deviceTable"><tbody>
            
END;
//Begin listing the individual Devices:
$allDevices = getDevices();
foreach ($allDevices as $device){
    echo "<tr class=\"main\">\r\n\t\t\t\t";
    echo "<td><span class=\"expand\">" . $device["Name"] . "</span>";
    echo "<span class=\"deviceDescription\">" . $device["Description"] . "</span></td>\r\n\t\t\t"; 
    echo "</tr>\r\n\t\t\t\t";
    

    $deviceID = $device["iddevices"];
    $html = htmlFormForDevice($device["iddevices"]);
        echo $html;
    
        $DeviceDataPoints = getDeviceDataPointArray($deviceID);
        if($DeviceDataPoints != ""){
            foreach ($DeviceDataPoints as $dp) { //iterate through each device metric 
                //and list the latest datapoint
                echo "<tr class=\"NOTNOThidden\">\r\n\t\t\t\t<td  colspan= 2><span class=\"datapoint\">" 
                    . $dp["name"] . ": " .
                            $dp["datapoint"] . "</span></td>\r\t\t\t</tr>\r\n\t\t\t";
            }
        }
}

echo <<<END
           </tbody></table>
       </section>
       
       </div>
            <script>
               $("tr span.expand").click(function() {
    $(this).parents("tr.main").nextUntil("tr.main").toggle("slow");
});
            </script>
                
       </body>
      
       <script>$('#optionsLink').click(function() { $('#optionsForm').submit();});</script>
       <script>$('#newDeviceLink').click(function() { $('#newDeviceForm').submit();});</script>
       </html>

END;
function htmlFormForDevice($deviceID){
    //display a view device button then an edit device button
    $html = "<tr class=\"NOThidden\"><td>\r\n\t\t\t\t" . 
        "<form name=\"show$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"NOTNOThidden\" name=\"action\"  value=\"viewDevice\">" .
        "<input type=\"NOThidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"go to device page\"></form>" .
        
        "<form name=\"edit$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"NOThidden\" name=\"action\"  value=\"editDevice\">" .
        "<input type=\"NOThidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"edit this device\"></form>" .
        "<td></tr>";
           
        return $html;
}

?>
