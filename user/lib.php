<?PHP  // $Id$

/// FUNCTIONS ///////////////////////////////////////////////////////////

function ImageCopyBicubic ($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {

    global $CFG;

    if (function_exists("ImageCopyResampled") and $CFG->gdversion >= 2) { 
       return ImageCopyResampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y,
                                 $dst_w, $dst_h, $src_w, $src_h);
    }

    $totalcolors = imagecolorstotal($src_img);
    for ($i=0; $i<$totalcolors; $i++) { 
        if ($colors = ImageColorsForIndex($src_img, $i)) {
            ImageColorAllocate($dst_img, $colors['red'], $colors['green'], $colors['blue']);
        }
    }

    $scaleX = ($src_w - 1) / $dst_w; 
    $scaleY = ($src_h - 1) / $dst_h; 

    $scaleX2 = $scaleX / 2.0; 
    $scaleY2 = $scaleY / 2.0; 

    for ($j = 0; $j < $dst_h; $j++) { 
        $sY = $j * $scaleY; 

        for ($i = 0; $i < $dst_w; $i++) { 
            $sX = $i * $scaleX; 

            $c1 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX,(int)$sY+$scaleY2)); 
            $c2 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX,(int)$sY)); 
            $c3 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX+$scaleX2,(int)$sY+$scaleY2)); 
            $c4 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX+$scaleX2,(int)$sY)); 

            $red = (int) (($c1['red'] + $c2['red'] + $c3['red'] + $c4['red']) / 4); 
            $green = (int) (($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) / 4); 
            $blue = (int) (($c1['blue'] + $c2['blue'] + $c3['blue'] + $c4['blue']) / 4); 

            $color = ImageColorClosest ($dst_img, $red, $green, $blue); 
            ImageSetPixel ($dst_img, $i + $dst_x, $j + $dst_y, $color); 
        } 
    } 
}

function print_user($user, $course, $string) {

    global $USER, $COUNTRIES;
    
    echo "<TABLE WIDTH=80% ALIGN=CENTER BORDER=0 CELLPADDING=1 CELLSPACING=1><TR><TD BGCOLOR=#888888>";
    echo "<TABLE WIDTH=100% BORDER=0 CELLPADDING=0 CELLSPACING=0><TR>";
    echo "<TD WIDTH=100 BGCOLOR=#FFFFFF VALIGN=top>";
    echo "<A HREF=\"view.php?id=$user->id&course=$course->id\">";
    if ($user->picture) {
        echo "<IMG BORDER=0 ALIGN=left WIDTH=100 SRC=\"pix.php/$user->id/f1.jpg\">";
    } else {
        echo "<IMG BORDER=0 ALIGN=left WIDTH=100 SRC=\"default/f1.jpg\">";
    }
    echo "</A>";
    echo "</TD><TD WIDTH=100% BGCOLOR=#FFFFFF VALIGN=top>";
    echo "<FONT SIZE=-1>";
    echo "<FONT SIZE=3><B>$user->firstname $user->lastname</B></FONT>";
    echo "<P>";
    echo "$string->email: <A HREF=\"mailto:$user->email\">$user->email</A><BR>";
    echo "$string->location: $user->city, ".$COUNTRIES["$user->country"]."<BR>";
    echo "$string->lastaccess: ".userdate($user->lastaccess);
    echo "&nbsp (".format_time(time() - $user->lastaccess).")";
    echo "</TD><TD VALIGN=bottom BGCOLOR=#FFFFFF NOWRAP>";

    echo "<FONT SIZE=1>";
    if (isteacher($course->id)) {
        $timemidnight = usergetmidnight(time());
        echo "<A HREF=\"../course/user.php?id=$course->id&user=$user->id\">$string->activity</A><BR>";
        echo "<A HREF=\"../course/unenrol.php?id=$course->id&user=$user->id\">$string->unenrol</A><BR>";
        if (isstudent($course->id, $user->id)) {
            echo "<A HREF=\"../course/loginas.php?id=$course->id&user=$user->id\">$string->loginas</A><BR>";
        }
    } 
    echo "<A HREF=\"view.php?id=$user->id&course=$course->id\">$string->fullprofile...</A>";
    echo "</FONT>";

    echo "</TD></TR></TABLE></TD></TR></TABLE>";
}

?>
