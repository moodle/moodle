<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Code for parsing xml files.
 *
 * Handles functionality for:
 *
 *    Import of xml files in questionbank and course import.
 *    Can handle xml files larger than 10MB through chunking the input file.
 *    Uses a similar interface to the original version xmlize() by Hans Anderson.
 *
 * @package    core
 * @subpackage lib
 * @version    This is version 2.0. A complete rewrite.
 * @copyright  Kilian Singer
 * @licence    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
 * Exception thrown when there is an error parsing an XML file.
 *
 * @copyright 2010 The Open University
 */
 class xml_format_exception extends moodle_exception {
    /** @var string */
    public $errorstring;
    public $line;
    public $char;
    function __construct($errorstring, $line, $char, $link = '') {
        $this->errorstring = $errorstring;
        $this->line = $line;
        $this->char = $char;

        $a = new stdClass();
        $a->errorstring = $errorstring;
        $a->errorline = $line;
        $a->errorchar = $char;
        parent::__construct('errorparsingxml', 'error', $link, $a);
    }
}


/**
 * Class for parsing xml files.
 *
 * Handles functionality for:
 *
 *    Import of xml files in questionbank and course import.
 *    Can handle xml files larger than 10MB through chunking the input file.
 *    Uses a similar interface to the original version xmlize() by Hans Anderson.
 *
 * @package    core
 * @subpackage lib
 * @version    This is version 2.0. A complete rewrite.
 * @copyright  Kilian Singer
 * @licence    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class huge_xml_parse {
    private $xml;             //resulting $xml tree
    private $current;         //array stores references to constructed $xml tree
    private $level;           //stores the level in the XML tree

/*** 
 * Is called when tags are opened.
 *
 * @author Kilian Singer
 * @param resource $parser The XML parser resource.
 * @param string $data The XML source to parse.
 */
    function startElement($parser, $name, $attrs) {
		$current=&$this->current;
		$level=&$this->level;
		if (!empty($name)) {
			if ($level == 0) {
				$current[$level][$name] = array();
				$current[$level][$name]["@"] = $attrs; //attribute
				$current[$level][$name]["#"] = array(); //other tags
				$current[$level + 1] = & $current[$level][$name]["#"];
				$level++;
			}
			else {
				if (empty($current[$level][$name])) {
					$current[$level][$name] = array();
				}
				$siz = count($current[$level][$name]);
				if (!empty($attrs)) $current[$level][$name][$siz]["@"] = $attrs; //attribute
				$current[$level][$name][$siz]["#"] = array(); //other tags
				$current[$level + 1] = & $current[$level][$name][$siz]["#"];
				$level++;
			}
		}
	}

    
/*** 
 * Is called when tags are closed.
 *
 * @author Kilian Singer
 * @param resource $parser The XML parser resource.
 * @param string $data The XML source to parse.
 */
	function endElement($parser, $name){
		$current=&$this->current;
		$level=&$this->level;
		if (!empty($name)) {
			if (empty($current[$level])) $current[$level] = NULL;
			else
			if (array_key_exists(0, $current[$level])) {
				if (count($current[$level]) == 1) {
					$current[$level] = $current[$level][0]; //we remove array index if we only have a single entry
				}
			}

			$level--;
		}
	}

    
/*** 
 * Is called for text between the start and the end of tags. 
 *
 * @author Kilian Singer
 * @param resource $parser The XML parser resource.
 * @param string $data The XML source to parse.
 */
    function characterData($parser, $data) {
		$current=&$this->current;
		$level=&$this->level;
		if (($data == "0") || (!empty($data) && trim($data) != "")) {
			$siz = count($current[$level]);
			if ($siz == 0) $current[$level][0] = $data;
			else {
				$key = max(array_keys($current[$level]));
				if (is_int($key)) {
					end($current[$level]);
					if (is_int(key($current[$level]))) { //if last index is nummeric we have CDATA and concat
						$current[$level][$key] = $current[$level][$key] . $data;
					}
					else {
						$current[$level][$key + 1] = $data; //otherwise we make a new key
					}
				}
				else {
					$current[$level][0] = $data;
				}
			}
		}
	}

/*** 
 * Parses XML string. 
 *
 * @author Kilian Singer
 * @param string $data the XML source to parse.
 * @param int $whitespace If set to 1 allows the parser to skip "space" characters in xml document. Default is 1
 * @param string $encoding Specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * @param bool $reporterrors if set to true, then a {@link xml_format_exception}
 *      exception will be thrown if the XML is not well-formed. Otherwise errors are ignored.
 * @return array representation of the parsed XML.
 */
    function parse($data, $whitespace = 1, $encoding = 'UTF-8', $reporterrors = false) {
		$data = trim($data);
		$this->xml = array();
		$this->current = array();
		$this->level = 0;
		$this->current[0] = & $this->xml;
		$parser = xml_parser_create($encoding);
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $whitespace);
		xml_set_element_handler($parser, [$this, "startElement"], [$this, "endElement"]);
		xml_set_character_data_handler($parser, [$this, "characterData"]);
		for ($i = 0; $i < strlen($data); $i+= 4096) if(!xml_parse($parser, substr($data, $i, 4096))) break; // start parsing an xml document
		
		if ($reporterrors) {
			$errorcode = xml_get_error_code($parser);
			if ($errorcode) {
				$exception = new xml_format_exception(xml_error_string($errorcode),
				xml_get_current_line_number($parser),
				xml_get_current_column_number($parser));
				xml_parser_free($parser);
				throw $exception;
			}	
		}

		
		xml_parser_free($parser); // deletes the parser

		if (empty($this->xml)) {
		// XML file is invalid or empty, return false
		      return false;
		}

		return $this->xml;
	}
}

/*** 
 * @param string $data the XML source to parse.
 * @param int $whitespace If set to 1 allows the parser to skip "space" characters in xml document. Default is 1
 * @param string $encoding Specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * @param bool $reporterrors if set to true, then a {@link xml_format_exception}
 *      exception will be thrown if the XML is not well-formed. Otherwise errors are ignored.
 * @return array representation of the parsed XML.
 */
function xmlize($data, $whitespace = 1, $encoding = 'UTF-8', $reporterrors = false){
    $hxml = new huge_xml_parse();
    return $hxml->parse($data,$whitespace,$encoding,$reporterrors);
}


