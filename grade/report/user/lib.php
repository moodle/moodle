<?php // $Id$
/**
 * File in which the user_report class is defined.
 * @package gradebook
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Class providing an API for the user report building and displaying.
 * @uses grade_report
 * @package gradebook
 */
class grade_report_user extends grade_report {

    /**
     * The user.
     * @var object $user
     */
    var $user;

    /**
     * A flexitable to hold the data.
     * @var object $table
     */
    var $table;

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $userid The id of the user
     */
    function grade_report_user($courseid, $gpr, $context, $userid) {
        global $CFG;
        parent::grade_report($courseid, $gpr, $context);

        // Grab the grade_tree for this course
        $this->gtree = new grade_tree($this->courseid, true, $this->get_pref('aggregationposition'));

        // get the user (for full name)
        $this->user = get_record('user', 'id', $userid);

        // base url for sorting by first/last name
        $this->baseurl = $CFG->wwwroot.'/grade/report?id='.$courseid.'&amp;userid='.$userid;
        $this->pbarurl = $this->baseurl;

        // Setup groups if requested
        if ($this->get_pref('showgroups')) {
            $this->setup_groups();
        }

        $this->setup_table();
    }

    /**
     * Prepares the headers and attributes of the flexitable.
     */
    function setup_table() {
        /*
        * Table has 6 columns
        *| pic  | itemname/description | grade (grade_final) | percentage | rank | feedback |
        */

        // setting up table headers
        $tablecolumns = array('itempic', 'itemname', 'grade', 'percentage', 'rank', 'feedback');
        $tableheaders = array('', $this->get_lang_string('gradeitem', 'grades'), $this->get_lang_string('grade'),
            $this->get_lang_string('percent', 'grades'), $this->get_lang_string('rank', 'grades'),
            $this->get_lang_string('feedback'));

        $this->table = new flexible_table('grade-report-user-'.$this->courseid);

        $this->table->define_columns($tablecolumns);
        $this->table->define_headers($tableheaders);
        $this->table->define_baseurl($this->baseurl);

        $this->table->set_attribute('cellspacing', '0');
        $this->table->set_attribute('id', 'user-grade');
        $this->table->set_attribute('class', 'boxaligncenter generaltable');

        // not sure tables should be sortable or not, because if we allow it then sorted resutls distort grade category structure and sortorder
        $this->table->set_control_variables(array(
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_HIDE    => 'shide',
                TABLE_VAR_SHOW    => 'sshow',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage'
                ));

        $this->table->setup();
    }

    function fill_table() {
        global $CFG;
        $numusers = $this->get_numusers();

        if ($all_grade_items = grade_item::fetch_all(array('courseid'=>$this->courseid))) {
            $grade_items = array();
            foreach ($all_grade_items as $item) {
                $grade_items[$item->sortorder] = $item;
            }
            unset($all_grade_items);
            ksort($grade_items);

            $total = $grade_items[1];
            unset($grade_items[1]);
            $grade_items[] = $total;

            foreach ($grade_items as $grade_item) {

                $data = array();

                $grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$this->user->id));
                $grade_text = $grade_grade->load_text();

                /// prints mod icon if available
                if ($grade_item->itemtype == 'mod') {
                    $iconpath = $CFG->dirroot.'/mod/'.$grade_item->itemmodule.'/icon.gif';
                    $icon = $CFG->wwwroot.'/mod/'.$grade_item->itemmodule.'/icon.gif';
                    if (file_exists($iconpath)) {
                        $data[] = '<img src = "'.$icon.'" alt="'.$grade_item->itemname.'" class="activityicon"/>';
                    }
                } else {
                    $data[] = '';
                }

                // TODO: indicate items that "needsupdate" - missing final calculation

                /// prints grade item name
                if ($grade_item->is_course_item() or $grade_item->is_category_item()) {
                    $data[] = '<b>'.$grade_item->get_name().'</b>';
                } else {
                    $data[] = $grade_item->get_name();
                }

                /// prints the grade

                if ($grade_grade->is_excluded()) {
                    $excluded = get_tring('excluded', 'grades').' ';
                } else {
                    $excluded = '';
                }

                if ($grade_item->scaleid) {
                    // using scales
                    if ($scale = get_record('scale', 'id', $grade_item->scaleid)) {
                        $scales = explode(",", $scale->scale);
                        // reindex because scale is off 1
                        // invalid grade if gradeval < 1
                        if ((int) $grade_grade->finalgrade < 1) {
                            $data[] = $excluded.'-';
                        } else {
                            $data[] = $excluded.$scales[$grade_grade->finalgrade-1];
                        }
                    }
                } else {
                    // normal grade, or text, just display
                    $data[] = $excluded.$this->get_grade_clean($grade_grade->finalgrade);
                }

                /// prints percentage

                if ($grade_item->gradetype == GRADE_TYPE_VALUE) {
                    // processing numeric grade
                    if ($grade_grade->finalgrade) {
                        $percentage = $this->get_grade_clean(($grade_grade->finalgrade / $grade_item->grademax) * 100).'%';
                    } else {
                        $percentage = '-';
                    }

                } else if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
                    // processing scale grade
                    $scale = get_record('scale', 'id', $grade_item->scaleid);
                    $scalevals = explode(",", $scale->scale);
                    $percentage = $this->get_grade_clean(($grade_grade->finalgrade) / count($scalevals) * 100).'%';

                } else {
                    // text grade
                    $percentage = '-';
                }

                $data[] = $percentage;

                /// prints rank
                if ($grade_grade->finalgrade) {
                    /// find the number of users with a higher grade
                    $sql = "SELECT COUNT(DISTINCT(userid))
                            FROM {$CFG->prefix}grade_grades
                            WHERE finalgrade > $grade_grade->finalgrade
                            AND itemid = $grade_item->id";
                    $rank = count_records_sql($sql) + 1;

                    $data[] = "$rank/$numusers";
                } else {
                    // no grade, no rank
                    $data[] = "-";
                }

                /// prints notes
                if (!empty($grade_text->feedback)) {
                    $data[] = $grade_text->feedback;
                } else {
                    $data[] = '&nbsp;';
                }
                $this->table->add_data($data);
            }

            return true;
        } else {
            notify(get_string('nogradeitem', 'grades'));
            return false;
        }
    }

    /**
     * Prints or returns the HTML from the flexitable.
     * @param bool $return Whether or not to return the data instead of printing it directly.
     * @return string
     */
    function print_table($return=false) {
        ob_start();
        $this->table->print_html();
        $html = ob_get_clean();
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * @var array $data
     * @return bool Success or Failure (array of errors).
     */
    function process_data($data) {
    }

}
?>
