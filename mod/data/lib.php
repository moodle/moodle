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

defined('MOODLE_INTERNAL') || die();

// Some constants
define ('DATA_MAX_ENTRIES', 50);
define ('DATA_PERPAGE_SINGLE', 1);

define ('DATA_FIRSTNAME', -1);
define ('DATA_LASTNAME', -2);
define ('DATA_APPROVED', -3);
define ('DATA_TIMEADDED', 0);
define ('DATA_TIMEMODIFIED', -4);
define ('DATA_TAGS', -5);

define ('DATA_CAP_EXPORT', 'mod/data:viewalluserpresets');
// Users having assigned the default role "Non-editing teacher" can export database records
// Using the mod/data capability "viewalluserpresets" existing in Moodle 1.9.x.
// In Moodle >= 2, new roles may be introduced and used instead.

define('DATA_PRESET_COMPONENT', 'mod_data');
define('DATA_PRESET_FILEAREA', 'site_presets');
define('DATA_PRESET_CONTEXT', SYSCONTEXTID);

define('DATA_EVENT_TYPE_OPEN', 'open');
define('DATA_EVENT_TYPE_CLOSE', 'close');

require_once(__DIR__ . '/deprecatedlib.php');

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
    /** @var priority for globalsearch indexing */
    protected static $priority = self::NO_PRIORITY;
    /** priority value for invalid fields regarding indexing */
    const NO_PRIORITY = 0;
    /** priority value for minimum priority */
    const MIN_PRIORITY = 1;
    /** priority value for low priority */
    const LOW_PRIORITY = 2;
    /** priority value for high priority */
    const HIGH_PRIORITY = 3;
    /** priority value for maximum priority */
    const MAX_PRIORITY = 4;

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
            $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
            $str .= html_writer::div($image, 'inline-req');
        }
        $str .= '</label><input class="basefieldinput form-control d-inline mod-data-input" ' .
                'type="text" name="field_' . $this->field->id . '" ' .
                'id="field_' . $this->field->id . '" value="' . s($content) . '" />';
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
        // Throw an exception if field type doen't exist. Anyway user should never access to edit a field with an unknown fieldtype.
        if ($this->type === 'unknown') {
            throw new \moodle_exception(get_string('missingfieldtype', 'data', (object)['name' => $this->field->name]));
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

        $filepath = $CFG->dirroot.'/mod/data/field/'.$this->type.'/mod.html';

        if (!file_exists($filepath)) {
            throw new \moodle_exception(get_string('missingfieldtype', 'data', (object)['name' => $this->field->name]));
        } else {
            require_once($filepath);
        }

        echo html_writer::start_div('mt-3');
        echo html_writer::tag('input', null, array('type' => 'submit', 'value' => $savebutton,
            'class' => 'btn btn-primary'));
        echo html_writer::tag('input', null, array('type' => 'submit', 'name' => 'cancel',
            'value' => get_string('cancel'), 'class' => 'btn btn-secondary ml-2'));
        echo html_writer::end_div();

        echo '</form>';

        echo $OUTPUT->box_end();
    }

    /**
     * Validates params of fieldinput data. Overwrite to validate fieldtype specific data.
     *
     * You are expected to return an array like ['paramname' => 'Error message for paramname param'] if there is an error,
     * return an empty array if everything is fine.
     *
     * @param stdClass $fieldinput The field input data to check
     * @return array $errors if empty validation was fine, otherwise contains one or more error messages
     */
    public function validate(stdClass $fieldinput): array {
        return [];
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
        return get_string('fieldtypelabel', "datafield_$this->type");
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
        $str .= $OUTPUT->pix_icon('field/' . $this->type, $this->type, 'data');
        $str .= '</a>';
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

    /**
     * Returns the priority for being indexed by globalsearch
     *
     * @return int
     */
    public static function get_priority() {
        return static::$priority;
    }

    /**
     * Returns the presentable string value for a field content.
     *
     * The returned string should be plain text.
     *
     * @param stdClass $content
     * @return string
     */
    public static function get_content_value($content) {
        return trim($content->content, "\r\n ");
    }

    /**
     * Return the plugin configs for external functions,
     * in some cases the configs will need formatting or be returned only if the current user has some capabilities enabled.
     *
     * @return array the list of config parameters
     * @since Moodle 3.3
     */
    public function get_config_for_external() {
        // Return all the field configs to null (maybe there is a private key for a service or something similar there).
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = null;
        }
        return $configs;
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
        $table->attributes['class'] = 'mod-data-default-template ##approvalstatusclass##';
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

        if (core_tag_tag::is_enabled('mod_data', 'data_records')) {
            $label = new html_table_cell(get_string('tags') . ':');
            if ($form) {
                $cell = data_generate_tag_form();
            } else {
                $cell = new html_table_cell('##tags##');
            }
            $table->data[] = new html_table_row(array($label, $cell));
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
 * Build the form elements to manage tags for a record.
 *
 * @param int|bool $recordid
 * @param string[] $selected raw tag names
 * @return string
 */
function data_generate_tag_form($recordid = false, $selected = []) {
    global $CFG, $DB, $OUTPUT, $PAGE;

    $tagtypestoshow = \core_tag_area::get_showstandard('mod_data', 'data_records');
    $showstandard = ($tagtypestoshow != core_tag_tag::HIDE_STANDARD);
    $typenewtags = ($tagtypestoshow != core_tag_tag::STANDARD_ONLY);

    $str = html_writer::start_tag('div', array('class' => 'datatagcontrol'));

    $namefield = empty($CFG->keeptagnamecase) ? 'name' : 'rawname';

    $tagcollid = \core_tag_area::get_collection('mod_data', 'data_records');
    $tags = [];
    $selectedtags = [];

    if ($showstandard) {
        $tags += $DB->get_records_menu('tag', array('isstandard' => 1, 'tagcollid' => $tagcollid),
            $namefield, 'id,' . $namefield . ' as fieldname');
    }

    if ($recordid) {
        $selectedtags += core_tag_tag::get_item_tags_array('mod_data', 'data_records', $recordid);
    }

    if (!empty($selected)) {
        list($sql, $params) = $DB->get_in_or_equal($selected, SQL_PARAMS_NAMED);
        $params['tagcollid'] = $tagcollid;
        $sql = "SELECT id, $namefield FROM {tag} WHERE tagcollid = :tagcollid AND rawname $sql";
        $selectedtags += $DB->get_records_sql_menu($sql, $params);
    }

    $tags += $selectedtags;

    $str .= '<select class="custom-select" name="tags[]" id="tags" multiple>';
    foreach ($tags as $tagid => $tag) {
        $selected = key_exists($tagid, $selectedtags) ? 'selected' : '';
        $str .= "<option value='$tag' $selected>$tag</option>";
    }
    $str .= '</select>';

    if (has_capability('moodle/tag:manage', context_system::instance()) && $showstandard) {
        $url = new moodle_url('/tag/manage.php', array('tc' => core_tag_area::get_collection('mod_data',
            'data_records')));
        $str .= ' ' . $OUTPUT->action_link($url, get_string('managestandardtags', 'tag'));
    }

    $PAGE->requires->js_call_amd('core/form-autocomplete', 'enhance', $params = array(
            '#tags',
            $typenewtags,
            '',
            get_string('entertags', 'tag'),
            false,
            $showstandard,
            get_string('noselection', 'form')
        )
    );

    $str .= html_writer::end_tag('div');

    return $str;
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

    $filepath = $CFG->dirroot.'/mod/data/field/'.$type.'/field.class.php';
    // It should never access this method if the subfield class doesn't exist.
    if (!file_exists($filepath)) {
        throw new \moodle_exception('invalidfieldtype', 'data');
    }
    require_once($filepath);
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
 * @param stdClass $field the field record
 * @param stdClass $data the data instance
 * @param stdClass|null $cm optional course module data
 * @return data_field_base the field object instance or data_field_base if unkown type
 */
function data_get_field($field, $data, $cm=null) {
    global $CFG;
    if (!isset($field->type)) {
        return new data_field_base($field);
    }
    $filepath = $CFG->dirroot.'/mod/data/field/'.$field->type.'/field.class.php';
    if (!file_exists($filepath)) {
        return new data_field_base($field);
    }
    require_once($filepath);
    $newfield = 'data_field_'.$field->type;
    $newfield = new $newfield($field, $data, $cm);
    return $newfield;
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
function data_numentries($data, $userid=null) {
    global $USER, $DB;
    if ($userid === null) {
        $userid = $USER->id;
    }
    $sql = 'SELECT COUNT(*) FROM {data_records} WHERE dataid=? AND userid=?';
    return $DB->count_records_sql($sql, array($data->id, $userid));
}

/**
 * function that takes in a dataid and adds a record
 * this is used everytime an add template is submitted
 *
 * @global object
 * @global object
 * @param object $data
 * @param int $groupid
 * @param int $userid
 * @return bool
 */
function data_add_record($data, $groupid = 0, $userid = null) {
    global $USER, $DB;

    $cm = get_coursemodule_from_instance('data', $data->id);
    $context = context_module::instance($cm->id);

    $record = new stdClass();
    $record->userid = $userid ?? $USER->id;
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

    $course = get_course($cm->course);
    data_update_completion_state($data, $course, $cm);

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
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/data/locallib.php');

    if (empty($data->assessed)) {
        $data->assessed = 0;
    }

    if (empty($data->ratingtime) || empty($data->assessed)) {
        $data->assesstimestart  = 0;
        $data->assesstimefinish = 0;
    }

    $data->timemodified = time();

    $data->id = $DB->insert_record('data', $data);

    // Add calendar events if necessary.
    data_set_events($data);
    if (!empty($data->completionexpected)) {
        \core_completion\api::update_completion_date_event($data->coursemodule, 'data', $data->id, $data->completionexpected);
    }

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
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/data/locallib.php');

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

    // Add calendar events if necessary.
    data_set_events($data);
    $completionexpected = (!empty($data->completionexpected)) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($data->coursemodule, 'data', $data->id, $completionexpected);

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

    // Delete all information related to fields.
    $fields = $DB->get_records('data_fields', ['dataid' => $id]);
    foreach ($fields as $field) {
        $todelete = data_get_field($field, $data, $cm);
        $todelete->delete_field();
    }

    // Remove old calendar events.
    $events = $DB->get_records('event', array('modulename' => 'data', 'instance' => $id));
    foreach ($events as $event) {
        $event = calendar_event::load($event);
        $event->delete();
    }

    // cleanup gradebook
    data_grade_item_delete($data);

    // Delete the instance itself
    // We must delete the module record after we delete the grade item.
    $result = $DB->delete_records('data', array('id'=>$id));

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
            if (!$grade->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
                $result->info .= ', ' . get_string('gradenoun') . ': ' . $grade->str_long_grade;
            } else {
                $result->info = get_string('gradenoun') . ': ' . get_string('hidden', 'grades');
            }
        }
        return $result;
    } else if ($grade) {
        $result = (object) [
            'time' => grade_get_date_for_user_grade($grade, $user),
        ];
        if (!$grade->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
            $result->info = get_string('gradenoun') . ': ' . $grade->str_long_grade;
        } else {
            $result->info = get_string('gradenoun') . ': ' . get_string('hidden', 'grades');
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
        if (!$grade->hidden || has_capability('moodle/grade:viewhidden', context_course::instance($course->id))) {
            echo $OUTPUT->container(get_string('gradenoun') . ': ' . $grade->str_long_grade);
            if ($grade->str_feedback) {
                echo $OUTPUT->container(get_string('feedback').': '.$grade->str_feedback);
            }
        } else {
            echo $OUTPUT->container(get_string('gradenoun') . ': ' . get_string('hidden', 'grades'));
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

    static $fields = array();
    static $dataid = null;

    if (empty($dataid)) {
        $dataid = $data->id;
    } else if ($dataid != $data->id) {
        $fields = array();
    }

    if (empty($fields)) {
        $fieldrecords = $DB->get_records('data_fields', array('dataid'=>$data->id));
        foreach ($fieldrecords as $fieldrecord) {
            $fields[]= data_get_field($fieldrecord, $data);
        }
    }

    if (empty($records)) {
        return;
    }

    if (!$jumpurl) {
        $jumpurl = new moodle_url('/mod/data/view.php', array('d' => $data->id));
    }
    $jumpurl = new moodle_url($jumpurl, array('page' => $page, 'sesskey' => sesskey()));

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
        if (data_user_can_manage_entry($record, $data, $context)) {
            $backtourlparams = [
                'd' => $data->id,
            ];
            if ($template === 'singletemplate') {
                $backtourlparams['mode'] = 'single';
            }
            $backtourl = new \moodle_url('/mod/data/view.php', $backtourlparams);
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/edit.php?d='
                             .$data->id.'&amp;rid='.$record->id.'&amp;sesskey='.sesskey().'&amp;backto='
                             . urlencode($backtourl->out(false)) .'">' .
                             $OUTPUT->pix_icon('t/edit', get_string('edit')) . '</a>';
            $replacement[] = '<a href="'.$CFG->wwwroot.'/mod/data/view.php?d='
                             .$data->id.'&amp;delete='.$record->id.'&amp;sesskey='.sesskey().'">' .
                             $OUTPUT->pix_icon('t/delete', get_string('delete')) . '</a>';
        } else {
            $replacement[] = '';
            $replacement[] = '';
        }

        $moreurl = $CFG->wwwroot . '/mod/data/view.php?d=' . $data->id . '&amp;rid=' . $record->id;
        if ($search) {
            $moreurl .= '&amp;filter=1';
        }
        $patterns[]='##more##';
        $replacement[] = '<a href="'.$moreurl.'">' . $OUTPUT->pix_icon('t/preview', get_string('more', 'data')) . '</a>';

        $patterns[]='##moreurl##';
        $replacement[] = $moreurl;

        $patterns[]='##delcheck##';
        if ($canmanageentries) {
            $checkbox = new \core\output\checkbox_toggleall('listview-entries', false, [
                'id' => "entry_{$record->id}",
                'name' => 'delcheck[]',
                'classes' => 'recordcheckbox',
                'value' => $record->id,
            ]);
            $replacement[] = $OUTPUT->render($checkbox);
        } else {
            $replacement[] = '';
        }

        $patterns[]='##user##';
        $replacement[] = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$record->userid.
                               '&amp;course='.$data->course.'">'.fullname($record).'</a>';

        $patterns[] = '##userpicture##';
        $ruser = user_picture::unalias($record, null, 'userid');
        // If the record didn't come with user data, retrieve the user from database.
        if (!isset($ruser->picture)) {
            $ruser = core_user::get_user($record->userid);
        }
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

        $patterns[] = '##approvalstatus##';
        $patterns[] = '##approvalstatusclass##';
        if (!$data->approval) {
            $replacement[] = '';
            $replacement[] = '';
        } else if ($record->approved) {
            $replacement[] = get_string('approved', 'data');
            $replacement[] = 'approved';
        } else {
            $replacement[] = get_string('notapproved', 'data');
            $replacement[] = 'notapproved';
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

        if (core_tag_tag::is_enabled('mod_data', 'data_records')) {
            $patterns[] = "##tags##";
            $replacement[] = $OUTPUT->tag_list(
                core_tag_tag::get_item_tags('mod_data', 'data_records', $record->id), '', 'data-tags');
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
    echo '<div class="datapreferences mb-3">';
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
    echo html_writer::select($pagesizes, 'perpage', $perpage, false, array('id' => 'pref_perpage', 'class' => 'custom-select'));

    if ($advanced) {
        $regsearchclass = 'search_none';
        $advancedsearchclass = 'search_inline';
    } else {
        $regsearchclass = 'search_inline';
        $advancedsearchclass = 'search_none';
    }
    echo '<div id="reg_search" class="' . $regsearchclass . ' form-inline" >&nbsp;&nbsp;&nbsp;';
    echo '<label for="pref_search">' . get_string('search') . '</label> <input type="text" ' .
         'class="form-control" size="16" name="search" id= "pref_search" value="' . s($search) . '" /></div>';
    echo '&nbsp;&nbsp;&nbsp;<label for="pref_sortby">'.get_string('sortby').'</label> ';
    // foreach field, print the option
    echo '<select name="sort" id="pref_sortby" class="custom-select mr-1">';
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
    echo '<select id="pref_order" name="order" class="custom-select mr-1">';
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
    echo '&nbsp;<input type="checkbox" id="advancedcheckbox" name="advanced" value="1" ' . $checked . ' ' .
         'onchange="showHideAdvSearch(this.checked);" class="mx-1" />' .
         '<label for="advancedcheckbox">' . get_string('advancedsearch', 'data') . '</label>';
    echo '&nbsp;<input type="submit" class="btn btn-secondary" value="' . get_string('savesettings', 'data') . '" />';

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

    static $fields = array();
    static $dataid = null;

    if (empty($dataid)) {
        $dataid = $data->id;
    } else if ($dataid != $data->id) {
        $fields = array();
    }

    if (empty($fields)) {
        $fieldrecords = $DB->get_records('data_fields', array('dataid'=>$data->id));
        foreach ($fieldrecords as $fieldrecord) {
            $fields[]= data_get_field($fieldrecord, $data);
        }
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
        if ($searchfield->type === 'unknown') {
            continue;
        }
        if (!empty($search_array[$field->field->id]->data)) {
            $replacement[] = $searchfield->display_search_field($search_array[$field->field->id]->data);
        } else {
            $replacement[] = $searchfield->display_search_field();
        }
    }
    $fn = !empty($search_array[DATA_FIRSTNAME]->data) ? $search_array[DATA_FIRSTNAME]->data : '';
    $ln = !empty($search_array[DATA_LASTNAME]->data) ? $search_array[DATA_LASTNAME]->data : '';
    $patterns[]    = '/##firstname##/';
    $replacement[] = '<label class="accesshide" for="u_fn">' . get_string('authorfirstname', 'data') . '</label>' .
                     '<input type="text" class="form-control" size="16" id="u_fn" name="u_fn" value="' . s($fn) . '" />';
    $patterns[]    = '/##lastname##/';
    $replacement[] = '<label class="accesshide" for="u_ln">' . get_string('authorlastname', 'data') . '</label>' .
                     '<input type="text" class="form-control" size="16" id="u_ln" name="u_ln" value="' . s($ln) . '" />';

    if (core_tag_tag::is_enabled('mod_data', 'data_records')) {
        $patterns[] = "/##tags##/";
        $selectedtags = isset($search_array[DATA_TAGS]->rawtagnames) ? $search_array[DATA_TAGS]->rawtagnames : [];
        $replacement[] = data_generate_tag_form(false, $selectedtags);
    }

    // actual replacement of the tags

    $options = new stdClass();
    $options->para=false;
    $options->noclean=true;
    echo '<tr><td>';
    echo preg_replace($patterns, $replacement, format_text($data->asearchtemplate, FORMAT_HTML, $options));
    echo '</td></tr>';

    echo '<tr><td colspan="4"><br/>' .
         '<input type="submit" class="btn btn-primary mr-1" value="' . get_string('savesettings', 'data') . '" />' .
         '<input type="submit" class="btn btn-secondary" name="resetadv" value="' . get_string('resetsettings', 'data') . '" />' .
         '</td></tr>';
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
 * @param string $actionbar
 */
function data_print_header($course, $cm, $data, $currenttab='', string $actionbar = '') {

    global $CFG, $displaynoticegood, $displaynoticebad, $OUTPUT, $PAGE, $USER;

    $PAGE->set_title($data->name);
    echo $OUTPUT->header();

    echo $actionbar;

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
 * Check whether the current user is allowed to manage the given record considering manageentries capability,
 * data_in_readonly_period() result, ownership (determined by data_isowner()) and manageapproved setting.
 * @param mixed $record record object or id
 * @param object $data data object
 * @param object $context context object
 * @return bool returns true if the user is allowd to edit the entry, false otherwise
 */
function data_user_can_manage_entry($record, $data, $context) {
    global $DB;

    if (has_capability('mod/data:manageentries', $context)) {
        return true;
    }

    // Check whether this activity is read-only at present.
    $readonly = data_in_readonly_period($data);

    if (!$readonly) {
        // Get record object from db if just id given like in data_isowner.
        // ...done before calling data_isowner() to avoid querying db twice.
        if (!is_object($record)) {
            if (!$record = $DB->get_record('data_records', array('id' => $record))) {
                return false;
            }
        }
        if (data_isowner($record)) {
            if ($data->approval && $record->approved) {
                return $data->manageapproved == 1;
            } else {
                return true;
            }
        }
    }

    return false;
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
        global $DB, $CFG;
        require_once($CFG->libdir.'/xmlize.php');

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
        global $DB, $CFG, $OUTPUT;

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
            $missingfieldtypes = [];
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
                    $filepath = "field/$newfield->type/field.class.php";
                    if (!file_exists($filepath)) {
                        $missingfieldtypes[] = $newfield->name;
                        continue;
                    }
                    include_once($filepath);

                    if (!isset($newfield->description)) {
                        $newfield->description = '';
                    }
                    $classname = 'data_field_'.$newfield->type;
                    $fieldclass = new $classname($newfield, $this->module);
                    $fieldclass->insert_field();
                    unset($fieldclass);
                }
            }
            if (!empty($missingfieldtypes)) {
                echo $OUTPUT->notification(get_string('missingfieldtypeimport', 'data') . html_writer::alist($missingfieldtypes));
            }
        }

        /* Get rid of all old unused data */
        foreach ($currentfields as $cid => $currentfield) {
            if (!array_key_exists($cid, $preservedfields)) {
                /* Data not used anymore so wipe! */
                echo "Deleting field $currentfield->name<br />";

                // Delete all information related to fields.
                $todelete = data_get_field_from_id($currentfield->id, $this->module);
                $todelete->delete_field();
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

    $mform->addElement('checkbox', 'reset_data_tags', get_string('removealldatatags', 'data'));
    $mform->disabledIf('reset_data_tags', 'reset_data', 'checked');
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

                core_tag_tag::delete_instances('mod_data', null, $datacontext->id);
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

                core_tag_tag::remove_all_item_tags('mod_data', 'data_records', $record->id);

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

    // Remove all the tags.
    if (!empty($data->reset_data_tags)) {
        if ($datas = $DB->get_records_sql($alldatassql, array($data->courseid))) {
            foreach ($datas as $dataid => $unused) {
                if (!$cm = get_coursemodule_from_instance('data', $dataid)) {
                    continue;
                }

                $context = context_module::instance($cm->id);
                core_tag_tag::delete_instances('mod_data', null, $context->id);

            }
        }
        $status[] = array('component' => $componentstr, 'item' => get_string('tagsdeleted', 'data'), 'error' => false);
    }

    // updating dates - shift may be negative too
    if ($data->timeshift) {
        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        shift_course_mod_dates('data', array('timeavailablefrom', 'timeavailableto',
            'timeviewfrom', 'timeviewto', 'assesstimestart', 'assesstimefinish'), $data->timeshift, $data->courseid);
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
    return ['moodle/rating:view', 'moodle/rating:viewany', 'moodle/rating:viewall', 'moodle/rating:rate',
            'moodle/comment:view', 'moodle/comment:post', 'moodle/comment:delete'];
}

/**
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function data_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return true;
        case FEATURE_GRADE_OUTCOMES:          return true;
        case FEATURE_RATE:                    return true;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
        case FEATURE_COMMENT:                 return true;
        case FEATURE_MOD_PURPOSE:             return MOD_PURPOSE_COLLABORATION;

        default: return null;
    }
}

/**
 * Import records for a data instance from csv data.
 *
 * @param object $cm Course module of the data instance.
 * @param object $data The data instance.
 * @param string $csvdata The csv data to be imported.
 * @param string $encoding The encoding of csv data.
 * @param string $fielddelimiter The delimiter of the csv data.
 * @return int Number of records added.
 */
function data_import_csv($cm, $data, &$csvdata, $encoding, $fielddelimiter) {
    global $CFG, $DB;
    // Large files are likely to take their time and memory. Let PHP know
    // that we'll take longer, and that the process should be recycled soon
    // to free up memory.
    core_php_time_limit::raise();
    raise_memory_limit(MEMORY_EXTRA);

    $iid = csv_import_reader::get_new_iid('moddata');
    $cir = new csv_import_reader($iid, 'moddata');

    $context = context_module::instance($cm->id);

    $readcount = $cir->load_csv_content($csvdata, $encoding, $fielddelimiter);
    $csvdata = null; // Free memory.
    if (empty($readcount)) {
        print_error('csvfailed', 'data', "{$CFG->wwwroot}/mod/data/edit.php?d={$data->id}");
    } else {
        if (!$fieldnames = $cir->get_columns()) {
            print_error('cannotreadtmpfile', 'error');
        }

        // Check the fieldnames are valid.
        $rawfields = $DB->get_records('data_fields', array('dataid' => $data->id), '', 'name, id, type');
        $fields = array();
        $errorfield = '';
        $usernamestring = get_string('username');
        $safetoskipfields = array(get_string('user'), get_string('email'),
            get_string('timeadded', 'data'), get_string('timemodified', 'data'),
            get_string('approved', 'data'), get_string('tags', 'data'));
        $userfieldid = null;
        foreach ($fieldnames as $id => $name) {
            if (!isset($rawfields[$name])) {
                if ($name == $usernamestring) {
                    $userfieldid = $id;
                } else if (!in_array($name, $safetoskipfields)) {
                    $errorfield .= "'$name' ";
                }
            } else {
                // If this is the second time, a field with this name comes up, it must be a field not provided by the user...
                // like the username.
                if (isset($fields[$name])) {
                    if ($name == $usernamestring) {
                        $userfieldid = $id;
                    }
                    unset($fieldnames[$id]); // To ensure the user provided content fields remain in the array once flipped.
                } else {
                    $field = $rawfields[$name];
                    $filepath = "$CFG->dirroot/mod/data/field/$field->type/field.class.php";
                    if (!file_exists($filepath)) {
                        $errorfield .= "'$name' ";
                        continue;
                    }
                    require_once($filepath);
                    $classname = 'data_field_' . $field->type;
                    $fields[$name] = new $classname($field, $data, $cm);
                }
            }
        }

        if (!empty($errorfield)) {
            print_error('fieldnotmatched', 'data',
                "{$CFG->wwwroot}/mod/data/edit.php?d={$data->id}", $errorfield);
        }

        $fieldnames = array_flip($fieldnames);

        $cir->init();
        $recordsadded = 0;
        while ($record = $cir->next()) {
            $authorid = null;
            if ($userfieldid) {
                if (!($author = core_user::get_user_by_username($record[$userfieldid], 'id'))) {
                    $authorid = null;
                } else {
                    $authorid = $author->id;
                }
            }
            if ($recordid = data_add_record($data, 0, $authorid)) {  // Add instance to data_record.
                foreach ($fields as $field) {
                    $fieldid = $fieldnames[$field->field->name];
                    if (isset($record[$fieldid])) {
                        $value = $record[$fieldid];
                    } else {
                        $value = '';
                    }

                    if (method_exists($field, 'update_content_import')) {
                        $field->update_content_import($recordid, $value, 'field_' . $field->field->id);
                    } else {
                        $content = new stdClass();
                        $content->fieldid = $field->field->id;
                        $content->content = $value;
                        $content->recordid = $recordid;
                        $DB->insert_record('data_content', $content);
                    }
                }

                if (core_tag_tag::is_enabled('mod_data', 'data_records') &&
                    isset($fieldnames[get_string('tags', 'data')])) {
                    $columnindex = $fieldnames[get_string('tags', 'data')];
                    $rawtags = $record[$columnindex];
                    $tags = explode(',', $rawtags);
                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if (empty($tag)) {
                            continue;
                        }
                        core_tag_tag::add_item_tag('mod_data', 'data_records', $recordid, $context, $tag);
                    }
                }

                $recordsadded++;
                print get_string('added', 'moodle', $recordsadded) . ". " . get_string('entry', 'data') . " (ID $recordid)<br />\n";
            }
        }
        $cir->close();
        $cir->cleanup(true);
        return $recordsadded;
    }
    return 0;
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
 * @param bool $tags whether to include tags
 * @return array
 */
function data_get_exportdata($dataid, $fields, $selectedfields, $currentgroup=0, $context=null,
                             $userdetails=false, $time=false, $approval=false, $tags = false) {
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
    if ($tags) {
        $exportdata[0][] = get_string('tags', 'data');
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
            if ($tags) {
                $itemtags = \core_tag_tag::get_item_tags_array('mod_data', 'data_records', $record->id);
                $exportdata[$line][] = implode(', ', $itemtags);
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
    require_once($CFG->dirroot . '/mod/data/locallib.php');

    $rid = optional_param('rid', 0, PARAM_INT);

    $data = $DB->get_record('data', array('id'=>$cm->instance));
    $currentgroup = groups_get_activity_group($cm);
    $groupmode = groups_get_activity_groupmode($cm);

     $numentries = data_numentries($data);
    $canmanageentries = has_capability('mod/data:manageentries', context_module::instance($cm->id));

    if ($data->entriesleft = data_get_entries_left_to_add($data, $numentries, $canmanageentries)) {
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
    global $DB, $CFG, $USER;

    $data = $DB->get_record('data', array("id" => $settings->get_page()->cm->instance));

    $currentgroup = groups_get_activity_group($settings->get_page()->cm);
    $groupmode = groups_get_activity_groupmode($settings->get_page()->cm);

    // Took out participation list here!
    if (data_user_can_add_entry($data, $currentgroup, $groupmode, $settings->get_page()->cm->context)) {
        if (empty($editentry)) { //TODO: undefined
            $addstring = get_string('add', 'data');
        } else {
            $addstring = get_string('editentry', 'data');
        }
        $addentrynode = $datanode->add($addstring,
            new moodle_url('/mod/data/edit.php', array('d' => $settings->get_page()->cm->instance)));
        $addentrynode->set_show_in_secondary_navigation(false);
    }

    if (has_capability(DATA_CAP_EXPORT, $settings->get_page()->cm->context)) {
        // The capability required to Export database records is centrally defined in 'lib.php'
        // and should be weaker than those required to edit Templates, Fields and Presets.
        $exportentriesnode = $datanode->add(get_string('exportentries', 'data'),
            new moodle_url('/mod/data/export.php', array('d' => $data->id)));
        $exportentriesnode->set_show_in_secondary_navigation(false);
    }
    if (has_capability('mod/data:manageentries', $settings->get_page()->cm->context)) {
        $importentriesnode = $datanode->add(get_string('importentries', 'data'),
            new moodle_url('/mod/data/import.php', array('d' => $data->id)));
        $importentriesnode->set_show_in_secondary_navigation(false);
    }

    if (has_capability('mod/data:managetemplates', $settings->get_page()->cm->context)) {
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

        $datanode->add(get_string('fields', 'data'),
            new moodle_url('/mod/data/field.php', array('d' => $data->id)));
        $datanode->add(get_string('templates', 'data'),
            new moodle_url('/mod/data/templates.php', array('d' => $data->id)));
        $datanode->add(get_string('presets', 'data'), new moodle_url('/mod/data/preset.php', array('d' => $data->id)));
    }

    if (!empty($CFG->enablerssfeeds) && !empty($CFG->data_enablerssfeeds) && $data->rssarticles > 0) {
        require_once("$CFG->libdir/rsslib.php");

        $string = get_string('rsstype', 'data');

        $url = new moodle_url(rss_get_url($settings->get_page()->cm->context->id, $USER->id, 'mod_data', $data->id));
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
        'manageapproved',
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
    // Check to see if we have any record IDs.
    if (empty($recordids)) {
        // Send back an empty search.
        return array();
    }
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
    $searchcriteria = $alias;   // Keep the criteria.
    $nestsearch = $searcharray[$alias];
    // searching for content outside of mdl_data_content
    if ($alias < 0) {
        $alias = '';
    }
    list($insql, $params) = $DB->get_in_or_equal($recordids, SQL_PARAMS_NAMED);
    $nestselect = 'SELECT c' . $alias . '.recordid
                     FROM {data_content} c' . $alias . '
               INNER JOIN {data_fields} f
                       ON f.id = c' . $alias . '.fieldid
               INNER JOIN {data_records} r
                       ON r.id = c' . $alias . '.recordid
               INNER JOIN {user} u
                       ON u.id = r.userid ';
    $nestwhere = 'WHERE r.dataid = :dataid
                    AND c' . $alias .'.recordid ' . $insql . '
                    AND ';

    $params['dataid'] = $dataid;
    if (count($nestsearch->params) != 0) {
        $params = array_merge($params, $nestsearch->params);
        $nestsql = $nestselect . $nestwhere . $nestsearch->sql;
    } else if ($searchcriteria == DATA_TIMEMODIFIED) {
        $nestsql = $nestselect . $nestwhere . $nestsearch->field . ' >= :timemodified GROUP BY c' . $alias . '.recordid';
        $params['timemodified'] = $nestsearch->data;
    } else if ($searchcriteria == DATA_TAGS) {
        if (empty($nestsearch->rawtagnames)) {
            return [];
        }
        $i = 0;
        $tagwhere = [];
        $tagselect = '';
        foreach ($nestsearch->rawtagnames as $tagrawname) {
            $tagselect .= " INNER JOIN {tag_instance} ti_$i
                                    ON ti_$i.component = 'mod_data'
                                   AND ti_$i.itemtype = 'data_records'
                                   AND ti_$i.itemid = r.id
                            INNER JOIN {tag} t_$i
                                    ON ti_$i.tagid = t_$i.id ";
            $tagwhere[] = " t_$i.rawname = :trawname_$i ";
            $params["trawname_$i"] = $tagrawname;
            $i++;
        }
        $nestsql = $nestselect . $tagselect . $nestwhere . implode(' AND ', $tagwhere);
    } else {    // First name or last name.
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

    $userfieldsapi = \core_user\fields::for_userpic()->excluding('id');
    $namefields = $userfieldsapi->get_sql('u', false, '', '', false)->selects;

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
        $selectdata .= ' AND c.fieldid = :sort AND s.recordid = r.id';
        $nestselectsql .= ',{data_content} s ';
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

                core_tag_tag::remove_all_item_tags('mod_data', 'data_records', $recordid);

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
                $course = get_course($courseid);
                $cm = get_coursemodule_from_instance('data', $data->id, 0, false, MUST_EXIST);
                data_update_completion_state($data, $course, $cm);

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

/**
 * This standard function will check all instances of this module
 * and make sure there are up-to-date events created for each of them.
 * If courseid = 0, then every data event in the site is checked, else
 * only data events belonging to the course specified are checked.
 * This function is used, in its new format, by restore_refresh_events()
 *
 * @param int $courseid
 * @param int|stdClass $instance Data module instance or ID.
 * @param int|stdClass $cm Course module object or ID (not used in this module).
 * @return bool
 */
function data_refresh_events($courseid = 0, $instance = null, $cm = null) {
    global $DB, $CFG;
    require_once($CFG->dirroot.'/mod/data/locallib.php');

    // If we have instance information then we can just update the one event instead of updating all events.
    if (isset($instance)) {
        if (!is_object($instance)) {
            $instance = $DB->get_record('data', array('id' => $instance), '*', MUST_EXIST);
        }
        data_set_events($instance);
        return true;
    }

    if ($courseid) {
        if (! $data = $DB->get_records("data", array("course" => $courseid))) {
            return true;
        }
    } else {
        if (! $data = $DB->get_records("data")) {
            return true;
        }
    }

    foreach ($data as $datum) {
        data_set_events($datum);
    }
    return true;
}

/**
 * Fetch the configuration for this database activity.
 *
 * @param   stdClass    $database   The object returned from the database for this instance
 * @param   string      $key        The name of the key to retrieve. If none is supplied, then all configuration is returned
 * @param   mixed       $default    The default value to use if no value was found for the specified key
 * @return  mixed                   The returned value
 */
function data_get_config($database, $key = null, $default = null) {
    if (!empty($database->config)) {
        $config = json_decode($database->config);
    } else {
        $config = new stdClass();
    }

    if ($key === null) {
        return $config;
    }

    if (property_exists($config, $key)) {
        return $config->$key;
    }
    return $default;
}

/**
 * Update the configuration for this database activity.
 *
 * @param   stdClass    $database   The object returned from the database for this instance
 * @param   string      $key        The name of the key to set
 * @param   mixed       $value      The value to set for the key
 */
function data_set_config(&$database, $key, $value) {
    // Note: We must pass $database by reference because there may be subsequent calls to update_record and these should
    // not overwrite the configuration just set.
    global $DB;

    $config = data_get_config($database);

    if (!isset($config->$key) || $config->$key !== $value) {
        $config->$key = $value;
        $database->config = json_encode($config);
        $DB->set_field('data', 'config', $database->config, ['id' => $database->id]);
    }
}
/**
 * Sets the automatic completion state for this database item based on the
 * count of on its entries.
 * @since Moodle 3.3
 * @param object $data The data object for this activity
 * @param object $course Course
 * @param object $cm course-module
 */
function data_update_completion_state($data, $course, $cm) {
    // If completion option is enabled, evaluate it and return true/false.
    $completion = new completion_info($course);
    if ($data->completionentries && $completion->is_enabled($cm)) {
        $numentries = data_numentries($data);
        // Check the number of entries required against the number of entries already made.
        if ($numentries >= $data->completionentries) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        } else {
            $completion->update_state($cm, COMPLETION_INCOMPLETE);
        }
    }
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $data       data object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.3
 */
function data_view($data, $course, $cm, $context) {
    global $CFG;
    require_once($CFG->libdir . '/completionlib.php');

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $data->id
    );

    $event = \mod_data\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('data', $data);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Get icon mapping for font-awesome.
 */
function mod_data_get_fontawesome_icon_map() {
    return [
        'mod_data:field/checkbox' => 'fa-check-square-o',
        'mod_data:field/date' => 'fa-calendar-o',
        'mod_data:field/file' => 'fa-file',
        'mod_data:field/latlong' => 'fa-globe',
        'mod_data:field/menu' => 'fa-bars',
        'mod_data:field/multimenu' => 'fa-bars',
        'mod_data:field/number' => 'fa-hashtag',
        'mod_data:field/picture' => 'fa-picture-o',
        'mod_data:field/radiobutton' => 'fa-circle-o',
        'mod_data:field/textarea' => 'fa-font',
        'mod_data:field/text' => 'fa-i-cursor',
        'mod_data:field/url' => 'fa-link',
    ];
}

/*
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function data_check_updates_since(cm_info $cm, $from, $filter = array()) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/mod/data/locallib.php');

    $updates = course_check_module_updates_since($cm, $from, array(), $filter);

    // Check for new entries.
    $updates->entries = (object) array('updated' => false);

    $data = $DB->get_record('data', array('id' => $cm->instance), '*', MUST_EXIST);
    $searcharray = [];
    $searcharray[DATA_TIMEMODIFIED] = new stdClass();
    $searcharray[DATA_TIMEMODIFIED]->sql     = '';
    $searcharray[DATA_TIMEMODIFIED]->params  = array();
    $searcharray[DATA_TIMEMODIFIED]->field   = 'r.timemodified';
    $searcharray[DATA_TIMEMODIFIED]->data    = $from;

    $currentgroup = groups_get_activity_group($cm);
    // Teachers should retrieve all entries when not in separate groups.
    if (has_capability('mod/data:manageentries', $cm->context) && groups_get_activity_groupmode($cm) != SEPARATEGROUPS) {
        $currentgroup = 0;
    }
    list($entries, $maxcount, $totalcount, $page, $nowperpage, $sort, $mode) =
        data_search_entries($data, $cm, $cm->context, 'list', $currentgroup, '', null, null, 0, 0, true, $searcharray);

    if (!empty($entries)) {
        $updates->entries->updated = true;
        $updates->entries->itemids = array_keys($entries);
    }

    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @param int $userid User id to use for all capability checks, etc. Set to 0 for current user (default).
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_data_core_calendar_provide_event_action(calendar_event $event,
                                                     \core_calendar\action_factory $factory,
                                                     int $userid = 0) {
    global $USER;

    if (!$userid) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['data'][$event->instance];

    if (!$cm->uservisible) {
        // The module is not visible to the user for any reason.
        return null;
    }

    $now = time();

    if (!empty($cm->customdata['timeavailableto']) && $cm->customdata['timeavailableto'] < $now) {
        // The module has closed so the user can no longer submit anything.
        return null;
    }

    // The module is actionable if we don't have a start time or the start time is
    // in the past.
    $actionable = (empty($cm->customdata['timeavailablefrom']) || $cm->customdata['timeavailablefrom'] <= $now);

    return $factory->create_instance(
        get_string('add', 'data'),
        new \moodle_url('/mod/data/view.php', array('id' => $cm->id)),
        1,
        $actionable
    );
}

/**
 * Add a get_coursemodule_info function in case any database type wants to add 'extra' information
 * for the course (see resource).
 *
 * Given a course_module object, this function returns any "extra" information that may be needed
 * when printing this activity in a course listing.  See get_array_of_activities() in course/lib.php.
 *
 * @param stdClass $coursemodule The coursemodule object (record).
 * @return cached_cm_info An object on information that the courses
 *                        will know about (most noticeably, an icon).
 */
function data_get_coursemodule_info($coursemodule) {
    global $DB;

    $dbparams = ['id' => $coursemodule->instance];
    $fields = 'id, name, intro, introformat, completionentries, timeavailablefrom, timeavailableto';
    if (!$data = $DB->get_record('data', $dbparams, $fields)) {
        return false;
    }

    $result = new cached_cm_info();
    $result->name = $data->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $result->content = format_module_intro('data', $data, $coursemodule->id, false);
    }

    // Populate the custom completion rules as key => value pairs, but only if the completion mode is 'automatic'.
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['completionentries'] = $data->completionentries;
    }
    // Other properties that may be used in calendar or on dashboard.
    if ($data->timeavailablefrom) {
        $result->customdata['timeavailablefrom'] = $data->timeavailablefrom;
    }
    if ($data->timeavailableto) {
        $result->customdata['timeavailableto'] = $data->timeavailableto;
    }

    return $result;
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $cm object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_data_get_completion_active_rule_descriptions($cm) {
    // Values will be present in cm_info, and we assume these are up to date.
    if (empty($cm->customdata['customcompletionrules'])
        || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return [];
    }

    $descriptions = [];
    foreach ($cm->customdata['customcompletionrules'] as $key => $val) {
        switch ($key) {
            case 'completionentries':
                if (!empty($val)) {
                    $descriptions[] = get_string('completionentriesdesc', 'data', $val);
                }
                break;
            default:
                break;
        }
    }
    return $descriptions;
}

/**
 * This function calculates the minimum and maximum cutoff values for the timestart of
 * the given event.
 *
 * It will return an array with two values, the first being the minimum cutoff value and
 * the second being the maximum cutoff value. Either or both values can be null, which
 * indicates there is no minimum or maximum, respectively.
 *
 * If a cutoff is required then the function must return an array containing the cutoff
 * timestamp and error string to display to the user if the cutoff value is violated.
 *
 * A minimum and maximum cutoff return value will look like:
 * [
 *     [1505704373, 'The due date must be after the sbumission start date'],
 *     [1506741172, 'The due date must be before the cutoff date']
 * ]
 *
 * @param calendar_event $event The calendar event to get the time range for
 * @param stdClass $instance The module instance to get the range from
 * @return array
 */
function mod_data_core_calendar_get_valid_event_timestart_range(\calendar_event $event, \stdClass $instance) {
    $mindate = null;
    $maxdate = null;

    if ($event->eventtype == DATA_EVENT_TYPE_OPEN) {
        // The start time of the open event can't be equal to or after the
        // close time of the database activity.
        if (!empty($instance->timeavailableto)) {
            $maxdate = [
                $instance->timeavailableto,
                get_string('openafterclose', 'data')
            ];
        }
    } else if ($event->eventtype == DATA_EVENT_TYPE_CLOSE) {
        // The start time of the close event can't be equal to or earlier than the
        // open time of the database activity.
        if (!empty($instance->timeavailablefrom)) {
            $mindate = [
                $instance->timeavailablefrom,
                get_string('closebeforeopen', 'data')
            ];
        }
    }

    return [$mindate, $maxdate];
}

/**
 * This function will update the data module according to the
 * event that has been modified.
 *
 * It will set the timeopen or timeclose value of the data instance
 * according to the type of event provided.
 *
 * @throws \moodle_exception
 * @param \calendar_event $event
 * @param stdClass $data The module instance to get the range from
 */
function mod_data_core_calendar_event_timestart_updated(\calendar_event $event, \stdClass $data) {
    global $DB;

    if (empty($event->instance) || $event->modulename != 'data') {
        return;
    }

    if ($event->instance != $data->id) {
        return;
    }

    if (!in_array($event->eventtype, [DATA_EVENT_TYPE_OPEN, DATA_EVENT_TYPE_CLOSE])) {
        return;
    }

    $courseid = $event->courseid;
    $modulename = $event->modulename;
    $instanceid = $event->instance;
    $modified = false;

    $coursemodule = get_fast_modinfo($courseid)->instances[$modulename][$instanceid];
    $context = context_module::instance($coursemodule->id);

    // The user does not have the capability to modify this activity.
    if (!has_capability('moodle/course:manageactivities', $context)) {
        return;
    }

    if ($event->eventtype == DATA_EVENT_TYPE_OPEN) {
        // If the event is for the data activity opening then we should
        // set the start time of the data activity to be the new start
        // time of the event.
        if ($data->timeavailablefrom != $event->timestart) {
            $data->timeavailablefrom = $event->timestart;
            $data->timemodified = time();
            $modified = true;
        }
    } else if ($event->eventtype == DATA_EVENT_TYPE_CLOSE) {
        // If the event is for the data activity closing then we should
        // set the end time of the data activity to be the new start
        // time of the event.
        if ($data->timeavailableto != $event->timestart) {
            $data->timeavailableto = $event->timestart;
            $modified = true;
        }
    }

    if ($modified) {
        $data->timemodified = time();
        $DB->update_record('data', $data);
        $event = \core\event\course_module_updated::create_from_cm($coursemodule, $context);
        $event->trigger();
    }
}

/**
 * Callback to fetch the activity event type lang string.
 *
 * @param string $eventtype The event type.
 * @return lang_string The event type lang string.
 */
function mod_data_core_calendar_get_event_action_string(string $eventtype): string {
    $modulename = get_string('modulename', 'data');

    switch ($eventtype) {
        case DATA_EVENT_TYPE_OPEN:
            $identifier = 'calendarstart';
            break;
        case DATA_EVENT_TYPE_CLOSE:
            $identifier = 'calendarend';
            break;
        default:
            return get_string('requiresaction', 'calendar', $modulename);
    }

    return get_string($identifier, 'data', $modulename);
}
