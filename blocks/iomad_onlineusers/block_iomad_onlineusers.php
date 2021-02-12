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
 * @package   block_iomad_onlineusers
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . "/local/iomad/lib/user.php");

/**
 * This block needs to be reworked.
 * The new roles system does away with the concepts of rigid student and
 * teacher roles.
 */
class block_iomad_onlineusers extends block_base {
    public function init() {
        $this->title = get_string('title', 'block_iomad_onlineusers');
    }

    public function has_config() {
        return true;
    }

    public function hide_header() {
        return false;
    }

    public function get_title() {
        return get_string('pluginname', 'block_iomad_onlineusers');
    }

    public function get_content() {
        global $USER, $CFG, $DB, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!isloggedin()) {
            $this->content->text = get_string('pleaselogin', 'block_iomad_onlineusers');
            return $this->content;
        }

        $timetoshowusers = 300; // Seconds default.
        if (isset($CFG->block_iomad_onlineusers_timetosee)) {
            $timetoshowusers = $CFG->block_iomad_onlineusers_timetosee * 60;
        }
        $timefrom = 100 * floor((time() - $timetoshowusers) / 100); // Round to nearest 100 seconds for better query cache.
        $now = time();

        // Calculate if we are in separate groups.
        $isseparategroups = ($this->page->course->groupmode == SEPARATEGROUPS
                             && $this->page->course->groupmodeforce
                             && !iomad::has_capability('moodle/site:accessallgroups', $this->page->context));

        // Get the user current group.
        $currentgroup = $isseparategroups ? groups_get_course_group($this->page->course) : null;

        $groupmembers = "";
        $groupselect  = "";
        $rafrom       = "";
        $rawhere      = "";
        $params = array();
        $params['now'] = $now;
        $params['timefrom'] = $timefrom;

        // Add this to the SQL to show only group users.
        if ($currentgroup !== null) {
            $groupmembers = ", {groups_members} gm";
            $groupselect = "AND u.id = gm.userid AND gm.groupid = :currentgroup";
            $params['currentgroup'] = $currentgroup;
        }

        $companyselect = "";
        $companyusersjoin = "";
        if (company_user::is_company_user()) {
            company_user::load_company();
            $companyusersjoin = ", {user_info_data} muid, {user_info_field} muif";
            $companyselect = " AND muif.id = muid.fieldid
                               AND u.id = muid.userid
                               AND muif.shortname = 'company'
                               AND muid.data = :companyshortname ";
            $params['companyshortname'] = $USER->company->shortname;
        }

        $userfields = user_picture::fields('u', array('username'));

        if ($this->page->course->id == SITEID) {  // Site-level.
            $sql = "SELECT $userfields, MAX(u.lastaccess) AS lastaccess
                      FROM {user} u $groupmembers $companyusersjoin
                     WHERE u.lastaccess > $timefrom
                           $groupselect
                           $companyselect
                  GROUP BY $userfields
                  ORDER BY lastaccess DESC ";

            $csql = "SELECT COUNT(u.id), u.id
                       FROM {user} u $groupmembers $companyusersjoin
                      WHERE u.lastaccess > $timefrom
                            $groupselect
                            $companyselect
                   GROUP BY u.id";

        } else {
            // Course level - show only enrolled users for now.
            // TODO: add a new capability for viewing of all users (guests+enrolled+viewing).

            list($esqljoin, $eparams) = get_enrolled_sql($this->page->context);
            $params = array_merge($params, $eparams);

            $sql = "SELECT $userfields, MAX(ul.timeaccess) AS lastaccess
                      FROM {user_lastaccess} ul $groupmembers, {user} u
                      JOIN ($esqljoin) euj ON euj.id = u.id
                     WHERE ul.timeaccess > :timefrom
                           AND u.id = ul.userid
                           AND ul.courseid = :courseid
                           AND ul.timeaccess <= :now
                           AND u.deleted = 0
                           $groupselect
                  GROUP BY $userfields
                  ORDER BY lastaccess DESC";

            $csql = "SELECT COUNT(u.id)
                      FROM {user_lastaccess} ul $groupmembers, {user} u
                      JOIN ($esqljoin) euj ON euj.id = u.id
                     WHERE ul.timeaccess > :timefrom
                           AND u.id = ul.userid
                           AND ul.courseid = :courseid
                           AND ul.timeaccess <= :now
                           AND u.deleted = 0
                           $groupselect";

            $params['courseid'] = $this->page->course->id;
        }

        // Calculate minutes.
        $minutes  = floor($timetoshowusers / 60);

        // Verify if we can see the list of users, if not just print number of users.
        if (!iomad::has_capability('block/online_users:viewlist', $this->page->context)) {
            if (!$usercount = $DB->count_records_sql($csql, $params)) {
                $usercount = get_string("none");
            }
            $this->content->text = "<div class=\"info\">".get_string("periodnminutes", "block_iomad_onlineusers", $minutes).
                                   ": $usercount</div>";
            return $this->content;
        }

        if ($users = $DB->get_records_sql($sql, $params, 0, 50)) {   // We'll just take the most recent 50 maximum.
            foreach ($users as $user) {
                $users[$user->id]->fullname = fullname($user);
            }
        } else {
            $users = array();
        }

        if (count($users) < 50) {
            $usercount = "";
        } else {
            $usercount = $DB->count_records_sql($csql, $params);
            $usercount = ": $usercount";
        }

        $this->content->text = "<div class=\"info\">(".
                                get_string("periodnminutes", "block_iomad_onlineusers", $minutes)."$usercount)</div>";

        // Now, we have in users, the list of users to show.
        // Because they are online.
        if (!empty($users)) {
            // Accessibility: Don't want 'Alt' text for the user picture; DO want it for the envelope/message link
            // (existing lang string).
            // Accessibility: Converted <div> to <ul>, inherit existing classes & styles.
            $this->content->text .= "<ul class='list'>\n";
            if (isloggedin() && iomad::has_capability('moodle/site:sendmessage', $this->page->context)
                           && !empty($CFG->messaging) && !isguestuser()) {
                $canshowicon = true;
            } else {
                $canshowicon = false;
            }
            foreach ($users as $user) {
                $this->content->text .= '<li class="listentry">';
                $timeago = format_time(time() - $user->lastaccess); // Bruno to calculate correctly on frontpage.

                if (isguestuser($user)) {
                    $this->content->text .= '<div class="user">'.$OUTPUT->user_picture($user, array('size' => 16));
                    $this->content->text .= get_string('guestuser').'</div>';

                } else {
                    $this->content->text .= '<div class="user">'.$OUTPUT->user_picture($user, array('size' => 16));
                    $this->content->text .= '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$user->id.'&amp;course='.
                                             $this->page->course->id.'" title="'.$timeago.'">'.$user->fullname.'</a></div>';
                }
                if ($canshowicon and ($USER->id != $user->id) and !isguestuser($user)) {
                    // Only when logged in and messaging active etc.
                    $anchortagcontents = '<img class="iconsmall" src="'.$OUTPUT->image_url('t/message') .
                                         '" alt="'. get_string('messageselectadd') .'" />';
                    $anchortag = '<a href="'.$CFG->wwwroot.'/message/index.php?id='.$user->id.'" title="'.
                                  get_string('messageselectadd').'">'.$anchortagcontents .'</a>';

                    $this->content->text .= '<div class="message">'.$anchortag.'</div>';
                }
                $this->content->text .= "</li>\n";
            }
            $this->content->text .= '</ul><div class="clearer"><!-- --></div>';
        } else {
            $this->content->text .= "<div class=\"info\">".get_string("none")."</div>";
        }

        return $this->content;
    }
}

