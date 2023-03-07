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
 * Migrate frameworks results.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_lpmigrate\output;
defined('MOODLE_INTERNAL') || die();

use context;
use context_course;
use context_module;
use moodle_url;
use renderable;
use templatable;
use stdClass;
use core_competency\competency;
use core_competency\competency_framework;
use core_competency\external\competency_exporter;
use core_competency\external\competency_framework_exporter;
use core_competency\url;
use tool_lpmigrate\framework_processor;

/**
 * Migrate frameworks results class.
 *
 * @package    tool_lpmigrate
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migrate_framework_results implements renderable, templatable {

    /** @var context The current page context. */
    protected $pagecontext;
    /** @var framework_processor The processor. */
    protected $processor;
    /** @var array $unmappedfrom Competencies from unmapped. */
    protected array $unmappedfrom = [];
    /** @var array $unmappedto competencies to unmapped. */
    protected array $unmappedto = [];
    /* @var competency_framework|null $frameworkfrom Framework from. */
    protected $frameworkfrom = null;
    /* @var competency_framework|null $frameworkto Framework to. */
    protected $frameworkto = null;

    /**
     * Construct.
     *
     * @param context $pagecontext The current page context.
     * @param framework_processor $processor The processor.
     * @param competency_framework $frameworkfrom Framework from.
     * @param competency_framework $frameworkto Framework to.
     * @param array $unmappedfrom Competencies from unmapped.
     * @param array $unmappedto Competencies to unmapped.
     */
    public function __construct(context $pagecontext, framework_processor $processor, competency_framework $frameworkfrom,
            competency_framework $frameworkto, array $unmappedfrom = array(), array $unmappedto = array()) {
        if (!$processor->has_run()) {
            throw new \coding_exception('The processor has not run.');
        }
        $this->pagecontext = $pagecontext;
        $this->processor = $processor;
        $this->unmappedfrom = $unmappedfrom;
        $this->unmappedto = $unmappedto;
        $this->frameworkfrom = $frameworkfrom;
        $this->frameworkto = $frameworkto;
    }

    /**
     * Export the data.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(\renderer_base $output) {
        global $DB;
        $data = new stdClass();

        $missingmappings = $this->processor->get_missing_mappings();

        $data->pagecontextid = $this->pagecontext->id;
        $data->expectedccmigrations = $this->processor->get_expected_course_competency_migrations();
        $data->expectedmcmigrations = $this->processor->get_expected_module_competency_migrations();
        $data->ccmigrationscount = $this->processor->get_course_competency_migrations();
        $data->mcmigrationscount = $this->processor->get_module_competency_migrations();
        $data->ccremovalscount = $this->processor->get_course_competency_removals();
        $data->mcremovalscount = $this->processor->get_module_competency_removals();

        $data->unmappedfrom = array();
        $data->unmappedto = array();

        $exporter = new competency_framework_exporter($this->frameworkfrom);
        $data->frameworkfrom = $exporter->export($output);
        $exporter = new competency_framework_exporter($this->frameworkto);
        $data->frameworkto = $exporter->export($output);

        $fromcontext = $this->frameworkfrom->get_context();
        $tocontext = $this->frameworkto->get_context();

        $compcontext = null;
        foreach ($this->unmappedfrom as $comp) {
            $exporter = new competency_exporter($comp, array('context' => $fromcontext));
            $data->unmappedfrom[] = $exporter->export($output);
        }

        foreach ($this->unmappedto as $comp) {
            $exporter = new competency_exporter($comp, array('context' => $tocontext));
            $data->unmappedto[] = $exporter->export($output);
        }

        $data->coursesfound = $this->processor->get_courses_found_count();
        $data->cmsfound = $this->processor->get_cms_found_count();
        $data->mappingsmissingcount = count($missingmappings);
        $data->hasunmappedto = count($data->unmappedto) > 0;
        $data->hasunmappedfrom = count($data->unmappedfrom) > 0;
        $warnings = $this->processor->get_warnings();
        $data->warnings = array();
        $data->warningcount = count($warnings);
        $errors = $this->processor->get_errors();
        $data->errors = array();
        $data->errorcount = count($errors);

        foreach ($warnings as $warning) {
            $cmcontext = !empty($warning['cmid']) ? context_module::instance($warning['cmid']) : null;
            $coursecontext = context_course::instance($warning['courseid']);
            $warning['cm'] = $cmcontext ? $cmcontext->get_context_name() : null;
            $warning['course'] = $coursecontext->get_context_name();
            $warning['competency'] = $DB->get_field(competency::TABLE, 'idnumber', array('id' => $warning['competencyid']));
            $data->warnings[] = $warning;
        }

        foreach ($errors as $error) {
            $cmcontext = !empty($error['cmid']) ? context_module::instance($error['cmid']) : null;
            $coursecontext = context_course::instance($error['courseid']);
            $error['cm'] = $cmcontext ? $cmcontext->get_context_name() : null;
            $error['course'] = $coursecontext->get_context_name();
            $error['competency'] = $DB->get_field(competency::TABLE, 'idnumber', array('id' => $error['competencyid']));
            $data->errors[] = $error;
        }

        $data->pluginbaseurl = (new moodle_url('/admin/tool/lpmigrate'))->out(false);
        $data->frameworksurl = url::frameworks($this->pagecontext->id)->out(false);

        return $data;
    }
}
