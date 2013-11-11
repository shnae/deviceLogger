<?php
echo <<<END
<div id="output">this will be replaced></div>
       <script id = "source" language="javascript" type="text/javascript">
       setInterval(function() {
        $(function ()
       {
        $.ajax({
            url: 'ajax_getdevicedatapoints.php',
            data: "0",
            type: "POST",
            dataType: 'json',
            success: function(data)
                {
                    var id = data[0]["datapoint"];
                    $('#output').html("<b>id: </b>" + id);
                }
        
            });
          });
         }, 3000); 
        </script>
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
END;
?>
