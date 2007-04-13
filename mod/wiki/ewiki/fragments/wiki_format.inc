<?php

/* 
 * Wiki-Engine from "ErfurtWiki", Mario Salzer <milky@erphesfurt.de>
 * Adapted by Frank Luithle <sigi@fsinfo.cs.uni-sb.de>
 *
 * WikiLinks and binary already stripped off -> just the rendering core
 */

// URL prefixes
$ewiki_idf_url = array( "http://",
                        "mailto:",
                        "ftp://",
                        "irc://",
                        "telnet://",
                        "news://",
                        "internal://",
                        "chrome://",
                        "file://" );

// allowed wikinames
//define( "EWIKI_CHARS_L", "a-z����_��$" );
//define( "EWIKI_CHARS_U", "A-Z���" );

function wiki_format( $wiki_source,
                      $strip_slashes = true,
                      $safe_html_allowed = true,
                      $table_html_allowed = false ) {

  if ( $strip_slashes ) {
    $wiki_source = stripslashes( $wiki_source );
  }

  // formatted output
  $o = "<p>\n";

  // state vars
  $li_o = "";
  $tbl_o = 0;
  $post = "";

  $wm_whole_line = array( "!!!" => "h2",
                          "!!"  => "h3",
                          "!"   => "h4",
                          " "   => "tt",
                          ";:"  => 'div style="left-margin:10pt;"' );

  $table_defaults = 'cellpadding="2" border="1" cellspacing="0"';

  // these tags will be preserved if the $safe_html_allowed argument
  // is set to 'true'
  $rescue_html = array( "tt", "b", "i", "strong", "em", "s", "kbd", "var",
                        "xmp", "sup", "sub", "pre", "q", "h2", "h3", "h4",
                        "h5", "h6", "cite", "code", "u" );

  $syn_htmlentities = array( "&" => "&amp;",
                             ">" => "&gt;",
                             "<" => "&lt;",
                             "%%%" => "<br/>" );

  $wm_list = array( "-" => array('ul type="square"', "", "li"),
                    "*" => array('ul type="circle"', "", "li"),
                    "#" => array("ol", "", "li"),
                    ":" => array("dl", "dt", "dd") );

  $wm_text_style = array( "'''" => array("''__", "__''"),
                          "___" => array("''__", "__''"),
                          "''" => array("<em>", "</em>"),
                          "__" => array("<strong>", "</strong>"),
                          // "^^" => array("<sup>", "</sup>"),
                          // "***" => array("<b><i>", "</i></b>"),
                          // "###" => array("<big><b>", "</b></big>"),
                          "**" => array("<b>", "</b>"),
                          "##" => array("<big>", "</big>"),
                          "��" => array("<small>", "</small>") );

  $link_regex = "#(!?\[[^[\]\n]+\])|((?:!?[a-z]{2,6}://|mailto:)[^\s\[\]\'\"\)\,<]+)#";

  #$link_regex = "#(!?\[[^[\]\n]+\])|((?:!?[".EWIKI_CHARS_U."]+[".EWIKI_CHARS_L.
  #  ":]+){2}[\w\d]*)|((?:!?[a-z]{2,6}://|mailto:)[^\s\[\]\'\"\)\,<]+)#";

  // eliminate html
  foreach ( $syn_htmlentities as $find => $replace ) {
    $wiki_source = str_replace( $find, $replace, $wiki_source );
  }
  array_pop( $syn_htmlentities );

  // unescape allowed html
  if ( $safe_html_allowed ) {
    foreach ( $rescue_html as $tag ) {
      foreach( array( $tag, "/$tag", ( $tag = strtoupper($tag) ), "/$tag" )
               as $tag ) {
        $wiki_source = str_replace( '&lt;' . $tag . '&gt;',
                                    "<" . $tag . ">",
                                    $wiki_source );
      }
    }
  }

  $wiki_source = trim( $wiki_source ) . "\n";

  foreach ( explode( "\n", $wiki_source ) as $line ) {
    $line = rtrim( $line );
    $post = "";

    // paragraphs
    if ( empty($line) ) {
      $post .= "</p>\n\n<p>";
    } elseif ( strpos( $line, "----" ) === 0 ) {
      $o .= "<hr>\n";
      continue;
    } elseif ( strpos( $line, "&lt;!--" ) === 0 ) {
      $o .= "<!-- " . s( str_replace( "--", "__", substr($line, 7) ) ) . " -->\n";
      continue;
    }

    // unescape html markup || tables wiki markup
    if ( strlen( $line ) && ( $line[0] == "|" ) ) {
      if ( strlen( $line ) >
           strlen( trim( $line, "|" ) ) + 1 ) {
        $line = substr( $line, 1, strlen( $line ) - 2 );
        if ( !$tbl_o ) {
          $o .= "<table " . $table_defaults . ">\n";
        }
        $line = "<tr>\n<td>" . str_replace("|", "</td>\n<td>", $line) . "</td>\n</tr>";
        $tbl_o = 1;
      } elseif ( $table_html_allowed ) {
        $line = ltrim( substr( $line, 1 ) );
        foreach ( array_flip( $syn_htmlentities ) as $find => $replace ) {
          $line = str_replace( $find, $replace, $line );
        }
      }
    } elseif ($tbl_o) {
      $o .= "</table>\n";
      $tbl_o = 0;
    }

    // whole-line wikimarkup
    foreach ( $wm_whole_line as $find => $replace ) {
      if ( substr( $line, 0, strlen($find) ) == $find ) {
        $line = ltrim( substr( $line, strlen($find) ) );
        $o .= "<$replace>";
        $post = "</" . strtok( $replace, " " ) . ">" . $post;
      }
    }

    // wiki list markup
    if ( strlen( $li_o ) ||
         strlen( $line ) && isset( $wm_list[@$line[0]] ) ) {
      $n = 0;
      $li = "";
      // count differences to previous list wikimarkup
      while ( strlen( $line ) && ( $li0 = $line[0] ) && isset( $wm_list[$li0] ) ) {
        $li .= $li0;
        $n++;
        $line = substr($line, 1);
      }
      $line = ltrim($line);

      // fetch list definition
      if ( strlen( $li ) && ( $last_list_i = $li[strlen($li)-1] ) )
        list( $list_tag, $list_dt0, $list_entry_tag ) = $wm_list[$last_list_i];

      // output <ul> until new list wikimarkup rule matched
      while ( strlen($li_o) < strlen($li) ) {
        $add = $li[ strlen($li_o) ];
        $o .= "<" . $wm_list[ $add ][ 0 ] . ">\n";
        $li_o .= $add;
      }

      // close </ul> lists until "$li_o" == "$li" (list wikimarkup state var)
      while ( strlen($li_o) > strlen($li) ) {
        $del = $li_o[ strlen($li_o) - 1 ];
        $o .= "</" . strtok( $wm_list[$del][0], " " ) . ">\n";
        $li_o = substr( $li_o, 0, strlen($li_o) - 1 );
      }

      // more work for <dl> lists
      if ( !empty($list_dt0) ) {
        list( $line_dt, $line ) = explode( $last_list_i, $line, 2 );
        $o .= "<$list_dt0>$line_dt</$list_dt0>";
        $list_dt0 = $last_list_i = false;
      }

      // finally enclose current line in <li>...</li>
      if ( !empty($line) ) {
        $o .=  "<$list_entry_tag>";
        $post = "</$list_entry_tag>" . $post;
      }

      $li_o = $li;
    }

    // link-regex here??
    // (was formerly, may be faster if applied to the whole formatted
    // page, but this could also introduce some rendering bugs)

    // text style triggers
    foreach ( $wm_text_style as $find => $replace ) {
      $n = strlen( $find );
      $loop = 20;
      while( ( $loop-- ) &&
             ( ($l = strpos($line, $find)) !== false ) &&
             ( $r = strpos($line, $find, $l + $n) ) ) {
        $line = substr( $line, 0, $l ) . $replace[0] .
          substr( $line, $l + strlen($find), $r - $l - $n ) .
          $replace[1] . substr( $line, $r + $n );
      }
    }

    // add formatted line to page-output
    $o .= $line . $post . "\n";

  }

  // close last line
  $o .= "</p>\n";

  // finally the link-detection-regex
  // (impossible to do with simple string arithmetics)
  $o = preg_replace_callback( $link_regex, "wiki_link_regex_callback", $o );

  return( $o );
}


function wiki_link_regex_callback( $uu ) {

   global $ewiki_idf_url;

   $str = $uu[0];

   // link bracket '[' escaped with '!'
   if ( $str[0] == "!" ) {
     return(substr($str, 1));
   } elseif ( $str[0] == "[" ) {
     $str = substr( $str, 1, strlen($str) - 2 );
   }

   // explicit title given via [ foo | bar ]
   $href = $title = strtok( $str, "|" );
   if ( $uu = strtok("|") ) {
      $href = $uu;
   }

   // title and href swapped: swap back
   if ( strpos( "://", $title ) ||
        strpos( $title, ":" ) && !strpos( $href, ":" ) ) {
     $uu = $title;
     $title = $href;
     $href = $uu;
   }

   $title = trim($title);
   $href = trim($href);

   /* create _no_ WikiLinks */
   if ( false ){
     // interwiki links
     if ( strpos($href, ":") &&
          !strpos($href, "//") &&
          ($p1 = @$ewiki_interwiki[strtok($href, ":")]) ) {
       while ($p1_alias = @$ewiki_interwiki[$p1]) {
         $p1 = $p1_alias;
       }
       $href = $p1 . strtok("\000");
     } elseif (($ewiki_links === true) ||
               @$ewiki_links[$href] ||
               @$ewiki_internal_pages[$href]) {
       // ordinary internal WikiLinks
      $str = '<a href="' . EWIKI_SCRIPT .
        urlencode($href) . '">' . $title . '</a>';
     } else {
       $str = '<b>' . $title . '</b><a href="' .
         EWIKI_SCRIPT . urlencode($href) /*.EWIKI_ADDPARAMDELIM.'edit'*/ .
         ' ">?</a>';
     }
   }

   // convert normal URLs
   foreach ( $ewiki_idf_url as $find ) {
     if ( strpos( $href, $find ) === 0 ) {
      $str = '<a href="' . $href . '">' . $title . '</a>';
      break;
     }
   }

   return($str);
}

?>
