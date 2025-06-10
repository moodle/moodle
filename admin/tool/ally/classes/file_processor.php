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
 * File processor for Ally.
 * @package   tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

/**
 * File processor for Ally.
 * Can be used to process individual or groups of files.
 *
 * @package   tool_ally
 * @author    Guy Thomas <citricity@gmail.com>
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_processor extends traceable_processor {

    protected static $pushtrace = [];

    public static function build_payload($event, $eventname) {
        return [local_file::to_crud($event)];
    }

    /**
     * Push file updates to Ally without batching, etc.
     *
     * @param push_file_updates $updates
     * @param $data mixed The stored file object to send
     * @param $eventname
     * @return bool Successfully pushed file.
     * @throws \coding_exception
     */
    public static function push_update(push_updates $updates, $data, $eventname) {
        // Ignore draft files and files in the recycle bin.
        $filearea = $data->get_filearea();
        if ($filearea === 'draft' || $filearea === 'recyclebin_course') {
            return false;
        }
        $payload = [local_file::to_crud($data)];

        if (PHPUNIT_TEST) {
            if (!isset(static::$pushtrace[$eventname])) {
                static::$pushtrace[$eventname] = [];
            }
            static::$pushtrace[$eventname][] = $payload;

            // If we aren't using a mock version of $updates service then return now.
            if ($updates instanceof \Prophecy\Prophecy\ProphecySubjectInterface) {
                $updates->send($payload);
            }
            return true; // Return true always for PHPUNIT_TEST.
        }

        $updates->send($payload);
        return true;
    }

    /**
     * Get ally config.
     * @return null|push_config
     */
    public static function get_config($reset = false) {
        static $config = null;
        if ($config === null || PHPUNIT_TEST || $reset) {
            $config = new push_config();
        }
        return $config;
    }

    /**
     * Push updates for files.
     * @param \stored_file $file;
     * @param bool $validate If false, skip the validator step.
     * @return bool Successfully pushed file.
     * @throws \Exception
     */
    public static function push_file_update(\stored_file $file, bool $validate = true) {
        $config = self::get_config();
        if (!$config->is_valid() || $config->is_cli_only()) {
            return false;
        }

        if ($validate && !local_file::file_validator()->validate_stored_file($file, null, false)) {
            return false;
        }

        local_file::remove_file_from_deletion_queue($file);

        $updates = new push_file_updates($config);
        return self::push_update($updates, $file, 'file_created');
    }
}
