<?php //$Id$

require_once $CFG->libdir.'/gradelib.php';

/**
 * Print grading plugin selection popup form.
 *
 * @param int $courseid id of course
 * @param string $active_type type of plugin on current page - import, export, report or edit
 * @param string $active_plugin active plugin type - grader, user, cvs, ...
 * @param boolean $return return as string
 * @return nothing or string if $return true
 */
function print_grade_plugin_selector($courseid, $active_type, $active_plugin, $return=false) {
    global $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $courseid);

    $menu = array();

    $active = '';

/// report plugins with its special structure
    if ($reports = get_list_of_plugins('grade/report', 'CVS')) {         // Get all installed reports
        foreach ($reports as $key => $plugin) {                      // Remove ones we can't see
            if (!has_capability('gradereport/'.$plugin.':view', $context)) {
                unset($reports[$key]);
            }
        }
    }
    $reportnames = array();
    if (!empty($reports)) {
        foreach ($reports as $plugin) {
            $url = 'report/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'report' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $reportnames[$url] = get_string('modulename', 'gradereport_'.$plugin, NULL, $CFG->dirroot.'/grade/report/'.$plugin.'lang/');
        }
        asort($reportnames);
    }
    if (!empty($reportnames)) {
        $menu['reportgroup']='--'.get_string('reportplugins', 'grades');
        $menu = $menu+$reportnames;
    }

/// standard import plugins
    if ($imports = get_list_of_plugins('grade/import', 'CVS')) {         // Get all installed import plugins
        foreach ($imports as $key => $plugin) {                      // Remove ones we can't see
            if (!has_capability('gradeimport/'.$plugin.':view', $context)) {
                unset($imports[$key]);
            }
        }
    }
    $importnames = array();
    if (!empty($imports)) {
        foreach ($imports as $plugin) {
            $url = 'import/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'import' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $importnames[$url] = get_string('modulename', 'gradeimport_'.$plugin, NULL, $CFG->dirroot.'/grade/import/'.$plugin.'lang/');
        }
        asort($importnames);
    }
    if (!empty($importnames)) {
        $menu['importgroup']='--'.get_string('importplugins', 'grades');
        $menu = $menu+$importnames;
    }

/// standard export plugins
    if ($exports = get_list_of_plugins('grade/export', 'CVS')) {         // Get all installed export plugins
        foreach ($exports as $key => $plugin) {                      // Remove ones we can't see
            if (!has_capability('gradeexport/'.$plugin.':view', $context)) {
                unset($exports[$key]);
            }
        }
    }
    $exportnames = array();
    if (!empty($exports)) {
        foreach ($exports as $plugin) {
            $url = 'export/'.$plugin.'/index.php?id='.$courseid;
            if ($active_type == 'export' and $active_plugin == $plugin ) {
                $active = $url;
            }
            $exportnames[$url] = get_string('modulename', 'gradeexport_'.$plugin, NULL, $CFG->dirroot.'/grade/export/'.$plugin.'lang/');
        }
        asort($exportnames);
    }
    if (!empty($exportnames)) {
        $menu['exportgroup']='--'.get_string('exportplugins', 'grades');
        $menu = $menu+$exportnames;
    }

/// editing scripts - not real plugins
    if (has_capability('moodle/grade:manage', $context)
      or has_capability('moodle/course:managescales', $context)
      or has_capability('moodle/course:update', $context)) {
        $menu['edit']='--'.get_string('edit');

        if (has_capability('moodle/grade:manage', $context)) {
            $url = 'edit/tree/index.php?id='.$courseid;
            if ($active_type == 'edit' and $active_plugin == 'tree' ) {
                $active = $url;
            }
            $menu[$url] = get_string('edittree', 'grades');
        }

        if (has_capability('moodle/course:managescales', $context)) {
            $url = 'edit/scale/index.php?id='.$courseid;
            if ($active_type == 'edit' and $active_plugin == 'scale' ) {
                $active = $url;
            }
            $menu[$url] = get_string('scales');
        }

        if (!empty($CFG->enableoutcomes) && (has_capability('moodle/grade:manage', $context) or
                                             has_capability('moodle/course:update', $context))) {
            if (has_capability('moodle/course:update', $context)) {  // Default to course assignment
                $url = 'edit/outcome/course.php?id='.$courseid;
            } else {
                $url = 'edit/outcome/index.php?id='.$courseid;
            }
            if ($active_type == 'edit' and $active_plugin == 'outcome' ) {
                $active = $url;
            }
            $menu[$url] = get_string('outcomes', 'grades');
        }
    }

/// finally print/return the popup form
    return popup_form($CFG->wwwroot.'/grade/', $menu, 'choosepluginreport', $active, 'choose', '', '', $return, 'self', get_string('view'));
}

/**
 * Utility class used for return tracking when using edit and other forms in grade plugins
 */
class grade_plugin_return {
    var $type;
    var $plugin;
    var $courseid;
    var $userid;
    var $page;

    /**
     * Constructor
     * @param array $params - associative array with return parameters, if null parameter are taken from _GET or _POST
     */
    function grade_plugin_return ($params=null) {
        if (empty($params)) {
            $this->type     = optional_param('gpr_type', null, PARAM_SAFEDIR);
            $this->plugin   = optional_param('gpr_plugin', null, PARAM_SAFEDIR);
            $this->courseid = optional_param('gpr_courseid', null, PARAM_INT);
            $this->userid   = optional_param('gpr_userid', null, PARAM_INT);
            $this->page     = optional_param('gpr_page', null, PARAM_INT);

        } else {
            foreach ($params as $key=>$value) {
                if (array_key_exists($key, $this)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * Returns return parameters as options array suitable for buttons.
     * @return array options
     */
    function get_options() {
        if (empty($this->type)) {
            return array();
        }

        $params = array();

        if (!empty($this->plugin)) {
            $params['plugin'] = $this->plugin;
        }

        if (!empty($this->courseid)) {
            $params['id'] = $this->courseid;
        }

        if (!empty($this->userid)) {
            $params['userid'] = $this->userid;
        }

        if (!empty($this->page)) {
            $params['page'] = $this->page;
        }

        return $params;
    }

    /**
     * Returns return url
     * @param string $default default url when params not set
     * @return string url
     */
    function get_return_url($default, $extras=null) {
        global $CFG;

        if (empty($this->type) or empty($this->plugin)) {
            return $default;
        }

        $url = $CFG->wwwroot.'/grade/'.$this->type.'/'.$this->plugin.'/index.php';
        $glue = '?';

        if (!empty($this->courseid)) {
            $url .= $glue.'id='.$this->courseid;
            $glue = '&amp;';
        }

        if (!empty($this->userid)) {
            $url .= $glue.'userid='.$this->userid;
            $glue = '&amp;';
        }

        if (!empty($this->page)) {
            $url .= $glue.'page='.$this->page;
            $glue = '&amp;';
        }

        if (!empty($extras)) {
            foreach($extras as $key=>$value) {
                $url .= $glue.$key.'='.$value;
                $glue = '&amp;';
            }
        }

        return $url;
    }

    /**
     * Returns string with hidden return tracking form elements.
     * @return string
     */
    function get_form_fields() {
        if (empty($this->type)) {
            return '';
        }

        $result  = '<input type="hidden" name="gpr_type" value="'.$this->type.'" />';

        if (!empty($this->plugin)) {
            $result .= '<input type="hidden" name="gpr_plugin" value="'.$this->plugin.'" />';
        }

        if (!empty($this->courseid)) {
            $result .= '<input type="hidden" name="gpr_courseid" value="'.$this->courseid.'" />';
        }

        if (!empty($this->userid)) {
            $result .= '<input type="hidden" name="gpr_userid" value="'.$this->userid.'" />';
        }

        if (!empty($this->page)) {
            $result .= '<input type="hidden" name="gpr_page" value="'.$this->page.'" />';
        }
    }

    /**
     * Add hidden elements into mform
     * @param object $mform moodle form object
     * @return void
     */
    function add_mform_elements(&$mform) {
        if (empty($this->type)) {
            return;
        }

        $mform->addElement('hidden', 'gpr_type', $this->type);
        $mform->setType('gpr_type', PARAM_SAFEDIR);

        if (!empty($this->plugin)) {
            $mform->addElement('hidden', 'gpr_plugin', $this->plugin);
            $mform->setType('gpr_plugin', PARAM_SAFEDIR);
        }

        if (!empty($this->courseid)) {
            $mform->addElement('hidden', 'gpr_courseid', $this->courseid);
            $mform->setType('gpr_courseid', PARAM_INT);
        }

        if (!empty($this->userid)) {
            $mform->addElement('hidden', 'gpr_userid', $this->userid);
            $mform->setType('gpr_userid', PARAM_INT);
        }

        if (!empty($this->page)) {
            $mform->addElement('hidden', 'gpr_page', $this->page);
            $mform->setType('gpr_page', PARAM_INT);
        }
    }

    /**
     * Add return tracking params into url
     * @param string $url
     * @return string $url with erturn tracking params
     */
    function add_url_params($url) {
        if (empty($this->type)) {
            return $url;
        }

        if (strpos($url, '?') === false) {
            $url .= '?gpr_type='.$this->type;
        } else {
            $url .= '&amp;gpr_type='.$this->type;
        }

        if (!empty($this->plugin)) {
            $url .= '&amp;gpr_plugin='.$this->plugin;
        }

        if (!empty($this->courseid)) {
            $url .= '&amp;gpr_courseid='.$this->courseid;
        }

        if (!empty($this->userid)) {
            $url .= '&amp;gpr_userid='.$this->userid;
        }

        if (!empty($this->page)) {
            $url .= '&amp;gpr_page='.$this->page;
        }

        return $url;
    }
}

/**
 * Function central to gradebook for building and printing the navigation (breadcrumb trail).
 * @param string $path The path of the calling script (using __FILE__?)
 * @param string $pagename The language string to use as the last part of the navigation (non-link)
 * @param mixed  $id Either a plain integer (assuming the key is 'id') or an array of keys and values (e.g courseid => $courseid, itemid...)
 * @return string
 */
function grade_build_nav($path, $pagename=null, $id=null) {
    global $CFG, $COURSE;

    $strgrades = get_string('grades', 'grades');

    // Parse the path and build navlinks from its elements
    $dirroot_length = strlen($CFG->dirroot) + 1; // Add 1 for the first slash
    $path = substr($path, $dirroot_length);
    $path = str_replace('\\', '/', $path);

    $path_elements = explode('/', $path);

    $path_elements_count = count($path_elements);

    $last_element = $path_elements[$path_elements_count-1]; // Should be the filename (including extension)

    // First link is always 'grade'
    $navlinks = array();
    $navlinks[] = array('name' => $strgrades,
                        'link' => $CFG->wwwroot.'/grade/index.php?id='.$COURSE->id,
                        'type' => 'misc');

    $link = '';
    $numberofelements = 3;

    // Prepare URL params string
    $id_string = '?';
    if (!is_null($id)) {
        if (is_array($id)) {
            foreach ($id as $idkey => $idvalue) {
                $id_string .= "$idkey=$idvalue&amp;";
            }
        } else {
            $id_string .= "id=$id";
        }
    }

    $navlink4 = null;

    // Second level links
    switch ($path_elements[1]) {
        case 'edit': // No link
            if ($path_elements[3] != 'index.php') {
                $numberofelements = 4;
            }
            break;
        case 'import': // No link
            break;
        case 'export': // No link
            break;
        case 'report':
            // $id is required for this link. Do not print it if $id isn't given
            if (!is_null($id)) {
                $link = $CFG->wwwroot . '/grade/report/index.php' . $id_string;
            }

            if ($path_elements[2] == 'grader') {
                $numberofelements = 4;
            }
            break;

        default:
            // If this element isn't among the ones already listed above, it isn't supported, throw an error.
            debugging("grade_build_nav() doesn't support ". $path_elements[1] . " as the second path element after 'grade'.");
            return false;
    }

    $navlinks[] = array('name' => get_string($path_elements[1], 'grades'), 'link' => $link, 'type' => 'misc');

    // Third level links
    if (empty($pagename)) {
        $pagename = get_string($path_elements[2], 'grades');
    }

    switch ($numberofelements) {
        case 3:
            $navlinks[] = array('name' => $pagename, 'link' => $link, 'type' => 'misc');
            break;
        case 4:

            if ($path_elements[2] == 'grader' AND $path_elements[3] != 'index.php') {
                $navlinks[] = array('name' => get_string('modulename', 'gradereport_grader'),
                                    'link' => "$CFG->wwwroot/grade/report/grader/index.php$id_string",
                                    'type' => 'misc');
            }
            $navlinks[] = array('name' => $pagename, 'link' => '', 'type' => 'misc');
            break;
    }
    $navigation = build_navigation($navlinks);

    return $navigation;
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

/**
 * This class represents a complete tree of categories, grade_items and final grades,
 * organises as an array primarily, but which can also be converted to other formats.
 * It has simple method calls with complex implementations, allowing for easy insertion,
 * deletion and moving of items and categories within the tree.
 */
class grade_tree {

    /**
     * The basic representation of the tree as a hierarchical, 3-tiered array.
     * @var object $top_element
     */
    var $top_element;

    /**
     * A string of GET URL variables, namely courseid and sesskey, used in most URLs built by this class.
     * @var string $commonvars
     */
    var $commonvars;

    /**
     * 2D array of grade items and categories
     */
    var $levels;

    /**
     * Course context
     */
    var $context;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated.
     * and renumbering.
     * @param int $courseid
     * @param boolean $fillers include fillers and colspans, make the levels var "rectangular"
     * @param boolean $category_grade_last category grade item is the last child
     * @param array $collapsed array of collapsed categories
     */
    function grade_tree($courseid, $fillers=true, $category_grade_last=false, $collapsed=null, $nooutcomes=false) {
        global $USER, $CFG;

        $this->courseid   = $courseid;
        $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;id=$this->courseid";
        $this->levels     = array();
        $this->context    = get_context_instance(CONTEXT_COURSE, $courseid);

        // get course grade tree
        $this->top_element = grade_category::fetch_course_tree($courseid, true);

        // collapse the categories if requested
        if (!empty($collapsed)) {
            grade_tree::category_collapse($this->top_element, $collapsed);
        }

        // no otucomes if requested
        if (!empty($nooutcomes)) {
            grade_tree::no_outcomes($this->top_element);
        }

        // move category item to last position in category
        if ($category_grade_last) {
            grade_tree::category_grade_last($this->top_element);
        }

        if ($fillers) {
            // inject fake categories == fillers
            grade_tree::inject_fillers($this->top_element, 0);
            // add colspans to categories and fillers
            grade_tree::inject_colspans($this->top_element);
        }

        grade_tree::fill_levels($this->levels, $this->top_element, 0);
    }

    /**
     * Static recursive helper - removes items from collapsed categories
     * @static
     * @param array $element The seed of the recursion
     * @param array $collapsed array of collapsed categories
     * @return void
     */
    function category_collapse(&$element, $collapsed) {
        if ($element['type'] != 'category') {
            return;
        }
        if (empty($element['children']) or count($element['children']) < 2) {
            return;
        }

        if (in_array($element['object']->id, $collapsed['aggregatesonly'])) {
            $category_item = reset($element['children']); //keep only category item
            $element['children'] = array(key($element['children'])=>$category_item);

        } else {
            if (in_array($element['object']->id, $collapsed['gradesonly'])) { // Remove category item
                reset($element['children']);
                $first_key = key($element['children']);
                unset($element['children'][$first_key]);
            }
            foreach ($element['children'] as $sortorder=>$child) { // Recurse through the element's children
                grade_tree::category_collapse($element['children'][$sortorder], $collapsed);
            }
        }
    }

    /**
     * Static recursive helper - removes all outcomes
     * @static
     * @param array $element The seed of the recursion
     * @return void
     */
    function no_outcomes(&$element) {
        if ($element['type'] != 'category') {
            return;
        }
        foreach ($element['children'] as $sortorder=>$child) {
            if ($element['children'][$sortorder]['type'] == 'item'
              and $element['children'][$sortorder]['object']->is_outcome_item()) {
                unset($element['children'][$sortorder]);

            } else if ($element['children'][$sortorder]['type'] == 'category') {
                grade_tree::no_outcomes($element['children'][$sortorder]);
            }
        }
    }

    /**
     * Static recursive helper - makes the grade_item for category the last children
     * @static
     * @param array $element The seed of the recursion
     * @return void
     */
    function category_grade_last(&$element) {
        if (empty($element['children'])) {
            return;
        }
        if (count($element['children']) < 2) {
            return;
        }
        $category_item = reset($element['children']);
        $order = key($element['children']);
        unset($element['children'][$order]);
        $element['children'][$order] =& $category_item;
        foreach ($element['children'] as $sortorder=>$child) {
            grade_tree::category_grade_last($element['children'][$sortorder]);
        }
    }

    /**
     * Static recursive helper - fills the levels array, useful when accessing tree elements of one level
     * @static
     * @param int $levels
     * @param array $element The seed of the recursion
     * @param int $depth
     * @return void
     */
    function fill_levels(&$levels, &$element, $depth) {
        if (!array_key_exists($depth, $levels)) {
            $levels[$depth] = array();
        }

        // prepare unique identifier
        if ($element['type'] == 'category') {
            $element['eid'] = 'c'.$element['object']->id;
        } else if (in_array($element['type'], array('item', 'courseitem', 'categoryitem'))) {
            $element['eid'] = 'i'.$element['object']->id;
        }

        $levels[$depth][] =& $element;
        $depth++;
        if (empty($element['children'])) {
            return;
        }
        $prev = 0;
        foreach ($element['children'] as $sortorder=>$child) {
            grade_tree::fill_levels($levels, $element['children'][$sortorder], $depth);
            $element['children'][$sortorder]['prev'] = $prev;
            $element['children'][$sortorder]['next'] = 0;
            if ($prev) {
                $element['children'][$prev]['next'] = $sortorder;
            }
            $prev = $sortorder;
        }
    }

    /**
     * Static recursive helper - makes full tree (all leafes are at the same level)
     */
    function inject_fillers(&$element, $depth) {
        $depth++;

        if (empty($element['children'])) {
            return $depth;
        }
        $chdepths = array();
        $chids = array_keys($element['children']);
        $last_child  = end($chids);
        $first_child = reset($chids);

        foreach ($chids as $chid) {
            $chdepths[$chid] = grade_tree::inject_fillers($element['children'][$chid], $depth);
        }
        arsort($chdepths);

        $maxdepth = reset($chdepths);
        foreach ($chdepths as $chid=>$chd) {
            if ($chd == $maxdepth) {
                continue;
            }
            for ($i=0; $i < $maxdepth-$chd; $i++) {
                if ($chid == $first_child) {
                    $type = 'fillerfirst';
                } else if ($chid == $last_child) {
                    $type = 'fillerlast';
                } else {
                    $type = 'filler';
                }
                $oldchild =& $element['children'][$chid];
                $element['children'][$chid] = array('object'=>'filler', 'type'=>$type, 'eid'=>'', 'depth'=>$element['object']->depth,'children'=>array($oldchild));
            }
        }

        return $maxdepth;
    }

    /**
     * Static recursive helper - add colspan information into categories
     */
    function inject_colspans(&$element) {
        if (empty($element['children'])) {
            return 1;
        }
        $count = 0;
        foreach ($element['children'] as $key=>$child) {
            $count += grade_tree::inject_colspans($element['children'][$key]);
        }
        $element['colspan'] = $count;
        return $count;
    }

    /**
     * Parses the array in search of a given eid and returns a element object with
     * information about the element it has found.
     * @param int $eid
     * @return object element
     */
    function locate_element($eid) {
        // it is a grade - construct a new object
        if (strpos($eid, 'n') === 0) {
            if (!preg_match('/n(\d+)u(\d+)/', $eid, $matches)) {
                return null;
            }

            $itemid = $matches[1];
            $userid = $matches[2];

            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$itemid)) {
                return null;
            }

            // $gradea->id may be null - means does not exist yet
            $grade = new grade_grade(array('itemid'=>$itemid, 'userid'=>$userid));

            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'n'.$itemid.'u'.$userid,'object'=>$grade, 'type'=>'grade');

        } else if (strpos($eid, 'g') === 0) {
            $id = (int)substr($eid, 1);
            if (!$grade = grade_grade::fetch(array('id'=>$id))) {
                return null;
            }
            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$grade->itemid)) {
                return null;
            }
            $grade->grade_item =& $item_el['object']; // this may speedup grade_grade methods!
            return array('eid'=>'g'.$id,'object'=>$grade, 'type'=>'grade');
        }

        // it is a category or item
        foreach ($this->levels as $row) {
            foreach ($row as $element) {
                if ($element['type'] == 'filler') {
                    continue;
                }
                if ($element['eid'] == $eid) {
                    return $element;
                }
            }
        }

        return null;
    }

    /**
     * Returns the grade eid - the grade may not exist yet.
     * @param $grade_grade object
     * @return string eid
     */
    function get_grade_eid($grade_grade) {
        if (empty($grade_grade->id)) {
            return 'n'.$grade_grade->itemid.'u'.$grade_grade->userid;
        } else {
            return 'g'.$grade_grade->id;
        }
    }

    /**
     * Return edit icon for give element
     * @param object $element
     * @return string
     */
    function get_edit_icon($element, $gpr) {
        global $CFG;

        if (!has_capability('moodle/grade:manage', $this->context)) {
            if ($element['type'] == 'grade' and has_capability('moodle/grade:override', $this->context)) {
                // oki - let them override grade
            } else {
                return '';
            }
        }

        static $stredit = null;
        if (is_null($stredit)) {
            $stredit = get_string('edit');
        }

        $object = $element['object'];
        $overlib = '';

        switch ($element['type']) {
            case 'item':
            case 'categoryitem':
            case 'courseitem':
                if (empty($object->outcomeid) || empty($CFG->enableoutcomes)) {
                    $url = $CFG->wwwroot.'/grade/edit/tree/item.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                } else {
                    $url = $CFG->wwwroot.'/grade/edit/tree/outcomeitem.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                }
                $url = $gpr->add_url_params($url);
                break;

            case 'category':
                $url = $CFG->wwwroot.'/grade/edit/tree/category.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                $url = $gpr->add_url_params($url);
                break;

            case 'grade':
                if (empty($object->id)) {
                    $url = $CFG->wwwroot.'/grade/edit/tree/grade.php?courseid='.$this->courseid.'&amp;itemid='.$object->itemid.'&amp;userid='.$object->userid;
                } else {
                    $url = $CFG->wwwroot.'/grade/edit/tree/grade.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                }
                $url = $gpr->add_url_params($url);
                if (!empty($object->feedback)) {
                    $feedback = format_text($object->feedback, $object->feedbackformat);
                    $function = "return overlib('".s(ltrim($object->feedback)."', FULLHTML);");
                    $overlib = 'onmouseover="'.$function.'" onmouseout="return nd();"';
                }
                break;

            default:
                $url = null;
        }

        if ($url) {
            return '<a href="'.$url.'"><img '.$overlib.' src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$stredit.'" title="'.$stredit.'"/></a>';

        } else {
            return '';
        }
    }

    /**
     * Return hiding icon for give element
     * @param object $element
     * @return string
     */
    function get_hiding_icon($element, $gpr) {
        global $CFG;

        if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:hide', $this->context)) {
            return '';
        }

        static $strshow = null;
        static $strhide   = null;
        if (is_null($strshow)) {
            $strshow = get_string('show');
            $strhide = get_string('hide');
        }

        if ($element['object']->is_hidden()) {
            $icon = 'show';
            $tooltip = '';

            if ($element['type'] != 'category' and $element['object']->get_hidden() > 1) { // Change the icon and add a tooltip showing the date
                $icon = 'hiddenuntil';
                $tooltip = get_string('hiddenuntildate', 'grades', userdate($element['object']->get_hidden()));
            }

            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=show&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img title="' . $tooltip . '" src="'.$CFG->pixpath.'/t/' . $icon . '.gif" class="iconsmall" alt="'
                     . $strshow.'" title="'.$strshow.'"/></a>';

        } else {
            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=hide&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/hide.gif" class="iconsmall" alt="'.$strhide.'" title="'.$strhide.'"/></a>';
        }
        return $action;
    }

    /**
     * Return locking icon for give element
     * @param object $element
     * @return string
     */
    function get_locking_icon($element, $gpr) {
        global $CFG;

        static $strunlock = null;
        static $strlock   = null;
        if (is_null($strunlock)) {
            $strunlock = get_string('unlock', 'grades');
            $strlock   = get_string('lock', 'grades');
        }

        if ($element['object']->is_locked()) {
            $icon = 'unlock';
            $tooltip = '';

            if ($element['type'] != 'category' and $element['object']->get_locktime() > 1) { // Change the icon and add a tooltip showing the date
                $icon = 'locktime';
                $tooltip = get_string('locktimedate', 'grades', userdate($element['object']->get_locktime()));
            }

            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:unlock', $this->context)) {
                return '';
            }
            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=unlock&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/' . $icon . '.gif" title="' . $tooltip
                     . '" class="iconsmall" alt="'.$strunlock
                     . '" title="'.$strunlock.'"/></a>';

        } else {
            if (!has_capability('moodle/grade:manage', $this->context) and !has_capability('moodle/grade:lock', $this->context)) {
                return '';
            }
            $url     = $CFG->wwwroot.'/grade/edit/tree/action.php?id='.$this->courseid.'&amp;action=lock&amp;sesskey='.sesskey()
                     . '&amp;eid='.$element['eid'];
            $url     = $gpr->add_url_params($url);
            $action  = '<a href="'.$url.'"><img src="'.$CFG->pixpath.'/t/lock.gif" class="iconsmall" alt="'.$strlock.'" title="'
                     . $strlock.'"/></a>';
        }
        return $action;
    }

    /**
     * Return calculation icon for given element
     * @param object $element
     * @return string
     */
    function get_calculation_icon($element, $gpr) {
        global $CFG;
        if (!has_capability('moodle/grade:manage', $this->context)) {
            return '';
        }

        $calculation_icon = '';

        $type   = $element['type'];
        $object = $element['object'];

        if ($type == 'item' or $type == 'courseitem' or $type == 'categoryitem') {
            $streditcalculation = get_string('editcalculation', 'grades');

            // show calculation icon only when calculation possible
            if ((!$object->is_normal_item() or $object->is_outcome_item())
              and ($object->gradetype == GRADE_TYPE_SCALE or $object->gradetype == GRADE_TYPE_VALUE)) {
                $url = $CFG->wwwroot.'/grade/edit/tree/calculation.php?courseid='.$this->courseid.'&amp;id='.$object->id;
                $url = $gpr->add_url_params($url);
                $calculation_icon = '<a href="'. $url.'"><img src="'.$CFG->pixpath.'/t/calc.gif" class="iconsmall" alt="'
                                       . $streditcalculation.'" title="'.$streditcalculation.'" /></a>'. "\n";
            }
        }

        return $calculation_icon;
    }
}

?>
