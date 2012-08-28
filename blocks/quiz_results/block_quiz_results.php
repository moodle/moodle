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
 * Classes to enforce the various access rules that can apply to a quiz.
 *
 * @package    block
 * @subpackage quiz_results
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/lib.php');


/**
 * Block quiz_results class definition.
 *
 * This block can be added to a course page or a quiz page to display of list of
 * the best/worst students/groups in a particular quiz.
 *
 * @package    block
 * @subpackage quiz_results
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('B_QUIZRESULTS_NAME_FORMAT_FULL', 1);
define('B_QUIZRESULTS_NAME_FORMAT_ID',   2);
define('B_QUIZRESULTS_NAME_FORMAT_ANON', 3);
define('B_QUIZRESULTS_GRADE_FORMAT_PCT', 1);
define('B_QUIZRESULTS_GRADE_FORMAT_FRA', 2);
define('B_QUIZRESULTS_GRADE_FORMAT_ABS', 3);

class block_quiz_results extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_quiz_results');
    }

    function applicable_formats() {
        return array('course' => true, 'mod-quiz' => true);
    }

    /**
     * If this block belongs to a quiz context, then return that quiz's id.
     * Otherwise, return 0.
     * @return integer the quiz id.
     */
    public function get_owning_quiz() {
        if (empty($this->instance->parentcontextid)) {
            return 0;
        }
        $parentcontext = context::instance_by_id($this->instance->parentcontextid);
        if ($parentcontext->contextlevel != CONTEXT_MODULE) {
            return 0;
        }
        $cm = get_coursemodule_from_id('quiz', $parentcontext->instanceid);
        if (!$cm) {
            return 0;
        }
        return $cm->instance;
    }

    function instance_config_save($data, $nolongerused = false) {
        if (empty($data->quizid)) {
            $data->quizid = $this->get_owning_quiz();
        }
        parent::instance_config_save($data);
    }

    function get_content() {
        global $USER, $CFG, $DB;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($this->page->activityname == 'quiz' && $this->page->context->id == $this->instance->parentcontextid) {
            $quiz = $this->page->activityrecord;
            $quizid = $quiz->id;
            $courseid = $this->page->course->id;
            $inquiz = true;
        } else if (!empty($this->config->quizid)) {
            $quizid = $this->config->quizid;
            $quiz = $DB->get_record('quiz', array('id' => $quizid));
            if (empty($quiz)) {
                $this->content->text = get_string('error_emptyquizrecord', 'block_quiz_results');
                return $this->content;
            }
            $courseid = $quiz->course;
            $inquiz = false;
        } else {
            $quizid = 0;
        }

        if (empty($quizid)) {
            $this->content->text = get_string('error_emptyquizid', 'block_quiz_results');
            return $this->content;
        }

        if (empty($this->config->showbest) && empty($this->config->showworst)) {
            $this->content->text = get_string('configuredtoshownothing', 'block_quiz_results');
            return $this->content;
        }

        // Get the grades for this quiz
        $grades = $DB->get_records('quiz_grades', array('quiz' => $quizid), 'grade, timemodified DESC');

        if (empty($grades)) {
            // No grades, sorry
            // The block will hide itself in this case
            return $this->content;
        }

        $groupmode = NOGROUPS;
        $best      = array();
        $worst     = array();

        if (!empty($this->config->nameformat)) {
            $nameformat = $this->config->nameformat;
        } else {
            $nameformat = B_QUIZRESULTS_NAME_FORMAT_FULL;
        }

        if (!empty($this->config->usegroups)) {
            if ($inquiz) {
                $cm = $this->page->cm;
                $context = $this->page->context;
            } else {
                $cm = get_coursemodule_from_instance('quiz', $quizid, $courseid);
                $context = context_module::instance($cm->id);
            }
            $groupmode = groups_get_activity_groupmode($cm);

            if ($groupmode == SEPARATEGROUPS && has_capability('moodle/site:accessallgroups', $context)) {
                // We 'll make an exception in this case
                $groupmode = VISIBLEGROUPS;
            }
        }

        switch ($groupmode) {
            case VISIBLEGROUPS:
            // Display group-mode results
            $groups = groups_get_all_groups($courseid);

            if(empty($groups)) {
                // No groups exist, sorry
                $this->content->text = get_string('error_nogroupsexist', 'block_quiz_results');
                return $this->content;
            }

            // Find out all the userids which have a submitted grade
            $userids = array();
            $gradeforuser = array();
            foreach ($grades as $grade) {
                $userids[] = $grade->userid;
                $gradeforuser[$grade->userid] = (float)$grade->grade;
            }

            // Now find which groups these users belong in
            list($usertest, $params) = $DB->get_in_or_equal($userids);
            $params[] = $courseid;
            $usergroups = $DB->get_records_sql('
                    SELECT gm.id, gm.userid, gm.groupid, g.name
                    FROM {groups} g
                    LEFT JOIN {groups_members} gm ON g.id = gm.groupid
                    WHERE gm.userid ' . $usertest . ' AND g.courseid = ?', $params);

            // Now, iterate the grades again and sum them up for each group
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

            foreach($groupgrades as $groupid => $groupgrade) {
                $groupgrades[$groupid]['average'] = $groupgrades[$groupid]['sum'] / $groupgrades[$groupid]['number'];
            }

            // Sort groupgrades according to average grade, ascending
            uasort($groupgrades, create_function('$a, $b', 'if($a["average"] == $b["average"]) return 0; return ($a["average"] > $b["average"] ? 1 : -1);'));

            // How many groups do we have with graded member submissions to show?
            $numbest  = empty($this->config->showbest) ? 0 : min($this->config->showbest, count($groupgrades));
            $numworst = empty($this->config->showworst) ? 0 : min($this->config->showworst, count($groupgrades) - $numbest);

            // Collect all the group results we are going to use in $best and $worst
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
            $gradeformat = intval(empty($this->config->gradeformat) ? B_QUIZRESULTS_GRADE_FORMAT_PCT : $this->config->gradeformat);

            if (!$inquiz) {
                // Don't show header and link to the quiz if we ARE at the quiz...
                $this->content->text .= '<h1><a href="'.$CFG->wwwroot.'/mod/quiz/view.php?q='.$quizid.'">'.$quiz->name.'</a></h1>';
            }

            if ($nameformat = B_QUIZRESULTS_NAME_FORMAT_FULL) {
                if (has_capability('moodle/course:managegroups', $context)) {
                    $grouplink = $CFG->wwwroot.'/group/overview.php?id='.$courseid.'&amp;group=';
                } else if (has_capability('moodle/course:viewparticipants', $context)) {
                    $grouplink = $CFG->wwwroot.'/user/index.php?id='.$courseid.'&amp;group=';
                } else {
                    $grouplink = '';
                }
            }

            $rank = 0;
            if(!empty($best)) {
                $this->content->text .= '<table class="grades"><caption>';
                $this->content->text .= ($numbest == 1?get_string('bestgroupgrade', 'block_quiz_results'):get_string('bestgroupgrades', 'block_quiz_results', $numbest));
                $this->content->text .= '</caption><colgroup class="number" /><colgroup class="name" /><colgroup class="grade" /><tbody>';
                foreach($best as $groupid => $averagegrade) {
                    switch($nameformat) {
                        case B_QUIZRESULTS_NAME_FORMAT_ANON:
                        case B_QUIZRESULTS_NAME_FORMAT_ID:
                            $thisname = get_string('group');
                        break;
                        default:
                        case B_QUIZRESULTS_NAME_FORMAT_FULL:
                            if ($grouplink) {
                                $thisname = '<a href="'.$grouplink.$groupid.'">'.$groupgrades[$groupid]['group'].'</a>';
                            } else {
                                $thisname = $groupgrades[$groupid]['group'];
                            }
                        break;
                    }
                    $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                    switch($gradeformat) {
                        case B_QUIZRESULTS_GRADE_FORMAT_FRA:
                            $this->content->text .= quiz_format_grade($quiz, $averagegrade).'/'.$quiz->grade;
                        break;
                        case B_QUIZRESULTS_GRADE_FORMAT_ABS:
                            $this->content->text .= quiz_format_grade($quiz, $averagegrade);
                        break;
                        default:
                        case B_QUIZRESULTS_GRADE_FORMAT_PCT:
                            $this->content->text .= round((float)$averagegrade / (float)$quiz->grade * 100).'%';
                        break;
                    }
                    $this->content->text .= '</td></tr>';
                }
                $this->content->text .= '</tbody></table>';
            }

            $rank = 0;
            if(!empty($worst)) {
                $worst = array_reverse($worst, true);
                $this->content->text .= '<table class="grades"><caption>';
                $this->content->text .= ($numworst == 1?get_string('worstgroupgrade', 'block_quiz_results'):get_string('worstgroupgrades', 'block_quiz_results', $numworst));
                $this->content->text .= '</caption><colgroup class="number" /><colgroup class="name" /><colgroup class="grade" /><tbody>';
                foreach($worst as $groupid => $averagegrade) {
                    switch($nameformat) {
                        case B_QUIZRESULTS_NAME_FORMAT_ANON:
                        case B_QUIZRESULTS_NAME_FORMAT_ID:
                            $thisname = get_string('group');
                        break;
                        default:
                        case B_QUIZRESULTS_NAME_FORMAT_FULL:
                            $thisname = '<a href="'.$CFG->wwwroot.'/course/group.php?group='.$groupid.'&amp;id='.$courseid.'">'.$groupgrades[$groupid]['group'].'</a>';
                        break;
                    }
                    $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                    switch($gradeformat) {
                        case B_QUIZRESULTS_GRADE_FORMAT_FRA:
                            $this->content->text .= quiz_format_grade($quiz, $averagegrade).'/'.$quiz->grade;
                        break;
                        case B_QUIZRESULTS_GRADE_FORMAT_ABS:
                            $this->content->text .= quiz_format_grade($quiz, $averagegrade);
                        break;
                        default:
                        case B_QUIZRESULTS_GRADE_FORMAT_PCT:
                            $this->content->text .= round((float)$averagegrade / (float)$quiz->grade * 100).'%';
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
                // Not logged in, so show nothing
                return $this->content;
            }

            $mygroups = groups_get_all_groups($courseid, $USER->id);
            if(empty($mygroups)) {
                // Not member of a group, show nothing
                return $this->content;
            }

            // Get users from the same groups as me.
            list($grouptest, $params) = $DB->get_in_or_equal(array_keys($mygroups));
            $mygroupsusers = $DB->get_records_sql_menu(
                    'SELECT DISTINCT userid, 1 FROM {groups_members} WHERE groupid ' . $grouptest,
                    $params);

            // Filter out the grades belonging to other users, and proceed as if there were no groups
            foreach ($grades as $key => $grade) {
                if (!isset($mygroupsusers[$grade->userid])) {
                    unset($grades[$key]);
                }
            }

            // No break, fall through to the default case now we have filtered the $grades array.
            default:
            case NOGROUPS:
            // Single user mode
            $numbest  = empty($this->config->showbest) ? 0 : min($this->config->showbest, count($grades));
            $numworst = empty($this->config->showworst) ? 0 : min($this->config->showworst, count($grades) - $numbest);

            // Collect all the usernames we are going to need
            $remaining = $numbest;
            $grade = end($grades);
            while($remaining--) {
                $best[$grade->userid] = $grade->id;
                $grade = prev($grades);
            }

            $remaining = $numworst;
            $grade = reset($grades);
            while($remaining--) {
                $worst[$grade->userid] = $grade->id;
                $grade = next($grades);
            }

            if(empty($best) && empty($worst)) {
                // Nothing to show, for some reason...
                return $this->content;
            }

            // Now grab all the users from the database
            $userids = array_merge(array_keys($best), array_keys($worst));
            $users = $DB->get_records_list('user', 'id', $userids, '', 'id, firstname, lastname, idnumber');

            // Ready for output!

            $gradeformat = intval(empty($this->config->gradeformat) ? B_QUIZRESULTS_GRADE_FORMAT_PCT : $this->config->gradeformat);

            if(!$inquiz) {
                // Don't show header and link to the quiz if we ARE at the quiz...
                $this->content->text .= '<h1><a href="'.$CFG->wwwroot.'/mod/quiz/view.php?q='.$quizid.'">'.$quiz->name.'</a></h1>';
            }

            $rank = 0;
            if(!empty($best)) {
                $this->content->text .= '<table class="grades"><caption>';
                $this->content->text .= ($numbest == 1?get_string('bestgrade', 'block_quiz_results'):get_string('bestgrades', 'block_quiz_results', $numbest));
                $this->content->text .= '</caption><colgroup class="number" /><colgroup class="name" /><colgroup class="grade" /><tbody>';
                foreach($best as $userid => $gradeid) {
                    switch($nameformat) {
                        case B_QUIZRESULTS_NAME_FORMAT_ID:
                            $thisname = get_string('user').' '.$users[$userid]->idnumber;
                        break;
                        case B_QUIZRESULTS_NAME_FORMAT_ANON:
                            $thisname = get_string('user');
                        break;
                        default:
                        case B_QUIZRESULTS_NAME_FORMAT_FULL:
                            $thisname = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid.'&amp;course='.$courseid.'">'.fullname($users[$userid]).'</a>';
                        break;
                    }
                    $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                    switch($gradeformat) {
                        case B_QUIZRESULTS_GRADE_FORMAT_FRA:
                            $this->content->text .=  quiz_format_grade($quiz, $grades[$gradeid]->grade).'/'.$quiz->grade;
                        break;
                        case B_QUIZRESULTS_GRADE_FORMAT_ABS:
                            $this->content->text .= quiz_format_grade($quiz, $grades[$gradeid]->grade);
                        break;
                        default:
                        case B_QUIZRESULTS_GRADE_FORMAT_PCT:
                            if ($quiz->grade) {
                                $this->content->text .= round((float)$grades[$gradeid]->grade / (float)$quiz->grade * 100).'%';
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
            if(!empty($worst)) {
                $worst = array_reverse($worst, true);
                $this->content->text .= '<table class="grades"><caption>';
                $this->content->text .= ($numworst == 1?get_string('worstgrade', 'block_quiz_results'):get_string('worstgrades', 'block_quiz_results', $numworst));
                $this->content->text .= '</caption><colgroup class="number" /><colgroup class="name" /><colgroup class="grade" /><tbody>';
                foreach($worst as $userid => $gradeid) {
                    switch($nameformat) {
                        case B_QUIZRESULTS_NAME_FORMAT_ID:
                            $thisname = get_string('user').' '.$users[$userid]->idnumber;
                        break;
                        case B_QUIZRESULTS_NAME_FORMAT_ANON:
                            $thisname = get_string('user');
                        break;
                        default:
                        case B_QUIZRESULTS_NAME_FORMAT_FULL:
                            $thisname = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid.'&amp;course='.$courseid.'">'.fullname($users[$userid]).'</a>';
                        break;
                    }
                    $this->content->text .= '<tr><td>'.(++$rank).'.</td><td>'.$thisname.'</td><td>';
                    switch($gradeformat) {
                        case B_QUIZRESULTS_GRADE_FORMAT_FRA:
                            $this->content->text .= quiz_format_grade($quiz, $grades[$gradeid]->grade).'/'.$quiz->grade;
                        break;
                        case B_QUIZRESULTS_GRADE_FORMAT_ABS:
                            $this->content->text .= quiz_format_grade($quiz, $grades[$gradeid]->grade);
                        break;
                        default:
                        case B_QUIZRESULTS_GRADE_FORMAT_PCT:
                            $this->content->text .= round((float)$grades[$gradeid]->grade / (float)$quiz->grade * 100).'%';
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

    function instance_allow_multiple() {
        return true;
    }
}


