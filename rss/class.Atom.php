<?php
/*
 * Project:     phAnTOM: a simple Atom parsing class
 * File:        atom_parse.php includes code for parsing
 *				Atom feeds, and returning an Atom object
 * Author:      Jeremy Ashcraft <ashcraft@13monkeys.com>
 * Version:		0.1
 * License:		GPL
 *
 * The lastest version of phAnTOM can be obtained from:
 * http://www.simplog.org
 *
 * For questions, help, comments, discussion, etc., please join the
 * Simplog Atom mailing list:
 * simplog-atom@lists.sourceforge.net
 *
 * This code is based on the MagpieRSS parser v0.52 written
 * by Kellan Elliott-McCrea <kellan@protest.net> and released under the GPL
 */
 

/* 
 * The lastest Atom feed spec is at http://diveintomark.org/public/2003/08/atom02spec.txt
 *
 *
 * RSS/Atom validators are readily available on the web at:
 * http://feeds.archive.org/validator/
 *
 */

class Atom {
	/*
	 * Useage Example:
	 *
	 * $xml = "<?xml version="1.0"......
	 *
	 * $atom = new atom( $xml );
	 *
	 * // print feed title
	 * print $atom->feed['title'];
	 *
	 * // print the title of each entry
	 * foreach ($atom->entries as $entry ) {
	 *	  print $entry[title];
	 * }
	 *
	 */
	 
	var $parser;
	
	var $current_item	= array();	// item currently being parsed
        var $entries		= array();	// collection of parsed items
	var $feed		= array();	// hash of channel fields
	
	var $parent_field	= array('RDF');
	var $author		= array();
	var $contributor	= array();
	var $current_field	= '';
	var $current_namespace	= false;
	
	var $ERROR = '';
	
/*======================================================================*\
    Function: MagpieRSS
    Purpose:  Constructor, sets up XML parser,parses source,
			  and populates object.. 
	Input:	  String containing the RSS to be parsed
\*======================================================================*/
	function Atom ($source) {
		
		# if PHP xml isn't compiled in, die
		#
		if (!function_exists('xml_parser_create')) {
			$this->error( 'Failed to load PHP\'s XML Extension. ' . 
						  'http://www.php.net/manual/en/ref.xml.php',
						   E_USER_ERROR );
		}
		
		$parser = @xml_parser_create();
		
		if (!is_resource($parser))
		{
			$this->error( 'Failed to create an instance of PHP\'s XML parser. ' .
						  'http://www.php.net/manual/en/ref.xml.php',
						  E_USER_ERROR );
		}

		
		$this->parser = $parser;
		
		# pass in parser, and a reference to this object
		# setup handlers
		#
		xml_set_object( $this->parser, $this );
		xml_set_element_handler($this->parser, 'start_element', 'end_element');
		xml_set_character_data_handler( $this->parser, 'cdata' ); 
	
		
		$status = xml_parse( $this->parser, $source );
		
		if (! $status ) {
			$errorcode = xml_get_error_code( $this->parser );
			if ( $errorcode != XML_ERROR_NONE ) {
				$xml_error = xml_error_string( $errorcode );
				$error_line = xml_get_current_line_number($this->parser);
				$error_col = xml_get_current_column_number($this->parser);
				$errormsg = $xml_error .' at line '. $error_line .', column '. $error_col;

				$this->error( $errormsg );
			}
		}
		
		xml_parser_free( $this->parser );
	}
	
	function start_element ($p, $element, &$attrs) {
		$element 	= strtolower( $element );
		# check for a namespace, and split if found
		#
		$namespace	= false;
		if ( strpos( $element, ':' ) ) {
			list($namespace, $element) = split( ':', $element, 2); 
		}
		$this->current_field = $element;
		if ( $namespace ) {
			$this->current_namespace = $namespace;
		}
		
		if ( $element == 'feed' ) {
			array_unshift( $this->parent_field, 'feed' );
		} else if ( $element == 'items' ) {
			array_unshift( $this->parent_field, 'items' );
		} else if ( $element == 'entry' ) {
			array_unshift( $this->parent_field, 'entry' );
		} else if ( $element == 'author' ) {
                        array_unshift( $this->parent_field, 'author' );
		} else if ( $element == 'contributor' ) {
                        array_unshift( $this->parent_field, 'contributor' );
                }
	}
	
	function end_element ($p, $element) {
		$element = strtolower($element);
							
		if ( $element == 'entry' ) {	
			$this->entries[] = $this->current_item;
			$this->current_item = array();
			array_shift( $this->parent_field );
		} else if ( $element == 'feed' or $element == 'items' or 
				 $element == 'author' or $element == 'contributor') {
			array_shift( $this->parent_field );
		}
		
		$this->current_field = '';
		$this->current_namespace = false;
	}
	
	function cdata ($p, $text) {
		# skip item, channel, items first time we see them
		#
		if ( $this->parent_field[0] == $this->current_field or
			 ! $this->current_field ) {
			return;
		} else if ( $this->parent_field[0] == 'feed') {
			if ( $this->current_namespace ) {
				$this->append(
					$this->feed[ $this->current_namespace ][ $this->current_field ],
					$text);
			} else {
				$this->append($this->feed[ $this->current_field ], $text);
			}
		
		} else if ( $this->parent_field[0] == 'entry' ) {
			if ( $this->current_namespace ) {
				$this->append(
					$this->current_item[ $this->current_namespace ][$this->current_field ],
					$text);
			} else {
				$this->append(
					$this->current_item[ $this->current_field ],
					$text );
			}
		} else if ( $this->parent_field[0] == 'author' ) {
                        if ( $this->current_namespace ) {
                                $this->append(
                                        $this->author[ $this->current_namespace ][ $this->current_field ],
                                        $text );
                        } else {
                                $this->append(
                                        $this->author[ $this->current_field ],
                                        $text );
                        }
                } else if ( $this->parent_field[0] == 'contributor' ) {
                        if ( $this->current_namespace ) {
                                $this->append(
                                        $this->contributor[ $this->current_namespace ][ $this->current_field ],
                                        $text );
                        } else {
                                $this->append(
                                        $this->contributor[ $this->current_field ],
                                        $text );
                        }
                }
	}
	
	function append (&$str1, $str2='') {
		if (!isset($str1) ) {
			$str1='';
		}
		$str1 .= $str2;
	}
	
	function error ($errormsg, $lvl=E_USER_WARNING) {
		// append PHP's error message if track_errors enabled
		if ( $php_errormsg ) { 
			$errormsg .= ' ('. $php_errormsg .')';
		}
		$this->ERROR = $errormsg;
		if ( ATOM_DEBUG ) {
			trigger_error( $errormsg, $lvl);		
		} else {
			error_log( $errormsg, 0);
		}
	}
		

/*======================================================================*\
	EVERYTHING BELOW HERE IS FOR DEBUGGING PURPOSES
\*======================================================================*/
	function show_list () {
		print '<ol>'."\n";
		foreach ($this->entries as $item) {
			print '<li>'. $this->show_entry( $item );
		}
		print '</ol>';
	}
	
	function show_feed () {
		print 'feed:<br />';
		print '<ul>';
		while ( list($key, $value) = each( $this->feed ) ) {
			print '<li> '. $key .': '. $value;
		}
		print '</ul>';
	}
	
	function show_entry ($item) {
		print 'entry: '. $item[title];
		print '<ul>';
		while ( list($key, $value) = each($item) ) {
			if ( is_array($value) ) {
				print '<br /><strong>'. $key .'</strong>';
				print '<ul>';
				while ( list( $ns_key, $ns_value) = each( $value ) ) {
					print '<li>'. $ns_key .': '. $ns_value;
				}
				print '</ul>';
			} else {
				print '<li> '. $key .': '. $value;
			}
		}
		print '</ul>';
	}

/*======================================================================*\
	END DEBUGGING FUNCTIONS	
\*======================================================================*/
	
} # end class Atom
?>