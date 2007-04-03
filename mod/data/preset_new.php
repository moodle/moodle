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
require_once('preset_class.php');

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

$preset = new Data_Preset($shortname, $data->id, null, $userid);
echo $preset->process_action($action, compact('shortname', 'fullname', 'data', 'userid', 'file', 'course', 'sesskey'));

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
    if (!empty($preset->user_id)) {
        $user = get_record('user', 'id', $preset->user_id);
        $desc = $preset->name.' ('.fullname($user, true).')';
    } else {
        $desc = $preset->name;
    }

    if (!empty($preset->screenshot)) {
        $screenshot = '<img width="150" class="presetscreenshot" src="'.$preset->screenshot.'" alt="'.get_string('screenshot').' '.$desc.'" />&nbsp;';
    }

    $fullname = $preset->user_id.'/'.$preset->shortname;

    $dellink = '';
    if ($preset->user_id > 0 and ($preset->user_id == $USER->id || has_capability('mod/data:manageuserpresets', $context))) {
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


?>
