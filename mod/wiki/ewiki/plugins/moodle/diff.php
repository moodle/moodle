<?php // $Id$

 # this is the "stupid diff", which shows up changes between two
 # saved versions of a WikiPage; even if working very unclean it
 # allows to see what has changed
 # it is accessible through the "info about page" action



 $ewiki_plugins["action"]["diff"] = "ewiki_page_stupid_diff";
 $ewiki_config["action_links"]["info"]["diff"] = "diff";



 function ewiki_page_stupid_diff($id, $data, $action) {
    global $wiki, $moodle_format;

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
        /// first do the formatiing to get normal display format without filters
        $options = new object();
        $options->smiley = false;
        $options->filter = false;
        $content0 = format_text($data0['content'], $moodle_format, $options);
        $content = format_text($data['content'], $moodle_format, $options);

        /// Remove all new line characters. They will be placed at HTML line breaks.
        $content0 = preg_replace('/\n|\r/i', ' ', $content0);
        $content0 = preg_replace('/(\S)\s+(\S)/', '$1 $2', $content0); // Remove multiple spaces.
        $content = preg_replace('/\n|\r/i', ' ', $content);
        $content = preg_replace('/(\S)\s+(\S)/', '$1 $2', $content);
        
        /// Replace <p>&nbsp;</p>
        $content0 = preg_replace('#(<p( [^>]*)?>(&nbsp;|\s+)</p>)|(<p( [^>]*)?></p>)#i', "\n", $content0);
        $content = preg_replace('#(<p( [^>]*)?>(&nbsp;|\s+)</p>)|(<p( [^>]*)?></p>)#i', "\n", $content);
        

        /// Place new line characters at logical HTML positions.
        $htmlendings = array('+(<br.*?>)+iU', '+(<p( [^>]*)?>)+iU', '+(</p>)+i', '+(<hr.*?>)+iU', '+(<ol.*?>)+iU',
                             '+(</ol>)+i', '+(<ul.*?>)+iU', '+(</ul>)+i', '+(<li.*?>)+iU', '+(</li>)+i', 
                             '+(</tr>)+i', '+(<div.*?>)+iU', '+(</div>)+i');
        $htmlrepl = array("\n\$1\n", "\n\$1\n", "\n\$1\n", "\n\$1\n", "\n\$1\n",
                          "\n\$1\n", "\n\$1\n", "\n\$1\n", "\n\$1\n", "\n\$1\n",
                          "\n\$1\n", "\n\$1\n", "\n\$1\n");
        $content0 = preg_replace($htmlendings, $htmlrepl, $content0);
        $content = preg_replace($htmlendings, $htmlrepl, $content);
    } else {
      $content0=$data0["content"];
      $content=$data["content"];
    }
    $txt0 = preg_split("+\s*\n+", trim($content0));
    $txt2 = preg_split("+\s*\n+", trim($content));

    $diff0 = array_diff($txt0, $txt2);
    $diff2 = array_diff($txt2, $txt0);

    foreach ($txt2 as $i => $line) {
       $i2 = $i;
       while ($rm = $diff0[$i2++]) {          
          if($wiki->htmlmode == 2) {
            if ($rm == '<br />') { //ugly hack to fix line breaks
                $rm = '';
            }
            $o .= "<b>-</b><font color=\"#990000\">".format_text($rm, $moodle_format, $options)."</font><br />\n";
          } else {
            $o .= "<b>-</b><font color=\"#990000\"><tt>".s($rm)."</tt></font><br />\n";
          }
          unset($diff0[$i2-1]);
       }

       if (in_array($line, $diff2)) {
          if($wiki->htmlmode == 2) {
            if ($line == '<br />') { //ugly hack to fix line breaks
                $line = '';
            }
            $o .= "<b>+</b><font color=\"#009900\">".format_text($line, $moodle_format, $options)."</font><br />\n";
          } else {
            $o .= "<b>+</b><font color=\"#009900\"><tt>".s($line)."</tt></font><br />\n";
          }
       }
       else {
          if($wiki->htmlmode == 2) {
            $o .= format_text($line, $moodle_format, $options)."\n";
          } else {
            $o .= "&nbsp; ".s($line)."<br />\n";
          }
       }

    }

    foreach ($diff0 as $rm) {
       $o .= "<b>-</b><font color=\"#990000\"> <tt>".s($rm)."</tt></font><br />\n";
    }

    return($o);
 }
?>
