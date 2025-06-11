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

namespace report_outline\output;

use core_report\output\coursestructure;
use course_modinfo;

/**
 * Activities list page in a hierarchical format.
 *
 * @package    report_outline
 * @copyright  2024 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hierarchicalactivities extends coursestructure {

    /**
     * Exports the data for a single activity.
     *
     * @param \renderer_base $output
     * @param \cm_info $cm
     * @param bool $indelegated Whether the activity is part of a delegated section or not.
     * @return array
     */
    public function export_activity_data(\renderer_base $output, \cm_info $cm, bool $indelegated = false): array {
        if (!$cm->has_view()) {
            return [];
        }
        if (!$cm->uservisible) {
            return [];
        }

        return [
            'isactivity' => true,
            'isdelegated' => false,
            'indelegated' => $indelegated,
            'visible' => $cm->visible,
            'id' => $cm->id,
        ];
    }


    /**
     * Print activity data.
     *
     * @param \renderer_base $output
     * @param string $mode
     * @param \cm_info $mod
     * @param \stdClass $user
     * @param \stdClass $course
     */
    public function print_activity(
            \renderer_base $output,
            string $mode,
            \cm_info $mod,
            \stdClass $user,
            \stdClass $course,
    ): void {
        global $CFG, $DB;

        $instance = $DB->get_record($mod->modname, ['id' => $mod->instance]);
        $libfile = "$CFG->dirroot/mod/$mod->modname/lib.php";

        if (!file_exists($libfile)) {
            return;
        }

        require_once($libfile);

        switch ($mode) {
            case "outline":
                $useroutline = $mod->modname . "_user_outline";
                if (function_exists($useroutline)) {
                    $toprint = $useroutline($course, $user, $mod, $instance);
                } else {
                    $toprint = report_outline_user_outline($user->id, $mod->id, $mod->modname, $mod->instance);
                }
                if (!$toprint) {
                    $toprint = (object) ['info' => '-'];
                }
                report_outline_print_row($mod, $instance, $toprint);
                break;
            case "complete":
                $usercomplete = $mod->modname . "_user_complete";
                $image = $output->pix_icon('monologo', $mod->modfullname, 'mod_' . $mod->modname, ['class' => 'icon']);
                echo "<h4 class=\"h6\">$image $mod->modfullname: " .
                        "<a href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">" .
                        format_string($instance->name, true) . "</a></h4>";

                ob_start();

                echo "<ul>";
                if (function_exists($usercomplete)) {
                    $usercomplete($course, $user, $mod, $instance);
                } else {
                    echo report_outline_user_complete($user->id, $mod->id, $mod->modname, $mod->instance);
                }
                echo "</ul>";

                $toprint = ob_get_contents();
                ob_end_clean();

                if (str_replace(' ', '', $toprint) != '<ul></ul>') {
                    echo $toprint;
                }
                break;
        }
    }
}
