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
 * Behat search-related step definitions.
 *
 * @package core_search
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL used, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Moodle\BehatExtension\Exception\SkippedException;

/**
 * Behat search-related step definitions.
 *
 * @package core_search
 * @category test
 * @copyright 2017 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_search extends behat_base {
    /**
     * Create event when starting on the front page.
     *
     * @Given /^I search for "(?P<query>[^"]*)" using the header global search box$/
     * @param string $query Query to search for
     */
    public function i_search_for_using_the_header_global_search_box($query) {
        // Click the search icon.
        $this->execute("behat_general::i_click_on", [get_string('togglesearch', 'core'), 'button']);

        // Set the field.
        $this->execute('behat_forms::i_set_the_field_to', ['q', $query]);

        // Submit the form.
        $this->execute("behat_general::i_click_on_in_the",
            [get_string('search', 'core'), 'button', '#usernavigation', 'css_element']);
    }

    /**
     * Sets results which will be returned for the next search. It will only return links to
     * activities at present.
     *
     * @Given /^global search expects the query "(?P<query>[^"]*)" and will return:$/
     * @param string $query Expected query value (just used to check the query passed to the engine)
     * @param TableNode $data Data rows
     */
    public function global_search_expects_the_query_and_will_return($query, TableNode $data) {
        global $DB;
        $outdata = new stdClass();
        $outdata->query = $query;
        $outdata->results = [];
        foreach ($data->getHash() as $rowdata) {
            // Check and get the data from the user-entered row.
            $input = [
                'type' => '',
                'idnumber' => '',
                'title' => '',
                'content' => '',
                'modified' => ''
            ];
            foreach ($rowdata as $key => $value) {
                if (!array_key_exists($key, $input)) {
                    throw new Exception('Field ' . $key . '" does not exist');
                }
                $input[$key] = $value;
            }
            foreach (['idnumber', 'type'] as $requiredfield) {
                if (!$input[$requiredfield]) {
                    throw new Exception('Must specify required field: ' . $requiredfield);
                }
            }

            // Check type (we only support activity at present, this could be extended to allow
            // faking other types of search results such as a user, course, or forum post).
            if ($input['type'] !== 'activity') {
                throw new Exception('Unsupported type: ' . $input['type']);
            }

            // Find the specified activity.
            $idnumber = $input['idnumber'];
            $cmid = $DB->get_field('course_modules', 'id', ['idnumber' => $idnumber], IGNORE_MISSING);
            if (!$cmid) {
                throw new Exception('Cannot find activity with idnumber: ' . $idnumber);
            }
            list ($course, $cm) = get_course_and_cm_from_cmid($cmid);
            $rec = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
            $context = \context_module::instance($cm->id);

            // Set up the internal fields used in creating the search document.
            $out = new stdClass();
            $out->itemid = $cm->instance;
            $out->componentname = 'mod_' . $cm->modname;
            $out->areaname = 'activity';
            $out->fields = new stdClass();
            $out->fields->contextid = $context->id;
            $out->fields->courseid = $course->id;
            if ($input['title']) {
                $out->fields->title = $input['title'];
            } else {
                $out->fields->title = $cm->name;
            }
            if ($input['content']) {
                $out->fields->content = $input['content'];
            } else {
                $out->fields->content = content_to_text($rec->intro, $rec->introformat);
            }
            if ($input['modified']) {
                $out->fields->modified = strtotime($input['modified']);
            } else {
                $out->fields->modified = $cm->added;
            }
            $out->extrafields = new stdClass();
            $out->extrafields->coursefullname = $course->fullname;

            $outdata->results[] = $out;
        }

        set_config('behat_fakeresult', json_encode($outdata), 'core_search');
    }

    /**
     * Updates the global search index to take account of any added activities.
     *
     * @Given /^I update the global search index$/
     * @throws moodle_exception
     */
    public function i_update_the_global_search_index() {
        \core_search\manager::instance()->index(false);
    }

    /**
     * This step looks to see if Solr is installed or skip the rest of the scenario otherwise
     *
     * @Given /^solr is installed/
     */
    public function solr_is_installed() {
        if (!function_exists('solr_get_version')) {
            throw new SkippedException('Skipping this scenario because Solr is not installed.');
        }
    }
}
