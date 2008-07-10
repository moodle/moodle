<?php
// $Id$

/**
 * This object is a representation of a file-based preset for database activities
 */

class Data_Preset
{
    
    /**
     * Required files in a preset directory
     * @var array $required_files
     */
    var $required_files = array('singletemplate.html',
                                'listtemplate.html',
                                'listtemplateheader.html',
                                'listtemplatefooter.html',
                                'addtemplate.html',
                                'rsstemplate.html',
                                'csstemplate.css',
                                'jstemplate.js',
                                'preset.xml');
    
    /**
    * The preset's shortname.
    * TODO document
    * @var string $shortname
    */
    var $shortname;
  
    /**
     * A database activity object
     * @var object $data
     */
    var $data;

    /**
     * Directory mapped to this Preset object.
     * @var string $directory
     */
    var $directory;

    /**
     * This Preset's singletemplate
     * @var string $singletemplate
     */
    var $singletemplate;

    /**
     * This Preset's listtemplate
     * @var string $listtemplate
     */
    var $listtemplate;
    
    /**
     * This Preset's listtemplateheader
     * @var string $listtemplateheader
     */
    var $listtemplateheader;
    
    /**
     * This Preset's listtemplatefooter
     * @var string $listtemplatefooter
     */
    var $listtemplatefooter;
    
    /**
     * This Preset's addtemplate
     * @var string $addtemplate
     */
    var $addtemplate;
    
    /**
     * This Preset's rsstemplate
     * @var string $rsstemplate
     */
    var $rsstemplate;
    
    /**
     * This Preset's csstemplate
     * @var string $csstemplate
     */
    var $csstemplate;
    
    /**
     * This Preset's jstemplate
     * @var string $jstemplate
     */
    var $jstemplate;
    
    /**
     * This Preset's xml
     * @var string $xml
     */
    var $xml;
 
    var $user_id;

    /**
     * Constructor
     */
    function Data_Preset($shortname = null, $data_id = null, $directory = null, $user_id = null)
    { 
        $this->shortname = $shortname;
        $this->user_id = $user_id;
        
        if (empty($directory)) {
            $this->directory = $this->get_path();
        } else {
            $this->directory = $directory;
        }
       
        if (!empty($data_id)) {  
            if (!$this->data = get_record('data', 'id', $data_id)) {
                print_error('wrongdataid','data'); 
            } else { 
                $this->listtemplate       = $this->data->listtemplate;
                $this->singletemplate     = $this->data->singletemplate;
                $this->listtemplateheader = $this->data->listtemplateheader;
                $this->listtemplatefooter = $this->data->listtemplatefooter;
                $this->addtemplate        = $this->data->addtemplate;
                $this->rsstemplate        = $this->data->rsstemplate;
                $this->csstemplate        = $this->data->csstemplate;
                $this->jstemplate         = $this->data->jstemplate;
            }
        }
    }
    
    /*
     * Returns the best name to show for a preset
     * If the shortname has spaces in it, replace them with underscores. 
     * Convert the name to lower case.
     */
    function best_name($shortname = null) {
        if (empty($shortname)) {
            $shortname = $this->shortname;
        }

        /// We are looking inside the preset itself as a first choice, but also in normal data directory
        $string = get_string('presetname'.$shortname, 'data', NULL, $this->directory.'/lang/');

        if (substr($string, 0, 1) == '[') {
            return strtolower(str_replace(' ', '_', $shortname));
        } else {
            return $string;
        }
    }
    
    /**
    * TODO figure out what's going on here with the user id. This method doesn't look quite right to me. 
    */
    function get_path() {
        global $USER, $CFG, $COURSE;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        if ($this->user_id > 0 && ($this->user_id == $USER->id || has_capability('mod/data:viewalluserpresets', $context))) {
            return $CFG->dataroot.'/data/preset/'.$this->user_id.'/'.$this->shortname;
        } else if ($this->user_id == 0) {
            return $CFG->dirroot.'/mod/data/preset/'.$this->shortname;
        } else if ($this->user_id < 0) {
            return $CFG->dataroot.'/temp/data/'.-$this->user_id.'/'.$this->shortname;
        }

        return 'Does it disturb you that this code will never run?';
    }
   
   
   /**
    * A preset is a directory with a number of required files. 
    * This function verifies that the given directory contains
    * all these files, thus validating as a preset directory.
    * 
    * @param string $directory An optional directory to check. Will use this Preset's directory if not provided.
    * @return mixed True if the directory contains all the files required to qualify as Preset object; 
    *                 an array of the missing filename is returned otherwise
    */
    function has_all_required_files($directory = null) 
    {
        if (empty($directory)) {
            $directory = $this->directory;
        } else {
            $directory = rtrim($directory, '/\\') . '/';
        }
        
        $missing_files = array();

        foreach ($this->required_files as $file) {
            if(!file_exists($directory . '/' . $file)) {
                $missing_files[] = $file;
            }
        }
        
        if (!empty($missing_files)) {
            return $missing_files;
        }

        return true;
    }
    
    /**
    * Deletes all the files in the directory mapped by this Preset object.
    *
    * @return boolean False if an error occured while trying to delete one of the files, true otherwise.
    */
    function clean_files() 
    {
        foreach ($this->required_files as $file) {
            if (!unlink($this->directory . '/' . $file)) {
                return $file;
            }
        }

        return true;
    }
   
    function get_template_files()
    {
        $template_files = array();

        foreach ($this->required_files as $file) {
            if (preg_match('/^([a-z]+template[a-z]?)\.[a-z]{2,4}$/', $file, $matches)) {
                $template_files[$matches[1]] = $file;
            }
        }

        return $template_files; 
    }
    
    /**
    * Exports this Preset object as a series of files in the Preset's directory.
    * @return string The path/name of the resulting zip file if successful.
    */
    function export() {
        global $CFG;
        $this->directory = $CFG->dataroot.'/temp';
        // write all templates, but not the xml yet

        $template_files = $this->get_template_files();
        foreach ($template_files as $var => $file) {
            $handle = fopen($this->directory . '/' . $file, 'w');
            fwrite($handle, $this->$var);
            fclose($handle);
        }

        /* All the display data is now done. Now assemble preset.xml */
        $fields = get_records('data_fields', 'dataid', $this->data->id);
        $presetfile = fopen($this->directory.'/preset.xml', 'w');
        $presetxml = "<preset>\n\n";

        /* Database settings first. Name not included? */
        $settingssaved = array('intro', 
                               'comments',
                               'requiredentries', 
                               'requiredentriestoview', 
                               'maxentries',
                               'rssarticles', 
                               'approval', 
                               'scale', 
                               'assessed',
                               'defaultsort', 
                               'defaultsortdir', 
                               'editany');

        $presetxml .= "<settings>\n";
        foreach ($settingssaved as $setting) {
            $presetxml .= "<$setting>{$this->data->$setting}</$setting>\n";
        }
        $presetxml .= "</settings>\n\n";

        /* Now for the fields. Grabs all settings that are non-empty */
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $presetxml .= "<field>\n";
                foreach ($field as $key => $value) {
                    if ($value != '' && $key != 'id' && $key != 'dataid') {
                        $presetxml .= "<$key>$value</$key>\n";
                    }
                }
                $presetxml .= "</field>\n\n";
            }
        }

        $presetxml .= "</preset>";
        fwrite($presetfile, $presetxml);
        fclose($presetfile);

        /* Check all is well */
        if (is_array($missing_files = $this->has_all_required_files())) {
            $missing_files = implode(', ', $missing_files);
            print_error('filesnotgenerated', 'data', null, $missing_files); 
        }
        
        // Remove export.zip
        @unlink($this->directory.'/export.zip');
        
        $filelist = array();
        foreach ($this->required_files as $file) {
            $filelist[$file] = $this->directory . '/' . $file;
        }

        // zip_files is part of moodlelib
        $status = zip_files($filelist, $this->directory.'/export.zip');

        /* made the zip... now return the filename for storage.*/
        return $this->directory.'/export.zip';
    }
    
    
    /**
    * Loads the contents of the preset folder to initialise this Preset object.
    * TODO document
    */
    function load_from_file($directory = null) {
        global $CFG;
        if (empty($directory) && empty($this->directory)) {
            $this->directory = $this->get_path();
        }

        if (is_array($missing_files = $this->has_all_required_files())) {
            $a = new StdClass();
            $a->missing_files = implode(', ', $missing_files);
            $a->directory = $this->directory;
            print_error('directorynotapreset','data', null, $a); 
        }

        /* Grab XML */
        $presetxml = file_get_contents($this->directory.'/preset.xml');
        $parsedxml = xmlize($presetxml);

        /* First, do settings. Put in user friendly array. */
        $settingsarray = $parsedxml['preset']['#']['settings'][0]['#'];
        $settings = new StdClass();

        foreach ($settingsarray as $setting => $value) {
            $settings->$setting = $value[0]['#'];
        }

        /* Now work out fields to user friendly array */
        $fieldsarray = $parsedxml['preset']['#']['field'];
        $fields = array();
        foreach ($fieldsarray as $field) {
            $f = new StdClass();
            foreach ($field['#'] as $param => $value) {
                $f->$param = $value[0]['#'];
            }
            $f->dataid = $this->data->id;
            $f->type = clean_param($f->type, PARAM_ALPHA);
            $fields[] = $f;
        }
        

        /* Now add the HTML templates to the settings array so we can update d */
        $template_files = $this->get_template_files();
        
        foreach ($template_files as $var => $file) {
            $settings->$var = file_get_contents($this->directory . '/' . $file);
        }
        
        $settings->instance = $this->data->id;

        /* Now we look at the current structure (if any) to work out whether we need to clear db
           or save the data */
        $currentfields = array();
        $currentfields = get_records('data_fields', 'dataid', $this->data->id);
        $currentfields = array_merge($currentfields);
        return array($settings, $fields, $currentfields);
    }
    
    
    /**
    * Import options
    * TODO document
    * TODO replace all output by a return value
    */
    function get_import_html() {
        if (!confirm_sesskey()) {
            print_error("confirmsesskeybad");
        }

        $strblank = get_string('blank', 'data');
        $strcontinue = get_string('continue');
        $strwarning = get_string('mappingwarning', 'data');
        $strfieldmappings = get_string('fieldmappings', 'data');
        $strnew = get_string('new');

        $sesskey = sesskey();

        list($settings, $newfields,  $currentfields) = $this->load_from_file();

        $html = '';

        $html .= '<div style="text-align:center"><form action="preset.php" method="post">';
        $html .= '<fieldset class="invisiblefieldset">';
        $html .= '<input type="hidden" name="action" value="finishimport" />';
        $html .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $html .= '<input type="hidden" name="d" value="'.$this->data->id.'" />';
        $html .= '<input type="hidden" name="fullname" value="'.$this->user_id.'/'.$this->shortname.'" />';

        if (!empty($currentfields) && !empty($newfields)) {
            $html .= "<h3>$strfieldmappings ";
            helpbutton('fieldmappings', '', 'data');
            $html .= '</h3><table>';

            foreach ($newfields as $nid => $newfield) {
                $html .= "<tr><td><label for=\"id_$newfield->name\">$newfield->name</label></td>";
                $html .= '<td><select name="field_'.$nid.'" id="id_'.$newfield->name.'">';

                $selected = false;
                foreach ($currentfields as $cid => $currentfield) {
                    if ($currentfield->type == $newfield->type) {
                        if ($currentfield->name == $newfield->name) {
                            $html .= '<option value="'.$cid.'" selected="selected">'.$currentfield->name.'</option>';
                            $selected=true;
                        }
                        else {
                            $html .= '<option value="'.$cid.'">'.$currentfield->name.'</option>';
                        }
                    }
                }

                if ($selected)
                    $html .= '<option value="-1">-</option>';
                else
                    $html .= '<option value="-1" selected="selected">-</option>';
                $html .= '</select></td></tr>';
            }
            $html .= '</table>';
            $html .= "<p>$strwarning</p>";
        }
        else if (empty($newfields)) {
            print_error('nodefinedfields', 'data'); 
        }
        $html .= '<input type="submit" value="'.$strcontinue.'" /></fieldset></form></div>';
        return $html; 
    }


    /**
    * import()
    * TODO document
    */
    function import() {
        global $CFG;

        list($settings, $newfields, $currentfields) = $this->load_from_file();
        $preservedfields = array();

        /* Maps fields and makes new ones */
        if (!empty($newfields)) {
            /* We require an injective mapping, and need to know what to protect */
            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);
                if ($cid == -1) continue;

                if (array_key_exists($cid, $preservedfields)) {
                    print_error('notinjectivemap', 'data'); 
                } else {
                    $preservedfields[$cid] = true;
                }
            }

            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);
                /* A mapping. Just need to change field params. Data kept. */
                if ($cid != -1 and isset($currentfelds[$cid])) {
                    $fieldobject = data_get_field_from_id($currentfields[$cid]->id, $this->data);
                    foreach ($newfield as $param => $value) {
                        if ($param != "id") {
                            $fieldobject->field->$param = $value;
                        }
                    }
                    unset($fieldobject->field->similarfield);
                    $fieldobject->update_field();
                    unset($fieldobject);
                }
                /* Make a new field */
                else {
                    include_once("field/$newfield->type/field.class.php");

                    if (!isset($newfield->description)) {
                        $newfield->description = '';
                    }
                    $classname = 'data_field_'.$newfield->type;
                    $fieldclass = new $classname($newfield, $this->data);
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
                    // Why delete existing data records and related comments/ratings ??
                    /*
                    if ($content = get_records('data_content', 'fieldid', $id)) {
                        foreach ($content as $item) {
                            delete_records('data_ratings', 'recordid', $item->recordid);
                            delete_records('data_comments', 'recordid', $item->recordid);
                            delete_records('data_records', 'id', $item->recordid);
                        }
                    }
                    */
                    delete_records('data_content', 'fieldid', $id);
                    delete_records('data_fields', 'id', $id);
                }
            }
        }

        data_update_instance(addslashes_object($settings));

        if (strstr($this->directory, '/temp/')) clean_preset($this->directory); /* Removes the temporary files */
        return true;
    }
    
    /**
     * Runs the Preset action method that matches the given action string.
     * @param string $action
     * @return string html
     */
    function process_action($action, $params)
    {
        echo $action;
        if (in_array("action_$action", get_class_methods(get_class($this)))) {
            return $this->{"action_$action"}($params);
        } else {
            print_error('undefinedprocessactionmethod', 'data', null, $action); 
        }
    }

////////////////////
// ACTION METHODS //
////////////////////
    
    function action_base($params)
    {
        return null;
    }

    function action_confirmdelete($params)
    {
        global $CFG, $USER;
        $html = '';
        $course = $params['course'];
        $shortname = $params['shortname'];

        if (!confirm_sesskey()) { // GET request ok here
            print_error('confirmsesskeybad'); 
        }
        
        $this->user_id = $params['userid'];

        if (!$cm = get_coursemodule_from_instance('data', $this->data->id, $course->id)) {
            print_error('invalidrequest'); 
        }

        $context = get_context_instance(COURSE_MODULE, $cm->id);

        if ($this->user_id > 0 and ($this->user_id == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
           //ok can delete
        } else {
            print_error('invalidrequest'); 
        }

        $path = $this->get_path();

        $strwarning = get_string('deletewarning', 'data').'<br />'.
                      data_preset_name($shortname, $path);

        $options = new object();
        $options->fullname = $this->user_id.'/'.$shortname;
        $options->action   = 'delete';
        $options->d        = $this->data->id;
        $options->sesskey  = sesskey();

        $optionsno = new object();
        $optionsno->d = $this->data->id;
        notice_yesno($strwarning, 'preset.php', 'preset.php', $options, $optionsno, 'post', 'get');
        $html .= print_footer($course, null, true); 
        echo $html;
        exit();
    } 

    function action_delete($params)
    { 
        global $CFG, $USER;
        $course = $params['course'];
        $shortname = $params['shortname'];
        
        if (!data_submitted() and !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        if (!$cm = get_coursemodule_from_instance('data', $this->data->id, $course->id)) {
            print_error('invalidrequest'); 
        }

        $context = get_context_instance(COURSE_MODULE, $cm->id);

        if ($this->user_id > 0 and ($this->user_id == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
           //ok can delete
        } else {
            print_error('invalidrequest');
        }
        
        $this->shortname = $this->best_name($shortname);

        $this->path = $this->get_path();
        $this->directory = $this->path;
        
        if (!$this->clean_files()) {
            print_error('failedpresetdelete', 'data'); 
        }
        rmdir($this->path);

        $strdeleted = get_string('deleted', 'data');
        notify("$shortname $strdeleted", 'notifysuccess');
    }

    function action_importpreset($params)
    { 
        $course = $params['course'];
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        $this->shortname = $params['shortname'];
        $this->data = $params['data'];
        $this->user_id = $params['userid']; 
        $html = '';
        $html .= $this->get_import_html();

        $html .= print_footer($course, null, true); 
        echo $html;
        exit();
    }

    function action_importzip($params)
    {
        global $CFG, $USER;
        $course = $params['course'];
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalid_request');
        }

        if (!make_upload_directory('temp/data/'.$USER->id)) {
            print_error('errorcreatingdirectory', null, null, 'temp/data/' . $USER->id); 
        }

        $this->file = $CFG->dataroot.'/temp/data/'.$USER->id;
        $this->directory = $this->file;
        $this->user_id = $USER->id;
        $this->clean_files($this->file);

        if (!unzip_file($CFG->dataroot."/$USER->id/$file", $this->file, false)) {
        }

        $html .= $this->get_import_html();
        $html .= print_footer($course, null, true); 
        echo $html;
        exit();
    }

    function action_finishimport($params)
    {
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest'); 
        }
        $this->shortname = $this->best_name($this->data->name);
        $this->directory = $this->get_path();
        $this->import();

        $strimportsuccess = get_string('importsuccess', 'data');
        $straddentries = get_string('addentries', 'data');
        $strtodatabase = get_string('todatabase', 'data');
        if (!get_records('data_records', 'dataid', $this->data->id)) {
            notify('$strimportsuccess <a href="edit.php?d=' . $this->data->id . "\">$straddentries</a> $strtodatabase", 'notifysuccess');
        } else {
            notify("$strimportsuccess", 'notifysuccess');
        } 
    }

    function action_export($params)
    {
        global $CFG, $USER;
        $course = $params['course'];
        $html = '';

        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalid_request');
        }

        $this->shortname = $params['shortname'];
        $this->data = $params['data'];
        
        $html .= '<div style="text-align:center">';
        $file = $this->export();
        $html .= get_string('exportedtozip', 'data')."<br />";
        $permanentfile = $CFG->dataroot.'/' . $course->id . '/moddata/data/' . $this->data->id . '/preset.zip';
        @unlink($permanentfile);
        /* is this created elsewhere? sometimes its not present... */
        make_upload_directory($course->id . '/moddata/data/' . $this->data->id);

        /* now just move the zip into this folder to allow a nice download */
        if (!rename($file, $permanentfile)) {
            print_error('movezipfailed', 'data'); 
        }
        
        require_once($CFG->libdir.'/filelib.php');
        $html .= '<a href="'. get_file_url($course->id .'/moddata/data/'. $this->data->id .'/preset.zip') .'">'. get_string('download', 'data') .'</a>';
        $html .= '</div>'; 
        return $html;
    }
   
    /**
     * First stage of saving a Preset: ask for a name
     */
    function action_save1($params)
    {
        $html = '';
        $sesskey = sesskey();
        $course = $params['course'];
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalid_request');
        }

        $strcontinue = get_string('continue');
        $strwarning = get_string('presetinfo', 'data');
        $strname = get_string('shortname');

        $html .= '<div style="text-align:center">';
        $html .= '<p>'.$strwarning.'</p>';
        $html .= '<form action="preset.php" method="post">';
        $html .= '<fieldset class="invisiblefieldset">';
        $html .= '<label for="shortname">'.$strname.'</label> <input type="text" id="shortname" name="name" value="'.$this->best_name($this->data->name).'" />';
        $html .= '<input type="hidden" name="action" value="save2" />';
        $html .= '<input type="hidden" name="d" value="'.$this->data->id.'" />';
        $html .= '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
        $html .= '<input type="submit" value="'.$strcontinue.'" /></fieldset></form></div>';
        $html .= print_footer($course, null, true); 
        echo $html;
        exit();
    }

    /**
     * Second stage of saving a preset: If the given name already exists, 
     * suggest to use a different name or offer to overwrite the existing preset.
     */
    function action_save2($params)
    {
        $course = $params['course'];
        $this->data = $params['data'];

        global $CFG, $USER;
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalid_request');
        }

        $strcontinue = get_string('continue');
        $stroverwrite = get_string('overwrite', 'data');
        $strname = get_string('shortname');

        $name = $this->best_name(optional_param('name', $this->data->name, PARAM_FILE));
        $this->shortname = $name;
        $sesskey = sesskey();

        if (!is_array($this->has_all_required_files("$CFG->dataroot/data/preset/$USER->id/$name"))) {
            notify("Preset already exists: Pick another name or overwrite ($CFG->dataroot/data/preset/$USER->id/$name)");

            $html .= '<div style="text-align:center">';
            $html .= '<form action="preset.php" method="post">';
            $html .= '<fieldset class="invisiblefieldset">';
            $html .= '<label for="shortname">'.$strname.'</label> <input id="shortname" type="text" name="name" value="'.$name.'" />';
            $html .= '<input type="hidden" name="action" value="save2" />';
            $html .= '<input type="hidden" name="d" value="'.$this->data->id.'" />';
            $html .= '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
            $html .= '<input type="submit" value="'.$strcontinue.'" /></fieldset></form>';

            $html .= '<form action="preset.php" method="post">';
            $html .= '<div>';
            $html .= '<input type="hidden" name="name" value="'.$name.'" />';
            $html .= '<input type="hidden" name="action" value="save3" />';
            $html .= '<input type="hidden" name="d" value="'.$this->data->id.'" />';
            $html .= '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
            $html .= '<input type="submit" value="'.$stroverwrite.'" /></div></form>';
            $html .= '</div>';
            $html .= print_footer($course, null, true); 
            echo $html;
            exit();
        } 
    }

    /**
     * Third stage of saving a preset, overwrites an existing preset with the new one.
     */
    function action_save3($params)
    {
        global $CFG, $USER;
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        $name = $this->best_name(optional_param('name', $this->data->name, PARAM_FILE));
        $this->directory = "/data/preset/$USER->id/$name";
        $this->shortname = $name;
        $this->user_id = $USER->id;

        make_upload_directory($this->directory);
        $this->clean_files($CFG->dataroot.$this->directory);

        $file = $this->export();
        if (!unzip_file($file, $CFG->dataroot.$this->directory, false)) {
            print_error('cannotunzipfile'); 
        }
        notify(get_string('savesuccess', 'data'), 'notifysuccess'); 
    }
}

?>
