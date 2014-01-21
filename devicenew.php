<?php
include_once 'database.php';
include_once 'inc_htmlBoilerplate.php';
echo <<<END
   <!DOCTYPE html> 
   <html lang="en">
        <head>
            <meta charset="utf-8">
             <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Devices View</title>
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
        </div>
        </body>
        $html_AllPagesFooter
        </html>
    
END;

?>
