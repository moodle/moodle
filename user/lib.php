<?PHP  // $Id$

/// FUNCTIONS ///////////////////////////////////////////////////////////

function ImageCopyBicubic ($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {

    if (function_exists("ImageCopyResampled")) {   // Assumes gd >= 2.0.1 as well
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


?>
