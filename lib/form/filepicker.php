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
 * Filepicker form element
 *
 * Contains HTML class for a single filepicker form element
 *
 * @package   core_form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once("HTML/QuickForm/button.php");
require_once($CFG->dirroot.'/repository/lib.php');
require_once('templatable_form_element.php');

/**
 * Filepicker form element
 *
 * HTML class for a single filepicker element (based on button)
 *
 * @package   core_form
 * @category  form
 * @copyright 2009 Dongsheng Cai <dongsheng@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_filepicker extends HTML_QuickForm_input implements templatable {
    use templatable_form_element {
        export_for_template as export_for_template_base;
    }
    /** @var string html for help button, if empty then no help will icon will be dispalyed. */
    public $_helpbutton = '';

    /** @var array options provided to initalize filemanager */
    // PHP doesn't support 'key' => $value1 | $value2 in class definition
    // We cannot do $_options = array('return_types'=> FILE_INTERNAL | FILE_REFERENCE);
    // So I have to set null here, and do it in constructor
    protected $_options    = array('maxbytes'=>0, 'accepted_types'=>'*', 'return_types'=>null);

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the filepicker
     * @param string $elementLabel (optional) filepicker label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param array $options set of options to initalize filepicker
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG, $PAGE;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (empty($options['return_types'])) {
            $this->_options['return_types'] = FILE_INTERNAL;
        }
        $fpmaxbytes = 0;
        if (!empty($options['maxbytes'])) {
            $fpmaxbytes = $options['maxbytes'];
        }
        $coursemaxbytes = 0;
        if (!empty($PAGE->course->maxbytes)) {
            $coursemaxbytes = $PAGE->course->maxbytes;
        }
        $this->_options['maxbytes'] = get_user_max_upload_file_size($PAGE->context, $CFG->maxbytes, $coursemaxbytes, $fpmaxbytes);
        $this->_type = 'filepicker';
        parent::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function MoodleQuickForm_filepicker($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($elementName, $elementLabel, $attributes, $options);
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
     * Returns type of filepicker element
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
     * Returns HTML for filepicker form element.
     *
     * @return string
     */
    function toHtml() {
        global $CFG, $COURSE, $USER, $PAGE, $OUTPUT;
        $id     = $this->_attributes['id'];
        $elname = $this->_attributes['name'];

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }
        if (!$draftitemid = (int)$this->getValue()) {
            // no existing area info provided - let's use fresh new draft area
            $draftitemid = file_get_unused_draft_itemid();
            $this->setValue($draftitemid);
        }

        if ($COURSE->id == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($COURSE->id);
        }

        $client_id = uniqid();

        $args = new stdClass();
        // need these three to filter repositories list
        $args->accepted_types = $this->_options['accepted_types']?$this->_options['accepted_types']:'*';
        $args->return_types = $this->_options['return_types'];
        $args->itemid = $draftitemid;
        $args->maxbytes = $this->_options['maxbytes'];
        $args->context = $PAGE->context;
        $args->buttonname = $elname.'choose';
        $args->elementid = $id;

        $html = $this->_getTabs();
        $fp = new file_picker($args);
        $options = $fp->options;
        $options->context = $PAGE->context;
        $html .= $OUTPUT->render($fp);
        $html .= '<input type="hidden" name="'.$elname.'" id="'.$id.'" value="'.$draftitemid.'" class="filepickerhidden"/>';

        $module = array('name'=>'form_filepicker', 'fullpath'=>'/lib/form/filepicker.js', 'requires'=>array('core_filepicker', 'node', 'node-event-simulate', 'core_dndupload'));
        $PAGE->requires->js_init_call('M.form_filepicker.init', array($fp->options), true, $module);

        $nonjsfilepicker = new moodle_url('/repository/draftfiles_manager.php', array(
            'env'=>'filepicker',
            'action'=>'browse',
            'itemid'=>$draftitemid,
            'subdirs'=>0,
            'maxbytes'=>$options->maxbytes,
            'maxfiles'=>1,
            'ctx_id'=>$PAGE->context->id,
            'course'=>$PAGE->course->id,
            'sesskey'=>sesskey(),
            ));

        // non js file picker
        $html .= '<noscript>';
        $html .= "<div><object type='text/html' data='$nonjsfilepicker' height='160' width='600' style='border:1px solid #000'></object></div>";
        $html .= '</noscript>';

        if (!empty($options->accepted_types) && $options->accepted_types != '*') {
            $html .= html_writer::tag('p', get_string('filesofthesetypes', 'form'));
            $util = new \core_form\filetypes_util();
            $filetypes = $options->accepted_types;
            $filetypedescriptions = $util->describe_file_types($filetypes);
            $html .= $OUTPUT->render_from_template('core_form/filetypes-descriptions', $filetypedescriptions);
        }

        return $html;
    }

    /**
     * export uploaded file
     *
     * @param array $submitValues values submitted.
     * @param bool $assoc specifies if returned array is associative
     * @return array
     */
    function exportValue(&$submitValues, $assoc = false) {
        global $USER;

        $draftitemid = $this->_findValue($submitValues);
        if (null === $draftitemid) {
            $draftitemid = $this->getValue();
        }

        // make sure max one file is present and it is not too big
        if (!is_null($draftitemid)) {
            $fs = get_file_storage();
            $usercontext = context_user::instance($USER->id);
            if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id DESC', false)) {
                $file = array_shift($files);
                if ($this->_options['maxbytes']
                    and $this->_options['maxbytes'] !== USER_CAN_IGNORE_FILE_SIZE_LIMITS
                    and $file->get_filesize() > $this->_options['maxbytes']) {

                    // bad luck, somebody tries to sneak in oversized file
                    $file->delete();
                }
                foreach ($files as $file) {
                    // only one file expected
                    $file->delete();
                }
            }
        }

        return $this->_prepareValue($draftitemid, true);
    }

    public function export_for_template(renderer_base $output) {
        $context = $this->export_for_template_base($output);
        $context['html'] = $this->toHtml();
        return $context;
    }

    /**
     * Check that the file has the allowed type.
     *
     * @param array $value Draft item id with the uploaded files.
     * @return string|null Validation error message or null.
     */
    public function validateSubmitValue($value) {

        $filetypesutil = new \core_form\filetypes_util();
        $whitelist = $filetypesutil->normalize_file_types($this->_options['accepted_types']);

        if (empty($whitelist) || $whitelist === ['*']) {
            // Any file type is allowed, nothing to check here.
            return;
        }

        $draftfiles = file_get_drafarea_files($value);
        $wrongfiles = array();

        if (empty($draftfiles)) {
            // No file uploaded, nothing to check here.
            return;
        }

        foreach ($draftfiles->list as $file) {
            if (!$filetypesutil->is_allowed_file_type($file->filename, $whitelist)) {
                $wrongfiles[] = $file->filename;
            }
        }

        if ($wrongfiles) {
            $a = array(
                'whitelist' => implode(', ', $whitelist),
                'wrongfiles' => implode(', ', $wrongfiles),
            );
            return get_string('err_wrongfileextension', 'core_form', $a);
        }

        return;
    }
}
