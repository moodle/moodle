<?PHP

/**
 * This page return user info in CSV format to LAMS server.
 * The pass-in parameters are un, ts and hs.
 * un means username, ts means timestamp and hs means hash.
 * The plain text of the hash should be lower case string of
 * ts.trim()+un.trim()+serverId+serverKey. The hash algorithm
 * is sha1.
 * If the hash is not matched to the result calculated, then a
 * http error code should be returned.
 * Moodle's admin should be responsible for correctly setting
 * serverId and serverKey
 */
  include_once("../../config.php");

    if (empty($CFG->lams_serverid) || !empty($CFG->lams_serverkey)) {
        header("HTTP/1.1 401 Unauthenticated");
        exit(1);
    }
    $plaintext = trim($_GET["ts"]).trim($_GET["un"]).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
    $hash = sha1(strtolower($plaintext));
    if($hash!=$_GET["hs"]){
        header("HTTP/1.1 401 Unauthenticated");
        exit(1);
    }

    //OK, the caller is authenticated. Now let's fulfill its request.
    //What it needs is user info in CSV format. It should be like this:
    //username,first name,last name,job title, department, organisation,
    //address,phone,fax,mobile,email
    $user = get_record('user', 'username', $_GET["un"]);//return false if none found
    if(!$user){
        header("HTTP/1.1 401 Unauthenticated");//which status code is appropriate?
        exit(1);
    }
    $array = array($user->username,$user->firstname,$user->lastname,'','','','','','','',$user->email);
    $comma_separated = implode(",", $array);//need more sophiscated algorithm to generate CSV formatted string
    echo $comma_separated;
?>
