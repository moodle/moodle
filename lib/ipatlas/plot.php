<?php  

include("plotconf.inc");
include("plot.inc");

if (isset($user)) {
    $user = get_record("user", "id", $user);
    $fullname = fullname($user, true);
    $username = "<b>$fullname</b> [$user->city, $user->country] : ";
} else {
    $username = "";
}

if($warnings == "1") {
error_reporting(E_ALL);
} else {
error_reporting(E_ERROR);
}

// check if it is the user's ip, or another host

if(!isset($HTTP_GET_VARS["address"]) || ($HTTP_GET_VARS["address"] == "")) { 
    $address = $HTTP_SERVER_VARS['REMOTE_ADDR'];
    $local = 1; 
} else {
    $address = $HTTP_GET_VARS["address"];
    $local = 0; 
}

// this is the most important function, gets lat/lon and description of location
$values = getstuff($address, $local) or die("Error in plot.inc");

if(isset($logging) && is_writable("plotlog.txt")) {
  $log = @fopen("plotlog.txt", "a") or print "";
  @fputs($log, $HTTP_SERVER_VARS["REMOTE_ADDR"] ."\t". date("F j, Y, g:i a") . "\t$address\t$values[address]\t$values[lat]\t$values[lon]\n") or print "";
@fclose($log);
}

if(isset($HTTP_COOKIE_VARS["atlasprefs"]) && validcookie($HTTP_COOKIE_VARS["atlasprefs"])) {
list( , , , $imagething) = split(":", $HTTP_COOKIE_VARS["atlasprefs"]);
$earthimage = isvalidimage($imagething, $earthimages, $defaultimage);
} else {
$earthimage = $earthimages[$defaultimage];
}

if(strstr($earthimage, ":")) {
    list($earthimage, , , ) = split(":", $earthimage);
}

// check if we need to run it in css mode
if(!shouldrun($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {

list($width, $height) = getimagecoords($earthimages, $earthimage);

  // make sure some coords were found
  if($values["lat"] == "" || $values["lon"] == "") { 

    $display = "&nbsp;";
    $extracss = "";

} else {

list($x, $y) = getlocationcoords($values["lat"], $values["lon"], $width, $height);

if(isset($HTTP_COOKIE_VARS["atlasprefs"])) {
list( , , , , $dotname) = split(":", $HTTP_COOKIE_VARS["atlasprefs"]);
list($thedot, $dotwidth, $dotheight) = finddot($dotname, $cssdots, $defaultdot);
} else {
$dotname = $cssdots[$defaultdot];
list($dotname, , , ) = split(":", $dotname);
list($thedot, $dotwidth, $dotheight) = finddot($dotname, $cssdots, $defaultdot);
}

// magical formula for placing the css dot
$x = ($x - floor($dotwidth / 2));
$y = ($y - floor($dotheight / 2));

$extracss = "<style>
#dotDiv { padding-left:$x; padding-top:$y; }
</style>";
$display = "<div id=\"dotDiv\"><img width=\"$dotwidth\" height=\"$dotheight\" src=\"$thedot\">";

  }

} else {

  // gd mode

list($width, $height) = getimagecoords($earthimages, $earthimage) or die("Unable to find width/height for image $earthimage in config file");
$extracss = "";
$display = "<img src=\"plotimage.php?lat=$values[lat]&lon=$values[lon]\" width=\"$width\" height=\"$height\">";

}

# START HTML

print '

<html><head><title>'.t("Plotting").' '.$values["address"].'</title>
'.$extracss.'

<!-- your head tags here -->
<link rel="Stylesheet" href="ip-atlas.css">
</head><body bgcolor="'.$THEME->body.'">


<a name="map"></a>

<table valign="top" cellpadding=0 cellspacing=0 border=0 background="'.$earthimage.'" width="'.$width.'" height="'.$height.'"><tr><td valign="top">'.$display.'</td></tr></table>


<br>
';

if(isset($address)) {
print "$username $values[desc]";
}

$PHP_SELF = $HTTP_SERVER_VARS['PHP_SELF'];

print '
<br><br>
<form method="GET" action="'.$PHP_SELF.'#map">
<table width="100%"><tr><td nowrap align="left">
'.t("IP/Hostname:").' <input value="'.$values["address"].'" type="text" size="30" name="address"><input type="Submit" value="'.t("Submit").'"></td><td align="right" width="100%">
[ <a href="ip-atlas_prefs.php?lastquery='?><?php  if(isset($HTTP_GET_VARS["address"])) { echo $HTTP_GET_VARS["address"]; } ?><?php  echo '">'.t("preferences").'</a> ]
[ <a href="'."$PHP_SELF".'">'.t("locate me").'</a> ]
</td></tr></table>
</form>
';

include("footer.inc");

print "</body></html>";

?>
