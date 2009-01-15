<?php
// Used for tracking conditions that apply before activities are displayed
// to students ('conditional availability').

/** The activity is not displayed to students at all when conditions aren't met. */
define('CONDITION_STUDENTVIEW_HIDE',0);
/** The activity is displayed to students as a greyed-out name, with informational
    text that explains the conditions under which it will be available. */
define('CONDITION_STUDENTVIEW_SHOW',1);

/** The $cm variable is expected to contain all completion-related data */
define('CONDITION_MISSING_NOTHING',0);
/** The $cm variable is expected to contain the fields from course_modules but 
    not the course_modules_availability data */
define('CONDITION_MISSING_EXTRATABLE',1);
/** The $cm variable is expected to contain nothing except the ID */
define('CONDITION_MISSING_EVERYTHING',2);

class condition_info {
    private $cm,$gotdata;

    /**
     * Constructs with course-module details.
     *
     * @param object $cm Moodle course-module object. May have extra fields
     *   ->conditionsgrade, ->conditionscompletion which should come from 
     *   get_fast_modinfo. Should have ->availablefrom, ->availableuntil, 
     *   and ->showavailability, ->course; but the only required thing is ->id.
     * @param int $expectingmissing Used to control whether or not a developer
     *   debugging message (performance warning) will be displayed if some of 
     *   the above data is missing and needs to be retrieved; a 
     *   CONDITION_MISSING_xx constant
     * @param bool $loaddata If you need a 'write-only' object, set this value
     *   to false to prevent database access from constructor
     * @return condition_info Object which can retrieve information about the
     *   activity
     */
    public function __construct($cm,$expectingmissing=CONDITION_MISSING_NOTHING,
        $loaddata=true) {
        global $DB;

        // Check ID as otherwise we can't do the other queries
        if(empty($cm->id)) {
            throw new coding_exception("Invalid parameters; course-module ID not included");
        }

        // If not loading data, don't do anything else
        if(!$loaddata) {
            $this->cm=(object)array('id'=>$cm->id);
            $this->gotdata=false;
            return;
        }

        // Missing basic data from course_modules
        if(!isset($cm->availablefrom) || !isset($cm->availableuntil) || 
            !isset($cm->showavailability) || !isset($cm->course)) {
            if($expectingmissing<CONDITION_MISSING_EVERYTHING) {
                debugging('Performance warning: condition_info constructor is 
                    faster if you pass in $cm with at least basic fields 
                    (availablefrom,availableuntil,showavailability,course). 
                    [This warning can be disabled, see phpdoc.]',
                    DEBUG_DEVELOPER);
            }
            $cm=$DB->get_record('course_modules',array('id'=>$cm->id),
                'id,course,availablefrom,availableuntil,showavailability');
        }

        $this->cm=clone($cm);
        $this->gotdata=true;

        // Missing extra data
        if(!isset($cm->conditionsgrade) || !isset($cm->conditionscompletion)) {
            if($expectingmissing<CONDITION_MISSING_EXTRATABLE) {
                debugging('Performance warning: condition_info constructor is 
                    faster if you pass in a $cm from get_fast_modinfo.
                    [This warning can be disabled, see phpdoc.]',
                    DEBUG_DEVELOPER);
            }

            self::fill_availability_conditions($this->cm);
        }
    }

    /**
     * Adds the extra availability conditions (if any) into the given 
     * course-module object.
     *
     * @param object &$cm Moodle course-module data object
     */
    public static function fill_availability_conditions(&$cm) {
        if(empty($cm->id)) {
            throw new coding_exception("Invalid parameters; course-module ID not included");
        }

        // Does nothing if the variables are already present
        if(!isset($cm->conditionsgrade) ||
            !isset($cm->conditionscompletion)) {
            $cm->conditionsgrade=array();
            $cm->conditionscompletion=array();

            global $DB,$CFG;
            $conditions=$DB->get_records_sql($sql="
SELECT 
    cma.id as cmaid, gi.*,cma.sourcecmid,cma.requiredcompletion,cma.gradeitemid,
    cma.grademin as conditiongrademin, cma.grademax as conditiongrademax
FROM
    {course_modules_availability} cma
    LEFT JOIN {grade_items} gi ON gi.id=cma.gradeitemid
WHERE
    coursemoduleid=?",array($cm->id));
            foreach($conditions as $condition) {
                if(!is_null($condition->sourcecmid)) {
                    $cm->conditionscompletion[$condition->sourcecmid]=
                        $condition->requiredcompletion;
                } else {                    
                    $minmax=new stdClass;
                    $minmax->min=$condition->conditiongrademin;
                    $minmax->max=$condition->conditiongrademax;
                    $minmax->name=self::get_grade_name($condition);
                    $cm->conditionsgrade[$condition->gradeitemid]=$minmax;
                }
            }
        }
    }
    
    /**
     * Obtains the name of a grade item.
     * @param object $gradeitemobj Object from get_record on grade_items table,
     *     (can be empty if you want to just get !missing)
     * @return string Name of item of !missing if it didn't exist
     */
    private static function get_grade_name($gradeitemobj) {
        global $CFG;
        if(isset($gradeitemobj->id)) {
            require_once($CFG->libdir.'/gradelib.php');
            $item=new grade_item;
            grade_object::set_properties($item,$gradeitemobj);    
            return $item->get_name();
        } else {
            return '!missing'; // Ooops, missing grade
        }
    }

    /**
     * @return A course-module object with all the information required to
     *   determine availability.
     * @throws coding_exception If data wasn't loaded
     */
    public function get_full_course_module() {
        $this->require_data();
        return $this->cm;
    }

    /**
     * Adds to the database a condition based on completion of another module.
     * @param int $cmid ID of other module
     * @param int $requiredcompletion COMPLETION_xx constant
     */
    public function add_completion_condition($cmid,$requiredcompletion) {
        // Add to DB
        global $DB;
        $DB->insert_record('course_modules_availability',
            (object)array('coursemoduleid'=>$this->cm->id,
                'sourcecmid'=>$cmid,'requiredcompletion'=>$requiredcompletion),
            false);

        // Store in memory too
        $this->cm->conditionscompletion[$cmid]=$requiredcompletion;
    }

    /**
     * Adds to the database a condition based on the value of a grade item.
     * @param int $gradeitemid ID of grade item
     * @param float $min Minimum grade (>=), up to 5 decimal points, or null if none
     * @param float $max Maximum grade (<), up to 5 decimal points, or null if none
     * @param bool $updateinmemory If true, updates data in memory; otherwise,
     *   memory version may be out of date (this has performance consequences,
     *   so don't do it unless it really needs updating)
     */
    public function add_grade_condition($gradeitemid,$min,$max,$updateinmemory=false) {
        // Normalise nulls
        if($min==='') {
            $min=null;
        }
        if($max==='') {
            $max=null;
        }
        // Add to DB
        global $DB;
        $DB->insert_record('course_modules_availability',
            (object)array('coursemoduleid'=>$this->cm->id,
                'gradeitemid'=>$gradeitemid,'grademin'=>$min,'grademax'=>$max),
            false);

        // Store in memory too
        if($updateinmemory) {
            $this->cm->conditionsgrade[$gradeitemid]=(object)array(
                'min'=>$min,'max'=>$max);
            $this->cm->conditionsgrade[$gradeitemid]->name=
                self::get_grade_name($DB->get_record('grade_items',
                    array('id'=>$gradeitemid)));
        }
    }

    /**
     * Erases from the database all conditions for this activity.
     */
    public function wipe_conditions() {
        // Wipe from DB
        global $DB;
        $DB->delete_records('course_modules_availability',
            array('coursemoduleid'=>$this->cm->id));

        // And from memory
        $this->cm->conditionsgrade=array();
        $this->cm->conditionscompletion=array();
    }

    /**
     * Obtains a string describing all availability restrictions (even if
     * they do not apply any more).
     * @param object $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo. The value supplied 
     *   here must include list of all CMs with 'id' and 'name'
     * @return string Information string (for admin) about all restrictions on 
     *   this item
     * @throws coding_exception If data wasn't loaded
     */
    public function get_full_information($modinfo=null) {
        $this->require_data();                
        global $COURSE,$DB;

        $information='';

        // Completion conditions
        if(count($this->cm->conditionscompletion)>0) {
            if($this->cm->course==$COURSE->id) {
                $course=$COURSE;
            } else {
                $course=$DB->get_record('course',array('id'=>$this->cm->course),'id,enablecompletion,modinfo');
            }
            foreach($this->cm->conditionscompletion as $cmid=>$expectedcompletion) {
                if(!$modinfo) {
                    $modinfo=get_fast_modinfo($course);
                }
                $information.=get_string(
                    'requires_completion_'.$expectedcompletion,
                    'condition',$modinfo->cms[$cmid]->name).' ';
            }
        }

        // Grade conditions
        if(count($this->cm->conditionsgrade)>0) {
            foreach($this->cm->conditionsgrade as $gradeitemid=>$minmax) {
                // String depends on type of requirement. We are coy about
                // the actual numbers, in case grades aren't released to
                // students.
                if(is_null($minmax->min) && is_null($minmax->max)) {
                    $string='any';
                } else if(is_null($minmax->max)) {
                    $string='min';
                } else if(is_null($minmax->min)) {
                    $string='max';
                } else {
                    $string='range';
                }
                $information.=get_string('requires_grade_'.$string,'condition',$minmax->name).' ';
            }
        }

        // Dates
        if($this->cm->availablefrom) {
            $information.=get_string('requires_date','condition',userdate(
                $this->cm->availablefrom,get_string('strftimedate','langconfig')));
        }

        if($this->cm->availableuntil) {
            $information.=get_string('requires_date_before','condition',userdate(
                $this->cm->availableuntil,get_string('strftimedate','langconfig')));
        }

        $information=trim($information);
        return $information;
    }

    /**
     * Determines whether this particular course-module is currently available
     * according to these criteria. 
     * 
     * - This does not include the 'visible' setting (i.e. this might return 
     *   true even if visible is false); visible is handled independently.
     * - This does not take account of the viewhiddenactivities capability.
     *   That should apply later.
     *
     * @param string &$information If the item has availability restrictions,
     *   a string that describes the conditions will be stored in this variable; 
     *   if this variable is set blank, that means don't display anything
     * @param bool $grabthelot Performance hint: if true, caches information 
     *   required for all course-modules, to make the front page and similar
     *   pages work more quickly (works only for current user)
     * @param int $userid If set, specifies a different user ID to check availability for
     * @param object $modinfo Usually leave as null for default. Specify when
     *   calling recursively from inside get_fast_modinfo. The value supplied 
     *   here must include list of all CMs with 'id' and 'name'
     * @return bool True if this item is available to the user, false otherwise
     * @throws coding_exception If data wasn't loaded
     */
    public function is_available(&$information,$grabthelot=false,$userid=0,$modinfo=null) {
        $this->require_data();                
        global $COURSE,$DB;

        $available=true;
        $information='';

        // Check each completion condition
        if(count($this->cm->conditionscompletion)>0) {
            if($this->cm->course==$COURSE->id) {
                $course=$COURSE;
            } else {
                $course=$DB->get_record('course',array('id'=>$this->cm->course),'id,enablecompletion,modinfo');
            }

            $completion=new completion_info($course);
            foreach($this->cm->conditionscompletion as $cmid=>$expectedcompletion) {
                // The completion system caches its own data
                $completiondata=$completion->get_data((object)array('id'=>$cmid),
                    $grabthelot,$userid,$modinfo);

                $thisisok=true;
                if($expectedcompletion==COMPLETION_COMPLETE) {
                    // 'Complete' also allows the pass, fail states
                    switch($completiondata->completionstate) {
                        case COMPLETION_COMPLETE:
                        case COMPLETION_COMPLETE_FAIL:
                        case COMPLETION_COMPLETE_PASS:
                            break;
                        default:
                            $thisisok=false;
                    }
                } else {
                    // Other values require exact match
                    if($completiondata->completionstate!=$expectedcompletion) {
                        $thisisok=false;
                    }
                }
                if(!$thisisok) {
                    $available=false;
                    if(!$modinfo) {
                        $modinfo=get_fast_modinfo($course);
                    }
                    $information.=get_string(
                        'requires_completion_'.$expectedcompletion,
                        'condition',$modinfo->cms[$cmid]->name).' ';
                }
            }
        }

        // Check each grade condition
        if(count($this->cm->conditionsgrade)>0) {
            foreach($this->cm->conditionsgrade as $gradeitemid=>$minmax) {
                $score=$this->get_cached_grade_score($gradeitemid,$grabthelot,$userid);
                if($score===false ||
                    (!is_null($minmax->min) && $score<$minmax->min) || 
                    (!is_null($minmax->max) && $score>=$minmax->max)) {
                    // Grade fail
                    $available=false;
                    // String depends on type of requirement. We are coy about
                    // the actual numbers, in case grades aren't released to
                    // students.
                    if(is_null($minmax->min) && is_null($minmax->max)) {
                        $string='any';
                    } else if(is_null($minmax->max)) {
                        $string='min';
                    } else if(is_null($minmax->min)) {
                        $string='max';
                    } else {
                        $string='range';
                    }
                    $information.=get_string('requires_grade_'.$string,'condition',$minmax->name).' ';
                }
            }
        }

        // Test dates
        if($this->cm->availablefrom) {
            if(time() < $this->cm->availablefrom) {
                $available=false;
                $information.=get_string('requires_date','condition',userdate(
                    $this->cm->availablefrom,get_string('strftimedate','langconfig')));
            }
        }

        if($this->cm->availableuntil) {
            if(time() >= $this->cm->availableuntil) {
                $available=false;
                // But we don't display any information about this case. This is
                // because the only reason to set a 'disappear' date is usually
                // to get rid of outdated information/clutter in which case there
                // is no point in showing it...

                // Note it would be nice if we could make it so that the 'until'
                // date appears below the item while the item is still accessible,
                // unfortunately this is not possible in the current system. Maybe
                // later, or if somebody else wants to add it.
            }
        }

        $information=trim($information);
        return $available;
    }

    /**
     * @return bool True if information about availability should be shown to
     *   normal users
     * @throws coding_exception If data wasn't loaded
     */
    public function show_availability() {
        $this->require_data();
        return $this->cm->showavailability;
    }
    
    /**
     * Internal function cheks that data was loaded.
     * @throws coding_exception If data wasn't loaded
     */
    private function require_data() {
        if(!$this->gotdata) {
            throw new coding_exception('Error: cannot call when info was '.
                'constructed without data');
        }
    }

    /**
     * Obtains a grade score. Note that this score should not be displayed to 
     * the user, because gradebook rules might prohibit that. It may be a 
     * non-final score subject to adjustment later.
     *
     * @param int $gradeitemid Grade item ID we're interested in
     * @param bool $grabthelot If true, grabs all scores for current user on 
     *   this course, so that later ones come from cache
     * @param int $userid Set if requesting grade for a different user (does 
     * not use cache)
     * @return float Grade score, or false if user does not have a grade yet
     */
    private function get_cached_grade_score($gradeitemid,$grabthelot=false,$userid=0) {
        global $USER, $DB, $SESSION;
        if($userid==0 || $userid=$USER->id) {
            // For current user, go via cache in session
            if(empty($SESSION->gradescorecache) || $SESSION->gradescorecacheuserid!=$USER->id) {
                $SESSION->gradescorecache=array();
                $SESSION->gradescorecacheuserid=$USER->id;
            } 
            if(!array_key_exists($gradeitemid,$SESSION->gradescorecache)) {
                if($grabthelot) {
                    // Get all grades for the current course
                    $rs=$DB->get_recordset_sql("
SELECT
    gi.id,gg.finalgrade 
FROM 
    {grade_items} gi
    LEFT JOIN {grade_grades} gg ON gi.id=gg.itemid AND gg.userid=?
WHERE
    gi.courseid=?",array($USER->id,$this->cm->course));
                    foreach($rs as $record) {
                        $SESSION->gradescorecache[$record->id]=
                            is_null($record->finalgrade)
                                ? false 
                                : $record->finalgrade;

                    }
                    $rs->close();
                    // And if it's still not set, well it doesn't exist (eg
                    // maybe the user set it as a condition, then deleted the
                    // grade item) so we call it false
                    if(!array_key_exists($gradeitemid,$SESSION->gradescorecache)) {
                        $SESSION->gradescorecache[$gradeitemid]=false;
                    }
                } else {
                    // Just get current grade
                    $score=$DB->get_field('grade_grades','finalgrade',array(
                        'userid'=>$USER->id,'itemid'=>$gradeitemid));
                    // Treat the case where row exists but is null, same as
                    // case where row doesn't exist
                    if(is_null($score)) {
                        $score=false;
                    }
                    $SESSION->gradescorecache[$gradeitemid]=$score;
                }
            }
            return $SESSION->gradescorecache[$gradeitemid];
        } else {
            // Not the current user, so request the score individually
            $score=$DB->get_field('grade_grades','finalgrade',array(
                'userid'=>$userid,'itemid'=>$gradeitemid));
            if($score===null) {
                $score=false;
            }
            return $score;
        }
    }

    /** For testing only. Wipes information cached in user session. */
    static function wipe_session_cache() {
        global $SESSION;
        unset($SESSION->gradescorecache);
        unset($SESSION->gradescorecacheuserid);
    }

    /**
     * Utility function called by modedit.php; updates the 
     * course_modules_availability table based on the module form data.
     *
     * @param object $cm Course-module with as much data as necessary, min id
     * @param unknown_type $fromform
     * @param unknown_type $wipefirst
     */
    public static function update_cm_from_form($cm,$fromform,$wipefirst=true) {
        $ci=new condition_info($cm,CONDITION_MISSING_EVERYTHING,false);
        if($wipefirst) {
            $ci->wipe_conditions();
        }
        foreach($fromform->conditiongradegroup as $record) {
            if($record['conditiongradeitemid']) {
                $ci->add_grade_condition($record['conditiongradeitemid'],
                    $record['conditiongrademin'],$record['conditiongrademax']);
            }
        }
        if(isset($fromform->conditioncompletiongroup)) {
            foreach($fromform->conditioncompletiongroup as $record) {
                if($record['conditionsourcecmid']) {
                    $ci->add_completion_condition($record['conditionsourcecmid'],
                        $record['conditionrequiredcompletion']);
                }
            }
        }
    }

    /**
     * Used in course/lib.php because we need to disable the completion JS if
     * a completion value affects a conditional activity.
     * @param object $course Moodle course object
     * @param object $cm Moodle course-module
     * @return bool True if this is used in a condition, false otherwise
     */
    public static function completion_value_used_as_condition($course,$cm) {
        // Have we already worked out a list of required completion values
        // for this course? If so just use that
        static $affected = array();
        if (!array_key_exists($course->id, $affected)) {
            // We don't have data for this course, build it
            $modinfo = get_fast_modinfo($course);
            $affected[$course->id] = array();
            foreach ($modinfo->cms as $cm) {
                foreach ($cm->conditionscompletion as $cmid=>$expectedcompletion) {
                    $affected[$course->id][$cmid] = true;
                }
            }
        }
        return array_key_exists($cm->id,$affected[$course->id]);
    }
}
?>
