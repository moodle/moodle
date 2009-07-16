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
 * This script is used to edit the settings for a block instance.
 *
 * It works with the {@link block_edit_form} class, or rather the particular
 * subclass defined by this block, to do the editing.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../config.php');
require_once($CFG->dirroot . '/blocks/edit_form.php');

$blockid = required_param('id', PARAM_INTEGER);
$pagecontextid = required_param('pagecontextid', PARAM_INTEGER);
$pagetype = required_param('pagetype', PARAM_ALPHANUMEXT);
$subpage = optional_param('subpage', null, PARAM_ALPHANUMEXT);
$returnurl = required_param('returnurl', PARAM_LOCALURL);

$urlparams = array(
    'id' => $blockid,
    'pagecontextid' => $pagecontextid,
    'pagetype' => $pagetype,
    'returnurl' => $returnurl,
);
if ($subpage) {
    $urlparams['subpage'] = $subpage;
}
$PAGE->set_url('blocks/edit.php', $urlparams);

require_login();

$blockpage = new moodle_page();
$blockpage->set_context(get_context_instance_by_id($pagecontextid));
$blockpage->set_pagetype($pagetype);
$blockpage->set_subpage($subpage);
$url = new moodle_url($returnurl);
$blockpage->set_url($url->out(true), $url->params());

$block = block_load_for_page($blockid, $blockpage);

if (!$block->user_can_edit()) {
    throw new moodle_exception('nopermissions', '', $page->url->out(), get_string('editblock'));
}

$PAGE->set_context($block->context);
$PAGE->set_generaltype('form');

$formfile = $CFG->dirroot . '/blocks/' . $block->name() . '/edit_form.php';
if (is_readable($formfile)) {
    require_once($formfile);
    $classname = 'block_' . $block->name() . '_edit_form';
} else {
    $classname = 'block_edit_form';
} 
$mform = new $classname($block, $blockpage);

$mform->set_data($block->instance);

if ($mform->is_cancelled()) {
    redirect($CFG->wwwroot . '/' . $returnurl);

} else if ($data = $mform->get_data()) {
    $bi = new stdClass;
    $bi->id = $block->instance->id;
    $bi->showinsubcontexts = $data->showinsubcontexts;
    $bi->pagetypepattern = $data->pagetypepattern;
    if (empty($data->subpagepattern) || $data->subpagepattern == '%@NULL@%') {
        $bi->subpagepattern = null;
    } else {
        $bi->subpagepattern = $data->subpagepattern;
    }
    $bi->defaultregion = $data->defaultregion;
    $bi->defaultweight = $data->defaultweight;
    $DB->update_record('block_instances', $bi);

    $config = new stdClass;
    foreach ($data as $configfield => $value) {
        if (strpos($configfield, 'config_') !== 0) {
            continue;
        }
        $field = substr($configfield, 7);
        $config->$field = $value;
    }
    $block->instance_config_save($config);

    $bp = new stdClass;
    $bp->visible = $data->visible;
    $bp->region = $data->region;
    $bp->weight = $data->weight;
    $needbprecord = !$data->visible || $data->region != $data->defaultregion ||
            $data->weight != $data->defaultweight;

    if ($block->instance->blockpositionid && !$needbprecord) {
        $DB->delete_records('block_positions', array('id' => $block->instance->blockpositionid));

    } else if ($block->instance->blockpositionid && $needbprecord) {
        $bp->id = $block->instance->blockpositionid;
        $DB->update_record('block_positions', $bp);

    } else if ($needbprecord) {
        $bp->blockinstanceid = $block->instance->id;
        $bp->contextid = $blockpage->contextid;
        $bp->pagetype = $blockpage->pagetype;
        if ($blockpage->subpage) {
            $bp->subpage = $blockpage->subpage;
        } else {
            $bp->subpage = null;
        }
        $DB->insert_record('block_positions', $bp);
    }

    redirect($CFG->wwwroot . '/' . $returnurl);

} else {
    $strheading = get_string('editinga', $block->name());
    if (strpos($strheading, '[[') === 0) {
        $strheading = get_string('blockconfiga', 'moodle', $block->get_title());
    }

$PAGE->set_title($strheading);
    $PAGE->set_heading($strheading);

    echo $OUTPUT->header();
    echo $OUTPUT->heading($strheading, 2);

    $mform->display();

    echo $OUTPUT->footer();
}