<?php 
    require_once(dirname(__FILE__). '/../../../config.php');

    global $DB;

    $sql_query = "DELETE FROM {talentospilos_semestre} WHERE nombre = '2018A'";

    echo $DB->execute($sql_query);