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
 * Class for preparing data for Course Completions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\usergrades;


/**
 * Class for preparing data for Course Completions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class usergrade extends \local_intellidata\entities\entity {

    /**
     * Entity type.
     */
    const TYPE = 'usergrades';

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'id' => [
                'type' => PARAM_INT,
                'description' => 'Grade ID.',
                'default' => 0,
            ],
            'gradeitemid' => [
                'type' => PARAM_INT,
                'description' => 'Grade Item ID.',
                'default' => 0,
            ],
            'userid' => [
                'type' => PARAM_INT,
                'description' => 'User ID.',
                'default' => 0,
            ],
            'letter' => [
                'type' => PARAM_RAW_TRIMMED,
                'description' => 'Letter Grade.',
                'default' => 0,
            ],
            'score' => [
                'type' => PARAM_RAW,
                'description' => 'Percentage Grade.',
                'default' => 0,
            ],
            'point' => [
                'type' => PARAM_RAW,
                'description' => 'Real Grade.',
                'default' => 0,
            ],
            'feedback' => [
                'type' => PARAM_RAW,
                'description' => 'Grade Comment.',
                'default' => '',
            ],
            'hidden' => [
                'type' => PARAM_INT,
                'description' => 'Grade Status.',
                'default' => 0,
            ],
            'timemodified' => [
                'type' => PARAM_INT,
                'description' => 'Last Graded Time.',
                'default' => 0,
            ],
            'usermodified' => [
                'type' => PARAM_INT,
                'description' => 'User Grader ID.',
                'default' => 0,
            ],
        ];
    }

    /**
     * Prepare entity data for export.
     *
     * @param \stdClass $object
     * @param array $fields
     * @return null
     * @throws invalid_persistent_exception
     */
    public static function prepare_export_data($object, $fields = [], $table = '') {
        global $CFG;

        require_once($CFG->libdir . '/gradelib.php');

        $gradeitem = \grade_item::fetch(['id' => $object->itemid]);
        $data = new \stdClass();
        $data->id = $object->id;
        $data->gradeitemid = $object->itemid;
        $data->userid = $object->userid;
        $data->usermodified = $object->usermodified;
        $data->feedback = !empty($object->feedback) ? $object->feedback : '';
        $data->hidden = $object->hidden;
        $data->timemodified = $object->timemodified;

        if ($gradeitem) {
            // Each user have own grade max and grade min.
            $gradeitem->grademax = $object->rawgrademax;
            $gradeitem->grademin = $object->rawgrademin;

            $score = grade_format_gradevalue($object->finalgrade, $gradeitem, true, GRADE_DISPLAY_TYPE_PERCENTAGE);
            $displaytype = $gradeitem->gradetype == GRADE_TYPE_SCALE ? GRADE_DISPLAY_TYPE_REAL : GRADE_DISPLAY_TYPE_LETTER;
            $data->letter = grade_format_gradevalue($object->finalgrade, $gradeitem, true, $displaytype);
            $data->score = str_replace(' %', '', $score);
            $data->point = ($gradeitem->gradetype == GRADE_TYPE_SCALE) ?
                $gradeitem->bounded_grade($object->finalgrade) :
                grade_format_gradevalue($object->finalgrade, $gradeitem, true, GRADE_DISPLAY_TYPE_REAL);
        } else {
            if (CLI_SCRIPT) {
                mtrace('Not found gradeitem: ' . $object->itemid);
            }
            $data->letter = null;
            $data->score = null;
            $data->point = null;
        }

        return $data;
    }
}
