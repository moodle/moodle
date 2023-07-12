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
 * Master block ckass for use_stats compiler
 *
 * @package    block_use_stats
 * @category   blocks
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright  Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

class block_use_stats_renderer extends plugin_renderer_base {

    public function per_course(&$aggregate, &$fulltotal) {

        $config = get_config('block_use_stats');

        $fulltotal = 0;
        $eventsunused = 0;

        $usestatsorder = optional_param('usestatsorder', 'name', PARAM_TEXT);

        $tbl = block_use_stats::prepare_coursetable($aggregate, $fulltotal, $eventsunused, $usestatsorder);
        list($displaycourses, $courseshort, $coursefull, $courseelapsed) = $tbl;

        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        $currentnameurl = new moodle_url($url);
        $currentnameurl->params(array('usestatsorder' => 'name'));

        $currenttimeurl = new moodle_url($url);
        $currenttimeurl->params(array('usestatsorder' => 'time'));

        $str = '<div class="usestats-coursetable">';
        $label = get_string('byname', 'block_use_stats');
        $str .= '<div class="pull-left smalltext"><a href="'.$currentnameurl.'">'.$label.'</a></div>';
        $label = get_string('bytimedesc', 'block_use_stats');
        $str .= '<div class="pull-right smalltext"><a href="'.$currenttimeurl.'">'.$label.'</a></div>';
        $str .= '</div>';

        $str .= '<table width="100%">';
        foreach (array_keys($displaycourses) as $courseid) {
            if (!empty($config->filterdisplayunder)) {
                if ($courseelapsed[$courseid] < $config->filterdisplayunder) {
                    continue;
                }
            }
            $str .= '<tr>';
            $title = htmlspecialchars(format_string($coursefull[$courseid]));
            $str .= '<td class="teacherstatsbycourse" align="left" title="'.$title.'">';
            $str .= $courseshort[$courseid];
            $str .= '</td>';
            $str .= '<td class="teacherstatsbycourse" align="right">';
            $str .= block_use_stats_format_time($courseelapsed[$courseid]);
            $str .= '</td>';
            $str .= '</tr>';
        }

        if (!empty($config->filterdisplayunder)) {
            $title = htmlspecialchars(get_string('isfiltered', 'block_use_stats', $config->filterdisplayunder));
            $pix = '<img src="'.$this->output->pix_url('i/warning').'">';
            $str .= '<tr><td class="teacherstatsbycourse" title="'.$title.'">'.$pix.'</td>';
            $str .= '<td align="right" class="teacherstatsbycourse">';
            if (@$config->displayactivitytimeonly != DISPLAY_FULL_COURSE) {
                $str .= '('.get_string('activities', 'block_use_stats').')';
            }
            $str .= '</td></tr>';
        }

        $str .= '</table>';

        return $str;
    }

    /**
     * @global type $USER
     * @global type $DB
     * @global type $COURSE
     * @param type $context
     * @param type $id
     * @param type $fromwhen
     * @param type $userid
     * @return string
     */
    public function change_params_form($context, $id, $from, $to, $userid) {
        global $USER, $DB, $COURSE, $SESSION;

        $config = get_config('block_use_stats');

        $str = ' <form style="display:inline" name="ts_changeParms" method="post" action="#">';

        $str .= '<input type="hidden" name="id" value="'.$id.'" />';

        if (has_capability('block/use_stats:seesitedetails', $context, $USER->id) && ($COURSE->id == SITEID)) {
            $users = $DB->get_records('user', array('deleted' => '0'), 'lastname', 'id,'.get_all_user_name_fields(true, ''));
        } else if (has_capability('block/use_stats:seecoursedetails', $context, $USER->id)) {
            $coursecontext = context_course::instance($COURSE->id);
            $users = get_enrolled_users($coursecontext);
        } else if (has_capability('block/use_stats:seegroupdetails', $context, $USER->id)) {
            $mygroupings = groups_get_user_groups($COURSE->id);

            $mygroups = array();
            foreach ($mygroupings as $grouping) {
                $mygroups = $mygroups + $grouping;
            }

            $users = array();
            // Get all users in my groups.
            foreach ($mygroups as $mygroupid) {
                $members = groups_get_members($mygroupid, 'u.id,'.get_all_user_name_fields(true, 'u'));
                if ($members) {
                    $users = $users + $members;
                }
            }
        }
        if (!empty($users)) {
            $usermenu = array();
            foreach ($users as $user) {
                $usermenu[$user->id] = fullname($user, has_capability('moodle/site:viewfullnames', context_system::instance()));
            }
            $attrs = array('onchange' => 'document.ts_changeParms.submit();');
            $str .= html_writer::select($usermenu, 'uid', $userid, 'choose', $attrs);
        }

        if (@$config->backtrackmode == 'sliding') {
            if (@$config->backtracksource == 'studentchoice') {
                $str .= ' ';
                $str .= get_string('from', 'block_use_stats');
                foreach (array(5, 15, 30, 60, 90, 180, 365) as $interval) {
                    $timemenu[$interval] = $interval.' '.get_string('days');
                }
                $attrs = array('onchange' => 'document.ts_changeParms.submit();');
                $str .= html_writer::select($timemenu, 'ts_from', floor(($to - $from) / DAYSECS), 'choose', $attrs);
            }
        } else {
            if (@$config->backtracksource == 'studentchoice') {

                $str .= '<br/>'.get_string('fromrange', 'block_use_stats');
                $str .= $this->calendar('ts_from'.$context->id, $from);

                $str .= '(0h00)&ensp;'.get_string('to', 'block_use_stats');

                $str .= $this->calendar('ts_to'.$context->id, $to).'(23h59)';

                $checked = '';
                if ($SESSION->usestatstoenable = optional_param('usestatstoenable', false, PARAM_BOOL)) {
                    $checked = 'checked="checked"';
                }

                $jshandler = 'toggleusestatsto('.$context->id.')';
                $str .= '&nbsp;<input type="checkbox" name="usestatstoenable" value="1" '.$checked.' onchange="'.$jshandler.'" />';
                $str .= '&ensp;<input type="button"
                                  id="go-usestats-'.$context->id.'"
                                  name="go-usestats-'.$context->id.'"
                                  value="'.get_string('go', 'block_use_stats').'"
                                  onclick="document.ts_changeParms.submit()"
                                  />';

                $state = ($SESSION->usestatstoenable) ? 'false' : 'true';
                $date = date('Y-m-d', time()); // Force at "tomorrow" when disabled.
                $str .= '
<script type="text/javascript">
initusestatsto('.$context->id.', '.$state.',   \''.$date.'\');
</script>
';
            }
        }
        $str .= '</form><br/>';

        return $str;
    }

    protected function calendar($htmlkey, $value) {

        $valuestr = date('Y-m-d', $value);
        $str = '';

        $str .= '<input type="text"
                          size="10"
                          id="date-'.$htmlkey.'"
                          name="'.$htmlkey.'"
                          value="'.$valuestr.'"
                          />';
        $str .= '<script type="text/javascript">'."\n";
        $str .= 'var '.$htmlkey.'Cal = new dhtmlXCalendarObject(["date-'.$htmlkey.'"]);'."\n";
        $str .= $htmlkey.'Cal.loadUserLanguage(\''.current_language().'_utf8\');'."\n";
        $str .= '</script>'."\n";

        return $str;
    }

    /**
     * @param type $userid
     * @param type $from
     * @param type $to
     * @param type $context
     * @return HTML code for a button to pdf report task.
     */
    public function button_pdf($userid, $from, $to, $context) {
        global $CFG;

        if (block_use_stats_supports_feature('format/pdf')) {
            include_once($CFG->dirroot.'/blocks/use_stats/pro/renderer.php');
            $prorenderer = new block_use_stats_pro_renderer();
            return $prorenderer->button_pdf($userid, $from, $to, $context);
        }
        return '';
    }
}