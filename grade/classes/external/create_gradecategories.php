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

namespace core_grades\external;

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use external_warnings;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/gradelib.php");
require_once("$CFG->dirroot/grade/edit/tree/lib.php");

/**
 * Create gradecategories webservice.
 *
 * @package    core_grades
 * @copyright  2021 Peter Burnett <peterburnett@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.11
 */
class create_gradecategories extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.11
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'id of course', VALUE_REQUIRED),
                'categories' => new external_multiple_structure(
                    new external_single_structure([
                        'fullname' => new external_value(PARAM_TEXT, 'fullname of category', VALUE_REQUIRED),
                        'options' => new external_single_structure([
                            'aggregation' => new external_value(PARAM_INT, 'aggregation method', VALUE_OPTIONAL),
                            'aggregateonlygraded' => new external_value(PARAM_BOOL, 'exclude empty grades', VALUE_OPTIONAL),
                            'aggregateoutcomes' => new external_value(PARAM_BOOL, 'aggregate outcomes', VALUE_OPTIONAL),
                            'droplow' => new external_value(PARAM_INT, 'drop low grades', VALUE_OPTIONAL),
                            'itemname' => new external_value(PARAM_TEXT, 'the category total name', VALUE_OPTIONAL),
                            'iteminfo' => new external_value(PARAM_TEXT, 'the category iteminfo', VALUE_OPTIONAL),
                            'idnumber' => new external_value(PARAM_TEXT, 'the category idnumber', VALUE_OPTIONAL),
                            'gradetype' => new external_value(PARAM_INT, 'the grade type', VALUE_OPTIONAL),
                            'grademax' => new external_value(PARAM_INT, 'the grade max', VALUE_OPTIONAL),
                            'grademin' => new external_value(PARAM_INT, 'the grade min', VALUE_OPTIONAL),
                            'gradepass' => new external_value(PARAM_INT, 'the grade to pass', VALUE_OPTIONAL),
                            'display' => new external_value(PARAM_INT, 'the display type', VALUE_OPTIONAL),
                            'decimals' => new external_value(PARAM_INT, 'the decimal count', VALUE_OPTIONAL),
                            'hiddenuntil' => new external_value(PARAM_INT, 'grades hidden until', VALUE_OPTIONAL),
                            'locktime' => new external_value(PARAM_INT, 'lock grades after', VALUE_OPTIONAL),
                            'weightoverride' => new external_value(PARAM_BOOL, 'weight adjusted', VALUE_OPTIONAL),
                            'aggregationcoef2' => new external_value(PARAM_RAW, 'weight coefficient', VALUE_OPTIONAL),
                            'parentcategoryid' => new external_value(PARAM_INT, 'The parent category id', VALUE_OPTIONAL),
                            'parentcategoryidnumber' => new external_value(PARAM_TEXT,
                                'the parent category idnumber', VALUE_OPTIONAL),
                        ], 'optional category data', VALUE_DEFAULT, []),
                    ], 'Category to create', VALUE_REQUIRED)
                , 'Categories to create', VALUE_REQUIRED)
            ]
        );
    }

    /**
     * Creates gradecategories inside of the specified course.
     *
     * @param int $courseid the courseid to create the gradecategory in.
     * @param array $categories the categories to create.
     * @return array array of created categoryids and warnings.
     * @since Moodle 3.11
     */
    public static function execute(int $courseid, array $categories): array {
        $params = self::validate_parameters(self::execute_parameters(),
            ['courseid' => $courseid, 'categories' => $categories]);

        // Now params are validated, update the references.
        $courseid = $params['courseid'];
        $categories = $params['categories'];

        // Check that the context and permissions are OK.
        $context = \context_course::instance($courseid);
        self::validate_context($context);
        require_capability('moodle/grade:manage', $context);

        return self::create_gradecategories_from_data($courseid, $categories);
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 3.11
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'categoryids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'created cateogry ID')
            ),
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Takes an array of categories and creates the inside the category tree for the supplied courseid.
     *
     * @param int $courseid the courseid to create the categories inside of.
     * @param array $categories the categories to create.
     * @return array array of results and warnings.
     */
    public static function create_gradecategories_from_data(int $courseid, array $categories): array {
        global $CFG, $DB;

        $defaultparentcat = \grade_category::fetch_course_category($courseid);
        // Setup default data so WS call needs to contain only data to set.
        // This is not done in the Parameters, so that the array of options can be optional.
        $defaultdata = [
            'aggregation' => grade_get_setting($courseid, 'aggregation', $CFG->grade_aggregation, true),
            'aggregateonlygraded' => 1,
            'aggregateoutcomes' => 0,
            'droplow' => 0,
            'grade_item_itemname' => '',
            'grade_item_iteminfo' => '',
            'grade_item_idnumber' => '',
            'grade_item_gradetype' => GRADE_TYPE_VALUE,
            'grade_item_grademax' => 100,
            'grade_item_grademin' => 1,
            'grade_item_gradepass' => 1,
            'grade_item_display' => GRADE_DISPLAY_TYPE_DEFAULT,
            // Hack. This must be -2 to use the default setting.
            'grade_item_decimals' => -2,
            'grade_item_hiddenuntil' => 0,
            'grade_item_locktime' => 0,
            'grade_item_weightoverride' => 0,
            'grade_item_aggregationcoef2' => 0,
            'parentcategory' => $defaultparentcat->id
        ];

        // Most of the data items need boilerplate prepended. These are the exceptions.
        $ignorekeys = [
            'aggregation',
            'aggregateonlygraded',
            'aggregateoutcomes',
            'droplow',
            'parentcategoryid',
            'parentcategoryidnumber'
        ];

        $createdcats = [];
        foreach ($categories as $category) {
            // Setup default data so WS call needs to contain only data to set.
            // This is not done in the Parameters, so that the array of options can be optional.
            $data = $defaultdata;
            $data['fullname'] = $category['fullname'];

            foreach ($category['options'] as $key => $value) {
                if (!in_array($key, $ignorekeys)) {
                    $fullkey = 'grade_item_' . $key;
                    $data[$fullkey] = $value;
                } else {
                    $data[$key] = $value;
                }
            }

            // Handle parent category special case.
            // This figures the parent category id from the provided id OR idnumber.
            if (array_key_exists('parentcategoryid', $category['options']) && $parentcat = $DB->get_record('grade_categories',
                    ['id' => $category['options']['parentcategoryid'], 'courseid' => $courseid])) {
                $data['parentcategory'] = $parentcat->id;
            } else if (array_key_exists('parentcategoryidnumber', $category['options']) &&
                    $parentcatgradeitem = $DB->get_record('grade_items', [
                        'itemtype' => 'category',
                        'courseid' => $courseid,
                        'idnumber' => $category['options']['parentcategoryidnumber']
                    ], '*', IGNORE_MULTIPLE)) {
                if ($parentcat = $DB->get_record('grade_categories',
                        ['courseid' => $courseid, 'id' => $parentcatgradeitem->iteminstance])) {
                    $data['parentcategory'] = $parentcat->id;
                }
            }

            // Create new gradecategory item.
            $gradecategory = new \grade_category(['courseid' => $courseid], false);
            $gradecategory->apply_default_settings();
            $gradecategory->apply_forced_settings();

            // Data Validation.
            if (array_key_exists('grade_item_gradetype', $data) and $data['grade_item_gradetype'] == GRADE_TYPE_SCALE) {
                if (empty($data['grade_item_scaleid'])) {
                    $warnings[] = ['item' => 'scaleid', 'warningcode' => 'invalidscale',
                        'message' => get_string('missingscale', 'grades')];
                }
            }
            if (array_key_exists('grade_item_grademin', $data) and array_key_exists('grade_item_grademax', $data)) {
                if (($data['grade_item_grademax'] != 0 OR $data['grade_item_grademin'] != 0) AND
                    ($data['grade_item_grademax'] == $data['grade_item_grademin'] OR
                    $data['grade_item_grademax'] < $data['grade_item_grademin'])) {
                    $warnings[] = ['item' => 'grademax', 'warningcode' => 'invalidgrade',
                        'message' => get_string('incorrectminmax', 'grades')];
                }
            }

            if (!empty($warnings)) {
                return ['categoryids' => [], 'warnings' => $warnings];
            }

            // Now call the update function with data. Transactioned so the gradebook isn't broken on bad data.
            // This is done per-category so that children can correctly discover the parent categories.
            try {
                $transaction = $DB->start_delegated_transaction();
                \grade_edit_tree::update_gradecategory($gradecategory, (object) $data);
                $transaction->allow_commit();
                $createdcats[] = $gradecategory->id;
            } catch (\Exception $e) {
                // If the submitted data was broken for any reason.
                $warnings['database'] = $e->getMessage();
                $transaction->rollback();
                return ['warnings' => $warnings];
            }
        }

        return['categoryids' => $createdcats, 'warnings' => []];
    }
}
