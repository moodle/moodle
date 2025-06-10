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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/sdk/perflog.class.php');
require_once(__DIR__.'/lib.php');

class turnitintooltwo_performancelog extends PerformanceLog {

    /**
     * Log networking performance details of an individual request
     *
     * @param resource $ch The cURL handle corresponding to the request to log
     * @param float $totalresponsetime Total time taken for the request in seconds
     */
    protected function log($ch, $totalresponsetime) {
        global $CFG, $USER, $action;

        static $config;
        if (empty($config)) {
            $config = turnitintooltwo_admin_config();
        }

        if ($config->enableperformancelogs) {
            // We only keep 10 log files, delete any additional files.
            $prefix = "perflog_";

            $dirpath = $CFG->tempdir."/turnitintooltwo/logs";
            if (!file_exists($dirpath)) {
                mkdir($dirpath, 0777, true);
            }
            $dir = opendir($dirpath);
            $files = array();
            while ($entry = readdir($dir)) {
                if (substr(basename($entry), 0, 1) != "." AND substr_count(basename($entry), $prefix) > 0) {
                    $files[] = basename($entry);
                }
            }
            sort($files);
            for ($i = 0; $i < count($files) - 10; $i++) {
                unlink($dirpath."/".$files[$i]);
            }

            // Prepare string.
            $str = '';
            if (!empty($action)) {
                $str .= " - $action";
            } else {
                $do = (!empty($_REQUEST['do'])) ? $_REQUEST['do'] : '';
                if (!empty($do)) {
                    $str .= " - {$do}";
                }
            }
            $httpstatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $str .= " - HTTP:" . $httpstatus;
            if ($httpstatus === 0 && curl_getinfo($ch, CURLINFO_SIZE_UPLOAD) === 0) {
                // CURLINFO_CONNECT_TIME is not reliable when the request fails to connect.
                $connecttime = $totalresponsetime;
            } else {
                $connecttime = curl_getinfo($ch, CURLINFO_CONNECT_TIME);
            }
            $str .= " - connect:" . sprintf('%0.3f', $connecttime);
            $str .= " - total:" . sprintf('%0.3f', $totalresponsetime);
            $str .= " - up:" . curl_getinfo($ch, CURLINFO_SIZE_UPLOAD);
            $str .= " - down:" . curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
            $str .= " - userid:" . $USER->id;
            if (array_key_exists('REQUEST_URI', $_SERVER)) {
                $str .= " - " . $_SERVER['REQUEST_URI'];
            }

            if ($httpstatus === 0) {
                $str .= " - " . curl_error($ch);
            }

            // Write to log file.
            $filepath = $dirpath."/".$prefix.gmdate('Y-m-d', time()).".txt";
            $file = fopen($filepath, 'a');
            $output = date('Y-m-d H:i:s O') . $str . "\r\n";
            fwrite($file, $output);
            fclose($file);
        }
    }

}
