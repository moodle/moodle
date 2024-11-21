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
 * Contains the default section controls output class.
 *
 * @package   format_softcourse
 * @copyright Sylvain | Pimenko 2021 <contact@pimneko.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_softcourse\output\courseformat\content;

use core_courseformat\base as course_format;
use context_course;
use completion_info;
use core_courseformat\output\local\content\section as section_base;
use stdClass;

/**
 * Base class to render a course section.
 *
 * @package   format_softcourse
 * @copyright Sylvain | Pimenko 2021 <contact@pimneko.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section extends section_base {

    /** @var course_format the course format */
    protected $format;

    /**
     * Exports data for rendering in a template.
     *
     * @param \renderer_base $output The renderer for output
     * @return stdClass The data object for template rendering
     */
    public function export_for_template(\renderer_base $output): stdClass {
        // Review this part.
        $format = $this->format;

        $data = parent::export_for_template($output);
        $data->start_url = null;

        $course = $format->get_course();
        $modinfo = get_fast_modinfo($course);

        $completioninfo = new completion_info($course);
        $context = context_course::instance($course->id);

        // If section has no mods or cmlist itself is not set or empty, then we skip.
        if (!isset($data->cmlist) || empty($data->cmlist) || !$data->cmlist->cms) {
            $data->skip = true;
            return $data;
        }

        // Prepare some cm_info we will need further.
        foreach ($data->cmlist->cms as $key => $cm) {
            $cm = $modinfo->get_cm($cm->cmitem->id);
            $idsection = $cm->get_section_info()->section;
            // Hide the section 0 if the course format option is set to "Hide the section 0".

            if (!($idsection == 0 && $format->get_format_options()['hidesectionzero'] == 1)) {
                $info = $modinfo->get_section_info_all()[$idsection];
                $data->idsection = $idsection;
                $data->name = $info->name;
                $summary = new stdClass();
                $summary->summarytext = $info->summary;
                $data->summary = $summary;
                $data->uservisible = $info->uservisible;
                $data->visible = $info->visible;
                $data->available = $info->available;
                $data->cmlist->cms[$key]->cminfo = $cm;
                $data->skip = false;
            } else {
                $data->skip = true;
            }
        }

        // We check case where section are hidden.
        // We check case where section have only one hidden activity.
        if ((isset($data->visible) && $data->visible == 0) || (isset($data->uservisible) && $data->uservisible == false) ||
            (isset($data->available) && $data->available == false)) {
            $data->skip = true;
            return $data;
        } else if (isset($data->cmlist) && count($data->cmlist->cms) == 1 &&
            ((isset($data->cmlist->cms[0]->cminfo->visible) && $data->cmlist->cms[0]->cminfo->visible == 0) ||
                (isset($data->cmlist->cms[0]->cminfo->visibleoncoursepage) &&
                    $data->cmlist->cms[0]->cminfo->visibleoncoursepage == 0) ||
                (isset($data->cmlist->cms[0]->cminfo->uservisible) && $data->cmlist->cms[0]->cminfo->uservisible == false) ||
                (isset($data->cmlist->cms[0]->cminfo->available) && $data->cmlist->cms[0]->cminfo->available == false))) {
            $data->skip = true;
            return $data;
        }

        if (isset($data->name)) {
            $data->name = format_string(
                $data->name,
                true,
                [ 'context' => context_course::instance($course->id) ],
            );
        }

        $data->courseid = $course->id;
        $options = new stdClass();
        $options->noclean = true;
        $options->overflowdiv = true;
        $data->summary->summarytext = format_text(
            $data->summary->summarytext,
            1,
            $options,
        );
        $data->countactivitiestooltip = get_string(
            'countactivities',
            'format_softcourse',
        );
        $data->countactivities = 0;

        // Check capability to edit/delete softcourse section picture.
        if (has_capability(
            'moodle/course:update',
            $context,
        )) {
            $data->update_img = get_string(
                'update_img',
                'format_softcourse',
            );
            $data->delete_img = get_string(
                'delete_img',
                'format_softcourse',
            );
        }

        // Render the iamge section.
        $fs = get_file_storage();
        $file = $fs->get_area_files(
            $context->id,
            'format_softcourse',
            'sectionimage',
            $data->num,
            "itemid, filepath, filename",
            false,
        );

        if ($file) {
            $data->urlimg = \moodle_url::make_pluginfile_url(
                end($file)->get_contextid(),
                end($file)->get_component(),
                end($file)->get_filearea(),
                end($file)->get_itemid(),
                end($file)->get_filepath(),
                end($file)->get_filename(),
            );
        }

        $nbcomplete = 0;
        $nbcompletion = 0;
        $data->first_cm_url = '';
        $data->countactivities = 0;

        // Get completion of cms.
        foreach ($data->cmlist->cms as $cm) {

            // Check if $cm is a subsection
            if ($cm->cminfo->modname == 'subsection') {
                // Loop through modules in the subsection
                $sectionid = $cm->cminfo->get_custom_data()['sectionid'];
                $sectionnum = get_fast_modinfo($course->id)->get_section_info_by_id($sectionid);
                $sectionmods = $sectionnum->get_sequence_cm_infos();
                foreach ($sectionmods as $subsecmodule) {

                    // Assuming $module, $data, $completioninfo, $nbcompletion, $nbcomplete are already defined
                    [
                        $data,
                        $nbcompletion,
                        $nbcomplete
                    ] = $this->get_completion(
                        $subsecmodule,
                        $data,
                        $completioninfo,
                        $nbcompletion,
                        $nbcomplete,
                    );
                }
            } else {
                // Assuming $cm, $data, $completioninfo, $nbcompletion, $nbcomplete are already defined
                [
                    $data,
                    $nbcompletion,
                    $nbcomplete
                ] = $this->get_completion(
                    $cm,
                    $data,
                    $completioninfo,
                    $nbcompletion,
                    $nbcomplete,
                );
            }
        }

        // Count the percent of cm complete.
        if ($nbcompletion != 0) {
            $data->progression = get_string(
                'progression',
                'format_softcourse',
            );
            $percentcomplete = $nbcomplete * 100 / $nbcompletion;
            $data->progression_percent = intval($percentcomplete);
        }

        if ($data->start_url == null) {
            $data->disabledStart = 'true';
        }

        return $data;
    }

    /**
     * Retrieves completion data for a course module.
     *
     * @param object $cm The course module object
     * @param object $data The data object containing module information
     * @param object $completioninfo The completion information object
     * @param int $nbcompletion The number of completions
     * @param int $nbcomplete The number of completed modules
     *
     * @return array An array containing updated data object, total completions, and total completed modules
     */
    function get_completion($cm, $data, $completioninfo, $nbcompletion, $nbcomplete) {

        // Determine if the desired information is in $cm or $cm->cminfo
        $cminfo = property_exists($cm, 'cminfo') ? $cm->cminfo : $cm;

        if ((isset($cminfo->available) && $cminfo->available) &&
            ($cminfo->uservisible && !$cminfo->is_stealth() && $cminfo->modname != 'label' || !empty($cm->url)) &&
            $data->first_cm_url == '') {
            if ($cminfo->modname == 'resource') {
                $cminfo->url->param('forceview', 1);
            }
            if ($data->start_url == null) {
                $data->start_url = $cminfo->url->out(false);
            }
            $data->first_cm_url = $cminfo->url->out(false);
        }

        if (isset($cminfo->completion) && $cminfo->completion > 0) {
            $nbcompletion++;
        }

        if (isset($cminfo)) {
            $nbcomplete += $completioninfo->get_data($cminfo, true)->completionstate;

            if ($cminfo->deletioninprogress == 0 && $cminfo->visible == 1 && $cminfo->modname != "label" &&
                $cminfo->visibleoncoursepage == 1 && $cminfo->uservisible && $cminfo->available == true) {
                $data->countactivities += 1;
            }
        }

        return [
            $data,
            $nbcompletion,
            $nbcomplete
        ];
    }
}
