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
 * Test xmlize xml import.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2017 Kilian Singer {@link http://quantumtechnology.info}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/xmlize.php');

/**
 * xmlize.php - xmlize() is by Hans Anderson, {@link http://www.hansanderson.com/contact/}
 *
 * Ye Ole "Feel Free To Use it However" License [PHP, BSD, GPL].
 * some code in xml_depth is based on code written by other PHPers
 * as well as one Perl script.  Poor programming practice and organization
 * on my part is to blame for the credit these people aren't receiving.
 * None of the code was copyrighted, though.
 *
 * @package core
 * @subpackage lib
 * @author Hans Anderson
 * @version This is a stable release, 1.0.  I don't foresee any changes, but you
 * might check {@link http://www.hansanderson.com/php/xml/} to see
 * @copyright Hans Anderson
 * @license Feel Free To Use it However
 */

/**
 * Create an array structure from an XML string.
 *
 * Usage:<br>
 * <code>
 * $xml = xmlize($array);
 * </code>
 * See the function {@link traverse_xmlize()} for information about the
 * structure of the array, it's much easier to explain by showing you.
 * Be aware that the array is somewhat tricky.  I use xmlize all the time,
 * but still need to use {@link traverse_xmlize()} quite often to show me the structure!
 *
 * THIS IS A PHP 5 VERSION:
 *
 * This modified version basically has a new optional parameter
 * to specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * I recommend you to read this PHP bug. There you can see how PHP4, PHP5.0.0
 * and PHP5.0.2 will handle this.
 * {@link http://bugs.php.net/bug.php?id=29711}
 * Ciao, Eloy :-)
 *
 * @param string $data The XML source to parse.
 * @param int $whitespace  If set to 1 allows the parser to skip "space" characters in xml document. Default is 1
 * @param string $encoding Specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * @param bool $reporterrors if set to true, then a {@link xml_format_exception}
 *      exception will be thrown if the XML is not well-formed. Otherwise errors are ignored.
 * @return array representation of the parsed XML.
 */
function xmlize_old($data, $whitespace = 1, $encoding = 'UTF-8', $reporterrors = false) {

    $data = trim($data);
    $vals = array();
    $parser = xml_parser_create($encoding);
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $whitespace);
    xml_parse_into_struct($parser, $data, $vals);

    // Error handling when the xml file is not well-formed
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
    xml_parser_free($parser);

    $i = 0;
    if (empty($vals)) {
        // XML file is invalid or empty, return false
        return false;
    }

    $array = array();
    $tagname = $vals[$i]['tag'];
    if (isset($vals[$i]['attributes'])) {
        $array[$tagname]['@'] = $vals[$i]['attributes'];
    } else {
        $array[$tagname]['@'] = array();
    }

    $array[$tagname]["#"] = xml_depth($vals, $i);

    return $array;
}

/**
 * @internal You don't need to do anything with this function, it's called by
 * xmlize. It's a recursive function, calling itself as it goes deeper
 * into the xml levels.  If you make any improvements, please let me know.
 * @access private
 */
function xml_depth($vals, &$i) {
    $children = array();

    if ( isset($vals[$i]['value']) )
    {
        array_push($children, $vals[$i]['value']);
    }

    while (++$i < count($vals)) {

        switch ($vals[$i]['type']) {

           case 'open':

                if ( isset ( $vals[$i]['tag'] ) )
                {
                    $tagname = $vals[$i]['tag'];
                } else {
                    $tagname = '';
                }

                if ( isset ( $children[$tagname] ) )
                {
                    $size = sizeof($children[$tagname]);
                } else {
                    $size = 0;
                }

                if ( isset ( $vals[$i]['attributes'] ) ) {
                    $children[$tagname][$size]['@'] = $vals[$i]["attributes"];

                }

                $children[$tagname][$size]['#'] = xml_depth($vals, $i);

            break;


            case 'cdata':
                array_push($children, $vals[$i]['value']);
            break;

            case 'complete':
                $tagname = $vals[$i]['tag'];

                if( isset ($children[$tagname]) )
                {
                    $size = sizeof($children[$tagname]);
                } else {
                    $size = 0;
                }

                if( isset ( $vals[$i]['value'] ) )
                {
                    $children[$tagname][$size]["#"] = $vals[$i]['value'];
                } else {
                    $children[$tagname][$size]["#"] = '';
                }

                if ( isset ($vals[$i]['attributes']) ) {
                    $children[$tagname][$size]['@']
                                             = $vals[$i]['attributes'];
                }

            break;

            case 'close':
                return $children;
            break;
        }

    }

        return $children;


}
/**
 * This function performs a recursive array comparison.
 *
 * Code from {@link https://stackoverflow.com/questions/3876435/recursive-array-diff}.
 *
 * @package    core
 * @category   phpunit
 */
function arrayRecursiveDiff($aArray1, $aArray2) {
    $aReturn = array();
    foreach ($aArray1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $aArray2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            } else {
                if ($mValue != $aArray2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
           }
       } else {
           $aReturn[$mKey] = $mValue;
       }
    }
    return $aReturn;
}

/**
 * This test compares library against the original xmlize XML importer.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2017 Kilian Singer {@link http://quantumtechnology.info}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_xmlize_testcase extends basic_testcase {
    public function test_xmlimport_of_proper_file() {
        global $CFG;
        $filecontent = file_get_contents($CFG->libdir . "/tests/sample_questions.xml");
        // Put in the next line a modified file to see that it detects any difference.
        $filecontent2 = file_get_contents($CFG->libdir . "/tests/sample_questions.xml");
        $xml_new = xmlize($filecontent);
        $xml_old = xmlize_old($filecontent2);
        $diff = arrayRecursiveDiff($xml_old, $xml_new);
        $this->assertSame($diff, array());
    }
    public function test_xmlimport_of_wrong_file() {
        global $CFG;
        $filecontent = file_get_contents($CFG->libdir . "/tests/sample_questions_wrong.xml");
        // Put in the next line a modified file to see that it detects any difference.
        $filecontent2 = file_get_contents($CFG->libdir . "/tests/sample_questions_wrong.xml");
        $msg1="";
        $msg2="";
        $xml_new = array();
        $xml_old = array();
        try {
            $xml_new = xmlize($filecontent, 1, "UTF-8", true);
        } catch (Exception $e) {
            $msg1 = $e->getMessage();
            // echo 'Exception caught on purpose: ', $msg1, "\n";
        }
        try {
            $xml_old = xmlize_old($filecontent2, 1, "UTF-8", true);
        } catch (Exception $e) {
            $msg2 = $e->getMessage();
            // echo 'Exception caught on purpose: ', $msg2, "\n";
        }
        $diff = arrayRecursiveDiff($xml_old, $xml_new);
        $this->assertSame($diff, array());
        $this->assertSame($msg1, $msg2);
    }
}
