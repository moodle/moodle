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
 * Preset Menu
 *
 * This is the page that is the menu item in the config database
 * pages.
 *
 * This file is part of the Database module for Moodle
 *
 * @copyright 2005 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package mod-data
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

$url = new moodle_url('/mod/data/preset.php');
if ($action !== 'base') {
    $url->param('action', $action);
}
if ($fullname !== '') {
    $url->param('fullname', $fullname);
}
if ($file !== '') {
    $url->param('file', $file);
}

// find out preset owner userid and shortname
$parts = explode('/', $fullname);
$userid = empty($parts[0]) ? 0 : (int)$parts[0];
$shortname = empty($parts[1]) ? '' : $parts[1];
unset($parts);
unset($fullname);

if ($id) {
    $url->param('id', $id);
    $PAGE->set_url($url);
    if (! $cm = get_coursemodule_from_id('data', $id)) {
        print_error('invalidcoursemodule');
    }
    if (! $course = $DB->get_record('course', array('id'=>$cm->course))) {
        print_error('coursemisconf');
    }
    if (! $data = $DB->get_record('data', array('id'=>$cm->instance))) {
        print_error('invalidid', 'data');
    }
} else if ($d) {
    $url->param('d', $d);
    $PAGE->set_url($url);
    if (! $data = $DB->get_record('data', array('id'=>$d))) {
        print_error('invalidid', 'data');
    }
    if (! $course = $DB->get_record('course', array('id'=>$data->course))) {
        print_error('coursemisconf');
    }
    if (! $cm = get_coursemodule_from_instance('data', $data->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
} else {
    print_error('missingparameter');
}

// fill in missing properties needed for updating of instance
$data->course     = $cm->course;
$data->cmidnumber = $cm->idnumber;
$data->instance   = $cm->instance;

if (!$context = get_context_instance(CONTEXT_MODULE, $cm->id)) {
    print_error('cannotfindcontext');
}

require_login($course->id, false, $cm);

require_capability('mod/data:managetemplates', $context);

if ($userid && ($userid != $USER->id) && !has_capability('mod/data:viewalluserpresets', $context)) {
    print_error('cannotaccesspresentsother', 'data');
}

/* Need sesskey security check here for import instruction */
$sesskey = sesskey();

$PAGE->navbar->add(get_string('presets', 'data'));

/********************************************************************/
/* Output */
if ($action !== 'export') {
    data_print_header($course, $cm, $data, 'presets');
}

switch ($action) {
        /***************** Deleting *****************/
    case 'confirmdelete' :
        if (!confirm_sesskey()) { // GET request ok here
            print_error('invalidsesskey');
        }

        if ($userid > 0 and ($userid == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
           //ok can delete
        } else {
            print_error('invalidrequest');
        }

        $path = data_preset_path($course, $userid, $shortname);

        $strwarning = get_string('deletewarning', 'data').'<br />'.
                      data_preset_name($shortname, $path);

        $optionsyes = array('fullname' => $userid.'/'.$shortname,
                         'action' => 'delete',
                         'd' => $data->id);

        $optionsno = array('d' => $data->id);
        echo $OUTPUT->confirm($strwarning, new moodle_url('preset.php', $optionsyes), new moodle_url('preset.php', $optionsno));
        echo $OUTPUT->footer();
        exit(0);
        break;

    case 'delete' :
        if (!data_submitted() and !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        if ($userid > 0 and ($userid == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
           //ok can delete
        } else {
            print_error('invalidrequest');
        }

        $presetpath = data_preset_path($course, $userid, $shortname);

        if (!clean_preset($presetpath)) {
            print_error('cannotdeletepreset', 'data');
        }
        @rmdir($presetpath);

        $strdeleted = get_string('deleted', 'data');
        echo $OUTPUT->notification("$shortname $strdeleted", 'notifysuccess');
        break;

        /***************** Importing *****************/
    case 'importpreset' :
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        $pimporter = new PresetImporter($course, $cm, $data, $userid, $shortname);
        $pimporter->import_options();

        echo $OUTPUT->footer();
        exit(0);
        break;

        /* Imports a zip file. */
    case 'importzip' :
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        if (!make_upload_directory('temp/data/'.$USER->id)) {
            print_error('nopermissiontomkdir');
        }

        $presetfile = $CFG->dataroot.'/temp/data/'.$USER->id;
        clean_preset($presetfile);

        if (!unzip_file($CFG->dataroot."/$course->id/$file", $presetfile, false)) {
            print_error('cannotunzipfile');
        }

        $pimporter = new PresetImporter($course, $cm, $data, -$USER->id, $shortname);
        $pimporter->import_options();

        echo $OUTPUT->footer();
        exit(0);
        break;

    case 'finishimport':
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        $pimporter = new PresetImporter($course, $cm, $data, $userid, $shortname);
        $pimporter->import();

        $strimportsuccess = get_string('importsuccess', 'data');
        $straddentries = get_string('addentries', 'data');
        $strtodatabase = get_string('todatabase', 'data');
        if (!$DB->get_records('data_records', array('dataid'=>$data->id))) {
            echo $OUTPUT->notification("$strimportsuccess <a href='edit.php?d=$data->id'>$straddentries</a> $strtodatabase", 'notifysuccess');
        } else {
            echo $OUTPUT->notification("$strimportsuccess", 'notifysuccess');
        }
        break;

        /* Exports as a zip file ready for download. */
    case 'export':
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
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
            print_error('invalidrequest');
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
        echo $OUTPUT->footer();
        exit(0);
        break;

    case 'save2':
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        $strcontinue = get_string('continue');
        $stroverwrite = get_string('overwrite', 'data');
        $strname = get_string('shortname');

        $name = optional_param('name', $data->name, PARAM_FILE);

        if (is_directory_a_preset("$CFG->dataroot/data/preset/$USER->id/$name")) {
            echo $OUTPUT->notification("Preset already exists: Pick another name or overwrite");

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
            echo $OUTPUT->footer();
            exit(0);
            break;
        }

    case 'save3':
        if (!data_submitted() or !confirm_sesskey()) {
            print_error('invalidrequest');
        }

        $name = optional_param('name', $data->name, PARAM_FILE);
        $presetdirectory = "/data/preset/$USER->id/$name";

        make_upload_directory($presetdirectory);
        clean_preset($CFG->dataroot.$presetdirectory);

        $file = data_presets_export($course, $cm, $data);
        if (!unzip_file($file, $CFG->dataroot.$presetdirectory, false)) {
            print_error('cannotunziptopreset', 'data');
        }
        echo $OUTPUT->notification(get_string('savesuccess', 'data'), 'notifysuccess');
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
echo $OUTPUT->old_help_icon('exportzip', get_string('help'), 'data', false);
echo '</td><td>';
$options = array();
$options['sesskey'] = sesskey();
$options['action']  = 'export';
$options['d']       = $data->id;
echo $OUTPUT->single_button(new moodle_url('preset.php', $options), $strexport);
echo '</td></tr>';

echo '<tr><td><label>'.$strsaveaspreset.'</label>';
echo $OUTPUT->old_help_icon('savepreset', get_string('help'), 'data', false);
echo '</td><td>';
$options = array();
$options['sesskey'] = sesskey();
$options['action']  = 'save1';
$options['d']       = $data->id;
echo $OUTPUT->single_button(new moodle_url('preset.php', $options), $strsave);
echo '</td></tr>';
echo '<tr><td valign="top" colspan="2" align="center"><h3>'.$strimport.'</h3></td></tr>';
echo '<tr><td><label for="fromfile">'.$strfromfile.'</label>';
echo $OUTPUT->old_help_icon('importfromfile', get_string('help'), 'data', true);
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
echo $OUTPUT->old_help_icon('usepreset', get_string('help'), 'data', true);
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
        $user = $DB->get_record('user', array('id'=>$preset->userid));
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
                   '<img src="'.$OUTPUT->pix_url('t/delete') . '" class="iconsmall" alt="'.$strdelete.' '.$desc.'" /></a>';
    }

    echo '<input type="radio" name="fullname" id="usepreset'.$i.'" value="'.$fullname.'" /><label for="usepreset'.$i++.'">'.$desc.'</label>'.$dellink.'<br />';
}
echo '<br />';
echo '<input type="submit" value="'.$strchoose.'" />';
echo '</fieldset></form>';
echo '</td></tr>';
echo '</table>';
echo '</div>';

echo $OUTPUT->footer();
exit(0);

################################################################################


function data_presets_export($course, $cm, $data) {
    global $CFG, $DB;
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


