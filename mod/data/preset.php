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
$file     = optional_param('file', '', PARAM_PATH); // uploaded file

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

// fill in missing properties needed for updating of instance
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

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
if ($action !== 'export') {
    data_print_header($course, $cm, $data, 'presets');
}

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
        exit(0);
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
        exit(0);
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
        exit(0);
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
        $exportfile = data_presets_export($course, $cm, $data);
        $exportfilename = basename($exportfile);
        header("Content-Type: application/download\n");
        header("Content-Disposition: attachment; filename=$exportfilename");
        header('Expires: 0');
        header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
        header('Pragma: public');
        $exportfilehandler = fopen($exportfile, 'rb');
        print fread($exportfilehandler, filesize($exportfile));
        fclose($exportfilehandler);
        unlink($exportfile);
        exit(0);
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
        exit(0);
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
            exit(0);
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
helpbutton('exportzip', '', 'data', true, true);
echo '</td><td>';
$options = new object();
$options->action = 'export';
$options->d = $data->id;
$options->sesskey = sesskey();
print_single_button('preset.php', $options, $strexport, 'post');
echo '</td></tr>';

echo '<tr><td><label>'.$strsaveaspreset.'</label>';
helpbutton('savepreset', '', 'data', true, true);
echo '</td><td>';
$options = new object();
$options->action = 'save1';
$options->d = $data->id;
$options->sesskey = sesskey();
print_single_button('preset.php', $options, $strsave, 'post');
echo '</td></tr>';
echo '<tr><td valign="top" colspan="2" align="center"><h3>'.$strimport.'</h3></td></tr>';
echo '<tr><td><label for="fromfile">'.$strfromfile.'</label>';
helpbutton('importfromfile', '', 'data', true, true);
echo '</td><td>';
echo '<form id="uploadpreset" method="post" action="preset.php">';
echo '<fieldset class="invisiblefieldset">';
echo '<input type="hidden" name="d" value="'.$data->id.'" />';
echo '<input type="hidden" name="action" value="importzip" />';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<input name="file" size="20" value="" id="fromfile" type="text" /><input name="coursefiles" value="'.$strchooseorupload.'" onclick="return openpopup('."'/files/index.php?id={$course->id}&amp;choose=uploadpreset.file', 'coursefiles', 'menubar=0,location=0,scrollbars,resizable,width=750,height=500', 0".');" type="button" />';
echo '<input type="submit" value="'.$strimport.'" />';
echo '</fieldset></form>';
echo '</td></tr>';

echo '<tr valign="top"><td><label>'.$strusestandard.'</label>';
helpbutton('usepreset', '', 'data', true, true);
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
exit(0);

################################################################################


function data_presets_export($course, $cm, $data) {
    global $CFG;
    $presetname = clean_filename($data->name) . '-preset-' . gmdate("Ymd_Hi");
    $exportsubdir = "$course->id/moddata/data/$data->id/$presetname";
    make_upload_directory($exportsubdir);
    $exportdir = "$CFG->dataroot/$exportsubdir";

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
    $fields = get_records('data_fields', 'dataid', $data->id);
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
        error('Not all files generated!');
        // should be migrated to print_error()
    }

    $filelist = array(
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

    foreach ($filelist as $key => $file) {
        $filelist[$key] = $exportdir . '/' . $filelist[$key];
    }

    $exportfile = "$CFG->dataroot/$course->id/moddata/data/$data->id/$presetname.zip";
    file_exists($exportfile) && unlink($exportfile);
    $status = zip_files($filelist, $exportfile);
    // ToDo: status check
    foreach ($filelist as $file) {
        unlink($file);
    }
    rmdir($exportdir);

    // Return the full path to the exported preset file:
    return $exportfile;
}

?>
