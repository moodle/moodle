<?php

#  if someone uploads an image, which is larger than the allowed
#  image size (EWIKI_IMAGE_MAXSIZE), then this plugin tries to
#  rescale that image until it fits; it utilizes the PHP libgd
#  functions to accomplish this

#  NOTE: It is currently disabled for Win32, because nobody knows, if
#  this will crash the PHP interpreter on those systems.


define("EWIKI_IMGRESIZE_WIN", 0);


if (!strstr(PHP_VERSION, "-dev") && !function_exists("imagecreate") && function_exists("dl")) {   #-- try to load gd lib
   @dl("php_gd2.dll") or @dl("gd.so");
}
if (function_exists("imagecreate")) {
   $ewiki_plugins["image_resize"][] = "ewiki_binary_resize_image_gd";
}


function ewiki_binary_resize_image_gd(&$filename, &$mime, $return=0) {

           /*** this disallows Win32 ***/
   if (    (DIRECTORY_SEPARATOR!="/") && !EWIKI_IMAGERESIZE_WIN
       || (strpos($mime, "image/")!==0) )
   { 
      return(false);
   }

   $tmp_rescale = $filename;

   #-- initial rescale
   $r = EWIKI_IMAGE_MAXSIZE / filesize($tmp_rescale);
   $r = ($r) + ($r - 1) * ($r - 1);

   #-- read orig image
   strtok($mime, "/");
   $type = strtok("/");
   if (function_exists($pf = "imagecreatefrom$type")) {
      $orig_image = $pf($filename);
   }
   else {
      return(false);
   }
   $orig_x = imagesx($orig_image);
   $orig_y = imagesy($orig_image);

   #-- change mime from .gif to .png
   if (($type == "gif") && (false || function_exists("imagepng") && !function_exists("imagegif"))) {
      $type = "png";
   }

   #-- retry resizing
   $loop = 20;
   while (($loop--) && (filesize($tmp_rescale) > EWIKI_IMAGE_MAXSIZE)) {

      if ($filename == $tmp_rescale) {
         $tmp_rescale = tempnam(EWIKI_TMP, "ewiki.img_resize_gd.tmp.");
      }

      #-- sizes
      $new_x = (int) ($orig_x * $r);
      $new_y = (int) ($orig_y * $r);

      #-- new gd image
      $tc = function_exists("imageistruecolor") && imageistruecolor($orig_image);
      if (!$tc || ($type == "gif")) {
         $new_image = imagecreate($new_x, $new_y);
         imagepalettecopy($new_image, $orig_image);
      }
      else {
         $new_image = imagecreatetruecolor($new_x, $new_y);
      }

      #-- resize action
      imagecopyresized($new_image, $orig_image, 0,0, 0,0, $new_x,$new_y, $orig_x,$orig_y);

      #-- special things
      if ( ($type == "png") && function_exists("imagesavealpha") ) {
         imagesavealpha($new_image, 1);
      }

      #-- save
      if (function_exists($pf = "image$type")) {
         $pf($new_image, $tmp_rescale);
      }
      else {
         return(false);   # cannot save in orig format (.gif)
      }

      #-- prepare next run
      imagedestroy($new_image);
      clearstatcache();
      $r *= 0.95;
   }

   #-- stop
   imagedestroy($orig_image);

   #-- security check filesizes, abort
   if (!filesize($filename) || !filesize($tmp_rescale) || (filesize($tmp_rescale) > EWIKI_IMAGE_MAXSIZE)) {
      unlink($tmp_rescale);
      return($false);
   }

   #-- set $mime, as it may have changed (.gif)
   $mime = strtok($mime, "/") . "/" . $type;
   if (!strstr($filename, ".$type")) {
      unlink($filename);
      $filename .= ".$type";
   }

   #-- move tmp file to old name
   copy($tmp_rescale, $filename);
   unlink($tmp_rescale);
   return(true);

}

?>