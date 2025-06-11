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
 * Custom step to create course categories with specifc data
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\DriverException as DriverException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

require_once(__DIR__ . '/../../../../course/tests/behat/behat_course.php');

/**
 * Overrides to make behat course steps work with Snap.
 *
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class behat_theme_snap_behat_course_categories extends behat_base {

    /**
     * Creates the specified element. More info about available elements in http://docs.moodle.org/dev/Acceptance_testing#Fixtures.
     *
     * @Given /^I create the following course categories:$/
     *
     * @throws Exception
     * @throws PendingException
     * @param TableNode $data
     */
    public function i_create_the_following_course_categories(TableNode $data) {
        global $DB;

        // Now that we need them require the data generators.

        $requiredfields = array('id', 'idnumber');
        $elementname = 'categories';
        $categories = array();
        foreach ($data->getHash() as $elementdata) {

            // Check if all the required fields are there.
            foreach ($requiredfields as $requiredfield) {
                if (!isset($elementdata[$requiredfield])) {
                    throw new Exception($elementname . ' requires the field ' . $requiredfield . ' to be specified');
                }
            }
            $categories[] = $elementdata;
        }
        foreach ($categories as $category) {
            $category['parent'] = $DB->get_field('course_categories', 'id', array('idnumber' => $category['category']));
            self::create_category($category);
        }
    }

    /**
     * Creates a course category, it is pretty similar to the core function, but it allows you to set ID records.
     * @param $data
     * @param null $editoroptions
     * @return bool
     * @throws moodle_exception
     */
    public static function create_category($data, $editoroptions = null) {
        global $DB, $CFG;

        $data = (object)$data;
        $newcategory = new stdClass();
        $newcategory->id = $data->id;
        $newcategory->descriptionformat = FORMAT_MOODLE;
        $newcategory->description = '';
        // Copy all description* fields regardless of whether this is form data or direct field update.
        foreach ($data as $key => $value) {
            if (preg_match("/^description/", $key)) {
                $newcategory->$key = $value;
            }
        }

        if (empty($data->name)) {
            throw new moodle_exception('categorynamerequired');
        }
        if (core_text::strlen($data->name) > 255) {
            throw new moodle_exception('categorytoolong');
        }
        $newcategory->name = $data->name;

        // Validate and set idnumber.
        if (isset($data->idnumber)) {
            if (core_text::strlen($data->idnumber) > 100) {
                throw new moodle_exception('idnumbertoolong');
            }
            if (strval($data->idnumber) !== '' && $DB->record_exists('course_categories', array('idnumber' => $data->idnumber))) {
                throw new moodle_exception('categoryidnumbertaken');
            }
            $newcategory->idnumber = $data->idnumber;
        }

        if (isset($data->theme) && !empty($CFG->allowcategorythemes)) {
            $newcategory->theme = $data->theme;
        }

        if (empty($data->parent)) {
            $parent = core_course_category::get(0);
        } else {
            $parent = $DB->get_record('course_categories', array('id' => $data->parent));
        }
        $newcategory->parent = $parent->id;
        $newcategory->depth = $parent->depth + 1;

        // By default category is visible, unless visible = 0 is specified or parent category is hidden.
        if (isset($data->visible) && !$data->visible) {
            // Create a hidden category.
            $newcategory->visible = $newcategory->visibleold = 0;
        } else {
            // Create a category that inherits visibility from parent.
            $newcategory->visible = $parent->visible;
            // In case parent is hidden, when it changes visibility this new subcategory will automatically become visible too.
            $newcategory->visibleold = 1;
        }

        $newcategory->sortorder = 0;
        $newcategory->timemodified = time();
        $DB->insert_records('course_categories', array($newcategory), true);
            // Update path (only possible after we know the category id.
        $path = $parent->path . '/' . $newcategory->id;
        $DB->set_field('course_categories', 'path', $path, array('id' => $newcategory->id));

        // We should mark the context as dirty.
        context_coursecat::instance($newcategory->id)->mark_dirty();

        fix_course_sortorder();

        // If this is data from form results, save embedded files and update description.
        $categorycontext = context_coursecat::instance($newcategory->id);
        if ($editoroptions) {
            $newcategory = file_postupdate_standard_editor($newcategory, 'description', $editoroptions, $categorycontext,
                'coursecat', 'description', 0);

            // Update only fields description and descriptionformat.
            $updatedata = new stdClass();
            $updatedata->id = $newcategory->id;
            $updatedata->description = $newcategory->description;
            $updatedata->descriptionformat = $newcategory->descriptionformat;
            $DB->update_record('course_categories', $updatedata);
        }

        $event = \core\event\course_category_created::create(array(
            'objectid' => $newcategory->id,
            'context' => $categorycontext,
        ));
        $event->trigger();

        cache_helper::purge_by_event('changesincoursecat');

        return true;
    }
}
