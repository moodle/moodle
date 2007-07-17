<?php // $Id$
/**
 * File containing the grade_report class.
 * @package gradebook
 */

require_once($CFG->libdir.'/gradelib.php');

/**
 * An abstract class containing variables and methods used by all or most reports.
 * @abstract
 * @package gradebook
 */
class grade_report {
    /**
     * The courseid.
     * @var int $courseid
     */
    var $courseid;

    /** Grade plugin return tracking object.
    var $gpr;

    /**
     * The context.
     * @var int $context
     */
    var $context;

    /**
     * The grade_tree object.
     * @var object $gtree
     */
    var $gtree;

    /**
     * User preferences related to this report.
     * @var array $prefs
     */
    var $prefs = array();

    /**
     * The roles for this report.
     * @var string $gradebookroles
     */
    var $gradebookroles;

    /**
     * base url for sorting by first/last name.
     * @var string $baseurl
     */
    var $baseurl;

    /**
     * base url for paging.
     * @var string $pbarurl
     */
    var $pbarurl;

    /**
     * Current page (for paging).
     * @var int $page
     */
    var $page;

    /**
     * Array of cached language strings (using get_string() all the time takes a long time!).
     * @var array $lang_strings
     */
    var $lang_strings = array();

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $page The current page being viewed (when report is paged)
     */
    function grade_report($courseid, $gpr, $context, $page=null) {
        global $CFG;

        $this->courseid = $courseid;
        $this->gpr      = $gpr;
        $this->context  = $context;
        $this->page     = $page;

        // roles to be displayed in the gradebook
        $this->gradebookroles = $CFG->gradebookroles;

        // Grab the grade_tree for this course
        $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'));
    }

    /**
     * Given the name of a user preference (without grade_report_ prefix), locally saves then returns
     * the value of that preference. If the preference has already been fetched before,
     * the saved value is returned. If the preference is not set at the User level, the $CFG equivalent
     * is given (site default).
     * @static (Can be called statically, but then doesn't benefit from caching)
     * @param string $pref The name of the preference (do not include the grade_report_ prefix)
     * @param int $itemid An optional itemid to check for a more fine-grained preference
     * @return mixed The value of the preference
     */
    function get_pref($pref, $itemid=null) {
        global $CFG;
        $fullprefname = 'grade_report_' . $pref;

        $retval = null;

        if (!isset($this)) {
            if (!empty($itemid)) {
                $retval = get_user_preferences($fullprefname . $itemid, grade_report::get_pref($pref));
            } else {
                $retval = get_user_preferences($fullprefname, $CFG->$fullprefname);
            }
        } else {
            if (empty($this->prefs[$pref.$itemid])) {

                if (!empty($itemid)) {
                    $retval = get_user_preferences($fullprefname . $itemid);
                    if (empty($retval)) {
                        // No item pref found, we are returning the global preference
                        $retval = $this->get_pref($pref);
                        $itemid = null;
                    }
                } else {
                    $retval = get_user_preferences($fullprefname, $CFG->$fullprefname);
                }
                $this->prefs[$pref.$itemid] = $retval;
            } else {
                $retval = $this->prefs[$pref.$itemid];
            }
        }

        return $retval;
    }

    /**
     * Uses set_user_preferences() to update the value of a user preference. If 'default' is given as the value,
     * the preference will be removed in favour of a higher-level preference.
     * @static
     * @param string $pref_name The name of the preference.
     * @param mixed $pref_value The value of the preference.
     * @param int $itemid An optional itemid to which the preference will be assigned
     * @return bool Success or failure.
     * TODO print visual feedback
     */
    function set_pref($pref, $pref_value='default', $itemid=null) {
        $fullprefname = 'grade_report_' . $pref;
        if ($pref_value == 'default') {
            return unset_user_preference($fullprefname.$itemid);
        } else {
            return set_user_preference($fullprefname.$itemid, $pref_value);
        }
    }

    /**
     * Handles form data sent by this report for this report. Abstract method to implement in all children.
     * @abstract
     * @param array $data
     * @return mixed True or array of errors
     */
    function process_data($data) {
        // Implement in children classes
    }

    /**
     * Processes a single action against a category, grade_item or grade.
     * @param string $target Sortorder
     * @param string $action Which action to take (edit, delete etc...)
     * @return
     * TODO Update this, it's quite old and needs a major makeover
     */
    function process_action($target, $action) {
        $element = $this->gtree->locate_element($target);

        switch ($action) {
            case 'edit':
                break;
            case 'delete':
                if ($confirm == 1) { // Perform the deletion
                    //TODO: add proper delete support for grade items and categories
                    //$element['object']->delete();
                    // Print result message

                } else { // Print confirmation dialog
                    $eid = $element['eid'];
                    $strdeletecheckfull = $this->get_lang_string('deletecheck', '', $element['object']->get_name());
                    $linkyes = GRADE_EDIT_URL . "/tree.php?target=$eid&amp;action=delete&amp;confirm=1$this->gtree->commonvars";
                    $linkno = GRADE_EDIT_URL . "/tree.php?$this->gtree->commonvars";
                    notice_yesno($strdeletecheckfull, $linkyes, $linkno);
                }
                break;

            case 'hide':
            // TODO Implement calendar for selection of a date to hide element until
                $element['object']->set_hidden(1);
                $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'));
                break;
            case 'show':
                $element['object']->set_hidden(0);
                $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'));
                break;
            case 'lock':
            // TODO Implement calendar for selection of a date to lock element after
                if (!$element['object']->set_locked(1)) {
                    debugging("Could not update the element's locked state!");
                }
                $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'));
                break;
            case 'unlock':
                if (!$element['object']->set_locked(0)) {
                    debugging("Could not update the element's locked state!");
                }
                $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'));
                break;
            default:
                break;
        }
    }

    /**
     * format grade using lang specific decimal point and thousand separator
     * the result is suitable for printing on html page
     * @static
     * @param float $gradeval raw grade value pulled from db
     * @param int $decimalpoints Optional integers to override global decimalpoints preference
     * @return string $gradeval formatted grade value
     */
    function get_grade_clean($gradeval, $decimalpoints=null) {
        global $CFG;

        if (is_null($gradeval)) {
            $gradeval = '';
        } else {
            // decimal points as specified by user
            if (empty($decimalpoints)) {
                $decimalpoints = $this->get_pref('decimalpoints');
            }
            $gradeval = number_format($gradeval, $decimalpoints, $this->get_lang_string('decpoint', 'langconfig'),
                                      $this->get_lang_string('thousandsep', 'langconfig'));
        }

        return $gradeval;

        /*
        // commenting this out, if this is added, we also need to find the number of decimal place preserved
        // so it can go into number_format
        if ($gradeval != 0) {
            $gradeval = rtrim(trim($gradeval, "0"), ".");
        } else {
            $gradeval = 0;
        }
        */

    }

    /**
     * Given a user input grade, format it to standard format i.e. no thousand separator, and . as decimal point
     * @static
     * @param string $gradeval grade value from user input, language specific format
     * @return string - grade value for storage, en format
     */
    function format_grade($gradeval) {

        $decimalpt = $this->get_lang_string('decpoint', 'langconfig');
        $thousandsep = $this->get_lang_string('thousandsep', 'langconfig');
        // replace decimal point with '.';
        $gradeval = str_replace($decimalpt, '.', $gradeval);
        // thousand separator is not useful
        $gradeval = str_replace($thousandsep, '', $gradeval);

        return clean_param($gradeval, PARAM_NUMBER);
    }

    /**
     * First checks the cached language strings, then returns match if found, or uses get_string()
     * to get it from the DB, caches it then returns it.
     * @param string $strcode
     * @param string $section Optional language section
     * @return string
     */
    function get_lang_string($strcode, $section=null) {
        if (empty($this->lang_strings[$strcode])) {
            $this->lang_strings[$strcode] = get_string($strcode, $section);
        }
        return $this->lang_strings[$strcode];
    }

    /**
     * Computes then returns the percentage value of the grade value within the given range.
     * @param float $gradeval
     * @param float $grademin
     * @param float $grademx
     * @return float $percentage
     */
    function grade_to_percentage($gradeval, $grademin, $grademax) {
        if ($grademin >= $grademax) {
            debugging("The minimum grade ($grademin) was higher than or equal to the maximum grade ($grademax)!!! Cannot proceed with computation.");
        }
        $offset_value = $gradeval - $grademin;
        $offset_max = $grademax - $grademin;
        $factor = 100 / $offset_max;
        $percentage = $offset_value * $factor;
        return $percentage;
    }
}
?>
