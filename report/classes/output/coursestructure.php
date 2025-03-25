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

namespace core_report\output;

use core\output\local\properties\iconsize;
use core_course\output\activity_icon;

/**
 * Course sections, subsections and activities structure for reports.
 *
 * @package    core_report
 * @copyright  2024 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursestructure implements \renderable, \templatable {

    /**
     * Constructor
     *
     * @param \course_modinfo $modinfo
     */
    public function __construct(
        /** @var \course_modinfo $modinfo */
        protected \course_modinfo $modinfo
    ) {
    }

    /**
     * Exports the data.
     *
     * @param \renderer_base $output
     * @return array|\stdClass
     */
    public function export_for_template(\renderer_base $output) {

        $headers = $this->export_headers($output);
        return [
                'class' => 'generaltable boxaligncenter',
                'headers' => $headers,
                'headerscount' => count($headers),
                'activities' => $this->export_activities($output),
        ];
    }

    /**
     * Exports activities data.
     *
     * @param \renderer_base $output
     * @return array|\stdClass
     */
    public function export_activities(\renderer_base $output) {

        $activities = [];

        $delegatedsections = $this->modinfo->get_sections_delegated_by_cm();
        $allsections = $this->modinfo->get_sections();
        foreach ($allsections as $sectionnum => $sectionmodules) {
            // Add the section row.
            if ($sectionnum > 0) {
                $sectioninfo = $this->modinfo->get_section_info($sectionnum);

                // Don't show subsections here. We are showing them in the corresponding module.
                if ($sectioninfo->get_component_instance()) {
                    continue;
                }

                if (!$sectioninfo->uservisible) {
                    continue;
                }

                $activities[] = $this->export_section_data($output, $sectioninfo, false);
            }

            // Add section modules and possibly subsections.
            foreach ($sectionmodules as $cmid) {
                $cm = $this->modinfo->cms[$cmid];

                // Check if the module is delegating a section.
                if (array_key_exists($cm->id, $delegatedsections)) {
                    $subsectioninfo = $delegatedsections[$cm->id];
                    // Only non-empty are listed in allsections. We don't show empty sections.
                    if (!array_key_exists($subsectioninfo->sectionnum, $allsections)) {
                        continue;
                    }

                    $activities[] = $this->export_section_data($output, $subsectioninfo, true);

                    // Show activities inside the section.
                    $subsectionmodules = $allsections[$subsectioninfo->sectionnum];
                    foreach ($subsectionmodules as $subsectioncmid) {
                        $cm = $this->modinfo->cms[$subsectioncmid];
                        $activities[] = $this->export_activity_data($output, $cm, true);
                    }
                } else {
                    // It's simply a module.
                    $activities[] = $this->export_activity_data($output, $cm);
                }
            }
        }

        return $activities;
    }

    /**
     * Exports course sections, sections delegated by modules and modules data in a hierarchical format.
     *
     * @param \renderer_base $output
     * @return array|\stdClass
     */
    public function export_hierarchy(\renderer_base $output) {

        $sections = [];

        $allsections = $this->modinfo->get_sections();
        foreach ($allsections as $sectionnum => $sectionmodules) {
            // Add the section row.
            $sectioninfo = $this->modinfo->get_section_info($sectionnum);

            // Don't show subsections here. We are showing them in the corresponding module.
            if ($sectioninfo->get_component_instance()) {
                continue;
            }

            if (!$sectioninfo->uservisible) {
                continue;
            }

            $section = $this->export_section_data($output, $sectioninfo, false);
            if (empty($sectioninfo) || empty($sectioninfo->sequence)) {
                continue;
            }

            $activities = $this->export_hierarchy_section_activities_data($output, $sectioninfo, $allsections);
            if (!empty($activities)) {
                $section['activities'] = $activities;
            }

            $sections[] = $section;
        }

        return $sections;
    }

    /**
     * Exports activities data for a section in a hierarchical format.
     * @param \renderer_base $output
     * @param \section_info $sectioninfo
     * @param array $allsections
     * @return array
     */
    private function export_hierarchy_section_activities_data(
        \renderer_base $output,
        \section_info $sectioninfo,
        array $allsections
    ): array {
        $allsections = $this->modinfo->get_sections();

        $sectionmodules = explode(",", $sectioninfo->sequence);
        $activities = [];

        // Add section modules and possibly subsections.
        foreach ($sectionmodules as $cmid) {
            $activity = $this->export_hierarchy_activity_data($output, $this->modinfo->cms[$cmid], $allsections);
            if (!empty($activity)) {
                $activities[] = $activity;
            }
        }
        return $activities;
    }

    /**
     * Exports activity data for a section in a hierarchical format.
     * @param \renderer_base $output
     * @param \cm_info $cm
     * @param array $allsections
     * @return array|null
     */
    private function export_hierarchy_activity_data(
        \renderer_base $output,
        \cm_info $cm,
        array $allsections
    ): ?array {
        $delegatedsections = $this->modinfo->get_sections_delegated_by_cm();

        // Subsections has a special export.
        if (array_key_exists($cm->id, $delegatedsections)) {
            $subsectioninfo = $delegatedsections[$cm->id];
            // Only non-empty are listed in allsections. We don't show empty sections.
            if (!array_key_exists($subsectioninfo->sectionnum, $allsections)) {
                return null;
            }

            $subsection = $this->export_section_data($output, $subsectioninfo, true);
            if (empty($subsection)) {
                return null;
            }

            // Show activities inside the section.
            $subsectionmodules = $allsections[$subsectioninfo->sectionnum];
            $subactivities = [];
            foreach ($subsectionmodules as $subsectioncmid) {
                $cm = $this->modinfo->cms[$subsectioncmid];
                $activity = $this->export_activity_data($output, $cm, true);
                if (!empty($activity)) {
                    $subactivities[] = $activity;
                }
            }
            if (!empty($subactivities)) {
                $subsection['activities'] = $subactivities;
            }
            return $subsection;
        }

        return $this->export_activity_data($output, $cm);
    }

    /**
     * Exports the headers for report table.
     *
     * @param \renderer_base $output
     * @return array
     */
    protected function export_headers(\renderer_base $output): array {
        return [get_string('activity')];
    }

    /**
     * Exports the data for a single section.
     *
     * @param \renderer_base $output
     * @param \section_info $sectioninfo Section to export information from.
     * @param bool $isdelegated Whether the section is a delegated subsection or not.
     * @return array
     */
    public function export_section_data(\renderer_base $output, \section_info $sectioninfo, bool $isdelegated = false): array {
        $datasection = [
            'issection' => !$isdelegated,
            'isdelegated' => $isdelegated,
            'visible' => $sectioninfo->visible,
            'class' => 'section',
            'text' => get_section_name($sectioninfo->course, $sectioninfo->sectionnum),
        ];

        return $datasection;
    }

    /**
     * Exports the data for a single activity.
     *
     * @param \renderer_base $output
     * @param \cm_info $cm
     * @param bool $indelegated Whether the activity is part of a delegated section or not.
     * @return array
     */
    public function export_activity_data(\renderer_base $output, \cm_info $cm, bool $indelegated = false): array {
        global $CFG;

        if (!$cm->has_view()) {
            return [];
        }
        if (!$cm->uservisible) {
            return [];
        }

        $modulename = get_string('modulename', $cm->modname);

        $dataactivity = [
            'isactivity' => true,
            'indelegated' => $indelegated,
            'visible' => $cm->visible,
            'cells' => [],
        ];

        $activityicon = activity_icon::from_cm_info($cm)
            ->set_icon_size(iconsize::SIZE4);

        $dataactivity['activitycolumn'] = [
                'activityicon' => $output->render($activityicon),
                'link' => "$CFG->wwwroot/mod/$cm->modname/view.php?id=$cm->id",
                'text' => $cm->name,
        ];
        return $dataactivity;
    }
}
