<?

require("../../config.php");

# try to find GD on the server? set this
# to "0" if the script doesn't work or there is a broken image
if ($CFG->gdversion == 1 or $CFG->gdversion == 2) {
    $trygd = "1";
} else {
    $trygd = "0";
}

# language (look in language/ dir for language files)
$language = "en"; global $language;

include("translate.inc");

# display warnings? set to "0" if there are warnings
# on the screen. report these to ivan@xpenguin.com so
# this option will become unnessessary.
$warnings = "1";

# earth images
# url path:name:width:height
# everything must be accurate!
# take new image names out of the t()

$earthimages = array();
$earthimages[0] = "earth_620.jpg:".t("Color").":620:310";

# which image out of those is the default?
$defaultimage = 0;

# css dots for netscape 4 users and systems without GD
# url path:name:width:height
$cssdots = array();
$cssdots[0] = "reddot.gif:Red 7x7:7:7";
$cssdots[1] = "yellowdot.gif:Yellow 7x7:7:7";
$defaultdot = 0;

# block user agents "libwww-perl" "lwp-trivial" "LWP::Simple" "PHP/"
$blockbadagents = 1;

# log entries? comment the line to disable.
$logging = "yes";

# path to the cookie
$cookiepath = "/";

# set $use_firewall to "1" if behind a proxy
if ($CFG->proxyhost and $CFG->proxyport) {
    $use_firewall="1";
    $firewall_host=$CFG->proxyhost;
    $firewall_port=$CFG->proxyport;
} else {
    $use_firewall="";
    $firewall_host="localhost";
    $firewall_port=3128;
}

?>
