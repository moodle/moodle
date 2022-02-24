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
 * Fetches language packages from download.moodle.org server
 *
 * Language packages are available at https://download.moodle.org/langpack/
 * in ZIP format together with a file languages.md5 containing their hashes
 * and meta info.
 * Locally, language packs are saved into $CFG->dataroot/lang/
 *
 * @package    tool
 * @subpackage langimport
 * @copyright  2005 Yu Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('toollangimport');

if (empty($CFG->langotherroot)) {
    throw new moodle_exception('missingcfglangotherroot', 'tool_langimport');
}

$mode               = optional_param('mode', 0, PARAM_INT);              // action
$pack               = optional_param_array('pack', array(), PARAM_SAFEDIR);    // pack to install
$uninstalllang      = optional_param_array('uninstalllang', array(), PARAM_LANG);// installed pack to uninstall
$confirmtounistall  = optional_param('confirmtouninstall', '', PARAM_SAFEPATH);  // uninstallation confirmation
$purgecaches        = optional_param('purgecaches', false, PARAM_BOOL);  // explicit caches reset

if ($purgecaches) {
    require_sesskey();
    get_string_manager()->reset_caches();
    redirect($PAGE->url);
}

if (!empty($CFG->skiplangupgrade)) {
    echo $OUTPUT->header();
    echo $OUTPUT->box(get_string('langimportdisabled', 'tool_langimport'));
    echo $OUTPUT->single_button(new moodle_url($PAGE->url, array('purgecaches' => 1)), get_string('purgestringcaches', 'tool_langimport'));
    echo $OUTPUT->footer();
    die;
}

define('INSTALLATION_OF_SELECTED_LANG', 2);
define('DELETION_OF_SELECTED_LANG', 4);
define('UPDATE_ALL_LANG', 5);

get_string_manager()->reset_caches();

$controller = new tool_langimport\controller();

if (($mode == INSTALLATION_OF_SELECTED_LANG) and confirm_sesskey() and !empty($pack)) {
    if (is_array($pack) && count($pack) > 1) {
        // Installing multiple languages can take a while - perform it asynchronously in the background.
        $controller->schedule_languagepacks_installation($pack);

    } else {
        // Single language pack to be installed synchronously. It should be reasonably quick and can be used for debugging, too.
        core_php_time_limit::raise();
        $controller->install_languagepacks($pack);
    }
}

if ($mode == DELETION_OF_SELECTED_LANG and (!empty($uninstalllang) or !empty($confirmtounistall))) {
    // Actually deleting languages, languages to delete are passed as GET parameter as string
    // ...need to populate them to array.
    if (empty($uninstalllang)) {
        $uninstalllang = explode('/', $confirmtounistall);
    }

    if (in_array('en', $uninstalllang)) {
        // TODO.
        $controller->errors[] = get_string('noenglishuninstall', 'tool_langimport');

    } else if (empty($confirmtounistall) and confirm_sesskey()) { // User chose langs to be deleted, show confirmation.
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('uninstallconfirm', 'tool_langimport', implode(', ', $uninstalllang)),
            new moodle_url($PAGE->url, array(
                'mode' => DELETION_OF_SELECTED_LANG,
                'confirmtouninstall' => implode('/', $uninstalllang),
            )), $PAGE->url);
        echo $OUTPUT->footer();
        die;

    } else if (confirm_sesskey()) {   // Deleting languages.
        foreach ($uninstalllang as $ulang) {
            $controller->uninstall_language($ulang);
        }

    }
}

if ($mode == UPDATE_ALL_LANG) {
    core_php_time_limit::raise();
    $controller->update_all_installed_languages();
}
get_string_manager()->reset_caches();

$PAGE->set_primary_active_tab('siteadminnode');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('langimport', 'tool_langimport'));

$installedlangs = get_string_manager()->get_list_of_translations(true);
$locale = new \tool_langimport\locale();


if ($availablelangs = $controller->availablelangs) {
    $remote = true;
} else {
    $remote = false;
    $availablelangs = array();
    $a = [
        'src' => $controller->lang_pack_url(),
        'dest' => $CFG->dataroot.'/lang/',
    ];
    $errormessage = get_string('downloadnotavailable', 'tool_langimport', $a);
    \core\notification::error($errormessage);
}

$missinglocales = '';
$missingparents = array();
foreach ($installedlangs as $installedlang => $langpackname) {
    // Check locale availability.
    if (!$locale->check_locale_availability($installedlang)) {
        $missinglocales .= '<li>'.$langpackname.'</li>';
    }

    // This aligns the name of the language to match the available languages using
    // both the name for the language and the localized name for the language.
    $alang = array_filter($availablelangs, function($k) use ($installedlang) {
        return $k[0] == $installedlang;
    });
    $alang = array_pop($alang);
    if (!empty($alang[0]) and trim($alang[0]) !== 'en') {
        $installedlangs[$installedlang] = $alang[2] . ' &lrm;(' . $alang[0] . ')&lrm;';
    }

    $parent = get_parent_language($installedlang);
    if (empty($parent)) {
        continue;
    }
    if (!isset($installedlangs[$parent])) {
        $missingparents[$installedlang] = $parent;
    }
}

if (!empty($missinglocales)) {
    // There is at least one missing locale.
    $a = new stdClass();
    $a->globallocale = moodle_getlocale();
    $a->missinglocales = $missinglocales;
    $controller->errors[] = get_string('langunsupported', 'tool_langimport', $a);
}

if ($controller->info) {
    $info = implode('<br />', $controller->info);
    \core\notification::success($info);
}

if ($controller->errors) {
    $info = implode('<br />', $controller->errors);
    \core\notification::error($info);
}

// Inform about pending language packs installations.
foreach (\core\task\manager::get_adhoc_tasks('\tool_langimport\task\install_langpacks') as $installtask) {
    $installtaskdata = $installtask->get_custom_data();

    if (!empty($installtaskdata->langs)) {
        \core\notification::info(get_string('installpending', 'tool_langimport', implode(', ', $installtaskdata->langs)));
    }
}

if ($missingparents) {
    foreach ($missingparents as $l => $parent) {
        $a = new stdClass();
        $a->lang   = $installedlangs[$l];
        $a->parent = $parent;
        foreach ($availablelangs as $alang) {
            if ($alang[0] == $parent) {
                $shortlang = $alang[0];
                $a->parent = $alang[2].' ('.$shortlang.')';
            }
        }
        $info = get_string('missinglangparent', 'tool_langimport', $a);
        \core\notification::error($info);
    }
}

$uninstallurl = new moodle_url('/admin/tool/langimport/index.php', array('mode' => DELETION_OF_SELECTED_LANG));
$updateurl = null;
if ($remote) {
    $updateurl = new moodle_url('/admin/tool/langimport/index.php', array('mode' => UPDATE_ALL_LANG));
}
$installurl = new moodle_url('/admin/tool/langimport/index.php', array('mode' => INSTALLATION_OF_SELECTED_LANG));

// List of available languages.
$options = array();
foreach ($availablelangs as $alang) {
    if (!empty($alang[0]) and trim($alang[0]) !== 'en' and !$controller->is_installed_lang($alang[0], $alang[1])) {
        $options[$alang[0]] = $alang[2].' &lrm;('.$alang[0].')&lrm;';
    }
}

$renderable = new \tool_langimport\output\langimport_page($installedlangs, $options, $uninstallurl, $updateurl, $installurl);
$output = $PAGE->get_renderer('tool_langimport');
echo $output->render($renderable);

$PAGE->requires->strings_for_js(array('uninstallconfirm', 'uninstall', 'selectlangs', 'noenglishuninstall'),
                                'tool_langimport');
$PAGE->requires->yui_module('moodle-core-languninstallconfirm',
                            'Y.M.core.languninstallconfirm.init',
                             array(array('uninstallUrl' => $uninstallurl->out()))
                            );
echo $OUTPUT->footer();
