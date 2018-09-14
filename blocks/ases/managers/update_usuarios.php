<?php
require_once(dirname(__FILE__). '/../../../config.php');
require('query.php');
    global $DB;
    
    $sql_query1 = "UPDATE {user_info_data} SET data = '225' WHERE (fieldid = 2 AND userid = 108003)";
    $sql_query2 = "UPDATE {user_info_data} SET data = '232' WHERE (fieldid = 2 AND userid = 99097)";
    $sql_query3 = "UPDATE {user_info_data} SET data = '210' WHERE (fieldid = 2 AND userid = 103208)";
    $sql_query4 = "UPDATE {user_info_data} SET data = '210' WHERE (fieldid = 2 AND userid = 112881)";
    $sql_query5 = "UPDATE {user_info_data} SET data = '8' WHERE (fieldid = 2 AND userid = 103152)";
    $sql_query6 = "UPDATE {user_info_data} SET data = '213' WHERE (fieldid = 2 AND userid = 103328)";
    $sql_query7 = "UPDATE {user_info_data} SET data = '213' WHERE (fieldid = 2 AND userid = 112876)";
    $sql_query8 = "UPDATE {user_info_data} SET data = '91' WHERE (fieldid = 2 AND userid = 107313)";
    $sql_query9 = "UPDATE {user_info_data} SET data = '88' WHERE (fieldid = 2 AND userid = 109597)";
    
    
    
    
    
 
    $DB->execute($sql_query1);
    $DB->execute($sql_query2); 
    $DB->execute($sql_query3);
    $DB->execute($sql_query4);
    $DB->execute($sql_query5);
    $DB->execute($sql_query6);
    $DB->execute($sql_query7);
    $DB->execute($sql_query8);
    $DB->execute($sql_query9);
   