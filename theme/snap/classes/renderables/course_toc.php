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
 * Coures toc renderable
 * @author    gthomas2
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\renderables;

use context_course;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');
require_once($CFG->dirroot.'/course/format/lib.php');

class course_toc implements \renderable, \templatable {

    use \theme_snap\output\general_section_trait;
    use \theme_snap\renderables\trait_exportable;

    /**
     * @var bool
     */
    public $formatsupportstoc = false;

    /**
     * @var course_toc_module[]
     */
    public $modules = [];

    /**
     * @var \stdClass
     * @wsparam {
     *     chapters: {
     *        type: course_toc_chapter[],
     *        description: "An array of course_toc_chapter objects"
     *     },
     *     listlarge: {
     *        type: PARAM_ALPHAEXT,
     *        description: "list-large css class when TOC has more than 9 chapters"
     *     }
     * };
     */
    public $chapters;

    /**
     * @var course_toc_footer
     */
    public $footer;

    /**
     * @var \stdClass
     */
    protected $course;

    /**
     * @var \format_base
     */
    protected $format;

    /**
     * @var int
     */
    protected $numsections;

    private $loadmodules = true;

    /**
     * Constructor.
     * @param null|int $course
     * @param null|string $format
     * @param bool $loadmodules
     * @throws \coding_exception
     */
    public function __construct($course = null, $format = null, $loadmodules = true) {
        global $COURSE, $PAGE;
        if (empty($course)) {
            $course = $COURSE;
        }

        $this->loadmodules = $loadmodules;

        $supportedformats = ['weeks', 'topics'];
        if (!in_array($course->format, $supportedformats)) {
            return;
        } else {
            $this->formatsupportstoc = true;
        }

        if ($format) {
            $this->format  = $format;
        } else {
            $this->format  = course_get_format($course);
            $additionaloptions = $this->format->get_format_options(); // Has additional fields.
            foreach ($additionaloptions as $additionaloption => $value) {
                $course->$additionaloption = $value;
            }
        }

        $this->course = $course;
        $this->numsections = $this->format->get_last_section_number();

        // Set context first so $OUTPUT does not break later.
        if (!isset($PAGE->context) && AJAX_SCRIPT) {
            $PAGE->set_context(context_course::instance($this->course->id));
        }

        if ($this->loadmodules) {
            $this->set_modules();
        }
        $this->set_chapters();
        $this->set_footer();
    }

    /**
     * Set modules.
     * @throws \coding_exception
     */
    protected function set_modules() {
        // If course does not have any sections then exit - note, module search is not supported in course formats
        // that don't have sections.
        if (empty($this->numsections)) {
            return;
        }

        $modinfo = get_fast_modinfo($this->course);

        foreach ($modinfo->get_cms() as $cm) {
            if ($cm->modname == 'label') {
                continue;
            }
            if ($cm->sectionnum > $this->numsections) {
                continue; // Module outside of number of sections.
            }
            if (!$cm->uservisible && (empty($cm->availableinfo))) {
                continue; // Hidden completely.
            }

            $module = new course_toc_module();
            $module->cmid = $cm->id;
            $module->uservisible = $cm->uservisible;
            $module->modname = $cm->modname;
            $module->iconurl = $cm->get_icon_url();
            if ($cm->modname !== 'resource') {
                $module->srinfo = get_string('pluginname', $cm->modname);
            }
            $module->url = '#section-'.$cm->sectionnum.'&module-'.$cm->id;

            $module->formattedname = $cm->get_formatted_name();
            $this->modules[] = $module;
        }
    }

    protected function set_chapters() {
        global $PAGE;

        $this->chapters = (object) [];

        $this->chapters->listlarge = $this->numsections > 9 ? 'list-large' : '';

        $this->chapters->chapters = [];

        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($this->course->id));

        $modinfo = get_fast_modinfo($this->course);

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            // Students - If course hidden sections completely invisible & section is hidden, and you cannot
            // see hidden things, bale out.
            if ($this->course->hiddensections
                && !$thissection->visible
                && !$canviewhidden) {
                continue;
            }

            $conditional = $this->is_section_conditional($thissection);
            $chapter = new course_toc_chapter();
            $chapter->outputlink = true;
            $chapter->classes = '';

            if ($canviewhidden) { // Teachers.
                if ($conditional) {
                    $chapter->availabilityclass = 'text-warning';
                    $chapter->availabilitystatus = get_string('conditional', 'theme_snap');
                    $chapter->classes .= 'conditional ';
                }
                if (!$thissection->visible) {
                    $chapter->availabilityclass = 'text-warning';
                    $chapter->availabilitystatus = get_string('notpublished', 'theme_snap');
                    $chapter->classes .= 'draft ';
                }
            } else { // Students.
                if ($conditional && !$thissection->uservisible && !$thissection->availableinfo) {
                    // Conditional section, totally hidden from user so skip.
                    continue;
                }
                if ($conditional && $thissection->availableinfo) {
                    $chapter->availabilityclass = 'text-warning';
                    $chapter->availabilitystatus = get_string('conditional', 'theme_snap');
                    $chapter->classes .= 'conditional ';
                }
                if (!$conditional && !$thissection->visible) {
                    // Hidden section collapsed, so show as text in TOC.
                    $chapter->outputlink  = false;
                    $chapter->availabilityclass = 'text-warning';
                    $chapter->availabilitystatus = get_string('notavailable');
                    $chapter->classes .= 'draft ';
                }
            }

            $chapter->title = $this->format->get_section_name($section);
            if ($chapter->title == get_string('general')) {
                $chapter->title = get_string('introduction', 'theme_snap');
            }

            $chapter->iscurrent = false;
            if ($this->format->is_section_current($section)) {
                $chapter->iscurrent = true;
                $chapter->classes .= 'current ';
                if ($this->format->get_format() !== "weeks") {
                    $chapter->classes .= 'snap-visible-section ';
                }
            }

            $chapter->isweeksformat = false;
            if ($this->format->get_format() == "weeks") {
                $chapter->isweeksformat = true;
                if ($PAGE->url->get_path() === '/course/section.php' && optional_param('id', -1, PARAM_INT) == $thissection->id) {
                    $chapter->classes .= 'snap-visible-section ';
                } elseif ($PAGE->url->get_path() !== '/course/section.php' && $this->format->is_section_current($section)) {
                    $chapter->classes .= 'snap-visible-section ';
                }
            }

            if ($chapter->outputlink) {
                $chapter->url = '#section-'.$section;
            }
            $chapter->section = $section;
            $chapter->sectionid = $thissection->id;

            // Empty default progress.
            $chapter->progress = (object) [
                "progress" => (object) [
                    "complete" => null,
                    "total" => null,
                ],
                "completed" => null,
                "pixcompleted" => null,
            ];
            if ($this->loadmodules) {
                $chapter->progress = new course_toc_progress($this->course, $thissection);
            }
            $this->chapters->chapters[] = $chapter;
        }
    }

    /**
     * @throws \coding_exception
     */
    protected function set_footer() {
        global $OUTPUT;

        $this->footer = (object) [
            'canaddnewsection' => has_capability('moodle/course:update', context_course::instance($this->course->id)),
            'imgurladdnewsection' => $OUTPUT->image_url('pencil', 'theme'),
            'imgurltools' => $OUTPUT->image_url('course_dashboard', 'theme'),
        ];
    }

}
