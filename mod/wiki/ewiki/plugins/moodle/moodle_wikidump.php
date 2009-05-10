<?php // $Id$
# ToDo: Binary Content
#       Binary Linking
/*
   Allows to download a tarball including all WikiPages and images that
   currently are in the database.
*/


#-- text
$ewiki_t["en"]["WIKIEXPORTCOMMENT"] = "Here you can tailor your WikiDump to your needs.  When you are ready, click the \"Download\" button.";
$ewiki_t["en"]["DOWNLOAD_ARCHIVE"] = "Download";

#define("EWIKI_WIKIDUMP_ARCNAME", "WikiDump_");
#define("EWIKI_WIKIDUMP_DEFAULTTYPE", "TAR");
#define("EWIKI_WIKIDUMP_MAXLEVEL", 1);
define('EWIKI_DUMP_FILENAME_REGEX',"/\W\+/");

#-- glue
#if((function_exists(gzcompress) && EWIKI_WIKIDUMP_DEFAULTTYPE=="ZIP") || EWIKI_WIKIDUMP_DEFAULTTYPE=="TAR"){
  $ewiki_plugins["page"]["WikiExport"] = "moodle_ewiki_page_wiki_dump";
  #$ewiki_plugins["action"]['wikidump'] = "moodle_ewiki_page_wiki_dump";
#}

$ewiki_t["c"]["EWIKIDUMPCSS"] = '
  <style  TYPE="text/css">
  <!--
  body {
    background-color:#eeeeff;
    padding:2px;
  }    
  
  H2 {
    background:#000000;
    color:#ffffff;
    border:1px solid #000000;
  }
  -->
  </style>
  ';  
  

function moodle_ewiki_page_wiki_dump($id=0, $data=0, $action=0) {
  global $userid, $groupid, $cm, $wikipage, $wiki, $course, $CFG;
  #-- return legacy page
  $cont = true;
  if (!empty($_REQUEST["wikiexport"])) {
    $binaries=$_REQUEST["exportbinaries"];
    if(!$wiki->ewikiacceptbinary) {
      $binaries=0;
    }
    $exportformats=$_REQUEST["exportformats"];
    if($wiki->htmlmode==2) {
      $exportformats=1;
    }
    $cont=ewiki_page_wiki_dump_send($binaries, 
                                $exportformats, 
                                $_REQUEST["withvirtualpages"], 
                                $_REQUEST["exportdestinations"]);
  }  
  if($cont===false) {
     die;
  }
    
  $url = ewiki_script("", "WikiExport");
  $ret  = ewiki_make_title($id, ewiki_t($id), 2);
  $ret .= ($cont&&$cont!==true)?$cont."<br /><br />\n":"";
  $ret .= get_string("wikiexportcomment","wiki");
  // removing name="form" from the following form as it does not validate
  // and is not referenced. MDL-7861
  $ret .= "<br /><br />\n".
    '<FORM method="post" action="view.php">'."\n".
    "<div class=\"wikiexportbox\">\n".
    '<INPUT type="hidden" name="page" value="WikiExport" />'."\n".
    '<INPUT type="hidden" name="userid" value="'.$userid.'" />'."\n".
    '<INPUT type="hidden" name="groupid" value="'.$groupid.'" />'."\n".
    '<INPUT type="hidden" name="id" value="'.$cm->id.'" />'."\n".
    '<INPUT type="hidden" name="wikipage" value="'.$wikipage.'" />'."\n";
    
  
  // Export binaries too ?
  if(!$wiki->ewikiacceptbinary) {
    $ret.='<INPUT type="hidden" name="exportbinaries" value="0" />'.$exportdestinations[0]."\n";
  } else {
    $ret.='<INPUT type="hidden" name="exportbinaries" value="0" />'."\n";
  }
  $ret.="<TABLE cellpadding=\"5\">\n";
  if($wiki->ewikiacceptbinary) {
    $ret.="  <TR valign=\"top\">\n".
        '    <TD align="right">'.get_string("withbinaries","wiki").":</TD>\n".
        "    <TD>\n".
        '      <input type="checkbox" name="exportbinaries" value="1"'.($_REQUEST["exportbinaries"]==1?" checked":"")." />\n".
        "    </TD>\n".
        "  </TR>\n";
  }
  $ret.="  <TR valign=\"top\">\n".
      '    <TD align="right">'.get_string("withvirtualpages","wiki").":</TD>\n".
      "    <TD>\n".
      '      <input type="checkbox" name="withvirtualpages" value="1"'.($_REQUEST["withvirtualpages"]==1?" checked":"")." />\n".
      "    </TD>\n".
      "  </TR>\n";
  $exportformats=array( "0" => get_string("plaintext","wiki") , "1" => get_string("html","wiki"));
  /// Formats
  $ret.="  <TR valign=\"top\">\n".
        '    <TD align="right">'.get_string("exportformats","wiki").":</TD>\n".
        "    <TD>\n";
  if($wiki->htmlmode!=2) {
    $ret.= choose_from_menu($exportformats, "exportformats", $_REQUEST["exportformats"], "", "", "", true)."\n";
  } else {
    $ret.= '<INPUT type="hidden" name="exportformats" value="1" />'.
           get_string("html","wiki");
  }
  $ret.="    </TD>\n".
        "  </TR>\n";
  /// Destination
  $exportdestinations=array("0" => get_string("downloadaszip","wiki"));
  if(wiki_is_teacher($wiki)) {
    // Get Directory List
    $rawdirs = get_directory_list("$CFG->dataroot/$course->id", 'moddata', true, true, false);
    
    foreach ($rawdirs as $rawdir) {
      $exportdestinations[$rawdir] = get_string("moduledirectory","wiki").": ".$rawdir;
    }
  }
  
  $ret.="  <TR valign=\"top\">\n".
        '    <TD align="right">'.get_string("exportto","wiki").":</TD>\n".
        "    <TD>\n";
  if(count($exportdestinations)==1) {
    $ret.='<INPUT type="hidden" name="exportdestinations" value="0" />'.$exportdestinations[0]."\n";
  } else {
    $ret.=choose_from_menu($exportdestinations, "exportdestinations", $_REQUEST["exportdestinations"], "", "", "", true)."\n";
  }
  $ret.="    </TD>\n".
      "  </TR>\n".      
      "</TABLE>\n".
      '  <input type="submit" name="wikiexport" value= "'.get_string("export","wiki").'" />'."\n".
      "</div>\n";
      "</FORM>\n";
  return $ret;
}

function ewiki_page_wiki_dump_send($exportbinaries=0, $exportformats=0, $withvirtualpages=0, $exportdestinations=0) {
  global $ewiki_config, $wiki, $ewiki_plugins, $wiki_entry, $course, $CFG, $ewiki_t, $userid, $groupid;
  
  $filestozip=array();
  #-- disable protected email
  if (is_array($ewiki_plugins["link_url"])) {
      foreach($ewiki_plugins["link_url"] as $key => $linkplugin){
        if($linkplugin == "ewiki_email_protect_link"){
          unset($ewiki_plugins["link_url"][$key]);
        }
      }
  }
  
  /// HTML-Export
  if($exportformats==1) {
    #-- if exportformats is html
    $HTML_TEMPLATE = '<html>
      <head>'.$ewiki_t["c"]["EWIKIDUMPCSS"].'
      <title>$title</title>
      </head>
      <body bgcolor="#ffffff";>
      <div id="PageText">
      <h2>$title</h2>
      $content
      </div>
      </body>
      </html>';
  
    #-- reconfigure ewiki_format() to generate offline pages and files
    $html_ext = ".html";
    $ewiki_config["script"] = "%s$html_ext";
    $ewiki_config["script_binary"] = "%s";
  }
  
  // Export Virtual pages special
  $a_virtual = array_keys($ewiki_plugins["page"]);

  #-- get all pages / binary files
  $a_validpages = ewiki_valid_pages(1, $withvirtualpages);
  $a_pagelist = ewiki_sitemap_create($wiki_entry->pagename, $a_validpages, 100, 1);
  
  # Add linked binary files to pagelist
  foreach($a_pagelist as $key => $value) {
    if(is_array($a_validpages[$value]["refs"])){
      foreach($a_validpages[$value]["refs"] as $refs){
        if($a_validpages[$refs]["type"]=="image" || $a_validpages[$refs]["type"]=="file"){
          $a_pagelist[]=$refs;
        }
      }
    }
  }

  # Adjust links to binary files
  foreach($a_pagelist as $key => $value){
    if($a_validpages[$value]["type"]=="image"){
      $a_images[]=urlencode($value);
      $a_rimages[]=urlencode(preg_replace(EWIKI_DUMP_FILENAME_REGEX, "", $value));
      unset($a_validpages[$value]);
    } 
    if($a_validpages[$value]["type"]=="file") {
      $a_images[]=urlencode($value);
      $a_rimages[]=clean_filename(substr($value,strlen(EWIKI_IDF_INTERNAL)));
      $a_images[]=$value;
      $a_rimages[]=clean_filename(substr($value,strlen(EWIKI_IDF_INTERNAL)));
      unset($a_validpages[$value]);
    }
  }

  # Remove binaries from a_validpages and add to a_pagelist  
  foreach($a_validpages as $key => $value){
    if($a_validpages[$key]["type"]=="image" || $a_validpages[$key]["type"]=="file"){
      $a_pagelist[]=$key;
      unset($a_validpages[$key]);
    }    
  }  
  
  #print "<pre>"; print_r($a_validpages); print "</pre>";
  #print "<hr /><pre>"; print_r($a_pagelist); print "</pre>";

  $a_sitemap = ewiki_sitemap_create($wiki_entry->pagename, $a_validpages, 99, 0);
  if ($a_pagelist) {
    #-- create new zip file
    #if($arctype == "ZIP"){
    #  $archivename=EWIKI_WIKIDUMP_ARCNAME."$rootid.zip";
    #  $archive = new ewiki_virtual_zip();
    #} elseif ($arctype == "TAR") {
    #  $archivename=EWIKI_WIKIDUMP_ARCNAME."$rootid.tar";
    #  $archive = new ewiki_virtual_tarball();
    #} else {
    #  die();
    #}
    
    /// Create/Set Directory
    $wname=clean_filename(strip_tags(format_string($wiki->name,true)));
    if($exportdestinations) {
      if(wiki_is_teacher($wiki)) {
        $exportdir=$CFG->dataroot."/".$course->id."/".$exportdestinations;
      } else {
        add_to_log($course->id, "wiki", "hack", "", format_string($wiki->name,true).": Tried to export a wiki as non-teacher into $exportdestinations.");
        error("You are not a teacher !");
      }
    } else {
        $exportbasedir=tempnam("/tmp","WIKIEXPORT");
        @unlink($exportbasedir);
        @mkdir($exportbasedir);
        /// maybe we need to check the name here...?
        $exportdir=$exportbasedir."/".$wname;
        @mkdir($exportdir);
        if(!is_dir($exportdir)) {
            error("Cannot create temporary directory $exportdir !");
        }
    }
    
    $a_pagelist = array_unique($a_pagelist);
    
    
    #-- convert all pages
    foreach($a_pagelist as $pagename){      
      if ((!in_array($pagename, $a_virtual))) {
        $id = $pagename;
        #-- not a virtual page
        $row = ewiki_database("GET", array("id"=>$pagename));
        $content = "";
      } elseif($withvirtualpages) {
        $id = $pagename;
        #-- is a virtual page
        $pf = $ewiki_plugins["page"][$id];
        $content = $pf($id, $content, "view");
        if ($exportformats==1) {
          $content = str_replace('$content', $content, str_replace('$title', $id, $HTML_TEMPLATE));
        }
        $fn = urlencode($id);
        $fn = preg_replace(EWIKI_DUMP_FILENAME_REGEX, "", $fn);
        $fn = $fn.$html_ext;
      } else {
        continue;
      }
      
      if (empty($content)){
        switch ($row["flags"] & EWIKI_DB_F_TYPE) {
          // Text Page
          case (EWIKI_DB_F_TEXT):
            #print "<pre>"; print_r($row[content]); print "\n-------------</pre>";
            
            if($exportformats==1) {/// HTML-Export
              $content = ewiki_format($row["content"]);
            } else {
              $content = $row["content"];
            }
            
            # Binary files link adjustment when html
            if($exportformats==1) {
              $content = str_replace($a_images, $a_rimages, $content);
            }
            
            $fn = preg_replace(EWIKI_DUMP_FILENAME_REGEX, "",  urlencode($id));
            $fn = $fn.$html_ext;
            if($exportformats==1) {/// HTML-Export
              $content =  str_replace('$content', $content, str_replace('$title', $id, $HTML_TEMPLATE));
            }
            break;
          case (EWIKI_DB_F_BINARY):            
            #print "Binary: $row[id]<br />";
            if (($row["meta"]["class"]=="image" || $row["meta"]["class"]=="file") && ($exportbinaries)) {
              # Copy files to the appropriate directory              
              $fn= moodle_binary_get_path($id, $row["meta"], $course, $wiki, $userid, $groupid);
              $destfn=clean_filename(substr($id,strlen(EWIKI_IDF_INTERNAL)));
              $dest="$exportdir/".$destfn;
              if(!copy($fn,$dest)) {
                notify("Cannot copy $fn to $dest.");
              }
                          
              #$fn = urlencode(preg_replace(EWIKI_DUMP_FILENAME_REGEX, "", $id));
              #$content = &$row["content"];
              $filestozip[]=$exportdir."/".$destfn;
              continue (2);
            }
            else {
              #-- php considers switch statements as loops so continue 2 is needed to 
              #-- hit the end of the for loop 
              continue(2);
            }
            break;
          
          default:
            # don't want it
            continue(2);
        }
      }
  
      # Do not translate links when wiki already in pure html - mode
      if($wiki->htmlmode!=2) {
          $content=preg_replace_callback(
            '/(<a href=")(.*?)(\.html">)/',
            create_function(
            // single quotes are essential here,
            // or alternative escape all $ as \$
            '$matches',
            'return($matches[1].preg_replace(EWIKI_DUMP_FILENAME_REGEX,"",$matches[2]).$matches[3]);'
            ),
            $content
            );
      }
      #-- add file
      // Let's make sure the file exists and is writable first.
      if (!$handle = fopen($exportdir."/".$fn, 'w')) {
        error("Cannot open file ($exportdir/$fn)");
      }
      
      // Write $content to our opened file.
      if (fwrite($handle, $content) === FALSE) {
        error("Cannot write to file ($exportdir/$fn)");
      }

      fclose($handle);
      $filestozip[]=$exportdir."/".$fn;
      #$archive->add($content, $fn, array(
      #  "mtime" => $row["lastmodified"],
      #  "uname" => "ewiki",
      #  "mode" => 0664 | (($row["flags"]&EWIKI_DB_F_WRITEABLE)?0002:0000),
      #  ), $complevel);
    }
    
    #-- create index page
    /// HTML-Export
    if($exportformats==1) {
        $timer=array();
        $level=-1;
        $fordump=1;
        $str_formatted="<ul>\n<li><a href=\"".($wiki_entry->pagename).$html_ext."\">".($wiki_entry->pagename)."</a></li>";
        $fin_level=format_sitemap($a_sitemap, ($wiki_entry->pagename), $str_formatted, $level, $timer, $fordump);
        $str_formatted.="</ul>".str_pad("", $fin_level*6, "</ul>\n");
        $str_formatted=preg_replace_callback(
            '/(<a href=")(.*?)(\.html">)/',
            create_function(
              // single quotes are essential here,
              // or alternative escape all $ as \$
              '$matches',
              'return($matches[1].preg_replace(EWIKI_DUMP_FILENAME_REGEX,"",$matches[2]).$matches[3]);'
            ),
            $str_formatted
          );
        $str_formatted = str_replace('$content', $str_formatted, str_replace('$title', get_string("index","wiki"), $HTML_TEMPLATE));
        #-- add file
        // Let's make sure the file exists and is writable first.
        $indexname="index".$html_ext;
        if (!$handle = fopen($exportdir."/".$indexname, 'w')) {
          error("Cannot open file ($exportdir/$indexname)");
        }
        
        // Write $somecontent to our opened file.
        if (fwrite($handle, $str_formatted) === FALSE) {
          error("Cannot write to file ($exportdir/$indexname)");
        }
  
        fclose($handle);
        $filestozip[]=$exportdir."/".$indexname;
  
    #-- add index page
#    $archive->add($str_formatted, "Index_$rootid".$html_ext, array(
#      "mtime" => $row["lastmodified"],
#      "uname" => "ewiki",
#      "mode" => 0664 | (($row["flags"]&EWIKI_DB_F_WRITEABLE)?0002:0000),
#      ), $complevel);
    }     
        
    if(!$exportdestinations) {
      $archivename=$wname.".zip";
      zip_files($filestozip, "$exportbasedir/$archivename");

      #-- Headers
      Header("Content-type: application/zip");
      Header("Content-disposition: attachment; filename=\"$archivename\"");
      Header("Cache-control: private");
      Header("Original-Filename: $archivename");    
      Header("X-Content-Type: application/zip");
      Header("Content-Location: $archivename");      
      if(!@readfile("$exportbasedir/$archivename")) {
        error("Cannot read $exportbasedir/$archivename");
      }
      if(!deldir($exportbasedir)) {
        error("Cannot delete $exportbasedir");
      }
      #exit();
      return false;
    } else {
       return get_string("exportsuccessful","wiki")."<br />";      
    }
  }
}

function deldir($dir)
{
  $handle = opendir($dir);
  while (false!==($FolderOrFile = readdir($handle)))
  {
     if($FolderOrFile != "." && $FolderOrFile != "..")
     { 
       if(is_dir("$dir/$FolderOrFile"))
       { deldir("$dir/$FolderOrFile"); }  // recursive
       else
       { unlink("$dir/$FolderOrFile"); }
     } 
  }
  closedir($handle);
  if(rmdir($dir))
  { $success = true; }
  return $success; 
}
?>
