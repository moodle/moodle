<?php //$Id$

define('GRADE_FORMAT_PCT', 1);
define('GRADE_FORMAT_FRA', 2);
define('GRADE_FORMAT_ABS', 3);

class block_quiz_results extends block_base {
    function init() {
        $this->title = get_string('formaltitle', 'block_quiz_results');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2005012500;
    }

    function get_content() {
        global $USER, $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }
        if (empty($this->instance)) {
            $this->content = '';
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if($this->instance->pagetype == MOODLE_PAGE_COURSE) {
            // We need to see if we are monitoring a quiz 
            $quizid   = empty($this->config->quizid) ? 0 : $this->config->quizid;
            $courseid = $this->instance->pageid;
        }
        else {
            // Assuming we are displayed in the quiz view page
            // TODO
            $quizid   = 0;
            $courseid = 0;
        }

        if(empty($quizid)) {
            return $this->content;
        }

        // Get the quiz record
        $quiz = get_record('quiz', 'id', $quizid);
        if(empty($quiz)) {
            return $this->content;
        }

        // Get the grades for this quiz
        $grades = get_records('quiz_grades', 'quiz', $quizid, 'grade + 0, timemodified DESC');

        if(empty($grades)) {
            // No grades, sorry
            // TODO
        }

        $numbest  = empty($this->config->showbest) ? 0 : min($this->config->showbest, count($grades));
        $numworst = empty($this->config->showworst) ? 0 : min($this->config->showworst, count($grades) - $numbest);
        $best  = array();
        $worst = array();

        if(!empty($this->config->usegroups)) {
            // Group mode activated, what about it?
            // TODO
        }
        else {
            // Single user mode

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
            $users = get_records_list('user', 'id', implode(',',$userids), '', 'id, firstname, lastname');

            // Ready for output!

            $gradeformat = intval(empty($this->config->gradeformat) ? GRADE_FORMAT_PCT : $this->config->gradeformat);

            $this->content->text .= '<h1><a href="'.$CFG->wwwroot.'/mod/quiz/view.php?q='.$quizid.'">'.$quiz->name.'</a></h1>';

            $rank = 0;
            if(!empty($best)) {
                $this->content->text .= '<h2>'.get_string('bestgrades', 'block_quiz_results', $numbest).'</h2>';
                $this->content->text .= '<table class="grades"><tbody>';
                foreach($best as $userid => $gradeid) {
                    $this->content->text .= '<tr><td width="10%">'.(++$rank).'.</td><td><a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid.'&amp;course='.$courseid.'">'.fullname($users[$userid]).'</a></td><td width="10%">';
                    switch($gradeformat) {
                        case GRADE_FORMAT_FRA:
                            $this->content->text .= ($grades[$gradeid]->grade.'/'.$quiz->grade);
                        break;
                        case GRADE_FORMAT_ABS:
                            $this->content->text .= $grades[$gradeid]->grade;
                        break;
                        default:
                        case GRADE_FORMAT_PCT:
                            $this->content->text .= round(intval($grades[$gradeid]->grade) / intval($quiz->grade) * 100).'%';
                        break;
                    }
                    $this->content->text .= '</td></tr>';
                }
                $this->content->text .= '</tbody></table>';
            }

            $rank = 0;
            if(!empty($worst)) {
                $worst = array_reverse($worst, true);
                $this->content->text .= '<h2>'.get_string('worstgrades', 'block_quiz_results', $numworst).'</h2>';
                $this->content->text .= '<table class="grades"><tbody>';
                foreach($worst as $userid => $gradeid) {
                    $this->content->text .= '<tr><td width="10%">'.(++$rank).'.</td><td><a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid.'&amp;course='.$courseid.'">'.fullname($users[$userid]).'</a></td><td width="10%">';
                    switch($gradeformat) {
                        case GRADE_FORMAT_FRA:
                            $this->content->text .= ($grades[$gradeid]->grade.'/'.$quiz->grade);
                        break;
                        case GRADE_FORMAT_ABS:
                            $this->content->text .= $grades[$gradeid]->grade;
                        break;
                        default:
                        case GRADE_FORMAT_PCT:
                            $this->content->text .= round(intval($grades[$gradeid]->grade) / intval($quiz->grade) * 100).'%';
                        break;
                    }
                    $this->content->text .= '</td></tr>';
                }
                $this->content->text .= '</tbody></table>';
            }

        }


        return $this->content;
    }

    function instance_allow_config() {
        return true;
    }
}

?>
