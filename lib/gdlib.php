<?PHP  // $Id$
       // Collection of routines in Moodle related to processing 
       // images using GD.


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

function save_profile_image($id, $filename, $dir="users") {
// Given a filename to a known image, this function scales and crops
// it and saves it in the right place to be a "user" or "group" image.

    global $CFG;

    if (empty($CFG->gdversion)) {
        return false;
    }

    umask(0000);

    if (!file_exists("$CFG->dataroot/$dir")) {
        if (! mkdir("$CFG->dataroot/$dir", $CFG->directorypermissions)) {
            return false;
        }
    }

    if (!file_exists("$CFG->dataroot/$dir/$id")) {
        if (! mkdir("$CFG->dataroot/$dir/$id", $CFG->directorypermissions)) {
            return false;
        }
    }
    
    $originalfile = "$CFG->dataroot/$dir/$id/original";

    if (!move_uploaded_file($filename, $originalfile)) {
        return false;
    }

    @chmod($originalfile, 0666);

    $imageinfo = GetImageSize($originalfile);
    
    if (empty($imageinfo)) {
        unlink($originalfile);
        return false;
    }
    
    $image->width  = $imageinfo[0];
    $image->height = $imageinfo[1];
    $image->type   = $imageinfo[2];

    switch ($image->type) {
        case 1: 
            if (function_exists("ImageCreateFromGIF")) {
                $im = ImageCreateFromGIF($originalfile); 
            } else {
                notice("GIF not supported on this server");
                unlink($originalfile);
                return false;
            }
            break;
        case 2: 
            if (function_exists("ImageCreateFromJPEG")) {
                $im = ImageCreateFromJPEG($originalfile); 
            } else {
                notice("JPEG not supported on this server");
                unlink($originalfile);
                return false;
            }
            break;
        case 3:
            if (function_exists("ImageCreateFromPNG")) {
                $im = ImageCreateFromPNG($originalfile); 
            } else {
                notice("PNG not supported on this server");
                unlink($originalfile);
                return false;
            }
            break;
        default: 
            unlink($originalfile);
            return false;
    }

    unlink($originalfile);

    if (function_exists("ImageCreateTrueColor") and $CFG->gdversion >= 2) {
        $im1 = ImageCreateTrueColor(100,100);
        $im2 = ImageCreateTrueColor(35,35);
    } else {
        $im1 = ImageCreate(100,100);
        $im2 = ImageCreate(35,35);
    }
    
    $cx = $image->width / 2;
    $cy = $image->height / 2;

    if ($image->width < $image->height) {
        $half = floor($image->width / 2.0);
    } else {
        $half = floor($image->height / 2.0);
    }

    ImageCopyBicubic($im1, $im, 0, 0, $cx-$half, $cy-$half, 100, 100, $half*2, $half*2);
    ImageCopyBicubic($im2, $im, 0, 0, $cx-$half, $cy-$half, 35, 35, $half*2, $half*2);

    // Draw borders over the top.
    $black1 = ImageColorAllocate ($im1, 0, 0, 0);
    $black2 = ImageColorAllocate ($im2, 0, 0, 0);
    ImageLine ($im1, 0, 0, 0, 99, $black1);
    ImageLine ($im1, 0, 99, 99, 99, $black1);
    ImageLine ($im1, 99, 99, 99, 0, $black1);
    ImageLine ($im1, 99, 0, 0, 0, $black1);
    ImageLine ($im2, 0, 0, 0, 34, $black2);
    ImageLine ($im2, 0, 34, 34, 34, $black2);
    ImageLine ($im2, 34, 34, 34, 0, $black2);
    ImageLine ($im2, 34, 0, 0, 0, $black2);

    if (ImageJpeg($im1, "$CFG->dataroot/$dir/$id/f1.jpg", 90) and 
        ImageJpeg($im2, "$CFG->dataroot/$dir/$id/f2.jpg", 95) ) {
        @chmod("$CFG->dataroot/$dir/$id/f1.jpg", 0666);
        @chmod("$CFG->dataroot/$dir/$id/f2.jpg", 0666);
        return 1;
    } else {
        return 0;
    }
}


?>
