<?php  

include("plotconf.inc"); 
include("plot.inc"); 

if($warnings == "1") {
error_reporting(E_ALL);
} else {
error_reporting(E_ERROR);
}

?>
<?php

 if(shouldrun($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) { 
   $drawmode = "GD";
 } else {
   $drawmode = "CSS";
 }

if(isset($HTTP_POST_VARS["button"])) {
// save data from the POST
setcookie ("atlasprefs", "", time() - 36000000);
setcookie ("atlasprefs", "$HTTP_POST_VARS[shape]:$HTTP_POST_VARS[color]:$HTTP_POST_VARS[size]:$HTTP_POST_VARS[earthimage]:$HTTP_POST_VARS[cssdot]:$HTTP_POST_VARS[seldrawmode]", time() + 36000000, $cookiepath);

$setshape = $HTTP_POST_VARS["shape"];
$setcolor = $HTTP_POST_VARS["color"];
$setsize = $HTTP_POST_VARS["size"];
$setearthimage = $HTTP_POST_VARS["earthimage"];
$setcssdot = $HTTP_POST_VARS["cssdot"];
$setseldrawmode = $HTTP_POST_VARS["seldrawmode"];

 if($setseldrawmode == "1") {
   $drawmode = "GD";
 } else {
   $drawmode = "CSS";
 }

} elseif(isset($HTTP_COOKIE_VARS["atlasprefs"]) && validcookie($HTTP_COOKIE_VARS["atlasprefs"])) {
// get data from the cookie
@list($setshape, $setcolor, $setsize, $setearthimage, $setcssdot, $setseldrawmode) = split(":", $HTTP_COOKIE_VARS["atlasprefs"]);
} else {
$setshape = "Diamond";
$setsize = "3";
$setcolor = "red";
$setearthimage = $earthimages[$defaultimage];
$setcssdot = "reddot.gif";

if(shouldrun($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
  $setseldrawmode = 1;
} else {
  $setseldrawmode = 0;
}

// override old cookie if there is post data

 if(isset($HTTP_POST_VARS["seldrawmode"])) {
   $setsetdrawmode = $HTTP_POST_VARS["seldrawmode"];
 }

}

?>

<?php  # START HTML
 ?>

<html><head><title><?php  echo t("IP-Atlas Preferences") ?></title>

<!-- your head tags here -->
<link rel="Stylesheet" href="ip-atlas.css">
</head><body bgcolor="#ffffff">

</head><body>

<b><?php  echo t("IP-Atlas preferences"); ?></b> <?php  echo t("(cookie based)"); ?><br><br>

<?php
if(isset($HTTP_POST_VARS["button"])) {
print t("Your settings have been saved. You can now try"); ?> <a href="plot.php<?php  if(isset($HTTP_GET_VARS["lastquery"])) { echo "?address=$HTTP_GET_VARS[lastquery]"; } ?>"><?php  print t("plotting something.")."</a>"."<br><br>";
}


?>

<form action="<?php  echo $HTTP_SERVER_VARS['PHP_SELF']; ?><?php  if(isset($HTTP_GET_VARS["lastquery"])) { echo "?lastquery=$HTTP_GET_VARS[lastquery]"; } ?>" method="POST">

<?php  if(istheregd()) {
echo t("Draw mode (defaults guessed for your browser):"); 

print "<br><select name=\"seldrawmode\">";

if(!isset($setseldrawmode)) {
  if(shouldrun($HTTP_SERVER_VARS['HTTP_USER_AGENT'])) {
    $setseldrawmode = 1;
  } else {
    $setseldrawmode = 0;
  }
}

if($setseldrawmode == "1") {
  echo "<option value=\"1\" selected>GD";
  echo "<option value=\"0\">CSS";
} elseif($setseldrawmode == "0") {
  echo "<option value=\"1\">GD";
  echo "<option value=\"0\" selected>CSS";
}

print "</select><br><br>";

} else {

  print "<input type=\"hidden\" name=\"seldrawmode\" value=\"0\">";

}

?>

<?php  echo t("Pointer Preferences (the dot that marks lat/lon):"); ?><br>
<?php
if($drawmode == "GD") {
print '
<input type="hidden" name="cssdot" value="reddot.gif">

<table><tr>

<td>'.t("Shape:").'</td><td><select name="shape">
';

$shapes = array("Diamond", "Diamond Outline", "Square", "Square Outline", "Cross");
foreach($shapes as $curshape) {

if($setshape == $curshape) {
print "<option value=\"$curshape\" selected>".t($curshape);
} else {
print "<option value=\"$curshape\">".t($curshape);
}

}

print "</select></td></tr><tr><td>".t("Size:")."</td><td><select name=\"size\">";

$sizes = array("2", "3", "4", "5", "6", "7", "8");
foreach($sizes as $cursize) {

if($setsize == $cursize) {
print "<option value=\"$cursize\" selected>$cursize";
} else {
print "<option value=\"$cursize\">$cursize";
}

}

print "</select></td></tr><tr><td>".t("Color:")."</td><td><select name=\"color\">";

$colors = array("red", "white", "yellow", "magenta", "cyan", "green", "violet");
foreach($colors as $curcolor) {

if($setcolor == $curcolor) {
print "<option value=\"$curcolor\" selected>".t($curcolor);
} else {
print "<option value=\"$curcolor\">".t($curcolor);
}

}

print "
</select></td></tr></table>
";

} elseif($drawmode == "CSS") {

print t("Pointer:")." <select name=\"cssdot\">";

foreach($cssdots as $curdot) {

list($filename, $curdot, , ) = split(":", $curdot);

if($setcssdot == $filename) {
print "<option value=\"$filename\" selected>$curdot";
} else {
print "<option value=\"$filename\">$curdot";
}

}

print "</select><br>";

print '
<input type="hidden" name="shape" value="Diamond">
<input type="hidden" name="color" value="Red">
<input type="hidden" name="size" value="3">
';

}

?>

<br>



<?php  echo t("Other Preferences:") ?><br>
<?php  echo t("Earth Image:") ?>&nbsp;
<select name="earthimage">

<?php

foreach($earthimages as $curentry) {

list($curfile, $curname, , ) = split(":", $curentry);

if($setearthimage == $curfile) {
print "<option value=\"$curfile\" selected>$curname";
} else {
print "<option value=\"$curfile\">$curname";
}

}


?>

</select>
<br><br>
<input type="Submit" name="button" value="<?php  echo t("Save") ?>">

<div align="right">
[ <a href="plot.php<?php  if(isset($HTTP_GET_VARS["lastquery"])) { echo "?address=$HTTP_GET_VARS[lastquery]"; } ?>"><?php  echo t("main") ?></a> ]<br><br>
</div>
<?php  include("footer.inc"); ?>
</body></html>
