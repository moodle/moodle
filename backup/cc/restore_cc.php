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
 * @package   moodlecore
 * @subpackage backup-imscc
 * @copyright 2009 Mauro Rondinelli (mauro.rondinelli [AT] uvcms.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die('Direct access to this script is forbidden.');

require_once($CFG->dirroot . '/backup/cc/includes/constants.php');
require_once($CFG->dirroot . '/backup/cc/cc2moodle.php');

function cc_convert ($dir) {
    global $OUTPUT;

    $manifest_file = $dir . DIRECTORY_SEPARATOR . 'imsmanifest.xml';
    $moodle_file = $dir . DIRECTORY_SEPARATOR . 'moodle.xml';
    $schema_file = 'cc' . DIRECTORY_SEPARATOR . '' . DIRECTORY_SEPARATOR . 'schemas' . DIRECTORY_SEPARATOR . 'cclibxml2validator.xsd';

    if (is_readable($manifest_file) && !is_readable($moodle_file)) {

        $is_cc = detect_cc_format($manifest_file);

        if ($is_cc) {

            $detected_requirements = detect_requirements();

            if (!$detected_requirements["php5"]) {
                echo $OUTPUT->notification(get_string('cc_import_req_php5', 'imscc'));
                return false;
            }

            if (!$detected_requirements["dom"]) {
                echo $OUTPUT->notification(get_string('cc_import_req_dom', 'imscc'));
                return false;
            }

            if (!$detected_requirements["libxml"]) {
                echo $OUTPUT->notification(get_string('cc_import_req_libxml', 'imscc'));
                return false;
            }

            if (!$detected_requirements["libxmlminversion"]) {
                echo $OUTPUT->notification(get_string('cc_import_req_libxmlminversion', 'imscc'));
                return false;
            }
            if (!$detected_requirements["xsl"]) {
                echo $OUTPUT->notification(get_string('cc_import_req_xsl', 'imscc'));
                return false;
            }

            echo get_string('cc2moodle_checking_schema', 'imscc') . '<br />';

            $cc_manifest = new DOMDocument();

            if ($cc_manifest->load($manifest_file)) {
                if ($cc_manifest->schemaValidate($schema_file)) {

                    echo get_string('cc2moodle_valid_schema', 'imscc') . '<br />';

                    $cc2moodle = new cc2moodle($manifest_file);

                    if (!$cc2moodle->is_auth()) {
                        return $cc2moodle->generate_moodle_xml();
                    } else {
                        echo $OUTPUT->notification(get_string('cc2moodle_req_auth', 'imscc'));
                        return false;
                    }

                } else {
                    echo $OUTPUT->notification(get_string('cc2moodle_invalid_schema', 'imscc'));
                    return false;
                }

            } else {
                echo $OUTPUT->notification(get_string('cc2moodle_manifest_dont_load', 'imscc'));
                return false;
            }
        }
    }

    return true;
}

function detect_requirements () {

    if (floor(phpversion()) >= 5) {
        $detected["php5"] = true;
    } else {
        $detected["php5"] = false;
    }

    $detected["xsl"] = extension_loaded('xsl');
    $detected['dom'] = extension_loaded('dom');
    $detected['libxml'] = extension_loaded('libxml');
    $detected['libxmlminversion'] = extension_loaded('libxml') && version_compare(LIBXML_DOTTED_VERSION, '2.6.30', '>=');

    return $detected;

}

function detect_cc_format ($xml_file) {

    $inpos = 0;
    $xml_snippet = file_get_contents($xml_file, 0, NULL, 0, 500);

    if (!empty($xml_snippet)) {

        $xml_snippet = strtolower($xml_snippet);
        $xml_snippet = preg_replace('/\s*/m', '', $xml_snippet);
        $xml_snippet = str_replace("'", '', $xml_snippet);
        $xml_snippet = str_replace('"', '', $xml_snippet);

        $search_string = "xmlns=" . NS_COMMON_CARTRIDGE;

        $inpos = strpos($xml_snippet, $search_string);

        if ($inpos) {
            return true;
        } else {
            return false;
        }

    } else {
        return false;
    }

}
