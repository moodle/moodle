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
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\quizquestionrelations;

use local_intellidata\helpers\DBHelper;
use local_intellidata\helpers\ParamsHelper;
use local_intellidata\services\datatypes_service;

/**
 * Class for preparing data for Activities.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizquestionrelation extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'quizquestionrelations';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Quiz Slot ID.',
                'default' => 0,
            ],
            'quizid' => [
                'type' => PARAM_INT,
                'description' => 'Quiz ID.',
                'default' => 0,
            ],
            'questionid' => [
                'type' => PARAM_INT,
                'description' => 'Question ID.',
                'default' => 0,
            ],
            'slot' => [
                'type' => PARAM_INT,
                'description' => 'Slot Number.',
                'default' => 0,
            ],
            'type' => [
                'type' => PARAM_TEXT,
                'description' => 'Question Type.',
                'default' => '',
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @param string $table
     * @return null
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        global $DB;

        $release4 = ParamsHelper::compare_release('4.0.0');

        if (!$release4) {
            $object->type = 'q';
        } else {
            $datatype = datatypes_service::get_datatype('quizquestionrelations');
            if (isset($datatype['additional_tables']) && (in_array($table, $datatype['additional_tables']))) {
                if ($table == 'question_set_references') {
                    $catidsql = DBHelper::get_operator('JSON_EXTRACT', 'qsr.filtercondition', ['path' => 'questioncategoryid']);
                    $sql = "SELECT
                            qs.id,
                            qs.quizid,
                            qc.id AS questionid,
                            qs.slot,
                            'c' AS type,
                            null as refid,
                            qsr.id as refsid
                        FROM {quiz_slots} qs
                          JOIN {question_set_references} qsr ON qsr.questionarea = 'slot' AND qsr.itemid = qs.id
                          JOIN {question_categories} qc ON qc.contextid = qsr.questionscontextid
                                                           AND qc.id = CAST({$catidsql} AS DECIMAL)
                        WHERE qsr.id=:id";
                    $params = ['id' => $object->id];
                } else {
                    $sql = "SELECT
                            qs.id,
                            qs.quizid,
                            MAX(qve.questionid) AS questionid,
                            qs.slot,
                            'q' AS type,
                            MAX(qre.id) as refid,
                            null as refsid
                        FROM {quiz_slots} qs
                          JOIN {question_references} qre ON qre.itemid = qs.id
                          JOIN {question_versions} qve ON qve.questionbankentryid = qre.questionbankentryid
                        WHERE qre.id=:id
                        GROUP BY qs.id, qs.quizid, qs.slot";
                    $params = ['id' => $object->id];
                }
                $record = $DB->get_record_sql($sql, $params);

                if (isset($record)) {
                    $object = $record;
                }
            } else {
                $qrexist = $DB->record_exists_sql('SELECT 1
                                     FROM {question_references} qre
                                     JOIN {question_versions} qve ON qve.questionbankentryid = qre.questionbankentryid');
                if ($qrexist) {
                    $object->type = 'q';
                } else {
                    $object->type = 'c';
                }
            }
        }

        return $object;
    }
}
