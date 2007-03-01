<?php // $Id$
/* Preset Menu
 *
 * This is the page that is the menu item in the config database
 * pages.
 */

require_once('../../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/uploadlib.php');
require_once($CFG->libdir.'/xmlize.php');


$id       = optional_param('id', 0, PARAM_INT);    // course module id
$d        = optional_param('d', 0, PARAM_INT);     // database activity id
$action   = optional_param('action', 'base', PARAM_ALPHANUM); // current action
$fullname = optional_param('fullname', '', PARAM_PATH); // directory the preset is in
$file     = optional_param('file', '', PARAM_FILE); // uploaded file

// find out preset owner userid and shortname
$parts = explode('/', $fullname);
$userid = empty($parts[0]) ? 0 : (int)$parts[0];
$shortname = empty($parts[1]) ? '' : $parts[1];
unset($parts);
unset($fullname);

if ($id) {
    if (! $cm = get_coursemodule_from_id('data', $id)) {
        error('Course Module ID was incorrect');
    }
    if (! $course = get_record('course', 'id', $cm->course)) {
        error('Course is misconfigured');
    }
    if (! $data = get_record('data', 'id', $cm->instance)) {
        error('Module Incorrect');
    }
} else if ($d) {
    if (! $data = get_record('data', 'id', $d)) {
        error('Database ID Incorrect');
    }
    if (! $course = get_record('course', 'id', $data->course)) {
        error('Course is misconfigured');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        error('Course Module ID was incorrect');
    }
} else {
    error('Parameter missing');
}

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
    error('Could not find context');
}

require_login($course->id, false, $cm);

require_capability('mod/data:managetemplates', $context);

if ($userid && ($userid != $USER->id) && !has_capability('mod/data:viewalluserpresets', $context)) {
    error('You are not allowed to access presets from other users');
}

/* Need sesskey security check here for import instruction */
$sesskey = sesskey();

/********************************************************************/
/* Output */
data_print_header($course, $cm, $data, 'presets');

switch ($action) {

        /***************** Deleting *****************/
    case 'confirmdelete' :
        if (!confirm_sesskey()) { // GET request ok here
            error("Sesskey Invalid");
        }

        if ($userid > 0 and ($userid == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
           //ok can delete
        } else {
            error("Invalid request");
        }

        $path = data_preset_path($course, $userid, $shortname);

        $strwarning = get_string('deletewarning', 'data').'<br />'.
                      data_preset_name($shortname, $path);

        $options = new object();
        $options->fullname = $userid.'/'.$shortname;
        $options->action   = 'delete';
        $options->d        = $data->id;
        $options->sesskey  = sesskey();

        $optionsno = new object();
        $optionsno->d = $data->id;
        notice_yesno($strwarning, 'preset.php', 'preset.php', $options, $optionsno, 'post', 'get');
        print_footer($course);
        exit;
        break;

    case 'delete' :
        if (!data_submitted() and !confirm_sesskey()) {
            error("Invalid request");
        }

        if ($userid > 0 and ($userid == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
           //ok can delete
        } else {
            error("Invalid request");
        }

        $presetpath = data_preset_path($course, $userid, $shortname);

        if (!clean_preset($presetpath)) {
            error("Error deleting a preset!");
        }
        @rmdir($presetpath);

        $strdeleted = get_string('deleted', 'data');
        notify("$shortname $strdeleted", 'notifysuccess');

        break;


        /***************** Importing *****************/
    case 'importpreset' :
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        $pimporter = new PresetImporter($course, $cm, $data, $userid, $shortname);
        $pimporter->import_options();

        print_footer($course);
        exit;
        break;

        /* Imports a zip file. */
    case 'importzip' :
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        if (!make_upload_directory('temp/data/'.$USER->id)) {
            error("Can't Create Directory");
        }

        $presetfile = $CFG->dataroot.'/temp/data/'.$USER->id;
        clean_preset($presetfile);

        if (!unzip_file($CFG->dataroot."/$course->id/$file", $presetfile, false)) {
            error("Can't unzip file");
        }

        $pimporter = new PresetImporter($course, $cm, $data, -$USER->id, $shortname);
        $pimporter->import_options();

        print_footer($course);
        exit;
        break;

    case 'finishimport':
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        $pimporter = new PresetImporter($course, $cm, $data, $userid, $shortname);
        $pimporter->import();

        $strimportsuccess = get_string('importsuccess', 'data');
        $straddentries = get_string('addentries', 'data');
        $strtodatabase = get_string('todatabase', 'data');
        if (!get_records('data_records', 'dataid', $data->id)) {
            notify("$strimportsuccess <a href='edit.php?d=$data->id'>$straddentries</a> $strtodatabase", 'notifysuccess');
        } else {
            notify("$strimportsuccess", 'notifysuccess');
        }
        break;

        /* Exports as a zip file ready for download. */
    case 'export':
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        echo '<div style="text-align:center">';
        $file = data_presets_export($course, $cm, $data);
        echo get_string('exportedtozip', 'data')."<br />";
        $perminantfile = $CFG->dataroot."/$course->id/moddata/data/$data->id/preset.zip";
        @unlink($perminantfile);
        /* is this created elsewhere? sometimes its not present... */
        make_upload_directory("$course->id/moddata/data/$data->id");

        /* now just move the zip into this folder to allow a nice download */
        if (!rename($file, $perminantfile)) error("Can't move zip");
        echo "<a href='$CFG->wwwroot/file.php/$course->id/moddata/data/$data->id/preset.zip'>".get_string('download', 'data')."</a>";
        echo '</div>';
        break;



        /***************** Exporting *****************/
    case 'save1':
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        $strcontinue = get_string('continue');
        $strwarning = get_string('presetinfo', 'data');
        $strname = get_string('shortname');

        echo '<div style="text-align:center">';
        echo '<p>'.$strwarning.'</p>';
        echo '<form action="preset.php" method="post">';
        echo '<fieldset class="invisiblefieldset">';
        echo '<label for="shorname">'.$strname.'</label> <input type="text" id="shorname" name="name" value="'.$data->name.'" />';
        echo '<input type="hidden" name="action" value="save2" />';
        echo '<input type="hidden" name="d" value="'.$data->id.'" />';
        echo '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
        echo '<input type="submit" value="'.$strcontinue.'" /></fieldset></form></div>';
        print_footer($course);
        exit;
        break;

    case 'save2':
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        $strcontinue = get_string('continue');
        $stroverwrite = get_string('overwrite', 'data');
        $strname = get_string('shortname');

        $name = optional_param('name', $data->name, PARAM_FILE);

        if (is_directory_a_preset("$CFG->dataroot/data/preset/$USER->id/$name")) {
            notify("Preset already exists: Pick another name or overwrite");

            echo '<div style="text-align:center">';
            echo '<form action="preset.php" method="post">';
            echo '<fieldset class="invisiblefieldset">';
            echo '<label for="shorname">'.$strname.'</label> <input type="textbox" name="name" value="'.$name.'" />';
            echo '<input type="hidden" name="action" value="save2" />';
            echo '<input type="hidden" name="d" value="'.$data->id.'" />';
            echo '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
            echo '<input type="submit" value="'.$strcontinue.'" /></fieldset></form>';

            echo '<form action="preset.php" method="post">';
            echo '<div>';
            echo '<input type="hidden" name="name" value="'.$name.'" />';
            echo '<input type="hidden" name="action" value="save3" />';
            echo '<input type="hidden" name="d" value="'.$data->id.'" />';
            echo '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
            echo '<input type="submit" value="'.$stroverwrite.'" /></div></form>';
            echo '</div>';
            print_footer($course);
            exit;
            break;
        }

    case 'save3':
        if (!data_submitted() or !confirm_sesskey()) {
            error("Invalid request");
        }

        $name = optional_param('name', $data->name, PARAM_FILE);
        $presetdirectory = "/data/preset/$USER->id/$name";

        make_upload_directory($presetdirectory);
        clean_preset($CFG->dataroot.$presetdirectory);

        $file = data_presets_export($course, $cm, $data);
        if (!unzip_file($file, $CFG->dataroot.$presetdirectory, false)) {
            error("Can't unzip to the preset directory");
        }
        notify(get_string('savesuccess', 'data'), 'notifysuccess');
        break;
}

$presets = data_get_available_presets($context);

$strimport         = get_string('import');
$strfromfile       = get_string('fromfile', 'data');
$strchooseorupload = get_string('chooseorupload', 'data');
$strusestandard    = get_string('usestandard', 'data');
$strchoose         = get_string('choose');
$strexport         = get_string('export', 'data');
$strexportaszip    = get_string('exportaszip', 'data');
$strsaveaspreset   = get_string('saveaspreset', 'data');
$strsave           = get_string('save', 'data');
$strdelete         = get_string('delete');

echo '<div style="text-align:center">';
echo '<table class="presets" cellpadding="5">';
echo '<tr><td valign="top" colspan="2" align="center"><h3>'.$strexport.'</h3></td></tr>';

echo '<tr><td><label>'.$strexportaszip.'</label>';
helpbutton('exportzip', '', 'data');
echo '</td><td>';
$options = new object();
$options->action = 'export';
$options->d = $data->id;
$options->sesskey = sesskey();
print_single_button('preset.php', $options, $strexport, 'post');
echo '</td></tr>';

echo '<tr><td><label>'.$strsaveaspreset.'</label>';
helpbutton('savepreset', '', 'data');
echo '</td><td>';
$options = new object();
$options->action = 'save1';
$options->d = $data->id;
$options->sesskey = sesskey();
print_single_button('preset.php', $options, $strsave, 'post');
echo '</td></tr>';


echo '<tr><td valign="top" colspan="2" align="center"><h3>'.$strimport.'</h3></td></tr>';

echo '<tr><td><label for="fromfile">'.$strfromfile.'</label>';
helpbutton('importfromfile', '', 'data');
echo '</td><td>';

echo '<form id="uploadpreset" method="post" action="preset.php">';
echo '<fieldset class="invisiblefieldset">';
echo '<input type="hidden" name="d" value="'.$data->id.'" />';
echo '<input type="hidden" name="action" value="importzip" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input name="file" size="20" value="" id="fromfile" type="text" /><input name="coursefiles" value="'.$strchooseorupload.'" onclick="return openpopup('."'/files/index.php?id=2&amp;choose=uploadpreset.file', 'coursefiles', 'menubar=0,location=0,scrollbars,resizable,width=750,height=500', 0".');" type="button" />';
echo '<input type="submit" value="'.$strimport.'" />';
echo '</fieldset></form>';
echo '</td></tr>';


echo '<tr valign="top"><td><label>'.$strusestandard.'</label>';
helpbutton('usepreset', '', 'data');
echo '</td><td>';

echo '<form id="presets" method="post" action="preset.php" >';
echo '<fieldset class="invisiblefieldset">';
echo '<input type="hidden" name="d" value="'.$data->id.'" />';
echo '<input type="hidden" name="action" value="importpreset" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

$i = 0;
foreach ($presets as $id => $preset) {
    $screenshot = '';
    if (!empty($preset->userid)) {
        $user = get_record('user', 'id', $preset->userid);
        $desc = $preset->name.' ('.fullname($user, true).')';
    } else {
        $desc = $preset->name;
    }

    if (!empty($preset->screenshot)) {
        $screenshot = '<img width="150" class="presetscreenshot" src="'.$preset->screenshot.'" alt="'.get_string('screenshot').' '.$desc.'" />&nbsp;';
    }

    $fullname = $preset->userid.'/'.$preset->shortname;

    $dellink = '';
    if ($preset->userid > 0 and ($preset->userid == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
        $dellink = '&nbsp;<a href="preset.php?d='.$data->id.'&amp;action=confirmdelete&amp;fullname='.$fullname.'&amp;sesskey='.sesskey().'">'.
                   '<img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$strdelete.' '.$desc.'" /></a>';
    }

    echo '<input type="radio" name="fullname" id="usepreset'.$i.'" value="'.$fullname.'" /><label for="usepreset'.$i++.'">'.$desc.'</label>'.$dellink.'<br />';
}
echo '<br />';
echo '<input type="submit" value="'.$strchoose.'" />';
echo '</fieldset></form>';
echo '</td></tr>';
echo '</table>';
echo '</div>';

print_footer($course);



function is_directory_a_preset($directory) {
    $directory = rtrim($directory, '/\\') . '/';
    if (file_exists($directory.'singletemplate.html') &&
            file_exists($directory.'listtemplate.html') &&
            file_exists($directory.'listtemplateheader.html') &&
            file_exists($directory.'listtemplatefooter.html') &&
            file_exists($directory.'addtemplate.html') &&
            file_exists($directory.'rsstemplate.html') &&
            file_exists($directory.'rsstitletemplate.html') &&
            file_exists($directory.'csstemplate.css') &&
            file_exists($directory.'jstemplate.js') &&
            file_exists($directory.'preset.xml')) return true;
    else return false;
}


function clean_preset($folder) {
    if (@unlink($folder.'/singletemplate.html') &&
        @unlink($folder.'/listtemplate.html') &&
        @unlink($folder.'/listtemplateheader.html') &&
        @unlink($folder.'/listtemplatefooter.html') &&
        @unlink($folder.'/addtemplate.html') &&
        @unlink($folder.'/rsstemplate.html') &&
        @unlink($folder.'/rsstitletemplate.html') &&
        @unlink($folder.'/csstemplate.css') &&
        @unlink($folder.'/jstemplate.js') &&
        @unlink($folder.'/preset.xml')) {
        return true;
    }
    return false;
}


function data_presets_export($course, $cm, $data) {
    global $CFG;
    /* Info Collected. Now need to make files in moodledata/temp */
    $tempfolder = $CFG->dataroot.'/temp';
    $singletemplate     = fopen($tempfolder.'/singletemplate.html', 'w');
    $listtemplate       = fopen($tempfolder.'/listtemplate.html', 'w');
    $listtemplateheader = fopen($tempfolder.'/listtemplateheader.html', 'w');
    $listtemplatefooter = fopen($tempfolder.'/listtemplatefooter.html', 'w');
    $addtemplate        = fopen($tempfolder.'/addtemplate.html', 'w');
    $rsstemplate        = fopen($tempfolder.'/rsstemplate.html', 'w');
    $rsstitletemplate   = fopen($tempfolder.'/rsstitletemplate.html', 'w');
    $csstemplate        = fopen($tempfolder.'/csstemplate.css', 'w');
    $jstemplate         = fopen($tempfolder.'/jstemplate.js', 'w');

    fwrite($singletemplate, $data->singletemplate);
    fwrite($listtemplate, $data->listtemplate);
    fwrite($listtemplateheader, $data->listtemplateheader);
    fwrite($listtemplatefooter, $data->listtemplatefooter);
    fwrite($addtemplate, $data->addtemplate);
    fwrite($rsstemplate, $data->rsstemplate);
    fwrite($rsstitletemplate, $data->rsstitletemplate);
    fwrite($csstemplate, $data->csstemplate);
    fwrite($jstemplate, $data->jstemplate);

    fclose($singletemplate);
    fclose($listtemplate);
    fclose($listtemplateheader);
    fclose($listtemplatefooter);
    fclose($addtemplate);
    fclose($rsstemplate);
    fclose($rsstitletemplate);
    fclose($csstemplate);
    fclose($jstemplate);

    /* All the display data is now done. Now assemble preset.xml */
    $fields = get_records('data_fields', 'dataid', $data->id);
    $presetfile = fopen($tempfolder.'/preset.xml', 'w');
    $presetxml = "<preset>\n\n";

    /* Database settings first. Name not included? */
    $settingssaved = array('intro', 'comments',
            'requiredentries', 'requiredentriestoview', 'maxentries',
            'rssarticles', 'approval', 'scale', 'assessed',
            'defaultsort', 'defaultsortdir', 'editany');

    $presetxml .= "<settings>\n";
    foreach ($settingssaved as $setting) {
        $presetxml .= "<$setting>{$data->$setting}</$setting>\n";
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
    if (!is_directory_a_preset($tempfolder)) {
        error("Not all files generated!");
    }

    $filelist = array(
            'singletemplate.html',
            'listtemplate.html',
            'listtemplateheader.html',
            'listtemplatefooter.html',
            'addtemplate.html',
            'rsstemplate.html',
            'rsstitletemplate.html',
            'csstemplate.css',
            'jstemplate.js',
            'preset.xml');

    foreach ($filelist as $key => $file) {
        $filelist[$key] = $tempfolder.'/'.$filelist[$key];
    }

    @unlink($tempfolder.'/export.zip');
    $status = zip_files($filelist, $tempfolder.'/export.zip');

    /* made the zip... now return the filename for storage.*/
    return $tempfolder.'/export.zip';
}



class PresetImporter {
    function PresetImporter($course, $cm, $data, $userid, $shortname) {
        global $CFG;
        $this->course = $course;
        $this->cm = $cm;
        $this->data = $data;
        $this->userid = $userid;
        $this->shortname = $shortname;
        $this->folder = data_preset_path($course, $userid, $shortname);
    }

    function get_settings() {
        global $CFG;

        if (!is_directory_a_preset($this->folder)) {
            error("$this->userid/$this->shortname Not a preset");
        }

        /* Grab XML */
        $presetxml = file_get_contents($this->folder.'/preset.xml');
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
        $settings->singletemplate     = file_get_contents($this->folder."/singletemplate.html");
        $settings->listtemplate       = file_get_contents($this->folder."/listtemplate.html");
        $settings->listtemplateheader = file_get_contents($this->folder."/listtemplateheader.html");
        $settings->listtemplatefooter = file_get_contents($this->folder."/listtemplatefooter.html");
        $settings->addtemplate        = file_get_contents($this->folder."/addtemplate.html");
        $settings->rsstemplate        = file_get_contents($this->folder."/rsstemplate.html");
        $settings->rsstitletemplate   = file_get_contents($this->folder."/rsstitletemplate.html");
        $settings->csstemplate        = file_get_contents($this->folder."/csstemplate.css");
        $settings->jstemplate         = file_get_contents($this->folder."/jstemplate.js");

        $settings->instance = $this->data->id;

        /* Now we look at the current structure (if any) to work out whether we need to clear db
           or save the data */
        $currentfields = array();
        $currentfields = get_records('data_fields', 'dataid', $this->data->id);

        return array($settings, $fields, $currentfields);
    }

    function import_options() {
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        $strblank = get_string('blank', 'data');
        $strnofields = get_string('nofields', 'data');
        $strcontinue = get_string('continue');
        $strwarning = get_string('mappingwarning', 'data');
        $strfieldmappings = get_string('fieldmappings', 'data');
        $strnew = get_string('new');
        $strold = get_string('old');

        $sesskey = sesskey();

        list($settings, $newfields,  $currentfields) = $this->get_settings();

        echo '<div style="text-align:center"><form action="preset.php" method="post">';
        echo '<fieldset class="invisiblefieldset">';
        echo '<input type="hidden" name="action" value="finishimport" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<input type="hidden" name="d" value="'.$this->data->id.'" />';
        echo '<input type="hidden" name="fullname" value="'.$this->userid.'/'.$this->shortname.'" />';

        if (!empty($currentfields) && !empty($newfields)) {
            echo "<h3>$strfieldmappings ";
            helpbutton('fieldmappings', '', 'data');
            echo '</h3><table>';

            foreach ($newfields as $nid => $newfield) {
                echo "<tr><td><label for=\"id_$newfield->name\">$newfield->name</label></td>";
                echo '<td><select name="field_'.$nid.'" id="id_'.$newfield->name.'">';

                $selected = false;
                foreach ($currentfields as $cid => $currentfield) {
                    if ($currentfield->type == $newfield->type) {
                        if ($currentfield->name == $newfield->name) {
                            echo '<option value="'.$cid.'" selected="selected">'.$currentfield->name.'</option>';
                            $selected=true;
                        }
                        else {
                            echo '<option value="$cid">'.$currentfield->name.'</option>';
                        }
                    }
                }

                if ($selected)
                    echo '<option value="-1">-</option>';
                else
                    echo '<option value="-1" selected="selected">-</option>';
                echo '</select></td></tr>';
            }
            echo '</table>';
            echo "<p>$strwarning</p>";
        }
        else if (empty($newfields)) {
            error("New preset has no defined fields!");
        }
        echo '<input type="submit" value="'.$strcontinue.'" /></fieldset></form></div>';

    }

    function import() {
        global $CFG;

        list($settings, $newfields, $currentfields) = $this->get_settings();
        $preservedfields = array();

        /* Maps fields and makes new ones */
        if (!empty($newfields)) {
            /* We require an injective mapping, and need to know what to protect */
            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);
                if ($cid == -1) continue;

                if (array_key_exists($cid, $preservedfields)) error("Not an injective map");
                else $preservedfields[$cid] = true;
            }

            foreach ($newfields as $nid => $newfield) {
                $cid = optional_param("field_$nid", -1, PARAM_INT);

                /* A mapping. Just need to change field params. Data kept. */
                if ($cid != -1) {
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

                    if ($content = get_records('data_content', 'fieldid', $id)) {
                        foreach ($content as $item) {
                            delete_records('data_ratings', 'recordid', $item->recordid);
                            delete_records('data_comments', 'recordid', $item->recordid);
                            delete_records('data_records', 'id', $item->recordid);
                        }
                    }
                    delete_records('data_content', 'fieldid', $id);
                    delete_records('data_fields', 'id', $id);
                }
            }
        }

        data_update_instance(addslashes_object($settings));

        if (strstr($this->folder, '/temp/')) clean_preset($this->folder); /* Removes the temporary files */
        return true;
    }
}

function data_preset_path($course, $userid, $shortname) {
    global $USER, $CFG;

    $context = get_context_instance(CONTEXT_COURSE, $course->id);

    $userid = (int)$userid;

    if ($userid > 0 && ($userid == $USER->id || has_capability('mod/data:viewalluserpresets', $context))) {
        return $CFG->dataroot.'/data/preset/'.$userid.'/'.$shortname;
    } else if ($userid == 0) {
        return $CFG->dirroot.'/mod/data/preset/'.$shortname;
    } else if ($userid < 0) {
        return $CFG->dataroot.'/temp/data/'.-$userid.'/'.$shortname;
    }

    return 'Does it disturb you that this code will never run?';
}

?>
