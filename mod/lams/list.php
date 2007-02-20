<?php // $Id$

/// send LAMS learning deisgn list as a string seperated by ":" to client

require_once("../../config.php");
require_once("lib.php");

$result = lams_get_sequences($USER->username,$courseid);
if(is_string($result)){//some exception happened!
    $auth_exception = "AuthenticateException";
    $server_not_found_exception = "ServerNotFoundException";
    if(strpos($result,$auth_exception)){//found AuthenticationException in the error message
        header("HTTP/1.1 401 Unauthenticated");
        die;
    }else if (strpos($result,$server_not_found_exception)){
        header("HTTP/1.1 417 Expectation Failed");
        die;
    }else if ($result=="NOT_SET_UP"){
        header("HTTP/1.1 402 Setup Required");
        die;
    }else{
        header("HTTP/1.1 502 Bad Gateway");
        echo $result;
        die;
    }
}
$list_str = "";
foreach($result as $design){
    $list_str .= $design['sid'].",".$design['workspace'].",".$design['title'].":";
}
if (strlen($list_str)==0){
    header("HTTP/1.1 504 Gateway Timeout");
}else{
    echo $list_str;
}

?>
