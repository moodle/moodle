<?php

 # this is the "stupid diff", which shows up changes between two
 # saved versions of a WikiPage; even if working very unclean it
 # allows to see what has changed
 # it is accessible through the "info about page" action



 $ewiki_plugins["action"]["diff"] = "ewiki_page_stupid_diff";
 $ewiki_config["action_links"]["info"]["diff"] = "diff";



 function ewiki_page_stupid_diff($id, $data, $action) {
    global $wiki;

    if ($uu=$GLOBALS["ewiki_diff_versions"]) {
       list($new_ver, $old_ver) = $uu;
       $data = ewiki_database("GET", array("id" => $id, "version" => $new_ver));
    }
    else {
       $new_ver = $data["version"];
       $old_ver = $new_ver - 1;
    }
    if ($old_ver > 0) {
       $data0 = ewiki_database("GET", array("id" => $id, "version" => $old_ver));
    }
    
    $a->new_ver=$new_ver;
    $a->old_ver=$old_ver;
    $a->pagename=$id;
    $o = ewiki_make_title($id, get_string("differences","wiki",$a));

    # Different handling for html: closes Bug #1530 - Wiki diffs useless when using HTML editor    
    if($wiki->htmlmode==2) {
      $htmlendings=array("<br />","<br>","</p>","<hr />","<hr>","</li>","</tr>");
      $splitregexp="+\s*\n|\s*(".join("|",$htmlendings).")+";
      $content0=preg_replace("+(".join("|",$htmlendings).")+","\n",$data0["content"]);
      $content=preg_replace("+(".join("|",$htmlendings).")+","\n",$data["content"]);
    } else {
      $splitregexp="/\s*\n/";      
      $content0=$data0["content"];
      $content=$data["content"];
    }
    $txt0 = preg_split($splitregexp, trim($content0));
    $txt2 = preg_split($splitregexp, trim($content));

    /// Remove empty lines in html
    if($wiki->htmlmode==2) {
      $html0=$txt0;
      $html2=$txt2;
      $txt0=array();
      $txt2=array();
      
      for($i=0;$i<count($html0);$i++) {
        if(trim(strip_tags($html0[$i]))) { // There is something !
          $txt0[]=$html0[$i];
        }
      }
      for($i=0;$i<count($html2);$i++) {
        if(trim(strip_tags($html2[$i]))) { // There is something !
          $txt2[]=$html2[$i];
        }
      }  
    }
        
    $diff0 = array_diff($txt0, $txt2);
    $diff2 = array_diff($txt2, $txt0);

    foreach ($txt2 as $i => $line) {
       if($wiki->htmlmode != 2) {
         $line = htmlentities($line);
       }
       $i2 = $i;
       while ($rm = $diff0[$i2++]) {          
          if($wiki->htmlmode == 2) {
            $o .= "<br><b>-</b><font color=\"#990000\">$rm</font><br>\n";
          } else {
            $o .= "<b>-</b><font color=\"#990000\"><tt>$rm</tt></font><br>\n";
          }
          unset($diff0[$i2-1]);
       }

       if (in_array($line, $diff2)) {
          if($wiki->htmlmode == 2) {
            $o .= "<br><b>+</b><font color=\"#009900\">$line</font>\n";
          } else {
            $o .= "<b>+</b><font color=\"#009900\"><tt>$line</tt></font><br>\n";
          }
       }
       else {
          if($wiki->htmlmode == 2) {
            $o .= "$line\n";
          } else {
            $o .= "&nbsp; $line<br>\n";
          }
       }

    }

    foreach ($diff0 as $rm) {
       $o .= "<b>-</b><font color=\"#990000\"> <tt>$rm</tt></font><br>\n";
    }

    return($o);
 }
?>