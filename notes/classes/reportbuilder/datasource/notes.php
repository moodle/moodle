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

namespace core_notes\reportbuilder\datasource;

use lang_string;
use core_reportbuilder\datasource;
use core_reportbuilder\local\entities\{course, user};
use core_notes\reportbuilder\local\entities\note;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->dirroot}/notes/lib.php");

/**
 * Notes datasource
 *
 * @package     core_notes
 * @copyright   2022 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notes extends datasource {

    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('notes', 'core_notes');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $noteentity = new note();

        $postalias = $noteentity->get_table_alias('post');
        $this->set_main_table('post', $postalias);
        $this->add_base_condition_simple("{$postalias}.module", 'notes');

        $this->add_entity($noteentity);

        // Join the user entity to represent the note recipient.
        $recipiententity = (new user())
            ->set_entity_name('recipient')
            ->set_entity_title(new lang_string('recipient', 'core_notes'));
        $recipientalias = $recipiententity->get_table_alias('user');
        $this->add_entity($recipiententity->add_join("
            LEFT JOIN {user} {$recipientalias}
                   ON {$recipientalias}.id = {$postalias}.userid")
        );

        // Join the user entity to represent the note author. Override all entity table aliases to avoid clash with first instance.
        $authorentity = (new user())
            ->set_entity_name('author')
            ->set_entity_title(new lang_string('author', 'core_notes'))
            ->set_table_aliases([
                'user' => 'au',
                'context' => 'auctx',
            ]);
        $this->add_entity($authorentity->add_join("
            LEFT JOIN {user} au
                   ON au.id = {$postalias}.usermodified")
        );

        // Join the course entity for course notes.
        $courseentity = new course();
        $coursealias = $courseentity->get_table_alias('course');
        $this->add_entity($courseentity->add_join("
            LEFT JOIN {course} {$coursealias}
                   ON {$coursealias}.id = {$postalias}.courseid
                  AND {$postalias}.publishstate = '" . NOTES_STATE_PUBLIC . "'")
        );

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'recipient:fullname',
            'note:publishstate',
            'course:fullname',
            'note:content',
            'note:timecreated',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'recipient:fullname' => SORT_ASC,
            'note:timecreated' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'recipient:fullname',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
            'note:publishstate',
            'course:fullname',
            'recipient:fullname',
        ];
    }
}
