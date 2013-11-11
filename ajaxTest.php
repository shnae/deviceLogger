<?php
//              
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Devices List</title>
            <link rel="stylesheet" type="text/css" href="stylesheet.css" />
            <link rel="stylesheet"
                href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery.ui.all.css">
            <script 
                src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js">
            </script>
            <script
                src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.js">
            </script>

        </head>
        <body>
            <script>$("body").hide();</script>
        <header>
        <h1>Devices</h1>
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
    foreach ($DeviceDataPoints as $dp) { //iterate through each device metric 
        //and list the latest datapoint
       echo "<tr class=\"hidden\">\r\n\t\t\t\t<td  colspan= 2><span class=\"datapoint\">" 
        . $dp["name"] . ": " .
                $dp["datapoint"] . "</span></td>\r\t\t\t</tr>\r\n\t\t\t";
    }
}

echo <<<END
           </tbody></table>
       </section>
       

            <script>
               $("tr span.expand").click(function() {
    $(this).parents("tr.main").nextUntil("tr.main").toggle("slow");
});
            </script>
                
       </body>
       <script>$("body").show("slow");</script>
       </html>

END;
function htmlFormForDevice($deviceID){
    //display a view device button then an edit device button
    $html = "<tr class=\"hidden\"><td>\r\n\t\t\t\t" . 
        "<form name=\"show$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"view\">" .
        "<input type=\"hidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"go to device page\"></form>" .
        
        "<form name=\"edit$deviceID\" action=\"index.php\" method=\"post\" class=\"deviceAction\">" .
        "<input type=\"hidden\" name=\"action\"  value=\"edit\">" .
        "<input type=\"hidden\" name=\"device\" value=\"" . $deviceID . "\">" .
        "<input type=\"submit\" class=\"buttons\" name=\"submit\" value=\"edit this device\"></form>" .
        "<td></tr>";
           
        return $html;
}

?>
