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
 * Block definition class for the block_pimenkofeaturedcourses plugin.
 *
 * @package   block_pimenkofeaturedcourses
 * @copyright Pimenko | Sylvain Revenu
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/renderer.php');

class block_pimenkofeaturedcourses extends block_base {

    /**
     * Initialises the block.
     *
     * @return void
     */
    public function init(): void {
        $this->title = get_string('pimenkofeaturedcourses', 'block_pimenkofeaturedcourses');
    }

    /**
     * Gets the block contents.
     *
     * @return string The block HTML.
     */
    public function get_content() {
        global $DB, $OUTPUT, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->footer = '';

        // Add logic here to define your template data or any other content.
        $configdata = unserialize_object(base64_decode($this->instance->configdata));

        if (!isset($configdata->courseslist)) {
            return $this->content;
        } else {
            $courseslist = [];

            foreach ($configdata->courseslist as $key => $courseid) {
                $course = get_course($courseid);
                $courseelements = new core_course_list_element($course);

                $chelper = new coursecat_helper();
                $course->summary = $chelper->get_course_formatted_summary($courseelements);

                // Get course picture.
                $coursefiles = $courseelements->get_course_overviewfiles();
                if (count($coursefiles) > 0) {
                    $file = reset($coursefiles);
                    $course->urlimg = new moodle_url(
                        '/pluginfile.php/' . $file->get_contextid() . '/course/overviewfiles/' . $file->get_source()
                    );
                }

                // Get category info.
                if (!core_course_category::can_view_course_info($course, $USER)) {
                    continue;
                }
                $category = core_course_category::get($course->category, MUST_EXIST, true, $USER);
                $course->categoryname = $category->get_formatted_name();

                // Formatted course name.
                $course->fullname = $chelper->get_course_formatted_name($courseelements);

                // Get subscribed student numbers.
                $coursesql = "WHERE e.courseid = :courseid";
                $params['courseid'] = $course->id;
                $sql = "SELECT COUNT(DISTINCT(ue.userid)) AS enroled_count
                      FROM {user_enrolments} ue
                      JOIN {enrol} e ON e.id = ue.enrolid
                      $coursesql";
                $enroledcount = $DB->get_field_sql($sql, $params);
                $course->enroledcount = $enroledcount . ' ' . get_string('subscribers', 'block_pimenkofeaturedcourses');

                // Get customfields elements.
                $customfields = $courseelements->get_custom_fields();
                $course->customfields = [];

                // Adding of custom fields in the template.

                foreach ($customfields as $customfield) {
                    $cf = new stdClass();
                    $cf->customfield = $customfield->export_value();
                    $cf->customfieldname = $customfield->get_field()->get('shortname') . '_' . $customfield->get_field()->get('id');

                    if ($cf->customfield != '') {
                        $course->customfields[] = $cf;
                    }
                }

                // Get course tag list.
                $tagslist = array_values(core_tag_tag::get_item_tags_array('core', 'course', $course->id));
                $course->tagslist = '';

                if (!empty($tagslist) && count($tagslist) > 1) {
                    for ($i = 0; $i < count($tagslist); $i++) {
                        if ($i < count($tagslist) - 1) {
                            $course->tagslist .= $tagslist[$i] . ' - ';
                        } else {
                            $course->tagslist .= $tagslist[$i];
                        }
                    }
                } else if (!empty($tagslist)) {
                    $course->tagslist = $tagslist[0];
                }

                $enrolmethod = enrol_get_instances(
                    $course->id,
                    true
                );
                foreach ($enrolmethod as $enrol) {
                    if ($enrol->enrol == 'synopsis') {
                        $params = ['id' => $course->id];
                        $course->url = new moodle_url(
                            "/enrol/synopsis/index.php",
                            $params
                        );
                        break;
                    } else {
                        $course->url = new \moodle_url('/course/view.php', ['id' => $course->id]);
                    }
                }

                if (!isset($configdata->{'course_order_' . $course->id}) ||
                    array_key_exists($configdata->{'course_order_' . $course->id}, $courseslist)) {
                    $courseslist[] = $course;
                } else {
                    $courseslist[$configdata->{'course_order_' . $course->id}] = $course;
                }
            }

            // We need to sort ur array.
            $sortedlist = new ArrayObject($courseslist);
            $sortedlist->ksort();
            $courseslist = $sortedlist->getArrayCopy();

            $config = get_config('block_pimenkofeaturedcourses');
            $displayenrolnumber = '0';
            if (isset($config->displayenrolnumber)) {
                $displayenrolnumber = $config->displayenrolnumber;
            }

            $data = [
                'courses' => $courseslist,
                'displayenrolnumber' => $displayenrolnumber
            ];

            $this->content->text = $OUTPUT->render_from_template('block_pimenkofeaturedcourses/content', $data);
        }

        return $this->content;
    }

    /**
     * Defines in which pages this block can be added.
     *
     * @return array of the pages where the block can be added.
     */
    public function applicable_formats(): array {
        return [
            'all' => true
        ];
    }

    /**
     * Does this plugin have some settings ?
     * If yes => True
     *
     * @return bool
     */
    public function has_config(): bool {
        return true;
    }

    /**
     * Allow to have multiple instance of this plugin.
     *
     * @return bool
     */
    public function instance_allow_multiple(): bool {
        return true;
    }
}
