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
 * @package dataformview
 * @copyright 2013 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_dataform\pluginbase;

/**
 * A base class for dataform views
 * (see view/<view type>/<view type>.php)
 */
class dataformview {

    /** @const int View is available only to managers. */
    const VISIBILITY_DISABLED = 0;
    /** @const int View is available to all and appears in navigation. */
    const VISIBILITY_VISIBLE = 1;
    /** @const int View is available to all but does not appear in navigation. */
    const VISIBILITY_HIDDEN = 2;

    /** @const int Show edited entries separate from non-edited */
    const EDIT_SEPARATE = 1;
    /** @const int Show edited entries inline with non-edited */
    const EDIT_INLINE = 2;

    /** @var stdClass The view raw data object */
    protected $_view = null;
    /** @var dataformfilter The view's filter */
    protected $_filter = null;
    /** @var dataformviewpatterns The view's patterns object */
    protected $_renderer = null;
    /** @var array The var names of the view editor areas */
    protected $_editors = array('section');
    /** @var moodle_url The view's base url */
    protected $_baseurl = null;
    /** @var int|string Negative or comma delimited list of entry ids to be edited */
    protected $_editentries = 0;
    /** @var string Comma delimited list of entry ids that habe been processed */
    protected $_processedentries = null;

    /**
     * @return array List of the view file areas
     */
    public static function get_file_areas() {
        return array('section');
    }

    /**
     * Class constructor
     *
     * @param var $field    field id or DB record
     */
    public function __construct($view) {

        if (empty($view)) {
            throw new \coding_exception('View object must be passed to dataformview constructor.');
        }

        $this->_view = $view;
        $this->prepare_editors();
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
        $this->_view->{$key} = $value;
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
        if (isset($this->_view->{$key})) {
            return $this->_view->{$key};
        }
        return null;
    }


    /**
     *
     */
    public function set_viewfilter(array $options = array()) {
        $fm = \mod_dataform_filter_manager::instance($this->dataid);

        $forcedfilter = null;
        $filterfromid = null;
        $filterfromoptions = null;
        $filterfromurl = null;

        // Get forced filter if set.
        if ($this->filterid) {
            $forcedfilter = $fm->get_filter_by_id($this->filterid, array('view' => $this));
        }

        // Get filter from id option.
        if (!empty($options['id'])) {
            $fid = $options['id'];
            if ($fid != $this->filterid) {
                $filterfromid = $fm->get_filter_by_id($fid, array('view' => $this));
            }
            unset($options['id']);
        }

        // Get filter from other options.
        if ($this->perpage and empty($options['perpage'])) {
            $options['perpage'] = $this->perpage;
        }
        if (!empty($options)) {
            $options['dataid'] = $this->dataid;
            $filterfromoptions = new dataformfilter((object) $options);
        }

        // Get filter from url.
        if ($urloptions = ($this->is_active() ? $fm::get_filter_options_from_url() : null)) {
            $urloptions['dataid'] = $this->dataid;
            $filterfromurl = new dataformfilter((object) $urloptions);
        }

        $filterspecified = ($forcedfilter or $filterfromid or $filterfromoptions or $filterfromurl);

        // Get the base filter for this view.
        if ($filterspecified) {
            $filter = $forcedfilter ? $forcedfilter : $fm->get_filter_blank();
            $filter->append(array($filterfromid, $filterfromoptions, $filterfromurl));
        } else {
            // If no filter specified and there is default filter, use default.
            $fid = $this->df->defaultfilter ? $this->df->defaultfilter : 0;
            $filter = $fm->get_filter_by_id($fid, array('view' => $this));
        }

        // Content fields.
        if ($patternfields = $this->get_pattern_set('field')) {
            $filter->contentfields = array_keys($patternfields);
        }

        $this->_filter = $filter;
    }

    // VIEW DISPLAY.
    /**
     * Sets the view filter and any page settings before output.
     *
     * @return void
     */
    public function set_page($pagefile = null, array $options = null) {

        // Filter.
        $foptions = !empty($options['filter']) ? array('id' => $options['filter']) : array();
        $this->set_viewfilter($foptions);
    }

    /**
     * Sends to porfolio exporter if export is requested by export url param.
     *
     * @return void
     */
    public function process_portfolio_export() {
        global $CFG;

        // Proces export requests.
        $export = optional_param('export', '', PARAM_TAGLIST);  // Comma delimited entry ids or -1 for all entries in view
        if ($export and confirm_sesskey()) {
            if (!empty($CFG->enableportfolios)) {
                require_once("$CFG->libdir/portfoliolib.php");
                $exportparams = array(
                    'ca_id' => $this->df->cm->id,
                    'ca_vid' => $this->id,
                    'ca_fid' => $this->filter->id,
                    'ca_eids' => null,
                    'sesskey' => sesskey(),
                    'callbackfile' => '/mod/dataform/locallib.php',
                    'callbackclass' => 'dataform_portfolio_caller',
                    'callerformats' => optional_param('format', 'spreadsheet,richhtml', PARAM_TAGLIST),
                );

                redirect(new \moodle_url('/portfolio/add.php', $exportparams));
            }
        }
    }

    /**
     * Processes any view specific actions and entry actions.
     * The view will remember processed entry ids for later use.
     *
     * @return void
     */
    public function process_data() {
        // Process portfolio export request if any.
        $this->process_portfolio_export();;

        // New/update entries request.
        if ($editentries = optional_param('editentries', '', PARAM_TAGLIST)) {
            $this->editentries = $editentries;
            return;
        }

        // Process entries data.
        if ($processed = $this->process_entries_data()) {
            list($strnotify, $processedeids) = $processed;

            // Redirect base url.
            $redirecturl = $this->baseurl;
            $redirecturl->remove_params('eids');
            $redirecturl->remove_params('editentries');

            // TODO: handle filter removal if necessary.
            // $redirecturl->remove_params('filter');

            $response = $strnotify;
            $timeout = 0;

            // Are we returning to form?
            if ($editentries = $this->editentries) {
                if ($processedeids) {
                    $processedentries = implode(',', $processedeids);
                    // If we continue editing the same entries, simply return.
                    if ($processedentries == $editentries) {
                        return;
                    }
                }

                // Otherwise, redirect to same view with new editentries param.
                $redirecturl->param('editentries', $editentries);
                redirect($redirecturl, $response, $timeout);
            }

            // We are not returning to form, so we need to apply redirection settings if any.
            $submission = $this->submission_settings;
            $timeout = !empty($submission['timeout']) ? $submission['timeout'] : 0;
            if (!empty($submission['redirect'])) {
                $redirecturl->param('view', $submission['redirect']);
            }

            if ($processedeids) {
                // Submission response.
                if (!empty($submission['message'])) {
                    $response =  $submission['message'];
                }

                // Display after if set and not returning to form.
                if (!empty($submission['displayafter'])) {
                    $redirecturl->param('eids', implode(',', $processedeids));
                }
            }

            redirect($redirecturl, $response, $timeout);
        }
    }

    /**
     * Returns the view html to display.
     *
     * @param array $options An array of display options
     * @return string HTML fragment
     */
    public function display(array $options = array()) {
        // Trigger an event for accessing this view.
        $event = \mod_dataform\event\view_viewed::create($this->default_event_params);
        $event->add_record_snapshot('dataform_views', $this->data);
        $event->trigger();

        // Set content.
        $filter = !empty($options['filter']) ? $options['filter'] : $this->filter->clone;
        if ($this->user_is_editing() and !$this->in_edit_display_mode()) {
            // Display only the edited entries.
            $filter->eids = $this->editentries;
        }
        $options['filter'] = $filter;
        $this->set_entries_content($options);

        // Rewrite plugin file url.
        $pluginfileurl = isset($options['pluginfileurl']) ? $options['pluginfileurl'] : null;
        $this->rewrite_pluginfile_urls($pluginfileurl);

        // Complie the view template.
        $viewhtml = $this->compile_view_template($options);
        return $viewhtml;
    }

    /**
     * Fetches target entries from database.
     *
     * @return void
     */
    protected function set_entries_content(array $options) {
        $this->entry_manager->set_content($options);
        // Adjust page in case changed by selection method (e.g. random selection).
        $this->filter->page = $this->entry_manager->page;
    }

    /**
     *
     */
    protected function compile_view_template($options) {
        $formatoptions = array('para' => false, 'allowid' => true, 'trusted' => true, 'noclean' => true);
        $html = format_text($this->section, FORMAT_HTML, $formatoptions);

        // Replace view patterns including entry field patterns.
        if ($patterns = $this->get_pattern_set('view')) {
            $replacements = $this->renderer->get_replacements($patterns, null, $options);
            $html = str_replace(array_keys($replacements), $replacements, $html);
        }

        // Process calculations.
        $html = $this->process_calculations($html);

        $dataformviewtype = "dataformview-$this->type";
        $viewname = str_replace(' ', '_', $this->name);
        return \html_writer::tag('div', $html, array('class' => "$dataformviewtype $viewname"));

    }

    // GETTERS.

    /**
     * Returns the view instance (DB) data.
     *
     * @return stdClass
     */
    public function get_data() {
        return $this->_view;
    }

    /**
     * Returns the type name of the view
     */
    public function get_typename() {
        return get_string('pluginname', "dataformview_{$this->type}");
    }

    /**
     * Returns a menu list of visibility modes.
     *
     * @return array
     */
    public static function get_visibility_modes() {
        return array(
            self::VISIBILITY_DISABLED => get_string('viewdisabled', 'dataform'),
            self::VISIBILITY_VISIBLE => get_string('viewvisible', 'dataform'),
            self::VISIBILITY_HIDDEN => get_string('viewhidden', 'dataform'),
        );
    }

    /**
     * Returns the view component
     *
     * @return string
     */
    public function get_component() {
        return "dataformview_$this->type";
    }

    /**
     * Returns the parent dataform
     */
    public function get_df() {
        return \mod_dataform_dataform::instance($this->dataid);
    }

    /**
     *
     */
    public function get_filter() {
        if (!$this->_filter) {
            $this->set_viewfilter();
        }
        return $this->_filter;
    }

    /**
     * Returns the view cached patterns as an array
     * 'view' => array(pattern, pattern, ...)
     * 'field' => array(fieldid => array(pattern, pattern, ...), ...)
     *
     * @return array
     */
    public function get_patterns() {
        if (!empty($this->_view->patterns)) {
            // Unserialize if not done yet.
            if (!is_array($this->_view->patterns)) {
                $this->_view->patterns = unserialize($this->_view->patterns);
            }
            return $this->_view->patterns;
        }

        return null;
    }

    /**
     * Returns the view submission settings.
     *
     * @return array
     */
    public function get_submission_settings() {
        if (!empty($this->_view->submission)) {
            // Unserialize if not done yet.
            if (!is_array($this->_view->submission)) {
                $this->_view->submission = unserialize(base64_decode($this->_view->submission));
            }
            return $this->_view->submission;
        }

        return null;
    }

    /**
     *
     */
    public function get_baseurl() {
        $filter = $this->filter;

        if (!$this->_baseurl) {
            $baseurlparams = array();
            $baseurlparams['d'] = $this->dataid;
            $baseurlparams['view'] = $this->id;
            if ($filter->id) {
                $baseurlparams['filter'] = $filter->id;
            }
            if ($filter->eids) {
                $eids = is_array($filter->eids) ? implode(',', $filter->eids) : $filter->eids;
                $baseurlparams['eids'] = $eids;
            }
            if ($filter->page) {
                $baseurlparams['page'] = $filter->page;
            }
            if ($this->df->currentgroup) {
                $baseurlparams['currentgroup'] = $this->df->currentgroup;
            }
            $pagefile = $this->df->pagefile;
            $this->_baseurl = new \moodle_url("/mod/dataform/$pagefile.php", $baseurlparams);
        }
        return $this->_baseurl;
    }

    /**
     *
     */
    public function get_editors() {
        return $this->_editors;
    }

    /**
     *
     */
    public function get_editoroptions() {
        return array(
            'trusttext' => true,
            'noclean' => true,
            'noclean' => true,
            'subdirs' => false,
            'changeformat' => true,
            'collapsed' => true,
            'rows' => 5,
            'style' => 'width:100%',
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $this->df->course->maxbytes,
            'context' => $this->df->context
        );
    }

    /**
     * Returns the view's dataform-entries manager.
     *
     * @return mod_dataform_entry_manager
     */
    public function get_entry_manager() {
        return \mod_dataform_entry_manager::instance($this->dataid, $this->id);
    }

    /**
     * Returns the view's patterns (renderer) object.
     *
     * @return dataformviewpatterns|dataformviewpatterns_$type
     */
    public function get_renderer() {
        global $CFG;

        if (!$this->_renderer) {
            $type = $this->type;

            $patternsclass = "dataformview_{$type}_patterns";
            if (!class_exists($patternsclass)) {
                $patternsclass = 'mod_dataform\pluginbase\dataformviewpatterns';
            }
            $this->_renderer = new $patternsclass($this);
        }
        return $this->_renderer;
    }

    /**
     * Gets the list of edited entries. A negative number of editing new entries,
     * or comma delimited list of entry ids.
     *
     * @return int|string
     */
    public function get_editentries() {
        return $this->_editentries;
    }

    /**
     * Sets the list of edited entries. A negative number of editing new entries,
     * or comma delimited list of entry ids.
     *
     * @param int|string $value
     * @return void
     */
    public function set_editentries($value) {
        $this->_editentries = $value;
    }

    /**
     * Gets the list of processed entries as a comma delimited list of entry ids.
     *
     * @return string
     */
    public function get_processedentries() {
        return $this->_processedentries;
    }

    /**
     * Sets the list of processed entries as a comma delimited list of entry ids.
     *
     * @param tring $value
     * @return void
     */
    public function set_processedentries($value) {
        $this->_processedentries = $value;
    }

    /**
     * Returns default params for field events.
     * These params can be extended or overriden where the event is created.
     *
     * @return array
     */
    public function get_default_event_params() {
        return array(
            'objectid' => $this->id,
            'context' => $this->df->context,
            'other' => array(
                'dataid' => $this->dataid,
                'viewid' => $this->id,
            )
        );
    }

    // ATTRIBUTES.

    /**
     *
     */
    public function is_active() {
        return (optional_param('view', 0, PARAM_INT) == $this->id);
    }

    /**
     *
     */
    public function is_forcing_filter() {
        return $this->filterid;
    }

    /**
     *
     */
    public function user_is_editing() {
        if ($this->editentries and $this->allows_submission()) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if there is at least one submission button enabled.
     *
     * @return bool
     */
    public function allows_submission() {
        if (!$submission = $this->submission_settings) {
            return false;
        }

        $allowed = false;

        // Save buttons.
        foreach ($this->submission_buttons as $name) {
            if ($name == 'cancel') {
                continue;
            }
            if (array_key_exists($name, $submission)) {
                $allowed = true;
                break;
            }
        }

        return $allowed;
    }

    /**
     * Returns true if in the specified editing mode. When not in display mode
     * only the edited entries are displayed (default). It requires that
     * that submission is allowed for the view.
     *
     * @param int Edit display mode
     * @return bool
     */
    public function in_edit_display_mode($mode = null) {
        if (!$submission = $this->submission_settings) {
            return false;
        }
        if (empty($submission['display'])) {
            return false;
        }
        if (!$mode) {
            return true;
        }
        return ($submission['display'] == $mode);
    }

    /**
     * Returns list of entry ids to continue editing if required by the view submission settings.
     * This is the default display mode in the submission settings. It requires that
     * that submission is allowed for the view.
     *
     * @param stdClass $data Form data
     * @param string Comma delimited list of entry ids recently processed
     * @return string Comma delimited list of entry ids
     */
    public function continue_editing($data, $processedids) {
        foreach ($data as $var => $unused) {
            if (strpos($var, 'submitbutton') !== 0) {
                continue;
            }
            list(, $cont) = explode('_', $var);
            if ($cont == 'savecont' or $cont == 'savenewcont') {
                return implode(',', $processedids);
            }
            if ($cont == 'savecontnew') {
                return -count($processedids);
            }
        }

        return null;
    }

    // HELPERS.
    /**
     * Returns a list of fields registered in the view templates.
     * Fields may be excluded from the returned list by passing
     * an array of field ids to exclude via option 'exclude'.
     *
     * @param array $options
     * @return array
     */
    public function get_fields(array $options = null) {

        if ($fieldpatterns = $this->get_pattern_set('field')) {
            $fieldids = array_keys($fieldpatterns);
            if (!empty($options['exclude'])) {
                $fieldids = array_diff($fieldids, $options['exclude']);
            }
            return $this->df->field_manager->get_fields_by_id($fieldids);
        }

        return array();
    }

    /**
     * Generates character patterns menu.
     *
     * @return array
     */
    public function get_character_patterns_menu() {
        $patterns = array(
            '9' => 'tab',
            '10' => 'new line'
        );

        return $patterns;
    }

    /**
     * Returns a list of broken or suspect invalid patterns
     * in the templates and cached patterns.
     *
     * @return array
     */
    public function patterns_check() {
        $patterns = $this->get_patterns_from_templates();
        $storedpatterns = $this->patterns;

        // View patterns.
        $viewupdates = array();
        if (!empty($patterns['view']) or !empty($storedpatterns['view'])) {
            // Stored.
            if (!$viewupdates and !empty($patterns['view']) and empty($storedpatterns['view'])) {
                foreach ($patterns['view'] as $pattern) {
                    $viewupdates[] = array(
                        'pattern' => $pattern,
                        'type' => 'view',
                        'problem' => 'Not cached',
                        'dataform' => $this->df->name,
                        'view' => $this->name,
                    );
                }
            }

            if (!$viewupdates and empty($patterns['view']) and !empty($storedpatterns['view'])) {
                foreach ($storedpatterns['view'] as $pattern) {
                    $viewupdates[] = array(
                        'pattern' => $pattern,
                        'type' => 'view',
                        'problem' => 'Not used',
                        'dataform' => $this->df->name,
                        'view' => $this->name,
                    );
                }
            }

            if (!$viewupdates and $notstored = array_diff($patterns['view'], $storedpatterns['view'])) {
                foreach ($notstored as $pattern) {
                    $viewupdates[] = array(
                        'pattern' => $pattern,
                        'type' => 'view',
                        'problem' => 'Not cached',
                        'dataform' => $this->df->name,
                        'view' => $this->name,
                    );
                }
            }
        }

        // Field patterns.
        $fieldupdates = array();
        if (!empty($patterns['field']) or !empty($storedpatterns['field'])) {
            if (!$fieldupdates and !empty($patterns['field']) and empty($storedpatterns['field'])) {
                foreach ($patterns['field'] as $fieldid => $fieldpatterns) {
                    foreach ($fieldpatterns as $pattern) {
                        $fieldupdates[] = array(
                            'pattern' => $pattern,
                            'type' => 'field',
                            'problem' => 'Not used',
                            'dataform' => $this->df->name,
                            'view' => $this->name,
                        );
                    }
                }
            }

            if (!$fieldupdates and empty($patterns['field']) and !empty($storedpatterns['field'])) {
                foreach ($storedpatterns['field'] as $fieldid => $fieldpatterns) {
                    foreach ($fieldpatterns as $pattern) {
                        $fieldupdates[] = array(
                            'pattern' => $pattern,
                            'type' => 'field',
                            'problem' => 'Not used',
                            'dataform' => $this->df->name,
                            'view' => $this->name,
                        );
                    }
                }
            }

            if (!$fieldupdates) {
                foreach ($patterns['field'] as $fieldid => $fieldpatterns) {
                    if (empty($storedpatterns['field'][$fieldid])) {
                        foreach ($fieldpatterns as $pattern) {
                            $fieldupdates[] = array(
                                'pattern' => $pattern,
                                'type' => 'field',
                                'problem' => 'Not used',
                                'dataform' => $this->df->name,
                                'view' => $this->name,
                            );
                        }
                    } else if ($notstored = array_diff($patterns['field'][$fieldid], $storedpatterns['field'][$fieldid])) {
                        foreach ($notstored as $pattern) {
                            $fieldupdates[] = array(
                                'pattern' => $pattern,
                                'type' => 'field',
                                'problem' => 'Not cached',
                                'dataform' => $this->df->name,
                                'view' => $this->name,
                            );
                        }
                    }
                }
            }

        }

        // Anything else that looks like a pattern.
        $otherupdates = array();
        // Get the templates text.
        $text = $this->get_templates_text();
        // Remove found patterns from text.
        if (!empty($patterns['view'])) {
            $text = str_replace($patterns['view'], '', $text);
        }
        if (!empty($patterns['field'])) {
            foreach ($patterns['field'] as $fieldpatterns) {
                $text = str_replace($fieldpatterns, '', $text);
            }
        }
        // Find patterns of form ##...## and [[...]].
        preg_match_all("/##[^#]+##|\[\[[^\]]+\]\]/", $text, $matches);
        if (!empty($matches[0])) {
            foreach ($matches[0] as $pattern) {
                $otherupdates[] = array(
                    'pattern' => $pattern,
                    'type' => 'Unknown',
                    'problem' => '',
                    'dataform' => $this->df->name,
                    'view' => $this->name,
                );
            }
        }

        if ($updates = array_merge($viewupdates, $fieldupdates, $otherupdates)) {
            return $updates;
        }

        return null;
    }

    /**
     *
     */
    public function rewrite_pluginfile_urls($pluginfileurl = null) {
        foreach ($this->editors as $editor) {
            // Export with files should provide the file path.
            if ($pluginfileurl) {
                $this->$editor = str_replace('@@PLUGINFILE@@/', $pluginfileurl, $this->$editor);
            } else {
                $this->$editor = file_rewrite_pluginfile_urls($this->$editor,
                                                                'pluginfile.php',
                                                                $this->df->context->id,
                                                                $this->component,
                                                                $editor,
                                                                $this->id);
            }
        }
    }

    /**
     *
     */
    public function get_pattern_set($set = null) {
        // Must have patterns.
        if (!$patterns = $this->patterns) {
            return null;
        }

        // BC convert internal field ids where needed.
        if (!empty($patterns['field'])) {
            $patterns['field'] = $this->convert_internal_field_pattern_ids($patterns['field']);
            $this->patterns = $patterns;
        }

        if (is_null($set)) {
            return $patterns;
        } else if (array_key_exists($set, $patterns)) {
            return $patterns[$set];
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function get_pattern_fieldid($pattern) {
        if ($fieldpatterns = $this->get_pattern_set('field')) {
            foreach ($fieldpatterns as $fieldid => $patterns) {
                if (in_array($pattern, $patterns)) {
                    return $fieldid;
                }
            }
        }
        return null;
    }

    /**
     *
     */
    public function get_embedded_files($set = null) {
        $files = array();
        $fs = get_file_storage();

        // View files.
        if (empty($set) or $set == 'view') {
            foreach ($this->editors as $editor) {
                $files = array_merge($files, $fs->get_area_files($this->df->context->id,
                                                                $this->component,
                                                                $editor,
                                                                $this->id,
                                                                'sortorder, itemid, filepath, filename',
                                                                false));
            }
        }

        // Field files.
        if (empty($set) or $set == 'field') {
            // Find which fields actually display files/images in the view.
            $fids = array();
            if ($fieldpatterns = $this->get_pattern_set('field')) {
                $fields = $this->get_fields();
                foreach ($fieldpatterns as $fieldid => $patterns) {
                    if (array_intersect($patterns, $fields[$fieldid]->renderer->pluginfile_patterns())) {
                        $fids[] = $fieldid;
                    }
                }
            }
            // Get the files from the entries.
            if ($this->entry_manager->entries and !empty($fids)) {  // Set_content must have been called
                $files = array_merge($files, $this->entry_manager->get_embedded_files($fids));
            }
        }

        return $files;
    }

    // TEMPLATES.

    /**
     * Generates a default view for a new view instance or when reseting an existing instance.
     * View subtypes may need to override.
     *
     * @return void
     */
    public function generate_default_view() {
        // Set the view template.
        $this->set_default_view_template();

        // Set the entry template.
        $this->set_default_entry_template();

        // Set default submission settings.
        $settings = array(
            'save' => '',
            'cancel' => '',
            'timeout' => 1,
        );
        $this->submission = base64_encode(serialize($settings));
        $this->visible = 1;

    }

    /**
     * Generates the default view template for a new view instance or when reseting an existing instance.
     * If content is specified, sets the template to the content.
     * View subtypes may need to override.
     *
     * @param string $content HTML fragment.
     * @return void
     */
    public function set_default_view_template($content = null) {
        if ($content === null) {
            // Notifications.
            $notifications = \html_writer::tag('div', '##notifications##', array('class' => ''));

            // Add new entry.
            $addnewentry = \html_writer::tag('div', '##addnewentry##', array('class' => 'addnewentry-wrapper'));

            // Filtering.
            $quickfilters = \html_writer::tag('div', $this->get_default_filtering_template(), array('class' => 'quickfilters-wrapper'));

            // Paging bar.
            $pagingbar = \html_writer::tag('div', '##paging:bar##', array('class' => ''));
            // Entries.
            $entries = \html_writer::tag('div', '##entries##', array('class' => ''));

            // Set the view template.
            $exporthide = \html_writer::tag('div', $addnewentry. $quickfilters. $pagingbar, array('class' => 'exporthide'));

            $content = \html_writer::tag('div', $exporthide. $entries);
        }
        $this->section = $content;
    }

    /**
     * Generates the default entry template for a new view instance or when reseting an existing instance.
     * View subtypes need to override.
     *
     * @return void
     */
    public function set_default_entry_template($content = null) {
    }

    /**
     * @return string HTML fragment
     */
    protected function get_default_filtering_template() {
        $filtersmenulabel = \html_writer::label(get_string('filtercurrent', 'dataform'), 'filterbrowse_jump');
        $filtersmenu = \html_writer::tag('div', "$filtersmenulabel ##filtersmenu##", array('class' => 'quickfilter'));
        $quicksearchlabel = \html_writer::label(get_string('search', 'dataform'), 'usearch');
        $quicksearch = \html_writer::tag('div', "$quicksearchlabel ##quicksearch##", array('class' => 'quickfilter'));
        $quickperpagelabel = \html_writer::label(get_string('filterperpage', 'dataform'), 'perpage_jump');
        $quickperpage = \html_writer::tag('div', "$quickperpagelabel ##quickperpage##", array('class' => 'quickfilter'));
        $clearfix = \html_writer::tag('div', null, array('class' => 'clearfix'));
        return $filtersmenu. $quicksearch. $quickperpage. $clearfix;
    }

    /**
     *
     */
    protected function get_field_definitions($entry, $options) {
        $fields = $this->get_fields();
        $entry->baseurl = new \moodle_url($this->baseurl);

        if (!$fieldpatterns = $this->get_pattern_set('field')) {
            return array();
        }

        // HACK Adding [[entryid]] here so that it is available for display.
        $definitions = array('[[entryid]]' => $entry->id);
        foreach ($fieldpatterns as $fieldid => $patterns) {
            if (!isset($fields[$fieldid])) {
                continue;
            }
            $field = $fields[$fieldid];
            if ($fielddefinitions = $field->get_definitions($patterns, $entry, $options)) {
                $definitions = array_merge($definitions, $fielddefinitions);
            }
        }
        return $definitions;
    }

    /**
     * @param array $patterns array of arrays of pattern replacement pairs
     */
    protected function split_tags($patterns, $subject) {
        $delims = implode('|', $patterns);
        // Escape [ and ] and the pattern rule character *.
        $delims = quotemeta($delims);

        $elements = preg_split("/($delims)/", $subject, null, PREG_SPLIT_DELIM_CAPTURE);

        return $elements;
    }

    // VIEW ENTRIES.
    /**
     *
     */
    public function process_entries_data() {
        $strnotify = '';
        $processedeids = null;

        $entryman = $this->entry_manager;

        // Direct url params; not from form
        // The following actions may require confirmation.
        $confirmed = optional_param('confirmed', 0, PARAM_BOOL);

        // Duplicate any requested entries (comma delimited eids).
        if ($duplicate = optional_param('duplicate', '', PARAM_SEQUENCE)) {
            if (confirm_sesskey()) {
                return $entryman->process_entries('duplicate', $duplicate, null, $confirmed);
            }
            return null;
        }

        // Delete any requested entries (comma delimited eids).
        if ($delete = optional_param('delete', '', PARAM_SEQUENCE)) {
            if (confirm_sesskey()) {
                return $entryman->process_entries('delete', $delete, null, $confirmed);
            }
            return null;
        }

        // Check if returning from form.
        if ($update = optional_param('update', '', PARAM_TAGLIST)) {
            if (confirm_sesskey()) {
                // Check if returning from cancelled form.
                $entriesform = $this->get_entries_form();
                if ($entriesform->is_cancelled()) {
                    $this->editentries = '';
                    return array(null, null);
                }

                $this->editentries = $update;
                // Get entries only if updating existing entries.
                if ($update != -1) {
                    $filter = $this->filter->clone;
                    $filter->eids = explode(',', $update);
                    $entryman->set_content(array('filter' => $filter));
                    $elements = $this->get_entries_definition($entryman->entries);
                } else {
                    $elements = $this->get_entries_definition(array());
                }

                // Get the form.
                $entriesform = $this->get_entries_form(array('elements' => $elements));

                // Process the form if not cancelled
                // HACK Work around MDL-44446: get_data returns null if no input is submitted.
                if ($entriesform->is_submitted() and $entriesform->is_validated()) {
                    if (!$data = $entriesform->get_data()) {
                        $data = new \stdClass;
                    }
                    // Validated successfully so process request.
                    list($strnotify, $processedeids) = $entryman->process_entries('update', $update, $data, true);

                    $this->editentries = $this->continue_editing($data, $processedeids);
                    return array($strnotify, $processedeids);
                }
            }
            return null;
        }

        return null;
    }

    /**
     * Returns the html for entries displays. Assumes that {@link mod_dataform_entry_manager::set_content()}
     * has been called for this view and its entries property is set in accordance with the display mode
     * of the view submission settings.
     *
     * @return string HTML fragment
     */
    public function get_entries_display(array $options = null) {
        $html = '';
        $entryman = $this->entry_manager;
        $editing = $this->user_is_editing();

        if ($editing) {
            if ($this->in_edit_display_mode(self::EDIT_SEPARATE)) {
                // Extract the edited and non-edited entries from the set.
                $editentries = explode(',', $this->editentries);
                $edited = array();
                $nonedited = array();

                foreach ($entryman->entries as $entryid => $entry) {
                    if (in_array($entryid, $editentries)) {
                        $edited[$entryid] = $entry;
                    } else {
                        $nonedited[$entryid] = $entry;
                    }
                }

                // Get the form html for the edited entries.
                if ($elements = $this->get_entries_definition($edited)) {
                    if ($this->editentries) {
                        $entriesform = $this->get_entries_form(array('elements' => $elements));
                        $html .= $entriesform->render();
                    } else {
                        // Turns out that all requested edits are not allowed.
                        $html .= implode('', $elements);
                    }
                }

                // Add the rest.
                $elements = $this->get_entries_definition($nonedited, false);
                $html .= implode('', $elements);

            } else {
                // Get the form html for all entries.
                if ($elements = $this->get_entries_definition($entryman->entries)) {
                    if ($this->editentries) {
                        $entriesform = $this->get_entries_form(array('elements' => $elements));
                        $html .= $entriesform->render();
                    } else {
                        // Turns out that all requested edits are not allowed.
                        $html .= implode('', $elements);
                    }
                }
            }

            return $html;
        }

        // Not editing so fetch the entries and display html.
        $elements = $this->get_entries_definition($entryman->entries, false);
        $html .= implode('', $elements);

        // RM ??? Replace pluginfile urls if needed (e.g. in export).
        $pluginfileurl = isset($options['pluginfileurl']) ? $options['pluginfileurl'] : null;
        if ($pluginfileurl) {
            $pluginfilepath = \moodle_url::make_file_url("/pluginfile.php", "/{$this->df->context->id}/mod_dataform/content");
            $pattern = str_replace('/', '\/', $pluginfilepath);
            $pattern = "/$pattern\/\d+\//";
            $html = preg_replace($pattern, $pluginfileurl, $html);
        }
        return $html;
    }

    /**
     * Returns array of html fragments and/or function calls that constitute the
     * display definition of the entries.
     *
     * @param bool $allowedit Set to true to allow editing definitions
     * @return array
     */
    public function get_entries_definition($entries, $allowedit = true) {
        $groupedelements = array();

        foreach ($this->get_display_definition($entries) as $name => $entriesset) {
            $definitions = array();
            // New entry set.
            if ($name == 'newentry' and $allowedit) {
                foreach ($entriesset as $entryid => $unused) {
                    $definitions[$entryid] = $this->new_entry_definition($entryid);
                }
                $groupedelements[$name] = $this->group_entries_definition($definitions, $name);
                continue;
            }

            // Existing entries set.
            foreach ($entriesset as $entryid => $editthisone) {
                if (!empty($entries[$entryid])) {
                    $options = $allowedit ? array('edit' => $editthisone) : null;
                    $fielddefinitions = $this->get_field_definitions($entries[$entryid], $options);
                    $definitions[$entryid] = $this->entry_definition($fielddefinitions, $options);
                }
            }
            $groupedelements[$name] = $this->group_entries_definition($definitions, $name);
        }
        // Free up memory.
        unset($definitions);
        unset($entrieset);

        // Flatten the elements.
        $elements = array();
        foreach ($groupedelements as $group) {
            $elements = array_merge($elements, $group);
            // Free up memory.
            unset($group);
        }

        return $elements;
    }

    /**
     * Returns the entries form.
     * Optional options:
     * - Array $elements Html and function calls for form definition.
     * - Comma delimited list of entry ids to edit.
     *
     * @param array $options
     * @return dataformview[_viewtype]_entries_form
     */
    public function get_entries_form(array $options = null) {
        global $CFG;

        $filter = $this->filter;
        $editentries = !empty($options['editentries']) ? $options['editentries'] : $this->editentries;

        // Prepare params for form.
        $actionparams = array(
            'd' => $this->dataid,
            'view' => $this->id,
            'filtid' => $filter->id,
            'page' => $filter->page,
            'update' => $editentries
        );
        if ($filter->eids) {
            $actionparams['eids'] = $editentries;
        }

        $pagefile = $this->df->pagefile;
        $actionurl = new \moodle_url("/mod/dataform/$pagefile.php", $actionparams);
        $customdata = array(
            'view' => $this,
            'update' => $editentries
        );

        // Pass elements if given.
        if (!empty($options['elements'])) {
            $customdata['elements'] = $options['elements'];
        }

        $formclass = $this->get_entries_form_class();
        return new $formclass($actionurl, $customdata, 'post', '', array('class' => 'entriesform'));
    }

    /**
     * Returns the entries form for one new entry.
     *
     * @return dataformview[_viewtype]_entries_form
     */
    public function get_new_entry_form() {
        $elements = $this->new_entry_definition();
        // Wrap with entriesview.
        array_unshift($elements, \html_writer::start_tag('div', array('class' => 'entriesview')));
        array_push($elements, \html_writer::end_tag('div'));

        $options = array('elements' => $elements, 'editentries' => -1);
        return $this->get_entries_form($options);
    }

    /**
     *
     */
    protected function process_calculations($text) {
        global $CFG;

        // HACK removing occurences of [[entryid]] because they are
        // currently not resolved in new entries.
        $text = str_replace('[[entryid]]', '', $text);

        if (preg_match_all("/%%F\d*:=[^%]*%%/", $text, $matches)) {
            require_once("$CFG->libdir/mathslib.php");
            sort($matches[0]);

            // List of formulas.
            $formulas = array();
            // Formula replacements.
            $replacements = array();

            // Register all formulas according to formula identifier.
            foreach ($matches[0] as $pattern) {
                $cleanpattern = trim($pattern, '%');
                list($fid, $formula) = explode(':=', $cleanpattern, 2);
                // Skip an empty formula.
                if (empty($formula) and $formula !== 0) {
                    continue;
                }
                isset($formulas[$fid]) or $formulas[$fid] = array();
                // Enclose formula in brackets to preserve precedence.
                $formulas[$fid][] = "($formula)";
                $replacements[$pattern] = $formula;
            }

            // Process group formulas in formulas (e.g. _F1_).
            // We put 0 if we can't find a replacement so that the formula could be calculated.
            foreach ($formulas as $fid => $formulae) {
                foreach ($formulae as $key => $formula) {
                    if (preg_match_all("/_F\d+_/", $formula, $frefs)) {
                        foreach ($frefs[0] as $fref) {
                            $fref = trim($fref, '_');
                            if (isset($formulas[$fref])) {
                                $replace = implode(',', $formulas[$fref]);
                            } else {
                                $replace = 0;
                            }
                            $formula = str_replace("_{$fref}_", $replace, $formula);
                        }
                        $formulae[$key] = $formula;
                    }
                }
                $formulas[$fid] = $formulae;
            }

            // Process group formulas in replacements (e.g. _F1_).
            // We put 0 if we can't find a replacement so that the formula could be calculated.
            foreach ($replacements as $pattern => $formula) {
                if (preg_match_all("/_F\d+_/", $formula, $frefs)) {
                    foreach ($frefs[0] as $fref) {
                        $fref = trim($fref, '_');
                        if (isset($formulas[$fref])) {
                            $replace = implode(',', $formulas[$fref]);
                        } else {
                            $replace = 0;
                        }
                        $formula = str_replace("_{$fref}_", $replace, $formula);
                    }
                    $replacements[$pattern] = $formula;
                }
            }

            // Calculate.
            foreach ($replacements as $pattern => $formula) {
                // Number of decimals can be set as ;n at the end of the formula.
                $decimals = null;
                if (strpos($formula, ';')) {
                    list($formula, $decimals) = explode(';', $formula);
                }
                $calc = new \calc_formula("=$formula");
                $result = $calc->evaluate();
                if ($result === false) {
                    // False as result indicates some problem.
                    // We remove the formula altogether.
                    $replacements[$pattern] = '';
                } else {
                    // Set decimals.
                    if (is_numeric($decimals)) {
                        $result = sprintf("%4.{$decimals}f", $result);
                    }
                    $replacements[$pattern] = $result;
                }
            }

            $text = str_replace(array_keys($replacements), $replacements, $text);
        }
        return $text;
    }

    /**
     *
     */
    protected function group_entries_definition($entriesset, $name = '') {
        return array();
    }

    /**
     *
     */
    protected function new_entry_definition($entryid = -1) {
        return array();
    }

    /**
     *
     */
    protected function entry_definition($fielddefinitions, array $options = null) {
        return array();
    }

    /**
     *
     */
    protected function get_display_definition(array $entries = null) {

        $displaydefinition = array();
        $editnewentries = null;
        $editentries = null;

        // Display a new entry to add in its own group.
        if ($this->editentries < 0) {
            $editnewentries = $this->editentries;
            $accessparams = array('dataformid' => $this->dataid, 'viewid' => $this->id);
            if (\mod_dataform\access\entry_add::validate($accessparams)) {
                $displaydefinition['newentry'] = array();
                for ($i = -1; $i >= $editnewentries; $i--) {
                    $displaydefinition['newentry'][$i] = null;
                }
            } else {
                $editnewentries = null;
            }
        } else if ($this->editentries) {
            $editentries = explode(',', $this->editentries);
            $editentries = array_combine($editentries, $editentries);
        }

        // Compile entries if any.
        if ($entries) {
            $groupname = '';
            $groupdefinition = array();

            foreach ($entries as $entryid => $entry) {
                // Is this entry edited.
                if ($editthisone = $editentries ? !empty($editentries[$entryid]) : false) {
                    $accessparams = array('dataformid' => $this->dataid, 'viewid' => $this->id);
                    if (!$editthisone = \mod_dataform\access\entry_update::validate($accessparams + array('entry' => $entry))) {
                        unset($editentries[$entryid]);
                    }
                }

                // Add to the current entries group.
                $groupdefinition[$entryid] = $editthisone;

            }
            // Collect remaining definitions (all of it if no groupby).
            $displaydefinition[$groupname] = $groupdefinition;
        }

        $this->editentries = $editentries ? implode(',', $editentries) : $editnewentries;

        return $displaydefinition;
    }

    /**
     *
     */
    protected function get_entries_form_class() {
        return 'mod_dataform\pluginbase\entriesform';
    }

    // SUBMISSION SETTINGS.

    /**
     * Returns list of submission button names.
     *
     * @return array Array of strings
     */
    public function get_submission_buttons() {
        return array(
            'save',
            'savecont',
            'savenew',
            'savecontnew',
            'savenewcont',
            'cancel'
        );
    }

    // HELPERS and BACKWARD COMPATIBILITY.

    /**
     *
     */
    private function convert_internal_field_pattern_ids($fieldpatterns) {
        $fieldids = array(
            'entry' => -1,
            'timecreated' => -4,
            'timemodified' => -4,
            'approve' => -5,
            'group' => -3,
            'userid' => -2,
            'username' => -2,
            'userfirstname' => -2,
            'userlastname' => -2,
            'userusername' => -2,
            'useridnumber' => -2,
            'userpicture' => -2,
            'comment' => -6,
            'rating' => -7,
            'ratingavg' => -7,
            'ratingcount' => -7,
            'ratingmax' => -7,
            'ratingmin' => -7,
            'ratingsum' => -7,
        );

        $newfieldpatterns = array();
        if (!empty($fieldpatterns)) {
            foreach ($fieldpatterns as $fieldid => $patterns) {
                if (!empty($fieldids[$fieldid])) {
                    $newfieldid = $fieldids[$fieldid];
                    if (empty($newfieldpatterns[$newfieldid])) {
                        $newfieldpatterns[$newfieldid] = array();
                    }
                    $newfieldpatterns[$newfieldid] = array_merge($newfieldpatterns[$newfieldid], $patterns);
                } else {
                    $newfieldpatterns[$fieldid] = $patterns;
                }
            }
        }
        return $newfieldpatterns;
    }

    // VIEW TYPE.

    /**
     * Insert a new view into the database
     * $this->view is assumed set
     */
    public function add($data) {
        global $DB;

        $this->set_view($data);

        if (!$viewid = $DB->insert_record('dataform_views', $this->data)) {
            return false;
        }

        $this->id = $viewid;

        // Update item id of files area.
        $fs = get_file_storage();
        $contextid = $this->df->context->id;
        $component = $this->component;
        foreach ($this::get_file_areas() as $filearea) {
            $files = $fs->get_area_files($contextid, $component, $filearea, 0);
            if (count($files) > 1) {
                foreach ($files as $file) {
                    $filerec = new \stdClass;
                    $filerec->itemid = $this->id;
                    $fs->create_file_from_storedfile($filerec, $file);
                }
            }
            $fs->delete_area_files($contextid, $component, $filearea, 0);
        }

        // Trigger an event for creating this view.
        $event = \mod_dataform\event\view_created::create($this->default_event_params);
        $event->add_record_snapshot('dataform_views', $this->data);
        $event->trigger();

        return $this->id;
    }

    /**
     * Update a view in the database
     * $this->view is assumed set
     */
    public function update($data) {
        global $DB;

        $this->set_view($data);

        if ($DB->update_record('dataform_views', $this->data)) {

            // Trigger an event for updating this view.
            $event = \mod_dataform\event\view_updated::create($this->default_event_params);
            $event->add_record_snapshot('dataform_views', $this->data);
            $event->trigger();

            return $this->id;
        }
        return false;
    }

    /**
     * Delete a view from the database
     */
    public function delete() {
        global $DB;

        if ($this->id) {
            $fs = get_file_storage();
            $contextid = $this->df->context->id;
            foreach ($this::get_file_areas() as $filearea) {
                $fs->delete_area_files($contextid, $this->component, $filearea, $this->id);
            }

            $DB->delete_records('dataform_views', array('id' => $this->id));

            // Trigger an event for deleting this view.
            $event = \mod_dataform\event\view_deleted::create($this->default_event_params);
            $event->add_record_snapshot('dataform_views', $this->data);
            $event->trigger();
        }

        return true;
    }

    /**
     * Insert a new copy of the view into the database
     *
     * @param string $name The name of the new copy; assumed unique.
     * @param dataformview $view The view to duplicate.
     */
    public function duplicate($name) {
        global $DB;

        $newview = clone($this->data);
        unset($newview->id);
        $newview->name = $name;
        // Make sure patterns are serialized.
        if ($newview->patterns and is_array($newview->patterns)) {
            $newview->patterns = serialize($newview->patterns);
        }

        if (!$newview->id = $DB->insert_record('dataform_views', $newview)) {
            return false;
        }

        // Duplicate view files.
        $fs = get_file_storage();
        $contextid = $this->df->context->id;
        foreach ($this::get_file_areas() as $filearea) {
            $files = $fs->get_area_files($contextid, $this->component, $filearea, $this->id);
            if (count($files) > 1) {
                foreach ($files as $file) {
                    $filerec = new \stdClass;
                    $filerec->itemid = $newview->id;
                    $fs->create_file_from_storedfile($filerec, $file);
                }
            }
        }

        // Trigger an event for duplicating this view.
        $eventparams = $this->default_event_params;
        $eventparams['objectid'] = $newview->id;
        $eventparams['other']['viewname'] = $name;
        $event = \mod_dataform\event\view_created::create($eventparams);
        $event->add_record_snapshot('dataform_views', $newview);
        $event->trigger();

        return $newview->id;
    }

    /**
     *
     */
    public function get_form() {
        global $CFG;

        $formclass = 'dataformview_'. $this->type. '_form';
        $formparams = array(
            'd' => $this->dataid,
            'vedit' => $this->id,
            'type' => $this->type
        );
        $actionurl = new \moodle_url('/mod/dataform/view/edit.php', $formparams);

        return new $formclass($this, $actionurl);
    }

    /**
     * prepare view data for form
     */
    public function to_form($data = null) {
        $data = $data ? $data : $this->data;

        // Prepare view editors.
        $data = $this->prepare_view_editors($data);

        return $data;
    }

    /**
     * prepare view data for form
     */
    public function from_form($data) {
        $data = $this->update_view_editors($data);

        return $data;
    }

    /**
     * Prepare view editors for form
     */
    public function prepare_view_editors($data) {
        $editoroptions = $this->editoroptions;

        foreach ($this->editors as $editor) {
            $data = file_prepare_standard_editor(
                $data,
                $editor,
                $editoroptions,
                $this->df->context,
                $this->component,
                $editor,
                $this->id
            );
        }
        return $data;
    }

    /**
     * Update view editors from form
     */
    public function update_view_editors($data) {
        $editoroptions = $this->editoroptions;
        $component = 'dataformview_'. $this->type;

        foreach ($this->editors as $editor) {
            $data = file_postupdate_standard_editor(
                $data,
                $editor,
                $editoroptions,
                $this->df->context,
                $component,
                $editor,
                $this->id
            );
        }

        return $data;
    }

    /**
     * Subclass may need to override
     */
    public function replace_patterns_in_view($patterns, $replacements) {
        foreach ($this->editors as $editor) {
            $this->$editor = str_replace($patterns, $replacements, $this->$editor);
        }
        $this->update($this->data);
    }

    /**
     * Returns the name/type of the view
     */
    public function name_exists($name, $viewid) {
        return $this->df->name_exists('views', $name, $viewid);
    }

    /**
     * @param string $feature FEATURE_xx constant for requested feature
     * @return mixed True if module supports feature, null if doesn't know
     */
    public function supports_feature($feature) {
        return null;
    }

    /**
     * Sets the view data from form data.
     *
     * @return bool Always true.
     */
    protected function set_view($data) {
        $data = $this->postpare_editors($data);

        $this->name = !empty($data->name) ? trim($data->name) : $this->type;
        $this->description = !empty($data->description) ? $data->description : '';
        $this->visible = !empty($data->visible) ? $data->visible : 0;
        $this->perpage = !empty($data->perpage) ? $data->perpage : 0;
        $this->groupby = !empty($data->groupby) ? $data->groupby : '';
        $this->filterid = !empty($data->filterid) ? $data->filterid : 0;
        $this->section = !empty($data->section) ? $data->section : null;

        for ($i = 1; $i <= 10; $i++) {
            $this->{"param$i"} = (!empty($data->{"param$i"}) ? $data->{"param$i"} : null);
        }

        // Compile view and field patterns.
        $this->patterns = null;
        if ($patterns = $this->get_patterns_from_templates()) {
            $this->patterns = serialize($patterns);
        }

        // Compile submission settings.
        if (empty($data->submission)) {
            $this->submission = null;
        } else if (is_array($data->submission)) {
            $this->submission = base64_encode(serialize($data->submission));
        }

        return true;
    }

    /**
     *
     */
    protected function prepare_editors() {
        foreach ($this->editors as $editor) {
            if ($editordata = $this->$editor) {
                if (strpos($editordata, 'ft:') === 0
                            and strpos($editordata, 'tr:') === 4
                            and strpos($editordata, 'ct:') === 8) {
                    $format = substr($editordata, 3, 1);
                    $trust = substr($editordata, 7, 1);
                    $text = substr($editordata, 11);
                } else {
                    list($format, $trust, $text) = array(FORMAT_HTML, 1, $editordata);
                }
            } else {
                list($format, $trust, $text) = array(FORMAT_HTML, 1, '');
            }
            $this->_view->{$editor.'format'} = $format;
            $this->_view->{$editor.'trust'} = $trust;
            $this->$editor = $text;
        }
    }

    /**
     *
     */
    protected function postpare_editors($data) {
        foreach ($this->editors as $editor) {
            $format = !empty($data->{$editor.'format'}) ? $data->{$editor.'format'} : FORMAT_HTML;
            $trust = !empty($data->{$editor.'trust'}) ? $data->{$editor.'trust'} : 1;
            $text = !empty($data->$editor) ? $data->$editor : '';

            // Replace \n in non text format.
            if ($format != FORMAT_PLAIN) {
                $text = str_replace("\n", "", $text);
            }

            if (!empty($text)) {
                $data->$editor = $text;
            } else {
                $data->$editor = null;
            }
        }
        return $data;
    }

    /**
     * Extracts view and field patterns from the view templates.
     * View and field patterns are extracted from editors and we assume
     * that they are already updated from the from data.
     *
     * $return array Array of patterns
     */
    protected function get_patterns_from_templates() {
        $patterns = array();

        // Extract view and field patterns from editor text.
        $text = $this->get_templates_text();
        if (trim($text)) {
            // This view patterns.
            if ($viewpatterns = $this->renderer->search($text)) {
                $patterns['view'] = $viewpatterns;
            }
            // Field patterns.
            if ($fields = $this->df->field_manager->get_fields(array('forceget' => true))) {
                foreach ($fields as $fieldid => $field) {
                    if ($fieldpatterns = $field->renderer->search($text)) {
                        if (empty($patterns['field'])) {
                            $patterns['field'] = array();
                        }
                        $patterns['field'][$fieldid] = $fieldpatterns;
                    }
                }
            }
        }

        return $patterns;
    }

    /**
     * Returns all templates text in one string.
     * View types which have templates that are not editors need to override.
     *
     * $return string
     */
    protected function get_templates_text() {
        $text = '';
        foreach ($this->editors as $editor) {
            $text .= $this->$editor ? ' '. $this->$editor : '';
        }
        return trim($text);
    }
}
