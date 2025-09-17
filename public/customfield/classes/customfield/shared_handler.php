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

declare(strict_types=1);

namespace core_customfield\customfield;

use core\context;
use core\context\system;
use core\exception\coding_exception;
use core\url;
use core_customfield\field_controller;

/**
 * Shared handler for custom fields
 *
 * @package    core_customfield
 * @copyright  2025 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shared_handler extends \core_customfield\handler {
    /**
     * @var shared_handler|null
     */
    protected static ?shared_handler $singleton = null;

    /**
     * Returns a singleton
     *
     * @param int $itemid
     * @return self
     */
    public static function create(int $itemid = 0): self {
        if (static::$singleton === null) {
            self::$singleton = new static($itemid);
        }
        return self::$singleton;
    }

    /**
     * Run reset code after unit tests to reset the singleton usage.
     */
    public static function reset_caches(): void {
        if (!PHPUNIT_TEST) {
            throw new coding_exception('This feature is only intended for use in unit tests');
        }

        static::$singleton = null;
    }

    /**
     * The current user can configure custom fields on this component.
     *
     * @return bool true if the current user can configure custom fields, false otherwise
     */
    public function can_configure(): bool {
        return has_capability('moodle/customfield:configureshared', $this->get_configuration_context());
    }

    /**
     * The current user can edit custom fields on the given report.
     *
     * @param field_controller $field
     * @param int $instanceid id of the report to test edit permission
     * @return bool true if the current user can edit custom fields, false otherwise
     */
    public function can_edit(field_controller $field, int $instanceid = 0): bool {
        return has_capability('moodle/customfield:configureshared', $this->get_configuration_context());
    }

    /**
     * The current user can view custom fields on the given report.
     *
     * @param field_controller $field
     * @param int $instanceid id of the report to test edit permission
     * @return bool true if the current user can edit custom fields, false otherwise
     */
    public function can_view(field_controller $field, int $instanceid): bool {
        return true;
    }

    /**
     * Context that should be used for new categories created by this handler
     *
     * @return context the context for configuration
     */
    public function get_configuration_context(): context {
        return system::instance();
    }

    /**
     * URL for configuration of the fields on this handler.
     *
     * @return url The URL to configure custom fields for this component
     */
    public function get_configuration_url(): url {
        return new url('/customfield/customfield.php');
    }

    /**
     * Returns the context for the data associated with the given instanceid.
     *
     * @param int $instanceid id of the record to get the context for
     * @return context the context for the given record
     */
    public function get_instance_context(int $instanceid = 0): context {
        return system::instance();
    }
}
