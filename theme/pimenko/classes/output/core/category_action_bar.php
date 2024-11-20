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

namespace theme_pimenko\output\core;

use context_coursecat;
use context_system;
use core_course_category;
use core_date;
use DateTime;
use moodle_url;
use theme_pimenko\form\date_form;
use theme_config;

/**
 * Class responsible for generating the action bar (tertiary nav) elements in an individual category page
 *
 * @package    core
 * @copyright  2021 Peter Dias
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_action_bar extends \core_course\output\category_action_bar {

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
        $template = [
            'categoryselect' => $this->get_category_select($output),
            'search' => $this->get_search_form(),
            'additionaloptions' => $this->get_additional_category_options()
        ];

        $categoryid = filter_input(INPUT_GET, 'categoryid', FILTER_SANITIZE_URL);

        if ($categoryid == null ) {
            $categoryid = 0;
        }

        $template['categoryid'] = $categoryid;

        $theme = theme_config::load('pimenko');

        if (isset($theme->settings->tagfilter) && $theme->settings->tagfilter && $theme->settings->enablecatalog) {
            $template['tagselect'] = $this->get_tags_select($output);
        }

        if (isset($theme->settings->customfieldfilter) && $theme->settings->customfieldfilter && $theme->settings->enablecatalog) {
            $template['customfieldfilter'] = $theme->settings->customfieldfilter;
            $template['customfields'] = $this->get_customfield_select($output);
        }

        return $template;
    }

    /**
     * Gets the url_select to be displayed in the participants page if available.
     *
     * @param \renderer_base $output
     * @return object The content required to render the url_select
     */
    public function get_category_select(\renderer_base $output): object {
        if (!$this->searchvalue) {
            $categories = core_course_category::make_categories_list();

            if (count($categories) >= 1) {
                foreach ($categories as $id => $cat) {
                    $category = core_course_category::get($id);
                    if ($category->visible ||
                        has_capability('moodle/course:viewhiddencourses', context_coursecat::instance($category->id))) {
                        $url = new moodle_url($this->page->url, ['categoryid' => $id]);
                        $options[$url->out()] = $cat;
                    }
                }
                $categoryid = filter_input(INPUT_GET, 'categoryid', FILTER_SANITIZE_URL);
                if ($categoryid) {
                    $currenturl = new moodle_url($this->page->url, ['categoryid' => $categoryid]);
                } else {
                    $currenturl = new moodle_url('/course/index.php', []);
                }

                $select = new \url_select($options, $currenturl, null);
                $select->set_label(get_string('categories'), ['class' => 'sr-only']);
                $select->class .= ' text-truncate w-100';
                return $select->export_for_template($output);
            }
        }

        return new \stdClass();
    }

    /**
     * Gets the tags to be displayed in the catalog page if available.
     *
     * @param \renderer_base $output
     * @return object|null The content required to render the url_select
     */
    public function get_tags_select(\renderer_base $output): ?object {
        global $DB;

        $alltagsobj = new \stdClass();
        $alltagsobj->id = '0';
        $alltagsobj->name = get_string('alltags', 'theme_pimenko');
        $alltagsobj->rawname = get_string('alltags', 'theme_pimenko');
        $alltags[] = $alltagsobj;

        $tags = $DB->get_records_sql('SELECT * FROM {tag}');
        $tags = array_merge($alltags, $tags);

        if (count($tags) >= 1) {
            $options = [];
            $categoryid = filter_input(INPUT_GET, 'categoryid', FILTER_SANITIZE_URL);

            foreach ($tags as $id => $tag) {
                $url = new moodle_url($this->page->url->get_path(), ['tagid' => $tag->id, 'categoryid' => $categoryid]);
                $options[$url->out(false)] = $tag->rawname;
            }
            $tagid = filter_input(INPUT_GET, 'tagid', FILTER_SANITIZE_URL);
            $currenturl = new moodle_url($this->page->url->get_path(), ['tagid' => $tagid, 'categoryid' => $categoryid]);
            $currenturl = $currenturl->out(false);
            $select = new \url_select($options, $currenturl, null);
            $select->set_label(get_string('tags'), ['class' => 'sr-only']);
            $select->class .= ' text-truncate w-100';

            return $select->export_for_template($output);
        } else {
            return null;
        }
    }

    /**
     * Gets the profil field to be displayed in the catalog page if available.
     *
     * @param \renderer_base $output
     * @return array|null The content required to render the url_select
     */
    public function get_customfield_select(\renderer_base $output): ?array {
        global $DB;

        $customfields = $DB->get_records_sql("SELECT
            cf.id,
            cf.shortname,
            cf.name,
            cf.type,
            cf.description,
            cf.descriptionformat,
            cf.sortorder,
            cf.categoryid,
            cf.configdata,
            cf.timecreated,
            cf.timemodified
            FROM {customfield_field} cf
            LEFT JOIN {customfield_category} cc ON cc.id = cf.categoryid
            WHERE cc.area = 'course'
            ORDER BY cf.sortorder");

        $templatecustomfields = [];

        if (count($customfields) >= 1) {
            foreach ($customfields as $customfield) {

                $options = [];
                $customfieldselected = filter_input(
                    INPUT_GET, 'customfieldselected', FILTER_SANITIZE_URL);
                $customfieldvalue = filter_input(INPUT_GET, 'customfieldvalue', FILTER_SANITIZE_URL);
                $customfieldtext = filter_input(INPUT_GET, 'customfieldtext', FILTER_SANITIZE_URL);
                $categoryid = filter_input(INPUT_GET, 'categoryid', FILTER_SANITIZE_URL);

                if ($customfield->type == 'select') {
                    $urlall = new moodle_url($this->page->url, ['customfieldselected' => $customfield->shortname,
                        'customfieldvalue' => 'all', 'categoryid' => $categoryid]);
                    $options[$urlall->out(false)] = format_string($customfield->name);

                    // Get options of customfield.
                    $jsconfdata = json_decode($customfield->configdata);
                    $customfieldoptions = explode(PHP_EOL, $jsconfdata->options);
                    foreach ($customfieldoptions as $key => $customfieldoption) {
                        $url = new moodle_url($this->page->url->get_path(),
                            [
                                'customfieldselected' => format_string($customfield->shortname),
                                // Key value +1 for select since it not start with 0.
                                'customfieldvalue' => $key + 1,
                                'categoryid' => $categoryid
                            ]
                        );
                        $options[$url->out(false)] = format_string($customfieldoption);
                    }

                    // Get the current url value.
                    $currenturl = new moodle_url($this->page->url->get_path(),
                        ['customfieldselected' => $customfieldselected, 'customfieldvalue' => $customfieldvalue,
                            'categoryid' => $categoryid]);
                    $currenturl = $currenturl->out(false);
                    $select = new \url_select($options, $currenturl, null);
                    $select->set_label($customfield->shortname, ['class' => 'sr-only']);
                    $select->class .= ' text-truncate w-100';

                    $template = $select->export_for_template($output);
                    $template->selecttype = true;
                    $template->name = 'customfieldselect_' . $customfield->shortname;
                    $templatecustomfields[] = $template;
                } else if ($customfield->type == 'text' || $customfield->type == 'textarea') {
                    $template = [];
                    $template['action'] = new moodle_url(
                        $this->page->url->get_path(), ['customfieldtext' => $customfield->shortname, 'categoryid' => $categoryid]);
                    $template['btnclass'] = 'btn-primary';

                    if ($customfieldvalue !== 'all' && $customfieldtext) {
                        $template['searchstring'] = format_string($customfieldvalue);
                    } else {
                        $template['searchstring'] = format_string($customfield->name);
                    }

                    $template['inputname'] = 'search_' . $customfield->shortname;

                    $template['name'] = 'customfieldsearch customfieldselect_' . $customfield->shortname;
                    $template['texttype'] = true;
                    $template['query'] = null;
                    $templatecustomfields[] = $template;

                } else if ($customfield->type == 'checkbox') {
                    $urlall = new moodle_url($this->page->url, ['customfieldselected' => $customfield->shortname,
                        'customfieldvalue' => 'all', 'categoryid' => $categoryid]);
                    $options[$urlall->out(false)] = format_string($customfield->name);

                    // Get options of customfield.
                    $customfieldoptions = [
                        1 => get_string('yes', 'theme_pimenko'),
                        0 => get_string('no', 'theme_pimenko')];
                    foreach ($customfieldoptions as $key => $customfieldoption) {
                        $url = new moodle_url($this->page->url->get_path(), ['customfieldselected' => $customfield->shortname,
                            'customfieldvalue' => $key, 'categoryid' => $categoryid]);
                        $options[$url->out(false)] = $customfieldoption;
                    }

                    // Get the current url value.
                    $currenturl = new moodle_url($this->page->url->get_path(),
                        ['customfieldselected' => $customfieldselected,
                            'customfieldvalue' => $customfieldvalue, 'categoryid' => $categoryid]);
                    $currenturl = $currenturl->out(false);
                    $select = new \url_select($options, $currenturl, null);
                    $select->set_label($customfield->shortname, ['class' => 'sr-only']);
                    $select->class .= ' text-truncate w-100';

                    $template = $select->export_for_template($output);
                    $template->selecttype = true;
                    $template->name = 'customfieldselect_' . $customfield->shortname;
                    $templatecustomfields[] = $template;
                } else if ($customfield->type == 'date') {
                    $day = filter_input(INPUT_GET, 'day', FILTER_SANITIZE_URL);
                    $year = filter_input(INPUT_GET, 'year', FILTER_SANITIZE_URL);
                    $month = filter_input(INPUT_GET, 'month', FILTER_SANITIZE_URL);
                    if ($day && $year && $month) {
                        $timestamp =
                            new DateTime($year . '-' . $month . '-' . $day,
                                core_date::get_user_timezone_object());
                        $timestamp->setTime(0, 0, 0);
                        $customfield->urlselectedvalue = $timestamp->getTimestamp();
                    }
                    $url = new moodle_url($this->page->url->get_path());
                    $mform = new date_form(
                        $url, $customfield);
                    $template = new \stdClass();
                    $template->date_selector = $mform->render();
                    $template->date = true;
                    $template->name = $customfield->shortname;
                    $templatecustomfields[] = $template;
                }
            }
            return $templatecustomfields;
        } else {
            return null;
        }
    }
}
