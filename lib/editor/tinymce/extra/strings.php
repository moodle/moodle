<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

define('NO_MOODLE_COOKIES', true);
define('NO_UPGRADE_CHECK', true);

require_once('../../../../config.php');

$lang  = optional_param('editorlanguage', 'en', PARAM_SAFEDIR);
$theme = optional_param('editortheme', 'advanced', PARAM_SAFEDIR);

if (file_exists($CFG->dataroot .'/lang/'. $lang) or file_exists($CFG->dirroot .'/lang/'. $lang)) {
    //ok
} else if (file_exists($CFG->dataroot.'/lang/'.$lang.'_utf8') or
           file_exists($CFG->dirroot .'/lang/'.$lang.'_utf8')) {
    $lang = $lang.'_utf8';
} else {
    $lang = 'en_utf8';
}

// load english defaults
$string = array();
foreach (get_langpack_locations('en_utf8') as $location) {
    if (!file_exists($location)) {
        continue;
    }
    include_once($location);
}

// find parent language
if ($parent = get_parent_language($lang)) {
    foreach (get_langpack_locations($parent) as $location) {
        if (!file_exists($location)) {
            continue;
        }
        include_once($location);
    }
}

// load wanted language
if ($lang !== 'en_utf8') {
    foreach (get_langpack_locations($lang) as $location) {
        if (!file_exists($location)) {
            continue;
        }
        include_once($location);
    }
}

//process the $strings to match expected tinymce lang array stucture
$result = array('main'=>array(), 'plugins'=>array(), 'themes'=>array());

foreach ($string as $key=>$value) {
    $parts = preg_split('|[/:]|', $key);
    if (count($parts) != 3) {
        // incorrect string - ignore
        continue;
    }
    $result[$parts[0]][$parts[1]][$parts[2]] = $value;
}

$output = '';

//main
$output .= 'tinyMCE.addI18n({'.$lang.':'.json_encode($result['main']).'});';

//plugins
foreach ($result['plugins'] as $pluginname=>$plugin) {
    $output .= "tinyMCE.addI18n('$lang.$pluginname',".json_encode($plugin).');';
}

if (!empty($result['themes'][$theme])) {
    $output .= "tinyMCE.addI18n('$lang.$theme',".json_encode($result['themes'][$theme]).');';
}
if (!empty($result['themes'][$theme.'_dlg'])) {
    $output .= "tinyMCE.addI18n('$lang.{$theme}_dlg',".json_encode($result['themes'][$theme.'_dlg']).');';
}


$lifetime = '10'; // TODO: increase later
@header('Content-type: text/javascript; charset=utf-8');
@header('Content-length: '.strlen($output));
@header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
@header('Cache-control: max-age='.$lifetime);
@header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
@header('Pragma: ');

echo $output;


/// ======= Functions =================

function get_langpack_locations($lang) {
    global $CFG;

    $result = array();
    $result[] = "$CFG->dirroot/lang/$lang/editor_tinymce.php";
    $result[] = "$CFG->dataroot/lang/$lang/editor_tinymce.php";
    $result[] = "$CFG->dataroot/lang/{$lang}_local/editor_tinymce.php";

    return $result;
}
