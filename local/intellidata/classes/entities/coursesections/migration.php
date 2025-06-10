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
 * Class for migration Course Sections.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2023 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\coursesections;

/**
 * Class for migration Course Sections.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2023 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\coursesections\sections';
    /** @var string */
    public $eventname = '\core\event\course_section_created';
    /** @var string */
    public $table = 'course_sections';

    /**
     * Prepare records for export.
     *
     * @param $records
     * @return \Generator
     * @throws \coding_exception
     */
    public function prepare_records_iterable($records) {
        foreach ($records as $record) {
            $record = $this->entity::prepare_export_data($record);

            $entity = new $this->entity($record);
            $recorddata = $entity->export();

            yield $recorddata;
        }
    }
}
