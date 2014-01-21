<?php
include_once 'database.php';
$html_AllPagesNavigation = <<<END
<div class ="navbar navbar-inverse">
        <div class="container">
        <div class = "container">   
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">DeviceLogger</a>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li><a href="options.php">Options</a></li>
                        <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Devices <b class="caret"></b></a>
                        <ul class="dropdown-menu" role="menu">

END;

$allDevices = getDevices();
$html = "";
foreach ($allDevices as $device){
    $html_AllPagesNavigation .= '<li><a class="navDeviceLink" navBarDeviceID=' . $device["iddevices"] . ' href="#">' . $device["Name"] . '</a></li>';
};

$html_AllPagesNavigation .= <<<END
                        <li class="divider"></li>
                        <li><a id="navNewDeviceLink" href="devicenew.php">New Device...</a>
                            
                        </li>
                        </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
</div>
<div id="navDeviceLinkForm"></div>
END;
$html_AllPagesFooter = <<<END
    <script>
        $(document).ready(function () {
            $('.dropdown-toggle').dropdown();
        
        });
        $(".navDeviceLink, .btnDevice").click(function() {
            var deviceID = $(this).attr("navBarDeviceID");
            
            $("#navDeviceLinkForm").html(
                '<form action="index.php" method="post"><input type="hidden" name="action" value="viewDevice"><input type="hidden" name="device" value=' + deviceID + '></form>'
            );
            $("#navDeviceLinkForm form").submit();
        
        });
        
    </script>
    
END;


?>
 

