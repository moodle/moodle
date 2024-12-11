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

namespace core_course\output;

use action_menu;
use action_menu_link_secondary;
use context_coursecat;
use core_course_category;
use course_request;
use moodle_page;
use moodle_url;

/**
 * Class responsible for generating the action bar (tertiary nav) elements in an individual category page
 *
 * @package    core
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_action_bar extends manage_categories_action_bar {
    /**
     * @var object The current category we are referring to.
     */
    protected $category;
    /**
     * Constructor category_action_bar
     *
     * @param moodle_page $page The page object
     * @param object $category
     * @param object|null $course The course that we are generating the nav for
     * @param string|null $searchvalue
     */
    public function __construct(moodle_page $page, object $category, ?object $course = null, ?string $searchvalue = null) {
        $this->category = $category;
        parent::__construct($page, 'courses', $course, $searchvalue);
    }

    /**
     * Gets the url_select to be displayed in the participants page if available.
     *
     * @param \renderer_base $output
     * @return object|null The content required to render the url_select
     */
    protected function get_category_select(\renderer_base $output): ?object {
        if (!$this->searchvalue && !core_course_category::is_simple_site()) {
            $categories = core_course_category::make_categories_list();
            if (count($categories) > 1) {
                foreach ($categories as $id => $cat) {
                    $url = new moodle_url($this->page->url, ['categoryid' => $id]);
                    $options[$url->out()] = $cat;
                }
                $currenturl = new moodle_url($this->page->url, ['categoryid' => $this->category->id]);
                $select = new \url_select($options, $currenturl, null);
                $select->set_label(get_string('categories'), ['class' => 'visually-hidden']);
                $select->class .= ' text-truncate w-100';
                return $select->export_for_template($output);
            }
        }

        return null;
    }

    /**
     * Gets the additional options to be displayed within a 'More' dropdown in the tertiary navigation.
     * The predefined order defined by UX is:
     *  - Add a course
     *  - Add a sub cat
     *  - Manage course
     *  - Request a course
     *  - Course pending approval
     *
     * @return array
     */
    protected function get_additional_category_options(): array {
        global $CFG, $DB;
        if ($this->category->is_uservisible()) {
            $context = get_category_or_system_context($this->category->id);
            if (has_capability('moodle/course:create', $context)) {
                $params = [
                    'category' => $this->category->id ?: $CFG->defaultrequestcategory,
                    'returnto' => $this->category->id ? 'category' : 'topcat'
                ];

                $options[0] = [
                    'url' => new moodle_url('/course/edit.php', $params),
                    'string' => get_string('addnewcourse')
                ];
            }

            if (!empty($CFG->enablecourserequests)) {
                // Display an option to request a new course.
                if (course_request::can_request($context)) {
                    $params = [];
                    if ($context instanceof context_coursecat) {
                        $params['category'] = $context->instanceid;
                    }

                    $options[3] = [
                        'url' => new moodle_url('/course/request.php', $params),
                        'string' => get_string('requestcourse')
                    ];
                }

                // Display the manage pending requests option.
                if (has_capability('moodle/site:approvecourse', $context)) {
                    $disabled = !$DB->record_exists('course_request', array());
                    if (!$disabled) {
                        $options[4] = [
                            'url' => new moodle_url('/course/pending.php'),
                            'string' => get_string('coursespending')
                        ];
                    }
                }
            }
        }

        if ($this->category->can_create_course() || $this->category->has_manage_capability()) {
            // Add 'Manage' button if user has permissions to edit this category.
            $options[2] = [
                'url' => new moodle_url('/course/management.php', ['categoryid' => $this->category->id]),
                'string' => get_string('managecourses')
            ];

            if ($this->category->has_manage_capability()) {
                $addsubcaturl = new moodle_url('/course/editcategory.php', array('parent' => $this->category->id));
                $options[1] = [
                    'url' => $addsubcaturl,
                    'string' => get_string('addsubcategory')
                ];
            }
        }

        // We have stored the options in a predefined order. Sort it based on index and return.
        if (isset($options)) {
            sort($options);
            return ['options' => $options];
        }

        return [];
    }

    /**
     * Export the content to be displayed on the category page.
     *
     * @param \renderer_base $output
     * @return array Consists of the following:
     *              - categoryselect A list of available categories to be fed into a urlselect
     *              - search The course search form
     *              - additionaloptions Additional actions that can be performed in a category
     */
    public function export_for_template(\renderer_base $output): array {
        $additionaloptions = $this->get_additional_category_options();
        // Generate the action menu if there are additional options.
        if (!empty($additionaloptions)) {
            $actionmenu = new action_menu();
            $actionmenu->set_kebab_trigger(get_string('moreactions'));
            $actionmenu->set_additional_classes('ms-auto');
            foreach ($additionaloptions['options'] as $option) {
                $actionmenu->add(new action_menu_link_secondary(
                    $option['url'],
                    null,
                    $option['string']
                ));
            }
            $actionmenucontent = $output->render($actionmenu);
        }

        return [
            'categoryselect' => $this->get_category_select($output),
            'search' => $this->get_search_form(),
            'additionaloptions' => $actionmenucontent ?? '',
        ];
    }

}
