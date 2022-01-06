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
 * Classes to enforce the various access rules that can apply to a activity.
 *
 * @package    block_activity_results
 * @copyright  2009 Tim Hunt
 * @copyright  2015 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/grade/constants.php');
require_once($CFG->dirroot . '/course/lib.php');

define('B_ACTIVITYRESULTS_NAME_FORMAT_FULL', 1);
define('B_ACTIVITYRESULTS_NAME_FORMAT_ID',   2);
define('B_ACTIVITYRESULTS_NAME_FORMAT_ANON', 3);
define('B_ACTIVITYRESULTS_GRADE_FORMAT_PCT', 1);
define('B_ACTIVITYRESULTS_GRADE_FORMAT_FRA', 2);
define('B_ACTIVITYRESULTS_GRADE_FORMAT_ABS', 3);
define('B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE', 4);

/**
 * Block activity_results class definition.
 *
 * This block can be added to a course page or a activity page to display of list of
 * the best/worst students/groups in a particular activity.
 *
 * @package    block_activity_results
 * @copyright  2009 Tim Hunt
 * @copyright  2015 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_activity_results extends block_base {

    /**
     * Core function used to initialize the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_activity_results');
    }

    /**
     * Allow the block to have a configuration page
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Core function, specifies where the block can be used.
     * @return array
     */
    public function applicable_formats() {
        return array('course-view' => true, 'mod' => true);
    }

    /**
     * If this block belongs to a activity context, then return that activity's id.
     * Otherwise, return 0.
     * @return stdclass the activity record.
     */
    public function get_owning_activity() {
        global $DB;

        // Set some defaults.
        $result = new stdClass();
        $result->id = 0;

        if (empty($this->instance->parentcontextid)) {
            return $result;
        }
        $parentcontext = context::instance_by_id($this->instance->parentcontextid);
        if ($parentcontext->contextlevel != CONTEXT_MODULE) {
            return $result;
        }
        $cm = get_coursemodule_from_id($this->page->cm->modname, $parentcontext->instanceid);
        if (!$cm) {
            return $result;
        }
        // Get the grade_items id.
        $rec = $DB->get_record('grade_items', array('iteminstance' => $cm->instance, 'itemmodule' => $this->page->cm->modname));
        if (!$rec) {
            return $result;
        }
        // See if it is a gradable activity.
        if (($rec->gradetype != GRADE_TYPE_VALUE) && ($rec->gradetype != GRADE_TYPE_SCALE)) {
            return $result;
        }
        return $rec;
    }

    /**
     * Used to save the form config data
     * @param stdclass $data
     * @param bool $nolongerused
     */
    public function instance_config_save($data, $nolongerused = false) {
        global $DB;
        if (empty($data->activitygradeitemid)) {
            // Figure out info about parent module.
            $info = $this->get_owning_activity();
            $data->activitygradeitemid = $info->id;
            if ($info->id < 1) {
                // No activity was selected.
                $info->itemmodule = '';
                $info->iteminstance = '';
            } else {
                $data->activityparent = $info->itemmodule;
                $data->activityparentid = $info->iteminstance;
            }
        } else {
            // Lookup info about the parent module (we have the id from mdl_grade_items.
            $info = $DB->get_record('grade_items', array('id' => $data->activitygradeitemid));
            $data->activityparent = $info->itemmodule;
            $data->activityparentid = $info->iteminstance;
        }
        parent::instance_config_save($data);
    }

    /**
     * Used to generate the content for the block.
     * @return string
     */
    public function get_content() {
        global $USER, $CFG, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        // We are configured so use the configuration.
        if (!empty($this->config->activitygradeitemid)) {
            // We are configured.
            $activitygradeitemid = $this->config->activitygradeitemid;

            // Lookup the module in the grade_items table.
            $activity = $DB->get_record('grade_items', array('id' => $activitygradeitemid));
            if (empty($activity)) {
                // Activity does not exist.
                $this->content->text = get_string('error_emptyactivityrecord', 'block_activity_results');
                return $this->content;
            }
            $courseid = $activity->courseid;
            $inactivity = false;
        } else {
            // Not configured.
            $activitygradeitemid = 0;
        }

        // Check to see if we are in the moule we are displaying results for.
        if (!empty($this->config->activitygradeitemid)) {
            if ($this->get_owning_activity()->id == $this->config->activitygradeitemid) {
                $inactivity = true;
            } else {
                $inactivity = false;
            }
        }

        // Activity ID is missing.
        if (empty($activitygradeitemid)) {
            $this->content->text = get_string('error_emptyactivityid', 'block_activity_results');
            return $this->content;
        }

        // Check to see if we are configured.
        if (empty($this->config->showbest) && empty($this->config->showworst)) {
            $this->content->text = get_string('configuredtoshownothing', 'block_activity_results');
            return $this->content;
        }

        // Check to see if it is a supported grade type.
        if (empty($activity->gradetype) || ($activity->gradetype != GRADE_TYPE_VALUE && $activity->gradetype != GRADE_TYPE_SCALE)) {
            $this->content->text = get_string('error_unsupportedgradetype', 'block_activity_results');
            return $this->content;
        }

        // Get the grades for this activity.
        $sql = 'SELECT * FROM {grade_grades}
                 WHERE itemid = ? AND finalgrade is not NULL
                 ORDER BY finalgrade, timemodified DESC';

        $grades = $DB->get_records_sql($sql, array( $activitygradeitemid));

        if (empty($grades) || $activity->hidden) {
            // No grades available, The block will hide itself in this case.
            return $this->content;
        }

        // Set up results.
        $groupmode = NOGROUPS;
        $best      = array();
        $worst     = array();

        if (!empty($this->config->nameformat)) {
            $nameformat = $this->config->nameformat;
        } else {
            $nameformat = B_ACTIVITYRESULTS_NAME_FORMAT_FULL;
        }

        // Get $cm and context.
        if ($inactivity) {
            $cm = $this->page->cm;
            $context = $this->page->context;
        } else {
            $cm = get_coursemodule_from_instance($activity->itemmodule, $activity->iteminstance, $courseid);
            $context = context_module::instance($cm->id);
        }

        if (!empty($this->config->usegroups)) {
            $groupmode = groups_get_activity_groupmode($cm);

            if ($groupmode == SEPARATEGROUPS && has_capability('moodle/site:accessallgroups', $context)) {
                // If you have the ability to see all groups then lets show them.
                $groupmode = VISIBLEGROUPS;
            }
        }

        switch ($groupmode) {
            case VISIBLEGROUPS:
                // Display group-mode results.
                $groups = groups_get_all_groups($courseid);

                if (empty($groups)) {
                    // No groups exist, sorry.
                    $this->content->text = get_string('error_nogroupsexist', 'block_activity_results');
                    return $this->content;
                }

                // Find out all the userids which have a submitted grade.
                $userids = array();
                $gradeforuser = array();
                foreach ($grades as $grade) {
                    $userids[] = $grade->userid;
                    $gradeforuser[$grade->userid] = (float)$grade->finalgrade;
                }

                // Now find which groups these users belong in.
                list($usertest, $params) = $DB->get_in_or_equal($userids);
                $params[] = $courseid;
                $usergroups = $DB->get_records_sql('
                        SELECT gm.id, gm.userid, gm.groupid, g.name
                        FROM {groups} g
                        LEFT JOIN {groups_members} gm ON g.id = gm.groupid
                        WHERE gm.userid ' . $usertest . ' AND g.courseid = ?', $params);

                // Now, iterate the grades again and sum them up for each group.
                $groupgrades = array();
                foreach ($usergroups as $usergroup) {
                    if (!isset($groupgrades[$usergroup->groupid])) {
                        $groupgrades[$usergroup->groupid] = array(
                                'sum' => (float)$gradeforuser[$usergroup->userid],
                                'number' => 1,
                                'group' => $usergroup->name);
                    } else {
                        $groupgrades[$usergroup->groupid]['sum'] += $gradeforuser[$usergroup->userid];
                        $groupgrades[$usergroup->groupid]['number'] += 1;
                    }
                }

                foreach ($groupgrades as $groupid => $groupgrade) {
                    $groupgrades[$groupid]['average'] = $groupgrades[$groupid]['sum'] / $groupgrades[$groupid]['number'];
                }

                // Sort groupgrades according to average grade, ascending.
                uasort($groupgrades, function($a, $b) {
                    if ($a["average"] == $b["average"]) {
                        return 0;
                    }
                    return ($a["average"] > $b["average"] ? 1 : -1);
                });

                // How many groups do we have with graded member submissions to show?
                $numbest  = empty($this->config->showbest) ? 0 : min($this->config->showbest, count($groupgrades));
                $numworst = empty($this->config->showworst) ? 0 : min($this->config->showworst, count($groupgrades) - $numbest);

                // Collect all the group results we are going to use in $best and $worst.
                $remaining = $numbest;
                $groupgrade = end($groupgrades);
                while ($remaining--) {
                    $best[key($groupgrades)] = $groupgrade['average'];
                    $groupgrade = prev($groupgrades);
                }

                $remaining = $numworst;
                $groupgrade = reset($groupgrades);
                while ($remaining--) {
                    $worst[key($groupgrades)] = $groupgrade['average'];
                    $groupgrade = next($groupgrades);
                }

                // Ready for output!
                if ($activity->gradetype == GRADE_TYPE_SCALE) {
                    // We must display the results using scales.
                    $gradeformat = B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE;
                    // Preload the scale.
                    $scale = $this->get_scale($activity->scaleid);
                } else if (intval(empty($this->config->gradeformat))) {
                    $gradeformat = B_ACTIVITYRESULTS_GRADE_FORMAT_PCT;
                } else {
                    $gradeformat = $this->config->gradeformat;
                }

                // Generate the header.
                $this->content->text .= $this->activity_link($activity, $cm);

                if ($nameformat == B_ACTIVITYRESULTS_NAME_FORMAT_FULL) {
                    if (has_capability('moodle/course:managegroups', $context)) {
                        $grouplink = $CFG->wwwroot.'/group/overview.php?id='.$courseid.'&amp;group=';
                    } else if (course_can_view_participants($context)) {
                        $grouplink = $CFG->wwwroot.'/user/index.php?id='.$courseid.'&amp;group=';
                    } else {
                        $grouplink = '';
                    }
                }

                $rank = 0;
                if (!empty($best)) {
                    $this->content->text .= '<table class="grades"><caption class="pb-0"><h6>';
                    if ($numbest == 1) {
                        $this->content->text .= get_string('bestgroupgrade', 'block_activity_results');
                    } else {
                        $this->content->text .= get_string('bestgroupgrades', 'block_activity_results', $numbest);
                    }
                    $this->content->text .= '</h6></caption><colgroup class="number" />';
                    $this->content->text .= '<colgroup class="name" /><colgroup class="grade" /><tbody>';
                    foreach ($best as $groupid => $averagegrade) {
                        switch ($nameformat) {
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ANON:
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ID:
                                $thisname = get_string('group');
                            break;
                            default:
                            case B_ACTIVITYRESULTS_NAME_FORMAT_FULL:
                                if ($grouplink) {
                                    $thisname = '<a href="'.$grouplink.$groupid.'">'.$groupgrades[$groupid]['group'].'</a>';
                                } else {
                                    $thisname = $groupgrades[$groupid]['group'];
                                }
                            break;
                        }
                        $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                        switch ($gradeformat) {
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE:
                                // Round answer up and locate appropriate scale.
                                $answer = (round($averagegrade, 0, PHP_ROUND_HALF_UP) - 1);
                                if (isset($scale[$answer])) {
                                    $this->content->text .= $scale[$answer];
                                } else {
                                    // Value is not in the scale.
                                    $this->content->text .= get_string('unknown', 'block_activity_results');
                                }
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_FRA:
                                $this->content->text .= $this->activity_format_grade($averagegrade)
                                    . '/' . $this->activity_format_grade($activity->grademax);
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_ABS:
                                $this->content->text .= $this->activity_format_grade($averagegrade);
                            break;
                            default:
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_PCT:
                                $this->content->text .= $this->activity_format_grade((float)$averagegrade /
                                        (float)$activity->grademax * 100).'%';
                            break;
                        }
                        $this->content->text .= '</td></tr>';
                    }
                    $this->content->text .= '</tbody></table>';
                }

                $rank = 0;
                if (!empty($worst)) {
                    $worst = array_reverse($worst, true);
                    $this->content->text .= '<table class="grades"><caption class="pb-0"><h6>';
                    if ($numworst == 1) {
                        $this->content->text .= get_string('worstgroupgrade', 'block_activity_results');
                    } else {
                        $this->content->text .= get_string('worstgroupgrades', 'block_activity_results', $numworst);
                    }
                    $this->content->text .= '</h6></caption><colgroup class="number" />';
                    $this->content->text .= '<colgroup class="name" /><colgroup class="grade" /><tbody>';
                    foreach ($worst as $groupid => $averagegrade) {
                        switch ($nameformat) {
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ANON:
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ID:
                                $thisname = get_string('group');
                            break;
                            default:
                            case B_ACTIVITYRESULTS_NAME_FORMAT_FULL:
                                if ($grouplink) {
                                    $thisname = '<a href="'.$grouplink.$groupid.'">'.$groupgrades[$groupid]['group'].'</a>';
                                } else {
                                    $thisname = $groupgrades[$groupid]['group'];
                                }
                            break;
                        }
                        $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                        switch ($gradeformat) {
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE:
                                // Round answer up and locate appropriate scale.
                                $answer = (round($averagegrade, 0, PHP_ROUND_HALF_UP) - 1);
                                if (isset($scale[$answer])) {
                                    $this->content->text .= $scale[$answer];
                                } else {
                                    // Value is not in the scale.
                                    $this->content->text .= get_string('unknown', 'block_activity_results');
                                }
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_FRA:
                                $this->content->text .= $this->activity_format_grade($averagegrade)
                                    . '/' . $this->activity_format_grade($activity->grademax);
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_ABS:
                                $this->content->text .= $this->activity_format_grade($averagegrade);
                            break;
                            default:
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_PCT:
                                $this->content->text .= $this->activity_format_grade((float)$averagegrade /
                                        (float)$activity->grademax * 100).'%';
                            break;
                        }
                        $this->content->text .= '</td></tr>';
                    }
                    $this->content->text .= '</tbody></table>';
                }
            break;

            case SEPARATEGROUPS:
                // This is going to be just like no-groups mode, only we 'll filter
                // out the grades from people not in our group.
                if (!isloggedin()) {
                    // Not logged in, so show nothing.
                    return $this->content;
                }

                $mygroups = groups_get_all_groups($courseid, $USER->id);
                if (empty($mygroups)) {
                    // Not member of a group, show nothing.
                    return $this->content;
                }

                // Get users from the same groups as me.
                list($grouptest, $params) = $DB->get_in_or_equal(array_keys($mygroups));
                $mygroupsusers = $DB->get_records_sql_menu(
                        'SELECT DISTINCT userid, 1 FROM {groups_members} WHERE groupid ' . $grouptest,
                        $params);

                // Filter out the grades belonging to other users, and proceed as if there were no groups.
                foreach ($grades as $key => $grade) {
                    if (!isset($mygroupsusers[$grade->userid])) {
                        unset($grades[$key]);
                    }
                }

                // No break, fall through to the default case now we have filtered the $grades array.
            default:
            case NOGROUPS:
                // Single user mode.
                $numbest  = empty($this->config->showbest) ? 0 : min($this->config->showbest, count($grades));
                $numworst = empty($this->config->showworst) ? 0 : min($this->config->showworst, count($grades) - $numbest);

                // Collect all the usernames we are going to need.
                $remaining = $numbest;
                $grade = end($grades);
                while ($remaining--) {
                    $best[$grade->userid] = $grade->id;
                    $grade = prev($grades);
                }

                $remaining = $numworst;
                $grade = reset($grades);
                while ($remaining--) {
                    $worst[$grade->userid] = $grade->id;
                    $grade = next($grades);
                }

                if (empty($best) && empty($worst)) {
                    // Nothing to show, for some reason...
                    return $this->content;
                }

                // Now grab all the users from the database.
                $userids = array_merge(array_keys($best), array_keys($worst));
                $fields = array_merge(array('id', 'idnumber'), get_all_user_name_fields());
                $fields = implode(',', $fields);
                $users = $DB->get_records_list('user', 'id', $userids, '', $fields);

                // If configured to view user idnumber, ensure current user can see it.
                $extrafields = get_extra_user_fields($this->context);
                $canviewidnumber = (array_search('idnumber', $extrafields) !== false);

                // Ready for output!
                if ($activity->gradetype == GRADE_TYPE_SCALE) {
                    // We must display the results using scales.
                    $gradeformat = B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE;
                    // Preload the scale.
                    $scale = $this->get_scale($activity->scaleid);
                } else if (intval(empty($this->config->gradeformat))) {
                    $gradeformat = B_ACTIVITYRESULTS_GRADE_FORMAT_PCT;
                } else {
                    $gradeformat = $this->config->gradeformat;
                }

                // Generate the header.
                $this->content->text .= $this->activity_link($activity, $cm);

                $rank = 0;
                if (!empty($best)) {
                    $this->content->text .= '<table class="grades"><caption class="pb-0"><h6>';
                    if ($numbest == 1) {
                        $this->content->text .= get_string('bestgrade', 'block_activity_results');
                    } else {
                        $this->content->text .= get_string('bestgrades', 'block_activity_results', $numbest);
                    }
                    $this->content->text .= '</h6></caption><colgroup class="number" />';
                    $this->content->text .= '<colgroup class="name" /><colgroup class="grade" /><tbody>';

                    foreach ($best as $userid => $gradeid) {
                        switch ($nameformat) {
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ID:
                                $thisname = get_string('user');
                                if ($canviewidnumber) {
                                    $thisname .= ' ' . s($users[$userid]->idnumber);
                                }
                            break;
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ANON:
                                $thisname = get_string('user');
                            break;
                            default:
                            case B_ACTIVITYRESULTS_NAME_FORMAT_FULL:
                                if (has_capability('moodle/user:viewdetails', $context)) {
                                    $thisname = html_writer::link(new moodle_url('/user/view.php',
                                        array('id' => $userid, 'course' => $courseid)), fullname($users[$userid]));
                                } else {
                                    $thisname = fullname($users[$userid]);
                                }
                            break;
                        }
                        $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                        switch ($gradeformat) {
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE:
                                // Round answer up and locate appropriate scale.
                                $answer = (round($grades[$gradeid]->finalgrade, 0, PHP_ROUND_HALF_UP) - 1);
                                if (isset($scale[$answer])) {
                                    $this->content->text .= $scale[$answer];
                                } else {
                                    // Value is not in the scale.
                                    $this->content->text .= get_string('unknown', 'block_activity_results');
                                }
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_FRA:
                                $this->content->text .= $this->activity_format_grade($grades[$gradeid]->finalgrade);
                                $this->content->text .= '/'.$this->activity_format_grade($activity->grademax);
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_ABS:
                                $this->content->text .= $this->activity_format_grade($grades[$gradeid]->finalgrade);
                            break;
                            default:
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_PCT:
                                if ($activity->grademax) {
                                    $this->content->text .= $this->activity_format_grade((float)$grades[$gradeid]->finalgrade /
                                            (float)$activity->grademax * 100).'%';
                                } else {
                                    $this->content->text .= '--%';
                                }
                            break;
                        }
                        $this->content->text .= '</td></tr>';
                    }
                    $this->content->text .= '</tbody></table>';
                }

                $rank = 0;
                if (!empty($worst)) {
                    $worst = array_reverse($worst, true);
                    $this->content->text .= '<table class="grades"><caption class="pb-0"><h6>';
                    if ($numbest == 1) {
                        $this->content->text .= get_string('worstgrade', 'block_activity_results');
                    } else {
                        $this->content->text .= get_string('worstgrades', 'block_activity_results', $numworst);
                    }
                    $this->content->text .= '</h6></caption><colgroup class="number" />';
                    $this->content->text .= '<colgroup class="name" /><colgroup class="grade" /><tbody>';
                    foreach ($worst as $userid => $gradeid) {
                        switch ($nameformat) {
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ID:
                                $thisname = get_string('user');
                                if ($canviewidnumber) {
                                    $thisname .= ' ' . s($users[$userid]->idnumber);
                                };
                            break;
                            case B_ACTIVITYRESULTS_NAME_FORMAT_ANON:
                                $thisname = get_string('user');
                            break;
                            default:
                            case B_ACTIVITYRESULTS_NAME_FORMAT_FULL:
                                if (has_capability('moodle/user:viewdetails', $context)) {
                                    $thisname = html_writer::link(new moodle_url('/user/view.php',
                                        array('id' => $userid, 'course' => $courseid)), fullname($users[$userid]));
                                } else {
                                    $thisname = fullname($users[$userid]);
                                }
                            break;
                        }
                        $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                        switch ($gradeformat) {
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_SCALE:
                                // Round answer up and locate appropriate scale.
                                $answer = (round($grades[$gradeid]->finalgrade, 0, PHP_ROUND_HALF_UP) - 1);
                                if (isset($scale[$answer])) {
                                    $this->content->text .= $scale[$answer];
                                } else {
                                    // Value is not in the scale.
                                    $this->content->text .= get_string('unknown', 'block_activity_results');
                                }
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_FRA:
                                $this->content->text .= $this->activity_format_grade($grades[$gradeid]->finalgrade);
                                $this->content->text .= '/'.$this->activity_format_grade($activity->grademax);
                            break;
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_ABS:
                                $this->content->text .= $this->activity_format_grade($grades[$gradeid]->finalgrade);
                            break;
                            default:
                            case B_ACTIVITYRESULTS_GRADE_FORMAT_PCT:
                                if ($activity->grademax) {
                                    $this->content->text .= $this->activity_format_grade((float)$grades[$gradeid]->finalgrade /
                                            (float)$activity->grademax * 100).'%';
                                } else {
                                    $this->content->text .= '--%';
                                }
                            break;
                        }
                        $this->content->text .= '</td></tr>';
                    }
                    $this->content->text .= '</tbody></table>';
                }
            break;
        }

        return $this->content;
    }

    /**
     * Allows the block to be added multiple times to a single page
     * @return boolean
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Formats the grade to the specified decimal points
     * @param float $grade
     * @return string
     */
    private function activity_format_grade($grade) {
        if (is_null($grade)) {
            return get_string('notyetgraded', 'block_activity_results');
        }
        return format_float($grade, $this->config->decimalpoints);
    }

    /**
     * Generates the Link to the activity module when displayed outside of the module.
     * @param stdclass $activity
     * @param stdclass $cm
     * @return string
     */
    private function activity_link($activity, $cm) {

        $o = html_writer::start_tag('h5');
        $o .= html_writer::link(new moodle_url('/mod/'.$activity->itemmodule.'/view.php',
        array('id' => $cm->id)), format_string(($activity->itemname), true, ['context' => context_module::instance($cm->id)]));
        $o .= html_writer::end_tag('h5');
        return $o;
    }

    /**
     * Generates a numeric array of scale entries
     * @param int $scaleid
     * @return array
     */
    private function get_scale($scaleid) {
        global $DB;
        $scaletext = $DB->get_field('scale', 'scale', array('id' => $scaleid), IGNORE_MISSING);
        $scale = explode ( ',', $scaletext);
        return $scale;

    }

    /**
     * Return the plugin config settings for external functions.
     *
     * @return stdClass the configs for both the block instance and plugin
     * @since Moodle 3.8
     */
    public function get_config_for_external() {
        // Return all settings for all users since it is safe (no private keys, etc..).
        $instanceconfigs = !empty($this->config) ? $this->config : new stdClass();
        $pluginconfigs = get_config('block_activity_results');

        return (object) [
            'instance' => $instanceconfigs,
            'plugin' => $pluginconfigs,
        ];
    }
}
