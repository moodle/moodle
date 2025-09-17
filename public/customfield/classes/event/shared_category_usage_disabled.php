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

namespace core_customfield\event;

use context;
use core_customfield\shared;

defined('MOODLE_INTERNAL') || die();

/**
 * Custom field shared category usage disabled event class.
 *
 * @package    core_customfield
 * @copyright  2025 David Carrillo <davidmc@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class shared_category_usage_disabled extends \core\event\base {

    /**
     * Initialise the event data.
     */
    protected function init(): void {
        $this->data['objecttable'] = 'customfield_shared';
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an instance from a shared object
     *
     * @param shared $shared
     * @param context $context
     * @return category_updated
     */
    public static function create_from_object(shared $shared, context $context): shared_category_usage_disabled {
        $eventparams = [
            'objectid' => $shared->get('id'),
            'context'  => $context,
            'other'    => [
                'categoryid' => $shared->get('categoryid'),
                'component' => $shared->get('component'),
                'area' => $shared->get('area'),
                'itemid' => $shared->get('itemid'),
            ],
        ];
        $event = self::create($eventparams);
        $event->add_record_snapshot($event->objecttable, $shared->to_record());
        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('eventsharedcategoryusagedisabled', 'core_customfield');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description(): string {
        return "The user with ID '$this->userid' disabled usage of shared category with ID '{$this->other['categoryid']}'" .
            " in '{$this->other['component']}/{$this->other['area']}'";
    }
}
