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
 * A test guide class fixture.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Convenience class to create marking guides.
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_guide {

    /** @var array $criteria Criteria for this marking guide. */
    protected $criteria = [];
    /** @var context $context The context that this marking guide is used in. */
    protected $context;
    /** @var string $name The name of this marking guide. */
    protected $name;
    /** @var string $text A description of this marking guide. */
    protected $text;
    /** @var integer $criterionid The current id for the criterion. */
    protected $criterionid = 0;
    /** @var integer $sortorder The current id for the sort order. */
    protected $sortorder = 0;
    /** @var gradingform_controller The grading form controller. */
    protected $controller;

    /** @var grading_manager $manager The grading manager to handle creating the real marking guide. */
    public $manager;

    /**
     * The constuctor for this test_guide object.
     *
     * @param context $context The context that this marking guide is used in.
     * @param string $name The name of the marking guide.
     * @param string $text The description of the marking guide.
     */
    public function __construct($context, $name, $text) {
        $this->context = $context;
        $this->name = $name;
        $this->text = $text;
        $this->manager = get_grading_manager();
        $this->manager->set_context($context);
        $this->manager->set_component('mod_assign');
        $this->manager->set_area('submission');
    }

    /**
     * Uses the appropriate data and APIs to create a marking guide.
     */
    public function create_guide() {

        $data = (object) [
            'areaid' => $this->context->id,
            'returnurl' => '',
            'name' => $this->name,
            'description_editor' => [
                'text' => $this->text,
                'format' => 1,
                'itemid' => 1
            ],
            'guide' => [
                'criteria' => $this->criteria,
                'options' => [
                    'alwaysshowdefinition' => 1,
                    'showmarkspercriterionstudents' => 1
                ],
                'comments' => []
            ],
            'saveguide' => 'Continue',
            'status' => 20
        ];

        $this->controller = $this->manager->get_controller('guide');
        $this->controller->update_definition($data);
    }

    /**
     * Adds criteria to the marking guide.
     *
     * @param string $shortname The shortname for the criterion.
     * @param string $description The description for the criterion.
     * @param string $descriptionmarkers The description for the marker for this criterion.
     * @param int $maxscore The maximum score possible for this criterion.
     */
    public function add_criteria($shortname, $description, $descriptionmarkers, $maxscore) {
        $this->criterionid++;
        $this->sortorder++;
        $this->criteria['NEWID' . $this->criterionid] = [
            'sortorder' => $this->sortorder,
            'shortname' => $shortname,
            'description' => $description,
            'descriptionmarkers' => $descriptionmarkers,
            'maxscore' => $maxscore
        ];
    }

    /**
     * Update the grade for the item provided.
     * Keep the gradeinfo array in the same order as the definition of the criteria.
     * The array should be [['remark' => remark, 'score' => intvalue],['remark' => remark, 'score' => intvalue]]
     * for a guide that has two criteria.
     *
     * @param  int $userid The user we are updating.
     * @param  int $itemid The itemid that the grade will be for
     * @param  array $gradeinfo Comments and grades for the grade.
     * @return gradingform_guide_instance The created instance associated with the grade created.
     */
    public function grade_item(int $userid, int $itemid, array $gradeinfo) : gradingform_guide_instance {
        global $DB;

        if (!isset($this->controller)) {
            throw new Exception("Please call create_guide before calling this method", 1);
        }

        $instance = $this->controller->create_instance($userid, $itemid);

        // I need the ids for the criteria and there doesn't seem to be a nice method to get it.
        $criteria = $DB->get_records('gradingform_guide_criteria');
        $data = ['criteria' => []];
        $i = 0;
        // The sort order should keep everything here in order.
        foreach ($criteria as $key => $value) {
            $data['criteria'][$key]['remark'] = $gradeinfo[$i]['remark'];
            $data['criteria'][$key]['remarkformat'] = 0;
            $data['criteria'][$key]['score'] = $gradeinfo[$i]['score'];
            $i++;
        }
        $data['itemid'] = $itemid;

        // Update this instance with data.
        $instance->update($data);
        return $instance;
    }
}
