<?php

include("plotconf.inc");
include("plot.inc");

$form_vars = ${"HTTP_".$HTTP_SERVER_VARS["REQUEST_METHOD"]."_VARS"};
$lat = $form_vars['lat']; 
$lon = $form_vars['lon']; 

if(isset($HTTP_COOKIE_VARS["atlasprefs"]) && validcookie($HTTP_COOKIE_VARS["atlasprefs"])) {
     list($setshape, $setcolor, $setsize, $earthimage, , ) = split(":", $HTTP_COOKIE_VARS["atlasprefs"]);
} else {
     $setshape = "Diamond";
     $setcolor = "red";
     $setsize = "3";
     $earthimage = $earthimages[$defaultimage];
     list($earthimage, , , ) = split(":", $earthimage);
}

$earthimage = isvalidimage($earthimage, $earthimages, $defaultimage);

if(strstr($earthimage, ":")) {
    list($earthimage, , , ) = split(":", $earthimage);
}

list($width, $height) = getimagecoords($earthimages, $earthimage);

if($setsize > 8 || $setsize < 2) {
    $setsize = 2;
}

$im = @ImageCreate ($width, $height)
    or die ("Cannot Initialize new GD image stream");
$background_color = ImageColorAllocate ($im, 255, 255, 255);


// color table
if($setcolor == "red") { $r = "255"; $g = "0"; $b = "0"; }
elseif($setcolor == "white") { $r = "255"; $g = "255"; $b = "254"; }
elseif($setcolor == "yellow") { $r = "255"; $g = "255"; $b = "0"; }
elseif($setcolor == "magenta") { $r = "255"; $g = "0"; $b = "255"; }
elseif($setcolor == "cyan") { $r = "0"; $g = "255"; $b = "255"; }
elseif($setcolor == "green") { $r = "0"; $g = "225"; $b = "0"; }
elseif($setcolor == "violet") { $r = "191"; $g = "0"; $b = "255"; }
else { $r = "255"; $g = "0"; $b = "0"; }

$loc_color = ImageColorAllocate ($im, $r, $g, $b);

if($lat == "" && $lon == "") { } else {

list($x, $y) = getlocationcoords($lat, $lon, $width, $height);

if($setshape == "Diamond") {
    ImageLine($im, ($x - $setsize), $y, $x, ($y + $setsize), $loc_color);
    ImageLine($im, ($x - $setsize), $y, $x, ($y - $setsize), $loc_color);
    ImageLine($im, ($x + $setsize), $y, $x, ($y + $setsize), $loc_color);
    ImageLine($im, ($x + $setsize), $y, $x, ($y - $setsize), $loc_color);
    ImageFill($im, $x, $y, $loc_color);
} elseif($setshape == "Diamond Outline") {
    ImageLine($im, ($x - $setsize), $y, $x, ($y + $setsize), $loc_color);
    ImageLine($im, ($x - $setsize), $y, $x, ($y - $setsize), $loc_color);
    ImageLine($im, ($x + $setsize), $y, $x, ($y + $setsize), $loc_color);
    ImageLine($im, ($x + $setsize), $y, $x, ($y - $setsize), $loc_color);
} elseif($setshape == "Square") {
    ImageFilledRectangle($im, ($x - $setsize), ($y - $setsize), ($x + $setsize), ($y + $setsize), $loc_color);
} elseif($setshape == "Square Outline") {
    ImageRectangle($im, ($x - $setsize), ($y - $setsize), ($x + $setsize), ($y + $setsize), $loc_color);
} elseif($setshape == "Cross") {
    ImageLine($im, ($x - $setsize), $y, ($x + $setsize), $y, $loc_color);
    ImageLine($im, $x, ($y - $setsize), $x, ($y + $setsize), $loc_color);
} else {
// draw a diamond if error
    ImageLine($im, ($x - $setsize), $y, $x, ($y + $setsize), $loc_color);
    ImageLine($im, ($x - $setsize), $y, $x, ($y - $setsize), $loc_color);
    ImageLine($im, ($x + $setsize), $y, $x, ($y + $setsize), $loc_color);
    ImageLine($im, ($x + $setsize), $y, $x, ($y - $setsize), $loc_color);
    ImageFill($im, $x, $y, $loc_color);
}

}


ImageColorTransparent ($im, $background_color);
Header("Pragma: no-cache");
Header("Expires: Thu, 26-Oct-1972 12:00:00");
Header("Content-type: image/png");
ImagePng ($im);
ImageDestroy($im);


?>
