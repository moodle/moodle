<?PHP
/////////////////////////////////////////////////////////////////////////////
//                                                                         //
// NOTICE OF COPYRIGHT                                                     //
//                                                                         //
// Moodle - Filter for converting TeX expressions to cached gif images     //
//                                                                         //
// Copyright (C) 2004 Zbigniew Fiedorowicz fiedorow@math.ohio-state.edu    //
// Originally based on code provided by Bruno Vernier bruno@vsbeducation.ca//
// This program is free software; you can redistribute it and/or modify    //
// it under the terms of the GNU General Public License as published by    //
// the Free Software Foundation; either version 2 of the License, or       //
// (at your option) any later version.                                     //
//                                                                         //
// This program is distributed in the hope that it will be useful,         //
// but WITHOUT ANY WARRANTY; without even the implied warranty of          //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           //
// GNU General Public License for more details:                            //
//                                                                         //
//          http://www.gnu.org/copyleft/gpl.html                           //
//                                                                         //
/////////////////////////////////////////////////////////////////////////////
//-------------------------------------------------------------------------
// NOTE: This Moodle text filter converts TeX expressions delimited
// by either $$...$$ or by <tex...>...</tex> tags to gif images using
// mimetex.cgi obtained from http://www.forkosh.com/mimetex.html authored by
// John Forkosh john@forkosh.com. The mimetex.cgi ELF binary compiled for Linux i386
// is included with this distribution. 
// Note that there may be patent restrictions on the production of gif images
// in Canada and some parts of Western Europe and Japan until July 2004.
//-------------------------------------------------------------------------
// You will then need to edit your moodle/config.php to invoke tex_filter.php
//-------------------------------------------------------------------------


/// Edit these lines to correspond to your installation
// File path to the directory where mathml_filter.php resides
    $CFG->filterDirectory = "/var/www/html/moodle1_2d/filter/tex";
// File paths to the echo binary executable
    $CFG->echo = "/bin/echo";
// Frequency with which cache cleanup code is called: 119 means once in 119 times
// that the filter is invoked
    $CFG->cacheCleanFreq = 119;
// Time in seconds after which image gifs which haven't been viewed are considered stale
// and are scheduled for deletion
    $CFG->cacheCleanTime = 14*24*3600;
// Command used to list the oldest cached gif files to be scheduled for deletion, in
// conjunction with the value of cacheCleanTime
    $CFG->cleanFiles = "cd ".  $CFG->dataroot . "/1/tex_files;/bin/ls -tr | /usr/bin/head -20";


/// These lines are important - the variable must match the name 
/// of the actual function below
    $textfilter_function='tex_filter';

    if (function_exists($textfilter_function)) {
        return;
    }


function string_file_picture($path, $courseid=0, $height="", $width="", $link="") {
  // Given the path to a picture file in a course, or a URL,
  // this function includes the picture in the page.
  global $CFG;
  $output = "";
  if ($height) {
    $height = "height=\"$height\"";
  }
  if ($width) {
    $width = "width=\"$width\"";
  }
  if ($link) {
    $output .= "<a href=\"$link\">";
  }
  if (substr(strtolower($path), 0, 7) == "http://") {
    $output .= "<img border=\"0\" $height $width src=\"$path\" />";

  } else if ($courseid) {
    $output .= "<img border=\"0\" $height $width src=\"";
    if ($CFG->slasharguments) {        // Use this method if possible for better caching
      $output .= "$CFG->wwwroot/file.php/$courseid/$path";
    } else {
      $output .= "$CFG->wwwroot/file.php?file=/$courseid/$path";
    }
    $output .= "\" />";
  } else {
    $output .= "Error: must pass URL or course";
  }
  if ($link) {
    $output .= "</a>";
  }
  return $output;
}

function tex_filter ($courseid, $text) {

    global $CFG;
    $filterDirectory = $CFG->filterDirectory;

    $scriptname = $_SERVER['SCRIPT_NAME'];
    if (!strstr($scriptname,'/forum/')) {
      return $text;
    }
    /// Do a quick check using stripos to avoid unnecessary wor
    if (!preg_match('/<tex/i',$text) && !strstr($text,'$$')) {
        return $text;
    }
    
    if (strstr($scriptname,'post.php')) {
      $parent = forum_get_post_full($_GET['reply']);
      $discussion = get_record("forum_discussions","id",$parent->discussion);
    } else if (strstr($scriptname,'discuss.php')) {
      $discussion = get_record("forum_discussions","id",$_GET['d'] );
    } else {
      return $text;
    }
    if ($discussion->forum != 130) {
      return $text;
    }
    
    $old_umask = umask();

    if (!file_exists($CFG->dataroot . "/1/")) {
        mkdir($CFG->dataroot . "/1/",0775);
    }

    if (!file_exists($CFG->dataroot . "/1/tex_files/")) {
      mkdir($CFG->dataroot . "/1/tex_files/",0775);
    }
    umask($old_umask);
    echo "\n<!--$CFG->cleanFiles-->\n";
    if (isadmin()) { error_reporting (E_ALL); }; //for debugging
    $timenow = time();
    if (!($timenow % $CFG->cacheCleanFreq)) {
      $cleanFiles = explode("\n",`$CFG->cleanFiles`);
      foreach ($cleanFiles as $cleanFile) {
        $pathname = $CFG->dataroot . "/1/tex_files/" . $cleanFile;
        if ($timenow - filemtime($pathname)>$CFG->cacheCleanTime) {
          unlink($pathname);
        } else {
          break;
        }
      }
    }

    
    $text .= ' ';
    preg_match_all('/\$(\$\$+?)([^\$])/s',$text,$matches);
    for ($i=0;$i<count($matches[0]);$i++) {
        $replacement = str_replace('$','&#x00024;',$matches[1][$i]).$matches[2][$i];
        $text = str_replace($matches[0][$i],$replacement,$text);
    }

    if (isadmin()) { error_reporting (E_ALL); }; //for debugging
     
    // <tex> TeX expression </tex>
    // or $$ TeX expression $$

    preg_match_all('/<tex>(.+?)<\/tex>|\$\$(.+?)\$\$/is', $text, $matches);  
    for ($i=0; $i<count($matches[0]); $i++) {
        $texexp = $matches[1][$i] . $matches[2][$i];
        $filename = "tex_files/". md5($texexp) . ".gif";
        $pathname = $CFG->dataroot . "/1/" . $filename;
        
        if (file_exists($pathname)) {
           touch($pathname);
           $text = str_replace( $matches[0][$i], string_file_picture($filename, 1), $text);
        } else {
           $texexp = str_replace('&lt;','<',$texexp);
           $texexp = str_replace('&gt;','>',$texexp);
           $texexp = preg_replace('!\r\n?!',' ',$texexp);

           system("QUERY_STRING=;export QUERY_STRING;$filterDirectory/mimetex.cgi -d ". escapeshellarg($texexp) . "  >$pathname");
	   $text = str_replace( $matches[0][$i], string_file_picture($filename, 1), $text);
        }
         
    }
    return $text; 
};


?>