<?php

///////////////////////////////////////////////////////////////////////////
// wiki.php - class for Wiki style formatting
//
// Transforms input string with Wiki style formatting into HTML
// 
//
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
//                                                                       //
// Copyright (C) 2003 Howard Miller - GUIDE - University of Glasgow 
// guide.gla.ac.uk
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

// set this to 1 *IF* we are running in Moodle
// it enables Moodle specific functions, otherwise 0
define( "IN_MOODLE",1 );

// state defines
define( "STATE_NONE",1 ); // blank line has been detected, so looking for first line on next para
define( "STATE_PARAGRAPH",2 ); // currently processing vanilla paragraph
define( "STATE_BLOCKQUOTE",3 ); // currently processing blockquote section
define( "STATE_PREFORM",4 ); // currently processing preformatted text
define( "STATE_NOTIKI",5 ); // currently processing preformatted / no formatting

// list defines
define( "LIST_NONE", 1 ); // no lists active
define( "LIST_UNORDERED", 2 ); // unordered list active
define( "LIST_ORDERED", 3 ); // ordered list active
define( "LIST_DEFINITION", 4 ); // definition list active


class Wiki {
  
  var $block_state;
  var $list_state;
  var $list_depth;
  var $spelling_on;
  var $list_backtrack;
  var $output; // output buffer

  function close_block( $state ) {
    // provide appropriate closure for block according to state
    
    // if in list close this first
    $lclose = "";
    if ($this->list_state != LIST_NONE) {
      $lclose = $this->do_list( " ",true );
    }
    	
    $sclose = "";
    switch ($state) {
      case STATE_PARAGRAPH:
        $sclose =  "</p>\n";
        break;
      case STATE_BLOCKQUOTE:
        $sclose =  "</blockquote>\n";
        break;
      case STATE_PREFORM:
        $sclose =  "</pre>\n";
        break;
      case STATE_NOTIKI:
        $sclose =  "</pre>\n";
        break;  
    }

    return $lclose . $sclose;
  }


  function do_replace( $line, $mark, $tag ) {
    // do the regex thingy for things like bold, italic etc
    // $mark is the magic character, and $tag the HTML tag to insert

    // BODGE: replace inline $mark characters in places where we want them ignored
    // they will be put back after main substitutue, stops problems with eg, and/or
    $bodge = chr(1);
    $line = eregi_replace( '([[:alnum:]])'.$mark.'([[:alnum:]])', '\\1'.$bodge.'\\2',$line );

    $regex = '(^| |[(.,])'.$mark.'([^'.$mark.']*)'.$mark.'([^[:alnum:]]|$)';
    $replace = '\\1<'.$tag.'>\\2</'.$tag.'>\\3';
    $line = eregi_replace( $regex, $replace, $line );

    // BODGE: back we go
    $line = eregi_replace( $bodge, $mark, $line );

    return $line;
  }

  function do_replace_sub( $line, $mark, $tag ) {
    // do regex for subscript and superscript (slightly different)
    // $mark is the magic character and $tag the HTML tag to insert

    $regex = $mark.'([^'.$mark.']*)'.$mark;
    $replace = '<'.$tag.'>\\1</'.$tag.'>';
    return eregi_replace( $regex, $replace, $line );
  }

  function do_list( $line, $blank=false ) {
    // handle line with list character on it
    // if blank line implies drop to level 0
    
    // get magic character and then delete it from the line if not blank
    if ($blank) {
      $listchar="";
      $count = 0;
    }
    else {
      $listchar = $line{0};	
      $count = strspn( $line, $listchar );
      $line = eregi_replace( "^[".$listchar."]+ ", "", $line );
    }
    
    // find what sort of list this character represents
    $list_tag = "";
    $item_tag = "";
    $list_style = LIST_NONE;
    switch ($listchar) {
      case '*':
        $list_tag = "ul";
        $item_tag = "li";
        $list_style = LIST_UNORDERED;
        break;
      case '#':
        $list_tag = "ol";
        $item_tag = "li";
        $list_style = LIST_ORDERED;
        break;
      case ';':
        $list_tag = "dl";
        $item_tag = "dd";
        $list_style = LIST_DEFINITION;
        break;
      case ':':
        $list_tag = "dl";
        $item_tag = "dt";
        $list_style = LIST_DEFINITION;
        break;  
      }  

    // tag opening/closing regime now - fun bit :-)
    $tags = "";

    // if depth has reduced do number of closes to restore level
    for ($i=$this->list_depth; $i>$count; $i-- ) {
      $close_tag = array_pop( $this->list_backtrack );
      $tags = $tags . $close_tag;
      }

    // if depth has increased do number of opens to balance
    for ($i=$this->list_depth; $i<$count; $i++ ) {
      array_push( $this->list_backtrack, "</$list_tag>" );
      $tags = $tags . "<$list_tag>";
    }

    // ok, so list state is now same as style and depth same as count
    $this->list_state = $list_style;
    $this->list_depth = $count;

    // apply formatting to remainder of line
    $line = $this->line_replace( $line );
    
    if ($blank) {
      $newline = $tags;
    }
    else {  
      $newline = $tags . "<$item_tag>" . $line . "</$item_tag>";
    }

    return $newline;
  }  	

  function line_replace( $line ) {
    // return line after various formatting replacements
    // have been made - order is vital to stop them interfering with each other
   
    if (IN_MOODLE==1) {
      global $CFG;
    }

    // convert < and > (kills HTML)
    $line = str_replace( ">", "&gt;", $line );
    $line = str_replace( "<", "&lt;", $line );
    
    // ---- (at least) means a <HR>
    $line = eregi_replace( "^-{4}.*", "<div class=\"hr\"><hr /></div>", $line );
    
    // is this a list line (starts with * # ; :)    
    if (eregi( "^([*]+|[#]+|[;]+|[:]+) ", $line )) {
      $line = $this->do_list( $line );	        
    }    	

   // typographic conventions
    $line = str_replace( "--", "&#8212;", $line );
    $line = str_replace( " - ", " &#8211; ", $line );
    $line = str_replace( "...", " &#8230; ", $line );
    $line = str_replace( "(R)", "&#174;", $line );
    $line = str_replace( "(r)", "&#174;", $line );
    $line = str_replace( "(TM)", "&#8482;", $line );
    $line = str_replace( "(tm)", "&#8482;", $line );
    $line = str_replace( "(C)", "&#169;", $line );
    // $line = str_replace( "(c)", "&#169;", $line );
    $line = str_replace( "1/4", "&#188;", $line );
    $line = str_replace( "1/2", "&#189;", $line );
    $line = str_replace( "3/4", "&#190;", $line );

    $line = eregi_replace( "([[:digit:]]+[[:space:]]*)x([[:space:]]*[[:digit:]]+)", "\\1&#215;\\2", $line ); // (digits) x (digits) - multiply    

    // do formatting tags
    // NOTE: The / replacement  *has* to be first, or it will screw the 
    // HTML tags that are added by the other ones
    $line = $this->do_replace( $line, "/", "em" );
    $line = $this->do_replace( $line, "\*", "strong" );
    $line = $this->do_replace( $line, "\+", "ins" );
    $line = $this->do_replace( $line, "-", "del" );
    $line = $this->do_replace_sub( $line, "~", "sub" );
    $line = $this->do_replace_sub( $line, "\^", "sup" );
    // $line = $this->do_replace( $line, "\"", "q" );
    // $line = $this->do_replace( $line, "'", "q" );
    $line = $this->do_replace( $line, "%", "code" );
    $line = $this->do_replace( $line, "@", "cite" );

    // replace quotes
    $regex = '(^| |[(.,])\"([^\"]*)\"([^[:alnum:]]|$)';
    $replace = '\\1&#8220;\\2&#8221;\\3';
    $line = eregi_replace( $regex, $replace, $line );
   
    // convert urls into proper link with optional link text URL(text)
    $line = eregi_replace("([[:space:]]|^)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])\(([^)]+)\)",
      "\\1<A HREF=\"\\2://\\3\\4\" TARGET=\"newpage\">\\5</A>", $line);
    $line = eregi_replace("([[:space:]])www\.([^[:space:]]*)([[:alnum:]#?/&=])\(([^)]+)\)", 
      "\\1<A HREF=\"http://www.\\2\\3\" TARGET=\"newpage\">\\5</A>", $line);
 
    // make urls (with and without httpd) into proper links
    $line = eregi_replace("([[:space:]]|^)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
      "\\1<A HREF=\"\\2://\\3\\4\" TARGET=\"newpage\">\\2://\\3\\4</A>", $line);
    $line = eregi_replace("([[:space:]])www\.([^[:space:]]*)([[:alnum:]#?/&=])", 
      "\\1<A HREF=\"http://www.\\2\\3\" TARGET=\"newpage\">www.\\2\\3</A>", $line);

    // make email addresses into mailtos....
    $line = eregi_replace("([[:space:]]|^)([[:alnum:]._-]+@[[:alnum:]._-]+)\(([^)]+)\)",
      "\\1<a href=\"mailto:\\2\">\\3</a>", $line);

    // !# at the beginning of any lines means a heading
    $line = eregi_replace( "^!([1-6]) (.*)$", "<h\\1>\\2</h\\1>", $line );
    
    // acronym handing, example HTML(Hypertext Markyp Language)
    $line = ereg_replace( "([A-Z]+)\(([^)]+)\)", "<acronym title=\"\\2\">\\1</acronym>", $line );

    // *Moodle Specific* 
    if (IN_MOODLE==1) {
      // Replace resource link >>##(Description Text)
      $line = eregi_replace( " ([a-zA-Z]+):([0-9]+)\(([^)]+)\)",
         " <a href=\"".$CFG->wwwroot."/mod/\\1/view.php?id=\\2\">\\3</a> ", $line );

      // Replace picture resource link 
      global $course;    // This is a bit risky - it won't work everywhere

      if ($CFG->slasharguments) {
        $line = eregi_replace( "/([a-zA-Z0-9./_-]+)(png|gif|jpg)\(([^)]+)\)",
          "<img src=\"$CFG->wwwroot/file.php/$course->id/\\1\\2\" alt=\"\\3\" />", $line );
      } else {
        $line = eregi_replace( "/([a-zA-Z0-9./_-]+)(png|gif|jpg)\(([^)]+)\)",
          "<img src=\"$CFG->wwwroot/file.php\?file=$course->id/\\1\\2\" alt=\"\\3\" />", $line );
      }

      // Replace everything else resource link
      if ($CFG->slasharguments) {
        $line = eregi_replace( "file:/([[:alnum:]/._-]+)\(([^)]+)\)",
          "<a href=\"$CFG->wwwroot/file.php/$course->id/\\1\" >\\2</a>", $line );
      } else {
        $line = eregi_replace( "file:/([[:alnum:]/._-]+)\(([^)]+)\)",
          "<a href=\"$CFG->wwwroot/file.php\?file=$course->id/\\1\" >\\2</a>", $line );
      }

      replace_smilies( $line );

    }
    
    return $line;
  }


  function spellcheck( $line,$pspell_link ) {

    // split line into words
    $words = preg_split( "/[\s,-.]/ ", $line );

    // run through words
    $newline = "";
    foreach($words as $word) {
      $check_word = eregi_replace( "[,;:./&()* ?\"]", "", $word );
      $check_word = eregi_replace( "^'|'$","",$check_word );

      // words not to check
      $docheck = true;
      if (eregi("[0-9]",$check_word)) { $docheck=false; }

      if ( $docheck && (!pspell_check( $pspell_link, $check_word)) ) {
        $suggests = pspell_suggest( $pspell_link,$check_word );
        $suggest_line = "";
        foreach($suggests as $suggest) {
          $suggest_line = $suggest_line . " " . $suggest;
        }
        $word = "<span class=\"spellcheck\"><acronym title=\"$suggest_line\">$word</acronym></span>";
      }
      $newline = $newline . " " . $word;
    }

    return $newline;
  }


  function format( $content ) {
    // main entry point for processing TikiText
    // $content is string containing text with Tiki formatting
    // return: string containing XHTML formatting

    // initialisation stuff
    $this->output = "";
    $this->block_state = STATE_NONE;
    $this->list_state = LIST_NONE;
    $this->list_depth = 0;
    $this->list_backtrack = array();
    $this->spelling_on = false;

    // split content into array of single lines
    $lines = explode( "\n",$content );
    $buffer = "";

    // add a wiki div tag for CSS
    $buffer = $buffer . "<div class=\"wiki\">\n";

    // run through lines
    foreach( $lines as $line ) {

      // is this a blank line?
      $blank_line = eregi( "^[[:blank:]\r]*$", $line );
      if ($blank_line) {
        // first end current block according to state
        $buffer = $buffer . $this->close_block( $this->block_state );
        $this->block_state = STATE_NONE;
        continue;
      }

      // is this a spelling line
      $spell_parms = array();
      $spelling = eregi( "^!SPELL:([a-z]+):?(american|british|canadian)?(\r| |$)", $line,$spell_parms );
      if ($spelling) {
        $this->spelling_on = true;
        $pspell_link = pspell_new( $spell_parms[1], $spell_parms[2] );
        $line = "";
      }

      // spellcheck
      if ($this->spelling_on) {
        $line = $this->spellcheck( $line, $pspell_link );
      }
      
      // act now depending on current block state
      if ($this->block_state == STATE_NONE) {
        // first character of line defines block type
        if (eregi( "^> ",$line )) {
          // blockquote
          $buffer = $buffer . "<blockquote>\n";
          $buffer = $buffer . $this->line_replace( eregi_replace( "^>","",$line) ). "\n";
          $this->block_state = STATE_BLOCKQUOTE;
        }
        else
        if (eregi( "^  ",$line) ) {
          // preformatted text
          $buffer = $buffer . "<pre>\n";
          $buffer = $buffer . $this->line_replace($line) . "\n";
          $this->block_state = STATE_PREFORM;
        }
        else 
        if (eregi("^\% ",$line) ) {
        	// preformatted text - no processing
        	$buffer = $buffer . "<pre>\n";
        	$buffer = $buffer . eregi_replace( "^\%","",$line) . "\n";
        	$this->block_state = STATE_NOTIKI;
        } 	
        else {
          // ordinary paragraph
          $buffer = $buffer . "<p>\n";
          $buffer = $buffer . $this->line_replace($line) . "\n";
          $this->block_state = STATE_PARAGRAPH; 
        }
        continue;
      }
       
      if (($this->block_state == STATE_PARAGRAPH) |
          ($this->block_state == STATE_BLOCKQUOTE) |
          ($this->block_state == STATE_PREFORM) ) {
        $buffer = $buffer . $this->line_replace($line) . "\n";
        continue;
      }
      elseif ($this->block_state == STATE_NOTIKI) {
        $buffer = $buffer . $line . "\n";
      }  	
    }

    // close off any block level tags
    $buffer = $buffer . $this->close_block( $this->block_state );

    // close off wiki div
    $buffer = $buffer . "</div>\n";

    //return $buffer;    
    return $buffer;
  }

}

?>
