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
 * Traceable processor for Ally.
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

use tool_ally\logging\logger;
use tool_ally\models\component_content;

/**
 * Traceable processor for Ally.
 * Can be used to allow an Ally processor to be traced during unit tests.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class traceable_processor {

    protected static $pushtrace = [];

    /**
     * Get push trace for PHP unit testing.
     * @param null|string $eventname
     * @param string $regex
     * @param string $eventkey
     * @return bool|mixed
     */
    public static function get_push_traces($eventname = null, $regex = null, $eventkey = null) {
        if ($eventname === null) {
            return static::$pushtrace;
        }
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('This is only supposed to be used for PHP Unit testing!');
        }
        if (isset(static::$pushtrace[$eventname])) {
            if ($regex === null || $eventkey === null) {
                return static::$pushtrace[$eventname];
            } else {
                foreach (static::$pushtrace[$eventname] as &$pushtrace) {
                    $pushtrace = array_filter($pushtrace, function($row) use($regex, $eventkey) {
                        return preg_match($regex, $row[$eventkey]) === 1;
                    });
                }
                return $pushtrace;
            }
        }
        return false;
    }

    public static function clear_push_traces() {
        static::$pushtrace = [];
    }

    /**
     * @param mixed $data
     * @param string $eventname
     * @return array
     */
    abstract public static function build_payload($data, $eventname);

    /**
     * Push content update to Ally without batching, etc.
     * @param push_updates $updates
     * @param mixed $data
     * @param string $eventname
     * @return bool true on success
     */
    public static function push_update(push_updates $updates, $data, $eventname) {
        $payload = static::build_payload($data, $eventname);

        if (empty($payload)) {
            return true;
        }

        if (PHPUNIT_TEST) {
            if (!isset(static::$pushtrace[$eventname])) {
                static::$pushtrace[$eventname] = [];
            }
            static::$pushtrace[$eventname][] = $payload;

            // If we aren't using a mock version of $updates service then return now.
            if ($updates instanceof \PHPUnit\Framework\MockObject\MockObject) {
                $updates->send($payload);
            }
            return true; // Return true always for PHPUNIT_TEST.
        }
        return $updates->send($payload);
    }

    /**
     * Get ally config.
     * @param boolean $reset
     * @return null|push_config
     */
    public static function get_config($reset = false) {
        static $config = null;
        if ($config === null || $reset) {
            $config = new push_config();
        }
        return $config;
    }

}
