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
        <h1>Devices</h1>
       
        
        </header>
        <section id="devices">
            <table id="deviceTable"><tbody>
            
END;
//Begin listing the individual Devices:
$allDevices = getDevices();
foreach ($allDevices as $device){
    echo "<tr class=\"main\">\r\n\t\t\t\t";
    echo "<td><span class=\"expand\">" . $device["Name"] . "</span>&nbsp";
    echo "<span class=\"deviceDescription\">" . $device["Description"] . "</span></td>\r\n\t\t\t"; 
    echo "</tr>\r\n\t\t\t\t";
    
    $deviceID = $device["iddevices"];
    
    $html = htmlFormForDevice($device["iddevices"]);
        echo $html;
        $DeviceDataPoints = getDeviceDataPointArray($deviceID);
        //var_dump($DeviceDataPoints);
        if($DeviceDataPoints != ""){
            foreach ($DeviceDataPoints as $dp) { //iterate through each device metric 
                //and list the latest datapoint
                //var_dump($dp);
                echo "<tr class=\"datapoint\">\r\n\t\t\t\t<td  colspan= 2><span class=\"datapoint\">" 
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
       $html_AllPagesFooter
       </html>

END;
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
