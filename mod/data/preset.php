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


$id      = optional_param('id', 0, PARAM_INT);    // course module id
$d       = optional_param('d', 0, PARAM_INT);     // database activity id 
$action  = optional_param('action', 'base', PARAM_RAW); // current action
$file    = optional_param('file', false, PARAM_PATH); // path of file to upload

if ($id) {
    if (! $cm = get_record('course_modules', 'id', $id)) {
        error('Course Module ID Incorrect');
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

require_login($course->id);

if (!isteacher($course->id)) {
    error('Must be a teacher to Import Database');
}


/* get the list of standard presets found in /mod/data/preset */
$presets = array();


if ($presetdir = opendir($CFG->dirroot.'/mod/data/preset')) {

    while ($userdir = readdir($presetdir)) {

        $fulluserdir = '/mod/data/preset/'.$userdir;

        if ($userdir == '.' || $userdir == '..') {
            continue;
        }

        /* Global Standard Presets */
        if (is_directory_a_preset($CFG->dirroot.$fulluserdir)) {
            $preset = new StdClass;
            $preset->path = $fulluserdir;
            $preset->name = $userdir;
            if (file_exists($fulluserdir.'/screenshot.jpg')) {
                $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$userdir.'/screenshot.jpg';
            }
            $presets[] = $preset;
            unset($preset);
        }

        /* User made presets stored in user folders */
        else if (get_record('user', 'id', $userdir)) {
            $userdirh = opendir($CFG->dirroot.$fulluserdir);
            while ($userpresetdir = readdir($userdirh)) {
                $fulluserpresetdir = $fulluserdir.'/'.$userpresetdir;
                if ($userpresetdir != '.' && $userpresetdir != '..' && is_directory_a_preset($CFG->dirroot.$fulluserpresetdir)) {
                    $preset = new StdClass;
                    $preset->path = $fulluserpresetdir;
                    $preset->name = $userpresetdir;
                    $preset->user = $userdir;
                    if (file_exists($fulluserpresetdir.'/screenshot.jpg')) {
                        $preset->screenshot = $CFG->wwwroot.'/mod/data/preset/'.$userdir.'/'.$userpresetdir.'/screenshot.jpg';
                    }
                    $presets[] = $preset;
                    unset($preset);
                }
            }
        }
    }
    closedir($presetdir);
}

/* Need sesskey security check here for import instruction */
$sesskey = sesskey();

/********************************************************************/
/* Output */
data_presets_print_header($course, $cm, $data);

echo "<center>";
switch ($action) {
    /* Main selection menu - default mode also. */
    default:
    case 'base':
        $strimport = get_string('import');
        $strfromfile = get_string('fromfile', 'data');
        $strchooseorupload = get_string('chooseorupload', 'data');
        $strok = get_string('ok');
        $strusestandard = get_string('usestandard', 'data');
        $strchoose = get_string('choose');
        $strexport = get_string('export', 'data');
        $strexportaszip = get_string('exportaszip', 'data');
        $strsaveaspreset = get_string('saveaspreset', 'data');
        $strdelete = get_string('delete');

        echo "<table cellpadding=7>";
        echo "<tr><td><h3>$strimport</h3></td>";
        echo "<td><form name='form' method='POST' action='?d=$data->id&action=importzip&sesskey=$sesskey' enctype='multipart/form-data'>";
        helpbutton('importfromfile', '', 'data');
        echo " $strfromfile:</td><td><input name=\"file\" size=\"20\" value=\"\" alt=\"file\" type=\"text\"><input name=\"coursefiles\" title=\"Choose or upload a file\" value=\"$strchooseorupload\" onclick=\"return openpopup('/files/index.php?id=2&choose=form.file', 'coursefiles', 'menubar=0,location=0,scrollbars,resizable,width=750,height=500', 0);\" type=\"button\">";
        echo "<input type=\"submit\" value=\"$strok\"/>";
        echo "</form></td></tr>";

        echo "<tr valign=top><td></td><td>";
        helpbutton('usepreset', '', 'data');
        echo " $strusestandard: </td><td>";
        echo "<table width=100%>";
        foreach ($presets as $id => $preset) {
            echo "<tr><form action='' method='POST'>";
            echo "<input type='hidden' name='file' value=\"$preset->path\">";
            echo "<input type='hidden' name='action' value='beginimport'>";
            echo "<input type='hidden' name='d' value='$data->id'>";
            echo "<input type='hidden' name='sesskey' value='$sesskey'>";
            echo "<td>";
            if ($preset->screenshot) {
                echo "<img src='$preset->screenshot' alt='$preset->screenshot' />";
            }
            echo "</td><td>$preset->name";
            if ($preset->user) {
                $user = get_record('user', 'id', $preset->user);
                echo " by $user->firstname $user->lastname";
            }
            echo "</td><td><input type='submit' value='$strchoose'></td></form>";
            echo "<td>";
            if ($preset->user == $USER->id || isadmin()) {
                echo "<form action='' method='POST'>";
                echo "<input type='hidden' name='d' value='$data->id' />";
                echo "<input type='hidden' name='action' value='confirmdelete' />";
                echo "<input type='hidden' name='sesskey' value='$sesskey' />";
                echo "<input type='hidden' name='deleteid' value='$id' />";
                echo "<input type='hidden' name='deletename' value=\"$preset->name\" />";
                echo "<input type='submit' value='$strdelete' /></form>";
            }
            echo "</td></tr>";
        }
        echo "</table></td></tr>";

        echo "<tr><td valign=top><h3>$strexport</h3></td>";
        echo "<td><form action='' method='POST'>";
        helpbutton('exportzip', '', 'data');
        echo " <input type='hidden' name='action' value='export' />";
        echo "<input type='hidden' name='d' value='$data->id' />";
        echo "<input type='submit' value='$strexportaszip' />";
        echo "</form>";

        echo "<form action='' method='POST'>";
        helpbutton('savepreset', '', 'data');     
        echo " <input type='hidden' name='action' value='save1' />";
        echo "<input type='hidden' name='d' value='$data->id' />";
        echo "<input type='hidden' name='sesskey' value='$sesskey' />";
        echo "<input type='submit' value='$strsaveaspreset' />";
        echo "</form>";

        echo "</table>";
        break;



        /***************** Deleting *****************/
    case 'confirmdelete' :
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        $deletename = required_param('deletename', PARAM_RAW);
        $deleteid = required_param('deleteid', PARAM_INT);

        $strwarning = get_string('deletewarning', 'data');
        $strdelete = get_string('delete');
        notify($strwarning);
        echo "<form action='' method='POST'>";
        echo "<input type='hidden' name='d' value='$data->id' />";
        echo "<input type='hidden' name='action' value='delete' />";
        echo "<input type='hidden' name='sesskey' value='$sesskey' />";
        echo "<input type='hidden' name='deleteid' value='$deleteid' />";
        echo "<input type='hidden' name='deletename' value=\"$deletename\" />";
        echo "<input type='submit' value='$strdelete' /></form>";
        break;

    case 'delete' :
        if (!confirm_sesskey()) {
            error('Sesskey Invalid');
        }

        $deletename = required_param('deletename', PARAM_RAW);
        $deleteid = required_param('deleteid', PARAM_INT);

        if (!empty($presets[$deleteid])) {
            if ($presets[$deleteid]->name == $deletename) {
                if (!clean_preset($CFG->dirroot.$presets[$deleteid]->path)) error("Error deleting");
            }
            rmdir($CFG->dirroot.$presets[$deleteid]->path);
        }
        else {
            error('Invalid delete');
        }

        $strdelete = get_string('deleted', 'data');
        notify("$deletename $strdeleted");

        break;



        /***************** Importing *****************/
    case 'beginimport' :
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        $pimporter = new PresetImporter($course, $cm, $data, $file);
        $pimporter->import_options();
        break;

        /* Imports a zip file. */
    case 'importzip' :
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        if (!unzip_file($CFG->dataroot."/$course->id/$file", $CFG->dataroot."/temp/data/".$USER->id, false)) 
            error("Can't unzip file");
        $presetfile = $CFG->dataroot."/temp/data/".$USER->id;

        $pimporter = new PresetImporter($course, $cm, $data, $presetfile);
        $pimporter->import_options();
        break;

    case 'finishimport':
        if (!confirm_sesskey()) {
            error('Sesskey Invalid');
        }

        $pimporter = new PresetImporter($course, $cm, $data, $file);
        $pimporter->import();

        $strimportsuccess = get_string('importsuccess', 'data');
        $straddentries = get_string('addentries', 'data');
        $strtodatabase = get_string('todatabase', 'data');
        if (!get_records('data_records', 'dataid', $data->id)) {
            notify("$strimportsuccess <a href='edit.php?d=$data->id'>$straddentries</a> $strtodatabase", 'notifysuccess');
        }
        else {
            notify("$strimportsuccess", 'notifysuccess');
        }
        break;

        /* Exports as a zip file ready for download. */
    case 'export':
        $file = data_presets_export($course, $cm, $data);
        echo get_string('exportedtozip', 'data')."<br>";
        $perminantfile = $CFG->dataroot."/$course->id/moddata/data/$data->id/preset.zip";
        @unlink($perminantfile);
        /* is this created elsewhere? sometimes its not present... */
        make_upload_directory("$course->id/moddata/data/$data->id");

        /* now just move the zip into this folder to allow a nice download */
        if (!rename($file, $perminantfile)) error("Can't move zip");
        echo "<a href='$CFG->wwwroot/file.php/$course->id/moddata/data/$data->id/preset.zip'>".get_string('download', 'data')."</a>";
        break;



        /***************** Exporting *****************/
    case 'save1':
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        $strcontinue = get_string('continue');
        $strwarning = get_string('presetwarning', 'data');

        echo "<div align=center>";
        echo "<p>$strwarning</p>";
        echo "<form action='' method='POST'>";
        echo "Name: <input type='textbox' name='name' value=\"$data->name\" />";
        echo "<input type='hidden' name='action' value='save2' />";
        echo "<input type='hidden' name='d' value='$data->id' />";
        echo "<input type='hidden' name='sesskey' value='$sesskey' />";
        echo "<input type='submit' value='$strcontinue' /></form></div>";
        break;

    case 'save2':
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        $strcontinue = get_string('continue');
        $stroverwrite = get_string('overwrite');

        $name = optional_param('name', $data->name, PARAM_FILE);

        if (is_directory_a_preset("$CFG->dirroot/mod/data/preset/$USER->id/$name")) {
            notify("Preset already exists: Pick another name or overwrite");

            echo "<div align=center>";
            echo "<form action='' method='POST'>";
            echo "New name: <input type='textbox' name='name' value=\"$name\" />";
            echo "<input type='hidden' name='action' value='save2' />";
            echo "<input type='hidden' name='d' value='$data->id' />";
            echo "<input type='hidden' name='sesskey' value='$sesskey' />";
            echo "<input type='submit' value='$strcontinue' /></form>";

            echo "<form action='' method='POST'>";
            echo "<input type='hidden' name='name' value=\"$name\" />";
            echo "<input type='hidden' name='action' value='save3' />";
            echo "<input type='hidden' name='d' value='$data->id' />";
            echo "<input type='hidden' name='sesskey' value='$sesskey' />";
            echo "<input type='submit' value='$stroverwrite' /></form>";
            echo "</div>";
            break;
        }

    case 'save3':
        if (!confirm_sesskey()) {
            error("Sesskey Invalid");
        }

        $name = optional_param('name', $data->name, PARAM_FILE);
        $presetdirectory = "$CFG->dirroot/mod/data/preset/$USER->id/$name";

        if (!is_dir($presetdirectory)) {
            @mkdir("$CFG->dirroot/mod/data/preset/$USER->id");
            mkdir($presetdirectory);
        }
        else {
            clean_preset($presetdirectory);
        }

        $file = data_presets_export($course, $cm, $data);
        if (!unzip_file($file, $presetdirectory, false)) error("Can't unzip to the preset directory");
        notify(get_string('savesuccess', 'data'), 'notifysuccess');
        break;

}
echo "</center>";
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
            file_exists($directory.'preset.xml')) return true;
    else return false;
}

function data_presets_print_header($course, $cm, $data, $showtabs=true) {

    global $CFG, $displaynoticegood, $displaynoticebad;

    $strdata = get_string('modulenameplural','data');

    print_header_simple($data->name, '', "<a href='index.php?id=$course->id'>$strdata</a> -> $data->name", 
            '', '', true, '', navmenu($course, $cm));

    print_heading(format_string($data->name));

    /// Print the tabs

    if ($showtabs) {
        $currenttab = 'presets';
        include_once('tabs.php');
    }

    /// Print any notices

    if (!empty($displaynoticegood)) {
        notify($displaynoticegood, 'notifysuccess');    // good (usually green)
    } else if (!empty($displaynoticebad)) {
        notify($displaynoticebad);                     // bad (usuually red)
    }
}


function clean_preset($folder) {
    if (unlink($folder.'/singletemplate.html') &&
            unlink($folder.'/listtemplate.html') &&
            unlink($folder.'/listtemplateheader.html') &&
            unlink($folder.'/listtemplatefooter.html') &&
            unlink($folder.'/addtemplate.html') &&
            unlink($folder.'/rsstemplate.html') &&
            unlink($folder.'/rsstitletemplate.html') &&
            unlink($folder.'/csstemplate.css') &&
            unlink($folder.'/preset.xml')) return true;
    else return false;
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

    fwrite($singletemplate, $data->singletemplate);
    fwrite($listtemplate, $data->listtemplate);
    fwrite($listtemplateheader, $data->listtemplateheader);
    fwrite($listtemplatefooter, $data->listtemplatefooter);
    fwrite($addtemplate, $data->addtemplate);
    fwrite($rsstemplate, $data->rsstemplate);
    fwrite($rsstitletemplate, $data->rsstitletemplate);
    fwrite($csstemplate, $data->csstemplate);

    fclose($singletemplate);
    fclose($listtemplate);
    fclose($listtemplateheader);
    fclose($listtemplatefooter);
    fclose($addtemplate);
    fclose($rsstemplate);
    fclose($rsstitletemplate);
    fclose($csstemplate);

    /* All the display data is now done. Now assemble preset.xml */
    $fields = get_records('data_fields', 'dataid', $data->id);
    $presetfile = fopen($tempfolder.'/preset.xml', 'w');
    $presetxml = "<preset>\n\n";

     /* Database settings first. Name not included? */
     $settingssaved = array('intro', 'comments', 'ratings', 'participants',
                            'requiredentries', 'requiredentriestoview', 'maxentries',
                            'rssarticles', 'approval', 'scale', 'assessed', 'assessedpublic',
                            'defaultsort', 'defaultsortdir', 'editany');

     $presetxml .= "<settings>\n";
     foreach ($settingssaved as $setting) {
         $presetxml .= "<$setting>{$data->$setting}</$setting>\n";
     }
     $presetxml .= "</settings>\n\n";

    /* Now for the fields. Grabs all settings that are non-empty */
    foreach ($fields as $field) {
        $presetxml .= "<field>\n";
        foreach ($field as $key => $value) {
            if ($value != '' && $key != 'id' && $key != 'dataid') {
                $presetxml .= "<$key>$value</$key>\n";
            }
        }
        $presetxml .= "</field>\n\n";
    }

    $presetxml .= "</preset>";
    fwrite($presetfile, $presetxml);
    fclose($presetfile);

    /* Check all is well */
    if (!is_directory_a_preset($tempfolder)) {
        error("Not all files generated!");
    }

    $filelist = array(
                      "singletemplate.html",
                      "listtemplate.html",
                      "listtemplateheader.html",
                      "listtemplatefooter.html",
                      "addtemplate.html",
                      "rsstemplate.html",
                      "rsstitletemplate.html",
                      "csstemplate.css",
                      "preset.xml");

    foreach ($filelist as $key => $file) {
        $filelist[$key] = $tempfolder.'/'.$filelist[$key];
    }

    @unlink($tempfolder.'/export.zip');
    $status = zip_files($filelist, $tempfolder.'/export.zip');

    /* made the zip... now return the filename for storage.*/
    return $tempfolder.'/export.zip';
}


function data_presets_import_zip($course, $cm, $data, $file) {
    global $CFG;

    /*
     * Now need to move file to temp directory for unzipping.
     */
    $tempfolder = $CFG->dataroot.'/temp';

    if (!file_exists($file)) {
        error('No such file '.$file);
    }

    if (file_exists($tempfolder.'/template.zip')) {
        unlink($tempfolder.'/template.zip');
    }

    if (!copy($file, $tempfolder.'/template.zip')) {
        error("Can't copy file");
    }

    /* Unzip. */
    clean_preset($tempfolder);
    if (!unzip_file($tempfolder.'/template.zip', '', false)) {
        error("Can't unzip file");
    }

    return data_presets_import_options($course, $cm, $data, $tempfolder);
}

class PresetImporter {   
    function PresetImporter($course, $cm, $data, $folder) {
        global $CFG;
        $this->course = $course;
        $this->cm = $cm;
        $this->data = $data;
        $this->folder = $CFG->dirroot.$folder;
        $this->postfolder = $folder;
    }


    function get_settings() {
        global $CFG;

        if (!is_directory_a_preset($this->folder)) {
            error("$this->folder Not a preset");
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
        $strcontinue = get_string("continue");
        $sesskey = sesskey();
        $strwarning = get_string('mappingwarning', 'data');
        $strfieldmappings = get_string('fieldmappings', 'data');
        $strnew = get_string("new");
        $strold = get_string("old");

        list($settings, $newfields,  $currentfields) = $this->get_settings();

        echo "<div align='center'><form action='' method='POST'>";
        echo "<input type='hidden' name='sesskey' value='$sesskey' />";
        echo "<input type='hidden' name='d' value='{$this->data->id}' />";
        echo "<input type='hidden' name='action' value='finishimport' />";
        echo "<input type='hidden' name='file' value=\"$this->postfolder\" />";

        if ($currentfields != array() && $newfields != array()) {
            echo "<h3>$strfieldmappings ";
            echo helpbutton('fieldmappings', '', 'data');
            echo "</h3><table>";

            foreach ($newfields as $nid => $newfield) {
                echo "<tr><td>$newfield->name </td>";
                echo "<td><select name='field_$nid'>";

                foreach ($currentfields as $cid => $currentfield) {
                    if ($currentfield->type == $newfield->type) {
                        if ($currentfield->name == $newfield->name) {
                            echo "<option value='$cid' selected='true'>$currentfield->name</option>";
                            $selected=true;
                        }
                        echo "<option value='$cid'>$currentfield->name</option>";
                    }
                }

                if ($selected)
                    echo "<option value='-1'>-</option>";
                else
                    echo "<option value='-1' selected='true'>-</option>";
                echo "</select></td></tr>";
            }
            echo "</table>";
            echo "<p>$strwarning</p>";
        }
        else if ($newfields == array()) {
            error("New preset has no defined fields!");
        }
        echo "<input type='submit' value='$strcontinue' /></form></div>";

    }

    function import() {
        global $CFG;

        list($settings, $newfields, $currentfields) = $this->get_settings();

        $preservedfields = array();

        /* Maps fields and makes new ones */
        if ($newfields != array()) {
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

                    $classname = 'data_field_'.$newfield->type;
                    $fieldclass = new $classname($newfield, $this->data);
                    $fieldclass->insert_field();
                    unset($fieldclass);
                }
            }
        }

        /* Get rid of all old unused data */
        if ($preservedfields != array()) {
            foreach ($currentfields as $cid => $currentfield) {
                if (!array_key_exists($cid, $preservedfields)) {
                    /* Data not used anymore so wipe! */
                    print "Deleting field $currentfield->name<br>";
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

        data_update_instance($settings);

        if (strstr($this->folder, "/temp/")) clean_preset($this->folder); /* Removes the temporary files */
        return true;
    }
}

?>
