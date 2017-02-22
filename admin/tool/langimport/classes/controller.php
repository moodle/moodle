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
 * Lang import controller
 *
 * @package    tool_langimport
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_langimport;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/componentlib.class.php');

/**
 * Lang import controller
 *
 * @package    tool_langimport
 * @copyright  2014 Dan Poltawski <dan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class controller {
    /** @var array list of informational messages */
    public $info;
    /** @var array  list of error messages */
    public $errors;
    /** @var \lang_installer */
    private $installer;
    /** @var array languages available on the remote server */
    public $availablelangs;

    /**
     * Constructor.
     */
    public function __construct() {
        make_temp_directory('');
        make_upload_directory('lang');

        $this->info = array();
        $this->errors = array();
        $this->installer = new \lang_installer();

        $this->availablelangs = $this->installer->get_remote_list_of_languages();
    }

    /**
     * Install language packs provided
     *
     * @param string|array $langs array of langcodes or individual langcodes
     * @param bool $updating true if updating the langpacks
     * @return int false if an error encountered or
     * @throws \moodle_exception when error is encountered installing langpack
     */
    public function install_languagepacks($langs, $updating = false) {
        global $CFG;

        $this->installer->set_queue($langs);
        $results = $this->installer->run();

        $updatedpacks = 0;

        foreach ($results as $langcode => $langstatus) {
            switch ($langstatus) {
                case \lang_installer::RESULT_DOWNLOADERROR:
                    $a       = new \stdClass();
                    $a->url  = $this->installer->lang_pack_url($langcode);
                    $a->dest = $CFG->dataroot.'/lang';
                    $this->errors[] = get_string('remotedownloaderror', 'error', $a);
                    throw new \moodle_exception('remotedownloaderror', 'error', $a);
                    break;
                case \lang_installer::RESULT_INSTALLED:
                    $updatedpacks++;
                    if ($updating) {
                        event\langpack_updated::event_with_langcode($langcode)->trigger();
                        $this->info[] = get_string('langpackupdated', 'tool_langimport', $langcode);
                    } else {
                        $this->info[] = get_string('langpackinstalled', 'tool_langimport', $langcode);
                        event\langpack_imported::event_with_langcode($langcode)->trigger();
                    }
                    break;
                case \lang_installer::RESULT_UPTODATE:
                    $this->info[] = get_string('langpackuptodate', 'tool_langimport', $langcode);
                    break;
            }
        }

        return $updatedpacks;
    }

    /**
     * Uninstall language pack
     *
     * @param string $lang language code
     * @return bool true if language succesfull installed
     */
    public function uninstall_language($lang) {
        global $CFG;

        $dest1 = $CFG->dataroot.'/lang/'.$lang;
        $dest2 = $CFG->dirroot.'/lang/'.$lang;
        $rm1 = false;
        $rm2 = false;
        if (file_exists($dest1)) {
            $rm1 = remove_dir($dest1);
        }
        if (file_exists($dest2)) {
            $rm2 = remove_dir($dest2);
        }

        if ($rm1 or $rm2) {
            $this->info[] = get_string('langpackremoved', 'tool_langimport', $lang);
            event\langpack_removed::event_with_langcode($lang)->trigger();
            return true;
        } else {    // Nothing deleted, possibly due to permission error.
            $this->errors[] = get_string('langpacknotremoved', 'tool_langimport', $lang);
            return false;
        }
    }

    /**
     * Updated all install language packs with the latest found on servre
     *
     * @return bool true if languages succesfully updated.
     */
    public function update_all_installed_languages() {
        global $CFG;

        if (!$availablelangs = $this->installer->get_remote_list_of_languages()) {
            $this->errors[] = get_string('cannotdownloadlanguageupdatelist', 'error');
            return false;
        }

        $md5array = array();    // Convert to (string)langcode => (string)md5.
        foreach ($availablelangs as $alang) {
            $md5array[$alang[0]] = $alang[1];
        }

        // Filter out unofficial packs.
        $currentlangs = array_keys(get_string_manager()->get_list_of_translations(true));
        $updateablelangs = array();
        foreach ($currentlangs as $clang) {
            if (!array_key_exists($clang, $md5array)) {
                $this->info[] = get_string('langpackupdateskipped', 'tool_langimport', $clang);
                continue;
            }
            $dest1 = $CFG->dataroot.'/lang/'.$clang;
            $dest2 = $CFG->dirroot.'/lang/'.$clang;

            if (file_exists($dest1.'/langconfig.php') || file_exists($dest2.'/langconfig.php')) {
                $updateablelangs[] = $clang;
            }
        }

        // Filter out packs that have the same md5 key.
        $neededlangs = array();
        foreach ($updateablelangs as $ulang) {
            if (!$this->is_installed_lang($ulang, $md5array[$ulang])) {
                $neededlangs[] = $ulang;
            }
        }

        try {
            $updated = $this->install_languagepacks($neededlangs, true);
        } catch (\moodle_exception $e) {
            $this->errors[] = 'An exception occurred while installing language packs: ' . $e->getMessage();
            return false;
        }

        if ($updated) {
            $this->info[] = get_string('langupdatecomplete', 'tool_langimport');
            // The strings have been changed so we need to purge their cache to ensure users see the changes.
            get_string_manager()->reset_caches();
        } else {
            $this->info[] = get_string('nolangupdateneeded', 'tool_langimport');
        }

        return true;
    }

    /**
     * checks the md5 of the zip file, grabbed from download.moodle.org,
     * against the md5 of the local language file from last update
     * @param string $lang language code
     * @param string $md5check md5 to check
     * @return bool true if installed
     */
    public function is_installed_lang($lang, $md5check) {
        global $CFG;
        $md5file = $CFG->dataroot.'/lang/'.$lang.'/'.$lang.'.md5';
        if (file_exists($md5file)) {
            return (file_get_contents($md5file) == $md5check);
        }
        return false;
    }

    /**
     * Returns the URL where a given language pack can be downloaded
     *
     * Alternatively, if the parameter is empty, returns URL of the page with the
     * list of all available language packs.
     *
     * @param string $langcode language code like 'cs' or empty for unknown
     * @return string URL
     */
    public function lang_pack_url($langcode = '') {
        return $this->installer->lang_pack_url($langcode);
    }
}
