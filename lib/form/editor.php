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
 * Editor input element
 *
 * Contains class to create preffered editor form element
 *
 * @package   core_form
 * @copyright 2009 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;

require_once('HTML/QuickForm/element.php');
require_once($CFG->dirroot.'/lib/filelib.php');
require_once($CFG->dirroot.'/repository/lib.php');

/**
 * Editor element
 *
 * It creates preffered editor (textbox/TinyMce) form element for the format (Text/HTML) selected.
 *
 * @package   core_form
 * @category  form
 * @copyright 2009 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @todo      MDL-29421 element Freezing
 * @todo      MDL-29426 ajax format conversion
 */
class MoodleQuickForm_editor extends HTML_QuickForm_element {
    /** @var string html for help button, if empty then no help will icon will be dispalyed. */
    public $_helpbutton = '';

    /** @var string defines the type of editor */
    public $_type       = 'editor';

    /** @var array options provided to initalize filepicker */
    protected $_options = array('subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 0, 'changeformat' => 0,
            'areamaxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED, 'context' => null, 'noclean' => 0, 'trusttext' => 0,
            'return_types' => 7);
    // $_options['return_types'] = FILE_INTERNAL | FILE_EXTERNAL | FILE_REFERENCE

    /** @var array values for editor */
    protected $_values     = array('text'=>null, 'format'=>null, 'itemid'=>null);

    /**
     * Constructor
     *
     * @param string $elementName (optional) name of the editor
     * @param string $elementLabel (optional) editor label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     * @param array $options set of options to initalize filepicker
     */
    function MoodleQuickForm_editor($elementName=null, $elementLabel=null, $attributes=null, $options=null) {
        global $CFG, $PAGE;

        $options = (array)$options;
        foreach ($options as $name=>$value) {
            if (array_key_exists($name, $this->_options)) {
                $this->_options[$name] = $value;
            }
        }
        if (!empty($options['maxbytes'])) {
            $this->_options['maxbytes'] = get_max_upload_file_size($CFG->maxbytes, $options['maxbytes']);
        }
        if (!$this->_options['context']) {
            // trying to set context to the current page context to make legacy files show in filepicker (e.g. forum post)
            if (!empty($PAGE->context->id)) {
                $this->_options['context'] = $PAGE->context;
            } else {
                $this->_options['context'] = context_system::instance();
            }
        }
        $this->_options['trusted'] = trusttext_trusted($this->_options['context']);
        parent::HTML_QuickForm_element($elementName, $elementLabel, $attributes);

        // Note: for some reason the code using this setting does not like bools.
        $this->_options['subdirs'] = (int)($this->_options['subdirs'] == 1);

        editors_head_setup();
    }

    /**
     * Sets name of editor
     *
     * @param string $name name of the editor
     */
    function setName($name) {
        $this->updateAttributes(array('name'=>$name));
    }

    /**
     * Returns name of element
     *
     * @return string
     */
    function getName() {
        return $this->getAttribute('name');
    }

    /**
     * Updates editor values, if part of $_values
     *
     * @param array $values associative array of values to set
     */
    function setValue($values) {
        $values = (array)$values;
        foreach ($values as $name=>$value) {
            if (array_key_exists($name, $this->_values)) {
                $this->_values[$name] = $value;
            }
        }
    }

    /**
     * Returns editor values
     *
     * @return array
     */
    function getValue() {
        return $this->_values;
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
        global $CFG;
        $this->_options['maxbytes'] = get_max_upload_file_size($CFG->maxbytes, $maxbytes);
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
        $this->_options['subdirs'] = (int)($allow == 1);
    }

    /**
     * Returns editor format
     *
     * @return int.
     */
    function getFormat() {
        return $this->_values['format'];
    }

    /**
     * Checks if editor used is a required field
     *
     * @return bool true if required field.
     */
    function isRequired() {
        return (isset($this->_options['required']) && $this->_options['required']);
    }

    /**
     * @deprecated since Moodle 2.0
     */
    function setHelpButton($_helpbuttonargs, $function='_helpbutton') {
        throw new coding_exception('setHelpButton() can not be used any more, please see MoodleQuickForm::addHelpButton().');
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
     * Returns type of editor element
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
     * Returns HTML for editor form element.
     *
     * @return string
     */
    function toHtml() {
        global $CFG, $PAGE;
        require_once($CFG->dirroot.'/repository/lib.php');

        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        }

        $ctx = $this->_options['context'];

        $id           = $this->_attributes['id'];
        $elname       = $this->_attributes['name'];

        $subdirs      = $this->_options['subdirs'];
        $maxbytes     = $this->_options['maxbytes'];
        $areamaxbytes = $this->_options['areamaxbytes'];
        $maxfiles     = $this->_options['maxfiles'];
        $changeformat = $this->_options['changeformat']; // TO DO: implement as ajax calls

        $text         = $this->_values['text'];
        $format       = $this->_values['format'];
        $draftitemid  = $this->_values['itemid'];

        // security - never ever allow guest/not logged in user to upload anything
        if (isguestuser() or !isloggedin()) {
            $maxfiles = 0;
        }

        $str = $this->_getTabs();
        $str .= '<div>';

        $editor = editors_get_preferred_editor($format);
        $strformats = format_text_menu();
        $formats =  $editor->get_supported_formats();
        foreach ($formats as $fid) {
            $formats[$fid] = $strformats[$fid];
        }

        // get filepicker info
        //
        $fpoptions = array();
        if ($maxfiles != 0 ) {
            if (empty($draftitemid)) {
                // no existing area info provided - let's use fresh new draft area
                require_once("$CFG->libdir/filelib.php");
                $this->setValue(array('itemid'=>file_get_unused_draft_itemid()));
                $draftitemid = $this->_values['itemid'];
            }

            $args = new stdClass();
            // need these three to filter repositories list
            $args->accepted_types = array('web_image');
            $args->return_types = $this->_options['return_types'];
            $args->context = $ctx;
            $args->env = 'filepicker';
            // advimage plugin
            $image_options = initialise_filepicker($args);
            $image_options->context = $ctx;
            $image_options->client_id = uniqid();
            $image_options->maxbytes = $this->_options['maxbytes'];
            $image_options->areamaxbytes = $this->_options['areamaxbytes'];
            $image_options->env = 'editor';
            $image_options->itemid = $draftitemid;

            // moodlemedia plugin
            $args->accepted_types = array('video', 'audio');
            $media_options = initialise_filepicker($args);
            $media_options->context = $ctx;
            $media_options->client_id = uniqid();
            $media_options->maxbytes  = $this->_options['maxbytes'];
            $media_options->areamaxbytes  = $this->_options['areamaxbytes'];
            $media_options->env = 'editor';
            $media_options->itemid = $draftitemid;

            // advlink plugin
            $args->accepted_types = '*';
            $link_options = initialise_filepicker($args);
            $link_options->context = $ctx;
            $link_options->client_id = uniqid();
            $link_options->maxbytes  = $this->_options['maxbytes'];
            $link_options->areamaxbytes  = $this->_options['areamaxbytes'];
            $link_options->env = 'editor';
            $link_options->itemid = $draftitemid;

            $fpoptions['image'] = $image_options;
            $fpoptions['media'] = $media_options;
            $fpoptions['link'] = $link_options;
        }

        //If editor is required and tinymce, then set required_tinymce option to initalize tinymce validation.
        if (($editor instanceof tinymce_texteditor)  && !is_null($this->getAttribute('onchange'))) {
            $this->_options['required'] = true;
        }

        // print text area - TODO: add on-the-fly switching, size configuration, etc.
        $editor->use_editor($id, $this->_options, $fpoptions);

        $rows = empty($this->_attributes['rows']) ? 15 : $this->_attributes['rows'];
        $cols = empty($this->_attributes['cols']) ? 80 : $this->_attributes['cols'];

        //Apply editor validation if required field
        $editorrules = '';
        if (!is_null($this->getAttribute('onblur')) && !is_null($this->getAttribute('onchange'))) {
            $editorrules = ' onblur="'.htmlspecialchars($this->getAttribute('onblur')).'" onchange="'.htmlspecialchars($this->getAttribute('onchange')).'"';
        }
        $str .= '<div><textarea id="'.$id.'" name="'.$elname.'[text]" rows="'.$rows.'" cols="'.$cols.'" spellcheck="true"'.$editorrules.'>';
        $str .= s($text);
        $str .= '</textarea></div>';

        $str .= '<div>';
        if (count($formats)>1) {
            $str .= html_writer::label(get_string('format'), 'menu'. $elname. 'format', false, array('class' => 'accesshide'));
            $str .= html_writer::select($formats, $elname.'[format]', $format, false, array('id' => 'menu'. $elname. 'format'));
        } else {
            $keys = array_keys($formats);
            $str .= html_writer::empty_tag('input',
                    array('name'=>$elname.'[format]', 'type'=> 'hidden', 'value' => array_pop($keys)));
        }
        $str .= '</div>';

        // during moodle installation, user area doesn't exist
        // so we need to disable filepicker here.
        if (!during_initial_install() && empty($CFG->adminsetuppending)) {
            // 0 means no files, -1 unlimited
            if ($maxfiles != 0 ) {
                $str .= '<input type="hidden" name="'.$elname.'[itemid]" value="'.$draftitemid.'" />';

                // used by non js editor only
                $editorurl = new moodle_url("$CFG->wwwroot/repository/draftfiles_manager.php", array(
                    'action'=>'browse',
                    'env'=>'editor',
                    'itemid'=>$draftitemid,
                    'subdirs'=>$subdirs,
                    'maxbytes'=>$maxbytes,
                    'areamaxbytes' => $areamaxbytes,
                    'maxfiles'=>$maxfiles,
                    'ctx_id'=>$ctx->id,
                    'course'=>$PAGE->course->id,
                    'sesskey'=>sesskey(),
                    ));
                $str .= '<noscript>';
                $str .= "<div><object type='text/html' data='$editorurl' height='160' width='600' style='border:1px solid #000'></object></div>";
                $str .= '</noscript>';
            }
        }


        $str .= '</div>';

        return $str;
    }

    /**
     * What to display when element is frozen.
     *
     * @return empty string
     */
    function getFrozenHtml() {

        return '';
    }
}
