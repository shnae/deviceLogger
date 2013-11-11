<?php

echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>Create New Device</title>
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
            <link rel="stylesheet" type="text/css" href="jquplot/jquery.jqplot.css" />

        </head>
        <body>
            
        <header>
        <h1>Create New Device</h1>
        <h2>Enter Device Details:</h2>
        </header>
        <section id="newDeviceInfo">
            <div class="newDeviceSpecs">
                <form action="index.php" method="post">
                <input type="hidden" name="action" value="createNewDevice">
                <span>Device Name:</span>
                <input type="text" name="deviceName"><br>
                <span>Description:</span>
                <input type="text" name="deviceDescription"><br>
                <span>Host Name/IP address: </span>
                <input type="text" name="deviceHostname"><br>
                <span>Make: </span>
                <input type="text" name="deviceMake"><br>
                <span>Model: </span>
                <input type="text" name="deviceModel"><br>
                <span>URI: </span>
                <input type="text" name="deviceUri"><br>
                <input type="submit" value="create device">
            </div>
        </section>
        </body>
        </html>
    
END;

?>
