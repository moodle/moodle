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
 * Customised version of phpmailer for Moodle
 *
 * @package    moodle
 * @subpackage lib
 * @author     Dan Poltawski <talktodan@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

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
    public function EncodeHeader($str, $position = 'text') {
        $encoded = textlib::encode_mimeheader($str, $this->CharSet);
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
     */
    public static function RFCDate() {
        $tz = date('Z');
        $tzs = ($tz < 0) ? '-' : '+';
        $tz = abs($tz);
        $tz = (($tz - ($tz%3600) )/3600)*100 + ($tz%3600)/60; // fixed tz bug
        $result = sprintf("%s %s%04d", date('D, j M Y H:i:s'), $tzs, $tz);

        return $result;
    }

    /**
     * This is a temporary replacement of the parent::EncodeQP() that does not
     * call quoted_printable_encode() even if it is available. See MDL-23240 for details
     *
     * @see parent::EncodeQP() for full documentation
     */
    public function EncodeQP($string, $line_max = 76, $space_conv = false) {
        //if (function_exists('quoted_printable_encode')) { //Use native function if it's available (>= PHP5.3)
        //    return quoted_printable_encode($string);
        //}
        $filters = stream_get_filters();
        if (!in_array('convert.*', $filters)) { //Got convert stream filter?
            return $this->EncodeQPphp($string, $line_max, $space_conv); //Fall back to old implementation
        }
        $fp = fopen('php://temp/', 'r+');
        $string = preg_replace('/\r\n?/', $this->LE, $string); //Normalise line breaks
        $params = array('line-length' => $line_max, 'line-break-chars' => $this->LE);
        $s = stream_filter_append($fp, 'convert.quoted-printable-encode', STREAM_FILTER_READ, $params);
        fputs($fp, $string);
        rewind($fp);
        $out = stream_get_contents($fp);
        stream_filter_remove($s);
        $out = preg_replace('/^\./m', '=2E', $out); //Encode . if it is first char on a line, workaround for bug in Exchange
        fclose($fp);
        return $out;
    }

    protected function PostSend() {
        // Now ask phpunit if it wants to catch this message.
        if (PHPUNIT_TEST && phpunit_util::is_redirecting_messages()) {
            $mail = new stdClass();
            $mail->header = $this->MIMEHeader;
            $mail->body = $this->MIMEBody;
            $mail->subject = $this->Subject;
            $mail->from = $this->From;
            phpunit_util::phpmailer_sent($mail);
            return true;
        } else {
            return parent::PostSend();
        }
    }
}
