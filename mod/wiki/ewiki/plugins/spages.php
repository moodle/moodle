<?php

/*
   The StaticPages plugin allows you to put some .html or .php files
   into dedicated directories, which then will get available with their
   basename as ewiki pages. The files can be in wiki format (.txt or no
   extension), they can also be in .html format and they may even contain
   php code (.php).

   Of course it is not possible to provide anything else, than viewing
   those pages (editing is not possible), but it is of course up to you
   to add php code to achieve some interactivity.
   The idea for this plugin was 'borought' from http://geeklog.org/.

   In your static page .php files you cannot do everything you could
   normally do, there are some restrictions because of the way these static
   pages are processed. You need to use $GLOBALS to access variables other
   than the $ewiki_ ones. To return headers() you must append them to the
   $headers[] or $ewiki_headers[] array.

   If you define("EWIKI_SPAGES_DIR") then this directory will be read
   initially, but you could also just edit the following list/array of 
   directories, or call ewiki_init_spages() yourself.
*/


#-- specify which dirs to search for page files
ewiki_init_spages(
   array(
      "spages",
      # "/usr/local/share/wikipages",
      # "C:/Documents/StaticPages/",
   )
);
if (defined("EWIKI_SPAGES_DIR")) {
   ewiki_init_spages(EWIKI_SPAGES_DIR);
}


#-- plugin glue
# - will be added automatically by _init_spages()


#-- return page
function ewiki_spage($id, $data, $action) {

   global $ewiki_spages, $ewiki_plugins;

   $r = "";

   #-- filename from $id
   $fn = $ewiki_spages[strtolower($id)];

   #-- php file
   if (strpos($fn, ".php") || strpos($fn, ".htm")) {

      #-- start new ob level
      ob_start();
      ob_implicit_flush(0);

      #-- prepare environment
      global $ewiki_id, $ewiki_title, $ewiki_author, $ewiki_ring,
             $ewiki_t, $ewiki_config, $ewiki_action, $_EWIKI,
             $ewiki_auth_user, $ewiki_headers, $headers;
      $ewiki_headers = array();
      $headers = &$ewiki_headers;

      #-- execute script
      include($fn);

      #-- close ob
      $r = ob_get_contents();
      ob_end_clean();

      #-- add headers
      if ($ewiki_headers) {
         headers(implode("\n", $ewiki_headers));
      }
   }

   #-- wiki file
   else {

      $f = fopen($fn, "rb");
      $r = fread($f, 256<<10);
      fclose($f);

      $r = $ewiki_plugins["render"][0]($r);
   }

   #-- strip <html> and <head> parts (if any)
   if (($l = strpos(strtolower($r), "<body")) &&
       ($w = strpos(strtolower($r), "</body"))  )
   {
      $l = strpos($r, ">", $l+1) + 1;
      $w = $w - $l;
      $r = substr($r, $l, $w);
   }

   #-- return body (means successful handled)
   return($r);

}



#-- return page
#<old># $ewiki_plugins["handler"][] = "ewiki_handler_spages";
function ewiki_handler_spages($id, $data, $action) {

   global $ewiki_spages;

   #-- compare requested page $id with spages` $id values
   $i0 = strtolower($id);
   foreach ($ewiki_spages as $i1 => $fn) {
      if (strtolower($i1)==$i0) {

         return(ewiki_spage($id));

      }
   }

}


#-- init
function ewiki_init_spages($dirs, $idprep="") {

   global $ewiki_spages, $ewiki_plugins;

   if (!is_array($dirs)) {
      $dirs = array($dirs);
   }

   #-- go through list of directories
   foreach ($dirs as $dir) {

      if (empty($dir)) {
         continue;
      }

      #-- read in one directory
      $dh = opendir($dir);
      while (false !== ($fn = readdir($dh))) {

         #-- skip over . and ..
         if ($fn[0] == ".") { continue; }

         #-- be recursive
         if ($fn && is_dir("$dir/$fn")) {
            if ($fn != trim($fn, ".")) {
               $fnadd = trim($fn, ".") . ".";
            }
            else {
               $fnadd = "$fn/";
            }

            ewiki_init_spages(array("$dir/$fn"), "$idprep$fnadd");

            continue;
         }

         #-- strip filename extensions
         $id = str_replace(
                  array(".html", ".htm", ".php", ".txt", ".wiki", ".src"),
                  "",
                  basename($fn)
         );

         #-- register spage file and as page plugin (one for every spage)
         $ewiki_spages[strtolower("$idprep$id")] = "$dir/$fn";
         $ewiki_plugins["page"]["$idprep$id"] = "ewiki_spage";

      }
      closedir($dh);
   }

}



?>