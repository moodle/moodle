<?php
// This file is part of The Course Module Navigation Block
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
 * Course contents block generates a table of course contents based on the section descriptions.
 *
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | Pimenko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/format/lib.php');

/**
 * Define the block course modulenavigation.
 *
 * @package    block_course_modulenavigation
 * @copyright  2019 Pimenko <contact@pimenko.com> <pimenko.com>
 * @author     Sylvain Revenu | Nick Papoutsis | Bas Brands | Pimenko
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_course_modulenavigation extends block_base {

    /**
     * Initializes the block, called by the constructor.
     */
    public function init() {
        $this->title = get_string(
            'pluginname',
            'block_course_modulenavigation',
        );
    }

    /**
     *  Allow parameters in admin settings
     */
    public function has_config() {
        return true;
    }

    /**
     * Amend the block instance after it is loaded.
     */
    public function specialization() {
        if (!empty($this->config->blocktitle)) {
            $this->title = $this->config->blocktitle;
        } else {
            $this->title = get_string(
                'config_blocktitle_default',
                'block_course_modulenavigation',
            );
        }
    }

    /**
     * Which page types this block may appear on.
     *
     * @return array
     */
    public function applicable_formats() {
        return [
            'site-index' => true,
            'course-view-*' => true,
        ];
    }

    /**
     * Populate this block's content object.
     *
     * @return stdClass|stdObject
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $DB;

        if (!is_null($this->content)) {
            return $this->content;
        }

        $selected = optional_param(
            'section',
            null,
            PARAM_INT,
        );
        $intab = optional_param(
            'dtab',
            null,
            PARAM_TEXT,
        );

        $this->content = new stdClass();
        $this->content->footer = '';
        $this->content->text = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($this->page->pagelayout == 'admin') {
            return $this->content;
        }

        $format = course_get_format($this->page->course);
        $course = $format->get_course(); // Needed to have numsections property available.

        if (!$format->uses_sections()) {
            if (debugging()) {
                $this->content->text = get_string(
                    'notusingsections',
                    'block_course_modulenavigation',
                );
            }
            return $this->content;
        }

        $sections = $format->get_sections();

        if (empty($sections)) {
            return $this->content;
        }

        $context = context_course::instance($course->id);

        $modinfo = get_fast_modinfo($course);

        $template = new stdClass();

        $completioninfo = new completion_info($course);

        if ($completioninfo->is_enabled()) {
            $template->completionon = 'completion';
        }

        $completionok = [
            COMPLETION_COMPLETE,
            COMPLETION_COMPLETE_PASS,
        ];

        $thiscontext = context::instance_by_id($this->page->context->id);

        $inactivity = false;
        $myactivityid = 0;

        if ($thiscontext->get_level_name() == get_string('activitymodule')) {
            // Uh-oh we are in a activity.
            $inactivity = true;
            if ($cm = $DB->get_record_sql(
                "SELECT cm.*, md.name AS modname
                                           FROM {course_modules} cm
                                           JOIN {modules} md ON md.id = cm.module
                                           WHERE cm.id = ?",
                [ $thiscontext->instanceid ],
            )) {
                $myactivityid = $cm->id;
            }
        }

        $template->inactivity = $inactivity;

        if (count($sections) > 1) {
            $template->hasprevnext = true;
            $template->hasnext = true;
            $template->hasprev = true;
        }

        $courseurl = new moodle_url(
            '/course/view.php',
            [ 'id' => $course->id ],
        );
        $template->courseurl = $courseurl->out();
        $sectionnums = [];

        foreach ($sections as $section) {
            $i = $section->section;
            $sectionnums[] = $section->section;
            if (!$section->uservisible) {
                if ($section->visible == 0 || !$section->available) {
                    continue;
                } else if (isset($section->modinfo->sections[$i]) && count($section->modinfo->sections[$i]) == 1 &&
                    ($section->modinfo->cms[$section->modinfo->sections[$i][0]]->visible == 0 ||
                        $section->modinfo->cms[$section->modinfo->sections[$i][0]]->visibleoncoursepage == 0 ||
                        !$section->modinfo->cms[$section->modinfo->sections[$i][0]]->available)) {
                    continue;
                }
            }
            if (!empty($section->name)) {
                $title = format_string(
                    $section->name,
                    true,
                    [ 'context' => $context ],
                );
            } else {
                $summary = file_rewrite_pluginfile_urls(
                    $section->summary,
                    'pluginfile.php',
                    $context->id,
                    'course',
                    'section',
                    $section->id,
                );
                $summary = format_text(
                    $summary,
                    $section->summaryformat,
                    [
                        'para' => false,
                        'context' => $context,
                    ],
                );
                $title = $format->get_section_name($section);
            }

            $thissection = new stdClass();
            $thissection->uservisible = $section->uservisible;
            $thissection->number = $i;
            $thissection->title = $title;
            $thissection->url = $format->get_view_url($section);

            $toggleclickontitle = get_config(
                'block_course_modulenavigation',
                'toggleclickontitle',
            );
            $togglecollapse = get_config(
                'block_course_modulenavigation',
                'togglecollapse',
            );
            $toggletitles = get_config(
                'block_course_modulenavigation',
                'toggletitles',
            );

            $thissection->collapse = ($toggleclickontitle == 2); // Display the menu if true, else go to link.
            $thissection->selected = ($togglecollapse == 2);
            $thissection->onlytitles = ($toggletitles == 2); // Show only titles if true, else show titles and contents.

            if ($i == $selected && !$inactivity) {
                $thissection->selected = true;
            }

            $thissection->modules = [];

            if (!empty($modinfo->sections[$i])) {
                foreach ($modinfo->sections[$i] as $modnumber) {

                    $module = $modinfo->cms[$modnumber];
                    $thismod = new stdClass();

                    if ($module->modname === 'subsection') {
                        $thismod->issubsection = true;
                        $thismod->subsection = $this->handle_subsection($module);

                        foreach ($thismod->subsection->modules as $key => $subsectionmodule) {
                            $modulechecked = $this->checkmodule(
                                $subsectionmodule,
                                $context,
                                $completioninfo,
                                $completionok,
                                $inactivity,
                                $myactivityid,
                                $thissection,
                            );
                            if($modulechecked !== null) {
                                $thismod->subsection->modules[$key] = $modulechecked;
                            }
                        }

                        if($thismod->subsection !== null && !empty($thismod->subsection->modules)) {
                            $thissection->modules[] = $thismod;
                        }
                    } else {
                        $modulechecked = $this->checkmodule(
                            $module,
                            $context,
                            $completioninfo,
                            $completionok,
                            $inactivity,
                            $myactivityid,
                            $thissection,
                        );

                        if($modulechecked !== null) {
                            $thissection->modules[] = $modulechecked;
                        }
                    }
                }

                $thissection->hasmodules = (count($thissection->modules) > 0);
                // We prevent case of section (mod_subsection) are added at the end ...
                if ($thissection->hasmodules
                    && $thissection->uservisible
                    && $section->component !== "mod_subsection") {
                    $template->sections[] = $thissection;
                }
            }

            if ($thissection->selected) {

                $pn = $this->get_prev_next(
                    $sectionnums,
                    $thissection->number,
                );

                $courseurl = new moodle_url(
                    '/course/view.php',
                    [
                        'id' => $course->id,
                        'section' => $i,
                    ],
                );
                $template->courseurl = $courseurl->out();

                if ($pn->next === false) {
                    $template->hasnext = false;
                }
                if ($pn->prev === false) {
                    $template->hasprev = false;
                }

                $prevurl = new moodle_url(
                    '/course/view.php',
                    [
                        'id' => $course->id,
                        'section' => $pn->prev,
                    ],
                );
                $template->prevurl = $prevurl->out(false);

                $currurl = new moodle_url(
                    '/course/view.php',
                    [
                        'id' => $course->id,
                        'section' => $thissection->number,
                    ],
                );
                $template->currurl = $currurl->out(false);

                $nexturl = new moodle_url(
                    '/course/view.php',
                    [
                        'id' => $course->id,
                        'section' => $pn->next,
                    ],
                );
                $template->nexturl = $nexturl->out(false);
            }
        }

        if ($intab) {
            $template->inactivity = true;
        }

        $template->coursename = $course->fullname;
        $template->config = $this->config;
        $renderer = $this->page->get_renderer(
            'block_course_modulenavigation',
            'nav',
        );
        $this->content->text = $renderer->render_nav($template);
        return $this->content;
    }

    /**
     *
     * Function to get the previous and next values in an array.
     *
     * @param array $array
     * @param int $current
     * @return stdClass
     */
    private function get_prev_next(array $array, int $current): stdClass {
        $pn = new stdClass();
        $hascurrent = $pn->next = $pn->prev = false;

        foreach ($array as $a) {
            if ($hascurrent) {
                $pn->next = $a;
                break;
            }
            if ($a == $current) {
                $hascurrent = true;
            } else {
                $pn->prev = $a;
            }
        }
        return $pn;
    }

    /**
     * Handle subsection for a given module.
     *
     * @param object $module The module object for which the subsection is being handled.
     *
     * @return stdClass Returns a stdClass object representing the subsection with the following properties:
     *  - title: The title of the subsection.
     *  - uservisible: Visibility status of the subsection.
     *  - number: A unique identifier for the subsection.
     *  - url: The URL to view the subsection.
     *  - modules: An array containing modules*/
    public function handle_subsection($module) {

        $format = course_get_format($this->page->course);
        $sectionid = $module->get_custom_data()['sectionid'];
        $sectionnum = get_fast_modinfo($this->page->course->id)->get_section_info_by_id($sectionid);
        $sectionmods = $sectionnum->get_sequence_cm_infos();

        $subsection = new stdClass();
        $subsection->title = $sectionnum->name;
        $subsection->uservisible = $sectionnum->uservisible;
        $subsection->number = 'sub_' . $sectionnum->id;
        $subsection->url = $format->get_view_url($sectionnum);

        $subsection->modules = [];
        foreach ($sectionmods as $mod) {
            $this->add_to_list(
                $subsection->modules,
                $mod,
            );
        }

        if ($subsection->uservisible) {
            return $subsection;
        } else {
            return null;
        }
    }

    /**
     * Adds a module to the list of modules and activities.
     *
     * @param array $mods The list of modules.
     * @param object $module The module to be added.
     * @return void
     */
    private function add_to_list(&$mods, $module) {
        if (!$module->uservisible || $module->is_stealth() || empty($module->url)) {
            return;
        }

        $mods[] = $module;
    }

    /**
     * Check the module for various conditions and return an object with relevant information
     *
     * @param object $module The module object to be checked
     * @param string $context The context of the module
     * @param object $completioninfo Object containing completion information
     * @param array $completionok Array of completion states considered as completed
     * @param bool $inactivity Flag indicating if inactivity is present
     * @param int $myactivityid The ID of the current activity
     * @param object $thissection The current section object
     *
     * @return stdClass|null Returns an object with module information or null if module does not meet criteria
     */
    private function checkmodule($module, $context, $completioninfo, $completionok, $inactivity, $myactivityid, $thissection) {
        $thismod = new stdClass();

        if ((get_config(
                    'block_course_modulenavigation',
                    'toggleshowlabels',
                ) == 1) && ($module->modname == 'label')) {
            return null;
        }
        if ($module->deletioninprogress == '1' || !$module->is_visible_on_course_page() || !$module->uservisible) {
            return null;
        }
        if ((!$module->visible || !$module->visibleoncoursepage || !$module->available) && !$module->uservisible) {
            return null;
        }
        if ($inactivity && $myactivityid == $module->id) {
            $thissection->selected = true;
            $thismod->active = 'active';
        }
        $thismod->name = format_string(
            $module->name,
            true,
            [ 'context' => $context ],
        );
        $thismod->url = $module->url;
        $thismod->onclick = $module->onclick;
        if ($module->modname == 'label') {
            $thismod->url = '';
            $thismod->onclick = '';
            $thismod->label = 'true';
        }
        $hascompletion = $completioninfo->is_enabled($module);
        if ($hascompletion) {
            $thismod->completeclass = 'incomplete';
        }
        $completiondata = $completioninfo->get_data(
            $module,
            true,
        );
        if (in_array(
            $completiondata->completionstate,
            $completionok,
        )) {
            $thismod->completeclass = 'completed';
        }

        return $thismod;
    }
}
