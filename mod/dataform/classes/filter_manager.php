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
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Filter manager class
 */
class mod_dataform_filter_manager {

    const USER_FILTER_MAX_NUM = 5;
    const USER_FILTER_URL = -1;
    const USER_FILTER_QUICK = -2;
    const USER_FILTER_QUICK_RESET = -3;
    const USER_FILTER_ADVANCED = -4;
    const USER_FILTER_ADVANCED_RESET_ALL = -5;
    const USER_FILTER_ID_START = -10;

    protected $_dataformid;
    protected $_filters;

    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'filter_manager')) {
            $instance = new mod_dataform_filter_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'filter_manager', $instance);
        }

        return $instance;
    }

    /**
     * constructor
     */
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
        $this->_filters = array();
    }

    /**
     * Returns true if max filters as set by admin has been reached.
     *
     * @return bool
     */
    public function is_at_max_filters() {
        global $DB, $CFG;

        if ($CFG->dataform_maxfilters) {
            if ($count = $DB->count_records('dataform_filters', array('dataid' => $this->_dataformid))) {
                if ($count >= $CFG->dataform_maxfilters) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *
     */
    public function get_filter_by_id($filterid = 0, array $options = null) {
        global $DB;

        $df = mod_dataform_dataform::instance($this->_dataformid);
        $dfid = $this->_dataformid;

        // URL FILTER.
        if ($filterid == self::USER_FILTER_URL and $view and $view->is_active()) {
            if ($foptions = self::get_filter_options_from_url()) {
                $foptions['dataid'] = $dfid;
                return new mod_dataform\pluginbase\dataformfilter((object) $foptions);
            }
            $filterid = 0;
        }

        // USER FILTER.
        if ($filterid < 0) {
            // For actual user filters we need a view and whether advanced.
            $view = !empty($options['view']) ? $options['view'] : null;
            $viewid = $view ? $view->id : 0;

            // Set user quick filter.
            if ($filterid == self::USER_FILTER_QUICK and $view and $view->is_active()) {
                $instance = $this->set_quick_filter($view);
                return new mod_dataform\pluginbase\dataformfilter($instance);
            }

            // Reset user quick filter.
            if ($filterid == self::USER_FILTER_QUICK_RESET and $view and $view->is_active()) {
                $quickfilterid = self::USER_FILTER_QUICK;
                unset_user_preference("dataform-filter-$dfid-$viewid-$quickfilterid");
            }

            // Reset all user advanced filters for view.
            if ($filterid == self::USER_FILTER_ADVANCED_RESET_ALL and $view and $view->is_active()) {
                if ($afilters = $this->get_user_filters_menu($viewid)) {
                    foreach (array_keys($afilters) as $afilterid) {
                        unset_user_preference("dataform-filter-$dfid-$viewid-$afilterid");
                    }
                    unset_user_preference("dataform-filter-$dfid-$viewid-userfilters");
                }
            }

            // Retrieve existing user filter.
            if (($filterid <= self::USER_FILTER_ID_START) and $view and $view->is_active()) {
                if ($filter = get_user_preferences("dataform-filter-$dfid-$viewid-$filterid", null)) {
                    $filter = unserialize(base64_decode($filter));
                    $filter->dataid = $dfid;
                    return new mod_dataform\pluginbase\dataformfilter($filter);
                }
            }
            $filterid = 0;
        }

        // For all cases try default.
        if (!$filterid and $df->defaultfilter) {
            $filterid = $df->defaultfilter;
        }

        // DF FILTER.
        if ($filterid > 0 and $this->get_filters() and isset($this->_filters[$filterid])) {
            return $this->_filters[$filterid]->clone;
        }

        return $this->get_filter_blank();
    }

    /**
     *
     */
    public function get_filter_blank() {
        $filter = new \stdClass;
        $filter->dataid = $this->_dataformid;
        $filter->name = get_string('filternew', 'dataform');
        $filter->perpage = 0;

        return new mod_dataform\pluginbase\dataformfilter($filter);
    }

    /**
     *
     */
    public function get_filter_from_url($url, $raw = false) {
        global $DB;

        $df = mod_dataform_dataform::instance($this->_dataformid);
        $dfid = $this->_dataformid;

        if ($options = self::get_filter_options_from_url($url)) {
            $options['dataid'] = $dfid;

            // Get the filter if exists and add options to it.

            $filter = new mod_dataform\pluginbase\dataformfilter((object) $options);

            if ($raw) {
                return $filter->instance;
            } else {
                return $filter;
            }
        }
        return null;
    }

    /**
     *
     */
    public function get_filters($exclude = null, $menu = false, $forceget = false) {
        global $DB;
        if (!$this->_filters or $forceget) {
            $this->_filters = array();
            if ($filters = $DB->get_records('dataform_filters', array('dataid' => $this->_dataformid))) {
                foreach ($filters as $filterid => $filterdata) {
                    $this->_filters[$filterid] = new mod_dataform\pluginbase\dataformfilter($filterdata);
                }
            }
        }

        if ($this->_filters) {
            if (empty($exclude) and !$menu) {
                return $this->_filters;
            } else {
                $filters = array();
                $df = mod_dataform_dataform::instance($this->_dataformid);
                foreach ($this->_filters as $filterid => $filter) {
                    if (!empty($exclude) and in_array($filterid, $exclude)) {
                        continue;
                    }
                    if ($menu) {
                        if ($filter->visible or has_capability('mod/dataform:managefilters', $df->context)) {
                            $filters[$filterid] = $filter->name;
                        }
                    } else {
                        $filters[$filterid] = $filter;
                    }
                }
                return $filters;
            }
        }
        return $this->_filters;
    }

    /**
     *
     */
    public function process_filters($action, $fids, $confirmed = false) {
        global $CFG, $DB, $OUTPUT;

        $df = mod_dataform_dataform::instance($this->_dataformid);

        $filters = array();

        if (has_capability('mod/dataform:managefilters', $df->context)) {
            // Don't need record from database for filter form submission.
            if ($fids) {
                // Some filters are specified for action.
                $fids = !is_array($fids) ? explode(',', $fids) : $fids;
                foreach ($fids as $filterid) {
                    $filters[$filterid] = $this->get_filter_by_id($filterid);
                }
            } else if ($action == 'update') {
                $filters[0] = $this->get_filter_blank();
            }
        }
        $processedfids = array();
        $strnotify = '';

        if (empty($filters)) {
            $df->notifications = array('problem' => array('filternoneforaction' => get_string('filternoneforaction', 'dataform', $action)));
            return false;
        } else {
            if (!$confirmed) {
                $output = $df->get_renderer();
                echo $output->header('filters');

                // Print a confirmation page.
                echo $OUTPUT->confirm(get_string("filtersconfirm$action", 'dataform', count($filters)),
                        new moodle_url('/mod/dataform/filter/index.php', array('d' => $df->id,
                                                                        $action => implode(',', array_keys($filters)),
                                                                        'sesskey' => sesskey(),
                                                                        'confirmed' => 1)),
                        new moodle_url('/mod/dataform/filter/index.php', array('d' => $df->id)));

                echo $OUTPUT->footer();
                exit;

            } else {
                // Go ahead and perform the requested action.
                switch ($action) {
                    case 'duplicate':
                        if (!empty($filters)) {
                            foreach ($filters as $filter) {
                                if ($this->is_at_max_filters()) {
                                    break;
                                }
                                // Set new name.
                                while ($df->name_exists('filters', $filter->name)) {
                                    $filter->name = 'Copy of '. $filter->name;
                                }
                                $newfilter = $filter->clone;
                                $newfilter->id = 0;
                                $newfilter->update();

                                $this->_filters[$newfilter->id] = $newfilter;
                                $processedfids[] = $filter->id;
                            }
                        }
                        $strnotify = 'filtersadded';
                        break;

                    case 'visible':
                        $updatefilter = new stdClass;
                        foreach ($filters as $filter) {
                            $filter->visible = (int) !$filter->visible;
                            $filter->update();

                            $this->_filters[$filter->id] = $filter;
                            $processedfids[] = $filter->id;
                        }

                        $strnotify = '';
                        break;

                    case 'delete':
                        foreach ($filters as $filter) {
                            $filter->delete();
                            unset($this->_filters[$filter->id]);

                            // Reset default filter if needed.
                            if ($filter->id == $df->defaultfilter) {
                                $df->update((object) array('defaultfilter' => 0));
                            }

                            $processedfids[] = $filter->id;
                        }
                        $strnotify = 'filtersdeleted';
                        break;

                    default:
                        break;
                }

                if (!empty($strnotify)) {
                    $filtersprocessed = $processedfids ? count($processedfids) : 'No';
                    $df->notifications = array('success' => array('' => get_string($strnotify, 'dataform', $filtersprocessed)));
                }
                return $processedfids;
            }
        }
    }

    /**
     *
     */
    public function delete_filters() {
        if ($filters = $this->get_filters(null, true, true)) {
            $filterids = array_keys($filters);
            $this->process_filters('delete', $filterids, true);
        }
    }

    /**
     *
     */
    public function delete_advanced_filters() {
        global $DB;

        // Clean up dataform user preference.
        $select = $DB->sql_like('name', '?');
        $params = array("dataform-filter-{$this->_dataformid}-%");
        $preferences = $DB->get_records_select('user_preferences', $select, $params);
        foreach ($preferences as $preference) {
            unset_user_preference($preference->name, $preference->userid);
        }
    }

    /**
     *
     */
    public function get_filter_form($filter) {
        global $CFG;

        $formparams = array('d' => $this->_dataformid, 'fid' => $filter->id, 'update' => 1);
        $formurl = new moodle_url('/mod/dataform/filter/edit.php', $formparams);

        $mform = new mod_dataform\pluginbase\dataformfilterform_standard($filter->instance, $formurl);
        return $mform;
    }

    /**
     *
     */
    public function get_filter_from_form($filter, $formdata, $finalize = false) {
        $filter->name = $formdata->name;
        $filter->description = !empty($formdata->description) ? $formdata->description : '';
        $filter->perpage = !empty($formdata->perpage) ? $formdata->perpage : 0;
        $filter->visible = !empty($formdata->visible) ? $formdata->visible : 0;
        $filter->selection = !empty($formdata->selection) ? $formdata->selection : 0;
        $filter->groupby = !empty($formdata->groupby) ? $formdata->groupby : 0;
        $filter->search = isset($formdata->search) ? $formdata->search : '';
        $filter->customsort = $this->get_sort_options_from_form($formdata);
        $filter->customsearch = $this->get_search_options_from_form($formdata, $finalize);

        if ($filter->customsearch) {
            $filter->search = '';
        }

        return $filter;
    }

    /**
     *
     */
    protected function get_sort_options_from_form($formdata) {
        $filterformhelper = '\mod_dataform\helper\filterform';
        $sortfields = $filterformhelper::get_custom_sort_from_form($formdata);
        if ($sortfields) {
            return serialize($sortfields);
        } else {
            return '';
        }
    }

    /**
     *
     */
    protected function get_search_options_from_form($formdata) {
        $filterformhelper = '\mod_dataform\helper\filterform';
        $searchfields = $filterformhelper::get_custom_search_from_form($formdata, $this->_dataformid);
        if ($searchfields) {
            return serialize($searchfields);
        } else {
            return '';
        }
    }

    // ADVANCED FILTER.

    /**
     *
     */
    public function get_advanced_filter_form($filter, $view, $pagefile = 'view') {
        $urlparams = array('d' => $this->_dataformid, 'view' => $view->id, 'pagefile' => $pagefile, 'fid' => $filter->id);
        $formurl = new moodle_url('/mod/dataform/filter/editadvanced.php', $urlparams);
        $mform = new mod_dataform\pluginbase\dataformfilterform_advanced($filter, $formurl, array('view' => $view));
        return $mform;
    }

    /**
     *
     */
    public function get_user_filters_menu($viewid) {
        $filters = array();

        $df = mod_dataform_dataform::instance($this->_dataformid);
        $dfid = $df->id;
        // Add last quick filter.
        $quickfilterid = self::USER_FILTER_QUICK;
        if ($quickfilter = get_user_preferences("dataform-filter-$dfid-$viewid-$quickfilterid", null)) {
            $filters[$quickfilterid] = get_string('filterquick', 'dataform');
        }
        // Add user saved filters.
        if ($filternames = get_user_preferences("dataform-filter-$dfid-$viewid-userfilters", '')) {
            foreach (explode(';', $filternames) as $filteridname) {
                list($filterid, $name) = explode(' ', $filteridname, 2);
                $filters[$filterid] = $name;
            }
            $savedreset = true;
        }
        // Add quick reset option.
        if ($quickfilter) {
            $filters[self::USER_FILTER_QUICK_RESET] = get_string('filterquickreset', 'dataform');
        }
        // Add saved reset option.
        if ($filternames) {
            $filters[self::USER_FILTER_ADVANCED_RESET_ALL] = get_string('filtersavedreset', 'dataform');
        }

        return $filters;
    }

    /**
     *
     */
    public function set_quick_filter($view) {
        $df = mod_dataform_dataform::instance($this->_dataformid);
        $dfid = $df->id;
        $viewid = $view->id;

        $filterid = self::USER_FILTER_QUICK;
        $instance = get_user_preferences("dataform-filter-$dfid-$viewid-$filterid", null);
        $filteroptions = (object) self::get_filter_options_from_url();
        // Neither saved filter nor new options.
        if (empty($instance) and empty($filteroptions)) {
            return null;
        }
        // Saved filter but no new options.
        if ($instance and empty($filteroptions)) {
            $instance = unserialize(base64_decode($instance));
            return $instance;
        }
        // Saved filter and new options.
        if ($instance and $filteroptions) {
            $instance = (object) unserialize(base64_decode($instance));
            // Add options to the existing filter and save.
            foreach ($filteroptions as $option => $val) {
                // Skip id.
                if ($option == 'id') {
                    continue;
                }
                // Reset search if needed.
                if ($option == 'searchreset' and empty($filteroptions->search)) {
                    $instance->search = '';
                    continue;
                }

                $instance->$option = $val;
            }

            $instance->dataid = $dfid;
            set_user_preference("dataform-filter-$dfid-$viewid-$filterid", base64_encode(serialize($instance)));
            return $instance;
        }

        // New options only.
        $filteroptions->id = $filterid;
        $filteroptions->dataid = $dfid;
        set_user_preference("dataform-filter-$dfid-$viewid-$filterid", base64_encode(serialize($filteroptions)));
        return $filteroptions;
    }

    /**
     *
     */
    public function set_advanced_filter($filter, $view, $newfilter = true) {
        $dfid = $this->_dataformid;
        $viewid = $view->id;
        $filterid = $filter->id;

        // Get saved user filters.
        $userfilters = array();
        if ($filternames = get_user_preferences("dataform-filter-$dfid-$viewid-userfilters", '')) {
            foreach (explode(';', $filternames) as $filteridname) {
                list($fid, $name) = explode(' ', $filteridname, 2);
                $userfilters[$fid] = $name;
            }
        }

        // If max number of user filters pop the last.
        $maxfilters = $newfilter ? self::USER_FILTER_MAX_NUM - 1 : self::USER_FILTER_MAX_NUM;
        if (count($userfilters) >= $maxfilters) {
            $fids = array_keys($userfilters);
            while (count($fids) >= $maxfilters) {
                $fid = array_pop($fids);
                unset($userfilters[$fid]);
                unset_user_preference("dataform-filter-$dfid-$viewid-$fid");
            }
        }

        $newfilter = ($newfilter or $filterid > self::USER_FILTER_ID_START or !$userfilters or !array_key_exists($filterid, $userfilters));

        if ($newfilter) {
            $filterid = $userfilters ? min(array_keys($userfilters)) - 1 : self::USER_FILTER_ID_START;
        }

        // Save the filter.
        $filter->id = $filterid;
        $filter->dataid = $dfid;
        if (!$filter->name) {
            $filter->name = get_string('filtersaved', 'dataform'). ' '. abs($filterid);
        }

        set_user_preference("dataform-filter-$dfid-$viewid-$filterid", base64_encode(serialize($filter->instance)));

        // Add the new filter to the beginning of the userfilters.
        if ($newfilter) {
            $userfilters = array($filterid => $filter->name) + $userfilters;
        } else {
            $userfilters[$filterid] = $filter->name;
        }

        foreach ($userfilters as $filterid => $name) {
            $userfilters[$filterid] = "$filterid $name";
        }
        set_user_preference("dataform-filter-$dfid-$viewid-userfilters", implode(';', $userfilters));

        return $filter;
    }

    // HELPERS.

    /**
     *
     */
    public static function get_filter_url_query($filter) {
        $urlquery = array();

        if ($filter->customsort) {
            $urlquery[] = 'usort='. self::get_sort_url_query(unserialize($filter->customsort));
        }
        if ($filter->customsearch) {
            $urlquery[] = 'ucsearch='. self::get_search_url_query(unserialize($filter->customsearch));
        }
        if ($filter->search) {
            $urlquery[] = 'usearch='. urlencode($filter->search);
        }

        if ($urlquery) {
            return implode('&', $urlquery);
        }
        return '';
    }

    /**
     *
     */
    public static function get_sort_url_query(array $sorties) {
        if ($sorties) {
            $usort = array();
            foreach ($sorties as $fieldid => $dir) {
                $usort[] = "$fieldid $dir";
            }
            return urlencode(implode(',', $usort));
        }
        return '';
    }

    /**
     *
     */
    public static function get_sort_options_from_query($query) {
        $usort = null;
        if ($query) {
            $usort = urldecode($query);
            $usort = array_map(
                function($a) {
                    return explode(' ', $a);
                },
                explode(',', $usort)
            );
        }
        return $usort;
    }

    /**
     *
     */
    public static function get_search_url_query(array $searchies) {
        $ucsearch = null;
        if ($searchies) {
            $ucsearch = array();
            foreach ($searchies as $fieldid => $andor) {
                foreach ($andor as $key => $soptions) {
                    if (empty($soptions)) {
                        continue;
                    }
                    foreach ($soptions as $options) {
                        if (empty($options)) {
                            continue;
                        }
                        list($element, $not, $op, $value) = $options;
                        $searchvalue = is_array($value) ? implode('|', $value) : $value;
                        $ucsearch[] = "$fieldid:$key:$element,$not,$op,$searchvalue";
                    }
                }
            }
            $ucsearch = implode('@', $ucsearch);
            $ucsearch = urlencode($ucsearch);
        }
        return $ucsearch;
    }

    /**
     *
     */
    public static function get_search_options_from_query($query) {
        $soptions = array();
        if ($query) {
            $ucsearch = urldecode($query);
            $searchies = explode('@', $ucsearch);
            foreach ($searchies as $key => $searchy) {
                list($fieldid, $andor, $options) = explode(':', $searchy);
                $soptions[$fieldid] = array($andor => array_map(
                    function($a) {
                        return explode(',', $a);
                    },
                    explode('#', $options))
                );
            }
        }
        return $soptions;
    }

    /**
     *
     */
    public static function get_filter_options_from_url($url = null) {
        $filteroptions = array(
            'id' => array('filter', 0, PARAM_INT),
            'perpage' => array('uperpage', 0, PARAM_INT),
            'selection' => array('uselection', 0, PARAM_INT),
            'groupby' => array('ugroupby', 0, PARAM_INT),
            'search' => array('usearch', '', PARAM_RAW),
            'customsort' => array('usort', '', PARAM_RAW),
            'customsearch' => array('ucsearch', '', PARAM_RAW),
            'page' => array('page', 0, PARAM_INT),
            'eids' => array('eids', '', PARAM_TAGLIST),
            'users' => array('users', '', PARAM_SEQUENCE),
            'groups' => array('groups', '', PARAM_SEQUENCE),
        );

        $options = array();

        // Url provided.
        if ($url) {
            if ($url instanceof moodle_url) {
                foreach ($filteroptions as $option => $args) {
                    list($name, , ) = $args;
                    if ($val = $url->get_param($name)) {
                        if ($option == 'customsort') {
                            $options[$option] = self::get_sort_options_from_query($val);
                        } else if ($option == 'customsearch') {
                            $searchoptions = self::get_search_options_from_query($val);
                            $options[$option] = $searchoptions;
                        } else if ($option == 'search') {
                            $options[$option] = urldecode($val);
                        } else {
                            $options[$option] = $val;
                        }
                    }
                }
            }
            return $options;
        }

        // Optional params.
        foreach ($filteroptions as $option => $args) {
            list($name, , $type) = $args;
            $val = optional_param($name, null, $type);
            if (!is_null($val)) {
                if ($option == 'customsort') {
                    $options[$option] = self::get_sort_options_from_query($val);
                } else if ($option == 'customsearch') {
                    $searchoptions = self::get_search_options_from_query($val);
                    $options[$option] = $searchoptions;
                } else if ($option == 'search') {
                    $options[$option] = urldecode($val);
                } else {
                    $options[$option] = $val;
                }
            }
        }
        return $options;
    }
}
