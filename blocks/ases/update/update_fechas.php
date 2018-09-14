<?php
require_once(dirname(__FILE__). '/../../../config.php');

    global $DB;
    
    $sql_query = "UPDATE {talentospilos_seguimiento} SET fecha = 1506859200 WHERE fecha = 0";
   
    echo $DB->execute($sql_query);
    