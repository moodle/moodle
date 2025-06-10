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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\helpers;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class CurlHelper {

    /**
     * Send post request with CURL.
     *
     * @param $url
     * @param $params
     * @param $options
     * @param $debug
     * @return object
     */
    public static function send_post($url, $params = [], $options = [], $debug = false) {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        if ($debug) {
            ob_start();
            $curl = new \curl(['debug' => true]);
            // It is required for old versions of moodle, it is fixed in new ones.
            if (property_exists($curl, 'emulateredirects') && !$curl->emulateredirects) {
                $curl->emulateredirects = true;
            }
            $out = fopen('php://output', 'w');

            $options['CURLOPT_VERBOSE'] = true;
            $options['CURLOPT_STDERR'] = $out;

            $json = $curl->post($url, $params, $options);
            fclose($out);
            $output = ob_get_clean();
        } else {
            $curl = new \curl;
            // It is required for old versions of moodle, it is fixed in new ones.
            if (property_exists($curl, 'emulateredirects') && !$curl->emulateredirects) {
                $curl->emulateredirects = true;
            }
            $json = $curl->post($url, $params, $options);
            $output = $json;
        }

        $data = (object)json_decode($json);
        $data->curlinfo = $curl->info;
        $data->debugging = $output;

        return $data;
    }
}
