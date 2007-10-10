<?PHP  // $Id$
       // Authentication by looking up a RADIUS server
       // Contributed by Clive Gould <clive@ce.bromley.ac.uk>

function auth_user_login ($username, $password) {
    // Returns true if the username and password work
    // and false if they are wrong or don't exist.
    
    require_once 'Auth/RADIUS.php';
    
    global $CFG;
    
    // Added by Clive on 7th May for test purposes
    // printf("Username: $username <br>");
    // printf("Password: $password <br>");
    // printf("auth_radiushost: $CFG->auth_radiushost <br>");
    // printf("auth_radiusnasport: $CFG->auth_radiusnasport <br>");
    // printf("auth_radiussecret: $CFG->auth_radiussecret <br>");
    
    $rauth = new Auth_RADIUS_PAP($username, $password);
    $rauth->addServer($CFG->auth_radiushost, $CFG->auth_radiusnasport, $CFG->auth_radiussecret);
    
    if (!$rauth->start()) {
        printf("Radius start: %s<br>\n", $rauth->getError());
        exit;
    }
    
    $result = $rauth->send();
    if (PEAR::isError($result)) {
        printf("Radius send failed: %s<br>\n", $result->getMessage());
        exit;
    } else if ($result === true) {
        // printf("Radius Auth succeeded<br>\n");
        return true;
    } else {
        // printf("Radius Auth rejected<br>\n");
        return false;
    }
    
    // get attributes, even if auth failed
    if (!$rauth->getAttributes()) {
        printf("Radius getAttributes: %s<br>\n", $rauth->getError());
    } else {
        $rauth->dumpAttributes();
    }
    
    $rauth->close();
}
?>
