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
 * Page helper.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_lp;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context;
use moodle_exception;
use moodle_url;
use core_user;
use context_user;
use context_course;
use stdClass;

/**
 * Page helper.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_helper {

    /**
     * Set-up a course page.
     *
     * Example:
     * list($title, $subtitle) = page_helper::setup_for_course($pagecontextid, $url, $course, $pagetitle);
     * echo $OUTPUT->heading($title);
     * echo $OUTPUT->heading($subtitle, 3);
     *
     * @param  moodle_url $url The current page.
     * @param  stdClass $course The course.
     * @param  string $subtitle The title of the subpage, if any.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Return URL (course competencies page)
     */
    public static function setup_for_course(moodle_url $url, $course, $subtitle = '') {
        global $PAGE;

        $context = context_course::instance($course->id);

        $PAGE->set_course($course);

        if (!empty($subtitle)) {
            $title = $subtitle;
        } else {
            $title = get_string('coursecompetencies', 'tool_lp');
        }

        $returnurl = new moodle_url('/admin/tool/lp/coursecompetencies.php', array('courseid' => $course->id));

        $heading = $context->get_context_name();
        $PAGE->set_pagelayout('incourse');
        $PAGE->set_url($url);
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);

        if (!empty($subtitle)) {
            $PAGE->navbar->add(get_string('coursecompetencies', 'tool_lp'), $returnurl);
            // We're in a sub page without a specific template.
            $PAGE->navbar->add($subtitle, $url);
        }

        return array($title, $subtitle, $returnurl);
    }

    /**
     * Set-up a template page.
     *
     * Example:
     * list($title, $subtitle) = page_helper::setup_for_template($pagecontextid, $url, $template, $pagetitle);
     * echo $OUTPUT->heading($title);
     * echo $OUTPUT->heading($subtitle, 3);
     *
     * @param  int $pagecontextid The page context ID.
     * @param  moodle_url $url The current page.
     * @param  \core_competency\template $template The template, if any.
     * @param  string $subtitle The title of the subpage, if any.
     * @param  string $returntype The desired return page.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Return URL
     */
    public static function setup_for_template($pagecontextid, moodle_url $url, $template = null, $subtitle = '',
                                              $returntype = null) {
        global $PAGE, $SITE;

        $pagecontext = context::instance_by_id($pagecontextid);
        $context = $pagecontext;
        if (!empty($template)) {
            $context = $template->get_context();
        }

        $templatesurl = new moodle_url('/admin/tool/lp/learningplans.php', array('pagecontextid' => $pagecontextid));
        $templateurl = null;
        if ($template) {
            $templateurl = new moodle_url('/admin/tool/lp/templatecompetencies.php', [
                'templateid' => $template->get('id'),
                'pagecontextid' => $pagecontextid
            ]);
        }

        $returnurl = $templatesurl;
        if ($returntype != 'templates' && $templateurl) {
            $returnurl = $templateurl;
        }

        $PAGE->navigation->override_active_url($templatesurl);
        $PAGE->set_context($pagecontext);

        if (!empty($template)) {
            $title = format_string($template->get('shortname'), true, array('context' => $context));
        } else {
            $title = get_string('templates', 'tool_lp');
        }

        if ($pagecontext->contextlevel == CONTEXT_SYSTEM) {
            $heading = $SITE->fullname;
        } else if ($pagecontext->contextlevel == CONTEXT_COURSECAT) {
            $heading = $pagecontext->get_context_name();
        } else {
            throw new coding_exception('Unexpected context!');
        }

        $PAGE->set_pagelayout('admin');
        $PAGE->set_url($url);
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);

        if (!empty($template)) {
            $PAGE->navbar->add($title, $templateurl);
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }

        } else if (!empty($subtitle)) {
            // We're in a sub page without a specific template.
            $PAGE->navbar->add($subtitle, $url);
        }

        return array($title, $subtitle, $returnurl);
    }

    /**
     * Set-up a plan page.
     *
     * Example:
     * list($title, $subtitle) = page_helper::setup_for_plan($url, $template, $pagetitle);
     * echo $OUTPUT->heading($title);
     * echo $OUTPUT->heading($subtitle, 3);
     *
     * @param  int $userid The user ID.
     * @param  moodle_url $url The current page.
     * @param  \core_competency\plan $plan The plan, if any.
     * @param  string $subtitle The title of the subpage, if any.
     * @param  string $returntype The desired return page.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Return URL (main plan page)
     */
    public static function setup_for_plan($userid, moodle_url $url, $plan = null, $subtitle = '', $returntype = null) {
        global $PAGE, $USER;

        // Check that the user is a valid user.
        $user = core_user::get_user($userid);
        if (!$user || !core_user::is_real_user($userid)) {
            throw new \moodle_exception('invaliduser', 'error');
        }

        $context = context_user::instance($user->id);

        $plansurl = new moodle_url('/admin/tool/lp/plans.php', array('userid' => $userid));
        $planurl = null;
        if ($plan) {
            $planurl = new moodle_url('/admin/tool/lp/plan.php', array('id' => $plan->get('id')));
        }

        $returnurl = $plansurl;
        if ($returntype != 'plans' && $planurl) {
            $returnurl = $planurl;
        }

        $PAGE->navigation->override_active_url($plansurl);
        $PAGE->set_context($context);

        // If not his own plan, we want to extend the navigation for the user.
        $iscurrentuser = ($USER->id == $user->id);
        if (!$iscurrentuser) {
            $PAGE->navigation->extend_for_user($user);
            $PAGE->navigation->set_userid_for_parent_checks($user->id);
        }

        if (!empty($plan)) {
            $title = format_string($plan->get('name'), true, array('context' => $context));
        } else {
            $title = get_string('learningplans', 'tool_lp');
        }

        $PAGE->set_pagelayout('standard');
        $PAGE->set_url($url);
        $PAGE->set_title($title);
        $PAGE->set_heading($title);

        if (!empty($plan)) {
            $PAGE->navbar->add($title, $planurl);
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            // We're in a sub page without a specific plan.
            $PAGE->navbar->add($subtitle, $url);
        }

        return array($title, $subtitle, $returnurl);
    }

    /**
     * Set-up a user evidence page.
     *
     * Example:
     * list($title, $subtitle) = page_helper::setup_for_user_evidence($url, $template, $pagetitle);
     * echo $OUTPUT->heading($title);
     * echo $OUTPUT->heading($subtitle, 3);
     *
     * @param  int $userid The user ID.
     * @param  moodle_url $url The current page.
     * @param  \core_competency\user_evidence $evidence The user evidence, if any.
     * @param  string $subtitle The title of the subpage, if any.
     * @param  string $returntype The desired return page.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Return URL (main plan page)
     */
    public static function setup_for_user_evidence($userid, moodle_url $url, $evidence = null, $subtitle = '', $returntype = null) {
        global $PAGE, $USER;

        // Check that the user is a valid user.
        $user = core_user::get_user($userid);
        if (!$user || !core_user::is_real_user($userid)) {
            throw new \moodle_exception('invaliduser', 'error');
        }

        $context = context_user::instance($user->id);

        $evidencelisturl = new moodle_url('/admin/tool/lp/user_evidence_list.php', array('userid' => $userid));
        $evidenceurl = null;
        if ($evidence) {
            $evidenceurl = new moodle_url('/admin/tool/lp/user_evidence.php', array('id' => $evidence->get('id')));
        }

        $returnurl = $evidencelisturl;
        if ($returntype != 'list' && $evidenceurl) {
            $returnurl = $evidenceurl;
        }

        $PAGE->navigation->override_active_url($evidencelisturl);
        $PAGE->set_context($context);

        // If not his own evidence, we want to extend the navigation for the user.
        $iscurrentuser = ($USER->id == $user->id);
        if (!$iscurrentuser) {
            $PAGE->navigation->extend_for_user($user);
            $PAGE->navigation->set_userid_for_parent_checks($user->id);
        }

        if (!empty($evidence)) {
            $title = format_string($evidence->get('name'), true, array('context' => $context));
        } else {
            $title = get_string('userevidence', 'tool_lp');
        }

        $PAGE->set_pagelayout('standard');
        $PAGE->set_url($url);
        $PAGE->set_title($title);
        $PAGE->set_heading($title);

        if (!empty($evidence)) {
            $PAGE->navbar->add($title, $evidenceurl);
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            // We're in a sub page without a specific evidence.
            $PAGE->navbar->add($subtitle, $url);
        }

        return array($title, $subtitle, $returnurl);
    }

    /**
     * Set-up a framework page.
     *
     * Example:
     * list($pagetitle, $pagesubtitle, $url, $frameworksurl) = page_helper::setup_for_framework($id, $pagecontextid);
     * echo $OUTPUT->heading($pagetitle);
     * echo $OUTPUT->heading($pagesubtitle, 3);
     *
     * @param  int $id The framework ID.
     * @param  int $pagecontextid The page context ID.
     * @param  \core_competency\competency_framework $framework The framework.
     * @param  string $returntype The desired return page.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Page URL
     *               - Page framework URL
     */
    public static function setup_for_framework($id, $pagecontextid, $framework = null, $returntype = null) {
        global $PAGE;

        // We keep the original context in the URLs, so that we remain in the same context.
        $url = new moodle_url("/admin/tool/lp/editcompetencyframework.php", array('id' => $id, 'pagecontextid' => $pagecontextid));
        if ($returntype) {
            $url->param('return', $returntype);
        }
        $frameworksurl = new moodle_url('/admin/tool/lp/competencyframeworks.php', array('pagecontextid' => $pagecontextid));

        $PAGE->navigation->override_active_url($frameworksurl);
        $title = get_string('competencies', 'core_competency');
        if (empty($id)) {
            $pagetitle = get_string('competencyframeworks', 'tool_lp');
            $pagesubtitle = get_string('addnewcompetencyframework', 'tool_lp');

            $url->remove_params(array('id'));
            $PAGE->navbar->add($pagesubtitle, $url);
        } else {
            $pagetitle = $framework->get('shortname');
            $pagesubtitle = get_string('editcompetencyframework', 'tool_lp');
            if ($returntype == 'competencies') {
                $frameworksurl = new moodle_url('/admin/tool/lp/competencies.php', array(
                    'pagecontextid' => $pagecontextid,
                    'competencyframeworkid' => $id
                ));
            } else {
                $frameworksurl->param('competencyframeworkid', $id);
            }

            $PAGE->navbar->add($pagetitle, $frameworksurl);
            $PAGE->navbar->add($pagesubtitle, $url);
        }

        $PAGE->set_context(context::instance_by_id($pagecontextid));
        $PAGE->set_pagelayout('admin');
        $PAGE->set_url($url);
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        return array($pagetitle, $pagesubtitle, $url, $frameworksurl);
    }

    /**
     * Set-up a competency page.
     *
     * Example:
     * list($title, $subtitle) = page_helper::setup_for_competency($pagecontextid, $url, $competency, $pagetitle);
     * echo $OUTPUT->heading($title);
     * echo $OUTPUT->heading($subtitle, 3);
     *
     * @param  int $pagecontextid The page context ID.
     * @param  moodle_url $url The current page.
     * @param  \core_competency\competency_framework $framework The competency framework.
     * @param  \core_competency\competency $competency The competency, if any.
     * @param  \core_competency\competency $parent The parent competency, if any.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Return URL (main competencies page)
     * @throws coding_exception
     */
    public static function setup_for_competency($pagecontextid, moodle_url $url, $framework, $competency = null, $parent = null) {
        global $PAGE, $SITE;

        // Set page context.
        $pagecontext = context::instance_by_id($pagecontextid);
        $PAGE->set_context($pagecontext);

        // Set page heading.
        if ($pagecontext->contextlevel == CONTEXT_SYSTEM) {
            $heading = $SITE->fullname;
        } else if ($pagecontext->contextlevel == CONTEXT_COURSECAT) {
            $heading = $pagecontext->get_context_name();
        } else {
            throw new coding_exception('Unexpected context!');
        }
        $PAGE->set_heading($heading);

        // Set override active url.
        $frameworksurl = new moodle_url('/admin/tool/lp/competencyframeworks.php', ['pagecontextid' => $pagecontextid]);
        $PAGE->navigation->override_active_url($frameworksurl);

        // Set return url.
        $returnurloptions = [
            'competencyframeworkid' => $framework->get('id'),
            'pagecontextid' => $pagecontextid
        ];
        $returnurl = new moodle_url('/admin/tool/lp/competencies.php', $returnurloptions);
        $PAGE->navbar->add($framework->get('shortname'), $returnurl);

        // Set page layout.
        $PAGE->set_pagelayout('admin');

        if (empty($competency)) {
            // Add mode.
            $title = format_string($framework->get('shortname'), true, ['context' => $pagecontext]);

            // Set the sub-title for add mode.
            $level = $parent ? $parent->get_level() + 1 : 1;
            $subtitle = get_string('taxonomy_add_' . $framework->get_taxonomy($level), 'tool_lp');

        } else {
            // Edit mode.
            $title = format_string($competency->get('shortname'), true, ['context' => $competency->get_context()]);

            // Add competency name to breadcrumbs, if available.
            $PAGE->navbar->add($title);

            // Set the sub-title for edit mode.
            $subtitle = get_string('taxonomy_edit_' . $framework->get_taxonomy($competency->get_level()), 'tool_lp');
        }

        // Set page title.
        $PAGE->set_title($title);

        // Set page url.
        $PAGE->set_url($url);

        // Add editing mode link to breadcrumbs, if available.
        if (!empty($subtitle)) {
            $PAGE->navbar->add($subtitle, $url);
        }

        return [$title, $subtitle, $returnurl];
    }
}
