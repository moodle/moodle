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
 * The course exporter.
 *
 * @package     core
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\content\export\exporters;

use context_course;
use context_module;
use core\content\export\exported_item;
use core\content\export\zipwriter;
use section_info;
use stdClass;

/**
 * The course exporter.
 *
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_exporter extends component_exporter {

    /** @var stdClass The course being exported */
    protected $course;

    /** @var \course_modinfo The course_modinfo instnace for this course */
    protected $modinfo;

    /**
     * Constructor for the course exporter.
     *
     * @param   context_course $context The context of the course to export
     * @param   stdClass $user
     * @param   zipwriter $archive
     */
    public function __construct(context_course $context, stdClass $user, zipwriter $archive) {
        $this->course = get_course($context->instanceid);
        $this->modinfo = get_fast_modinfo($this->course, $user->id);

        parent::__construct($context, 'core_course', $user, $archive);
    }

    /**
     * Export the course.
     *
     * @param   \context[] $exportedcontexts A list of contexts which were successfully exported
     */
    public function export_course(array $exportedcontexts): void {
        // A course export is composed of:
        // - Course summary (including inline files)
        // - Overview files
        // - Section:
        // -- Section name
        // -- Section summary (including inline files)
        // -- List of available activities.

        $aboutpagelink = $this->add_course_about_page();
        $templatedata = (object) [
            'aboutpagelink' => $aboutpagelink,
            'sections' => [],
        ];

        // Add all sections.
        foreach ($this->modinfo->get_section_info_all() as $number => $section) {
            $templatedata->sections[] = $this->get_course_section($exportedcontexts, $section);
        }

        $this->get_archive()->add_file_from_template(
            $this->get_context(),
            'index.html',
            'core/content/export/course_index',
            $templatedata
        );
    }

    /**
     * Add course about page.
     *
     * @return  null|string The URL to the about page if one was generated
     */
    protected function add_course_about_page(): ?string {
        $hascontent = false;

        $templatedata = (object) [
            'summary' => '',
            'overviewfiles' => [],
        ];

        // Fetch the course summary content.
        if ($this->course->summary) {
            $summarydata = $this->get_archive()->add_pluginfiles_for_content(
                $this->get_context(),
                '_course',
                $this->course->summary,
                'course',
                'summary',
                0,
                null
            );

            if ($summarydata->has_any_data()) {
                $hascontent = true;
                $templatedata->summary = format_text($summarydata->get_content(), $this->course->summaryformat,
                    ['context' => $this->get_context()]);
            }
        }

        $files = $this->get_archive()->add_pluginfiles_for_content(
            $this->get_context(),
            '',
            '',
            'course',
            'overviewfiles',
            0,
            null
        )->get_noncontent_files();

        if (count($files)) {
            $templatedata->overviewfiles = $files;
            $hascontent = true;
        }

        if ($hascontent) {
            $this->get_archive()->add_file_from_template(
                $this->get_context(),
                'about.html',
                'core/content/export/course_summary',
                $templatedata
            );

            return $this->get_archive()->get_relative_context_path($this->get_context(), $this->get_context(), 'about.html');
        }

        return null;
    }

    /**
     * Fetch data for the specified course section.
     *
     * @param   \context[] $exportedcontexts A list of contexts which were successfully exported
     * @param   section_info $section The section being exported
     * @return  stdClass
     */
    protected function get_course_section(array $exportedcontexts, section_info $section): stdClass {
        $sectiondata = (object) [
            'number' => $section->section,
            'title' => format_string($section->name, true, ['context' => $this->get_context()]),
            'summary' => '',
            'activities' => [],
        ];

        // Fetch the section summary content.
        if ($section->summary) {
            $summarydata = $this->get_archive()->add_pluginfiles_for_content(
                $this->get_context(),
                '_course',
                $section->summary,
                'course',
                'section',
                $section->id,
                $section->id
            );

            if ($summarydata->has_any_data()) {
                $sectiondata->summary = format_text($summarydata->get_content(), $section->summaryformat,
                    ['context' => $this->get_context()]);
            }
        }

        if (empty($this->modinfo->sections[$section->section])) {
            return $sectiondata;
        }

        foreach ($this->modinfo->sections[$section->section] as $cmid) {
            $cm = $this->modinfo->cms[$cmid];
            if (!$cm->uservisible) {
                continue;
            }

            if (array_key_exists($cm->context->id, $exportedcontexts)) {
                // This activity was exported.
                // The link to it from the course index should be a relative link.
                $url = $this->get_archive()->get_relative_context_path($this->get_context(), $cm->context, 'index.html');
            } else {
                // This activity was not included in the export for some reason.
                // Link to the live activity.
                $url = $cm->url;
            }
            $sectiondata->activities[] = (object) [
                'title' => $cm->get_formatted_name(),
                'modname' => $cm->modfullname,
                'link' => $url,
            ];
        }

        return $sectiondata;
    }

    /**
     * Export all exportable content for an activity module.
     *
     * @param   context_module $modcontect
     * @param   exportable_item[] $export_exportables
     */
    public function export_mod_content(context_module $modcontext, array $exportables): void {
        $cm = $this->modinfo->get_cm($modcontext->instanceid);
        $modname = $cm->modname;

        $templatedata = (object) [
            'modulelink' => $cm->url,
            'modulename' => $cm->get_formatted_name(),
            'intro' => null,
            'sections' => [],
        ];

        if (plugin_supports('mod', $modname, FEATURE_MOD_INTRO, true)) {
            $templatedata->intro = $this->get_mod_intro_data($modcontext);
        }

        $exporteditems = [];
        foreach ($exportables as $exportable) {
            $exporteditem = $exportable->add_to_archive($this->get_archive());
            $templatedata->sections[] = $exporteditem->get_template_data();
        }

        // Add the index to the archive.
        $this->get_archive()->add_file_from_template(
            $modcontext,
            'index.html',
            'core/content/export/module_index',
            $templatedata
        );
    }

    /**
     * Get the course_module introduction data.
     *
     * @param   context_module $modcontect
     * @return  null|string The content of the intro area
     */
    protected function get_mod_intro_data(context_module $modcontext): ?string {
        global $DB;

        $cm = $this->modinfo->get_cm($modcontext->instanceid);
        $modname = $cm->modname;

        $record = $DB->get_record($modname, ['id' => $cm->instance], 'intro, introformat');

        // Fetch the module intro content.
        if ($record->intro) {
            $exporteditem = $this->get_archive()->add_pluginfiles_for_content(
                $modcontext,
                '',
                $record->intro,
                "mod_{$modname}",
                'intro',
                0,
                null
            );

            if ($exporteditem->has_any_data()) {
                return format_text($exporteditem->get_content(), $record->introformat, ['context' => $modcontext]);
            }
        }

        return null;
    }
}
