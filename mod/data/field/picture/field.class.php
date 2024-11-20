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
 * Class picture field for database activity
 *
 * @package    datafield_picture
 * @copyright  2005 Martin Dougiamas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class data_field_picture extends data_field_base {
    var $type = 'picture';
    var $previewwidth  = 50;
    var $previewheight = 50;

    public function supports_preview(): bool {
        return true;
    }

    public function get_data_content_preview(int $recordid): stdClass {
        return (object)[
            'id' => 0,
            'fieldid' => $this->field->id,
            'recordid' => $recordid,
            'content' => 'datafield_picture/preview',
            'content1' => get_string('sample', 'datafield_picture'),
            'content2' => null,
            'content3' => null,
            'content4' => null,
        ];
    }

    function display_add_field($recordid = 0, $formdata = null) {
        global $CFG, $DB, $OUTPUT, $USER, $PAGE;

        // Necessary for the constants used in args.
        require_once($CFG->dirroot . '/repository/lib.php');

        $file        = false;
        $content     = false;
        $alttext     = '';
        $itemid = null;
        $fs = get_file_storage();

        if ($formdata) {
            $fieldname = 'field_' . $this->field->id . '_file';
            $itemid = clean_param($formdata->$fieldname, PARAM_INT);
            $fieldname = 'field_' . $this->field->id . '_alttext';
            if (isset($formdata->$fieldname)) {
                $alttext = $formdata->$fieldname;
            }
        } else if ($recordid) {
            if (!$content = $DB->get_record('data_content', array('fieldid' => $this->field->id, 'recordid' => $recordid))) {
                // Quickly make one now!
                $content = new stdClass();
                $content->fieldid  = $this->field->id;
                $content->recordid = $recordid;
                $id = $DB->insert_record('data_content', $content);
                $content = $DB->get_record('data_content', array('id' => $id));
            }
            file_prepare_draft_area($itemid, $this->context->id, 'mod_data', 'content', $content->id);
            if (!empty($content->content)) {
                if ($file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
                    $usercontext = context_user::instance($USER->id);

                    if ($thumbfile = $fs->get_file($usercontext->id, 'user', 'draft', $itemid, '/', 'thumb_'.$content->content)) {
                        $thumbfile->delete();
                    }
                }
            }
            $alttext = $content->content1;
        } else {
            $itemid = file_get_unused_draft_itemid();
        }
        $str = '<div title="' . s($this->field->description) . '">';
        $str .= '<fieldset><legend><span class="accesshide">'.s($this->field->name);

        if ($this->field->required) {
            $str .= '&nbsp;' . get_string('requiredelement', 'form') . '</span></legend>';
            $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
            $str .= html_writer::div($image, 'inline-req');
        } else {
            $str .= '</span></legend>';
        }
        $str .= '<noscript>';
        if ($file) {
            $src = file_encode_url($CFG->wwwroot.'/pluginfile.php/', $this->context->id.'/mod_data/content/'.$content->id.'/'.$file->get_filename());
            $str .= '<img width="'.s($this->previewwidth).'" height="'.s($this->previewheight).'" src="'.$src.'" alt="" />';
        }
        $str .= '</noscript>';

        $options = new stdClass();
        $options->maxbytes  = $this->field->param3;
        $options->maxfiles  = 1; // Only one picture permitted.
        $options->itemid    = $itemid;
        $options->accepted_types = array('web_image');
        $options->return_types = FILE_INTERNAL;
        $options->context = $PAGE->context;
        if (!empty($file)) {
            $options->filename = $file->get_filename();
            $options->filepath = '/';
        }

        $fm = new form_filemanager($options);
        // Print out file manager.

        $output = $PAGE->get_renderer('core', 'files');
        $str .= '<div class="mod-data-input">';
        $str .= $output->render($fm);

        $str .= '<div class="mdl-left">';
        $str .= '<input type="hidden" name="field_' . $this->field->id . '_file" value="' . s($itemid) . '" />';
        $str .= '<label for="field_' . $this->field->id . '_alttext">' .
                get_string('alttext', 'data') .
                '</label>&nbsp;<input type="text" class="form-control" name="field_' .
                $this->field->id . '_alttext" id="field_' . $this->field->id . '_alttext" value="' . s($alttext) . '" />';
        $str .= '</div>';
        $str .= '</div>';

        $str .= '</fieldset>';
        $str .= '</div>';

        return $str;
    }

    /**
     * Validate the image field type parameters.
     *
     * This will check for valid numeric values in the width and height fields.
     *
     * @param stdClass $fieldinput the field input data
     * @return array array of error messages if width or height parameters are not numeric
     * @throws coding_exception
     */
    public function validate(stdClass $fieldinput): array {
        $errors = [];
        // These are the params we have to check if they are numeric, because they represent width and height of the image
        // in single and list view.
        $widthandheightparams = ['param1', 'param2', 'param4', 'param5'];

        foreach ($widthandheightparams as $param) {
            if (!empty($fieldinput->$param) && !is_numeric($fieldinput->$param)) {
                $errors[$param] = get_string('error_invalid' . $param, 'datafield_picture');
            }
        }
        return $errors;
    }

    // TODO delete this function and instead subclass data_field_file - see MDL-16493

    function get_file($recordid, $content=null) {
        global $DB;
        if (empty($content)) {
            if (!$content = $DB->get_record('data_content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid))) {
                return null;
            }
        }
        $fs = get_file_storage();
        if (!$file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
            return null;
        }

        return $file;
    }

    function display_search_field($value = '') {
        return '<label class="accesshide" for="f_' . $this->field->id . '">' . get_string('fieldname', 'data') . '</label>' .
               '<input type="text" size="16" id="f_' . $this->field->id . '" name="f_' . $this->field->id . '" ' .
               'value="' . s($value) . '" class="form-control"/>';
    }

    public function parse_search_field($defaults = null) {
        $param = 'f_'.$this->field->id;
        if (empty($defaults[$param])) {
            $defaults = array($param => '');
        }
        return optional_param($param, $defaults[$param], PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_picture_$i";
        return array(" ({$tablealias}.fieldid = {$this->field->id} AND ".$DB->sql_like("{$tablealias}.content", ":$name", false).") ", array($name=>"%$value%"));
    }

    function display_browse_field($recordid, $template) {
        global $OUTPUT;

        $content = $this->get_data_content($recordid);

        if (!$content || empty($content->content)) {
            return '';
        }

        $alt   = $content->content1;
        $title = $alt;

        $width  = $this->field->param1 ? ' width="' . s($this->field->param1) . '" ' : ' ';
        $height = $this->field->param2 ? ' height="' . s($this->field->param2) . '" ' : ' ';

        if ($this->preview) {
            $imgurl = $OUTPUT->image_url('sample', 'datafield_picture');
            return '<img ' . $width . $height . ' src="' . $imgurl . '" alt="' . s($alt) . '" class="list_picture"/>';
        }

        if ($template == 'listtemplate') {
            $filename = 'thumb_' . $content->content;
            // Thumbnails are already converted to the correct width and height.
            $width = '';
            $height = '';
            $url = new moodle_url('/mod/data/view.php', ['d' => $this->field->dataid, 'rid' => $recordid]);
        } else {
            $filename = $content->content;
            $url = null;
        }
        $imgurl = moodle_url::make_pluginfile_url($this->context->id, 'mod_data', 'content', $content->id, '/', $filename);

        if (!$url) {
            $url = $imgurl;
        }
        $img = '<img ' . $width . $height . ' src="' . $imgurl->out() . '" alt="' . s($alt) .
            '" title="' . s($title) . '" class="list_picture"/>';
        return '<a class="data-field-link" href="' . $url->out() . '">' . $img . '</a>';
    }

    function update_field() {
        global $DB, $OUTPUT;

        // Get the old field data so that we can check whether the thumbnail dimensions have changed
        $oldfield = $DB->get_record('data_fields', array('id'=>$this->field->id));
        $DB->update_record('data_fields', $this->field);

        // Have the thumbnail dimensions changed?
        if ($oldfield && ($oldfield->param4 != $this->field->param4 || $oldfield->param5 != $this->field->param5)) {
            // Check through all existing records and update the thumbnail
            if ($contents = $DB->get_records('data_content', array('fieldid'=>$this->field->id))) {
                $fs = get_file_storage();
                if (count($contents) > 20) {
                    echo $OUTPUT->notification(get_string('resizingimages', 'data'), 'notifysuccess');
                    echo "\n\n";
                    // To make sure that ob_flush() has the desired effect
                    ob_flush();
                }
                foreach ($contents as $content) {
                    if (!$file = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', $content->content)) {
                        continue;
                    }
                    if ($thumbfile = $fs->get_file($this->context->id, 'mod_data', 'content', $content->id, '/', 'thumb_'.$content->content)) {
                        $thumbfile->delete();
                    }
                    core_php_time_limit::raise(300);
                    // Might be slow!
                    $this->update_thumbnail($content, $file);
                }
            }
        }
        return true;
    }

    function update_content($recordid, $value, $name='') {
        global $CFG, $DB, $USER;

        // Should always be available since it is set by display_add_field before initializing the draft area.
        $content = $DB->get_record('data_content', array('fieldid' => $this->field->id, 'recordid' => $recordid));
        if (!$content) {
            $content = (object)array('fieldid' => $this->field->id, 'recordid' => $recordid);
            $content->id = $DB->insert_record('data_content', $content);
        }

        $names = explode('_', $name);
        switch ($names[2]) {
            case 'file':
                $fs = get_file_storage();
                file_save_draft_area_files($value, $this->context->id, 'mod_data', 'content', $content->id);
                $usercontext = context_user::instance($USER->id);
                $files = $fs->get_area_files(
                    $this->context->id,
                    'mod_data', 'content',
                    $content->id,
                    'itemid, filepath, filename',
                    false);

                // We expect no or just one file (maxfiles = 1 option is set for the form_filemanager).
                if (count($files) == 0) {
                    $content->content = null;
                } else {
                    $file = array_values($files)[0];

                    if (count($files) > 1) {
                        // This should not happen with a consistent database. Inform admins/developers about the inconsistency.
                        debugging('more then one file found in mod_data instance {$this->data->id} picture field (field id: {$this->field->id}) area during update data record {$recordid} (content id: {$content->id})', DEBUG_NORMAL);
                    }

                    if ($file->get_imageinfo() === false) {
                        $url = new moodle_url('/mod/data/edit.php', array('d' => $this->field->dataid));
                        redirect($url, get_string('invalidfiletype', 'error', $file->get_filename()));
                    }
                    $content->content = $file->get_filename();
                    $this->update_thumbnail($content, $file);
                }
                $DB->update_record('data_content', $content);

                break;

            case 'alttext':
                // only changing alt tag
                $content->content1 = clean_param($value, PARAM_NOTAGS);
                $DB->update_record('data_content', $content);
                break;

            default:
                break;
        }
    }

    function update_thumbnail($content, $file) {
        // (Re)generate thumbnail image according to the dimensions specified in the field settings.
        // If thumbnail width and height are BOTH not specified then no thumbnail is generated, and
        // additionally an attempted delete of the existing thumbnail takes place.
        $fs = get_file_storage();
        $filerecord = [
            'contextid' => $file->get_contextid(), 'component' => $file->get_component(), 'filearea' => $file->get_filearea(),
            'itemid' => $file->get_itemid(), 'filepath' => $file->get_filepath(),
            'filename' => 'thumb_' . $file->get_filename(), 'userid' => $file->get_userid()
        ];
        try {
            // This may fail for various reasons.
            $newwidth = isset($this->field->param4) ? (int) $this->field->param4 : null;
            $newheight = isset($this->field->param5) ? (int) $this->field->param5 : null;
            $fs->convert_image($filerecord, $file, $newwidth, $newheight, true);
            return true;
        } catch (Exception $e) {
            debugging($e->getMessage());
            return false;
        }
    }

    /**
     * Here we export the text value of a picture field which is the filename of the exported picture.
     *
     * @param stdClass $record the record which is being exported
     * @return string the value which will be stored in the exported file for this field
     */
    public function export_text_value(stdClass $record): string {
        return !empty($record->content) ? $record->content : '';
    }

    /**
     * Specifies that this field type supports the export of files.
     *
     * @return bool true which means that file export is being supported by this field type
     */
    public function file_export_supported(): bool {
        return true;
    }

    /**
     * Exports the file content for file export.
     *
     * @param stdClass $record the data content record the file belongs to
     * @return null|string The file content of the stored file or null if no file should be exported for this record
     */
    public function export_file_value(stdClass $record): null|string {
        $file = $this->get_file($record->id);
        return $file ? $file->get_content() : null;
    }

    /**
     * Specifies that this field type supports the import of files.
     *
     * @return bool true which means that file import is being supported by this field type
     */
    public function file_import_supported(): bool {
        return true;
    }

    /**
     * Provides the necessary code for importing a file when importing the content of a mod_data instance.
     *
     * @param int $contentid the id of the mod_data content record
     * @param string $filecontent the content of the file to import as string
     * @param string $filename the filename the imported file should get
     * @return void
     */
    public function import_file_value(int $contentid, string $filecontent, string $filename): void {
        $filerecord = [
            'contextid' => $this->context->id,
            'component' => 'mod_data',
            'filearea' => 'content',
            'itemid' => $contentid,
            'filepath' => '/',
            'filename' => $filename,
        ];
        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $filecontent);
        $this->update_thumbnail(null, $file);
    }

    function file_ok($path) {
        return true;
    }

    /**
     * Custom notempty function
     *
     * @param string $value
     * @param string $name
     * @return bool
     */
    function notemptyfield($value, $name) {
        global $USER;

        $names = explode('_', $name);
        if ($names[2] == 'file') {
            $usercontext = context_user::instance($USER->id);
            $fs = get_file_storage();
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $value);
            return count($files) >= 2;
        }
        return false;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of config parameters
     * @since Moodle 3.3
     */
    public function get_config_for_external() {
        // Return all the config parameters.
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = $this->field->{"param$i"};
        }
        return $configs;
    }

    public function get_field_params(): array {
        global $DB, $CFG;

        $data = parent::get_field_params();

        $course = $DB->get_record('course', ['id' => $this->data->course]);
        $filesizes = get_max_upload_sizes($CFG->maxbytes, $course->maxbytes, 0, $this->field->param3);

        foreach ($filesizes as $value => $name) {
            if (!((isset($this->field->param3) && $value == $this->field->param3))) {
                $data['options'][] = ['name' => $name, 'value' => $value, 'selected' => 0];
            } else {
                $data['options'][] = ['name' => $name, 'value' => $value, 'selected' => 1];
            }
        }

        return $data;
    }
}
