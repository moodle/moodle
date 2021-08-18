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
 * Library functions for MathType filter.
 *
 * @package    filter
 * @subpackage wiris
 * @copyright  WIRIS Europe (Maths for more S.L)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return an array with the position of the tags named $name on $code variable.
 * @param  String  $code       html code.
 * @param  String  $name       tag name.
 * @param  String  $autoclosed indicates if the tag is autoclosed.
 * @param  boolean $all        indicates if the array should contain all the tags or only the first one.
 * @param  int     $offset      search will start this number of characters counted from the beginning of the string
 * @return array
 */
function wrs_getelementsbynamefromstring($code, $name, $autoclosed, $all = false, $offset = 0) {
    $elements = array();
    $code = strtolower($code);
    $name = strtolower($name);
    $start = strpos($code, "<" . $name . " ", $offset);

    $i = 0;
    while ($start) {
        if ($autoclosed) {
            $endstring = '>';
        } else {
            $endstring = '</' . $name . '>';
        }

        $end = strpos($code, $endstring, $start);

        if ($end) {
            $end += strlen($endstring);
            $element = array();
            $element['start'] = $start;
            $element['end'] = $end;
            $elements[$i] = $element;
            $i++;
        } else {
            $end = $start + 1;
        }

        $start = strpos($code, '<' . $name . ' ', $end);
        if (!$all) {
            break;
        }

    }

    return $elements;
}

/**
 * Create CAS initial session id.
 * @return String
 */
function wrs_createsessionid() {
    $template = array(8, 4, 4, 4, 12);
    $id = '';
    for ($j = 0; $j < count($template); $j++) {
        if ($j > 0) {
            $id .= '-';
        }
        for ($i = 0; $i <= $template[$j]; $i++) {
            $c = strtoupper(dechex((int)floor(mt_rand() / mt_getrandmax() * 16)));
            $id .= $c;
        }
    }
    return $id;
}

/**
 * Set initial session on server
 * @param  String $sessionid CAS session id.
 * @param  String $xml       xml session.
 */
function wrs_setinitialsession($sessionid, $xml) {
    $wrap = com_wiris_system_CallWrapper::getInstance();
    $wrap->start();
    $h = new com_wiris_plugin_impl_HttpImpl('https://stateful.wiris.net/wiris/set', null);
    $h->setParameter('session_id', $sessionid);
    $h->setParameter('value', $xml);
    $h->setParameter('revision', 1);
    $post = true;
    $h->request($post);
    $wrap->stop();
}

/**
 * Includes a <nonapplet> tag on all the <APPLET> tags with an image linking a CAS jnlp containing the applet session.
 * This allows to download CAS jnlp for chrome browsers.
 * @param  String $text with <APPLET_TAGS>
 * @return String Filtered text.
 */
function wrs_filterapplettojnlp($text) {

    // An array containing the first applet tag. Don't get all because we use recursion on
    // $text and the long of the $text changes dynamically.
    $appletlist = wrs_getelementsbynamefromstring($text, 'applet', false, false);

    $i = 0;
    while (count($appletlist) != 0) {
        $output = '';
        $appletcode = htmlspecialchars_decode(substr($text, $appletlist[$i]['start'], $appletlist[$i]['end']));
        if (strpos($appletcode, ' src="') && strpos($appletcode, 'value="<session')) {
            $sessionid = wrs_createsessionid();
            $srcstart = strpos($appletcode, ' src="') + strlen(' src="');
            $srcend = strpos($appletcode, '.png"', $srcstart);
            $src = substr($appletcode, $srcstart, $srcend - $srcstart + 4);
            // Quick fix to obtain the algorithm language.
            $langstart = strpos($appletcode, ' lang="') + strlen(' lang="');
            $langend = strpos($appletcode, ' version="', $langstart);
            $lang = substr($appletcode, $langstart, $langend - $langstart - 1);

            $hreflink = 'http://stateful.wiris.net/demo/wiris/wiriscas.jnlp?session_id=' . $sessionid.'&lang='.$lang;
            $output .= html_writer::start_tag('a', array('href' => $hreflink));
            $img = '';
            if (method_exists('html_writer', 'img')) {
                $img = html_writer::img($src, 'CAS');
            } else {
                $img .= html_writer::start_tag('img', array('src' => $src));
                $img .= html_writer::end_tag('img');
            }
            $output .= $img;
            $output .= html_writer::end_tag('a');
            // We add noapplet tag in order to see CAS image on Chrome browser.
            $output = '<noapplet>' . $output . '</noapplet>' . '</APPLET>';
            // Searching applet without </applet> close tag.
            $appletsubstring = substr($text, $appletlist[$i]['start'], $appletlist[$i]['end'] - $appletlist[$i]['start'] - 9);
            // Applet substring to be replaced.
            $search = substr($text, $appletlist[$i]['start'], $appletlist[$i]['end'] - $appletlist[$i]['start']);
            $output = $appletsubstring . $output;
            $text = str_replace($search, $output, $text);

            $xmlstart = strpos($appletcode, 'value="<session');
            $xmlend = strpos($appletcode, '/session>"');
            $xml = substr($appletcode, $xmlstart + 7, $xmlend - $xmlstart + 2);

            wrs_setinitialsession($sessionid, $xml);
        }
        $appletlist = wrs_getelementsbynamefromstring($text, 'applet', false, false, $appletlist[$i]['end']);
    }
    return $text;
}

/**
 * Automatic class loading not avaliable for Moodle 2.4 and 2.5.
 * This method loads all files under "classes" folder.
 *
 */
function wrs_loadclasses() {
    global $CFG;

    if ($CFG->version < 2013111800) {
        require_once($CFG->dirroot . '/filter/wiris/classes/pluginwrapper.php');
        require_once($CFG->dirroot . '/filter/wiris/classes/paramsprovider.php');
        require_once($CFG->dirroot . '/filter/wiris/classes/configurationupdater.php');
        require_once($CFG->dirroot . '/filter/wiris/classes/pluginwrapperconfigurationupdater.php');
        require_once($CFG->dirroot . '/filter/wiris/classes/accessprovider.php');
    }
}
