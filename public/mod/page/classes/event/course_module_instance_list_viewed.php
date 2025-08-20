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

namespace mod_page\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_page instance list viewed event class.
 *
 * @package    mod_page
 * @since      Moodle 2.7
 * @copyright  2013 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @deprecated This event is deprecated and will be removed in Moodle 6.0.
 * @todo       Remove class in 6.0 (MDL-86384)
 */
#[\core\attribute\deprecated(
    replacement: core\event\course_resources_list_viewed::class,
    since: '5.1',
    mdl: 'MDL-84632',
)]
class course_module_instance_list_viewed extends \core\event\course_module_instance_list_viewed {
    #[\Override]
    #[\core\attribute\deprecated(
        replacement: core\event\course_resources_list_viewed::class,
        since: '5.1',
        mdl: 'MDL-84632',
    )]
    protected function init() {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
        parent::init();
    }
}
