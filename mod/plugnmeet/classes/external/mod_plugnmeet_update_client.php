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
 *
 * @package    mod_plugnmeet
 * @author     Jibon L. Costa <jibon@mynaparrot.com>
 * @copyright  2022 MynaParrot
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . "/externallib.php");
if (!class_exists("plugNmeetConnect")) {
    require($CFG->dirroot . '/mod/plugnmeet/helpers/plugNmeetConnect.php');
}

/**
 *
 */
class mod_plugnmeet_update_client extends external_api {
    /**
     * @return external_function_parameters
     */
    public static function update_client_parameters() {
        return new external_function_parameters([

        ]);
    }

    /**
     * @return array|stdClass
     * @throws coding_exception
     * @throws dml_exception
     */
    public static function update_client() {
        global $CFG, $USER;
        $output = new stdClass();
        $output->status = false;

        try {
            $usercontext = context_user::instance($USER->id);
            require_login();
            require_capability('moodle/course:update', $usercontext);
        } catch (Exception $e) {
            $output->msg = $e->getMessage();
            return $output;
        }

        $config = get_config('mod_plugnmeet');
        $clientzipfile = $CFG->dataroot . "/temp/client.zip";

        require_once($CFG->libdir . '/filelib.php');
        $curl = new curl();
        $result = $curl->download_one($config->client_download_url, null, array(
            'filepath' => $clientzipfile,
            'timeout' => 60
        ));

        if ($result !== true) {
            $output->msg = $result;
            return $output;
        }

        $zip = new ZipArchive;
        $res = $zip->open($clientzipfile);
        if ($res === true) {
            $extractpath = $CFG->dirroot . "/mod/plugnmeet/pix/";
            // For safety let's delete client first.
            self::delete_dir($extractpath . "client");

            $zip->extractTo($extractpath);
            $zip->close();
            unlink($clientzipfile);

            $output->status = true;
            $output->msg = get_string('client_updated_success', 'plugnmeet');
        } else {
            $output->msg = get_string('client_updated_failed', 'plugnmeet');;
        }

        return (array)$output;
    }

    /**
     * @param $dirpath
     * @return void
     */
    private static function delete_dir($dirpath) {
        if (!is_dir($dirpath)) {
            return;
        }
        if (substr($dirpath, strlen($dirpath) - 1, 1) != '/') {
            $dirpath .= '/';
        }
        $it = new RecursiveDirectoryIterator($dirpath, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it,
            RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($dirpath);
    }

    /**
     * @return external_single_structure
     */
    public static function update_client_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_BOOL, 'status of request'),
            'msg' => new external_value(PARAM_TEXT, 'status message', VALUE_REQUIRED),
        ]);
    }
}
