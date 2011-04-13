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
 * This is the main renderer for the enrol section.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * This is the core renderer
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_enrol_renderer extends plugin_renderer_base {

    /**
     * Renders a course enrolment table
     *
     * @param course_enrolment_table $table
     * @return string
     */
    protected function render_course_enrolment_users_table(course_enrolment_users_table $table) {

        $table->initialise_javascript();

        $content = '';
        $enrolmentselector = $table->get_enrolment_selector();
        if ($enrolmentselector) {
            $content .= $this->output->render($enrolmentselector);
        }
        $cohortenroller = $table->get_cohort_enrolment_control();
        if ($cohortenroller) {
            $content .= $this->output->render($cohortenroller);
        }
        $content  .= $this->output->render($table->get_enrolment_type_filter());
        $content .= $this->output->render($table->get_paging_bar());
        $content .= html_writer::table($table);
        $content .= $this->output->render($table->get_paging_bar());
        $enrolmentselector = $table->get_enrolment_selector();
        if ($enrolmentselector) {
            $content .= $this->output->render($enrolmentselector);
        }
        $cohortenroller = $table->get_cohort_enrolment_control();
        if ($cohortenroller) {
            $content .= $this->output->render($cohortenroller);
        }
        return $content;
    }

    /**
     * Renders a course enrolment table
     *
     * @param course_enrolment_table $table
     * @return string
     */
    protected function render_course_enrolment_other_users_table(course_enrolment_other_users_table $table) {

        $table->initialise_javascript();

        $content = '';
        $searchbutton = $table->get_user_search_button();
        if ($searchbutton) {
            $content .= $this->output->render($searchbutton);
        }
        $content .= html_writer::tag('div', get_string('otheruserdesc', 'enrol'), array('class'=>'otherusersdesc'));
        $content .= $this->output->render($table->get_paging_bar());
        $content .= html_writer::table($table);
        $content .= $this->output->render($table->get_paging_bar());
        $searchbutton = $table->get_user_search_button();
        if ($searchbutton) {
            $content .= $this->output->render($searchbutton);
        }
        return $content;
    }

    /**
     * Generates HTML to display the users roles and any available actions
     *
     * @param int $userid
     * @param array $roles
     * @param array $assignableroles
     * @param moodle_url $pageurl
     * @return string
     */
    public function user_roles_and_actions($userid, $roles, $assignableroles, $canassign, $pageurl) {
        $iconenroladd    = $this->output->pix_url('t/enroladd');
        $iconenrolremove = $this->output->pix_url('t/delete');

        // get list of roles
        $rolesoutput = '';
        foreach ($roles as $roleid=>$role) {
            if ($canassign && !$role['unchangeable']) {
                $strunassign = get_string('unassignarole', 'role', $role['text']);
                $icon = html_writer::empty_tag('img', array('alt'=>$strunassign, 'src'=>$iconenrolremove));
                $url = new moodle_url($pageurl, array('action'=>'unassign', 'role'=>$roleid, 'user'=>$userid));
                $rolesoutput .= html_writer::tag('div', $role['text'] . html_writer::link($url, $icon, array('class'=>'unassignrolelink', 'rel'=>$roleid, 'title'=>$strunassign)), array('class'=>'role role_'.$roleid));
            } else {
                $rolesoutput .= html_writer::tag('div', $role['text'], array('class'=>'role unchangeable', 'rel'=>$roleid));
            }
        }
        $output = '';
        if (!empty($assignableroles) && $canassign) {
            $roleids = array_keys($roles);
            $hasallroles = true;
            foreach (array_keys($assignableroles) as $key) {
                if (!in_array($key, $roleids)) {
                    $hasallroles = false;
                    break;
                }
            }
            if (!$hasallroles) {
                $url = new moodle_url($pageurl, array('action'=>'assign', 'user'=>$userid));
                $icon = html_writer::empty_tag('img', array('alt'=>get_string('assignroles', 'role'), 'src'=>$iconenroladd));
                $output = html_writer::tag('div', html_writer::link($url, $icon, array('class'=>'assignrolelink', 'title'=>get_string('assignroles', 'role'))), array('class'=>'addrole'));
            }
        }
        $output .= html_writer::tag('div', $rolesoutput, array('class'=>'roles'));
        return $output;
    }

    /**
     * Generates the HTML to view the users groups and available group actions
     *
     * @param int $userid
     * @param array $groups
     * @param array $allgroups
     * @param bool $canmanagegroups
     * @param moodle_url $pageurl
     * @return string
     */
    public function user_groups_and_actions($userid, $groups, $allgroups, $canmanagegroups, $pageurl) {
        $iconenroladd    = $this->output->pix_url('t/enroladd');
        $iconenrolremove = $this->output->pix_url('t/delete');
        $straddgroup = get_string('addgroup', 'group');

        $groupoutput = '';
        foreach($groups as $groupid=>$name) {
            if ($canmanagegroups) {
                $icon = html_writer::empty_tag('img', array('alt'=>get_string('removefromgroup', 'group', $name), 'src'=>$iconenrolremove));
                $url = new moodle_url($pageurl, array('action'=>'removemember', 'group'=>$groupid, 'user'=>$userid));
                $groupoutput .= html_writer::tag('div', $name . html_writer::link($url, $icon), array('class'=>'group', 'rel'=>$groupid));
            } else {
                $groupoutput .= html_writer::tag('div', $name, array('class'=>'group', 'rel'=>$groupid));
            }
        }
        $groupoutput = html_writer::tag('div', $groupoutput, array('class'=>'groups'));
        if ($canmanagegroups && (count($groups) < count($allgroups))) {
            $icon = html_writer::empty_tag('img', array('alt'=>$straddgroup, 'src'=>$iconenroladd));
            $url = new moodle_url($pageurl, array('action'=>'addmember', 'user'=>$userid));
            $groupoutput .= html_writer::tag('div', html_writer::link($url, $icon), array('class'=>'addgroup'));
        }
        return $groupoutput;
    }

    /**
     * Generates the HTML for the given enrolments + available actions
     *
     * @param int $userid
     * @param array $enrolments
     * @param moodle_url $pageurl
     * @return string
     */
    public function user_enrolments_and_actions($userid, $enrolments, $pageurl) {
        $iconedit        = $this->output->pix_url('t/edit');
        $iconenrolremove = $this->output->pix_url('t/delete');
        $strunenrol = get_string('unenrol', 'enrol');
        $stredit = get_string('edit');

        $output = '';
        foreach ($enrolments as $ueid=>$enrolment) {
            $enrolmentoutput = $enrolment['text'].' '.$enrolment['period'];
            if ($enrolment['dimmed']) {
                $enrolmentoutput = html_writer::tag('span', $enrolmentoutput, array('class'=>'dimmed_text'));
            }
            if ($enrolment['canunenrol']) {
                $icon = html_writer::empty_tag('img', array('alt'=>$strunenrol, 'src'=>$iconenrolremove));
                $url = new moodle_url($pageurl, array('action'=>'unenrol', 'ue'=>$ueid));
                $enrolmentoutput .= html_writer::link($url, $icon, array('class'=>'unenrollink', 'rel'=>$ueid));
            }
            if ($enrolment['canmanage']) {
                $icon = html_writer::empty_tag('img', array('alt'=>$stredit, 'src'=>$iconedit));
                $url = new moodle_url($url, array('action'=>'edit', 'ue'=>$ueid));
                $enrolmentoutput .= html_writer::link($url, $icon, array('class'=>'editenrollink', 'rel'=>$ueid));
            }
            $output .= html_writer::tag('div', $enrolmentoutput, array('class'=>'enrolment'));
        }
        return $output;
    }

}

/**
 * Main course enrolment table
 *
 * This table is used to display the enrolment information for a course.
 * It requires that a course enrolment manager be provided during constuct with
 * provides all of the information for the table.
 * The control then produces the table, the paging, and the associated JS actions
 * for the page.
 *
 * @package    core
 * @subpackage enrol
 * @copyright  2010 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enrolment_table extends html_table implements renderable {

    /**
     * The get/post variable that is used to identify the page.
     * Default: page
     */
    const PAGEVAR = 'page';

    /**
     * The get/post variable to is used to identify the number of items to display
     * per page.
     * Default: perpage
     */
    const PERPAGEVAR = 'perpage';

    /**
     * The get/post variable that is used to identify the sort field for the table.
     * Default: sort
     */
    const SORTVAR = 'sort';

    /**
     * The get/post variable that is used to identify the sort direction for the table.
     * Default: dir
     */
    const SORTDIRECTIONVAR = 'dir';

    /**
     * The default number of items per page.
     * Default: 100
     */
    const DEFAULTPERPAGE = 100;

    /**
     * The default sort, options are course_enrolment_table::$sortablefields
     * Default: lastname
     */
    const DEFAULTSORT = 'lastname';

    /**
     * The default direction
     * Default: ASC
     */
    const DEFAULTSORTDIRECTION = 'ASC';

    /**
     * The current page, starting from 0
     * @var int
     */
    public $page = 0;

    /**
     * The total number of pages
     * @var int
     */
    public $pages = 0;

    /**
     * The number of items to display per page
     * @var int
     */
    public $perpage = 0;

    /**
     * The URL of the page for this table
     * @var moodle_page
     */
    public $moodlepage;

    /**
     * The sort field for this table, should be one of course_enrolment_table::$sortablefields
     * @var string
     */
    public $sort;

    /**
     * The sort direction, either ASC or DESC
     * @var string
     */
    public $sortdirection;

    /**
     * The course manager this table is displaying for
     * @var course_enrolment_manager
     */
    protected $manager;

    /**
     * The paging bar that controls the paging for this table
     * @var paging_bar
     */
    protected $pagingbar = null;

    /**
     * The total number of users enrolled in the course
     * @var int
     */
    protected $totalusers = null;

    /**
     * The users enrolled in this course
     * @var array
     */
    protected $users = null;

    /**
     * The fields for this table
     * @var array
     */
    protected $fields = array();

    /**
     * An array of sortable fields
     * @static
     * @var array
     */
    protected static $sortablefields = array('firstname', 'lastname', 'email');

    /**
     * Constructs the table
     *
     * @param course_enrolment_manager $manager
     */
    public function __construct(course_enrolment_manager $manager, moodle_page $moodlepage) {

        $this->manager        = $manager;
        $this->moodlepage     = $moodlepage;

        $this->page           = optional_param(self::PAGEVAR, 0, PARAM_INT);
        $this->perpage        = optional_param(self::PERPAGEVAR, self::DEFAULTPERPAGE, PARAM_INT);
        $this->sort           = optional_param(self::SORTVAR, self::DEFAULTSORT, PARAM_ALPHA);
        $this->sortdirection  = optional_param(self::SORTDIRECTIONVAR, self::DEFAULTSORTDIRECTION, PARAM_ALPHA);

        $this->attributes = array('class'=>'userenrolment');
        if (!in_array($this->sort, self::$sortablefields)) {
            $this->sort = self::DEFAULTSORT;
        }
        if ($this->page < 0) {
            $this->page = 0;
        }
        if ($this->sortdirection !== 'ASC' && $this->sortdirection !== 'DESC') {
            $this->sortdirection = self::DEFAULTSORTDIRECTION;
        }

        $this->id = html_writer::random_id();
    }

    /**
     * Gets the sort direction for a given field
     *
     * @param string $field
     * @return string ASC or DESC
     */
    public function get_field_sort_direction($field) {
        if ($field == $this->sort) {
            return ($this->sortdirection == 'ASC')?'DESC':'ASC';
        }
        return self::DEFAULTSORTDIRECTION;
    }

    /**
     * Sets the fields for this table. These get added to the tables head as well.
     *
     * You can also use a multi dimensional array for this to have multiple fields
     * in a single column
     *
     * @param array $fields An array of fields to set
     * @param string $output
     */
    public function set_fields($fields, $output) {
        $this->fields = $fields;
        $this->head = array();
        $this->colclasses = array();
        $this->align = array();
        $url = $this->moodlepage->url;
        foreach ($fields as $name => $label) {
            $newlabel = '';
            if (is_array($label)) {
                $bits = array();
                foreach ($label as $n => $l) {
                    if ($l === false) {
                        continue;
                    }
                    if (!in_array($n, self::$sortablefields)) {
                        $bits[] = $l;
                    } else {
                        $link = html_writer::link(new moodle_url($url, array(self::SORTVAR=>$n)), $fields[$name][$n]);
                        if ($this->sort == $n) {
                            $link .= ' '.html_writer::link(new moodle_url($url, array(self::SORTVAR=>$n, self::SORTDIRECTIONVAR=>$this->get_field_sort_direction($n))), $this->get_direction_icon($output, $n));
                        }
                        $bits[] = html_writer::tag('span', $link, array('class'=>'subheading_'.$n));

                    }
                }
                $newlabel = join(' / ', $bits);
            } else {
                if (!in_array($name, self::$sortablefields)) {
                    $newlabel = $label;
                } else {
                    $newlabel  = html_writer::link(new moodle_url($url, array(self::SORTVAR=>$name)), $fields[$name]);
                    if ($this->sort == $name) {
                        $newlabel .= ' '.html_writer::link(new moodle_url($url, array(self::SORTVAR=>$name, self::SORTDIRECTIONVAR=>$this->get_field_sort_direction($name))), $this->get_direction_icon($output, $name));
                    }
                }
            }
            $this->head[] = $newlabel;
            $this->colclasses[] = 'field col_'.$name;
        }
    }
    /**
     * Sets the total number of users
     *
     * @param int $totalusers
     */
    public function set_total_users($totalusers) {
        $this->totalusers = $totalusers;
        $this->pages = ceil($this->totalusers / $this->perpage);
        if ($this->page > $this->pages) {
            $this->page = $this->pages;
        }
    }
    /**

     */
    /**
     * Sets the users for this table
     *
     * @param array $users
     * @return void
     */
    public function set_users(array $users) {
        $this->users = $users;
        foreach ($users as $userid=>$user) {
            $user = (array)$user;
            $row = new html_table_row();
            $row->attributes = array('class' => 'userinforow');
            $row->id = 'user_'.$userid;
            $row->cells = array();
            foreach ($this->fields as $field => $label) {
                if (is_array($label)) {
                    $bits = array();
                    foreach (array_keys($label) as $subfield) {
                        if (array_key_exists($subfield, $user)) {
                            $bits[] = html_writer::tag('div', $user[$subfield], array('class'=>'subfield subfield_'.$subfield));
                        }
                    }
                    if (empty($bits)) {
                        $bits[] = '&nbsp;';
                    }
                    $row->cells[] = new html_table_cell(join(' ', $bits));
                } else {
                    if (!array_key_exists($field, $user)) {
                        $user[$field] = '&nbsp;';
                    }
                    $row->cells[] = new html_table_cell($user[$field]);
                }
            }
            $this->data[] = $row;
        }
    }

    public function initialise_javascript() {
        if (has_capability('moodle/role:assign', $this->manager->get_context())) {
            $this->moodlepage->requires->strings_for_js(array(
                'assignroles',
                'confirmunassign',
                'confirmunassigntitle',
                'confirmunassignyes',
                'confirmunassignno'
            ), 'role');
            $modules = array('moodle-enrol-rolemanager', 'moodle-enrol-rolemanager-skin');
            $function = 'M.enrol.rolemanager.init';
            $arguments = array(
                'containerId'=>$this->id,
                'userIds'=>array_keys($this->users),
                'courseId'=>$this->manager->get_course()->id,
                'otherusers'=>isset($this->otherusers));
            $this->moodlepage->requires->yui_module($modules, $function, array($arguments));
        }
    }

    /**
     * Gets the paging bar instance for this table
     *
     * @return paging_bar
     */
    public function get_paging_bar() {
        if ($this->pagingbar == null) {
            $this->pagingbar = new paging_bar($this->totalusers, $this->page, $this->perpage, $this->moodlepage->url, self::PAGEVAR);
        }
        return $this->pagingbar;
    }

    /**
     * Gets the direction icon for the sortable field within this table
     *
     * @param core_renderer $output
     * @param string $field
     * @return string
     */
    protected function get_direction_icon($output, $field) {
        $direction = self::DEFAULTSORTDIRECTION;
        if ($this->sort == $field) {
            $direction = $this->sortdirection;
        }
        if ($direction === 'ASC') {
            return html_writer::empty_tag('img', array('alt'=>'', 'src'=>$output->pix_url('t/down')));
        } else {
            return html_writer::empty_tag('img', array('alt'=>'', 'src'=>$output->pix_url('t/up')));
        }
    }

    /**
     * Gets the params that will need to be added to the url in order to return to this page.
     *
     * @return array
     */
    public function get_url_params() {
        return array(
            self::PAGEVAR => $this->page,
            self::PERPAGEVAR => $this->perpage,
            self::SORTVAR => $this->sort,
            self::SORTDIRECTIONVAR => $this->sortdirection
        );
    }
}

/**
 * Table control used for enrolled users
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enrolment_users_table extends course_enrolment_table {

    /**
     * An array of sortable fields
     * @static
     * @var array
     */
    protected static $sortablefields = array('firstname', 'lastname', 'email', 'lastaccess');

    /**
     * Returns a button to enrol cohorts or thier users
     *
     * @staticvar int $count
     * @return single_button|false
     */
    public function get_cohort_enrolment_control() {
        static $count = 0;

        // First make sure that cohorts is enabled
        $plugins = $this->manager->get_enrolment_plugins();
        if (!array_key_exists('cohort', $plugins)) {
            return false;
        }
        $course = $this->manager->get_course();
        if (!$plugins['cohort']->get_newinstance_link($course->id)) {
            // user can not see any cohort === can not use this
            return false;
        }
        $count ++;
        $cohorturl = new moodle_url('/enrol/cohort/addinstance.php', array('id'=>$course->id));
        $control = new single_button($cohorturl, get_string('enrolcohort', 'enrol'), 'get');
        $control->class = 'singlebutton enrolcohortbutton instance'.$count;
        $control->formid = 'manuallyenrol_single_'+$count;
        if ($count == 1) {
            $this->moodlepage->requires->strings_for_js(array('enrol','synced','enrolcohort','enrolcohortusers'), 'enrol');
            $this->moodlepage->requires->string_for_js('assignroles', 'role');
            $this->moodlepage->requires->string_for_js('cohort', 'cohort');
            $this->moodlepage->requires->string_for_js('users', 'moodle');

            $hasmanualinstance = false;
            // No point showing this at all if the user cant manually enrol users
            if (has_capability('enrol/manual:manage', $this->manager->get_context())) {
                // Make sure manual enrolments instance exists
                $instances = $this->manager->get_enrolment_instances();
                foreach ($instances as $instance) {
                    if ($instance->enrol == 'manual') {
                        $hasmanualinstance = true;
                        break;
                    }
                }
            }

            $modules = array('moodle-enrol-quickcohortenrolment', 'moodle-enrol-quickcohortenrolment-skin');
            $function = 'M.enrol.quickcohortenrolment.init';
            $arguments = array(
                'courseid'=>$course->id,
                'ajaxurl'=>'/enrol/ajax.php',
                'url'=>$this->moodlepage->url->out(false),
                'manualEnrolment'=>$hasmanualinstance);
            $this->moodlepage->requires->yui_module($modules, $function, array($arguments));
        }
        return $control;
    }

    /**
     * Gets the enrolment selector control for this table and initialises its
     * JavaScript
     *
     * @return single_button|url_select
     */
    public function get_enrolment_selector() {
        global $CFG;
        static $count = 0;

        $instances  = $this->manager->get_enrolment_instances();
        $plugins    = $this->manager->get_enrolment_plugins();
        $manuals    = array();
        // print enrol link or selection
        $links = array();
        foreach($instances as $instance) {
            $plugin = $plugins[$instance->enrol];
            if ($link = $plugin->get_manual_enrol_link($instance)) {
                $links[$instance->id] = $link;
                $manuals[$instance->id] = $instance;
            }
        }
        if (!empty($links)) {
            $arguments = array();
            $count ++;
            if (count($links) == 1) {
                $control = new single_button(reset($links), get_string('enrolusers', 'enrol_manual'), 'get');
                $control->class = 'singlebutton enrolusersbutton instance'.$count;
                $control->formid = 'manuallyenrol_single_'+$count;
                $arguments[] = array('id'=>key($links), 'name'=>$plugins[$instances[key($links)]->enrol]->get_instance_name($instances[key($links)]));
            } else if (count($links) > 1) {
                $inames     = $this->manager->get_enrolment_instance_names();
                $options = array();
                foreach ($links as $i=>$link) {
                    $options[$link->out(false)] = $inames[$i];
                    $arguments[] = array('id'=>$i, 'name'=>$plugins[$instances[$i]->enrol]->get_instance_name($instances[$i]));
                }
                $control = new url_select($options, '', array(''=>get_string('enrolusers', 'enrol_manual').'...'));
                $control->class = 'singlebutton enrolusersbutton instance'.$count;
                $control->formid = 'manuallyenrol_select_'+$count;
            }

            $course = $this->manager->get_course();
            $timeformat = get_string('strftimedatefullshort');
            $today = time();
            $today = make_timestamp(date('Y', $today), date('m', $today), date('d', $today), 0, 0, 0);
            $startdateoptions = array();
            if ($course->startdate > 0) {
                $startdateoptions[2] = get_string('coursestart') . ' (' . userdate($course->startdate, $timeformat) . ')';
            }
            $startdateoptions[3] = get_string('today') . ' (' . userdate($today, $timeformat) . ')' ;

            if ($count == 1) {
                $instance = reset($manuals);
                $this->moodlepage->requires->strings_for_js(array(
                    'ajaxoneuserfound',
                    'ajaxxusersfound',
                    'ajaxnext25',
                    'enrol',
                    'enrolmentoptions',
                    'enrolusers',
                    'errajaxfailedenrol',
                    'errajaxsearch',
                    'none',
                    'usersearch',
                    'unlimitedduration',
                    'startdatetoday',
                    'durationdays',
                    'enrolperiod',
                    'finishenrollingusers',
                    'recovergrades'), 'enrol');
                $this->moodlepage->requires->string_for_js('assignroles', 'role');
                $this->moodlepage->requires->string_for_js('startingfrom', 'moodle');

                $modules = array('moodle-enrol-enrolmentmanager', 'moodle-enrol-enrolmentmanager-skin');
                $function = 'M.enrol.enrolmentmanager.init';
                $arguments = array(
                    'instances'=>$arguments,
                    'courseid'=>$course->id,
                    'ajaxurl'=>'/enrol/ajax.php',
                    'url'=>$this->moodlepage->url->out(false),
                    'optionsStartDate'=>$startdateoptions,
                    'defaultRole'=>$instance->roleid,
                    'disableGradeHistory'=>$CFG->disablegradehistory);
                $this->moodlepage->requires->yui_module($modules, $function, array($arguments));
            }
            return $control;
        }
        return null;
    }
    /**
     * Gets the enrolment type filter control for this table
     *
     * @return single_select
     */
    public function get_enrolment_type_filter() {
        $selector = new single_select($this->moodlepage->url, 'ifilter', array(0=>get_string('all')) + (array)$this->manager->get_enrolment_instance_names(), $this->manager->get_enrolment_filter(), array());
        $selector->set_label( get_string('enrolmentinstances', 'enrol'));
        return $selector;
    }
}

/**
 * Table used for other users
 *
 * Other users are users who have roles but are not enrolled.
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enrolment_other_users_table extends course_enrolment_table {

    public $otherusers = true;

    /**
     * Constructs the table
     *
     * @param course_enrolment_manager $manager
     * @param moodle_page $moodlepage
     */
    public function __construct(course_enrolment_manager $manager, moodle_page $moodlepage) {
        parent::__construct($manager, $moodlepage);
        $this->attributes = array('class'=>'userenrolment otheruserenrolment');
    }

    /**
     * Gets a button to search users and assign them roles in the course.
     *
     * @staticvar int $count
     * @param int $page
     * @return single_button
     */
    public function get_user_search_button() {
        static $count = 0;
        if (!has_capability('moodle/role:assign', $this->manager->get_context())) {
            return false;
        }
        $count++;
        $url = new moodle_url('/admin/roles/assign.php', array('contextid'=>$this->manager->get_context()->id, 'sesskey'=>sesskey()));
        $control = new single_button($url, get_string('assignroles', 'role'), 'get');
        $control->class = 'singlebutton assignuserrole instance'.$count;
        if ($count == 1) {
            $this->moodlepage->requires->strings_for_js(array(
                    'ajaxoneuserfound',
                    'ajaxxusersfound',
                    'ajaxnext25',
                    'enrol',
                    'enrolmentoptions',
                    'enrolusers',
                    'errajaxfailedenrol',
                    'errajaxsearch',
                    'none',
                    'usersearch',
                    'unlimitedduration',
                    'startdatetoday',
                    'durationdays',
                    'enrolperiod'), 'enrol');
            $this->moodlepage->requires->string_for_js('assignrole', 'role');

            $modules = array('moodle-enrol-otherusersmanager', 'moodle-enrol-otherusersmanager-skin');
            $function = 'M.enrol.otherusersmanager.init';
            $arguments = array(
                'courseId'=> $this->manager->get_course()->id,
                'ajaxUrl' => '/enrol/ajax.php',
                'url' => $this->moodlepage->url->out(false));
            $this->moodlepage->requires->yui_module($modules, $function, array($arguments));
        }
        return $control;
    }
}
