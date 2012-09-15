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
 * Language packages are available at http://download.moodle.org/langpack/
 * in ZIP format together with a file languages.md5 containing their hashes
 * and meta info.
 * Locally, language packs are saved into $CFG->dataroot/lang/
 *
 * @package    tool
 * @subpackage langimport
 * @copyright  2005 Yu Zhang
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/componentlib.class.php');

admin_externalpage_setup('toollangimport');

if (empty($CFG->langotherroot)) {
    throw new moodle_exception('missingcfglangotherroot', 'tool_langimport');
}

$mode          = optional_param('mode', 0, PARAM_INT);              // action
$pack          = optional_param_array('pack', array(), PARAM_SAFEDIR);    // pack to install
$uninstalllang = optional_param('uninstalllang', '', PARAM_LANG);   // installed pack to uninstall
$confirm       = optional_param('confirm', 0, PARAM_BOOL);          // uninstallation confirmation
$purgecaches   = optional_param('purgecaches', false, PARAM_BOOL);  // explicit caches reset

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

$notice_ok    = array();
$notice_error = array();

if (($mode == INSTALLATION_OF_SELECTED_LANG) and confirm_sesskey() and !empty($pack)) {
    set_time_limit(0);
    make_temp_directory('');
    make_upload_directory('lang');

    $installer = new lang_installer($pack);
    $results = $installer->run();
    foreach ($results as $langcode => $langstatus) {
        switch ($langstatus) {
        case lang_installer::RESULT_DOWNLOADERROR:
            $a       = new stdClass();
            $a->url  = $installer->lang_pack_url($langcode);
            $a->dest = $CFG->dataroot.'/lang';
            print_error('remotedownloaderror', 'error', 'index.php', $a);
            break;
        case lang_installer::RESULT_INSTALLED:
            $notice_ok[] = get_string('langpackinstalled', 'tool_langimport', $langcode);
            break;
        case lang_installer::RESULT_UPTODATE:
            $notice_ok[] = get_string('langpackuptodate', 'tool_langimport', $langcode);
            break;
        }
    }
}

if ($mode == DELETION_OF_SELECTED_LANG and !empty($uninstalllang)) {
    if ($uninstalllang == 'en') {
        $notice_error[] = 'English language pack can not be uninstalled';

    } else if (!$confirm and confirm_sesskey()) {
        echo $OUTPUT->header();
        echo $OUTPUT->confirm(get_string('uninstallconfirm', 'tool_langimport', $uninstalllang),
                     'index.php?mode='.DELETION_OF_SELECTED_LANG.'&uninstalllang='.$uninstalllang.'&confirm=1',
                     'index.php');
        echo $OUTPUT->footer();
        die;

    } else if (confirm_sesskey()) {
        $dest1 = $CFG->dataroot.'/lang/'.$uninstalllang;
        $dest2 = $CFG->dirroot.'/lang/'.$uninstalllang;
        $rm1 = false;
        $rm2 = false;
        if (file_exists($dest1)){
            $rm1 = remove_dir($dest1);
        }
        if (file_exists($dest2)){
            $rm2 = remove_dir($dest2);
        }
        if ($rm1 or $rm2) {
            $notice_ok[] = get_string('langpackremoved', 'tool_langimport');
        } else {    //nothing deleted, possibly due to permission error
            $notice_error[] = 'An error has occurred, language pack is not completely uninstalled, please check file permissions';
        }
    }
}

if ($mode == UPDATE_ALL_LANG) {
    set_time_limit(0);

    $installer = new lang_installer();

    if (!$availablelangs = $installer->get_remote_list_of_languages()) {
        print_error('cannotdownloadlanguageupdatelist', 'error');
    }
    $md5array = array();    // (string)langcode => (string)md5
    foreach ($availablelangs as $alang) {
        $md5array[$alang[0]] = $alang[1];
    }

    // filter out unofficial packs
    $currentlangs = array_keys(get_string_manager()->get_list_of_translations(true));
    $updateablelangs = array();
    foreach ($currentlangs as $clang) {
        if (!array_key_exists($clang, $md5array)) {
            $notice_ok[] = get_string('langpackupdateskipped', 'tool_langimport', $clang);
            continue;
        }
        $dest1 = $CFG->dataroot.'/lang/'.$clang;
        $dest2 = $CFG->dirroot.'/lang/'.$clang;

        if (file_exists($dest1.'/langconfig.php') || file_exists($dest2.'/langconfig.php')){
            $updateablelangs[] = $clang;
        }
    }

    // then filter out packs that have the same md5 key
    $neededlangs = array();   // all the packs that needs updating
    foreach ($updateablelangs as $ulang) {
        if (!is_installed_lang($ulang, $md5array[$ulang])) {
            $neededlangs[] = $ulang;
        }
    }

    make_temp_directory('');
    make_upload_directory('lang');

    // clean-up currently installed versions of the packs
    foreach ($neededlangs as $packindex => $pack) {
        if ($pack == 'en') {
            continue;
        }

        // delete old directories
        $dest1 = $CFG->dataroot.'/lang/'.$pack;
        $dest2 = $CFG->dirroot.'/lang/'.$pack;
        $rm1 = false;
        $rm2 = false;
        if (file_exists($dest1)) {
            if (!remove_dir($dest1)) {
                $notice_error[] = 'Could not delete old directory '.$dest1.', update of '.$pack.' failed, please check permissions.';
                unset($neededlangs[$packindex]);
                continue;
            }
        }
        if (file_exists($dest2)) {
            if (!remove_dir($dest2)) {
                $notice_error[] = 'Could not delete old directory '.$dest2.', update of '.$pack.' failed, please check permissions.';
                unset($neededlangs[$packindex]);
                continue;
            }
        }
    }

    // install all needed language packs
    $installer->set_queue($neededlangs);
    $results = $installer->run();
    $updated = false;    // any packs updated?
    foreach ($results as $langcode => $langstatus) {
        switch ($langstatus) {
        case lang_installer::RESULT_DOWNLOADERROR:
            $a       = new stdClass();
            $a->url  = $installer->lang_pack_url($langcode);
            $a->dest = $CFG->dataroot.'/lang';
            print_error('remotedownloaderror', 'error', 'index.php', $a);
            break;
        case lang_installer::RESULT_INSTALLED:
            $updated = true;
            $notice_ok[] = get_string('langpackinstalled', 'tool_langimport', $langcode);
            break;
        case lang_installer::RESULT_UPTODATE:
            $notice_ok[] = get_string('langpackuptodate', 'tool_langimport', $langcode);
            break;
        }
    }

    if ($updated) {
        $notice_ok[] = get_string('langupdatecomplete', 'tool_langimport');
    } else {
        $notice_ok[] = get_string('nolangupdateneeded', 'tool_langimport');
    }

    unset($installer);
}
get_string_manager()->reset_caches();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('langimport', 'tool_langimport'));

$installedlangs = get_string_manager()->get_list_of_translations(true);

$missingparents = array();
foreach ($installedlangs as $installedlang => $unused) {
    $parent = get_parent_language($installedlang);
    if (empty($parent)) {
        continue;
    }
    if (!isset($installedlangs[$parent])) {
        $missingparents[$installedlang] = $parent;
    }
}

$installer = new lang_installer();

if ($availablelangs = $installer->get_remote_list_of_languages()) {
    $remote = true;
} else {
    $remote = false;
    $availablelangs = array();
    echo $OUTPUT->box_start();
    print_string('remotelangnotavailable', 'tool_langimport', $CFG->dataroot.'/lang/');
    echo $OUTPUT->box_end();
}

if ($notice_ok) {
    $info = implode('<br />', $notice_ok);
    echo $OUTPUT->notification($info, 'notifysuccess');
}

if ($notice_error) {
    $info = implode('<br />', $notice_error);
    echo $OUTPUT->notification($info, 'notifyproblem');
}

if ($missingparents) {
    foreach ($missingparents as $l=>$parent) {
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
        echo $OUTPUT->notification($info, 'notifyproblem');
    }
}

echo $OUTPUT->box_start();

echo html_writer::start_tag('table');
echo html_writer::start_tag('tr');

// list of installed languages
$url = new moodle_url('/admin/tool/langimport/index.php', array('mode' => DELETION_OF_SELECTED_LANG));
echo html_writer::start_tag('td', array('valign' => 'top'));
echo html_writer::start_tag('form', array('id' => 'uninstallform', 'action' => $url->out(), 'method' => 'post'));
echo html_writer::start_tag('fieldset');
echo html_writer::label(get_string('installedlangs', 'tool_langimport'), 'menuuninstalllang');
echo html_writer::empty_tag('br');
echo html_writer::select($installedlangs, 'uninstalllang', '', false, array('size' => 15));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('br');
echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('uninstall', 'tool_langimport')));
echo html_writer::end_tag('fieldset');
echo html_writer::end_tag('form');
if ($remote) {
    $url = new moodle_url('/admin/tool/langimport/index.php', array('mode' => UPDATE_ALL_LANG));
    echo html_writer::start_tag('form', array('id' => 'updateform', 'action' => $url->out(), 'method' => 'post'));
    echo html_writer::tag('fieldset', html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('updatelangs','tool_langimport'))));
    echo html_writer::end_tag('form');
}
echo html_writer::end_tag('td');

// list of available languages
$options = array();
foreach ($availablelangs as $alang) {
    if (!empty($alang[0]) and trim($alang[0]) !== 'en' and !is_installed_lang($alang[0], $alang[1])) {
        $options[$alang[0]] = $alang[2].' ('.$alang[0].')';
    }
}
if (!empty($options)) {
    echo html_writer::start_tag('td', array('valign' => 'top'));
    $url = new moodle_url('/admin/tool/langimport/index.php', array('mode' => INSTALLATION_OF_SELECTED_LANG));
    echo html_writer::start_tag('form', array('id' => 'installform', 'action' => $url->out(), 'method' => 'post'));
    echo html_writer::start_tag('fieldset');
    echo html_writer::label(get_string('availablelangs','install'), 'menupack');
    echo html_writer::empty_tag('br');
    echo html_writer::select($options, 'pack[]', '', false, array('size' => 15, 'multiple' => 'multiple'));
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
    echo html_writer::empty_tag('br');
    echo html_writer::empty_tag('input', array('type' => 'submit', 'value' => get_string('install','tool_langimport')));
    echo html_writer::end_tag('fieldset');
    echo html_writer::end_tag('form');
    echo html_writer::end_tag('td');
}

echo html_writer::end_tag('tr');
echo html_writer::end_tag('table');
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
die();

////////////////////////////////////////////////////////////////////////////////
// Local functions /////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/**
 * checks the md5 of the zip file, grabbed from download.moodle.org,
 * against the md5 of the local language file from last update
 * @param string $lang
 * @param string $md5check
 * @return bool
 */
function is_installed_lang($lang, $md5check) {
    global $CFG;
    $md5file = $CFG->dataroot.'/lang/'.$lang.'/'.$lang.'.md5';
    if (file_exists($md5file)){
        return (file_get_contents($md5file) == $md5check);
    }
    return false;
}
