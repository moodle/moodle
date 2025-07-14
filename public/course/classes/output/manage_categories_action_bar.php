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

use moodle_page;
use moodle_url;

/**
 * Class responsible for generating the action bar (tertiary nav) elements in the category management page
 *
 * @package    core
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_categories_action_bar implements \renderable {
    /** @var object $course The course we are dealing with. */
    protected $course;
    /** @var moodle_page $page The current page. */
    protected $page;
    /** @var string|null $viewmode The viewmode of the underlying page - Course and categories, categories or courses */
    protected $viewmode;
    /** @var string|null $heading The heading to display */
    protected $heading;
    /** @var string|null $searchvalue The search value if any */
    protected $searchvalue;

    /**
     * Constructor for the manage_categories_action_bar
     *
     * @param moodle_page $page The page object
     * @param string $viewmode The type of page we are viewing.
     * @param object|null $course The course that we are generating the nav for
     * @param string|null $searchvalue The search value if applicable
     */
    public function __construct(moodle_page $page, string $viewmode, ?object $course, ?string $searchvalue) {
        $this->course = $course;
        $this->page = $page;
        $this->viewmode = $viewmode;
        $this->searchvalue = $searchvalue;
        if ($searchvalue) {
            $this->heading = get_string('searchresults');
        }
    }

    /**
     * Gets the url_select to be displayed in the participants page if available.
     *
     * @param \renderer_base $output
     * @return object|null The content required to render the url_select
     */
    protected function get_dropdown(\renderer_base $output): ?object {
        // If a search is being performed then no need to display the dropdown.
        if ($this->searchvalue) {
            return null;
        }

        $modes = \core_course\management\helper::get_management_viewmodes();
        $activeurl = null;
        $content = [];
        foreach ($modes as $mode => $description) {
            $url = new moodle_url($this->page->url, ['view' => $mode]);
            $content[$url->out()] = $description;
            if ($this->viewmode == $mode) {
                $activeurl = $url->out();
                $this->heading = get_string("manage$mode");
            }
        }

        // Default to the first option if asking for default. This is combined.
        if (!$activeurl && $this->viewmode === 'default') {
            $activeurl = array_key_first($content);
            $this->heading = get_string("managecombined");
        }

        if ($content) {
            $urlselect = new \url_select($content, $activeurl, null);
            $urlselect->set_label(get_string('viewing'), ['class' => 'visually-hidden']);
            return $urlselect->export_for_template($output);
        }

        return null;
    }

    /**
     * Gets the url_select to be displayed in the participants page if available.
     *
     * @param \renderer_base $output
     * @return object|null The content required to render the url_select
     */
    protected function get_category_select(\renderer_base $output): ?object {
        if (!$this->searchvalue && $this->viewmode === 'courses') {
            $categories = \core_course_category::make_categories_list(array('moodle/category:manage', 'moodle/course:create'));
            if (!$categories) {
                return null;
            }
            $currentcat = $this->page->url->param('categoryid');
            foreach ($categories as $id => $cat) {
                $url = new moodle_url($this->page->url, ['categoryid' => $id]);
                if ($id == $currentcat) {
                    $currenturl = $url->out();
                }
                $options[$url->out()] = $cat;
            }

            $select = new \url_select($options, $currenturl);
            $select->set_label(get_string('category'), ['class' => 'visually-hidden']);
            $select->class .= ' text-truncate w-100';
            return $select->export_for_template($output);
        }

        return null;
    }

    /**
     * Get the search box
     *
     * @return array
     */
    protected function get_search_form(): array {
        $searchform = [
            'btnclass' => 'btn-primary',
            'inputname' => 'search',
            'searchstring' => get_string('searchcourses'),
            'query' => $this->searchvalue
        ];
        if (\core_course_category::has_capability_on_any(['moodle/category:manage', 'moodle/course:create'])) {
            $searchform['action'] = new moodle_url('/course/management.php');
        } else {
            $searchform['action'] = new moodle_url('/course/search.php');
        }
        return $searchform;
    }

    /**
     * Export the content to be displayed on the participants page.
     *
     * @param \renderer_base $output
     * @return array Consists of the following:
     *              - urlselect A stdclass representing the standard navigation options to be fed into a urlselect
     *              - renderedcontent Rendered content to be displayed in line with the tertiary nav
     */
    public function export_for_template(\renderer_base $output): array {
        return [
            'urlselect' => $this->get_dropdown($output),
            'categoryselect' => $this->get_category_select($output),
            'search' => $this->get_search_form(),
            'heading' => $this->heading,
        ];
    }
}
