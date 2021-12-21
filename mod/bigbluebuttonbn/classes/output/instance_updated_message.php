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

namespace mod_bigbluebuttonbn\output;

use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\local\helpers\roles;
use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderable for the instance notification updated message
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Darko Miletic  (darko.miletic [at] gmail [dt] com)
 */
class instance_updated_message implements renderable, templatable {

    /** @var int The activity was created */
    const TYPE_CREATED = 0;

    /** @var int The activity was updated */
    const TYPE_UPDATED = 1;

    /**
     * @var instance $instance
     */
    protected $instance;

    /**
     * Instance updated constructor
     *
     * @param instance $instance
     * @param int $type
     */
    public function __construct(instance $instance, int $type) {
        $this->instance = $instance;
        $this->type = $type;
    }

    /**
     * Defer to template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        return (object) [
            'is_update' => $this->type === self::TYPE_UPDATED,
            'is_create' => $this->type === self::TYPE_CREATED,
            'name' => $this->instance->get_meeting_name(),
            'link' => $this->instance->get_view_url()->out(),
            'description' => $this->instance->get_meeting_description(),
            'openingtime' => $this->instance->get_instance_var('openingtime'),
            'closingtime' => $this->instance->get_instance_var('closingtime'),
        ];
    }
}
