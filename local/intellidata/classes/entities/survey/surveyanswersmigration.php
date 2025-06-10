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
 * Class for migration Survey Answers.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\survey;

/**
 * Class for migration Survey Answers.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2022 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class surveyanswersmigration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\survey\surveyanswers';
    /** @var string */
    public $table = 'survey_answers';
    /** @var string */
    public $tablealias = 'sa';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        $where = 'sa.id > 0';
        $select = ($count) ?
            "SELECT COUNT(sa.id) as recordscount" :
            "SELECT sa.*, sq.text as questiontext, sq.type as questiontype";

        $sql = "$select
                  FROM {" . $this->table . "} sa
             LEFT JOIN {survey_questions} sq ON sq.id = sa.question
                 WHERE $where";

        if ($condition) {
            $sql .= " AND " . $condition;
        }

        return [$sql, $conditionparams];
    }

    /**
     * Prepare records for export.
     *
     * @param $records
     * @return \Generator
     * @throws \coding_exception
     */
    public function prepare_records_iterable($records) {
        foreach ($records as $sanswer) {
            if (!empty($sanswer->questiontext)) {
                $sanswer->questiontext = get_string($sanswer->questiontext, "mod_survey");
            }

            $entity = new $this->entity($sanswer);
            $data = $entity->export();
            yield $data;
        }
    }
}
