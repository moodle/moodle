Description of libcurlemu v1.0.3 import into Moodle

Changes:
 * example.php - removed
 * original HTTPRetriever v1.1.5 replaced by standalone package v1.1.9
 * fixed many warnings and cookie problem in HTTPRetriever - marked by //moodlefix (to be reported later upstream after some more testing)
 
Note to developers:
 1/ if you want to test binary curl, disable curl in PHP config
 2/ if you want to test php emulation, do 1/ and define("CURL_PATH","/usr/bin/curlxxxxxxx"); in config.php

TODO:
 * test the proxy function and add admin tree settings for $CFG->proxyuser and $CFG->proxypassword

$Id$