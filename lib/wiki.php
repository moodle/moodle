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
  var $output; // output buffer

  function close_block( $state ) {
    // provide appropriate closure for block according to state
    
    // if in list close this first
    $ltag = "";
    switch ($this->list_state) {
      case LIST_NONE:
        break;
      case LIST_UNORDERED:
         $ltag = "</ul>\n";
         break;
       case LIST_ORDERED:
         $ltag = "</ol>\n";
         break;
       case LIST_DEFINITION:
         $ltag = "</dl>\n";     	
         break;
    }    	
    $this->list_state = LIST_NONE;
    	
    switch ($state) {
      case STATE_PARAGRAPH:
        return "$ltag</p>\n";
        break;
      case STATE_BLOCKQUOTE:
        return "$ltag</blockquote>\n";
        break;
      case STATE_PREFORM:
        return "$ltag</pre>\n";
        break;
      case STATE_NOTIKI:
        return "$ltag</pre>\n";
        break;  
    }
  }


  function do_replace( $line, $mark, $tag ) {
    // do the regex thingy for things like bold, italic etc
    // $mark is the magic character, and $tag the HTML tag to insert

    $regex = '(^| |[(.,])'.$mark.'([^'.$mark.']*)'.$mark.'($| |[.,;:)])';
    $replace = '\\1<'.$tag.'>\\2</'.$tag.'>\\3';
    return eregi_replace( $regex, $replace, $line );
  }
  
  function do_list( $line ) {
    // handle line with list character on it
    
    // get magic character and then delete it from the line
    $listchar = $line{0};	
    $line = eregi_replace( "^[*#;:] ", "", $line );
    
    // if not in "list mode" then we need to drop the appropriate start tag
    $tag = "";
    if ($this->list_state == LIST_NONE) {
      switch ($listchar) {
        case '*':
          $tag = "<ul>";
          $this->list_state = LIST_UNORDERED;
          break;
        case '#':
          $tag = 	"<ol>";
          $this->list_state = LIST_ORDERED;
          break;
        case ';':
        case ':':
          $tag = "<dl>";
          $this->list_state = LIST_DEFINITION;
          break;  
        }  
      }     	
      
    // generate appropriate list tag
    $ltag = "";
    switch ($listchar) {
      case '*':
      case '#':
        $ltag = "<li>";
        break;
      case ';':
        $ltag = "<dd>";
        break;
      case ':':
        $ltag = "<dt>";
        break;      
    }    
    
    return $tag . $ltag . $line;
  }  	

  function line_replace( $line ) {
    // return line after various formatting replacements
    // have been made - order is vital to stop them interfering with each other
    
    // ---- (at least) means a <HR>
    $line = eregi_replace( "^-{4}.*", "<hr />", $line );
    
    // is this a list line (starts with * # ; :)    
    if (eregi( "^[*#;:] ", $line )) {
      $line = $this->do_list( $line );	        
    }    	
    
   // typographic conventions
    $line = eregi_replace( "--", "&#8212;", $line );
    $line = eregi_replace( " - ", " &#8211; ", $line );
    $line = eregi_replace( "\.\.\.", " &#8230; ", $line );
    $line = eregi_replace( "\(R\)", "&#174;", $line );
    $line = eregi_replace( "\(TM\)", "&#8482;", $line );
    $line = eregi_replace( "\(C\)", "&#169;", $line );
    $line = eregi_replace( "1/4", "&#188;", $line );
    $line = eregi_replace( "1/2", "&#189;", $line );
    $line = eregi_replace( "3/4", "&#190;", $line );
    $line = eregi_replace( "([[:digit:]]+[[:space:]]*)x([[:space:]]*[[:digit:]]+)", "\\1&#215;\\2", $line ); // (digits) x (digits) - multiply    

    // do formatting tags
    // NOTE: The / replacement  *has* to be first, or it will screw the 
    // HTML tags that are added by the other ones
    $line = $this->do_replace( $line, "/", "em" );
    $line = $this->do_replace( $line, "\*", "strong" );
    $line = $this->do_replace( $line, "\+", "ins" );
    $line = $this->do_replace( $line, "-", "del" );
    $line = $this->do_replace( $line, "~", "sub" );
    $line = $this->do_replace( $line, "\^", "sup" );
    $line = $this->do_replace( $line, "\"", "q" );
    $line = $this->do_replace( $line, "'", "q" );
    $line = $this->do_replace( $line, "%", "code" );
    $line = $this->do_replace( $line, "@", "cite" );
    
    // make urls (with and without httpd) into proper links
    $line = eregi_replace("([[:space:]]|^)([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",
      "\\1<A HREF=\"\\2://\\3\\4\" TARGET=\"newpage\">\\2://\\3\\4</A>", $line);
    $line = eregi_replace("([[:space:]])www\.([^[:space:]]*)([[:alnum:]#?/&=])", 
      "\\1<A HREF=\"http://www.\\2\\3\" TARGET=\"newpage\">www.\\2\\3</A>", $line);
                          
    // !# at the beginning of any lines means a heading
    $line = eregi_replace( "^!([1-6]) (.*)$", "<h\\1>\\2</h\\1>", $line );
    
    // acronym handing, example HTML(Hypertext Markyp Language)
    $line = ereg_replace( "([A-Z]+)\(([^)]+)\)", "<acronym title=\"\\2\">\\1</acronym>", $line );
    
    return $line;
  }

  function format( $content ) {
    // main entry point for processing TikiText
    // $content is string containing text with Tiki formatting
    // return: string containing XHTML formatting

    // initialisation stuff
    $this->output = "";
    $this->block_state = STATE_NONE;
    $this->list_state = LIST_NONE;

    // split content into array of single lines
    $lines = explode( "\n",$content );
    $buffer = "";

    // run through lines
    foreach( $lines as $line ) {

      // convert line contents
      // if ($this->block_state!=STATE_NOTIKI) {
      //   $line = $this->line_replace( $line );
      //}  

      // is this a blank line?
      $blank_line = eregi( "^[[:blank:]\r]*$", $line );
      if ($blank_line) {
        // first end current block according to state
        $buffer = $buffer . $this->close_block( $this->block_state );
        $this->block_state = STATE_NONE;
        continue;
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

    return $buffer;    
  }

}

?>
