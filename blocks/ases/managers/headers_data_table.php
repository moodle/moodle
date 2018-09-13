<?php

    $array = array();
    
    if(isset($_POST['chk']))
    {
        foreach($_POST['chk'] as $chk)
        {
            array_push($array, array("data" => $chk));
        };
    }
    
    echo json_encode($array);

?>
