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
 * @package mod_dataform
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * View manager class
 */
class mod_dataform_view_manager {

    /** @var int The id of the Dataform this manager works for. */
    protected $_dataformid;
    protected $_items;

    /**
     * Returns and caches (for the current script) if not already, a views manager for the specified Dataform.
     *
     * @param int Dataform id
     * @return mod_dataform_view_manager
     */
    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'view_manager')) {
            $instance = new mod_dataform_view_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'view_manager', $instance);
        }

        return $instance;
    }

    /**
     * constructor
     */
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
        $this->_items = array();
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        return null;
    }

    /**
     * Returns true if max views as set by admin has been reached.
     *
     * @return bool
     */
    public function is_at_max_views() {
        global $DB, $CFG;

        if ($CFG->dataform_maxviews) {
            if ($count = $DB->count_records('dataform_views', array('dataid' => $this->_dataformid))) {
                if ($count >= $CFG->dataform_maxviews) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Adds a view of the given type with default settings.
     *
     * @param string $type View type
     * @return dataformview_type_type View object
     */
    public function add_view($type) {
        if ($view = $this->get_view($type)) {
            $view->generate_default_view();
            $view->add($view->data);
            $this->_items[$view->id] = $view->data;
        }
        return $view;
    }

    /**
     *
     */
    public function has_views() {
        global $DB;

        return  $DB->record_exists('dataform_views', array('dataid' => $this->_dataformid));
    }

    /**
     *
     */
    protected function get_view_records($forceget = false, $options = null, $sort = '') {
        global $DB;

        if (empty($this->_items) or $forceget) {
            $this->_items = array();
            $params = array('dataid' => $this->_dataformid);
            if (!empty($options['type'])) {
                $params['type'] = $options['type'];
            }
            if (!$this->_items = $DB->get_records('dataform_views', $params, $sort)) {
                return false;
            }
        }
        return $this->_items;
    }

    /**
     * Given a view name return the view object
     *
     */
    public function get_view_by_name($viewname, $forceget = false) {
        global $DB;

        if (!empty($this->_items) and !$forceget) {
            foreach ($this->_items as $view) {
                if ($view->name == $viewname) {
                    return $this->get_view($view);
                }
            }
        }

        // Either no view or forceget so get the view from DB.
        if ($view = $DB->get_record('dataform_views', array('dataid' => $this->_dataformid, 'name' => $viewname))) {
            $this->_items[$view->id] = $view;
            return $this->get_view($view);
        }

        return false;
    }

    /**
     * Returns a view object by id.
     * If no spcific view is requested or the requested view is not found,
     * tries to return the default view.
     * This method does not check access permission.
     *
     * @return dataformview|false
     */
    public function get_view_by_id($viewid = 0) {
        global $DB;

        if (!$viewid and !$viewid = \mod_dataform_dataform::instance($this->_dataformid)->defaultview) {
            return false;
        }

        if (!empty($this->_items[$viewid])) {
            return $this->get_view($this->_items[$viewid]);
        }

        // Not stored so get the view from DB.
        if ($view = $DB->get_record('dataform_views', array('id' => $viewid))) {
            $this->_items[$view->id] = $view;
            return $this->get_view($view);
        }

        return false;
    }

    /**
     * Given an array of view ids return the view objects
     *
     */
    public function get_views_by_id(array $viewids, $forceget = false) {
        $views = array();
        foreach ($viewids as $viewid) {
            if ($view = $this->get_view_by_id($viewid, $forceget)) {
                $views[$viewid] = $view;
            }
        }
        return $views;
    }

    /**
     * returns a view subclass object given a view record or view type
     * invoke plugin methods
     * input: $param $vt - mixed, view record or view type
     */
    public function get_view($objortype, $active = false) {
        global $CFG;

        if ($objortype) {
            if (is_object($objortype)) {
                $type = $objortype->type;
                $obj = $objortype;
            } else {
                $type = $objortype;
                $obj = new stdClass;
                $obj->type = $type;
                $obj->dataid = $this->_dataformid;
            }

            $objclass = "dataformview_{$type}_$type";
            $view = new $objclass($obj, $active);
            return $view;
        }
        return false;
    }

    /**
     * given a view type returns the view object from $this->_items
     * Initializes $this->_items if necessary
     */
    public function get_views_by_type($type, $forceget = false) {
        if (!$views = $this->get_view_records($forceget, array('type' => $type))) {
            return false;
        }

        $typeviews = array();
        foreach ($views as $viewid => $view) {
            $typeviews[$viewid] = $this->get_view($view);
        }
        return $typeviews;
    }

    /**
     * GGiven a parentclass name returns all views that are instances of the parentclass
     * Initializes $this->_items if necessary
     */
    public function get_views_by_instanceof($parentclass, $forceget = false) {
        if (!$views = $this->get_views(array('forceget' => $forceget))) {
            return false;
        }

        foreach ($views as $viewid => $view) {
            if ($view instanceof $parentclass) {
                continue;
            }
            unset($views[$viewid]);
        }

        return $views;
    }

    /**
     *
     */
    public function get_views(array $options = null) {
        $forceget = !empty($options['forceget']);
        $sort = !empty($options['sort']) ? $options['sort'] : '';

        if (!$this->get_view_records($forceget, null, $sort)) {
            return false;
        }

        $views = array();
        $exclude = !empty($options['exclude']) ? $options['exclude'] : null;
        foreach ($this->_items as $viewid => $view) {
            if ($exclude and in_array($viewid, $exclude)) {
                continue;
            }
            $views[$viewid] = $this->get_view($view);
        }
        return $views;
    }

    /**
     *
     */
    public function get_views_menu() {
        global $DB;

        $menu = array();

        $params = array('dataid' => $this->_dataformid);
        $views = $DB->get_records('dataform_views', $params, '', 'id,name,visible');

        // Check access to the view.
        $df = mod_dataform_dataform::instance($this->_dataformid);
        $canaccessdisabled = has_capability('mod/dataform:viewaccessdisabled', $df->context);
        foreach ($views as $viewid => $view) {
            if (!$canaccessdisabled) {
                if (!$view->visible) {
                    continue;
                }
                $accessparams = array('dataformid' => $this->_dataformid, 'viewid' => $viewid);
                if (!mod_dataform\access\view_access::validate($accessparams)) {
                    continue;
                }
            }
            $menu[$view->id] = $view->name;
        }

        return $menu;
    }

    /**
     *
     */
    public function get_views_navigation_menu() {
        global $DB;

        $menu = array();

        $params = array('dataid' => $this->_dataformid);
        $views = $DB->get_records('dataform_views', $params, '', 'id,name,visible');

        // Check access to the view.
        $df = mod_dataform_dataform::instance($this->_dataformid);
        $manager = has_capability('mod/dataform:manageviews', $df->context);
        foreach ($views as $viewid => $view) {
            // Skip hidden views.
            if ($view->visible == \mod_dataform\pluginbase\dataformview::VISIBILITY_HIDDEN) {
                continue;
            }
            if (!$manager) {
                if (!$view->visible) {
                    continue;
                }
                $accessparams = array('dataformid' => $this->_dataformid, 'viewid' => $viewid);
                if (!mod_dataform\access\view_access::validate($accessparams)) {
                    continue;
                }
            }
            $menu[$view->id] = $view->name;
        }

        return $menu;
    }

    /**
     * Search for a field name and replaces it with another one in all the *
     * form templates. Set $newfieldname as '' if you want to delete the   *
     * field from the form.
     */
    public function replace_patterns_in_views($patterns, $replacements) {
        if ($views = $this->get_views()) {
            foreach ($views as $view) {
                $view->replace_patterns_in_view($patterns, $replacements);
            }
        }
    }

    /**
     * Processes view crud requests and returns a list of processed view ids.
     *
     * @param string $action
     * @param string|array $vids View ids to process
     * @param object|array $data
     * @param bool $confirmed
     * @return bool/array
     * @throws \required_capability_exception on mod/dataform:manageviews
     */
    public function process_views($action, $vids, $data = null, $confirmed = false) {
        global $DB, $OUTPUT;

        $df = mod_dataform_dataform::instance($this->_dataformid);

        require_capability('mod/dataform:manageviews', $df->context);

        // Get array of ids.
        if (!is_array($vids)) {
            $vids = explode(',', $vids);
        }

        $views = $this->get_views_by_id($vids);

        $processedvids = array();
        $strnotify = '';

        if (empty($views)) {
            $df->notifications = array('problem' => array('viewnoneforaction' => get_string('viewnoneforaction', 'dataform')));
            return false;
        } else {
            if (!$confirmed) {
                $output = $df->get_renderer();
                echo $output->header('views');

                // Print a confirmation page.
                echo $output->confirm(get_string("viewsconfirm$action", 'dataform', count($views)),
                        new moodle_url('/mod/dataform/view/index.php', array('d' => $this->_dataformid,
                                                                        $action => implode(',', array_keys($views)),
                                                                        'sesskey' => sesskey(),
                                                                        'confirmed' => 1)),
                        new moodle_url('/mod/dataform/view/index.php', array('d' => $this->_dataformid)));

                echo $output->footer();
                exit;

            } else {
                // Go ahead and perform the requested action.
                switch ($action) {
                    case 'visible':
                        $strnotify = '';

                        $data = $data ? (array) $data : null;

                        // We need a visibility value.
                        if (!isset($data['visibility'])) {
                            break;
                        }

                        // Visibility value must be a valid mode.
                        if (!array_key_exists($data['visibility'], \mod_dataform\pluginbase\dataformview::get_visibility_modes())) {
                            break;
                        }

                        $updateview = new stdClass;
                        foreach ($views as $vid => $view) {
                            // Default view cannot be disabled.
                            if ($vid == $df->defaultview and !$data['visibility']) {
                                continue;
                            }

                            // Update visibility.
                            $view->visible = $data['visibility'];
                            if ($view->update($view->data)) {
                                $processedvids[] = $vid;
                            }
                        }

                        break;

                    case 'filter':
                        $updateview = new stdClass;
                        $filterid = optional_param('fid', 0, PARAM_INT);
                        foreach ($views as $vid => $view) {
                            if ($filterid != $view->filterid) {
                                $view->filterid = ($filterid == -1 ? 0 : $filterid);

                                // Update.
                                if ($view->update($view->data)) {
                                    $processedvids[] = $vid;
                                }
                            }
                        }

                        $strnotify = 'viewsupdated';
                        break;

                    case 'reset':
                        foreach ($views as $vid => $view) {
                            // Generate default view and update.
                            $view->generate_default_view();

                            // Update.
                            if ($view->update($view->data)) {
                                $processedvids[] = $vid;
                            }
                        }

                        $strnotify = 'viewsupdated';
                        break;

                    case 'duplicate':
                        foreach ($views as $vid => $view) {
                            if ($this->is_at_max_views()) {
                                break;
                            }

                            // Get new name.
                            $i = 1;
                            while ($df->name_exists('views', $view->name. "_$i")) {
                                $i++;
                            }
                            $viewname = $view->name. "_$i";

                            // Update.
                            if ($view->duplicate($viewname)) {
                                $processedvids[] = $vid;
                            }
                        }

                        $strnotify = 'viewsadded';
                        break;

                    case 'delete':
                        foreach ($views as $vid => $view) {
                            $view->delete();
                            unset($this->_items[$vid]);
                            $processedvids[] = $vid;

                            // Reset default view if needed.
                            if ($view->id == $df->defaultview) {
                                $df->update((object) array('defaultiview' => 0));
                            }
                        }
                        $strnotify = 'viewsdeleted';
                        break;

                    case 'default':
                        foreach ($views as $vid => $view) {
                            if (!$view->visible) {
                                $updateview = new stdClass;
                                $updateview->id = $vid;
                                $updateview->visible = 1;
                                $DB->update_record('dataform_views', $updateview);
                            }

                            $df->update((object) array('defaultview' => $vid));
                            $processedvids[] = $vid;
                            // There should be only one, so break.
                            break;
                        }
                        $strnotify = '';
                        break;

                    default:
                        break;
                }

                if ($strnotify) {
                    $viewsprocessed = $processedvids ? count($processedvids) : 'No';
                    $df->notifications = array('success' => array('' => get_string($strnotify, 'dataform', $viewsprocessed)));
                }
                return $processedvids;
            }
        }
    }

    /**
     *
     */
    public function delete_views() {
        if ($views = $this->views_menu) {
            $viewids = array_keys($views);
            $this->process_views('delete', $viewids, null, true);
        }
    }

    /**
     * Displays patterns clean up form and processes form submission.
     *
     * @param int $dataformid The id of the dataform to clean up. Default -1 for all dataforms.
     * @param int $viewid The id of the view to clean up. Default -1 for all views.
     * @return void
     */
    public static function patterns_cleanup($dataformid = -1, $viewid = -1) {
        global $DB, $PAGE, $CFG;

        if (!$dataformid or !$viewid) {
            redirect($PAGE->url);
        }

        require_once("$CFG->dirroot/mod/dataform/view/patternsform.php");

        // Get the Dataforms.
        if ($dataformid == -1) {
            if (!$dataformids = $DB->get_records('dataform', array(), '', 'id')) {
                return false;
            }
            $dataformids = array_keys($dataformids);
        } else {
            $dataformids = array($dataformid);
        }

        // Collate the broken patterns.
        $brokenpatterns = array();
        foreach ($dataformids as $dfid) {
            $df = mod_dataform_dataform::instance($dfid);
            // Get the views.
            if ($viewid == -1) {
                if (!$views = $df->view_manager->get_views()) {
                    continue;
                }
            } else {
                if (!$view = $df->view_manager->get_view_by_id($viewid)) {
                    continue;
                }
                $views = array($view);
            }

            foreach ($views as $view) {
                if ($updates = $view->patterns_check()) {
                    $brokenpatterns = array_merge($brokenpatterns, $updates);
                }
            }
        }

        if (!$brokenpatterns) {
            redirect($PAGE->url);
        }

        // Get the form.
        $mform = new mod_dataform_view_patternsform(null, array('patterns' => $brokenpatterns));

        // Cancelled.
        if ($mform->is_cancelled()) {
            redirect($PAGE->url);
        }

        // Clean up.
        if ($data = $mform->get_data()) {
            if (!empty($data->replacements)) {
                $patterns = array_keys($data->replacements);
                $replacements = $data->replacements;

                if ($viewid and $viewid != -1) {
                    $df = mod_dataform_dataform::instance($dataformid);
                    $view = $df->view_manager->get_view_by_id($viewid);
                    $view->replace_patterns_in_view($patterns, $replacements);

                } else {
                    foreach ($dataformids as $dfid) {
                        $df = mod_dataform_dataform::instance($dfid);
                        $df->view_manager->replace_patterns_in_views($patterns, $replacements);
                    }
                }
            }
            // Redirect to refresh the form.
            $url = new moodle_url($PAGE->url, array('patternscleanup' => $viewid));
            redirect($url);

        }

        // Heading.
        echo html_writer::tag('h3', get_string('patternsreplacement', 'dataform'));

        // Display the form.
        $actionurl = new moodle_url($PAGE->url, array('patternscleanup' => $viewid));
        $customdata = array('patterns' => $brokenpatterns);
        $mform = new mod_dataform_view_patternsform($actionurl, $customdata);
        $mform->display();
    }

}
