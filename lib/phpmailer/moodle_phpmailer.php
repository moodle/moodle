<?php

/**
 * Moodle - Modular Object-Oriented Dynamic Learning Environment
 *          http://moodle.org
 * Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    moodle
 * @subpackage lib
 * @author     Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Customised version of phpmailer for Moodle
 */

// PLEASE NOTE: we use the phpmailer class _unmodified_
// through the joys of OO. Distros are free to use their stock
// version of this file.
require_once($CFG->libdir.'/phpmailer/class.phpmailer.php');

/**
 * Moodle Customised version of the PHPMailer class
 *
 * This class extends the stock PHPMailer class
 * in order to make sensible configuration choices,
 * and behave in a way which is friendly to moodle.
 *
 * @copyright 2009 Dan Poltawski <talktodan@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_phpmailer extends PHPMailer {

    /**
     * Constructor - creates an instance of the PHPMailer class
     * with Moodle defaults.
     */
    public function __construct(){
        global $CFG;
        $this->Version   = 'Moodle '.$CFG->version;         // mailer version
        $this->PluginDir = $CFG->libdir.'/phpmailer/';      // plugin directory (eg smtp plugin)
        $this->CharSet   = 'UTF-8';
    }

    /**
     * Extended AddCustomHeader function in order to stop duplicate 
     * message-ids
     * http://tracker.moodle.org/browse/MDL-3681
     */
    public function AddCustomHeader($custom_header) {
        if(preg_match('/message-id:(.*)/i', $custom_header, $matches)){
            $this->MessageID = $matches[1];
            return true;
        }else{
            return parent::AddCustomHeader($custom_header);
        }
    }

    /**
     * Use internal moodles own textlib to encode mimeheaders.
     * Fall back to phpmailers inbuilt functions if not 
     */
    public function EncodeHeader ($str, $position = 'text') {
        $textlib = textlib_get_instance();
        $encoded = $textlib->encode_mimeheader($str, $this->CharSet);
        if ($encoded !== false) {
            $encoded = str_replace("\n", $this->LE, $encoded);
            if ($position == 'phrase') {
                return ("\"$encoded\"");
            }
            return $encoded;
        }

        return parent::EncodeHeader($str, $position);
    }

    /**
     * Replaced function to fix tz bug:
     * http://tracker.moodle.org/browse/MDL-12596
     *
     * PLEASE NOTE: intentionally not declared this function public in 
     * order that we keep compatibiltiy with previous versions of phpmailer 
     * where it was declared private.
     */
    static function RFCDate() {
        $tz = date('Z');
        $tzs = ($tz < 0) ? '-' : '+';
        $tz = abs($tz);
        $tz = (($tz - ($tz%3600) )/3600)*100 + ($tz%3600)/60; // fixed tz bug
        $result = sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);

        return $result;
    }
}
