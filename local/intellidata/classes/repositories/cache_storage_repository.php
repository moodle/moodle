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
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

namespace local_intellidata\repositories;

use local_intellidata\helpers\DebugHelper;
use local_intellidata\helpers\SettingsHelper;
use local_intellidata\helpers\StorageHelper;

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intellidata
 * @copyright  2020 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class cache_storage_repository extends file_storage_repository {

    /**
     * Storage component.
     */
    const STORAGE_COMPONENT = 'local_intellidata';
    /**
     * Cache area.
     */
    const CACHE_AREA = 'events';
    /**
     * Cache user identifier.
     */
    const CACHE_USER_IDENTIFIER = '_idcacheuserid_';

    /** @var null|string */
    public $datatype = null;

    /**
     * Get cache storage.
     *
     * @return \cache_application|\cache_session|\cache_store
     */
    private function get_cache_storage() {
        return \cache::make(self::STORAGE_COMPONENT, self::CACHE_AREA);
    }

    /**
     * Get record from cache.
     *
     * @param $cache
     * @return array
     */
    private function get_record($cache, $key) {
        return ($cache->has($key))
            ? $cache->get($key)
            : [];
    }

    /**
     * Get cache key.
     *
     * @return string
     */
    private function cache_key() {
        global $USER;

        return $this->datatype['name'] .
            (
                !empty($USER->id)
                    ? self::CACHE_USER_IDENTIFIER . $USER->id
                    : ''
            );
    }

    /**
     * Get datatype cache keys.
     *
     * @return string
     */
    private function get_datatype_keys($cache) {
        $cachekeys = (method_exists($cache, 'get_all_keys')) ? $cache->get_all_keys() : [];

        $datatypekeys = [];
        foreach ($cachekeys as $key) {
            $keyprefix = $this->datatype['name'] . self::CACHE_USER_IDENTIFIER;
            if ($key == $this->datatype['name'] ||
                substr($key, 0, strlen($keyprefix)) === $keyprefix) {
                $datatypekeys[] = $key;
            }
        }

        return $datatypekeys;
    }

    /**
     * Save data to storage.
     *
     * @param $data
     * @throws \moodle_exception
     */
    public function save_data($data) {
        $cache = $this->get_cache_storage();
        $cachekey = $this->cache_key();
        $cacherecord = $this->get_record($cache, $cachekey);

        $cacherecord[] = $data;

        if (!$cache->set($cachekey, $cacherecord)) {
            // Something wrong.
            DebugHelper::error_log("IntelliData events tracking: error save event to cache,
            key:{$cachekey}, data:" . json_encode($cacherecord));
        }

        if (count($cacherecord) > 1000) {
            $this->save_file(false);
            $cacherecord = [];
        }

        if (!$cache->set($cachekey, $cacherecord)) {
            // Something wrong.
            DebugHelper::error_log("IntelliData events tracking: error save event to cache,
            key:{$cachekey}, data:" . json_encode($cacherecord));
        }
    }

    /**
     * Save files based on storage data.
     *
     * @return \stored_file|null
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \file_exception
     * @throws \moodle_exception
     * @throws \stored_file_creation_exception
     */
    public function save_file($savetempfile = true) {
        $cache = $this->get_cache_storage();

        // Get records from cache storage.
        $keys = $this->get_datatype_keys($cache);
        $tempfile = $this->get_temp_file();
        if (count($keys)) {
            $this->export_keys($cache, $keys, $tempfile);
        }

        if ($savetempfile == true) {
            // Save file to filedir and database.
            $params = [
                'datatype' => $this->datatype['name'],
                'filename' => StorageHelper::generate_filename(),
                'tempdir' => $this->storagefolder,
                'tempfile' => $tempfile,
            ];

            if ($this->datatype['rewritable']) {
                parent::delete_files();
            }

            return StorageHelper::save_file($params);
        } else {
            return null;
        }
    }

    /**
     * Export cache keys.
     *
     * @param $cache
     * @param $keys
     * @param $tempfile
     * @return int|void
     */
    public function export_keys($cache, $keys, $tempfile) {
        $exportedrecords = 0;

        foreach ($keys as $key) {
            $exportedrecords += $this->export_key($cache, $key, $tempfile);
        }

        return $exportedrecords;
    }

    /**
     * * Export single key to temp file and clean storage.
     *
     * @param $cache
     * @param $key
     * @param $tempfile
     * @return int|void
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_key($cache, $key, $tempfile) {
        $records = $this->get_record($cache, $key);
        if (!is_array($records)) {
            // Something wrong.
            DebugHelper::error_log("IntelliData events tracking: error read event from cache,
            key:{$key}");

            return 0;
        }

        if (!count($records)) {
            return 0;
        }

        // Purge cache for specific key.
        $this->clean_storage($cache, [$key]);

        // Export records to file.
        $this->export_data($tempfile, $records);

        return count($records);
    }

    /**
     * Delete data for specific datatype.
     *
     * @param null $params
     * @return int|void
     * @throws \dml_exception
     */
    public function delete_files($params = null) {
        $cache = $this->get_cache_storage();
        $keys = $this->get_datatype_keys($cache);

        $this->clean_storage($cache, $keys);

        return parent::delete_files($params);
    }

    /**
     * Delete data from storage.
     *
     * @param $cache
     * @param $keys
     */
    public function clean_storage($cache, $keys) {
        if (count($keys)) {
            foreach ($keys as $key) {
                if ($cache->has($key)) {
                    $cache->delete($key);
                }
            }
        }
    }

    /**
     * Export data to files.
     *
     * @param $tempfile
     * @param $records
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_data($tempfile, $records) {
        $writerecordslimits = (int)SettingsHelper::get_setting('migrationwriterecordslimit');
        $data = [];

        if (count($records)) {
            $i = 0;
            $countrecords = 0;

            foreach ($records as $record) {
                $data[] = $record;

                if ($i >= $writerecordslimits) {
                    mtrace("Complete $countrecords records.");
                    // Save data into the file.
                    StorageHelper::save_in_file($tempfile, implode(PHP_EOL, $data));
                    $data = [];
                    $i = 0;
                }
                $i++;
                $countrecords++;
            }

            StorageHelper::save_in_file($tempfile, implode(PHP_EOL, $data));
        }
    }
}
