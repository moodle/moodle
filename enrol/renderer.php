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
 * @package    core_enrol
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
     * Renderers the enrol_user_button.
     *
     * @param enrol_user_button $button
     * @return string XHTML
     */
    protected function render_enrol_user_button(enrol_user_button $button) {
        $attributes = array('type'     => 'submit',
                            'value'    => $button->label,
                            'disabled' => $button->disabled ? 'disabled' : null,
                            'title'    => $button->tooltip,
                            'class'    => 'btn btn-secondary m-y-1');

        if ($button->actions) {
            $id = html_writer::random_id('single_button');
            $attributes['id'] = $id;
            foreach ($button->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }
        $button->initialise_js($this->page);

        // first the input element
        $output = html_writer::empty_tag('input', $attributes);

        // then hidden fields
        $params = $button->url->params();
        if ($button->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $var => $val) {
            $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        if ($button->method === 'get') {
            $url = $button->url->out_omit_querystring(true); // url without params, the anchor part allowed
        } else {
            $url = $button->url->out_omit_querystring();     // url without params, the anchor part not allowed
        }
        if ($url === '') {
            $url = '#'; // there has to be always some action
        }
        $attributes = array('method' => $button->method,
                            'action' => $url,
                            'id'     => $button->formid);
        $output = html_writer::tag('form', $output, $attributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $button->class));
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

        // Get list of roles.
        $rolesoutput = '';
        foreach ($roles as $roleid=>$role) {
            if ($canassign and (is_siteadmin() or isset($assignableroles[$roleid])) and !$role['unchangeable']) {
                $strunassign = get_string('unassignarole', 'role', $role['text']);
                $icon = $this->output->pix_icon('t/delete', $strunassign);
                $url = new moodle_url($pageurl, array('action'=>'unassign', 'roleid'=>$roleid, 'user'=>$userid));
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
                $url = new moodle_url($pageurl, array('action' => 'assign', 'user' => $userid));
                $roleicon = $this->output->pix_icon('i/assignroles', get_string('assignroles', 'role'));
                $link = html_writer::link($url, $roleicon, array('class' => 'assignrolelink'));
                $output = html_writer::tag('div', $link, array('class'=>'addrole'));
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
        $groupicon = $this->output->pix_icon('i/group', get_string('addgroup', 'group'));

        $groupoutput = '';
        foreach($groups as $groupid=>$name) {
            if ($canmanagegroups and groups_remove_member_allowed($groupid, $userid)) {
                $icon = $this->output->pix_icon('t/delete', get_string('removefromgroup', 'group', $name));
                $url = new moodle_url($pageurl, array('action'=>'removemember', 'group'=>$groupid, 'user'=>$userid));
                $groupoutput .= html_writer::tag('div', $name . html_writer::link($url, $icon), array('class'=>'group', 'rel'=>$groupid));
            } else {
                $groupoutput .= html_writer::tag('div', $name, array('class'=>'group', 'rel'=>$groupid));
            }
        }
        $output = '';
        if ($canmanagegroups && (count($groups) < count($allgroups))) {
            $url = new moodle_url($pageurl, array('action'=>'addmember', 'user'=>$userid));
            $output .= html_writer::tag('div', html_writer::link($url, $groupicon), array('class'=>'addgroup'));
        }
        $output = $output.html_writer::tag('div', $groupoutput, array('class'=>'groups'));
        return $output;
    }

    /**
     * Generates the HTML for the given enrolments + available actions
     *
     * @param int $userid
     * @param array $enrolments
     * @param moodle_url $pageurl
     * @return string
     */
    public function user_enrolments_and_actions($enrolments) {
        $output = '';
        foreach ($enrolments as $ue) {
            $enrolmentoutput = $ue['text'].' '.$ue['period'];
            if ($ue['dimmed']) {
                $enrolmentoutput = html_writer::tag('span', $enrolmentoutput, array('class'=>'dimmed_text'));
            } else {
                $enrolmentoutput = html_writer::tag('span', $enrolmentoutput);
            }
            foreach ($ue['actions'] as $action) {
                $enrolmentoutput .= $this->render($action);
            }
            $output .= html_writer::tag('div', $enrolmentoutput, array('class'=>'enrolment'));
        }
        return $output;
    }

    /**
     * Renders a user enrolment action
     * @param user_enrolment_action $icon
     * @return string
     */
    protected function render_user_enrolment_action(user_enrolment_action $icon) {
        return html_writer::link($icon->get_url(), $this->output->render($icon->get_icon()), $icon->get_attributes());
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
     * An array of bulk user enrolment operations
     * @var array
     */
    protected $bulkoperations = array();

    /**
     * An array of sortable fields
     * @static
     * @var array
     */
    protected static $sortablefields = array('firstname', 'lastname', 'firstnamephonetic', 'lastnamephonetic', 'middlename',
            'alternatename', 'idnumber', 'email', 'phone1', 'phone2', 'institution', 'department', 'lastaccess', 'lastcourseaccess' );

    /**
     * Constructs the table
     *
     * @param course_enrolment_manager $manager
     */
    public function __construct(course_enrolment_manager $manager) {

        $this->manager        = $manager;

        $this->page           = optional_param(self::PAGEVAR, 0, PARAM_INT);
        $this->perpage        = optional_param(self::PERPAGEVAR, self::DEFAULTPERPAGE, PARAM_INT);
        $this->sort           = optional_param(self::SORTVAR, self::DEFAULTSORT, PARAM_ALPHANUM);
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

        // Collect the bulk operations for the currently filtered plugin if there is one.
        $plugin = $manager->get_filtered_enrolment_plugin();
        if ($plugin and enrol_is_enabled($plugin->get_name())) {
            $this->bulkoperations = $plugin->get_bulk_operations($manager);
        }
    }

    /**
     * Returns an array of enrol_user_buttons that are created by the different
     * enrolment plugins available.
     *
     * @return array
     */
    public function get_manual_enrol_buttons() {
        return $this->manager->get_manual_enrol_buttons();
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
        $url = $this->manager->get_moodlepage()->url;

        if (!empty($this->bulkoperations)) {
            // If there are bulk operations add a column for checkboxes.
            $this->head[] = '';
            $this->colclasses[] = 'field col_bulkops';
        }

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
                        $sorturl = new moodle_url($url, array(self::SORTVAR => $n, self::SORTDIRECTIONVAR => $this->get_field_sort_direction($n)));
                        $link = html_writer::link($sorturl, $fields[$name][$n]);
                        if ($this->sort == $n) {
                            $link .= $this->get_direction_icon($output, $n);
                        }
                        $bits[] = html_writer::tag('span', $link, array('class'=>'subheading_'.$n));

                    }
                }
                $newlabel = join(' / ', $bits);
            } else {
                if (!in_array($name, self::$sortablefields)) {
                    $newlabel = $label;
                } else {
                    $sorturl = new moodle_url($url, array(self::SORTVAR => $name, self::SORTDIRECTIONVAR => $this->get_field_sort_direction($name)));
                    $newlabel  = html_writer::link($sorturl, $fields[$name]);
                    if ($this->sort == $name) {
                        $newlabel .= $this->get_direction_icon($output, $name);
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
     * Sets the users for this table
     *
     * @param array $users
     * @return void
     */
    public function set_users(array $users) {
        $this->users = $users;
        $hasbulkops = !empty($this->bulkoperations);
        foreach ($users as $userid=>$user) {
            $user = (array)$user;
            $row = new html_table_row();
            $row->attributes = array('class' => 'userinforow');
            $row->id = 'user_'.$userid;
            $row->cells = array();
            if ($hasbulkops) {
                // Add a checkbox into the first column.
                $input = html_writer::empty_tag('input', array('type' => 'checkbox', 'name' => 'bulkuser[]', 'value' => $userid));
                $row->cells[] = new html_table_cell($input);
            }
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
            $this->manager->get_moodlepage()->requires->strings_for_js(array(
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
            $this->manager->get_moodlepage()->requires->yui_module($modules, $function, array($arguments));
        }
    }

    /**
     * Gets the paging bar instance for this table
     *
     * @return paging_bar
     */
    public function get_paging_bar() {
        if ($this->pagingbar == null) {
            $this->pagingbar = new paging_bar($this->totalusers, $this->page, $this->perpage, $this->manager->get_moodlepage()->url, self::PAGEVAR);
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
            return $output->pix_icon('t/sort_asc', get_string('sort'));
        } else {
            return $output->pix_icon('t/sort_desc', get_string('sort'));
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

    /**
     * Returns an array of URL params for both the table and the manager.
     *
     * @return array
     */
    public function get_combined_url_params() {
        return $this->get_url_params() + $this->manager->get_url_params();
    }

    /**
     * Sets the bulk operations for this table.
     *
     * @param array $bulkoperations
     */
    public function set_bulk_user_enrolment_operations(array $bulkoperations) {
        $this->bulkoperations = $bulkoperations;
    }

    /**
     * Returns an array of bulk operations.
     *
     * @return array
     */
    public function get_bulk_user_enrolment_operations() {
        return $this->bulkoperations;
    }

    /**
     * Returns true fi the table is aware of any bulk operations that can be performed on users
     * selected from the currently filtered enrolment plugins.
     *
     * @return bool
     */
    public function has_bulk_user_enrolment_operations() {
        return !empty($this->bulkoperations);
    }
}

/**
 * Table control used for enrolled users
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enrolment_users_table extends course_enrolment_table {

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
     */
    public function __construct(course_enrolment_manager $manager) {
        parent::__construct($manager);
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
            $this->manager->get_moodlepage()->requires->strings_for_js(array(
                    'ajaxoneuserfound',
                    'ajaxxusersfound',
                    'ajaxxmoreusersfound',
                    'ajaxnext25',
                    'enrol',
                    'enrolmentoptions',
                    'enrolusers',
                    'enrolxusers',
                    'errajaxfailedenrol',
                    'errajaxsearch',
                    'foundxcohorts',
                    'none',
                    'usersearch',
                    'unlimitedduration',
                    'startdatetoday',
                    'durationdays',
                    'enrolperiod'), 'enrol');
            $this->manager->get_moodlepage()->requires->string_for_js('assignrole', 'role');

            $modules = array('moodle-enrol-otherusersmanager', 'moodle-enrol-otherusersmanager-skin');
            $function = 'M.enrol.otherusersmanager.init';
            $arguments = array(
                'courseId'=> $this->manager->get_course()->id,
                'ajaxUrl' => '/enrol/ajax.php',
                'url' => $this->manager->get_moodlepage()->url->out(false));
            $this->manager->get_moodlepage()->requires->yui_module($modules, $function, array($arguments));
        }
        return $control;
    }
}
