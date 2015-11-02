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
 * @package   mod_data
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Some constants
define ('DATA_MAX_ENTRIES', 50);
define ('DATA_PERPAGE_SINGLE', 1);

define ('DATA_FIRSTNAME', -1);
define ('DATA_LASTNAME', -2);
define ('DATA_APPROVED', -3);
define ('DATA_TIMEADDED', 0);
define ('DATA_TIMEMODIFIED', -4);

define ('DATA_CAP_EXPORT', 'mod/data:viewalluserpresets');

define('DATA_PRESET_COMPONENT', 'mod_data');
define('DATA_PRESET_FILEAREA', 'site_presets');
define('DATA_PRESET_CONTEXT', SYSCONTEXTID);

// Users having assigned the default role "Non-editing teacher" can export database records
// Using the mod/data capability "viewalluserpresets" existing in Moodle 1.9.x.
// In Moodle >= 2, new roles may be introduced and used instead.

/**
 * @package   mod_data
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_field_base {     // Base class for Database Field Types (see field/*/field.class.php)

    /** @var string Subclasses must override the type with their name */
    var $type = 'unknown';
    /** @var object The database object that this field belongs to */
    var $data = NULL;
    /** @var object The field object itself, if we know it */
    var $field = NULL;
    /** @var int Width of the icon for this fieldtype */
    var $iconwidth = 16;
    /** @var int Width of the icon for this fieldtype */
    var $iconheight = 16;
    /** @var object course module or cmifno */
    var $cm;
    /** @var object activity context */
    var $context;

    /**
     * Constructor function
     *
     * @global object
     * @uses CONTEXT_MODULE
     * @param int $field
     * @param int $data
     * @param int $cm
     */
    function __construct($field=0, $data=0, $cm=0) {   // Field or data or both, each can be id or object
        global $DB;

        if (empty($field) && empty($data)) {
            print_error('missingfield', 'data');
        }

        if (!empty($field)) {
            if (is_object($field)) {
                $this->field = $field;  // Programmer knows what they are doing, we hope
            } else if (!$this->field = $DB->get_record('data_fields', array('id'=>$field))) {
                print_error('invalidfieldid', 'data');
            }
            if (empty($data)) {
                if (!$this->data = $DB->get_record('data', array('id'=>$this->field->dataid))) {
                    print_error('invalidid', 'data');
                }
            }
        }

        if (empty($this->data)) {         // We need to define this properly
            if (!empty($data)) {
                if (is_object($data)) {
                    $this->data = $data;  // Programmer knows what they are doing, we hope
                } else if (!$this->data = $DB->get_record('data', array('id'=>$data))) {
                    print_error('invalidid', 'data');
                }
            } else {                      // No way to define it!
                print_error('missingdata', 'data');
            }
        }

        if ($cm) {
            $this->cm = $cm;
        } else {
            $this->cm = get_coursemodule_from_instance('data', $this->data->id);
        }

        if (empty($this->field)) {         // We need to define some default values
            $this->define_default_field();
        }

        $this->context = context_module::instance($this->cm->id);
    }


    /**
     * This field just sets up a default field object
     *
     * @return bool
     */
    function define_default_field() {
        global $OUTPUT;
        if (empty($this->data->id)) {
            echo $OUTPUT->notification('Programmer error: dataid not defined in field class');
        }
        $this->field = new stdClass();
        $this->field->id = 0;
        $this->field->dataid = $this->data->id;
        $this->field->type   = $this->type;
        $this->field->param1 = '';
        $this->field->param2 = '';
        $this->field->param3 = '';
        $this->field->name = '';
        $this->field->description = '';
        $this->field->required = false;

        return true;
    }

    /**
     * Set up the field object according to data in an object.  Now is the time to clean it!
     *
     * @return bool
     */
    function define_field($data) {
        $this->field->type        = $this->type;
        $this->field->dataid      = $this->data->id;

        $this->field->name        = trim($data->name);
        $this->field->description = trim($data->description);
        $this->field->required    = !empty($data->required) ? 1 : 0;

        if (isset($data->param1)) {
            $this->field->param1 = trim($data->param1);
        }
        if (isset($data->param2)) {
            $this->field->param2 = trim($data->param2);
        }
        if (isset($data->param3)) {
            $this->field->param3 = trim($data->param3);
        }
        if (isset($data->param4)) {
            $this->field->param4 = trim($data->param4);
        }
        if (isset($data->param5)) {
            $this->field->param5 = trim($data->param5);
        }

        return true;
    }

    /**
     * Insert a new field in the database
     * We assume the field object is already defined as $this->field
     *
     * @global object
     * @return bool
     */
    function insert_field() {
        global $DB, $OUTPUT;

        if (empty($this->field)) {
            echo $OUTPUT->notification('Programmer error: Field has not been defined yet!  See define_field()');
            return false;
        }

        $this->field->id = $DB->insert_record('data_fields',$this->field);

        // Trigger an event for creating this field.
        $event = \mod_data\event\field_created::create(array(
            'objectid' => $this->field->id,
            'context' => $this->context,
            'other' => array(
                'fieldname' => $this->field->name,
                'dataid' => $this->data->id
            )
        ));
        $event->trigger();

        return true;
    }


    /**
     * Update a field in the database
     *
     * @global object
     * @return bool
     */
    function update_field() {
        global $DB;

        $DB->update_record('data_fields', $this->field);

        // Trigger an event for updating this field.
        $event = \mod_data\event\field_updated::create(array(
            'objectid' => $this->field->id,
            'context' => $this->context,
            'other' => array(
                'fieldname' => $this->field->name,
                'dataid' => $this->data->id
            )
        ));
        $event->trigger();

        return true;
    }

    /**
     * Delete a field completely
     *
     * @global object
     * @return bool
     */
    function delete_field() {
        global $DB;

        if (!empty($this->field->id)) {
            // Get the field before we delete it.
            $field = $DB->get_record('data_fields', array('id' => $this->field->id));

            $this->delete_content();
            $DB->delete_records('data_fields', array('id'=>$this->field->id));

            // Trigger an event for deleting this field.
            $event = \mod_data\event\field_deleted::create(array(
                'objectid' => $this->field->id,
                'context' => $this->context,
                'other' => array(
                    'fieldname' => $this->field->name,
                    'dataid' => $this->data->id
                 )
            ));
            $event->add_record_snapshot('data_fields', $field);
            $event->trigger();
        }

        return true;
    }

    /**
     * Print the relevant form element in the ADD template for this field
     *
     * @global object
     * @param int $recordid
     * @return string
     */
    function display_add_field($recordid=0, $formdata=null) {
        global $DB, $OUTPUT;

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id;
            $content = $formdata->$fieldname;
        } else if ($recordid) {
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
        } else {
            $content = '';
        }

        // beware get_field returns false for new, empty records MDL-18567
        if ($content===false) {
            $content='';
        }

        $str = '<div title="' . s($this->field->description) . '">';
        $str .= '<label for="field_'.$this->field->id.'"><span class="accesshide">'.$this->field->name.'</span>';
        if ($this->field->required) {
            $image = html_writer::img($OUTPUT->pix_url('req'), get_string('requiredelement', 'form'),
                                     array('class' => 'req', 'title' => get_string('requiredelement', 'form')));
            $str .= html_writer::div($image, 'inline-req');
        }
        $str .= '</label><input class="basefieldinput mod-data-input" type="text" name="field_'.$this->field->id.'"';
        $str .= ' id="field_' . $this->field->id . '" value="'.s($content).'" />';
        $str .= '</div>';

        return $str;
    }

    /**
     * Print the relevant form element to define the attributes for this field
     * viewable by teachers only.
     *
     * @global object
     * @global object
     * @return void Output is echo'd
     */
    function display_edit_field() {
        global $CFG, $DB, $OUTPUT;

        if (empty($this->field)) {   // No field has been defined yet, try and make one
            $this->define_default_field();
        }
        echo $OUTPUT->box_start('generalbox boxaligncenter boxwidthwide');

        echo '<form id="editfield" action="'.$CFG->wwwroot.'/mod/data/field.php" method="post">'."\n";
        echo '<input type="hidden" name="d" value="'.$this->data->id.'" />'."\n";
        if (empty($this->field->id)) {
            echo '<input type="hidden" name="mode" value="add" />'."\n";
            $savebutton = get_string('add');
        } else {
            echo '<input type="hidden" name="fid" value="'.$this->field->id.'" />'."\n";
            echo '<input type="hidden" name="mode" value="update" />'."\n";
            $savebutton = get_string('savechanges');
        }
        echo '<input type="hidden" name="type" value="'.$this->type.'" />'."\n";
        echo '<input name="sesskey" value="'.sesskey().'" type="hidden" />'."\n";

        echo $OUTPUT->heading($this->name(), 3);

        require_once($CFG->dirroot.'/mod/data/field/'.$this->type.'/mod.html');

        echo '<div class="mdl-align">';
        echo '<input type="submit" value="'.$savebutton.'" />'."\n";
        echo '<input type="submit" name="cancel" value="'.get_string('cancel').'" />'."\n";
        echo '</div>';

        echo '</form>';

        echo $OUTPUT->box_end();
    }

    /**
     * Display the content of the field in browse mode
     *
     * @global object
     * @param int $recordid
     * @param object $template
     * @return bool|string
     */
    function display_browse_field($recordid, $template) {
        global $DB;

        if ($content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            if (isset($content->content)) {
                $options = new stdClass();
                if ($this->field->param1 == '1') {  // We are autolinking this field, so disable linking within us
                    //$content->content = '<span class="nolink">'.$content->content.'</span>';
                    //$content->content1 = FORMAT_HTML;
                    $options->filter=false;
                }
                $options->para = false;
                $str = format_text($content->content, $content->content1, $options);
            } else {
                $str = '';
            }
            return $str;
        }
        return false;
    }

    /**
     * Update the content of one data field in the data_content table
     * @global object
     * @param int $recordid
     * @param mixed $value
     * @param string $name
     * @return bool
     */
    function update_content($recordid, $value, $name=''){
        global $DB;

        $content = new stdClass();
        $content->fieldid = $this->field->id;
        $content->recordid = $recordid;
        $content->content = clean_param($value, PARAM_NOTAGS);

        if ($oldcontent = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
            $content->id = $oldcontent->id;
            return $DB->update_record('data_content', $content);
        } else {
            return $DB->insert_record('data_content', $content);
        }
    }

    /**
     * Delete all content associated with the field
     *
     * @global object
     * @param int $recordid
     * @return bool
     */
    function delete_content($recordid=0) {
        global $DB;

        if ($recordid) {
            $conditions = array('fieldid'=>$this->field->id, 'recordid'=>$recordid);
        } else {
            $conditions = array('fieldid'=>$this->field->id);
        }

        $rs = $DB->get_recordset('data_content', $conditions);
        if ($rs->valid()) {
            $fs = get_file_storage();
            foreach ($rs as $content) {
                $fs->delete_area_files($this->context->id, 'mod_data', 'content', $content->id);
            }
        }
        $rs->close();

        return $DB->delete_records('data_content', $conditions);
    }

    /**
     * Check if a field from an add form is empty
     *
     * @param mixed $value
     * @param mixed $name
     * @return bool
     */
    function notemptyfield($value, $name) {
        return !empty($value);
    }

    /**
     * Just in case a field needs to print something before the whole form
     */
    function print_before_form() {
    }

    /**
     * Just in case a field needs to print something after the whole form
     */
    function print_after_form() {
    }


    /**
     * Returns the sortable field for the content. By default, it's just content
     * but for some plugins, it could be content 1 - content4
     *
     * @return string
     */
    function get_sort_field() {
        return 'content';
    }

    /**
     * Returns the SQL needed to refer to the column.  Some fields may need to CAST() etc.
     *
     * @param string $fieldname
     * @return string $fieldname
     */
    function get_sort_sql($fieldname) {
        return $fieldname;
    }

    /**
     * Returns the name/type of the field
     *
     * @return string
     */
    function name() {
        return get_string('name'.$this->type, 'data');
    }

    /**
     * Prints the respective type icon
     *
     * @global object
     * @return string
     */
    function image() {
        global $OUTPUT;

        $params = array('d'=>$this->data->id, 'fid'=>$this->field->id, 'mode'=>'display', 'sesskey'=>sesskey());
        $link = new moodle_url('/mod/data/field.php', $params);
        $str = '<a href="'.$link->out().'">';
        $str .= '<img src="'.$OUTPUT->pix_url('field/'.$this->type, 'data') . '" ';
        $str .= 'height="'.$this->iconheight.'" width="'.$this->iconwidth.'" alt="'.$this->type.'" title="'.$this->type.'" /></a>';
        return $str;
    }

    /**
     * Per default, it is assumed that fields support text exporting.
     * Override this (return false) on fields not supporting text exporting.
     *
     * @return bool true
     */
    function text_export_supported() {
        return true;
    }

    /**
     * Per default, return the record's text value only from the "content" field.
     * Override this in fields class if necesarry.
     *
     * @param string $record
     * @return string
     */
    function export_text_value($record) {
        if ($this->text_export_supported()) {
            return $record->content;
        }
    }

    /**
     * @param string $relativepath
     * @return bool false
     */
    function file_ok($relativepath) {
        return false;
    }
}


/**
 * Given a template and a dataid, generate a default case template
 *
 * @global object
 * @param object $data
 * @param string template [addtemplate, singletemplate, listtempalte, rsstemplate]
 * @param int $recordid
 * @param bool $form
 * @param bool $update
 * @return bool|string
 */
function data_generate_default_template(&$data, $template, $recordid=0, $form=false, $update=true) {
    global $DB;

    if (!$data && !$template) {
        return false;
    }
    if ($template == 'csstemplate' or $template == 'jstemplate' ) {
        return '';
    }

    // get all the fields for that database
    if ($fields = $DB->get_records('data_fields', array('dataid'=>$data->id), 'id')) {

        $table = new html_table();
        $table->attributes['class'] = 'mod-data-default-template';
        $table->colclasses = array('template-field', 'template-token');
        $table->data = array();
        foreach ($fields as $field) {
            if ($form) {   // Print forms instead of data
                $fieldobj = data_get_field($field, $data);
                $token = $fieldobj->display_add_field($recordid, null);
            } else {           // Just print the tag
                $token = '[['.$field->name.']]';
            }
            $table->data[] = array(
                $field->name.': ',
                $token
            );
        }
        if ($template == 'listtemplate') {
            $cell = new html_table_cell('##edit##  ##more##  ##delete##  ##approve##  ##disapprove##  ##export##');
            $cell->colspan = 2;
            $cell->attributes['class'] = 'controls';
            $table->data[] = new html_table_row(array($cell));
        } else if ($template == 'singletemplate') {
            $cell = new html_table_cell('##edit##  ##delete##  ##approve##  ##disapprove##  ##export##');
            $cell->colspan = 2;
            $cell->attributes['class'] = 'controls';
            $table->data[] = new html_table_row(array($cell));
        } else if ($template == 'asearchtemplate') {
            $row = new html_table_row(array(get_string('authorfirstname', 'data').': ', '##firstname##'));
            $row->attributes['class'] = 'searchcontrols';
            $table->data[] = $row;
            $row = new html_table_row(array(get_string('authorlastname', 'data').': ', '##lastname##'));
            $row->attributes['class'] = 'searchcontrols';
            $table->data[] = $row;
        }

        $str = '';
        if ($template == 'listtemplate'){
            $str .= '##delcheck##';
            $str .= html_writer::empty_tag('br');
        }

        $str .= html_writer::start_tag('div', array('class' => 'defaulttemplate'));
        $str .= html_writer::table($table);
        $str .= html_writer::end_tag('div');
        if ($template == 'listtemplate'){
            $str .= html_writer::empty_tag('hr');
        }

        if ($update) {
            $newdata = new stdClass();
            $newdata->id = $data->id;
            $newdata->{$template} = $str;
            $DB->update_record('data', $newdata);
            $data->{$template} = $str;
        }

        return $str;
    }
}


/**
 * Search for a field name and replaces it with another one in all the
 * form templates. Set $newfieldname as '' if you want to delete the
 * field from the form.
 *
 * @global object
 * @param object $data
 * @param string $searchfieldname
 * @param string $newfieldname
 * @return bool
 */
function data_replace_field_in_templates($data, $searchfieldname, $newfieldname) {
    global $DB;

    if (!empty($newfieldname)) {
        $prestring = '[[';
        $poststring = ']]';
        $idpart = '#id';

    } else {
        $prestring = '';
        $poststring = '';
        $idpart = '';
    }

    $newdata = new stdClass();
    $newdata->id = $data->id;
    $newdata->singletemplate = str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->singletemplate);

    $newdata->listtemplate = str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->listtemplate);

    $newdata->addtemplate = str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->addtemplate);

    $newdata->addtemplate = str_ireplace('[['.$searchfieldname.'#id]]',
            $prestring.$newfieldname.$idpart.$poststring, $data->addtemplate);

    $newdata->rsstemplate = str_ireplace('[['.$searchfieldname.']]',
            $prestring.$newfieldname.$poststring, $data->rsstemplate);

    return $DB->update_record('data', $newdata);
}


/**
 * Appends a new field at the end of the form template.
 *
 * @global object
 * @param object $data
 * @param string $newfieldname
 */
function data_append_new_field_to_templates($data, $newfieldname) {
    global $DB;

    $newdata = new stdClass();
    $newdata->id = $data->id;
    $change = false;

    if (!empty($data->singletemplate)) {
        $newdata->singletemplate = $data->singletemplate.' [[' . $newfieldname .']]';
        $change = true;
    }
    if (!empty($data->addtemplate)) {
        $newdata->addtemplate = $data->addtemplate.' [[' . $newfieldname . ']]';
        $change = true;
    }
    if (!empty($data->rsstemplate)) {
        $newdata->rsstemplate = $data->singletemplate.' [[' . $newfieldname . ']]';
        $change = true;
    }
    if ($change) {
        $DB->update_record('data', $newdata);
    }
}


/**
 * given a field name
 * this function creates an instance of the particular subfield class
 *
 * @global object
 * @param string $name
 * @param object $data
 * @return object|bool
 */
function data_get_field_from_name($name, $data){
    global $DB;

    $field = $DB->get_record('data_fields', array('name'=>$name, 'dataid'=>$data->id));

    if ($field) {
        return data_get_field($field, $data);
    } else {
        return false;
    }
}

/**
 * given a field id
 * this function creates an instance of the particular subfield class
 *
 * @global object
 * @param int $fieldid
 * @param object $data
 * @return bool|object
 */
function data_get_field_from_id($fieldid, $data){
    global $DB;

    $field = $DB->get_record('data_fields', array('id'=>$fieldid, 'dataid'=>$data->id));

    if ($field) {
        return data_get_field($field, $data);
    } else {
        return false;
    }
}

/**
 * given a field id
 * this function creates an instance of the particular subfield class
 *
 * @global object
 * @param string $type
 * @param object $data
 * @return object
 */
function data_get_field_new($type, $data) {
    global $CFG;

    require_once($CFG->dirroot.'/mod/data/field/'.$type.'/field.class.php');
    $newfield = 'data_field_'.$type;
    $newfield = new $newfield(0, $data);
    return $newfield;
}

/**
 * returns a subclass field object given a record of the field, used to
 * invoke plugin methods
 * input: $param $field - record from db
 *
 * @global object
 * @param object $field
 * @param object $data
 * @param object $cm
 * @return object
 */
function data_get_field($field, $data, $cm=null) {
    global $CFG;

    if ($field) {
        require_once('field/'.$field->type.'/field.class.php');
        $newfield = 'data_field_'.$field->type;
        $newfield = new $newfield($field, $data, $cm);
        return $newfield;
    }
}


/**
 * Given record object (or id), returns true if the record belongs to the current user
 *
 * @global object
 * @global object
 * @param mixed $record record object or id
 * @return bool
 */
function data_isowner($record) {
    global $USER, $DB;

    if (!isloggedin()) { // perf shortcut
        return false;
    }

    if (!is_object($record)) {
        if (!$record = $DB->get_record('data_records', array('id'=>$record))) {
            return false;
        }
    }

    return ($record->userid == $USER->id);
}

/**
 * has a user reached the max number of entries?
 *
 * @param object $data
 * @return bool
 */
function data_atmaxentries($data){
    if (!$data->maxentries){
        return false;

    } else {
        return (data_numentries($data) >= $data->maxentries);
    }
}

/**
 * returns the number of entries already made by this user
 *
 * @global object
 * @global object
 * @param object $data
 * @return int
 */
function data_numentries($data){
    global $USER, $DB;
    $sql = 'SELECT COUNT(*) FROM {data_records} WHERE dataid=? AND userid=?';
    return $DB->count_records_sql($sql, array($data->id, $USER->id));
}

/**
 * function that takes in a dataid and adds a record
 * this is used everytime an add template is submitted
 *
 * @global object
 * @global object
 * @param object $data
 * @param int $groupid
 * @return bool
 */
function data_add_record($data, $groupid=0){
    global $USER, $DB;

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = context_module::instance($cm->id);

    $record = new stdClass();
    $record->userid = $USER->id;
    $record->dataid = $data->id;
    $record->groupid = $groupid;
    $record->timecreated = $record->timemodified = time();
    if (has_capability('mod/data:approve', $context)) {
        $record->approved = 1;
    } else {
        $record->approved = 0;
    }
    $record->id = $DB->insert_record('data_records', $record);

    // Trigger an event for creating this record.
    $event = \mod_data\event\record_created::create(array(
        'objectid' => $record->id,
        'context' => $context,
        'other' => array(
            'dataid' => $data->id
        )
    ));
    $event->trigger();

    return $record->id;
}

/**
 * check the multple existence any tag in a template
 *
 * check to see if there are 2 or more of the same tag being used.
 *
 * @global object
 * @param int $dataid,
 * @param string $template
 * @return bool
 */
function data_tags_check($dataid, $template) {
    global $DB, $OUTPUT;

    // first get all the possible tags
    $fields = $DB->get_records('data_fields', array('dataid'=>$dataid));
    // then we generate strings to replace
    $tagsok = true; // let's be optimistic
    foreach ($fields as $field){
        $pattern="/\[\[" . preg_quote($field->name, '/') . "\]\]/i";
        if (preg_match_all($pattern, $template, $dummy)>1){
            $tagsok = false;
            echo $OUTPUT->notification('[['.$field->name.']] - '.get_string('multipletags','data'));
        }
    }
    // else return true
    return $tagsok;
}

/**
 * Adds an instance of a data
 *
 * @param stdClass $data
 * @param mod_data_mod_form $mform
 * @return int intance id
 */
function data_add_instance($data, $mform = null) {
    global $DB;

    if (empty($data->assessed)) {
        $data->assessed = 0;
    }

    if (empty($data->ratingtime) || empty($data->assessed)) {
        $data->assesstimestart  = 0;
        $data->assesstimefinish = 0;
    }

    $data->timemodified = time();

    $data->id = $DB->insert_record('data', $data);

    data_grade_item_update($data);

    return $data->id;
}

/**
 * updates an instance of a data
 *
 * @global object
 * @param object $data
 * @return bool
 */
function data_update_instance($data) {
    global $DB, $OUTPUT;

    $data->timemodified = time();
    $data->id           = $data->instance;

    if (empty($data->assessed)) {
        $data->assessed = 0;
    }

    if (empty($data->ratingtime) or empty($data->assessed)) {
        $data->assesstimestart  = 0;
        $data->assesstimefinish = 0;
    }

    if (empty($data->notification)) {
        $data->notification = 0;
    }

    $DB->update_record('data', $data);

    data_grade_item_update($data);

    return true;

}

/**
 * deletes an instance of a data
 *
 * @global object
 * @param int $id
 * @return bool
 */
function data_delete_instance($id) {    // takes the dataid
    global $DB, $CFG;

    if (!$data = $DB->get_record('data', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = context_module::instance($cm->id);

/// Delete all the associated information

    // files
    $fs = get_file_storage();
    $fs->delete_area_files($context->id, 'mod_data');

    // get all the records in this data
    $sql = "SELECT r.id
              FROM {data_records} r
             WHERE r.dataid = ?";

    $DB->delete_records_select('data_content', "recordid IN ($sql)", array($id));

    // delete all the records and fields
    $DB->delete_records('data_records', array('dataid'=>$id));
    $DB->delete_records('data_fields', array('dataid'=>$id));

    // Delete the instance itself
    $result = $DB->delete_records('data', array('id'=>$id));

    // cleanup gradebook
    data_grade_item_delete($data);

    return $result;
}

/**
 * returns a summary of data activity of this user
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $data
 * @return object|null
 */
function data_user_outline($course, $user, $mod, $data) {
    global $DB, $CFG;
    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'data', $data->id, $user->id);
    if (empty($grades->items[0]->grades)) {
        $grade = false;
    } else {
        $grade = reset($grades->items[0]->grades);
    }


    if ($countrecords = $DB->count_records('data_records', array('dataid'=>$data->id, 'userid'=>$user->id))) {
        $result = new stdClass();
        $result->info = get_string('numrecords', 'data', $countrecords);
        $lastrecord   = $DB->get_record_sql('SELECT id,timemodified FROM {data_records}
                                              WHERE dataid = ? AND userid = ?
                                           ORDER BY timemodified DESC', array($data->id, $user->id), true);
        $result->time = $lastrecord->timemodified;
        if ($grade) {
            $result->info .= ', ' . get_string('grade') . ': ' . $grade->str_long_grade;
        }
        return $result;
    } else if ($grade) {
        $result = new stdClass();
        $result->info = get_string('grade') . ': ' . $grade->str_long_grade;

        //datesubmitted == time created. dategraded == time modified or time overridden
        //if grade was last modified by the user themselves use date graded. Otherwise use date submitted
        //TODO: move this copied & pasted code somewhere in the grades API. See MDL-26704
        if ($grade->usermodified == $user->id || empty($grade->datesubmitted)) {
            $result->time = $grade->dategraded;
        } else {
            $result->time = $grade->datesubmitted;
        }

        return $result;
    }
    return NULL;
}

/**
 * Prints all the records uploaded by this user
 *
 * @global object
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $data
 */
function data_user_complete($course, $user, $mod, $data) {
    global $DB, $CFG, $OUTPUT;
    require_once("$CFG->libdir/gradelib.php");

    $grades = grade_get_grades($course->id, 'mod', 'data', $data->id, $user->id);
    if (!empty($grades->items[0]->grades)) {
        $grade = reset($grades->items[0]->grades);
        echo $OUTPUT->container(get_string('grade').': '.$grade->str_long_grade);
        if ($grade->str_feedback) {
            echo $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
        }
    }

    if ($records = $DB->get_records('data_records', array('dataid'=>$data->id,'userid'=>$user->id), 'timemodified DESC')) {
        data_print_template('singletemplate', $records, $data);
    }
}

/**
 * Return grade for given user or all users.
 *
 * @global object
 * @param object $data
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function data_get_user_grades($data, $userid=0) {
    global $CFG;

    require_once($CFG->dirroot.'/rating/lib.php');

    $ratingoptions = new stdClass;
    $ratingoptions->component = 'mod_data';
    $ratingoptions->ratingarea = 'entry';
    $ratingoptions->modulename = 'data';
    $ratingoptions->moduleid   = $data->id;

    $ratingoptions->userid = $userid;
    $ratingoptions->aggregationmethod = $data->assessed;
    $ratingoptions->scaleid = $data->scale;
    $ratingoptions->itemtable = 'data_records';
    $ratingoptions->itemtableusercolumn = 'userid';

    $rm = new rating_manager();
    return $rm->get_user_grades($ratingoptions);
}

/**
 * Update activity grades
 *
 * @category grade
 * @param object $data
 * @param int $userid specific user only, 0 means all
 * @param bool $nullifnone
 */
function data_update_grades($data, $userid=0, $nullifnone=true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if (!$data->assessed) {
        data_grade_item_update($data);

    } else if ($grades = data_get_user_grades($data, $userid)) {
        data_grade_item_update($data, $grades);

    } else if ($userid and $nullifnone) {
        $grade = new stdClass();
        $grade->userid   = $userid;
        $grade->rawgrade = NULL;
        data_grade_item_update($data, $grade);

    } else {
        data_grade_item_update($data);
    }
}

/**
 * Update/create grade item for given data
 *
 * @category grade
 * @param stdClass $data A database instance with extra cmidnumber property
 * @param mixed $grades Optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return object grade_item
 */
function data_grade_item_update($data, $grades=NULL) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $params = array('itemname'=>$data->name, 'idnumber'=>$data->cmidnumber);

    if (!$data->assessed or $data->scale == 0) {
        $params['gradetype'] = GRADE_TYPE_NONE;

    } else if ($data->scale > 0) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $data->scale;
        $params['grademin']  = 0;

    } else if ($data->scale < 0) {
        $params['gradetype'] = GRADE_TYPE_SCALE;
        $params['scaleid']   = -$data->scale;
    }

    if ($grades  === 'reset') {
        $params['reset'] = true;
        $grades = NULL;
    }

    return grade_update('mod/data', $data->course, 'mod', 'data', $data->id, 0, $grades, $params);
}

/**
 * Delete grade item for given data
 *
 * @category grade
 * @param object $data object
 * @return object grade_item
 */
function data_grade_item_delete($data) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    return grade_update('mod/data', $data->course, 'mod', 'data', $data->id, 0, NULL, array('deleted'=>1));
}

// junk functions
/**
 * takes a list of records, the current data, a search string,
 * and mode to display prints the translated template
 *
 * @global object
 * @global object
 * @param string $template
 * @param array $records
 * @param object $data
 * @param string $search
 * @param int $page
 * @param bool $return
 * @param object $jumpurl a moodle_url by which to jump back to the record list (can be null)
 * @return mixed
 */
function data_print_template($template, $records, $data, $search='', $page=0, $return=false, moodle_url $jumpurl=null) {
    global $CFG, $DB, $OUTPUT;

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = context_module::instance($cm->id);

    static $fields = NULL;
    static $isteacher;
    static $dataid = NULL;

    if (empty($dataid)) {
        $dataid = $data->id;
    } else if ($dataid != $data->id) {
        $fields = NULL;
    }

    if (empty($fields)) {
        $fieldrecords = $DB->get_records('data_fields', array('dataid'=>$data->id));
        foreach ($fieldrecords as $fieldrecord) {
            $fields[]= data_get_field($fieldrecord, $data);
        }
        $isteacher = has_capability('mod/data:managetemplates', $context);
    }

    if (empty($records)) {
        return;
    }

    if (!$jumpurl) {
        $jumpurl = new moodle_url('/mod/data/view.php', array('d' => $data->id));
    }
    $jumpurl = new moodle_url($jumpurl, array('page' => $page, 'sesskey' => sesskey()));

    // Check whether this activity is read-only at present
    $readonly = data_in_readonly_period($data);

    foreach ($records as $record) {   // Might be just one for the single template

    // Replacing tags
        $patterns = array();
        $replacement = array();

    // Then we generate strings to replace for normal tags
        foreach ($fields as $field) {
            $patterns[]='[['.$field->field->name.']]';
            $replacement[] = highlight($search, $field->display_browse_field($record->id, $template));
        }

        $canmanageentries = has_capability('mod/data:manageentries', $context);

    // Replacing special tags (##Edit##, ##Delete##, ##More##)
        $patterns[]='##edit##';
        $patterns[]='##delete##';
        if ($canmanageentries || (!$readonly && data_isowner($record->id))) {
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/edit.php?d='
                             .$data->id.'&amp;rid='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/edit') . '" class="iconsmall" alt="'.get_string('edit').'" title="'.get_string('edit').'" /></a>';
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='
                             .$data->id.'&amp;delete='.$record->id.'&amp;sesskey='.sesskey().'"><img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall" alt="'.get_string('delete').'" title="'.get_string('delete').'" /></a>';
        } else {
            $replacement[] = '';
            $replacement[] = '';
        }

        $moreurl = $CFG->wwwroot . '/mod/data/view.php?d=' . $data->id . '&amp;rid=' . $record->id;
        if ($search) {
            $moreurl .= '&amp;filter=1';
        }
        $patterns[]='##more##';
        $replacement[] = '<a href="'.$moreurl.'"><img src="'.$OUTPUT->pix_url('t/preview').
                        '" class="iconsmall" alt="'.get_string('more', 'data').'" title="'.get_string('more', 'data').
                        '" /></a>';

        $patterns[]='##moreurl##';
        $replacement[] = $moreurl;

        $patterns[]='##delcheck##';
        if ($canmanageentries) {
            $replacement[] = html_writer::checkbox('delcheck[]', $record->id, false, '', array('class' => 'recordcheckbox'));
        } else {
            $replacement[] = '';
        }

        $patterns[]='##user##';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$record->userid.
                               '&amp;course='.$data->course.'">'.fullname($record).'</a>';

        $patterns[] = '##userpicture##';
        $ruser = user_picture::unalias($record, null, 'userid');
        $replacement[] = $OUTPUT->user_picture($ruser, array('courseid' => $data->course));

        $patterns[]='##export##';

        if (!empty($CFG->enableportfolios) && ($template == 'singletemplate' || $template == 'listtemplate')
            && ((has_capability('mod/data:exportentry', $context)
                || (data_isowner($record->id) && has_capability('mod/data:exportownentry', $context))))) {
            require_once($CFG->libdir . '/portfoliolib.php');
            $button = new portfolio_add_button();
            $button->set_callback_options('data_portfolio_caller', array('id' => $cm->id, 'recordid' => $record->id), 'mod_data');
            list($formats, $files) = data_portfolio_caller::formats($fields, $record);
            $button->set_formats($formats);
            $replacement[] = $button->to_html(PORTFOLIO_ADD_ICON_LINK);
        } else {
            $replacement[] = '';
        }

        $patterns[] = '##timeadded##';
        $replacement[] = userdate($record->timecreated);

        $patterns[] = '##timemodified##';
        $replacement [] = userdate($record->timemodified);

        $patterns[]='##approve##';
        if (has_capability('mod/data:approve', $context) && ($data->approval) && (!$record->approved)) {
            $approveurl = new moodle_url($jumpurl, array('approve' => $record->id));
            $approveicon = new pix_icon('t/approve', get_string('approve', 'data'), '', array('class' => 'iconsmall'));
            $replacement[] = html_writer::tag('span', $OUTPUT->action_icon($approveurl, $approveicon),
                    array('class' => 'approve'));
        } else {
            $replacement[] = '';
        }

        $patterns[]='##disapprove##';
        if (has_capability('mod/data:approve', $context) && ($data->approval) && ($record->approved)) {
            $disapproveurl = new moodle_url($jumpurl, array('disapprove' => $record->id));
            $disapproveicon = new pix_icon('t/block', get_string('disapprove', 'data'), '', array('class' => 'iconsmall'));
            $replacement[] = html_writer::tag('span', $OUTPUT->action_icon($disapproveurl, $disapproveicon),
                    array('class' => 'disapprove'));
        } else {
            $replacement[] = '';
        }

        $patterns[]='##comments##';
        if (($template == 'listtemplate') && ($data->comments)) {

            if (!empty($CFG->usecomments)) {
                require_once($CFG->dirroot  . '/comment/lib.php');
                list($context, $course, $cm) = get_context_info_array($context->id);
                $cmt = new stdClass();
                $cmt->context = $context;
                $cmt->course  = $course;
                $cmt->cm      = $cm;
                $cmt->area    = 'database_entry';
                $cmt->itemid  = $record->id;
                $cmt->showcount = true;
                $cmt->component = 'mod_data';
                $comment = new comment($cmt);
                $replacement[] = $comment->output(true);
            }
        } else {
            $replacement[] = '';
        }

        // actual replacement of the tags
        $newtext = str_ireplace($patterns, $replacement, $data->{$template});

        // no more html formatting and filtering - see MDL-6635
        if ($return) {
            return $newtext;
        } else {
            echo $newtext;

            // hack alert - return is always false in singletemplate anyway ;-)
            /**********************************
             *    Printing Ratings Form       *
             *********************************/
            if ($template == 'singletemplate') {    //prints ratings options
                data_print_ratings($data, $record);
            }

            /**********************************
             *    Printing Comments Form       *
             *********************************/
            if (($template == 'singletemplate') && ($data->comments)) {
                if (!empty($CFG->usecomments)) {
                    require_once($CFG->dirroot . '/comment/lib.php');
                    list($context, $course, $cm) = get_context_info_array($context->id);
                    $cmt = new stdClass();
                    $cmt->context = $context;
                    $cmt->course  = $course;
                    $cmt->cm      = $cm;
                    $cmt->area    = 'database_entry';
                    $cmt->itemid  = $record->id;
                    $cmt->showcount = true;
                    $cmt->component = 'mod_data';
                    $comment = new comment($cmt);
                    $comment->output(false);
                }
            }
        }
    }
}

/**
 * Return rating related permissions
 *
 * @param string $contextid the context id
 * @param string $component the component to get rating permissions for
 * @param string $ratingarea the rating area to get permissions for
 * @return array an associative array of the user's rating permissions
 */
function data_rating_permissions($contextid, $component, $ratingarea) {
    $context = context::instance_by_id($contextid, MUST_EXIST);
    if ($component != 'mod_data' || $ratingarea != 'entry') {
        return null;
    }
    return array(
        'view'    => has_capability('mod/data:viewrating',$context),
        'viewany' => has_capability('mod/data:viewanyrating',$context),
        'viewall' => has_capability('mod/data:viewallratings',$context),
        'rate'    => has_capability('mod/data:rate',$context)
    );
}

/**
 * Validates a submitted rating
 * @param array $params submitted data
 *            context => object the context in which the rated items exists [required]
 *            itemid => int the ID of the object being rated
 *            scaleid => int the scale from which the user can select a rating. Used for bounds checking. [required]
 *            rating => int the submitted rating
 *            rateduserid => int the id of the user whose items have been rated. NOT the user who submitted the ratings. 0 to update all. [required]
 *            aggregation => int the aggregation method to apply when calculating grades ie RATING_AGGREGATE_AVERAGE [required]
 * @return boolean true if the rating is valid. Will throw rating_exception if not
 */
function data_rating_validate($params) {
    global $DB, $USER;

    // Check the component is mod_data
    if ($params['component'] != 'mod_data') {
        throw new rating_exception('invalidcomponent');
    }

    // Check the ratingarea is entry (the only rating area in data module)
    if ($params['ratingarea'] != 'entry') {
        throw new rating_exception('invalidratingarea');
    }

    // Check the rateduserid is not the current user .. you can't rate your own entries
    if ($params['rateduserid'] == $USER->id) {
        throw new rating_exception('nopermissiontorate');
    }

    $datasql = "SELECT d.id as dataid, d.scale, d.course, r.userid as userid, d.approval, r.approved, r.timecreated, d.assesstimestart, d.assesstimefinish, r.groupid
                  FROM {data_records} r
                  JOIN {data} d ON r.dataid = d.id
                 WHERE r.id = :itemid";
    $dataparams = array('itemid'=>$params['itemid']);
    if (!$info = $DB->get_record_sql($datasql, $dataparams)) {
        //item doesn't exist
        throw new rating_exception('invaliditemid');
    }

    if ($info->scale != $params['scaleid']) {
        //the scale being submitted doesnt match the one in the database
        throw new rating_exception('invalidscaleid');
    }

    //check that the submitted rating is valid for the scale

    // lower limit
    if ($params['rating'] < 0  && $params['rating'] != RATING_UNSET_RATING) {
        throw new rating_exception('invalidnum');
    }

    // upper limit
    if ($info->scale < 0) {
        //its a custom scale
        $scalerecord = $DB->get_record('scale', array('id' => -$info->scale));
        if ($scalerecord) {
            $scalearray = explode(',', $scalerecord->scale);
            if ($params['rating'] > count($scalearray)) {
                throw new rating_exception('invalidnum');
            }
        } else {
            throw new rating_exception('invalidscaleid');
        }
    } else if ($params['rating'] > $info->scale) {
        //if its numeric and submitted rating is above maximum
        throw new rating_exception('invalidnum');
    }

    if ($info->approval && !$info->approved) {
        //database requires approval but this item isnt approved
        throw new rating_exception('nopermissiontorate');
    }

    // check the item we're rating was created in the assessable time window
    if (!empty($info->assesstimestart) && !empty($info->assesstimefinish)) {
        if ($info->timecreated < $info->assesstimestart || $info->timecreated > $info->assesstimefinish) {
            throw new rating_exception('notavailable');
        }
    }

    $course = $DB->get_record('course', array('id'=>$info->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('data', $info->dataid, $course->id, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    // if the supplied context doesnt match the item's context
    if ($context->id != $params['context']->id) {
        throw new rating_exception('invalidcontext');
    }

    // Make sure groups allow this user to see the item they're rating
    $groupid = $info->groupid;
    if ($groupid > 0 and $groupmode = groups_get_activity_groupmode($cm, $course)) {   // Groups are being used
        if (!groups_group_exists($groupid)) { // Can't find group
            throw new rating_exception('cannotfindgroup');//something is wrong
        }

        if (!groups_is_member($groupid) and !has_capability('moodle/site:accessallgroups', $context)) {
            // do not allow rating of posts from other groups when in SEPARATEGROUPS or VISIBLEGROUPS
            throw new rating_exception('notmemberofgroup');
        }
    }

    return true;
}

/**
 * Can the current user see ratings for a given itemid?
 *
 * @param array $params submitted data
 *            contextid => int contextid [required]
 *            component => The component for this module - should always be mod_data [required]
 *            ratingarea => object the context in which the rated items exists [required]
 *            itemid => int the ID of the object being rated [required]
 *            scaleid => int scale id [optional]
 * @return bool
 * @throws coding_exception
 * @throws rating_exception
 */
function mod_data_rating_can_see_item_ratings($params) {
    global $DB;

    // Check the component is mod_data.
    if (!isset($params['component']) || $params['component'] != 'mod_data') {
        throw new rating_exception('invalidcomponent');
    }

    // Check the ratingarea is entry (the only rating area in data).
    if (!isset($params['ratingarea']) || $params['ratingarea'] != 'entry') {
        throw new rating_exception('invalidratingarea');
    }

    if (!isset($params['itemid'])) {
        throw new rating_exception('invaliditemid');
    }

    $datasql = "SELECT d.id as dataid, d.course, r.groupid
                  FROM {data_records} r
                  JOIN {data} d ON r.dataid = d.id
                 WHERE r.id = :itemid";
    $dataparams = array('itemid' => $params['itemid']);
    if (!$info = $DB->get_record_sql($datasql, $dataparams)) {
        // Item doesn't exist.
        throw new rating_exception('invaliditemid');
    }

    // User can see ratings of all participants.
    if ($info->groupid == 0) {
        return true;
    }

    $course = $DB->get_record('course', array('id' => $info->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('data', $info->dataid, $course->id, false, MUST_EXIST);

    // Make sure groups allow this user to see the item they're rating.
    return groups_group_visible($info->groupid, $course, $cm);
}


/**
 * function that takes in the current data, number of items per page,
 * a search string and prints a preference box in view.php
 *
 * This preference box prints a searchable advanced search template if
 *     a) A template is defined
 *  b) The advanced search checkbox is checked.
 *
 * @global object
 * @global object
 * @param object $data
 * @param int $perpage
 * @param string $search
 * @param string $sort
 * @param string $order
 * @param array $search_array
 * @param int $advanced
 * @param string $mode
 * @return void
 */
function data_print_preference_form($data, $perpage, $search, $sort='', $order='ASC', $search_array = '', $advanced = 0, $mode= ''){
    global $CFG, $DB, $PAGE, $OUTPUT;

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = context_module::instance($cm->id);
    echo '<br /><div class="datapreferences">';
    echo '<form id="options" action="view.php" method="get">';
    echo '<div>';
    echo '<input type="hidden" name="d" value="'.$data->id.'" />';
    if ($mode =='asearch') {
        $advanced = 1;
        echo '<input type="hidden" name="mode" value="list" />';
    }
    echo '<label for="pref_perpage">'.get_string('pagesize','data').'</label> ';
    $pagesizes = array(2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,15=>15,
                       20=>20,30=>30,40=>40,50=>50,100=>100,200=>200,300=>300,400=>400,500=>500,1000=>1000);
    echo html_writer::select($pagesizes, 'perpage', $perpage, false, array('id'=>'pref_perpage'));

    if ($advanced) {
        $regsearchclass = 'search_none';
        $advancedsearchclass = 'search_inline';
    } else {
        $regsearchclass = 'search_inline';
        $advancedsearchclass = 'search_none';
    }
    echo '<div id="reg_search" class="' . $regsearchclass . '" >&nbsp;&nbsp;&nbsp;';
    echo '<label for="pref_search">'.get_string('search').'</label> <input type="text" size="16" name="search" id= "pref_search" value="'.s($search).'" /></div>';
    echo '&nbsp;&nbsp;&nbsp;<label for="pref_sortby">'.get_string('sortby').'</label> ';
    // foreach field, print the option
    echo '<select name="sort" id="pref_sortby">';
    if ($fields = $DB->get_records('data_fields', array('dataid'=>$data->id), 'name')) {
        echo '<optgroup label="'.get_string('fields', 'data').'">';
        foreach ($fields as $field) {
            if ($field->id == $sort) {
                echo '<option value="'.$field->id.'" selected="selected">'.$field->name.'</option>';
            } else {
                echo '<option value="'.$field->id.'">'.$field->name.'</option>';
            }
        }
        echo '</optgroup>';
    }
    $options = array();
    $options[DATA_TIMEADDED]    = get_string('timeadded', 'data');
    $options[DATA_TIMEMODIFIED] = get_string('timemodified', 'data');
    $options[DATA_FIRSTNAME]    = get_string('authorfirstname', 'data');
    $options[DATA_LASTNAME]     = get_string('authorlastname', 'data');
    if ($data->approval and has_capability('mod/data:approve', $context)) {
        $options[DATA_APPROVED] = get_string('approved', 'data');
    }
    echo '<optgroup label="'.get_string('other', 'data').'">';
    foreach ($options as $key => $name) {
        if ($key == $sort) {
            echo '<option value="'.$key.'" selected="selected">'.$name.'</option>';
        } else {
            echo '<option value="'.$key.'">'.$name.'</option>';
        }
    }
    echo '</optgroup>';
    echo '</select>';
    echo '<label for="pref_order" class="accesshide">'.get_string('order').'</label>';
    echo '<select id="pref_order" name="order">';
    if ($order == 'ASC') {
        echo '<option value="ASC" selected="selected">'.get_string('ascending','data').'</option>';
    } else {
        echo '<option value="ASC">'.get_string('ascending','data').'</option>';
    }
    if ($order == 'DESC') {
        echo '<option value="DESC" selected="selected">'.get_string('descending','data').'</option>';
    } else {
        echo '<option value="DESC">'.get_string('descending','data').'</option>';
    }
    echo '</select>';

    if ($advanced) {
        $checked = ' checked="checked" ';
    }
    else {
        $checked = '';
    }
    $PAGE->requires->js('/mod/data/data.js');
    echo '&nbsp;<input type="hidden" name="advanced" value="0" />';
    echo '&nbsp;<input type="hidden" name="filter" value="1" />';
    echo '&nbsp;<input type="checkbox" id="advancedcheckbox" name="advanced" value="1" '.$checked.' onchange="showHideAdvSearch(this.checked);" /><label for="advancedcheckbox">'.get_string('advancedsearch', 'data').'</label>';
    echo '&nbsp;<input type="submit" value="'.get_string('savesettings','data').'" />';

    echo '<br />';
    echo '<div class="' . $advancedsearchclass . '" id="data_adv_form">';
    echo '<table class="boxaligncenter">';

    // print ASC or DESC
    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    $i = 0;

    // Determine if we are printing all fields for advanced search, or the template for advanced search
    // If a template is not defined, use the deafault template and display all fields.
    if(empty($data->asearchtemplate)) {
        data_generate_default_template($data, 'asearchtemplate');
    }

    static $fields = NULL;
    static $isteacher;
    static $dataid = NULL;

    if (empty($dataid)) {
        $dataid = $data->id;
    } else if ($dataid != $data->id) {
        $fields = NULL;
    }

    if (empty($fields)) {
        $fieldrecords = $DB->get_records('data_fields', array('dataid'=>$data->id));
        foreach ($fieldrecords as $fieldrecord) {
            $fields[]= data_get_field($fieldrecord, $data);
        }

        $isteacher = has_capability('mod/data:managetemplates', $context);
    }

    // Replacing tags
    $patterns = array();
    $replacement = array();

    // Then we generate strings to replace for normal tags
    foreach ($fields as $field) {
        $fieldname = $field->field->name;
        $fieldname = preg_quote($fieldname, '/');
        $patterns[] = "/\[\[$fieldname\]\]/i";
        $searchfield = data_get_field_from_id($field->field->id, $data);
        if (!empty($search_array[$field->field->id]->data)) {
            $replacement[] = $searchfield->display_search_field($search_array[$field->field->id]->data);
        } else {
            $replacement[] = $searchfield->display_search_field();
        }
    }
    $fn = !empty($search_array[DATA_FIRSTNAME]->data) ? $search_array[DATA_FIRSTNAME]->data : '';
    $ln = !empty($search_array[DATA_LASTNAME]->data) ? $search_array[DATA_LASTNAME]->data : '';
    $patterns[]    = '/##firstname##/';
    $replacement[] = '<label class="accesshide" for="u_fn">'.get_string('authorfirstname', 'data').'</label><input type="text" size="16" id="u_fn" name="u_fn" value="'.$fn.'" />';
    $patterns[]    = '/##lastname##/';
    $replacement[] = '<label class="accesshide" for="u_ln">'.get_string('authorlastname', 'data').'</label><input type="text" size="16" id="u_ln" name="u_ln" value="'.$ln.'" />';

    // actual replacement of the tags
    $newtext = preg_replace($patterns, $replacement, $data->asearchtemplate);

    $options = new stdClass();
    $options->para=false;
    $options->noclean=true;
    echo '<tr><td>';
    echo format_text($newtext, FORMAT_HTML, $options);
    echo '</td></tr>';

    echo '<tr><td colspan="4"><br/><input type="submit" value="'.get_string('savesettings','data').'" /><input type="submit" name="resetadv" value="'.get_string('resetsettings','data').'" /></td></tr>';
    echo '</table>';
    echo '</div>';
    echo '</div>';
    echo '</form>';
    echo '</div>';
}

/**
 * @global object
 * @global object
 * @param object $data
 * @param object $record
 * @return void Output echo'd
 */
function data_print_ratings($data, $record) {
    global $OUTPUT;
    if (!empty($record->rating)){
        echo $OUTPUT->render($record->rating);
    }
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function data_get_view_actions() {
    return array('view');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function data_get_post_actions() {
    return array('add','update','record delete');
}

/**
 * @param string $name
 * @param int $dataid
 * @param int $fieldid
 * @return bool
 */
function data_fieldname_exists($name, $dataid, $fieldid = 0) {
    global $DB;

    if (!is_numeric($name)) {
        $like = $DB->sql_like('df.name', ':name', false);
    } else {
        $like = "df.name = :name";
    }
    $params = array('name'=>$name);
    if ($fieldid) {
        $params['dataid']   = $dataid;
        $params['fieldid1'] = $fieldid;
        $params['fieldid2'] = $fieldid;
        return $DB->record_exists_sql("SELECT * FROM {data_fields} df
                                        WHERE $like AND df.dataid = :dataid
                                              AND ((df.id < :fieldid1) OR (df.id > :fieldid2))", $params);
    } else {
        $params['dataid']   = $dataid;
        return $DB->record_exists_sql("SELECT * FROM {data_fields} df
                                        WHERE $like AND df.dataid = :dataid", $params);
    }
}

/**
 * @param array $fieldinput
 */
function data_convert_arrays_to_strings(&$fieldinput) {
    foreach ($fieldinput as $key => $val) {
        if (is_array($val)) {
            $str = '';
            foreach ($val as $inner) {
                $str .= $inner . ',';
            }
            $str = substr($str, 0, -1);

            $fieldinput->$key = $str;
        }
    }
}


/**
 * Converts a database (module instance) to use the Roles System
 *
 * @global object
 * @global object
 * @uses CONTEXT_MODULE
 * @uses CAP_PREVENT
 * @uses CAP_ALLOW
 * @param object $data a data object with the same attributes as a record
 *                     from the data database table
 * @param int $datamodid the id of the data module, from the modules table
 * @param array $teacherroles array of roles that have archetype teacher
 * @param array $studentroles array of roles that have archetype student
 * @param array $guestroles array of roles that have archetype guest
 * @param int $cmid the course_module id for this data instance
 * @return boolean data module was converted or not
 */
function data_convert_to_roles($data, $teacherroles=array(), $studentroles=array(), $cmid=NULL) {
    global $CFG, $DB, $OUTPUT;

    if (!isset($data->participants) && !isset($data->assesspublic)
            && !isset($data->groupmode)) {
        // We assume that this database has already been converted to use the
        // Roles System. above fields get dropped the data module has been
        // upgraded to use Roles.
        return false;
    }

    if (empty($cmid)) {
        // We were not given the course_module id. Try to find it.
        if (!$cm = get_coursemodule_from_instance('data', $data->id)) {
            echo $OUTPUT->notification('Could not get the course module for the data');
            return false;
        } else {
            $cmid = $cm->id;
        }
    }
    $context = context_module::instance($cmid);


    // $data->participants:
    // 1 - Only teachers can add entries
    // 3 - Teachers and students can add entries
    switch ($data->participants) {
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:writeentry', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:writeentry', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 3:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:writeentry', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:writeentry', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }

    // $data->assessed:
    // 2 - Only teachers can rate posts
    // 1 - Everyone can rate posts
    // 0 - No one can rate posts
    switch ($data->assessed) {
        case 0:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:rate', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:rate', CAP_PREVENT, $teacherrole->id, $context->id);
            }
            break;
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:rate', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:rate', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 2:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:rate', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:rate', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }

    // $data->assesspublic:
    // 0 - Students can only see their own ratings
    // 1 - Students can see everyone's ratings
    switch ($data->assesspublic) {
        case 0:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:viewrating', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:viewrating', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case 1:
            foreach ($studentroles as $studentrole) {
                assign_capability('mod/data:viewrating', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('mod/data:viewrating', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }

    if (empty($cm)) {
        $cm = $DB->get_record('course_modules', array('id'=>$cmid));
    }

    switch ($cm->groupmode) {
        case NOGROUPS:
            break;
        case SEPARATEGROUPS:
            foreach ($studentroles as $studentrole) {
                assign_capability('moodle/site:accessallgroups', CAP_PREVENT, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
        case VISIBLEGROUPS:
            foreach ($studentroles as $studentrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $studentrole->id, $context->id);
            }
            foreach ($teacherroles as $teacherrole) {
                assign_capability('moodle/site:accessallgroups', CAP_ALLOW, $teacherrole->id, $context->id);
            }
            break;
    }
    return true;
}

/**
 * Returns the best name to show for a preset
 *
 * @param string $shortname
 * @param  string $path
 * @return string
 */
function data_preset_name($shortname, $path) {

    // We are looking inside the preset itself as a first choice, but also in normal data directory
    $string = get_string('modulename', 'datapreset_'.$shortname);

    if (substr($string, 0, 1) == '[') {
        return $shortname;
    } else {
        return $string;
    }
}

/**
 * Returns an array of all the available presets.
 *
 * @return array
 */
function data_get_available_presets($context) {
    global $CFG, $USER;

    $presets = array();

    // First load the ratings sub plugins that exist within the modules preset dir
    if ($dirs = core_component::get_plugin_list('datapreset')) {
        foreach ($dirs as $dir=>$fulldir) {
            if (is_directory_a_preset($fulldir)) {
                $preset = new stdClass();
                $preset->path = $fulldir;
                $preset->userid = 0;
                $preset->shortname = $dir;
                $preset->name = data_preset_name($dir, $fulldir);
                if (file_exists($fulldir.'/screenshot.jpg')) {
                    $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.jpg';
                } else if (file_exists($fulldir.'/screenshot.png')) {
                    $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.png';
                } else if (file_exists($fulldir.'/screenshot.gif')) {
                    $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$dir.'/screenshot.gif';
                }
                $presets[] = $preset;
            }
        }
    }
    // Now add to that the site presets that people have saved
    $presets = data_get_available_site_presets($context, $presets);
    return $presets;
}

/**
 * Gets an array of all of the presets that users have saved to the site.
 *
 * @param stdClass $context The context that we are looking from.
 * @param array $presets
 * @return array An array of presets
 */
function data_get_available_site_presets($context, array $presets=array()) {
    global $USER;

    $fs = get_file_storage();
    $files = $fs->get_area_files(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA);
    $canviewall = has_capability('mod/data:viewalluserpresets', $context);
    if (empty($files)) {
        return $presets;
    }
    foreach ($files as $file) {
        if (($file->is_directory() && $file->get_filepath()=='/') || !$file->is_directory() || (!$canviewall && $file->get_userid() != $USER->id)) {
            continue;
        }
        $preset = new stdClass;
        $preset->path = $file->get_filepath();
        $preset->name = trim($preset->path, '/');
        $preset->shortname = $preset->name;
        $preset->userid = $file->get_userid();
        $preset->id = $file->get_id();
        $preset->storedfile = $file;
        $presets[] = $preset;
    }
    return $presets;
}

/**
 * Deletes a saved preset.
 *
 * @param string $name
 * @return bool
 */
function data_delete_site_preset($name) {
    $fs = get_file_storage();

    $files = $fs->get_directory_files(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA, 0, '/'.$name.'/');
    if (!empty($files)) {
        foreach ($files as $file) {
            $file->delete();
        }
    }

    $dir = $fs->get_file(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA, 0, '/'.$name.'/', '.');
    if (!empty($dir)) {
        $dir->delete();
    }
    return true;
}

/**
 * Prints the heads for a page
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $data
 * @param string $currenttab
 */
function data_print_header($course, $cm, $data, $currenttab='') {

    global $CFG, $displaynoticegood, $displaynoticebad, $OUTPUT, $PAGE;

    $PAGE->set_title($data->name);
    echo $OUTPUT->header();
    echo $OUTPUT->heading(format_string($data->name), 2);
    echo $OUTPUT->box(format_module_intro('data', $data, $cm->id), 'generalbox', 'intro');

    // Groups needed for Add entry tab
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

    // Print the tabs

    if ($currenttab) {
        include('tabs.php');
    }

    // Print any notices

    if (!empty($displaynoticegood)) {
        echo $OUTPUT->notification($displaynoticegood, 'notifysuccess');    // good (usually green)
    } else if (!empty($displaynoticebad)) {
        echo $OUTPUT->notification($displaynoticebad);                     // bad (usuually red)
    }
}

/**
 * Can user add more entries?
 *
 * @param object $data
 * @param mixed $currentgroup
 * @param int $groupmode
 * @param stdClass $context
 * @return bool
 */
function data_user_can_add_entry($data, $currentgroup, $groupmode, $context = null) {
    global $USER;

    if (empty($context)) {
        $cm = get_coursemodule_from_instance('data', $data->id, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
    }

    if (has_capability('mod/data:manageentries', $context)) {
        // no entry limits apply if user can manage

    } else if (!has_capability('mod/data:writeentry', $context)) {
        return false;

    } else if (data_atmaxentries($data)) {
        return false;
    } else if (data_in_readonly_period($data)) {
        // Check whether we're in a read-only period
        return false;
    }

    if (!$groupmode or has_capability('moodle/site:accessallgroups', $context)) {
        return true;
    }

    if ($currentgroup) {
        return groups_is_member($currentgroup);
    } else {
        //else it might be group 0 in visible mode
        if ($groupmode == VISIBLEGROUPS){
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Check whether the specified database activity is currently in a read-only period
 *
 * @param object $data
 * @return bool returns true if the time fields in $data indicate a read-only period; false otherwise
 */
function data_in_readonly_period($data) {
    $now = time();
    if (!$data->timeviewfrom && !$data->timeviewto) {
        return false;
    } else if (($data->timeviewfrom && $now < $data->timeviewfrom) || ($data->timeviewto && $now > $data->timeviewto)) {
        return false;
    }
    return true;
}

/**
 * @return bool
 */
function is_directory_a_preset($directory) {
    $directory = rtrim($directory, '/\\') . '/';
    $status = file_exists($directory.'singletemplate.html') &&
              file_exists($directory.'listtemplate.html') &&
              file_exists($directory.'listtemplateheader.html') &&
              file_exists($directory.'listtemplatefooter.html') &&
              file_exists($directory.'addtemplate.html') &&
              file_exists($directory.'rsstemplate.html') &&
              file_exists($directory.'rsstitletemplate.html') &&
              file_exists($directory.'csstemplate.css') &&
              file_exists($directory.'jstemplate.js') &&
              file_exists($directory.'preset.xml');

    return $status;
}

/**
 * Abstract class used for data preset importers
 */
abstract class data_preset_importer {

    protected $course;
    protected $cm;
    protected $module;
    protected $directory;

    /**
     * Constructor
     *
     * @param stdClass $course
     * @param stdClass $cm
     * @param stdClass $module
     * @param string $directory
     */
    public function __construct($course, $cm, $module, $directory) {
        $this->course = $course;
        $this->cm = $cm;
        $this->module = $module;
        $this->directory = $directory;
    }

    /**
     * Returns the name of the directory the preset is located in
     * @return string
     */
    public function get_directory() {
        return basename($this->directory);
    }

    /**
     * Retreive the contents of a file. That file may either be in a conventional directory of the Moodle file storage
     * @param file_storage $filestorage. should be null if using a conventional directory
     * @param stored_file $fileobj the directory to look in. null if using a conventional directory
     * @param string $dir the directory to look in. null if using the Moodle file storage
     * @param string $filename the name of the file we want
     * @return string the contents of the file or null if the file doesn't exist.
     */
    public function data_preset_get_file_contents(&$filestorage, &$fileobj, $dir, $filename) {
        if(empty($filestorage) || empty($fileobj)) {
            if (substr($dir, -1)!='/') {
                $dir .= '/';
            }
            if (file_exists($dir.$filename)) {
                return file_get_contents($dir.$filename);
            } else {
                return null;
            }
        } else {
            if ($filestorage->file_exists(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA, 0, $fileobj->get_filepath(), $filename)) {
                $file = $filestorage->get_file(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA, 0, $fileobj->get_filepath(), $filename);
                return $file->get_content();
            } else {
                return null;
            }
        }

    }
    /**
     * Gets the preset settings
     * @global moodle_database $DB
     * @return stdClass
     */
    public function get_preset_settings() {
        global $DB;

        $fs = $fileobj = null;
        if (!is_directory_a_preset($this->directory)) {
            //maybe the user requested a preset stored in the Moodle file storage

            $fs = get_file_storage();
            $files = $fs->get_area_files(DATA_PRESET_CONTEXT, DATA_PRESET_COMPONENT, DATA_PRESET_FILEAREA);

            //preset name to find will be the final element of the directory
            $explodeddirectory = explode('/', $this->directory);
            $presettofind = end($explodeddirectory);

            //now go through the available files available and see if we can find it
            foreach ($files as $file) {
                if (($file->is_directory() && $file->get_filepath()=='/') || !$file->is_directory()) {
                    continue;
                }
                $presetname = trim($file->get_filepath(), '/');
                if ($presetname==$presettofind) {
                    $this->directory = $presetname;
                    $fileobj = $file;
                }
            }

            if (empty($fileobj)) {
                print_error('invalidpreset', 'data', '', $this->directory);
            }
        }

        $allowed_settings = array(
            'intro',
            'comments',
            'requiredentries',
            'requiredentriestoview',
            'maxentries',
            'rssarticles',
            'approval',
            'defaultsortdir',
            'defaultsort');

        $result = new stdClass;
        $result->settings = new stdClass;
        $result->importfields = array();
        $result->currentfields = $DB->get_records('data_fields', array('dataid'=>$this->module->id));
        if (!$result->currentfields) {
            $result->currentfields = array();
        }


        /* Grab XML */
        $presetxml = $this->data_preset_get_file_contents($fs, $fileobj, $this->directory,'preset.xml');
        $parsedxml = xmlize($presetxml, 0);

        /* First, do settings. Put in user friendly array. */
        $settingsarray = $parsedxml['preset']['#']['settings'][0]['#'];
        $result->settings = new StdClass();
        foreach ($settingsarray as $setting => $value) {
            if (!is_array($value) || !in_array($setting, $allowed_settings)) {
                // unsupported setting
                continue;
            }
            $result->settings->$setting = $value[0]['#'];
        }

        /* Now work out fields to user friendly array */
        $fieldsarray = $parsedxml['preset']['#']['field'];
        foreach ($fieldsarray as $field) {
            if (!is_array($field)) {
                continue;
            }
            $f = new StdClass();
            foreach ($field['#'] as $param => $value) {
                if (!is_array($value)) {
                    continue;
                }
                $f->$param = $value[0]['#'];
            }
            $f->dataid = $this->module->id;
            $f->type = clean_param($f->type, PARAM_ALPHA);
            $result->importfields[] = $f;
        }
        /* Now add the HTML templates to the settings array so we can update d */
        $result->settings->singletemplate     = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"singletemplate.html");
        $result->settings->listtemplate       = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"listtemplate.html");
        $result->settings->listtemplateheader = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"listtemplateheader.html");
        $result->settings->listtemplatefooter = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"listtemplatefooter.html");
        $result->settings->addtemplate        = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"addtemplate.html");
        $result->settings->rsstemplate        = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"rsstemplate.html");
        $result->settings->rsstitletemplate   = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"rsstitletemplate.html");
        $result->settings->csstemplate        = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"csstemplate.css");
        $result->settings->jstemplate         = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"jstemplate.js");
        $result->settings->asearchtemplate    = $this->data_preset_get_file_contents($fs, $fileobj,$this->directory,"asearchtemplate.html");

        $result->settings->instance = $this->module->id;
        return $result;
    }

    /**
     * Import the preset into the given database module
     * @return bool
     */
    function import($overwritesettings) {
        global $DB, $CFG;

        $params = $this->get_preset_settings();
        $settings = $params->settings;
        $newfields = $params->importfields;
        $currentfields = $params->currentfields;
        $preservedfields = array();

        /* Maps fields and makes new ones */
        if (!empty($newfields)) {
            /* We require an injective mapping, and need to know what to protect */
            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);
                if ($cid == -1) {
                    continue;
                }
                if (array_key_exists($cid, $preservedfields)){
                    print_error('notinjectivemap', 'data');
                }
                else $preservedfields[$cid] = true;
            }

            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);

                /* A mapping. Just need to change field params. Data kept. */
                if ($cid != -1 and isset($currentfields[$cid])) {
                    $fieldobject = data_get_field_from_id($currentfields[$cid]->id, $this->module);
                    foreach ($newfield as $param => $value) {
                        if ($param != "id") {
                            $fieldobject->field->$param = $value;
                        }
                    }
                    unset($fieldobject->field->similarfield);
                    $fieldobject->update_field();
                    unset($fieldobject);
                } else {
                    /* Make a new field */
                    include_once("field/$newfield->type/field.class.php");

                    if (!isset($newfield->description)) {
                        $newfield->description = '';
                    }
                    $classname = 'data_field_'.$newfield->type;
                    $fieldclass = new $classname($newfield, $this->module);
                    $fieldclass->insert_field();
                    unset($fieldclass);
                }
            }
        }

        /* Get rid of all old unused data */
        if (!empty($preservedfields)) {
            foreach ($currentfields as $cid => $currentfield) {
                if (!array_key_exists($cid, $preservedfields)) {
                    /* Data not used anymore so wipe! */
                    print "Deleting field $currentfield->name<br />";

                    $id = $currentfield->id;
                    //Why delete existing data records and related comments/ratings??
                    $DB->delete_records('data_content', array('fieldid'=>$id));
                    $DB->delete_records('data_fields', array('id'=>$id));
                }
            }
        }

        // handle special settings here
        if (!empty($settings->defaultsort)) {
            if (is_numeric($settings->defaultsort)) {
                // old broken value
                $settings->defaultsort = 0;
            } else {
                $settings->defaultsort = (int)$DB->get_field('data_fields', 'id', array('dataid'=>$this->module->id, 'name'=>$settings->defaultsort));
            }
        } else {
            $settings->defaultsort = 0;
        }

        // do we want to overwrite all current database settings?
        if ($overwritesettings) {
            // all supported settings
            $overwrite = array_keys((array)$settings);
        } else {
            // only templates and sorting
            $overwrite = array('singletemplate', 'listtemplate', 'listtemplateheader', 'listtemplatefooter',
                               'addtemplate', 'rsstemplate', 'rsstitletemplate', 'csstemplate', 'jstemplate',
                               'asearchtemplate', 'defaultsortdir', 'defaultsort');
        }

        // now overwrite current data settings
        foreach ($this->module as $prop=>$unused) {
            if (in_array($prop, $overwrite)) {
                $this->module->$prop = $settings->$prop;
            }
        }

        data_update_instance($this->module);

        return $this->cleanup();
    }

    /**
     * Any clean up routines should go here
     * @return bool
     */
    public function cleanup() {
        return true;
    }
}

/**
 * Data preset importer for uploaded presets
 */
class data_preset_upload_importer extends data_preset_importer {
    public function __construct($course, $cm, $module, $filepath) {
        global $USER;
        if (is_file($filepath)) {
            $fp = get_file_packer();
            if ($fp->extract_to_pathname($filepath, $filepath.'_extracted')) {
                fulldelete($filepath);
            }
            $filepath .= '_extracted';
        }
        parent::__construct($course, $cm, $module, $filepath);
    }
    public function cleanup() {
        return fulldelete($this->directory);
    }
}

/**
 * Data preset importer for existing presets
 */
class data_preset_existing_importer extends data_preset_importer {
    protected $userid;
    public function __construct($course, $cm, $module, $fullname) {
        global $USER;
        list($userid, $shortname) = explode('/', $fullname, 2);
        $context = context_module::instance($cm->id);
        if ($userid && ($userid != $USER->id) && !has_capability('mod/data:manageuserpresets', $context) && !has_capability('mod/data:viewalluserpresets', $context)) {
           throw new coding_exception('Invalid preset provided');
        }

        $this->userid = $userid;
        $filepath = data_preset_path($course, $userid, $shortname);
        parent::__construct($course, $cm, $module, $filepath);
    }
    public function get_userid() {
        return $this->userid;
    }
}

/**
 * @global object
 * @global object
 * @param object $course
 * @param int $userid
 * @param string $shortname
 * @return string
 */
function data_preset_path($course, $userid, $shortname) {
    global $USER, $CFG;

    $context = context_course::instance($course->id);

    $userid = (int)$userid;

    $path = null;
    if ($userid > 0 && ($userid == $USER->id || has_capability('mod/data:viewalluserpresets', $context))) {
        $path = $CFG->dataroot.'/data/preset/'.$userid.'/'.$shortname;
    } else if ($userid == 0) {
        $path = $CFG->dirroot.'/mod/data/preset/'.$shortname;
    } else if ($userid < 0) {
        $path = $CFG->tempdir.'/data/'.-$userid.'/'.$shortname;
    }

    return $path;
}

/**
 * Implementation of the function for printing the form elements that control
 * whether the course reset functionality affects the data.
 *
 * @param $mform form passed by reference
 */
function data_reset_course_form_definition(&$mform) {
    $mform->addElement('header', 'dataheader', get_string('modulenameplural', 'data'));
    $mform->addElement('checkbox', 'reset_data', get_string('deleteallentries','data'));

    $mform->addElement('checkbox', 'reset_data_notenrolled', get_string('deletenotenrolled', 'data'));
    $mform->disabledIf('reset_data_notenrolled', 'reset_data', 'checked');

    $mform->addElement('checkbox', 'reset_data_ratings', get_string('deleteallratings'));
    $mform->disabledIf('reset_data_ratings', 'reset_data', 'checked');

    $mform->addElement('checkbox', 'reset_data_comments', get_string('deleteallcomments'));
    $mform->disabledIf('reset_data_comments', 'reset_data', 'checked');
}

/**
 * Course reset form defaults.
 * @return array
 */
function data_reset_course_form_defaults($course) {
    return array('reset_data'=>0, 'reset_data_ratings'=>1, 'reset_data_comments'=>1, 'reset_data_notenrolled'=>0);
}

/**
 * Removes all grades from gradebook
 *
 * @global object
 * @global object
 * @param int $courseid
 * @param string $type optional type
 */
function data_reset_gradebook($courseid, $type='') {
    global $CFG, $DB;

    $sql = "SELECT d.*, cm.idnumber as cmidnumber, d.course as courseid
              FROM {data} d, {course_modules} cm, {modules} m
             WHERE m.name='data' AND m.id=cm.module AND cm.instance=d.id AND d.course=?";

    if ($datas = $DB->get_records_sql($sql, array($courseid))) {
        foreach ($datas as $data) {
            data_grade_item_update($data, 'reset');
        }
    }
}

/**
 * Actual implementation of the reset course functionality, delete all the
 * data responses for course $data->courseid.
 *
 * @global object
 * @global object
 * @param object $data the data submitted from the reset course.
 * @return array status array
 */
function data_reset_userdata($data) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->dirroot.'/rating/lib.php');

    $componentstr = get_string('modulenameplural', 'data');
    $status = array();

    $allrecordssql = "SELECT r.id
                        FROM {data_records} r
                             INNER JOIN {data} d ON r.dataid = d.id
                       WHERE d.course = ?";

    $alldatassql = "SELECT d.id
                      FROM {data} d
                     WHERE d.course=?";

    $rm = new rating_manager();
    $ratingdeloptions = new stdClass;
    $ratingdeloptions->component = 'mod_data';
    $ratingdeloptions->ratingarea = 'entry';

    // Set the file storage - may need it to remove files later.
    $fs = get_file_storage();

    // delete entries if requested
    if (!empty($data->reset_data)) {
        $DB->delete_records_select('comments', "itemid IN ($allrecordssql) AND commentarea='database_entry'", array($data->courseid));
        $DB->delete_records_select('data_content', "recordid IN ($allrecordssql)", array($data->courseid));
        $DB->delete_records_select('data_records', "dataid IN ($alldatassql)", array($data->courseid));

        if ($datas = $DB->get_records_sql($alldatassql, array($data->courseid))) {
            foreach ($datas as $dataid=>$unused) {
                if (!$cm = get_coursemodule_from_instance('data', $dataid)) {
                    continue;
                }
                $datacontext = context_module::instance($cm->id);

                // Delete any files that may exist.
                $fs->delete_area_files($datacontext->id, 'mod_data', 'content');

                $ratingdeloptions->contextid = $datacontext->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        if (empty($data->reset_gradebook_grades)) {
            // remove all grades from gradebook
            data_reset_gradebook($data->courseid);
        }
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallentries', 'data'), 'error'=>false);
    }

    // remove entries by users not enrolled into course
    if (!empty($data->reset_data_notenrolled)) {
        $recordssql = "SELECT r.id, r.userid, r.dataid, u.id AS userexists, u.deleted AS userdeleted
                         FROM {data_records} r
                              JOIN {data} d ON r.dataid = d.id
                              LEFT JOIN {user} u ON r.userid = u.id
                        WHERE d.course = ? AND r.userid > 0";

        $course_context = context_course::instance($data->courseid);
        $notenrolled = array();
        $fields = array();
        $rs = $DB->get_recordset_sql($recordssql, array($data->courseid));
        foreach ($rs as $record) {
            if (array_key_exists($record->userid, $notenrolled) or !$record->userexists or $record->userdeleted
              or !is_enrolled($course_context, $record->userid)) {
                //delete ratings
                if (!$cm = get_coursemodule_from_instance('data', $record->dataid)) {
                    continue;
                }
                $datacontext = context_module::instance($cm->id);
                $ratingdeloptions->contextid = $datacontext->id;
                $ratingdeloptions->itemid = $record->id;
                $rm->delete_ratings($ratingdeloptions);

                // Delete any files that may exist.
                if ($contents = $DB->get_records('data_content', array('recordid' => $record->id), '', 'id')) {
                    foreach ($contents as $content) {
                        $fs->delete_area_files($datacontext->id, 'mod_data', 'content', $content->id);
                    }
                }
                $notenrolled[$record->userid] = true;

                $DB->delete_records('comments', array('itemid' => $record->id, 'commentarea' => 'database_entry'));
                $DB->delete_records('data_content', array('recordid' => $record->id));
                $DB->delete_records('data_records', array('id' => $record->id));
            }
        }
        $rs->close();
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deletenotenrolled', 'data'), 'error'=>false);
    }

    // remove all ratings
    if (!empty($data->reset_data_ratings)) {
        if ($datas = $DB->get_records_sql($alldatassql, array($data->courseid))) {
            foreach ($datas as $dataid=>$unused) {
                if (!$cm = get_coursemodule_from_instance('data', $dataid)) {
                    continue;
                }
                $datacontext = context_module::instance($cm->id);

                $ratingdeloptions->contextid = $datacontext->id;
                $rm->delete_ratings($ratingdeloptions);
            }
        }

        if (empty($data->reset_gradebook_grades)) {
            // remove all grades from gradebook
            data_reset_gradebook($data->courseid);
        }

        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallratings'), 'error'=>false);
    }

    // remove all comments
    if (!empty($data->reset_data_comments)) {
        $DB->delete_records_select('comments', "itemid IN ($allrecordssql) AND commentarea='database_entry'", array($data->courseid));
        $status[] = array('component'=>$componentstr, 'item'=>get_string('deleteallcomments'), 'error'=>false);
    }

    // updating dates - shift may be negative too
    if ($data->timeshift) {
        shift_course_mod_dates('data', array('timeavailablefrom', 'timeavailableto', 'timeviewfrom', 'timeviewto'), $data->timeshift, $data->courseid);
        $status[] = array('component'=>$componentstr, 'item'=>get_string('datechanged'), 'error'=>false);
    }

    return $status;
}

/**
 * Returns all other caps used in module
 *
 * @return array
 */
function data_get_extra_capabilities() {
    return array('moodle/site:accessallgroups', 'moodle/site:viewfullnames', 'moodle/rating:view', 'moodle/rating:viewany', 'moodle/rating:viewall', 'moodle/rating:rate', 'moodle/comment:view', 'moodle/comment:post', 'moodle/comment:delete');
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, null if doesn't know
 */
function data_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_RATE:                    return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}
/**
 * @global object
 * @param array $export
 * @param string $delimiter_name
 * @param object $database
 * @param int $count
 * @param bool $return
 * @return string|void
 */
function data_export_csv($export, $delimiter_name, $database, $count, $return=false) {
    global $CFG;
    require_once($CFG->libdir . '/csvlib.class.php');

    $filename = $database . '-' . $count . '-record';
    if ($count > 1) {
        $filename .= 's';
    }
    if ($return) {
        return csv_export_writer::print_array($export, $delimiter_name, '"', true);
    } else {
        csv_export_writer::download_array($filename, $export, $delimiter_name);
    }
}

/**
 * @global object
 * @param array $export
 * @param string $dataname
 * @param int $count
 * @return string
 */
function data_export_xls($export, $dataname, $count) {
    global $CFG;
    require_once("$CFG->libdir/excellib.class.php");
    $filename = clean_filename("{$dataname}-{$count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= '.xls';

    $filearg = '-';
    $workbook = new MoodleExcelWorkbook($filearg);
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] = $workbook->add_worksheet('');
    $rowno = 0;
    foreach ($export as $row) {
        $colno = 0;
        foreach($row as $col) {
            $worksheet[0]->write($rowno, $colno, $col);
            $colno++;
        }
        $rowno++;
    }
    $workbook->close();
    return $filename;
}

/**
 * @global object
 * @param array $export
 * @param string $dataname
 * @param int $count
 * @param string
 */
function data_export_ods($export, $dataname, $count) {
    global $CFG;
    require_once("$CFG->libdir/odslib.class.php");
    $filename = clean_filename("{$dataname}-{$count}_record");
    if ($count > 1) {
        $filename .= 's';
    }
    $filename .= clean_filename('-' . gmdate("Ymd_Hi"));
    $filename .= '.ods';
    $filearg = '-';
    $workbook = new MoodleODSWorkbook($filearg);
    $workbook->send($filename);
    $worksheet = array();
    $worksheet[0] = $workbook->add_worksheet('');
    $rowno = 0;
    foreach ($export as $row) {
        $colno = 0;
        foreach($row as $col) {
            $worksheet[0]->write($rowno, $colno, $col);
            $colno++;
        }
        $rowno++;
    }
    $workbook->close();
    return $filename;
}

/**
 * @global object
 * @param int $dataid
 * @param array $fields
 * @param array $selectedfields
 * @param int $currentgroup group ID of the current group. This is used for
 * exporting data while maintaining group divisions.
 * @param object $context the context in which the operation is performed (for capability checks)
 * @param bool $userdetails whether to include the details of the record author
 * @param bool $time whether to include time created/modified
 * @param bool $approval whether to include approval status
 * @return array
 */
function data_get_exportdata($dataid, $fields, $selectedfields, $currentgroup=0, $context=null,
                             $userdetails=false, $time=false, $approval=false) {
    global $DB;

    if (is_null($context)) {
        $context = context_system::instance();
    }
    // exporting user data needs special permission
    $userdetails = $userdetails && has_capability('mod/data:exportuserinfo', $context);

    $exportdata = array();

    // populate the header in first row of export
    foreach($fields as $key => $field) {
        if (!in_array($field->field->id, $selectedfields)) {
            // ignore values we aren't exporting
            unset($fields[$key]);
        } else {
            $exportdata[0][] = $field->field->name;
        }
    }
    if ($userdetails) {
        $exportdata[0][] = get_string('user');
        $exportdata[0][] = get_string('username');
        $exportdata[0][] = get_string('email');
    }
    if ($time) {
        $exportdata[0][] = get_string('timeadded', 'data');
        $exportdata[0][] = get_string('timemodified', 'data');
    }
    if ($approval) {
        $exportdata[0][] = get_string('approved', 'data');
    }

    $datarecords = $DB->get_records('data_records', array('dataid'=>$dataid));
    ksort($datarecords);
    $line = 1;
    foreach($datarecords as $record) {
        // get content indexed by fieldid
        if ($currentgroup) {
            $select = 'SELECT c.fieldid, c.content, c.content1, c.content2, c.content3, c.content4 FROM {data_content} c, {data_records} r WHERE c.recordid = ? AND r.id = c.recordid AND r.groupid = ?';
            $where = array($record->id, $currentgroup);
        } else {
            $select = 'SELECT fieldid, content, content1, content2, content3, content4 FROM {data_content} WHERE recordid = ?';
            $where = array($record->id);
        }

        if( $content = $DB->get_records_sql($select, $where) ) {
            foreach($fields as $field) {
                $contents = '';
                if(isset($content[$field->field->id])) {
                    $contents = $field->export_text_value($content[$field->field->id]);
                }
                $exportdata[$line][] = $contents;
            }
            if ($userdetails) { // Add user details to the export data
                $userdata = get_complete_user_data('id', $record->userid);
                $exportdata[$line][] = fullname($userdata);
                $exportdata[$line][] = $userdata->username;
                $exportdata[$line][] = $userdata->email;
            }
            if ($time) { // Add time added / modified
                $exportdata[$line][] = userdate($record->timecreated);
                $exportdata[$line][] = userdate($record->timemodified);
            }
            if ($approval) { // Add approval status
                $exportdata[$line][] = (int) $record->approved;
            }
        }
        $line++;
    }
    $line--;
    return $exportdata;
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Lists all browsable file areas
 *
 * @package  mod_data
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function data_get_file_areas($course, $cm, $context) {
    return array('content' => get_string('areacontent', 'mod_data'));
}

/**
 * File browsing support for data module.
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param cm_info $cm
 * @param context $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info_stored file_info_stored instance or null if not found
 */
function data_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG, $DB, $USER;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return null;
    }

    if (!isset($areas[$filearea])) {
        return null;
    }

    if (is_null($itemid)) {
        require_once($CFG->dirroot.'/mod/data/locallib.php');
        return new data_file_info_container($browser, $course, $cm, $context, $areas, $filearea);
    }

    if (!$content = $DB->get_record('data_content', array('id'=>$itemid))) {
        return null;
    }

    if (!$field = $DB->get_record('data_fields', array('id'=>$content->fieldid))) {
        return null;
    }

    if (!$record = $DB->get_record('data_records', array('id'=>$content->recordid))) {
        return null;
    }

    if (!$data = $DB->get_record('data', array('id'=>$field->dataid))) {
        return null;
    }

    //check if approved
    if ($data->approval and !$record->approved and !data_isowner($record) and !has_capability('mod/data:approve', $context)) {
        return null;
    }

    // group access
    if ($record->groupid) {
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            if (!groups_is_member($record->groupid)) {
                return null;
            }
        }
    }

    $fieldobj = data_get_field($field, $data, $cm);

    $filepath = is_null($filepath) ? '/' : $filepath;
    $filename = is_null($filename) ? '.' : $filename;
    if (!$fieldobj->file_ok($filepath.$filename)) {
        return null;
    }

    $fs = get_file_storage();
    if (!($storedfile = $fs->get_file($context->id, 'mod_data', $filearea, $itemid, $filepath, $filename))) {
        return null;
    }

    // Checks to see if the user can manage files or is the owner.
    // TODO MDL-33805 - Do not use userid here and move the capability check above.
    if (!has_capability('moodle/course:managefiles', $context) && $storedfile->get_userid() != $USER->id) {
        return null;
    }

    $urlbase = $CFG->wwwroot.'/pluginfile.php';

    return new file_info_stored($browser, $context, $storedfile, $urlbase, $itemid, true, true, false, false);
}

/**
 * Serves the data attachments. Implements needed access control ;-)
 *
 * @package  mod_data
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - justsend the file
 */
function data_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if ($filearea === 'content') {
        $contentid = (int)array_shift($args);

        if (!$content = $DB->get_record('data_content', array('id'=>$contentid))) {
            return false;
        }

        if (!$field = $DB->get_record('data_fields', array('id'=>$content->fieldid))) {
            return false;
        }

        if (!$record = $DB->get_record('data_records', array('id'=>$content->recordid))) {
            return false;
        }

        if (!$data = $DB->get_record('data', array('id'=>$field->dataid))) {
            return false;
        }

        if ($data->id != $cm->instance) {
            // hacker attempt - context does not match the contentid
            return false;
        }

        //check if approved
        if ($data->approval and !$record->approved and !data_isowner($record) and !has_capability('mod/data:approve', $context)) {
            return false;
        }

        // group access
        if ($record->groupid) {
            $groupmode = groups_get_activity_groupmode($cm, $course);
            if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
                if (!groups_is_member($record->groupid)) {
                    return false;
                }
            }
        }

        $fieldobj = data_get_field($field, $data, $cm);

        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_data/content/$content->id/$relativepath";

        if (!$fieldobj->file_ok($relativepath)) {
            return false;
        }

        $fs = get_file_storage();
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            return false;
        }

        // finally send the file
        send_stored_file($file, 0, 0, true, $options); // download MUST be forced - security!
    }

    return false;
}


function data_extend_navigation($navigation, $course, $module, $cm) {
    global $CFG, $OUTPUT, $USER, $DB;

    $rid = optional_param('rid', 0, PARAM_INT);

    $data = $DB->get_record('data', array('id'=>$cm->instance));
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

     $numentries = data_numentries($data);
    /// Check the number of entries required against the number of entries already made (doesn't apply to teachers)
    if ($data->requiredentries > 0 && $numentries < $data->requiredentries && !has_capability('mod/data:manageentries', context_module::instance($cm->id))) {
        $data->entriesleft = $data->requiredentries - $numentries;
        $entriesnode = $navigation->add(get_string('entrieslefttoadd', 'data', $data));
        $entriesnode->add_class('note');
    }

    $navigation->add(get_string('list', 'data'), new moodle_url('/mod/data/view.php', array('d'=>$cm->instance)));
    if (!empty($rid)) {
        $navigation->add(get_string('single', 'data'), new moodle_url('/mod/data/view.php', array('d'=>$cm->instance, 'rid'=>$rid)));
    } else {
        $navigation->add(get_string('single', 'data'), new moodle_url('/mod/data/view.php', array('d'=>$cm->instance, 'mode'=>'single')));
    }
    $navigation->add(get_string('search', 'data'), new moodle_url('/mod/data/view.php', array('d'=>$cm->instance, 'mode'=>'asearch')));
}

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $datanode The node to add module settings to
 */
function data_extend_settings_navigation(settings_navigation $settings, navigation_node $datanode) {
    global $PAGE, $DB, $CFG, $USER;

    $data = $DB->get_record('data', array("id" => $PAGE->cm->instance));

    $currentgroup = groups_get_activity_group($PAGE->cm);
    $groupmode = groups_get_activity_groupmode($PAGE->cm);

    if (data_user_can_add_entry($data, $currentgroup, $groupmode, $PAGE->cm->context)) { // took out participation list here!
        if (empty($editentry)) { //TODO: undefined
            $addstring = get_string('add', 'data');
        } else {
            $addstring = get_string('editentry', 'data');
        }
        $datanode->add($addstring, new moodle_url('/mod/data/edit.php', array('d'=>$PAGE->cm->instance)));
    }

    if (has_capability(DATA_CAP_EXPORT, $PAGE->cm->context)) {
        // The capability required to Export database records is centrally defined in 'lib.php'
        // and should be weaker than those required to edit Templates, Fields and Presets.
        $datanode->add(get_string('exportentries', 'data'), new moodle_url('/mod/data/export.php', array('d'=>$data->id)));
    }
    if (has_capability('mod/data:manageentries', $PAGE->cm->context)) {
        $datanode->add(get_string('importentries', 'data'), new moodle_url('/mod/data/import.php', array('d'=>$data->id)));
    }

    if (has_capability('mod/data:managetemplates', $PAGE->cm->context)) {
        $currenttab = '';
        if ($currenttab == 'list') {
            $defaultemplate = 'listtemplate';
        } else if ($currenttab == 'add') {
            $defaultemplate = 'addtemplate';
        } else if ($currenttab == 'asearch') {
            $defaultemplate = 'asearchtemplate';
        } else {
            $defaultemplate = 'singletemplate';
        }

        $templates = $datanode->add(get_string('templates', 'data'));

        $templatelist = array ('listtemplate', 'singletemplate', 'asearchtemplate', 'addtemplate', 'rsstemplate', 'csstemplate', 'jstemplate');
        foreach ($templatelist as $template) {
            $templates->add(get_string($template, 'data'), new moodle_url('/mod/data/templates.php', array('d'=>$data->id,'mode'=>$template)));
        }

        $datanode->add(get_string('fields', 'data'), new moodle_url('/mod/data/field.php', array('d'=>$data->id)));
        $datanode->add(get_string('presets', 'data'), new moodle_url('/mod/data/preset.php', array('d'=>$data->id)));
    }

    if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
        require_once("$CFG->libdir/rsslib.php");

        $string = get_string('rsstype','forum');

        $url = new moodle_url(rss_get_url($PAGE->cm->context->id, $USER->id, 'mod_data', $data->id));
        $datanode->add($string, $url, settings_navigation::TYPE_SETTING, null, null, new pix_icon('i/rss', ''));
    }
}

/**
 * Save the database configuration as a preset.
 *
 * @param stdClass $course The course the database module belongs to.
 * @param stdClass $cm The course module record
 * @param stdClass $data The database record
 * @param string $path
 * @return bool
 */
function data_presets_save($course, $cm, $data, $path) {
    global $USER;
    $fs = get_file_storage();
    $filerecord = new stdClass;
    $filerecord->contextid = DATA_PRESET_CONTEXT;
    $filerecord->component = DATA_PRESET_COMPONENT;
    $filerecord->filearea = DATA_PRESET_FILEAREA;
    $filerecord->itemid = 0;
    $filerecord->filepath = '/'.$path.'/';
    $filerecord->userid = $USER->id;

    $filerecord->filename = 'preset.xml';
    $fs->create_file_from_string($filerecord, data_presets_generate_xml($course, $cm, $data));

    $filerecord->filename = 'singletemplate.html';
    $fs->create_file_from_string($filerecord, $data->singletemplate);

    $filerecord->filename = 'listtemplateheader.html';
    $fs->create_file_from_string($filerecord, $data->listtemplateheader);

    $filerecord->filename = 'listtemplate.html';
    $fs->create_file_from_string($filerecord, $data->listtemplate);

    $filerecord->filename = 'listtemplatefooter.html';
    $fs->create_file_from_string($filerecord, $data->listtemplatefooter);

    $filerecord->filename = 'addtemplate.html';
    $fs->create_file_from_string($filerecord, $data->addtemplate);

    $filerecord->filename = 'rsstemplate.html';
    $fs->create_file_from_string($filerecord, $data->rsstemplate);

    $filerecord->filename = 'rsstitletemplate.html';
    $fs->create_file_from_string($filerecord, $data->rsstitletemplate);

    $filerecord->filename = 'csstemplate.css';
    $fs->create_file_from_string($filerecord, $data->csstemplate);

    $filerecord->filename = 'jstemplate.js';
    $fs->create_file_from_string($filerecord, $data->jstemplate);

    $filerecord->filename = 'asearchtemplate.html';
    $fs->create_file_from_string($filerecord, $data->asearchtemplate);

    return true;
}

/**
 * Generates the XML for the database module provided
 *
 * @global moodle_database $DB
 * @param stdClass $course The course the database module belongs to.
 * @param stdClass $cm The course module record
 * @param stdClass $data The database record
 * @return string The XML for the preset
 */
function data_presets_generate_xml($course, $cm, $data) {
    global $DB;

    // Assemble "preset.xml":
    $presetxmldata = "<preset>\n\n";

    // Raw settings are not preprocessed during saving of presets
    $raw_settings = array(
        'intro',
        'comments',
        'requiredentries',
        'requiredentriestoview',
        'maxentries',
        'rssarticles',
        'approval',
        'defaultsortdir'
    );

    $presetxmldata .= "<settings>\n";
    // First, settings that do not require any conversion
    foreach ($raw_settings as $setting) {
        $presetxmldata .= "<$setting>" . htmlspecialchars($data->$setting) . "</$setting>\n";
    }

    // Now specific settings
    if ($data->defaultsort > 0 && $sortfield = data_get_field_from_id($data->defaultsort, $data)) {
        $presetxmldata .= '<defaultsort>' . htmlspecialchars($sortfield->field->name) . "</defaultsort>\n";
    } else {
        $presetxmldata .= "<defaultsort>0</defaultsort>\n";
    }
    $presetxmldata .= "</settings>\n\n";
    // Now for the fields. Grab all that are non-empty
    $fields = $DB->get_records('data_fields', array('dataid'=>$data->id));
    ksort($fields);
    if (!empty($fields)) {
        foreach ($fields as $field) {
            $presetxmldata .= "<field>\n";
            foreach ($field as $key => $value) {
                if ($value != '' && $key != 'id' && $key != 'dataid') {
                    $presetxmldata .= "<$key>" . htmlspecialchars($value) . "</$key>\n";
                }
            }
            $presetxmldata .= "</field>\n\n";
        }
    }
    $presetxmldata .= '</preset>';
    return $presetxmldata;
}

function data_presets_export($course, $cm, $data, $tostorage=false) {
    global $CFG, $DB;

    $presetname = clean_filename($data->name) . '-preset-' . gmdate("Ymd_Hi");
    $exportsubdir = "mod_data/presetexport/$presetname";
    make_temp_directory($exportsubdir);
    $exportdir = "$CFG->tempdir/$exportsubdir";

    // Assemble "preset.xml":
    $presetxmldata = data_presets_generate_xml($course, $cm, $data);

    // After opening a file in write mode, close it asap
    $presetxmlfile = fopen($exportdir . '/preset.xml', 'w');
    fwrite($presetxmlfile, $presetxmldata);
    fclose($presetxmlfile);

    // Now write the template files
    $singletemplate = fopen($exportdir . '/singletemplate.html', 'w');
    fwrite($singletemplate, $data->singletemplate);
    fclose($singletemplate);

    $listtemplateheader = fopen($exportdir . '/listtemplateheader.html', 'w');
    fwrite($listtemplateheader, $data->listtemplateheader);
    fclose($listtemplateheader);

    $listtemplate = fopen($exportdir . '/listtemplate.html', 'w');
    fwrite($listtemplate, $data->listtemplate);
    fclose($listtemplate);

    $listtemplatefooter = fopen($exportdir . '/listtemplatefooter.html', 'w');
    fwrite($listtemplatefooter, $data->listtemplatefooter);
    fclose($listtemplatefooter);

    $addtemplate = fopen($exportdir . '/addtemplate.html', 'w');
    fwrite($addtemplate, $data->addtemplate);
    fclose($addtemplate);

    $rsstemplate = fopen($exportdir . '/rsstemplate.html', 'w');
    fwrite($rsstemplate, $data->rsstemplate);
    fclose($rsstemplate);

    $rsstitletemplate = fopen($exportdir . '/rsstitletemplate.html', 'w');
    fwrite($rsstitletemplate, $data->rsstitletemplate);
    fclose($rsstitletemplate);

    $csstemplate = fopen($exportdir . '/csstemplate.css', 'w');
    fwrite($csstemplate, $data->csstemplate);
    fclose($csstemplate);

    $jstemplate = fopen($exportdir . '/jstemplate.js', 'w');
    fwrite($jstemplate, $data->jstemplate);
    fclose($jstemplate);

    $asearchtemplate = fopen($exportdir . '/asearchtemplate.html', 'w');
    fwrite($asearchtemplate, $data->asearchtemplate);
    fclose($asearchtemplate);

    // Check if all files have been generated
    if (! is_directory_a_preset($exportdir)) {
        print_error('generateerror', 'data');
    }

    $filenames = array(
        'preset.xml',
        'singletemplate.html',
        'listtemplateheader.html',
        'listtemplate.html',
        'listtemplatefooter.html',
        'addtemplate.html',
        'rsstemplate.html',
        'rsstitletemplate.html',
        'csstemplate.css',
        'jstemplate.js',
        'asearchtemplate.html'
    );

    $filelist = array();
    foreach ($filenames as $filename) {
        $filelist[$filename] = $exportdir . '/' . $filename;
    }

    $exportfile = $exportdir.'.zip';
    file_exists($exportfile) && unlink($exportfile);

    $fp = get_file_packer('application/zip');
    $fp->archive_to_pathname($filelist, $exportfile);

    foreach ($filelist as $file) {
        unlink($file);
    }
    rmdir($exportdir);

    // Return the full path to the exported preset file:
    return $exportfile;
}

/**
 * Running addtional permission check on plugin, for example, plugins
 * may have switch to turn on/off comments option, this callback will
 * affect UI display, not like pluginname_comment_validate only throw
 * exceptions.
 * Capability check has been done in comment->check_permissions(), we
 * don't need to do it again here.
 *
 * @package  mod_data
 * @category comment
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return array
 */
function data_comment_permissions($comment_param) {
    global $CFG, $DB;
    if (!$record = $DB->get_record('data_records', array('id'=>$comment_param->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    if (!$data = $DB->get_record('data', array('id'=>$record->dataid))) {
        throw new comment_exception('invalidid', 'data');
    }
    if ($data->comments) {
        return array('post'=>true, 'view'=>true);
    } else {
        return array('post'=>false, 'view'=>false);
    }
}

/**
 * Validate comment parameter before perform other comments actions
 *
 * @package  mod_data
 * @category comment
 *
 * @param stdClass $comment_param {
 *              context  => context the context object
 *              courseid => int course id
 *              cm       => stdClass course module object
 *              commentarea => string comment area
 *              itemid      => int itemid
 * }
 * @return boolean
 */
function data_comment_validate($comment_param) {
    global $DB;
    // validate comment area
    if ($comment_param->commentarea != 'database_entry') {
        throw new comment_exception('invalidcommentarea');
    }
    // validate itemid
    if (!$record = $DB->get_record('data_records', array('id'=>$comment_param->itemid))) {
        throw new comment_exception('invalidcommentitemid');
    }
    if (!$data = $DB->get_record('data', array('id'=>$record->dataid))) {
        throw new comment_exception('invalidid', 'data');
    }
    if (!$course = $DB->get_record('course', array('id'=>$data->course))) {
        throw new comment_exception('coursemisconf');
    }
    if (!$cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        throw new comment_exception('invalidcoursemodule');
    }
    if (!$data->comments) {
        throw new comment_exception('commentsoff', 'data');
    }
    $context = context_module::instance($cm->id);

    //check if approved
    if ($data->approval and !$record->approved and !data_isowner($record) and !has_capability('mod/data:approve', $context)) {
        throw new comment_exception('notapproved', 'data');
    }

    // group access
    if ($record->groupid) {
        $groupmode = groups_get_activity_groupmode($cm, $course);
        if ($groupmode == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
            if (!groups_is_member($record->groupid)) {
                throw new comment_exception('notmemberofgroup');
            }
        }
    }
    // validate context id
    if ($context->id != $comment_param->context->id) {
        throw new comment_exception('invalidcontext');
    }
    // validation for comment deletion
    if (!empty($comment_param->commentid)) {
        if ($comment = $DB->get_record('comments', array('id'=>$comment_param->commentid))) {
            if ($comment->commentarea != 'database_entry') {
                throw new comment_exception('invalidcommentarea');
            }
            if ($comment->contextid != $comment_param->context->id) {
                throw new comment_exception('invalidcontext');
            }
            if ($comment->itemid != $comment_param->itemid) {
                throw new comment_exception('invalidcommentitemid');
            }
        } else {
            throw new comment_exception('invalidcommentid');
        }
    }
    return true;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function data_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-data-*'=>get_string('page-mod-data-x', 'data'));
    return $module_pagetype;
}

/**
 * Get all of the record ids from a database activity.
 *
 * @param int    $dataid      The dataid of the database module.
 * @param object $selectdata  Contains an additional sql statement for the
 *                            where clause for group and approval fields.
 * @param array  $params      Parameters that coincide with the sql statement.
 * @return array $idarray     An array of record ids
 */
function data_get_all_recordids($dataid, $selectdata = '', $params = null) {
    global $DB;
    $initsql = 'SELECT r.id
                  FROM {data_records} r
                 WHERE r.dataid = :dataid';
    if ($selectdata != '') {
        $initsql .= $selectdata;
        $params = array_merge(array('dataid' => $dataid), $params);
    } else {
        $params = array('dataid' => $dataid);
    }
    $initsql .= ' GROUP BY r.id';
    $initrecord = $DB->get_recordset_sql($initsql, $params);
    $idarray = array();
    foreach ($initrecord as $data) {
        $idarray[] = $data->id;
    }
    // Close the record set and free up resources.
    $initrecord->close();
    return $idarray;
}

/**
 * Get the ids of all the records that match that advanced search criteria
 * This goes and loops through each criterion one at a time until it either
 * runs out of records or returns a subset of records.
 *
 * @param array $recordids    An array of record ids.
 * @param array $searcharray  Contains information for the advanced search criteria
 * @param int $dataid         The data id of the database.
 * @return array $recordids   An array of record ids.
 */
function data_get_advance_search_ids($recordids, $searcharray, $dataid) {
    $searchcriteria = array_keys($searcharray);
    // Loop through and reduce the IDs one search criteria at a time.
    foreach ($searchcriteria as $key) {
        $recordids = data_get_recordids($key, $searcharray, $dataid, $recordids);
        // If we don't have anymore IDs then stop.
        if (!$recordids) {
            break;
        }
    }
    return $recordids;
}

/**
 * Gets the record IDs given the search criteria
 *
 * @param string $alias       Record alias.
 * @param array $searcharray  Criteria for the search.
 * @param int $dataid         Data ID for the database
 * @param array $recordids    An array of record IDs.
 * @return array $nestarray   An arry of record IDs
 */
function data_get_recordids($alias, $searcharray, $dataid, $recordids) {
    global $DB;

    $nestsearch = $searcharray[$alias];
    // searching for content outside of mdl_data_content
    if ($alias < 0) {
        $alias = '';
    }
    list($insql, $params) = $DB->get_in_or_equal($recordids, SQL_PARAMS_NAMED);
    $nestselect = 'SELECT c' . $alias . '.recordid
                     FROM {data_content} c' . $alias . ',
                          {data_fields} f,
                          {data_records} r,
                          {user} u ';
    $nestwhere = 'WHERE u.id = r.userid
                    AND f.id = c' . $alias . '.fieldid
                    AND r.id = c' . $alias . '.recordid
                    AND r.dataid = :dataid
                    AND c' . $alias .'.recordid ' . $insql . '
                    AND ';

    $params['dataid'] = $dataid;
    if (count($nestsearch->params) != 0) {
        $params = array_merge($params, $nestsearch->params);
        $nestsql = $nestselect . $nestwhere . $nestsearch->sql;
    } else {
        $thing = $DB->sql_like($nestsearch->field, ':search1', false);
        $nestsql = $nestselect . $nestwhere . $thing . ' GROUP BY c' . $alias . '.recordid';
        $params['search1'] = "%$nestsearch->data%";
    }
    $nestrecords = $DB->get_recordset_sql($nestsql, $params);
    $nestarray = array();
    foreach ($nestrecords as $data) {
        $nestarray[] = $data->recordid;
    }
    // Close the record set and free up resources.
    $nestrecords->close();
    return $nestarray;
}

/**
 * Returns an array with an sql string for advanced searches and the parameters that go with them.
 *
 * @param int $sort            DATA_*
 * @param stdClass $data       Data module object
 * @param array $recordids     An array of record IDs.
 * @param string $selectdata   Information for the where and select part of the sql statement.
 * @param string $sortorder    Additional sort parameters
 * @return array sqlselect     sqlselect['sql'] has the sql string, sqlselect['params'] contains an array of parameters.
 */
function data_get_advanced_search_sql($sort, $data, $recordids, $selectdata, $sortorder) {
    global $DB;

    $namefields = user_picture::fields('u');
    // Remove the id from the string. This already exists in the sql statement.
    $namefields = str_replace('u.id,', '', $namefields);

    if ($sort == 0) {
        $nestselectsql = 'SELECT r.id, r.approved, r.timecreated, r.timemodified, r.userid, ' . $namefields . '
                        FROM {data_content} c,
                             {data_records} r,
                             {user} u ';
        $groupsql = ' GROUP BY r.id, r.approved, r.timecreated, r.timemodified, r.userid, u.firstname, u.lastname, ' . $namefields;
    } else {
        // Sorting through 'Other' criteria
        if ($sort <= 0) {
            switch ($sort) {
                case DATA_LASTNAME:
                    $sortcontentfull = "u.lastname";
                    break;
                case DATA_FIRSTNAME:
                    $sortcontentfull = "u.firstname";
                    break;
                case DATA_APPROVED:
                    $sortcontentfull = "r.approved";
                    break;
                case DATA_TIMEMODIFIED:
                    $sortcontentfull = "r.timemodified";
                    break;
                case DATA_TIMEADDED:
                default:
                    $sortcontentfull = "r.timecreated";
            }
        } else {
            $sortfield = data_get_field_from_id($sort, $data);
            $sortcontent = $DB->sql_compare_text('c.' . $sortfield->get_sort_field());
            $sortcontentfull = $sortfield->get_sort_sql($sortcontent);
        }

        $nestselectsql = 'SELECT r.id, r.approved, r.timecreated, r.timemodified, r.userid, ' . $namefields . ',
                                 ' . $sortcontentfull . '
                              AS sortorder
                            FROM {data_content} c,
                                 {data_records} r,
                                 {user} u ';
        $groupsql = ' GROUP BY r.id, r.approved, r.timecreated, r.timemodified, r.userid, ' . $namefields . ', ' .$sortcontentfull;
    }

    // Default to a standard Where statement if $selectdata is empty.
    if ($selectdata == '') {
        $selectdata = 'WHERE c.recordid = r.id
                         AND r.dataid = :dataid
                         AND r.userid = u.id ';
    }

    // Find the field we are sorting on
    if ($sort > 0 or data_get_field_from_id($sort, $data)) {
        $selectdata .= ' AND c.fieldid = :sort';
    }

    // If there are no record IDs then return an sql statment that will return no rows.
    if (count($recordids) != 0) {
        list($insql, $inparam) = $DB->get_in_or_equal($recordids, SQL_PARAMS_NAMED);
    } else {
        list($insql, $inparam) = $DB->get_in_or_equal(array('-1'), SQL_PARAMS_NAMED);
    }
    $nestfromsql = $selectdata . ' AND c.recordid ' . $insql . $groupsql;
    $sqlselect['sql'] = "$nestselectsql $nestfromsql $sortorder";
    $sqlselect['params'] = $inparam;
    return $sqlselect;
}

/**
 * Checks to see if the user has permission to delete the preset.
 * @param stdClass $context  Context object.
 * @param stdClass $preset  The preset object that we are checking for deletion.
 * @return bool  Returns true if the user can delete, otherwise false.
 */
function data_user_can_delete_preset($context, $preset) {
    global $USER;

    if (has_capability('mod/data:manageuserpresets', $context)) {
        return true;
    } else {
        $candelete = false;
        if ($preset->userid == $USER->id) {
            $candelete = true;
        }
        return $candelete;
    }
}

/**
 * Delete a record entry.
 *
 * @param int $recordid The ID for the record to be deleted.
 * @param object $data The data object for this activity.
 * @param int $courseid ID for the current course (for logging).
 * @param int $cmid The course module ID.
 * @return bool True if the record deleted, false if not.
 */
function data_delete_record($recordid, $data, $courseid, $cmid) {
    global $DB, $CFG;

    if ($deleterecord = $DB->get_record('data_records', array('id' => $recordid))) {
        if ($deleterecord->dataid == $data->id) {
            if ($contents = $DB->get_records('data_content', array('recordid' => $deleterecord->id))) {
                foreach ($contents as $content) {
                    if ($field = data_get_field_from_id($content->fieldid, $data)) {
                        $field->delete_content($content->recordid);
                    }
                }
                $DB->delete_records('data_content', array('recordid'=>$deleterecord->id));
                $DB->delete_records('data_records', array('id'=>$deleterecord->id));

                // Delete cached RSS feeds.
                if (!empty($CFG->enablerssfeeds)) {
                    require_once($CFG->dirroot.'/mod/data/rsslib.php');
                    data_rss_delete_file($data);
                }

                // Trigger an event for deleting this record.
                $event = \mod_data\event\record_deleted::create(array(
                    'objectid' => $deleterecord->id,
                    'context' => context_module::instance($cmid),
                    'courseid' => $courseid,
                    'other' => array(
                        'dataid' => $deleterecord->dataid
                    )
                ));
                $event->add_record_snapshot('data_records', $deleterecord);
                $event->trigger();

                return true;
            }
        }
    }
    return false;
}

/**
 * Check for required fields, and build a list of fields to be updated in a
 * submission.
 *
 * @param $mod stdClass The current recordid - provided as an optimisation.
 * @param $fields array The field data
 * @param $datarecord stdClass The submitted data.
 * @return stdClass containing:
 * * string[] generalnotifications Notifications for the form as a whole.
 * * string[] fieldnotifications Notifications for a specific field.
 * * bool validated Whether the field was validated successfully.
 * * data_field_base[] fields The field objects to be update.
 */
function data_process_submission(stdClass $mod, $fields, stdClass $datarecord) {
    $result = new stdClass();

    // Empty form checking - you can't submit an empty form.
    $emptyform = true;
    $requiredfieldsfilled = true;
    $fieldsvalidated = true;

    // Store the notifications.
    $result->generalnotifications = array();
    $result->fieldnotifications = array();

    // Store the instantiated classes as an optimisation when processing the result.
    // This prevents the fields being re-initialised when updating.
    $result->fields = array();

    $submitteddata = array();
    foreach ($datarecord as $fieldname => $fieldvalue) {
        if (strpos($fieldname, '_')) {
            $namearray = explode('_', $fieldname, 3);
            $fieldid = $namearray[1];
            if (!isset($submitteddata[$fieldid])) {
                $submitteddata[$fieldid] = array();
            }
            if (count($namearray) === 2) {
                $subfieldid = 0;
            } else {
                $subfieldid = $namearray[2];
            }

            $fielddata = new stdClass();
            $fielddata->fieldname = $fieldname;
            $fielddata->value = $fieldvalue;
            $submitteddata[$fieldid][$subfieldid] = $fielddata;
        }
    }

    // Check all form fields which have the required are filled.
    foreach ($fields as $fieldrecord) {
        // Check whether the field has any data.
        $fieldhascontent = false;

        $field = data_get_field($fieldrecord, $mod);
        if (isset($submitteddata[$fieldrecord->id])) {
            // Field validation check.
            if (method_exists($field, 'field_validation')) {
                $errormessage = $field->field_validation($submitteddata[$fieldrecord->id]);
                if ($errormessage) {
                    $result->fieldnotifications[$field->field->name][] = $errormessage;
                    $fieldsvalidated = false;
                }
            }
            foreach ($submitteddata[$fieldrecord->id] as $fieldname => $value) {
                if ($field->notemptyfield($value->value, $value->fieldname)) {
                    // The field has content and the form is not empty.
                    $fieldhascontent = true;
                    $emptyform = false;
                }
            }
        }

        // If the field is required, add a notification to that effect.
        if ($field->field->required && !$fieldhascontent) {
            if (!isset($result->fieldnotifications[$field->field->name])) {
                $result->fieldnotifications[$field->field->name] = array();
            }
            $result->fieldnotifications[$field->field->name][] = get_string('errormustsupplyvalue', 'data');
            $requiredfieldsfilled = false;
        }

        // Update the field.
        if (isset($submitteddata[$fieldrecord->id])) {
            foreach ($submitteddata[$fieldrecord->id] as $value) {
                $result->fields[$value->fieldname] = $field;
            }
        }
    }

    if ($emptyform) {
        // The form is empty.
        $result->generalnotifications[] = get_string('emptyaddform', 'data');
    }

    $result->validated = $requiredfieldsfilled && !$emptyform && $fieldsvalidated;

    return $result;
}
