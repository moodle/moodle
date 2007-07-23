<?php //$Id$

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
    if (true) { //TODO: add proper capability here
        $menu['edit']='--'.get_string('edit');
        $url = 'edit/tree.php?id='.$courseid;
        if ($active_type == 'edit' and $active_plugin == 'tree' ) {
            $active = $url;
        }
        $menu[$url] = get_string('edittree', 'grades');
    }

/// finally print/return the popup form
    return popup_form($CFG->wwwroot.'/grade/', $menu, 'choosepluginreport', $active, 'choose', '', '', $return, 'self', get_string('selectplugin', 'grades'));
}

/**
 * Utility class used for return tracking when using edit and other forms from grade plubins
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
        if (empty($this->type) or empty($this->plugin)) {
            return array();
        }

        $params = array();

        $params['plugin'] = $this->plugin;

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
        if (empty($this->type) or empty($this->plugin)) {
            return '';
        }

        $result  = '<input type="hidden" name="gpr_type" value="'.$this->type.'" />';
        $result .= '<input type="hidden" name="gpr_plugin" value="'.$this->plugin.'" />';

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
        if (empty($this->type) or empty($this->plugin)) {
            return;
        }

        $mform->addElement('hidden', 'gpr_type', $this->type);
        $mform->setType('gpr_type', PARAM_SAFEDIR);

        $mform->addElement('hidden', 'gpr_plugin', $this->plugin);
        $mform->setType('gpr_plugin', PARAM_SAFEDIR);

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
        if (empty($this->type) or empty($this->plugin)) {
            return $url;
        }

        if (strpos($url, '?') === false) {
            $url .= '?gpr_type='.$this->type;
        } else {
            $url .= '&amp;gpr_type='.$this->type;
        }

        $url .= '&amp;gpr_plugin='.$this->plugin;

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
?>
