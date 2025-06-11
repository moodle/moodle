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
 * Snap course renderer.
 * Overrides core course renderer.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output\core;

defined('MOODLE_INTERNAL') || die();

use cm_info;
use context_course;
use context_module;
use html_writer;
use moodle_url;
use coursecat;
use stdClass;
use theme_snap\activity;
use theme_snap\activity_meta;
use core\output\local\dropdown\dialog as dropdown_dialog;
use core_completion\cm_completion_details;

require_once($CFG->dirroot . "/mod/book/locallib.php");
require_once($CFG->libdir . "/gradelib.php");
require_once($CFG->dirroot . '/course/renderer.php');
require_once("$CFG->libdir/resourcelib.php");

class course_renderer extends \core_course_renderer {

    /**
     * Output frontpage summary text and frontpage modules (stored as section 1 in site course)
     *
     * This may be disabled in settings
     * Copied from course/renderer.php in 3.11. This is an old implementation that needs our constant review.
     *
     * @return string
     */
    public function frontpage_section1() {
        global $SITE, $USER;

        $output = '';
        $editing = $this->page->user_is_editing();

        if ($editing) {
            // Make sure section with number 1 exists.
            course_create_sections_if_missing($SITE, 1);
        }

        $modinfo = get_fast_modinfo($SITE);
        $section = $modinfo->get_section_info(1);
        if (($section && (!empty($modinfo->sections[1]) || !empty($section->summary))) || $editing) {
            $output .= $this->box_start('generalbox sitetopic');

            // If currently moving a file then show the current clipboard.
            if (ismoving($SITE->id)) {
                $stractivityclipboard = strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
                $output .= '<p><font size="2">';
                $cancelcopyurl = new moodle_url('/course/mod.php', ['cancelcopy' => 'true', 'sesskey' => sesskey()]);
                $output .= "$stractivityclipboard&nbsp;&nbsp;(" . html_writer::link($cancelcopyurl, get_string('cancel')) .')';
                $output .= '</font></p>';
            }

            $context = context_course::instance(SITEID);

            // If the section name is set we show it.
            if (trim($section->name ?? '') !== '') {
                $output .= $this->heading(
                    format_string($section->name, true, ['context' => $context]),
                    2,
                    'sectionname'
                );
            }

            $summarytext = file_rewrite_pluginfile_urls($section->summary,
                'pluginfile.php',
                $context->id,
                'course',
                'section',
                $section->id);
            $summaryformatoptions = new stdClass();
            $summaryformatoptions->noclean = true;
            $summaryformatoptions->overflowdiv = true;

            $output .= format_text($summarytext, $section->summaryformat, $summaryformatoptions);

            if ($editing && has_capability('moodle/course:update', $context)) {
                $streditsummary = get_string('edit');
                $editsectionurl = new moodle_url('/course/editsection.php', ['id' => $section->id]);
                $attributes = ['title' => $streditsummary, 'aria-label' => $streditsummary];
                $output .= html_writer::link($editsectionurl, $this->pix_icon('t/edit', ''), $attributes) .
                    "<br /><br />";
            }

            $output .= $this->course_section_cm_list_snap($SITE, $section);

            $output .= $this->course_section_add_cm_control($SITE, $section->section);
            $output .= $this->box_end();
        }

        return $output;
    }
    /**
     * override course render for course module list items
     * add additional classes to list item (see $modclass)
     *
     * @author: SL / GT
     * @param stdClass $course
     * @param \completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return String
     */
    public function course_section_cm_list_item_snap($course,
    &$completioninfo,
    cm_info $mod,
    $sectionreturn,
    $displayoptions = []
    ) {
        $output = '';
        if ($modulehtml = $this->course_section_cm($course, $completioninfo, $mod, $sectionreturn, $displayoptions)) {
            list($snapmodtype, $mimetype) = $this->get_mod_type($mod);

            if ($mod->modname === 'resource') {
                // Default for resources/attachments e.g. pdf, doc, etc.
                $modclasses = ['snap-resource', 'snap-mime-'.$mimetype, 'snap-resource-long'];
                $resourcedisplayincourse = true;
                $modresourceneoptions = [
                    RESOURCELIB_DISPLAY_EMBED,
                    RESOURCELIB_DISPLAY_FRAME,
                    RESOURCELIB_DISPLAY_NEW,
                    RESOURCELIB_DISPLAY_DOWNLOAD,
                    RESOURCELIB_DISPLAY_POPUP,
                ];
                if (!empty($mod->customdata['display'])) {
                    if (in_array($mod->customdata['display'], $modresourceneoptions)) {
                        $resourcedisplayincourse = false;
                    }
                }
                if (in_array($mimetype, $this->snap_multimedia()) && $resourcedisplayincourse) {
                    $modclasses[] = 'js-snap-media';
                }
                // For images we overwrite with the native class.
                if ($this->is_image_mod($mod)) {
                    $modclasses = ['snap-native-image', 'snap-image', 'snap-mime-'.$mimetype];
                }
            } else if ($mod->modname === 'folder' && !$mod->url) {
                // Folder mod set to display on page.
                $modclasses = ['snap-activity'];
            } else if (plugin_supports('mod', $mod->modname, FEATURE_MOD_ARCHETYPE) === MOD_ARCHETYPE_RESOURCE) {
                $modclasses = ['snap-resource'];
                if ($mod->modname !== 'label') {
                    $modclasses = ['snap-resource', 'snap-resource-long'];
                }
            } else if ($mod->modname === 'scorm') {
                $modclasses = ['snap-resource', 'snap-resource-long'];
            } else if ($mod->modname !== 'label') {
                $modclasses = ['snap-activity'];
            }

            // Special classes for native html elements.
            // BEGIN LSU Book native to activity module.
            // if (in_array($mod->modname, ['page', 'book'])) {
            //     $modclasses = ['snap-native', 'snap-mime-'.$mod->modname];
            //     $attr['aria-expanded'] = "false";
            if (in_array($mod->modname, ['page'])) {
                $modclasses = array('snap-native', 'snap-mime-'.$mod->modname);
                $attr['aria-expanded'] = "false";
            } else if (in_array($mod->modname, ['book'])) {
                $modclasses = array('snap-activity', 'snap-mime-'.$mod->modname);
                $attr['aria-expanded'] = "false";
            // END LSU Book native to activity module.
            } else if ($modurl = $mod->url) {
                // For snap cards, js uses this to make the whole card clickable.
                if ($mod->uservisible) {
                    $attr['data-href'] = $modurl;
                }
            }

            // Is this mod draft?
            // We don't need visibleold as a condition here since it can affect a
            // module merged from a course to another, and the draft class won't be applied.
            // The "Not published to students" message won't be displayed next to the course module so teachers do not
            // realize that the content is not available to students.
            $section = $mod->get_section_info();
            if (!$mod->visible) {
                // If the section is hidden check the visibleold to prevent
                // the message will be displayed in all modules.
                if ($section->visible || (!$section->visible && !$mod->visibleold)) {
                    $modclasses[] = 'draft';
                }
            }

            // Is this mod stealth?
            if ($mod->is_stealth()) {
                $modclasses[] = 'stealth';
            }
            if ($mod->visible && $section && !$section->visible) {
                $modclasses[] = 'stealth-section-hidden';
            }

            $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $mod->context);
            // If the module isn't available, or we are a teacher (can view hidden activities) then get availability
            // info.
            $availabilityinfo = '';
            if (!$mod->available || $canviewhidden) {
                $availabilityinfo = $this->course_section_cm_availability($mod, $displayoptions);
            }

            if ($availabilityinfo !== '' && !$mod->uservisible || $canviewhidden) {
                $modclasses[] = 'conditional';
            }
            if (!$mod->available && !$mod->uservisible) {
                $modclasses[] = 'unavailable';
            }
            // TODO - can we add completion data.
            if (has_any_capability(['moodle/course:update', 'moodle/course:manageactivities'], $mod->context)) {
                $modclasses[] = 'snap-can-edit';
            }
            if (has_capability('moodle/course:viewhiddenactivities', $mod->context)) {
                $modclasses[] = 'snap-can-view-hidden';
            }

            $modclasses[] = 'snap-asset'; // Added to stop conflicts in flexpage.
            $modclasses[] = 'activity'; // Moodle needs this for drag n drop.
            $modclasses[] = $mod->modname;
            $modclasses[] = "modtype_$mod->modname";
            $modclasses[] = $mod->extraclasses;

            $attr['data-type'] = $snapmodtype;
            $attr['class'] = implode(' ', $modclasses);
            $attr['id'] = 'module-' . $mod->id;
            $attr['data-modcontext'] = $mod->context->id;

            $output .= html_writer::tag('li', $modulehtml, $attr);
        }
        return $output;
    }


    /**
     * Renders HTML to show course module availability information
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_availability(cm_info $mod, $displayoptions = []) {
        // If we have available info, always spit it out.
        if (!$mod->uservisible && !empty($mod->availableinfo)) {
            $availinfo = $mod->availableinfo;
        } else {
            $ci = new \core_availability\info_module($mod);
            $availinfo = $ci->get_full_information();
        }

        if ($availinfo) {
            $formattedinfo = \core_availability\info::format_info(
                $availinfo, $mod->get_course());
            return $formattedinfo;
        }

        return '';
    }

    /**
     * Renders HTML for completion tracking box on course page
     *
     * If completion is disabled, returns empty string
     * If completion is automatic, returns an icon of the current completion state
     * If completion is manual, returns a form (with an icon inside) that allows user to
     * toggle completion
     *
     * @param stdClass $course course object
     * @param \completion_info $completioninfo completion info for the course, it is recommended
     *     to fetch once for all modules in course/section for performance
     * @param cm_info $mod module to show completion for
     * @param array $displayoptions display options, not used in core
     * @return string
     * @throws \dml_exception
     */
    public function snap_course_section_cm_completion($course, &$completioninfo, cm_info $mod, $displayoptions = []) {
        global $CFG, $USER, $DB;

        $output = '';

        $istrackeduser = $completioninfo->is_tracked_user($USER->id);
        $isediting = $this->page->user_is_editing();

        if (!empty($displayoptions['hidecompletion']) || !isloggedin() || isguestuser() || !$mod->uservisible) {
            return $output;
        }
        if ($completioninfo === null) {
            $completioninfo = new \completion_info($course);
        }
        $completion = $completioninfo->is_enabled($mod);

        if ($completion == COMPLETION_TRACKING_NONE) {
            if ($isediting) {
                $output .= html_writer::span('&nbsp;', 'filler');
            }
            return $output;
        }

        $completionicon = '';

        if ($isediting || !$istrackeduser) {
            switch ($completion) {
                case COMPLETION_TRACKING_MANUAL :
                    $completionicon = 'manual-enabled';
                    break;
                case COMPLETION_TRACKING_AUTOMATIC :
                    $completionicon = 'auto-enabled';
                    break;
            }
        } else {
            $completiondata = $completioninfo->get_data($mod, true);
            if ($completion == COMPLETION_TRACKING_MANUAL) {
                switch($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE:
                        $completionicon = 'manual-n' . ($completiondata->overrideby ? '-override' : '');
                        break;
                    case COMPLETION_COMPLETE:
                        $completionicon = 'manual-y' . ($completiondata->overrideby ? '-override' : '');
                        break;
                }
            } else { // Automatic completion.
                switch($completiondata->completionstate) {
                    case COMPLETION_INCOMPLETE:
                        $completionicon = 'auto-n' . ($completiondata->overrideby ? '-override' : '');
                        break;
                    case COMPLETION_COMPLETE:
                        $completionicon = 'auto-y' . ($completiondata->overrideby ? '-override' : '');
                        break;
                    case COMPLETION_COMPLETE_PASS:
                        $completionicon = 'auto-pass';
                        break;
                    case COMPLETION_COMPLETE_FAIL:
                        $completionicon = 'auto-fail';
                        break;
                }
            }
        }
        if ($completionicon) {
            $formattedname = html_entity_decode($mod->get_formatted_name(), ENT_QUOTES, 'UTF-8');
            if (!$isediting && $istrackeduser && $completiondata->overrideby) {
                $args = new stdClass();
                $args->modname = $formattedname;
                $overridebyuser = \core_user::get_user($completiondata->overrideby, '*', MUST_EXIST);
                $args->overrideuser = fullname($overridebyuser);
                $imgalt = get_string('completion-alt-' . $completionicon, 'completion', $args);
            } else {
                $imgalt = get_string('completion-alt-' . $completionicon, 'completion', $formattedname);
            }

            if ($isediting || !$istrackeduser || !has_capability('moodle/course:togglecompletion', $mod->context)) {
                // When editing, the icon is just an image.
                $completionpixicon = new \pix_icon('i/completion-'.$completionicon, $imgalt, '',
                    ['class' => 'iconsmall', 'id' => 'completion-button-' . $mod->id]);
                $output .= html_writer::tag('span', $this->output->render($completionpixicon),
                    ['class' => 'autocompletion']);
            } else if ($completion == COMPLETION_TRACKING_MANUAL) {
                $newstate =
                    $completiondata->completionstate == COMPLETION_COMPLETE
                        ? COMPLETION_INCOMPLETE
                        : COMPLETION_COMPLETE;
                // In manual mode the icon is a toggle form...

                // If this completion state is used by the
                // conditional activities system, we need to turn
                // off the JS.
                $extraclass = '';
                if (!empty($CFG->enableavailability) &&
                    \core_availability\info::completion_value_used($course, $mod->id)) {
                    $extraclass = ' preventjs';

                }

                $output .= html_writer::start_tag('form', ['method' => 'post',
                    'action' => new moodle_url('/course/togglecompletion.php'),
                    'class' => 'togglecompletion', ]);
                $output .= html_writer::start_tag('div');
                $output .= html_writer::empty_tag('input', [
                    'type' => 'hidden', 'name' => 'id', 'value' => $mod->id, ]);
                $output .= html_writer::empty_tag('input', [
                    'type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey(), ]);
                $output .= html_writer::empty_tag('input', [
                    'type' => 'hidden', 'name' => 'modulename', 'value' => $formattedname, ]);
                $output .= html_writer::empty_tag('input', [
                    'type' => 'hidden', 'name' => 'completionstate', 'value' => $newstate, ]);
                $output .= html_writer::tag('button',
                    $this->output->pix_icon('i/completion-' . $completionicon, $imgalt,'', ['title' => '']),
                    ['class' => 'btn btn-link', 'id' => 'completion-button-' . $mod->id]);
                $output .= html_writer::end_tag('div');
                $output .= html_writer::end_tag('form');
            } else {
                // In auto mode, the icon is just an image.
                $showcompletionconditions = $course->showcompletionconditions == COMPLETION_SHOW_CONDITIONS;
                $completiondetails = cm_completion_details::get_instance($mod, $USER->id, $showcompletionconditions);
                $showcompletioninfo = $completiondetails->has_completion() &&
                ($showcompletionconditions || $completiondetails->show_manual_completion());
                if (!$showcompletioninfo) {
                    return $output;
                }
                $completionpixicon = new \pix_icon('i/completion-'.$completionicon, $imgalt, '', ['id' => 'completion-button-' . $mod->id]);
                $span = html_writer::tag('span', $this->output->render($completionpixicon),
                    ['class' => 'autocompletion']);
                $data = (object) [
                    'istrackeduser' => true,
                    'hasconditions' => true,
                    'completiondetails' => $completiondetails
                ];
                $dialogcontent = $this->output->render_from_template('core_courseformat/local/content/cm/completion_dialog', $data);
                $dialog = new dropdown_dialog(
                    $this->output->render($completionpixicon),
                    $this->get_completion_dialog_content($mod),
                    [
                        'classes' => 'completion-dropdown',
                        'buttonclasses' => 'btn btn-icon',
                        'dropdownposition' => dropdown_dialog::POSITION['end'],
                    ]
                );
                $dialog = $this->output->render($dialog);
                $output .= $dialog;
            }
        }
        return $output;
    }

    /**
     * Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link cm_info::get_after_link()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param \stdClass $course
     * @param \completion_info $completioninfo
     * @param \cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {
        global $COURSE, $CFG;

        $output = '';
        // We return empty string (because course module will not be displayed at all)
        // when
        // 1) The activity is not visible to users
        // and also
        // 2) The 'availableinfo' is empty, i.e. the activity was
        // hidden in a way that leaves no info, such as using the
        // eye icon.
        if (!$mod->uservisible
            && (empty($mod->availableinfo))) {
            return $output;
        }
        if (!$mod->is_visible_on_course_page()) {
            return $output;
        }

        $arialabelasset = $mod->get_module_type_name() . ' ' . get_string('activity', 'theme_snap');
        $output .= '<div class="asset-wrapper" role="group" aria-label="'.$arialabelasset.'">';

        // Drop section notice.
        if (has_capability('moodle/course:update', $mod->context)) {
            $output .= '<a class="snap-move-note" href="#">'.get_string('movehere', 'theme_snap').'</a>';
        }
        // Start the div for the activity content.
        $output .= "<div class='activityinstance'>";
        // Display the link to the module (or do nothing if module has no url).
        $cmname = $this->course_section_cm_name($mod, $displayoptions);
        $assetlink = '';

        if (!empty($cmname)) {
            // Activity/resource type.
            $assetlink .= '<h3 class="snap-asset-link">'.$cmname.'</h3>';
        }

        // Asset content.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);

        // Asset metadata - groups, completion etc.
        // Due date, feedback available and all the nice snap things.
        $snapcompletionmeta = '';
        $snapcompletiondata = $this->module_meta_html($mod);
        if ($snapcompletiondata) {
            $snapcompletionmeta = '<div class="snap-completion-meta">'.$snapcompletiondata.'</div>';
        }

        // Completion tracking.
        $completiontracking = '<div class="snap-asset-completion-tracking">';
        $completiontracking .= $this->snap_course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);
        $completiontracking .= '</div>';

        // Add specific class if the completion tracking is disabled for an activity.
        $completion = $completioninfo->is_enabled($mod);
        if ($completion == COMPLETION_TRACKING_NONE) {
            $completiontracking = '<div class="disabled-snap-asset-completion-tracking">';
            $completiontracking .= $this->snap_course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);
            $completiontracking .= '</div>';
        }

        // Draft & Stealth tags.
        $stealthtag = '';
        $drafttag = '';
        // Stealth tag.
        $stealthtag = '<div class="snap-stealth-tag">'.get_string('hiddenoncoursepage', 'moodle').'</div>';
        // Draft status - always output, shown via css of parent.
        $drafttag = '<div class="snap-draft-tag">'.get_string('draft', 'theme_snap').'</div>';

        // Group.
        $groupmeta = '';
        // Resources cannot have groups/groupings.
        if ($mod->modname !== 'resource') {
            $canmanagegroups = has_capability('moodle/course:managegroups', context_course::instance($mod->course));
            // This will show a grouping (group of groups) name against a module if one has been assigned to the module instance.
            if ($canmanagegroups && !empty($mod->groupingid)) {
                // Grouping label.
                $groupings = groups_get_all_groupings($mod->course);
                $groupmeta .= '<div class="snap-grouping-tag">'.format_string($groupings[$mod->groupingid]->name).'</div>';
            }
        }

        $canviewhidden = has_capability('moodle/course:viewhiddenactivities', $mod->context);
        // If the module isn't available, or we are a teacher (can view hidden activities) then get availability
        // info. Restrictions will appear on click over a lock image inside the activity header.
        $coursetoolsicon = '';
        if (!$mod->available || $canviewhidden) {
            $availabilityinfo = $this->course_section_cm_availability($mod, $displayoptions);
            if ($availabilityinfo) {
                $ariaconditionaltag = get_string('activityrestriction', 'theme_snap');
                $conditionaltagsrc = $this->output->image_url('lock', 'theme');
                $datamodcontext = $mod->context->id;
                $conditionaliconid = "snap-restriction-$datamodcontext";
                $restrictionsource = html_writer::tag('img', '', [
                    'class' => 'svg-icon',
                    'title' => $ariaconditionaltag,
                    'aria-hidden' => 'true',
                    'src' => $conditionaltagsrc,
                ]);
                $coursetoolsicon = html_writer::tag('a', $restrictionsource, [
                    'tabindex' => '0',
                    'class' => 'snap-conditional-tag',
                    'role' => 'button',
                    'data-toggle' => 'popover',
                    'data-trigger' => 'focus',
                    'data-placement' => 'right',
                    'id' => $conditionaliconid,
                    'data-html' => 'true',
                    'clickable' => 'true',
                    'data-content' => $availabilityinfo,
                    'aria-label' => $ariaconditionaltag,
                ]);
            }
        }

        // Add draft, conditional.
        $assetmeta = $stealthtag.$drafttag;

        // Add edit menu to Snap.
        $snapmenu = '';
        $snapmenu .= $this->course_section_menu_actions($mod);

        // Group modes.
        $groupsmenu = '';
        $courseformat = course_get_format($mod->get_course());
        $canmanagegroups = has_capability('moodle/course:managegroups', context_course::instance($mod->course));
        if ($canmanagegroups && $courseformat->show_groupmode($mod)) {
            $groupsmenu .= $this->activity_groups_menu_actions($mod);
        }
        // Build output.
        $forumposts = '<div class="badge badge-secondary">'.$this->get_forum_unread_posts($mod).'</div>';
        $postcontent = '<div class="snap-asset-meta" data-cmid="'.$mod->id.'">'.$assetmeta.$mod->afterlink.$forumposts.'</div>';
        $content = '<div class="snap-asset-content">'.$postcontent.$contentpart.$snapcompletionmeta.$groupmeta.'</div>';
        $cardicons = '<div class="snap-header-card-icons">'.$completiontracking.$coursetoolsicon.$groupsmenu.$snapmenu.'</div>';
        $output .= '<div class="snap-header-card">'.$assetlink.$cardicons.'</div>'.$content;

        // Bail at this point if we aren't using a supported format. (Folder view is only partially supported).
        $supported = ['topics', 'weeks', 'site'];
        if (!in_array($COURSE->format, $supported)) {
            $format = course_get_format($course);
            if ($sectionreturn) {
                $format->set_sectionnum($sectionreturn);
            }
            $modinfo = $format->get_modinfo();
            $section = $modinfo->get_section_info($format->get_sectionnum());
            $cmclass = $format->get_output_classname('content\\cm');
            $cm = new $cmclass($format, $section, $mod, $displayoptions);
            $renderer = $format->get_renderer($this->page);
            $data = $cm->export_for_template($renderer);
            return $this->output->render_from_template('core_courseformat/local/content/cm', $data) .$assetmeta;
        }

        // Allow moving and rearranging multiple activities at once.
        if (has_capability('moodle/course:manageactivities', context_module::instance($mod->id))) {
            $movealt = s(get_string('move', 'theme_snap', $mod->get_formatted_name()));
            $moveactivity = '<label class="snap-asset-move-label" aria-label="' . $movealt . '" for="snap-move-mod-' . $mod->id . 'role="button"">';
            $moveactivity .= '<input class="snap-asset-move-input js-snap-asset-move" id="snap-move-mod-' . $mod->id . '"
                                role="button" type="checkbox">';
            $moveactivity .= '<span class="sr-only">' . $movealt . '</span></label>';
            $output .= "<div hidden class='snap-asset-move-wrapper js-only' role='region' aria-label='" .
                            get_string('courseactionslabel', 'theme_snap') . "'>" . $moveactivity . "</div>";
        }

        $output .= "</div>"; // Close .activityinstance.
        $output .= "</div>"; // Close .asset-wrapper.
        return $output;
    }

    /**
     * Renders html to display the module content on the course page (i.e. text of the labels)
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_text(cm_info $mod, $displayoptions = []) {
        $output = '';
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            // Nothing to be displayed to the user.
            return $output;
        }

        // Get custom module content for Snap, or get modules own content.
        $modmethod = 'mod_'.$mod->modname.'_html';
        if ($this->is_image_mod($mod)) {
            $content = $this->mod_image_html($mod);
        } else if (method_exists($this,  $modmethod )) {
            $content = call_user_func([$this, $modmethod], $mod);
        } else {
            $content = $mod->get_formatted_content(['overflowdiv' => false, 'noclean' => true]);
        }

        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {
            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
            has_capability('moodle/course:viewhiddenactivities',
            context_course::instance($mod->course));
            if ($accessiblebutdim) {
                if ($conditionalhidden) {
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }
        }
        if ($mod->url) {
            if ($content) {
                // If specified, display extra content after link.
                $output = html_writer::tag('div', $content, ['class' => trim('contentafterlink ' . $textclasses)]);
                // Add chevron icon to content.
                $output .= '<div class="mt-3">
                            <a href="'.$mod->url.'&forceview=1" aria-label="'. get_string('gotoactivity', 'theme_snap', $mod->name) .'"><i class="fa fa-chevron-down" aria-hidden="true"></i></a>
                </div>';
            }
        } else {
            $snapmodtype = $this->get_mod_type($mod)[0];
            // Label title should not be displayed in the activity card header.
            $labelcurrentstr = get_string('modulename', 'mod_label');
            if (strcmp($snapmodtype, $labelcurrentstr) == 0) {
                $snapmodtype = '';
            }
            $assettype = '<div class="snap-assettype">'.$snapmodtype.'</div>';

            // No link, so display only content.
            $output = html_writer::tag('div', $assettype . $accesstext . $content,
                ['class' => 'contentwithoutlink text-break' . $textclasses]);
        }
        return $output;
    }

    /*
    ***** SNAP SPECIFIC DISPLAY OF RESOURCES *******
    */

    /**
     * Get module type
     * Note, if module is a resource, get the actual file type
     *
     * @author Guy Thomas
     * @date 2014-06-16
     * @param cm_info $mod
     * @return array
     */
    protected function get_mod_type(cm_info $mod): array {
        if ($mod->modname === 'resource') {
            $fs = get_file_storage();
            $files = $fs->get_area_files($mod->context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
            $mainfile = $files ? reset($files) : null;
            $ext = strtolower(pathinfo($mainfile->get_filename(), PATHINFO_EXTENSION));
            $filetypegroups = get_mimetypes_array();
            $extension = [
                'powerpoint' => 'ppt',
                'document' => 'doc',
                'spreadsheet' => 'xls',
                'archive' => 'zip',
                'pdf' => 'pdf',
                'text' => 'txt',
            ];
            $mimetype = $ext;
            if (isset($filetypegroups[$ext])) {
                $mimetype = $filetypegroups[$ext];
                $mimetype = $icon['string'] ?? $mimetype['icon'];
            }
            $ext = $extension[$mimetype] ?? $ext;
            return [$ext, $mimetype];
        } else {
            return [$mod->modfullname, null];
        }
    }

    /**
     * Is this an image module
     * @param cm_info $mod
     * @return bool
     */
    protected function is_image_mod(cm_info $mod) {
        if ($mod->modname == 'resource') {
            $fs = get_file_storage();
            $files = $fs->get_area_files($mod->context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
            $mainfile = $files ? reset($files) : null;
            if (file_extension_in_typegroup($mainfile->get_filename(), 'web_image')) {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * Submission call to action.
     *
     * @param cm_info $mod
     * @param activity_meta $meta
     * @return string
     * @throws \coding_exception
     */
    public static function submission_cta(cm_info $mod, activity_meta $meta) {
        global $CFG;

        if (empty($meta->submissionnotrequired)) {
            $url = $CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id;

            if ($meta->submitted) {
                if (empty($meta->timesubmitted)) {
                    $submittedonstr = '';
                } else {
                    $submittedonstr = ' '.userdate($meta->timesubmitted, get_string('strftimedate', 'langconfig'));
                }
                $message = $meta->submittedstr.$submittedonstr;
            } else {
                $warningstr = $meta->draft ? $meta->draftstr : $meta->notsubmittedstr;
                $warningstr = $meta->reopened ? $meta->reopenedstr : $warningstr;
                $message = $warningstr;
            }
            return html_writer::link($url, $message);
        }
        return '';
    }

    /**
     * Get the module meta data for a specific module.
     *
     * @param cm_info $mod
     * @return string
     */
    protected function module_meta_html(cm_info $mod) {
        global $COURSE;

        $content = '';

        if (is_guest(context_course::instance($COURSE->id))) {
            return '';
        }

        // Do we have an activity function for this module for returning meta data?
        // @todo - check module lib.php for a meta function (won't work for core mods but will for ours if we wish).
        $meta = activity::module_meta($mod);
        if (!$meta->is_set(true)) {
            // Can't get meta data for this module.
            return '';
        }

        if ($meta->isteacher) {
            // Teacher - useful teacher meta data.
            $engagementmeta = [];

            // Below, !== false means we get 0 out of x submissions.
            if (!$meta->submissionnotrequired && $meta->numsubmissions !== false) {
                $engagementmeta[] = get_string('xofy'.$meta->submitstrkey, 'theme_snap',
                    (object) [
                        'completed' => $meta->numsubmissions,
                        'participants' => \theme_snap\local::course_participant_count($COURSE->id, $mod->modname),
                    ]
                );
            }

            if ($meta->numrequiregrading) {
                $engagementmeta[] = get_string('xungraded', 'theme_snap', $meta->numrequiregrading);
            }
            if (!empty($engagementmeta)) {
                $engagementstr = implode(', ', $engagementmeta);

                $params = [
                    'action' => 'grading',
                    'id' => $mod->id,
                    'tsort' => 'timesubmitted',
                    'filter' => 'require_grading',
                ];
                $url = new moodle_url("/mod/{$mod->modname}/view.php", $params);

                $link = html_writer::link($url, $engagementstr);
                $content .= html_writer::tag('p', $link);
            }
            $suspended = \theme_snap\local::suspended_participant_count($COURSE->id, $mod->id);
            if ($suspended) {
                $content .= html_writer::tag('p', get_string("quizattemptswarn", "theme_snap"));
            }

        } else {
            // Feedback meta.
            if (!empty($meta->grade)) {
                // Note - the link that a module takes you to would be better off defined by a function in
                // theme/snap/activity - for now its just hard coded.
                $url = new \moodle_url('/grade/report/user/index.php', ['id' => $COURSE->id]);
                if (in_array($mod->modname, ['quiz', 'assign'])) {
                    $url = new \moodle_url('/mod/'.$mod->modname.'/view.php?id='.$mod->id);
                }
                $feedbackavailable = get_string('feedbackavailable', 'theme_snap');
                if ($mod->modname != 'lesson') {
                    $content .= html_writer::link($url, $feedbackavailable);
                }
            }

            // @codingStandardsIgnoreLine
            /* @var cm_info $mod */
            $content .= self::submission_cta($mod, $meta);
        }

        $modstoshowactivityopendate = ['data', 'quiz', 'scorm', 'workshop', 'assign'];

        // Activity open date.
         if (!empty($meta->timeopen) && in_array($mod->modname, $modstoshowactivityopendate)) {
            $dateformat = get_string('strftimedate', 'langconfig');
            $url = new moodle_url("/mod/{$mod->modname}/view.php", ['id' => $mod->id]);
            $pastopen = $meta->timeopen < time();
            $labeltext = $pastopen ? get_string('opened', 'theme_snap', userdate($meta->timeopen, $dateformat)) :
                get_string('opens', 'theme_snap', userdate($meta->timeopen, $dateformat));
            $dateclass = $pastopen ? 'snap-opened-date' : 'snap-open-date';
            $content .= html_writer::link($url, $labeltext,
                [
                    'class' => 'tag tag-success ' . $dateclass,
                    'data-from-cache' => $meta->timesfromcache ? 1 : 0,
                ]);
        }

        // Activity due date.
        if (!empty($meta->extension) || !empty($meta->timeclose)) {
            $dateformat = get_string('strftimedate', 'langconfig');
            if (!empty($meta->extension)) {
                $field = 'extension';
            } else if (!empty($meta->timeclose)) {
                $field = 'timeclose';
            }
            $labeltext = get_string('due', 'theme_snap', userdate($meta->$field, $dateformat));
            $pastdue = $meta->$field < time();
            $url = new \moodle_url("/mod/{$mod->modname}/view.php", ['id' => $mod->id]);
            $dateclass = $pastdue ? 'tag-danger' : 'tag-warning';
            $content .= html_writer::link($url, $labeltext,
                    [
                        'class' => 'snap-due-date tag '.$dateclass,
                        'data-from-cache' => $meta->timesfromcache ? 1 : 0,
                    ]);
        }

        return $content;
    }


    /**
     * Get resource module image html
     *
     * @param stdClass $mod
     * @return string
     */
    protected function mod_image_html($mod) {
        if (!$mod->uservisible) {
                return "";
        }

        $fs = get_file_storage();
        $context = \context_module::instance($mod->id);
        // TODO: this is not very efficient!!
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
        if (count($files) > 0) {
            foreach ($files as $file) {
                $imgsrc = \moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        $file->get_itemid(),
                        $file->get_filepath(),
                        $file->get_filename(),
                );
            }
        }

        $summary = '';
        $summary = $mod->get_formatted_content(['overflowdiv' => false, 'noclean' => true]);
        $modname = format_string($mod->name);
        $img = format_text('<img src="' .$imgsrc. '" alt="' .$modname. '"/>');
        $icon = '<img title="' .get_string('vieworiginalimage', 'theme_snap'). '"
                alt="' .get_string('vieworiginalimage', 'theme_snap'). '"
                src="' .$this->output->image_url('arrow-expand', 'theme'). '">';
        $imglink = '<a class="snap-expand-link" href="' .$imgsrc. '" target="_blank">' .$icon. '</a>';

        $output = '<figure class="snap-resource-figure figure">'
                    .$img.$imglink.
                    '<figcaption class="snap-resource-figure-caption figure-caption">'
                        .$modname.$summary.
                    '</figcaption>
                </figure>';

        return $output;
    }

    /**
     * Get page module html
     * @param cm_info $mod
     * @return string
     */
    protected function mod_page_html(cm_info $mod) {
        if (!$mod->uservisible) {
            return "";
        }

        $page = \theme_snap\local::get_page_mod($mod);

        $preview = $page->summary;

        $showexpandicon = true;
        if (!$page->intro) {
            $preview = shorten_text($page->content, 200);
            if ($preview == $page->content) {
                $showexpandicon = false;
            }
        }

        $readmore = get_string('readmore', 'theme_snap');
        $close = get_string('collapseicon', 'theme_snap');
        $expand = get_string('expandicon', 'theme_snap');

        $content = '';
        $contentloaded = 0;
        if (empty(get_config('theme_snap', 'lazyload_mod_page'))) {
            // Identify content elements which should force an AJAX lazy load.
            $elcontentblist = ['iframe', 'video', 'object', 'embed', 'model-viewer'];
            $content = $page->content;
            $lazyload = false;
            foreach ($elcontentblist as $el) {
                if (stripos($content, '<'.$el) !== false) {
                    $content = ''; // Don't include the content as it is likely to slow the page load down considerably.
                    $lazyload = true;
                }
            }
            $contentloaded = !$lazyload ? 1 : 0;
        }
        // With previous design, we allow displaying videos.
        if ($content == '') {
            if (stripos($page->content, '<video') !== false) {
                $content = $page->content;
                $contentloaded = 1;
            }
        }

        $pmcontextattribute = 'data-pagemodcontext="'.$mod->context->id.'"';
        $expandpagebutton = "
            <button 
                class='btn collapsed pagemod-readmore readmore-button snap-action-icon btn-outline-primary p-2'
                {$pmcontextattribute}
                aria-expanded='false'>
                <i aria-hidden='true' class='icon fa fa-chevron-down fa-fw' title='{$expand} {$page->name}'></i>
            </button>
        ";
        $o = "
        <div class='summary-container'>
            <div class='summary-text'>
                {$preview}
            </div>
        </div>";
        if ($showexpandicon) {
            $o .= "
            <div class='readmore-container d-flex justify-content-center'>
                {$expandpagebutton}
            </div>
            <div class=pagemod-content tabindex='-1' data-content-loaded={$contentloaded}>
                <div id='pagemod-content-container'>
                    {$content}
                </div>
                <div class='d-flex justify-content-center w-100 pt-3'>
                    <button class='snap-action-icon btn btn-outline-primary p-2 d-inline-flex' aria-expanded='true' title='{$close} {$page->name}'>
                        <i aria-hidden='true' class='icon fa fa-chevron-up fa-fw m-0' title='{$expand} {$page->name}'></i>
                    </button>
                </div>
            </div>";
        }
        return $o;
    }

    protected function mod_book_html($mod) {
        if (!$mod->uservisible) {
            return "";
        }
        global $DB;

        $cm = get_coursemodule_from_id('book', $mod->id, 0, false, MUST_EXIST);
        $book = $DB->get_record('book', ['id' => $cm->instance], '*', MUST_EXIST);
        $chapters = book_preload_chapters($book);

        if ($book->intro) {
            $context = context_module::instance($mod->id);
            $content = file_rewrite_pluginfile_urls($book->intro, 'pluginfile.php', $context->id, 'mod_book', 'intro', null);
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;
            $content = format_text($content, $book->introformat, $formatoptions);
            $o = '<div class="summary-text row">';
            $o .= '<div class="content-row col-sm-6">' .$content. '</div>';
            $o .= '<div class="chapters-row col-sm-6">' .$this->book_get_toc($chapters, $book, $cm) . '</div>';
            $o .= '</div>';
            return $o;
        }
        return $this->book_get_toc($chapters, $book, $cm);
    }

    /**
     * Simplified book toc Get assignment module html (includes meta data);
     *
     * Based on the function of same name in mod/book/localib.php
     * @param $mod
     * @return string
     */
    public function book_get_toc($chapters, $book, $cm) {
        $context = context_module::instance($cm->id);

        switch ($book->numbering) {
            case BOOK_NUM_BULLETS :
                $numclass = 'list-bullets';
                break;
            case BOOK_NUM_INDENTED:
                $numclass = 'list-indented';
                break;
            case BOOK_NUM_NONE:
                $numclass = 'list-none';
                break;
            case BOOK_NUM_NUMBERS :
            default :
                $numclass = 'list-numbers';
        }

        $toc = "<h4>".get_string('chapters', 'theme_snap')."</h4>";
        $toc .= '<ol class="bookmod-chapters '.$numclass.'">';
        $closemeflag = false; // Control for indented lists.
        $chapterlist = '';
        foreach ($chapters as $ch) {
            $title = trim(format_string($ch->title, true, ['context' => $context]));
            if (!$ch->hidden) {
                if ($closemeflag && !$ch->parent) {
                    $chapterlist .= "</ul></li>";
                    $closemeflag = false;
                }
                $chapterlist .= "<li>";
                $chapterlist .= html_writer::link(new moodle_url('/mod/book/view.php',
                    ['id' => $cm->id, 'chapterid' => $ch->id]), $title, []);
                if ($ch->subchapters) {
                    $chapterlist .= "<ul>";
                    $closemeflag = true;
                } else {
                    $chapterlist .= "</li>";
                }
            }
        }
        $toc .= $chapterlist.'</ol>';
        return $toc;
    }

    /**
     * Every mime type we consider to be multimedia.
     * @return array
     */
    protected function snap_multimedia() {
        return ['mp3', 'wav', 'audio', 'mov', 'wmv', 'video', 'mpeg', 'avi', 'quicktime', 'flash'];
    }

    /**
     * Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link
     *
     * Note, that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = []) {
        global $DB, $CFG;
        $output = '';

        // Nothing to be displayed to the user.
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            return $output;
        }

        // Is this for labels or something with no other page url to point to?
        $url = $mod->url;
        if (!$url) {
            return $output;
        }

        // Get asset name.
        $instancename = $mod->get_formatted_name();
        $groupinglabel = $mod->get_grouping_label();

        $target = '';
        $cmname = $mod->modname;
        $iconurl = $mod->get_icon_url();

        $activityimg = "<div class='activityiconcontainer ".$cmname."'>";
        if (strpos($iconurl, $CFG->wwwroot) !== 0) { // For LTI activities with custom icon URLs.
            $activityimg = "<div class='activityiconcontainer ".$cmname."' style='background-color:transparent;'>";
        }
        $activityimg .= "<img class='iconlarge activityicon' alt='' role='presentation' src='".$iconurl."' />";
        $activityimg .= "</div>";

        // Multimedia mods we want to open in the same window.
        $snapmultimedia = $this->snap_multimedia();

        $resourcedisplay = get_config('theme_snap', 'resourcedisplay');
        $displaydescription = get_config('theme_snap', 'displaydescription');
        $resourceonclick = "";
        if ($mod->modname === 'resource') {
            $extension = $this->get_mod_type($mod)[1];
            if (in_array($extension, $snapmultimedia)) {
                // For multimedia we need to handle the popup setting.
                // If popup add a redirect param to prevent the intermediate page.
                if ($mod->onclick) {
                    $resourceonclick = "onclick=\"{$mod->onclick}\"";
                    $url = '';
                }
            } else {
                if ($resourcedisplay == 'card' && $displaydescription) {
                    $url .= "&amp;forceview=1";
                } else {
                    if ($mod->onclick) {
                        $resourceonclick = "onclick=\"{$mod->onclick}\"";
                        $url = '';
                    }
                }
            }
        }

        $onclicklti = $this->theme_snap_lti_get_launch_container($mod);

        if ($mod->modname === 'url') {
            $urlmod = $DB->get_record('url', ['id' => $mod->instance], '*', MUST_EXIST);
            $cm = get_coursemodule_from_instance('url', $urlmod->id);
            $fullurl = new moodle_url('/mod/url/view.php', ['id' => $cm->id]);

            if ($urlmod->display == RESOURCELIB_DISPLAY_POPUP) {
                // In-pop display.
                $fullurl .= "&amp;redirect=1";
                $options = empty($urlmod->displayoptions) ? [] : (array)unserialize_array($urlmod->displayoptions);
                $width = empty($options['popupwidth']) ? 620 : $options['popupwidth'];
                $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
                $wh = "width={$width},height={$height},toolbar=no,location=no,menubar=no,copyhistory=no,status=no,";
                $wh .= "directories=no,scrollbars=yes,resizable=yes";
                $onclickurl = "event.preventDefault(); window.open('{$fullurl}', '', '{$wh}'); return false;";
                $onclicklti = "onclick=\"{$onclickurl}\"";
                $url = '';

            } else if ($urlmod->display == RESOURCELIB_DISPLAY_NEW) {
                // New Window display.
                $fullurl .= "&amp;redirect=1";
                $onclickurl = "window.open('{$fullurl}'); return false;";
                $onclicklti = "onclick=\"{$onclickurl}\"";
                $url = '';
            } else {
                $url = $fullurl;
            }
        }
        // Activity/resource type.
        $snapmodtype = $this->get_mod_type($mod)[0];
        $assettype = '<div class="snap-assettype">'.$snapmodtype.'</div>';

        $output .= $activityimg;
        if ($mod->uservisible) {
            $output .= "<div class='snap-header-container'>"
                             .$assettype.
                            "<a $target $onclicklti $resourceonclick class='mod-link' href='$url' title='$instancename'>".
                                "<p class='instancename'>$instancename</p>
                            </a>
                        </div>";
            $output .= $groupinglabel;
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->uservisible).
            $output .= "<div class='snap-header-container'>".
                            $assettype.
                            "<p class='instancename'>$instancename</p>".
                        "</div> $groupinglabel";
        }

        return $output;
    }

    /**
     * Wrapper around course_get_cm_edit_actions
     *
     * @param cm_info $mod The module
     * @param int $sr The section to link back to (used for creating the links)
     * @return array Of action_link or pix_icon objects
     */
    protected function course_get_cm_edit_actions(cm_info $mod, $sr = null) {
        $actions = course_get_cm_edit_actions($mod, -1, $sr);
        $actions = array_filter($actions, function($action) {
            return !($action instanceof \action_menu_filler);
        });
        $rename = core_course_inplace_editable($mod, $mod->indent, $sr);
        $edittitle = get_string('edittitle');
        $rename = str_replace('</a>', "$edittitle</a>", $rename);
        $actions['edit-rename'] = $rename;

        return $actions;
    }

    /**
     * Return move notice.
     * @return bool|string
     * @throws moodle_exception
     */
    public function snap_footer_alert() {
        return $this->output->render_from_template('theme_snap/footer_alert', null);
    }

    /**
     * Generates a notification if course format is not topics or weeks the user is editing and is a teacher/mananger.
     *
     * @return string
     * @throws \coding_exception
     */
    public function course_format_warning() {
        global $COURSE;

        $format = $COURSE->format;
        if (in_array($format, ['weeks', 'topics', 'tiles'])) {
            return '';
        }

        if (!$this->page->user_is_editing()) {
            return '';
        }

        if (!has_capability('moodle/course:manageactivities', context_course::instance($COURSE->id))) {
            return '';
        }

        $url = new moodle_url('/course/edit.php', ['id' => $COURSE->id]);
        return $this->output->notification(get_string('courseformatnotification', 'theme_snap', $url->out()));
    }

    /**
     * Renders html to display a course search form.
     *
     * @param string $value default value to populate the search field
     * @param string $format display format - 'plain' (default), 'short' or 'navbar'
     * @return string
     */
    public function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }

        switch ($format) {
            case 'navbar' :
                $formid = 'coursesearchnavbar';
                $inputid = 'navsearchbox';
                $inputsize = 20;
                break;
            case 'short' :
                $inputid = 'shortsearchbox';
                $inputsize = 12;
                break;
            default :
                $inputid = 'coursesearchbox';
                $inputsize = 30;
        }

        $data = (object) [
            'searchurl' => (new moodle_url('/course/search.php'))->out(false),
            'id' => $formid,
            'inputid' => $inputid,
            'inputsize' => $inputsize,
            'value' => $value,
        ];

        return $this->render_from_template('theme_snap/course_search_form', $data);
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|coursecat $category
     */
    public function course_category($category) {
        global $CFG;
        $this->page->blocks->add_region('side-pre');
        $basecategory = \core_course_category::get(0);
        $coursecat = \core_course_category::get(is_object($category) ? $category->id : $category);
        $site = get_site();
        $output = '';
        $categoryselector = '';
        // NOTE - we output manage catagory button in the layout file in Snap.

        if (!$coursecat->id) {
            if (\core_course_category::is_simple_site() == 1) {
                // There exists only one category in the system, do not display link to it.
                $coursecat = \core_course_category::get_default();
                $strfulllistofcourses = get_string('fulllistofcourses');
                $this->page->set_title("$site->shortname: $strfulllistofcourses");
            } else {
                $strcategories = get_string('categories');
                $this->page->set_title("$site->shortname: $strcategories");
            }
        } else {
            $title = $site->shortname;
            if ($basecategory->get_children_count() > 1) {
                $title .= ": ". $coursecat->get_formatted_name();
            }
            $this->page->set_title($title);

            // Print the category selector.
            if ($basecategory->get_children_count() > 1) {
                $select = new \single_select(new moodle_url('/course/index.php'), 'categoryid',
                        \core_course_category::make_categories_list(), $coursecat->id, null, 'switchcategory');
                $select->set_label(get_string('category').':');
                $categoryselector .= $this->render($select);
            }
        }
        $output .= '<div class="row">';
        $output .= '<div class="d-flex flex-wrap row-gap-1">';
        // Add cat select box if available.
        if(!empty($categoryselector)){
            $output .= '<div class="px-3">';
            $output .= $categoryselector;
            $output .= '</div>';
        }
        $output .= '<div class="px-3">';
        // Add course search form.
        $output .= $this->course_search_form();
        $output .= '</div>';
        $output .= '</div>';

        $chelper = new \coursecat_helper();
        // Prepare parameters for courses and categories lists in the tree.
        $atts = ['class' => 'category-browse category-browse-'.$coursecat->id];
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)->set_attributes($atts);

        $coursedisplayoptions = [];
        $catdisplayoptions = [];
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->has_children()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, ['browse' => 'courses']);
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'categories']);
            $catdisplayoptions['viewmoretext'] = new \lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->has_courses()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, ['browse' => 'categories']);
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'courses']);
            $coursedisplayoptions['viewmoretext'] = new \lang_string('viewallcourses');
        } else {
            // We have a category that has both subcategories and courses, display pagination separately.
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'courses', 'page' => 1]);
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, ['browse' => 'categories', 'page' => 1]);
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        // Add action buttons.
        $context = get_category_or_system_context($coursecat->id);

        return $output;
    }

    /**
     * Prints a course footer with course contacts, course description and recent updates.
     *
     * @return string
     */

    public function course_footer() {
        global $DB, $COURSE, $CFG;

        // Check toggle switch.
        if (empty($this->page->theme->settings->coursefootertoggle)) {
            return false;
        }

        $context = context_course::instance($COURSE->id);
        $courseteachers = '';
        $coursesummary = '';

        $clist = new \core_course_list_element($COURSE);
        $teachers = $clist->get_course_contacts();

        if (!empty($teachers)) {
            // Get all teacher user records in one go.
            $teacherids = [];
            foreach ($teachers as $teacher) {
                $teacherids[] = $teacher['user']->id;
            }
            $teacherusers = $DB->get_records_list('user', 'id', $teacherids);

            // Course contacts.
            $courseteachers .= '<h4 class="h5">'.get_string('coursecontacts', 'theme_snap').'</h4>';
            foreach ($teachers as $teacher) {
                if (!isset($teacherusers[$teacher['user']->id])) {
                    continue;
                }
                $teacheruser = $teacherusers[$teacher['user']->id];
                $courseteachers .= $this->print_teacher_profile($teacheruser);
            }
        }
        // If user can edit add link to manage users.
        if (has_capability('moodle/course:enrolreview', $context)) {
            if (empty($courseteachers)) {
                $courseteachers = "<h4 class='h5'>".get_string('coursecontacts', 'theme_snap')."</h4>";
            }
            $courseteachers .= '<br><a id="enrolled-users" class="btn btn-outline-secondary btn-sm"
                href="'.$CFG->wwwroot.'/user/index.php?id='.$COURSE->id.'">'.get_string('enrolledusers', 'enrol').'</a>';
        }

        // Course cummary.
        if (!empty($COURSE->summary)) {
            $coursesummary = '<h4 class="h5">'.get_string('aboutcourse', 'theme_snap').'</h4>';
            $formatoptions = new stdClass;
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;
            $coursesummarycontent = file_rewrite_pluginfile_urls($COURSE->summary,
                'pluginfile.php', $context->id, 'course', 'summary', null);
            $coursesummarycontent = format_text($coursesummarycontent, $COURSE->summaryformat, $formatoptions);
            $coursesummary .= '<div id="snap-course-footer-summary">'.$coursesummarycontent.'</div>';
        }

        // If able to edit add link to edit summary.
        if (has_capability('moodle/course:update', $context)) {
            if (empty($coursesummary)) {
                $coursesummary = '<h4 class="h5">'.get_string('aboutcourse', 'theme_snap').'</h4>';
            }
            $coursesummary .= '<br><a id="edit-summary" class="btn btn-outline-secondary btn-sm"
            href="'.$CFG->wwwroot.'/course/edit.php?id='.$COURSE->id.'#id_descriptionhdr">'.get_string('edit').'</a>';
        }

        // Get recent activities on mods in the course.
        $courserecentactivities = $this->get_mod_recent_activity($context);
        $courserecentactivity = '';
        if ($courserecentactivities) {
            $courserecentactivity = '<h4 class="h5">'.get_string('recentactivity').'</h4>';
            if (!empty($courserecentactivities)) {
                $courserecentactivity .= $courserecentactivities;
            }
        }
        // If user can edit add link to moodle recent activity stuff.
        if (has_capability('moodle/course:update', $context)) {
            if (empty($courserecentactivities)) {
                $courserecentactivity = '<h4 class="h5">'.get_string('recentactivity').'</h4>';
                $courserecentactivity .= get_string('norecentactivity');
            }
            $courserecentactivity .= '<div class="col-xs-12 clearfix"><a href="'.$CFG->wwwroot.'/course/recent.php?id='
                .$COURSE->id.'">'.get_string('showmore', 'form').'</a></div>';
        }

        if (!empty($courserecentactivity)) {
            $columns[] = $courserecentactivity;
        }
        if (!empty($courseteachers)) {
            $columns[] = $courseteachers;
        }
        if (!empty($coursesummary)) {
            $columns[] = $coursesummary;
        }

        $output = '';
        if (empty($columns)) {
            return $output;
        } else {
            $output .= '<div class="row">';
            $output .= '<div class="col-lg-3 col-md-4"><ul id="snap-course-footer-contacts">'.$courseteachers.'</ul></div>';
            $output .= '<div class="col-lg-9 col-md-8"><div id="snap-course-footer-about">'.$coursesummary.'</div></div>';
            $output .= '<div class="col-sm-12"><div id="snap-course-footer-recent-activity">'.$courserecentactivity.'</div></div>';
            $output .= '</div>';
        }

        $data = [
            'output' => $this,
        ];

        $output .= $this->render_from_template('theme_boost/footer', $data);

        return $output;
    }

    /**
     * Helper function to decide whether to show the communication link or not.
     *
     * @return bool
     */
    public function has_communication_links(): bool {
        if (during_initial_install() || !\core_communication\api::is_available()) {
            return false;
        }
        return !empty($this->communication_link());
    }

    /**
     * Returns the communication link, complete with html.
     *
     * @return string
     */
    public function communication_link(): string {
        $link = $this->communication_url() ?? '';
        $commicon = $this->pix_icon('t/messages-o', '', 'moodle', ['class' => 'fa fa-comments']);
        $newwindowicon = $this->pix_icon('i/externallink', get_string('opensinnewwindow'), 'moodle', ['class' => 'ms-1']);
        $content = $commicon . get_string('communicationroomlink', 'course') . $newwindowicon;
        $html = html_writer::tag('a', $content, ['target' => '_blank', 'href' => $link]);

        return !empty($link) ? $html : '';
    }

    /**
     * Returns the communication url for a given instance if it exists.
     *
     * @return string
     */
    public function communication_url(): string {
        global $COURSE;
        $url = '';
        if ($COURSE->id !== SITEID) {
            $comm = \core_communication\api::load_by_instance(
                context: \core\context\course::instance($COURSE->id),
                component: 'core_course',
                instancetype: 'coursecommunication',
                instanceid: $COURSE->id,
            );
            $url = $comm->get_communication_room_url();
        }

        return !empty($url) ? $url : '';
    }

    /**
     * Print teacher profile
     * Prints a media object with the techers photo, name (links to profile) and desctiption.
     *
     * @param stdClass $user
     * @return string
     */
    public function print_teacher_profile($user) {
        global $CFG, $USER;

        $userpicture = new \user_picture($user);
        $userpicture->link = false;
        $userpicture->alttext = true;
        if (empty($userpicture->user->imagealt)) {
            $userpicture->user->imagealt = format_string(fullname($user));
        }
        $userpicture->size = 100;
        $picture = $this->render($userpicture);

        $fullname = '<a href="' .$CFG->wwwroot. '/user/profile.php?id=' .$user->id. '"><h3 class="title" >'.format_string(fullname($user)).'</h3></a>';
        $data = (object) [
            'image' => $picture,
            'content' => $fullname,
        ];
        if ($USER->id != $user->id) {
            $messageicon = '<img class="svg-icon" alt="" role="presentation" src="'
                .$this->output->image_url('messages', 'theme').' ">';
            $message = '<br><small><a href="'.$CFG->wwwroot.
                '/message/index.php?id='.$user->id.'">message'.$messageicon.'</a></small>';
            $data->content .= $message;
        }

        return $this->render_from_template('theme_snap/media_object', $data);
    }

    /**
     * Print recent activites for a course
     *
     * @param stdClass $context
     * @return string
     */
    public function get_mod_recent_activity($context) {
        global $COURSE;
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);
        $recentactivity = [];
        $timestart = time() - (86400 * 7); // Only show last 7 days activity.
        if (optional_param('testing', false, PARAM_BOOL)) {
            $timestart = time() - (86400 * 3000); // 3000 days ago for testing.
        }
        $modinfo = get_fast_modinfo($COURSE);
        $usedmodules = $modinfo->get_used_module_names();
        // Don't show activity for folder mod.
        unset($usedmodules['folder']);
        if (empty($usedmodules)) {
            // No used modules so return null string.
            return '';
        }
        foreach ($usedmodules as $modname => $modfullname) {
            // Each module gets it's own logs and prints them.
            ob_start();
            $hascontent = component_callback('mod_'. $modname, 'print_recent_activity',
                    [$COURSE, $viewfullnames, $timestart], false);
            if ($hascontent) {
                $content = ob_get_contents();
                if (!empty($content)) {
                    $recentactivity[$modname] = $content;
                }
            }
            ob_end_clean();
        }

        $output = '';
        if (!empty($recentactivity)) {
            foreach ($recentactivity as $modname => $moduleactivity) {
                // Get mod icon, empty alt as title already there.
                $img = html_writer::tag('img', '', [
                    'src' => $this->output->image_url('icon', $modname),
                    'alt' => '',
                ]);

                // Create media object for module activity.
                $data = (object) [
                    'image' => $img,
                    'content' => $moduleactivity,
                    'class' => $modname,
                ];
                $output .= $this->render_from_template('theme_snap/media_object', $data);
            }
        }
        return $output;
    }

    /**
     * Renders HTML to display a list of course modules in a course section
     * Also displays "move here" controls in Javascript-disabled mode.
     * Copied from course/rederer.php
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This function calls {@link core_course_renderer::course_section_cm()}
     *
     * @param stdClass $course course object
     * @param int|stdClass|section_info $section relative section number or section object
     * @param int $sectionreturn section number to return to
     * @param int $displayoptions
     * @return void
     */

    public function course_section_cm_list_snap($course, $section, $sectionreturn = null, $displayoptions = []) {
        global $USER;
        $output = '';

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();

        if (is_object($section)) {
            $section = $modinfo->get_section_info($section->section);
        } else {
            $section = $modinfo->get_section_info($section);
        }
        $completioninfo = new \completion_info($course);

        // Check if we are currently in the process of moving a module with JavaScript disabled.
        $ismoving = $format->show_editor() && ismoving($course->id);

        if ($ismoving) {
            $strmovefull = strip_tags(get_string("movefull", "", "'$USER->activitycopyname'"));
        }

        // Get the list of modules visible to user (excluding the module being moved if there is one).
        $moduleshtml = [];
        if (!empty($modinfo->sections[$section->section])) {
            foreach ($modinfo->sections[$section->section] as $modnumber) {
                $mod = $modinfo->cms[$modnumber];

                if ($ismoving && $mod->id == $USER->activitycopy) {
                    // Do not display moving mod.
                    continue;
                }

                if ($modulehtml = $this->course_section_cm_list_item_snap($course,
                    $completioninfo, $mod, $sectionreturn, $displayoptions)) {
                    $moduleshtml[$modnumber] = $modulehtml;
                }
            }
        }

        $sectionoutput = '';
        if (!empty($moduleshtml) || $ismoving) {
            foreach ($moduleshtml as $modnumber => $modulehtml) {
                if ($ismoving) {
                    $movingurl = new \moodle_url('/course/mod.php', ['moveto' => $modnumber, 'sesskey' => sesskey()]);
                    $sectionoutput .= html_writer::tag('li',
                        html_writer::link($movingurl, '', ['title' => $strmovefull, 'class' => 'movehere']),
                        ['class' => 'movehere']);
                }

                $sectionoutput .= $modulehtml;
            }

            if ($ismoving) {
                $movingurl = new \moodle_url('/course/mod.php', ['movetosection' => $section->id, 'sesskey' => sesskey()]);
                $sectionoutput .= html_writer::tag('li',
                    html_writer::link($movingurl, '', ['title' => $strmovefull, 'class' => 'movehere']),
                    ['class' => 'movehere']);
            }
        }

        // Always output the section module list.
        $output .= html_writer::tag('ul', $sectionoutput, ['class' => 'section img-text']);

        return $output;
    }

    /**
     * Checks if course module has any conditions that may make it unavailable for
     * all or some of the students
     *
     * @param cm_info $mod
     * @return bool
     */
    public function is_cm_conditionally_hidden(cm_info $mod) {
        global $CFG;
        $conditionalhidden = false;
        if (!empty($CFG->enableavailability)) {
            $info = new \core_availability\info_module($mod);
            $conditionalhidden = !$info->is_available_for_all();
        }
        return $conditionalhidden;
    }

    /**
     * Get LTI launch container.
     *
     * @param cm_info $mod
     * @return string
     */
    public function theme_snap_lti_get_launch_container(cm_info $mod) {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/mod/lti/lib.php');
        require_once($CFG->dirroot.'/mod/lti/locallib.php');

        // LTI launch container for Snap.
        if (!$lti = $DB->get_record('lti', ['id' => $mod->instance],
            'icon, secureicon, intro, introformat, name, typeid, toolurl, launchcontainer')) {
            return null;
        }

        $info = new \cached_cm_info();

        if ($mod->showdescription) {
            // Convert intro to html. Do not filter cached version, filters run at display time.
            $info->content = format_module_intro('lti', $lti, $mod->id, false);
        }

        if (!empty($lti->typeid)) {
            $toolconfig = lti_get_type_config($lti->typeid);
        } else if ($tool = lti_get_tool_by_url_match($lti->toolurl)) {
            $toolconfig = lti_get_type_config($tool->id);
        } else {
            $toolconfig = [];
        }

        // We want to use the right icon based on whether the
        // current page is being requested over http or https.
        if (lti_request_is_using_ssl() &&
            (!empty($lti->secureicon) || (isset($toolconfig['secureicon']) && !empty($toolconfig['secureicon'])))) {
            if (!empty($lti->secureicon)) {
                $info->iconurl = new moodle_url($lti->secureicon);
            } else {
                $info->iconurl = new moodle_url($toolconfig['secureicon']);
            }
        } else if (!empty($lti->icon)) {
            $info->iconurl = new moodle_url($lti->icon);
        } else if (isset($toolconfig['icon']) && !empty($toolconfig['icon'])) {
            $info->iconurl = new moodle_url($toolconfig['icon']);
        }

        // For some reason Snap wasn't doing this right with some external tools,
        // with this we are creating the same behavior that core does, launching the content in a new window on click.
        $launchcontainer = lti_get_launch_container($lti, $toolconfig);
        $onclicklti = '';
        if (($launchcontainer == LTI_LAUNCH_CONTAINER_WINDOW) && ($mod->modname === 'lti')) {
            if ($mod->onclick) {
                $launchurl = new moodle_url('/mod/lti/launch.php', ['id' => $mod->id]);
                $onclickltiurl = 'window.open("' . $launchurl->out(false) . '", "lti-'.$mod->id.'"); return false;';
                $onclicklti = "onclick='$onclickltiurl'";
            }
        }

        return $onclicklti;
    }

    /**
     * Renders HTML for displaying the sequence of course module editing buttons
     *
     * @param cm_info $mod The module we are displaying actions for.
     * @return string
     *
     * @see course_get_cm_edit_actions()
     *
     */
    public function course_section_menu_actions(cm_info $mod = null) {
        global $CFG, $COURSE;
        // Build up edit actions.
        $actions = '';
        $actionsadvanced = [];
        $coursecontext = context_course::instance($mod->course);
        $modcontext = context_module::instance($mod->id);
        $baseurl = new moodle_url('/course/mod.php', ['sesskey' => sesskey()]);

        $str = get_strings(['editsettings', 'delete', 'move', 'duplicate', 'hide', 'show', 'roles',
            'makeavailable', 'makeunavailable', ], 'moodle');

        // Move, Edit, Delete.
        if (has_capability('moodle/course:manageactivities', $modcontext)) {
            // Update button.
            $editalt = get_string('activityedit', 'theme_snap');
            $actionsadvanced[] = '<li><a href="'.new moodle_url($baseurl,
                    ['update' => $mod->id, 'sr' => $mod->sectionnum]).'" aria-label="'
                .$editalt.' '.$mod->get_formatted_name().'" data-action="update" role="button" '.
                'class="snap-edit-asset dropdown-item" role="button"><i class="icon fa fa-pencil fa-fw "></i>'
                .$str->editsettings.'</a></li>';
            // Edit conditions button.
            if($COURSE->enablecompletion && $CFG->enablecompletion) {
                $editconditionsalt = get_string('editconditions', 'completion');
                $actionsadvanced[] = '<li><a href="' . new moodle_url(
                    '/course/modedit.php',
                    ['update' => $mod->id, 'showonly' => 'activitycompletionheader', 'sr' => $mod->sectionnum]
                ) . '" aria-label="'
                . $editconditionsalt . ' ' . $mod->get_formatted_name() . '" data-action="update" role="button" ' .
                'class="snap-edit-conditions-asset dropdown-item" role="button"><i class="icon fa fa-pencil fa-fw "></i>'
                . $editconditionsalt . '</a></li>';
            }
            // Move button.
            $movealt = s(get_string('move', 'theme_snap', $mod->get_formatted_name()));
            $actionsadvanced[] = '<li><a><label role="button" class="snap-asset-move dropdown-item" aria-label="'
                .$movealt.'" for="snap-move-mod-'.$mod->id.'"><input id="snap-move-mod-'.$mod->id.'" 
                class="js-snap-asset-move sr-only" role="button" type="checkbox">'.
                '<i class="icon fa fa-arrows fa-fw "></i>'.$str->move.'</label></a></li>';
            // Delete button.
            $actionsadvanced[] = '<li><a href="'.new moodle_url($baseurl, ['delete' => $mod->id]).
                '" data-action="delete" role="button" class="js_snap_delete dropdown-item">'.
                '<i class="icon fa fa-trash fa-fw "></i>'.$str->delete.'</a></li>';
        }

        // Hide/Show.
        if (has_capability('moodle/course:activityvisibility', $modcontext)) {
            $courseformat = course_get_format($mod->get_course());
            $sectioninfo = $mod->get_section_info();
            $availabilityclass = $courseformat->get_output_classname('content\\cm\\visibility');
            /** @var core_courseformat\output\local\content\cm\visibility */
            $availability = new $availabilityclass($courseformat, $sectioninfo, $mod);
            $availabilitychoice = $availability->get_choice_list();

            // Get selectable options.
            $selectableoptions = $availabilitychoice->get_selectable_options();

            if ($sectioninfo->visible && count($selectableoptions) === 1) {
                $hideaction = '<li><a href="'.new moodle_url($baseurl, ['hide' => $mod->id]);
                $hideaction .= '" data-action="hide" role="button" class="dropdown-item editing_hide js_snap_hide">'.
                    '<i class="icon fa fa-eye fa-fw "></i>'.$str->hide.'</a></li>';
                $actionsadvanced[] = $hideaction;
                $showaction = '<li><a href="'.new moodle_url($baseurl, ['show' => $mod->id]);
                $showaction .= '" data-action="show" role="button" class="dropdown-item editing_show js_snap_show">'.
                    '<i class="icon fa fa-eye-slash fa-fw "></i>'.$str->show.'</a></li>';
                $actionsadvanced[] = $showaction;
            } else {
                // Multiple options available, display as a submenu.
                $availabilityrender = $this->output->render($availabilitychoice);

                $data = (object) [
                    'toggleclass' => 'availability-dropdown',
                    'toggleariacontrols' => 'availability-menu',
                    'spanicon' => 'fa-eye',
                    'spancontent' => get_string('availability'),
                    'dropdownmenuid' => 'availability-menu',
                    'dropdownmenuclasses' => 'availability-dropdown-menu',
                    'dropdownoptions' => $availabilityrender,
                ];

                $actionsadvanced[] = $this->render_from_template('theme_snap/activity_sub_panel', $data);
            }
        }

        // Duplicate.
        $dupecaps = ['moodle/backup:backuptargetimport', 'moodle/restore:restoretargetimport'];
        if (has_all_capabilities($dupecaps, $coursecontext) &&
            plugin_supports('mod', $mod->modname, FEATURE_BACKUP_MOODLE2) &&
            plugin_supports('mod', $mod->modname, 'duplicate', true)) {
            $actionsadvanced[] = "<li><a href='".new moodle_url($baseurl, ['duplicate' => $mod->id]).
                "' data-action='duplicate' role='button' class='dropdown-item js_snap_duplicate'>".
                "<i class='icon fa fa-copy fa-fw'></i>$str->duplicate</a></li>";
        }

        // Assign roles.
        if (has_capability('moodle/role:assign', $modcontext)) {
            $actionsadvanced[] = "<li><a role='button' class='dropdown-item' href='".
                new moodle_url('/admin/roles/assign.php', ['contextid' => $modcontext->id]).
                "'><i class='icon fa fa-user-circle fa-fw'></i>$str->roles</a></li>";
        }

        // Group modes.
        $canmanagegroups = has_capability('moodle/course:managegroups', context_course::instance($mod->course));
        $courseformat = course_get_format($mod->get_course());
        if ($canmanagegroups && $courseformat->show_groupmode($mod)) {
            $sectioninfo = $mod->get_section_info();
            $groupmodeclass = $courseformat->get_output_classname('content\\cm\\groupmode');
            $groupmode = new $groupmodeclass($courseformat, $sectioninfo, $mod);
            $groupoptions = $groupmode->get_choice_list();
            $grouprender = $this->output->render($groupoptions);

            $data = (object) [
                'toggleclass' => 'groups-dropdown',
                'toggleariacontrols' => 'groups-menu',
                'spancontent' => get_string('groups'),
                'spanicon' => 'fa-user-group',
                'dropdownmenuid' => 'groups-menu',
                'dropdownmenuclasses' => 'groups-dropdown-menu',
                'dropdownoptions' => $grouprender,
            ];

            $actionsadvanced[] = $this->render_from_template('theme_snap/activity_sub_panel', $data);
        }

        // Give local plugins a chance to add icons.
        $localplugins = [];
        foreach (get_plugin_list_with_function('local', 'extend_module_editing_buttons') as $function) {
            $localplugins = array_merge($localplugins, $function($mod));
        }

        foreach (get_plugin_list_with_function('block', 'extend_module_editing_buttons') as $function) {
            // The Sharing Cart block should only be available for user with capability to manage activities.
            if ($function === 'block_sharing_cart_extend_module_editing_buttons') {
                if (has_capability('moodle/course:manageactivities', $modcontext)) {
                    $localplugins = array_merge($localplugins, $function($mod));
                }
            } else {
                $localplugins = array_merge($localplugins, $function($mod));
            }
        }

        // TODO - pld string is far too long....
        $locallinks = '';
        foreach ($localplugins as $localplugin) {
            $url = $localplugin->url;
            $text = $localplugin->text;
            $icon = $localplugin->icon;
            $iconhtml = $this->pix_icon($icon->pix, $text, $icon->component);
            $class = 'dropdown-item ' . $localplugin->attributes['class'];
            $actionsadvanced[] = "<a href='$url' class='$class'>$iconhtml $text</a>";
        }

        $advancedactions = '';
        if (!empty($actionsadvanced)) {
            $moreicons = '<i aria-hidden="true" class="icon fa fa-chevron-down fa-fw"></i>'.
                '<i aria-hidden="true" class="icon fa fa-chevron-up fa-fw"></i>';
            $advancedactions = '<div class="dropdown snap-edit-more-dropdown">';
            $advancedactions .= '<button class="snap-edit-asset-more" ';
            $advancedactions .= 'data-toggle="dropdown" data-boundary="window" data-offset="-10,12"';
            $advancedactions .= 'title=\''.get_string('moreoptionslabel', 'theme_snap').' "'.$mod->get_formatted_name().'"\'';
            $advancedactions .= 'aria-label="' . get_string('moreoptionslabel', 'theme_snap') . '" aria-expanded="false"';
            $advancedactions .= 'aria-controls="snap-asset-menu">'.$moreicons.'</button>';
            $advancedactions .= '<ul id="snap-asset-menu" class="dropdown-menu asset-edit-menu">';
            foreach ($actionsadvanced as $action) {
                $advancedactions .= "$action";
            }
            $advancedactions .= "</ul></div>";
        }
        // Add actions menu.
        $output = '';
        if ($advancedactions) {
            $output .= "<div class='js-only snap-asset-actions' role='region' aria-label='" .
                get_string('courseactionslabel', 'theme_snap') . "'>";
            $output .= $advancedactions;
            $output .= "</div>";
        }
        return $output;
    }

    /**
     * Renders HTML for displaying the group modes for each activity.
     *
     * @param cm_info $mod The module we are displaying groups for.
     * @return string
     *
     */
    public function activity_groups_menu_actions($mod) {

        global $OUTPUT;

        if ($mod->effectivegroupmode == VISIBLEGROUPS) {
            $groupiconurl = $OUTPUT->image_url('i/groupv');
            $groupalt = get_string('groupsvisible', 'group');
            $groupicon = \html_writer::img($groupiconurl, $groupalt);
        } else if ($mod->effectivegroupmode == SEPARATEGROUPS) {
            $groupiconurl = $OUTPUT->image_url('i/groups');
            $groupalt = get_string('groupsseparate', 'group');
            $groupicon = \html_writer::img($groupiconurl, $groupalt);
        } else {
            $groupiconurl = $OUTPUT->image_url('i/groupn');
            $groupalt = get_string('groupsnone', 'group');
            $groupicon = \html_writer::img($groupiconurl, $groupalt);
        }

        $courseformat = course_get_format($mod->get_course());
        $sectioninfo = $mod->get_section_info();
        $groupmodeclass = $courseformat->get_output_classname('content\\cm\\groupmode');
        $groupmode = new $groupmodeclass($courseformat, $sectioninfo, $mod);
        $groupoptions = $groupmode->get_choice_list();
        $resourcedisplay = get_config('theme_snap', 'resourcedisplay');
        $render = $this->output->render($groupoptions);

        $groupsdropdownebutton = \html_writer::tag('button', $groupicon,
            array(
                'class' => 'snap-groups-more',
                'data-toggle' => 'dropdown',
                'data-boundary' => 'window',
                'aria-expanded' => 'false',
                'aria-controls' => 'snap-groups-menu',
                ));
        $groupsdropdownlist = \html_writer::tag('ul', $render,
            array(
                'class' => 'dropdown-menu groups-edit-menu',
                'id' => 'snap-groups-menu',
            ));
        $groupsdropdownelement = \html_writer::tag('div',
            $groupsdropdownebutton.$groupsdropdownlist ,
            array('class' => 'dropdown snap-activity-groups-dropdown'));

        $output = '';
        if ($groupoptions && !($resourcedisplay == 'card' && $this->is_resource($mod))) {
            $output .= \html_writer::tag('div',
                $groupsdropdownelement,
                array(
                    'class' => 'js-only snap-groups-mode-actions',
                    'role' => 'region'
                ));
        }
        return $output;
    }

    private function get_completion_dialog_content($mod) {
        global $COURSE;

        $courseformat = course_get_format($mod->get_course());
        $section = $mod->get_section_info();
        $completionclass = $courseformat->get_output_classname('content\\cm\\completion');
        $completion = new $completionclass($courseformat, $section, $mod);
        $templatedata = $completion->export_for_template($this->output);

        return $templatedata->completiondialog['dialogcontent'];
    }

    private function get_forum_unread_posts($mod) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');

        $forumposts = '';
        if (forum_tp_can_track_forums()) {
            if ($unread = forum_tp_count_forum_unread_posts($mod, $mod->get_course())) {
                if ($unread == 1) {
                    $forumposts = get_string('unreadpostsone', 'forum');
                } else {
                    $forumposts = get_string('unreadpostsnumber', 'forum', $unread);
                }
            }
        }
        return $forumposts;
    }

    private function is_resource(\cm_info $cm): bool {
        return in_array($cm->modname, ['resource', 'scorm']) || plugin_supports('mod', $cm->modname, FEATURE_MOD_ARCHETYPE) === MOD_ARCHETYPE_RESOURCE;
    }

    // Callback to filter students from enrolled users.
    private function is_student($enrolledUsers) {
        $filteredStudents = array_filter($enrolledUsers, function($user) {
            if (isset($user[5])) {
                return true;
            }
        });
        return $filteredStudents;
    }

    /**
     * Override course render for course and category box in home page.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(\coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG, $PAGE, $OUTPUT, $USER;
        if ($PAGE->pagetype !== 'site-index') {
            return \core_course_renderer::coursecat_coursebox($chelper, $course, $additionalclasses);
        }
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new \core_course_list_element($course);
        }
        $content = '';
        $cardcontent = '';
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes = trim('coursebox clearfix '. $additionalclasses);

            $cardcontent .= html_writer::start_tag('div', array('class' => 'info'));
            $cardcontent .= $this->course_name($chelper, $course);
            $cardcontent .= $this->course_enrolment_icons($course);
            $cardcontent .= html_writer::end_tag('div');
            $cardcontent .= html_writer::start_tag('div', array('class' => 'content'));
            $cardcontent .= $this->coursecat_coursebox_content($chelper, $course);
            $cardcontent .= html_writer::end_tag('div');
        } else {
            //These are the course cards for Enrolled Courses and Available courses in the homepage.
            // Course image.
            $url = $OUTPUT->get_generated_image_for_id($course->id);
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                    '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
            }
            // Course visibility.
            $isvisible = $course->visible;
            $hiddeninfobadge = '';
            $imageclasses = 'snap-home-courses-image';
            if (!$isvisible) {
                $hiddeninfobadge = html_writer::tag('span',get_string('hiddenfromstudents'),
                    [
                        'class' => 'badge bg-info text-white hiddenbadge'
                    ]);
                $imageclasses .= ' hiddencourse';
            }
            $classes = trim('col-sm-3 coursebox clearfix '. $additionalclasses);
            // Course category information.
            $coursecategoryname = '';
            $category = \core_course_category::get($course->category, IGNORE_MISSING);
            if (isset($category)) {
                $category = $category->name;
                $coursecategoryname = html_writer::tag('span', '<b>'.get_string('category').": ".
                    '</b>'.$category, ['class' => 'coursecategory']);
            }

            if (isloggedin()) {
                // Enrolled students information.
                $enrolledstudents = $this->is_student(enrol_get_course_users_roles($course->id));
                $studentscount = count($enrolledstudents);
                $studentsstring = strtolower(get_string('students'));
                if ($studentscount == 1 ) {
                    $studentsstring = strtolower(get_string('student','theme_snap'));
                }
                $enrolledstudentsinfo = $studentscount.' '.$studentsstring;

                // Starred courses information.
                $usercontext = \context_user::instance($USER->id);
                $service = \core_favourites\service_factory::get_service_for_user_context($usercontext);
                $isfavourite = $service->favourite_exists('core_course', 'courses', $course->id,
                    \context_course::instance($course->id));
            }

            $data  = array(
                'classes' => $classes,
                'courseid' => $course->id,
                'isloggedin' => isloggedin(),
                'isfavourite' => $isfavourite ?? '',
                'imageclasses' => $imageclasses,
                'courseimage' => $url ? $url : '',
                'hiddeninfobadge' => $hiddeninfobadge,
                'enrolledstudents' => $enrolledstudentsinfo ?? '',
                'data-type' => self::COURSECAT_TYPE_COURSE,
                'coursecategoryname' => $coursecategoryname,
                'imagestyle' => 'background-image: url('.$url.');',
                'coursecontacts' => $this->course_contacts($course),
                'coursename' => $chelper->get_course_formatted_name($course),
                'coursesummary' => $this->course_summary($chelper, $course),
                'coursecustomfields' => $this->course_custom_fields($course),
                'hassummary' => $this->course_summary($chelper, $course) ? true : false,
                'courselink' => new moodle_url('/course/view.php', ['id' => $course->id]),
            );

            $content = $this->output->render_from_template('theme_snap/home_page_coursebox', $data);
            return $content;
        }

        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        $content .= $cardcontent;
        $content .= html_writer::end_tag('div');

        return $content;
    }

}
