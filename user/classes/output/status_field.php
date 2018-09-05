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
 * Class containing the data necessary for rendering the status field in the course participants page.
 *
 * @package    core_user
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use stdClass;
use templatable;
use user_enrolment_action;

/**
 * Class containing the data for the status field.
 *
 * @package    core_user
 * @copyright  2017 Jun Pataleta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class status_field implements renderable, templatable {

    /** Active user enrolment status constant. */
    const STATUS_ACTIVE = 0;

    /** Suspended user enrolment status constant. */
    const STATUS_SUSPENDED = 1;

    /** Not current user enrolment status constant. */
    const STATUS_NOT_CURRENT = 2;

    /** @var string $enrolinstancename The enrolment instance name. */
    protected $enrolinstancename;

    /** @var string $coursename The course's full name. */
    protected $coursename;

    /** @var string $fullname The user's full name. */
    protected $fullname;

    /** @var string $status The user enrolment status. */
    protected $status;

    /** @var int $timestart The timestamp when the user's enrolment starts. */
    protected $timestart;

    /** @var int $timeend The timestamp when the user's enrolment ends. */
    protected $timeend;

    /** @var user_enrolment_action[] $enrolactions Array of enrol action objects for the given enrolment method. */
    protected $enrolactions;

    /** @var bool $statusactive Indicates whether a user enrolment status should be rendered as active. */
    protected $statusactive = false;

    /** @var bool $statusactive Indicates whether a user enrolment status should be rendered as suspended. */
    protected $statussuspended = false;

    /** @var bool $statusactive Indicates whether a user enrolment status should be rendered as not current. */
    protected $statusnotcurrent = false;

    /**
     * status_field constructor.
     *
     * @param string $enrolinstancename The enrolment instance name.
     * @param string $coursename The course's full name.
     * @param string $fullname The user's full name.
     * @param string $status The user enrolment status.
     * @param int|null $timestart The timestamp when the user's enrolment starts.
     * @param int|null $timeend The timestamp when the user's enrolment ends.
     * @param user_enrolment_action[] $enrolactions Array of enrol action objects for the given enrolment method.
     */
    public function __construct($enrolinstancename, $coursename, $fullname, $status, $timestart = null, $timeend = null,
                                $enrolactions = []) {
        $this->enrolinstancename = $enrolinstancename;
        $this->coursename = $coursename;
        $this->fullname = $fullname;
        $this->status = $status;
        $this->timestart = $timestart;
        $this->timeend = $timeend;
        $this->enrolactions = $enrolactions;
    }

    /**
     * Function to export the renderer data in a format that is suitable for a
     * mustache template. This means:
     * 1. No complex types - only stdClass, array, int, string, float, bool
     * 2. Any additional info that is required for the template is pre-calculated (e.g. capability checks).
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->enrolinstancename = $this->enrolinstancename;
        $data->coursename = $this->coursename;
        $data->fullname = $this->fullname;
        $data->status = $this->status;
        $data->active = $this->statusactive;
        $data->suspended = $this->statussuspended;
        $data->notcurrent = $this->statusnotcurrent;

        if ($this->timestart) {
            $data->timestart = userdate($this->timestart);
        }
        if ($this->timeend) {
            $data->timeend = userdate($this->timeend);
        }
        $data->enrolactions = [];

        foreach ($this->enrolactions as $enrolaction) {
            $action = new stdClass();
            $action->url = $enrolaction->get_url()->out(false);
            $action->icon = $output->render($enrolaction->get_icon());
            $action->attributes = [];
            foreach ($enrolaction->get_attributes() as $name => $value) {
                $attribute = (object) [
                    'name' => $name,
                    'value' => $value
                ];
                $action->attributes[] = $attribute;
            }
            $data->enrolactions[] = $action;
        }

        return $data;
    }

    /**
     * Status setter.
     *
     * @param int $status The user enrolment status representing one of this class' STATUS_* constants.
     * @return status_field This class' instance. Useful for chaining.
     */
    public function set_status($status = self::STATUS_ACTIVE) {
        $this->statusactive = $status == static::STATUS_ACTIVE;
        $this->statussuspended = $status == static::STATUS_SUSPENDED;
        $this->statusnotcurrent = $status == static::STATUS_NOT_CURRENT;

        return $this;
    }
}
