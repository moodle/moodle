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

namespace core;

use core\exception\xml_format_exception;
use XMLParser;

/**
 * Class for parsing xml files.
 *
 * Handles functionality for:
 *
 * Import of xml files in questionbank and course import.
 * Can handle xml files larger than 10MB through chunking the input file.
 * Replaces the original xmlize by Hans Anderson, {@link http://www.hansanderson.com/contact/}
 * with equal interface.
 *
 * @package    core
 * @subpackage lib
 * @copyright  Kilian Singer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xml_parser {
    /** @var array resulting $xml tree */
    private array $xml;
    /** @var array stores references to constructed $xml tree */
    private array $current;
    /** @var int tores the level in the XML tree */
    private int $level;

    /**
     * Is called when tags are opened.
     *
     * Note: Used by xml element handler as callback.
     *
     * @param XMLParser $parser The XML parser resource.
     * @param string $name The XML source to parse.
     * @param array $attrs Stores attributes of XML tag.
     */
    private function startelement(
        XMLParser $parser,
        string $name,
        array $attrs,
    ): void {
        $current = &$this->current;
        $level = &$this->level;
        if (!empty($name)) {
            if ($level == 0) {
                $current[$level][$name] = [];
                $current[$level][$name]["@"] = $attrs; // Attribute.
                $current[$level][$name]["#"] = []; // Other tags.
                $current[$level + 1] = & $current[$level][$name]["#"];
                $level++;
            } else {
                if (empty($current[$level][$name])) {
                    $current[$level][$name] = [];
                }
                $siz = count($current[$level][$name]);
                if (!empty($attrs)) {
                    $current[$level][$name][$siz]["@"] = $attrs; // Attribute.
                }
                $current[$level][$name][$siz]["#"] = []; // Other tags.
                $current[$level + 1] = & $current[$level][$name][$siz]["#"];
                $level++;
            }
        }
    }

    /**
     * Is called when tags are closed.
     *
     * Note: Used by xml element handler as callback.
     *
     * @param XMLParser $parser The XML parser resource.
     * @param string $name The XML source to parse.
     */
    private function endelement(
        XMLParser $parser,
        string $name,
    ): void {
        $current = &$this->current;
        $level = &$this->level;
        if (!empty($name)) {
            if (empty($current[$level])) {
                $current[$level] = '';
            } else if (array_key_exists(0, $current[$level])) {
                if (count($current[$level]) == 1) {
                    $current[$level] = $current[$level][0]; // We remove array index if we only have a single entry.
                }
            }

            $level--;
        }
    }
    /**
     * Is called for text between the start and the end of tags.
     *
     * Note: Used by xml element handler as callback.
     *
     * @param XMLParser $parser The XML parser resource.
     * @param string $data The XML source to parse.
     */
    private function characterdata(
        XMLParser $parser,
        string $data,
    ): void {
        $current = &$this->current;
        $level = &$this->level;
        if (($data == "0") || (!empty($data) && trim($data) != "")) {
            $siz = count($current[$level]);
            if ($siz == 0) {
                $current[$level][0] = $data;
            } else {
                $key = max(array_keys($current[$level]));
                if (is_int($key)) {
                    end($current[$level]);
                    if (is_int(key($current[$level]))) { // If last index is nummeric we have CDATA and concat.
                        $current[$level][$key] = $current[$level][$key] . $data;
                    } else {
                        $current[$level][$key + 1] = $data; // Otherwise we make a new key.
                    }
                } else {
                    $current[$level][0] = $data;
                }
            }
        }
    }

    /**
     * Parses XML string.
     *
     * Note: Interface is kept equal to previous version.
     *
     * @param string $data the XML source to parse.
     * @param int $whitespace If set to 1 allows the parser to skip "space" characters in xml document. Default is 1
     * @param string $encoding Specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
     * @param bool $reporterrors if set to true, then a {@see xml_format_exception}
     *      exception will be thrown if the XML is not well-formed. Otherwise errors are ignored.
     * @return array|false representation of the parsed XML.
     * @throws xml_format_exception
     */
    public function parse(
        string $data,
        int $whitespace = 1,
        string $encoding = 'UTF-8',
        bool $reporterrors = false,
    ): array|false {
        $data = trim($data);
        $this->xml = [];
        $this->current = [];
        $this->level = 0;
        $this->current[0] = & $this->xml;
        $parser = xml_parser_create($encoding);
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $whitespace);
        xml_set_element_handler($parser, [$this, "startelement"], [$this, "endelement"]);
        xml_set_character_data_handler($parser, [$this, "characterdata"]);
        // Start parsing an xml document.
        for ($i = 0; $i < strlen($data); $i += 4096) {
            if (!xml_parse($parser, substr($data, $i, 4096))) {
                break;
            }
        }
        if ($reporterrors) {
            $errorcode = xml_get_error_code($parser);
            if ($errorcode) {
                $exception = new xml_format_exception(
                    xml_error_string($errorcode),
                    xml_get_current_line_number($parser),
                    xml_get_current_column_number($parser),
                );
                xml_parser_free($parser);
                throw $exception;
            }
        }
        xml_parser_free($parser); // Deletes the parser.
        if (empty($this->xml)) { // XML file is invalid or empty, return false.
            return false;
        }
        return $this->xml;
    }
}
