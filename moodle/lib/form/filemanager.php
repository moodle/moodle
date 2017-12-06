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
 * FileManager form element
 *
 * Contains HTML class for a filemanager form element
 *
 * @package   core_form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once('HTML/QuickForm/element.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');
require_once('templatable_form_element.php');

/**
 * Filemanager form element
 *
 * FilemaneManager lets user to upload/manage multiple files
 * @package   core_form
 * @category  form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_filemanager extends HTML_QuickForm_element implements templatable {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }

    /** @var string html for help button, if empty then no help will icon will be dispalyed. */
    public $_helpbutton = '';

    /** @var array options provided to initalize filemanager */
    // PHP doesn't support 'key' => $value1 | $value2 in class definition
    // We cannot do $_options = array('return_types'=> FILE_INTERNAL | FILE_REFERENCE);
    // So I have to set null here, and do it in constructor
    protected $_options = array('mainfile' => '', 'subdirs' => 1, 'maxbytes' => -1, 'maxfiles' => -1,
            'accepted_types' => '*', 'return_types' =>  null, 'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED);

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the filemanager
     * @param string $elementLabel (optional) filemanager label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param array $options set of options to initalize filemanager
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG, $PAGE;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $options['maxbytes']);
        }
        if (empty($options['return_types'])) {
            $this->_options['return_types'] = (FILE_INTERNAL | FILE_REFERENCE | FILE_CONTROLLED_LINK);
        }
        $this->_type = 'filemanager';
        parent::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_filemanager($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes, $options);
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    function onQuickFormEvent($event, $arg, &$caller)
    {
        switch ($event) {
            case 'createElement':
                $caller->setType($arg[0], PARAM_INT);
                break;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }

    /**
     * Sets name of filemanager
     *
     * @param string $name name of the filemanager
     */
    function setName($name) {
        $this->updateAttributes(array('name'=>$name));
    }

    /**
     * Returns name of filemanager
     *
     * @return string
     */
    function getName() {
        return $this->getAttribute('name');
    }

    /**
     * Updates filemanager attribute value
     *
     * @param string $value value to set
     */
    function setValue($value) {
        $this->updateAttributes(array('value'=>$value));
    }

    /**
     * Returns filemanager attribute value
     *
     * @return string
     */
    function getValue() {
        return $this->getAttribute('value');
    }

    /**
     * Returns maximum file size which can be uploaded
     *
     * @return int
     */
    function getMaxbytes() {
        return $this->_options['maxbytes'];
    }

    /**
     * Sets maximum file size which can be uploaded
     *
     * @param int $maxbytes file size
     */
    function setMaxbytes($maxbytes) {
        global $CFG, $PAGE;
        $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $maxbytes);
    }

    /**
     * Returns the maximum size of the area.
     *
     * @return int
     */
    function getAreamaxbytes() {
        return $this->_options['areamaxbytes'];
    }

    /**
     * Sets the maximum size of the area.
     *
     * @param int $areamaxbytes size limit
     */
    function setAreamaxbytes($areamaxbytes) {
        $this->_options['areamaxbytes'] = $areamaxbytes;
    }

    /**
     * Returns true if subdirectoy can be created, else false
     *
     * @return bool
     */
    function getSubdirs() {
        return $this->_options['subdirs'];
    }

    /**
     * Set option to create sub directory, while uploading  file
     *
     * @param bool $allow true if sub directory can be created.
     */
    function setSubdirs($allow) {
        $this->_options['subdirs'] = $allow;
    }

    /**
     * Returns maximum number of files which can be uploaded
     *
     * @return int
     */
    function getMaxfiles() {
        return $this->_options['maxfiles'];
    }

    /**
     * Sets maximum number of files which can be uploaded.
     *
     * @param int $num number of files
     */
    function setMaxfiles($num) {
        $this->_options['maxfiles'] = $num;
    }

    /**
     * Returns html for help button.
     *
     * @return string html for help button
     */
    function getHelpButton() {
        return $this->_helpbutton;
    }

    /**
     * Returns type of filemanager element
     *
     * @return string
     */
    function getElementTemplateType() {
        if ($this->_flagFrozen){
            return 'nodisplay';
        } else {
            return 'default';
        }
    }

    /**
     * Returns HTML for filemanager form element.
     *
     * @return string
     */
    function toHtml() {
        global $CFG, $USER, $COURSE, $PAGE, $OUTPUT;
        require_once("$CFG->dirroot/repository/lib.php");

        // security - never ever allow guest/not logged in user to upload anything or use this element!
        if (isguestuser() or !isloggedin()) {
            print_error('noguest');
        }

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $id          = $this->_attributes['id'];
        $elname      = $this->_attributes['name'];
        $subdirs     = $this->_options['subdirs'];
        $maxbytes    = $this->_options['maxbytes'];
        $draftitemid = $this->getValue();
        $accepted_types = $this->_options['accepted_types'];

        if (empty($draftitemid)) {
            // no existing area info provided - let's use fresh new draft area
            require_once("$CFG->libdir/filelib.php");
            $this->setValue(file_get_unused_draft_itemid());
            $draftitemid = $this->getValue();
        }

        $client_id = uniqid();

        // filemanager options
        $options = new stdClass();
        $options->mainfile  = $this->_options['mainfile'];
        $options->maxbytes  = $this->_options['maxbytes'];
        $options->maxfiles  = $this->getMaxfiles();
        $options->client_id = $client_id;
        $options->itemid    = $draftitemid;
        $options->subdirs   = $this->_options['subdirs'];
        $options->target    = $id;
        $options->accepted_types = $accepted_types;
        $options->return_types = $this->_options['return_types'];
        $options->context = $PAGE->context;
        $options->areamaxbytes = $this->_options['areamaxbytes'];

        $html = $this->_getTabs();
        $fm = new form_filemanager($options);
        $output = $PAGE->get_renderer('core', 'files');
        $html .= $output->render($fm);

        $html .= html_writer::empty_tag('input', array('value' => $draftitemid, 'name' => $elname, 'type' => 'hidden'));
        // label element needs 'for' attribute work
        $html .= html_writer::empty_tag('input', array('value' => '', 'id' => 'id_'.$elname, 'type' => 'hidden'));

        return $html;
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        $context['html'] = $this->toHtml();
        return $context;
    }
}

/**
 * Data structure representing a file manager.
 *
 * This class defines the data structure for file mnager
 *
 * @package   core_form
 * @copyright 2010 Dongsheng Cai
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo      do not use this abstraction (skodak)
 */
class form_filemanager implements renderable {
    /** @var stdClass $options options for filemanager */
    public $options;

    /**
     * Constructor
     *
     * @param stdClass $options options for filemanager
     *   default options are:
     *       maxbytes=>-1,
     *       areamaxbytes => FILE_AREA_MAX_BYTES_UNLIMITED,
     *       maxfiles=>-1,
     *       itemid=>0,
     *       subdirs=>false,
     *       client_id=>uniqid(),
     *       acepted_types=>'*',
     *       return_types=>FILE_INTERNAL,
     *       context=>$PAGE->context,
     *       author=>fullname($USER),
     *       licenses=>array build from $CFG->licenses,
     *       defaultlicense=>$CFG->sitedefaultlicense
     */
    public function __construct(stdClass $options) {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot. '/repository/lib.php');
        $defaults = array(
            'maxbytes'=>-1,
            'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'maxfiles'=>-1,
            'itemid'=>0,
            'subdirs'=>0,
            'client_id'=>uniqid(),
            'accepted_types'=>'*',
            'return_types'=>FILE_INTERNAL,
            'context'=>$PAGE->context,
            'author'=>fullname($USER),
            'licenses'=>array()
            );
        if (!empty($CFG->licenses)) {
            $array = explode(',', $CFG->licenses);
            foreach ($array as $license) {
                $l = new stdClass();
                $l->shortname = $license;
                $l->fullname = get_string($license, 'license');
                $defaults['licenses'][] = $l;
            }
        }
        if (!empty($CFG->sitedefaultlicense)) {
            $defaults['defaultlicense'] = $CFG->sitedefaultlicense;
        }
        foreach ($defaults as $key=>$value) {
            // Using !isset() prevents us from overwriting falsey values with defaults (as empty() did).
            if (!isset($options->$key)) {
                $options->$key = $value;
            }
        }

        $fs = get_file_storage();

        // initilise options, getting files in root path
        $this->options = file_get_drafarea_files($options->itemid, '/');

        // calculate file count
        $usercontext = context_user::instance($USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $options->itemid, 'id', false);
        $filecount = count($files);
        $this->options->filecount = $filecount;

        // copying other options
        foreach ($options as $name=>$value) {
            $this->options->$name = $value;
        }

        // calculate the maximum file size as minimum from what is specified in filepicker options,
        // course options, global configuration and php settings
        $coursebytes = $maxbytes = 0;
        list($context, $course, $cm) = get_context_info_array($this->options->context->id);
        if (is_object($course)) {
            $coursebytes = $course->maxbytes;
        }
        if (!empty($this->options->maxbytes) && $this->options->maxbytes > 0) {
            $maxbytes = $this->options->maxbytes;
        }
        $this->options->maxbytes = get_user_max_upload_file_size($context, $CFG->maxbytes, $coursebytes, $maxbytes);

        // building file picker options
        $params = new stdClass();
        $params->accepted_types = $options->accepted_types;
        $params->return_types = $options->return_types;
        $params->context = $options->context;
        $params->env = 'filemanager';
        $params->disable_types = !empty($options->disable_types)?$options->disable_types:array();
        $filepicker_options = initialise_filepicker($params);
        $this->options->filepicker = $filepicker_options;
    }

    public function get_nonjsurl() {
        global $PAGE;
        return new moodle_url('/repository/draftfiles_manager.php', array(
            'env'=>'filemanager',
            'action'=>'browse',
            'itemid'=>$this->options->itemid,
            'subdirs'=>$this->options->subdirs,
            'maxbytes'=>$this->options->maxbytes,
            'areamaxbytes' => $this->options->areamaxbytes,
            'maxfiles'=>$this->options->maxfiles,
            'ctx_id'=>$PAGE->context->id, // TODO ?
            'course'=>$PAGE->course->id, // TODO ?
            'sesskey'=>sesskey(),
            ));
    }
}
