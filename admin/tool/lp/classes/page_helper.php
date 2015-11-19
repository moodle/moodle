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
use moodle_url;

/**
 * Page helper.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_helper {

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
     * @param  \tool_lp\template $template The template, if any.
     * @param  string $subpage The title of the subpage, if any.
     * @return array With the following:
     *               - Page title
     *               - Page sub title
     *               - Return URL (main templates page)
     */
    public static function setup_for_template($pagecontextid, moodle_url $url, $template = null, $subtitle = '') {
        global $PAGE, $SITE;

        $pagecontext = context::instance_by_id($pagecontextid);
        $context = $pagecontext;
        if (!empty($template)) {
            $context = $template->get_context();
        }

        $templatesurl = new moodle_url('/admin/tool/lp/learningplans.php', array('pagecontextid' => $pagecontextid));

        $PAGE->navigation->override_active_url($templatesurl);
        $PAGE->set_context($pagecontext);

        if (!empty($template)) {
            $title = format_string($template->get_shortname(), true, array('context' => $context));
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
            $PAGE->navbar->add($title);
            $PAGE->navbar->add($subtitle, $url);

        } else if (!empty($subtitle)) {
            // We're in a sub page without a specific template.
            $PAGE->navbar->add($subtitle, $url);
        }

        return array($title, $subtitle, $templatesurl);
    }
}
