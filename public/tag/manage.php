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
 * Managing tags, tag areas and tags collections
 *
 * @package    core_tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');

use core\context\system;
use core_reportbuilder\system_report_factory;
use core_tag\reportbuilder\local\systemreports\tags;

$tagid       = optional_param('tagid', null, PARAM_INT);
$isstandard  = optional_param('isstandard', null, PARAM_INT);
$action      = optional_param('action', '', PARAM_ALPHA);
$tagcollid   = optional_param('tc', 0, PARAM_INT);

$params = array();
if ($tagcollid) {
    $params['tc'] = $tagcollid;
}

admin_externalpage_setup('managetags', '', $params, '', array('pagelayout' => 'report'));

if (empty($CFG->usetags)) {
    throw new \moodle_exception('tagsaredisabled', 'tag');
}

$tagobject = null;
if ($tagid) {
    $tagobject = core_tag_tag::get($tagid, '*', MUST_EXIST);
    $tagcollid = $tagobject->tagcollid;
}
$tagcoll = core_tag_collection::get_by_id($tagcollid);
$manageurl = new moodle_url('/tag/manage.php');
if ($tagcoll) {
    // We are inside a tag collection - add it to the breadcrumb.
    $PAGE->navbar->add(core_tag_collection::display_name($tagcoll),
            new moodle_url($manageurl, array('tc' => $tagcoll->id)));
}

$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');

$PAGE->set_primary_active_tab('siteadminnode');

switch($action) {

    case 'colladd':
        require_sesskey();
        $name = required_param('name', PARAM_NOTAGS);
        $searchable = optional_param('searchable', false, PARAM_BOOL);
        core_tag_collection::create(array('name' => $name, 'searchable' => $searchable));
        redirect($manageurl);
        break;

    case 'colldelete':
        if ($tagcoll && !$tagcoll->component) {
            require_sesskey();
            core_tag_collection::delete($tagcoll);
            \core\notification::success(get_string('changessaved', 'core_tag'));
        }
        redirect($manageurl);
        break;

    case 'collmoveup':
        if ($tagcoll) {
            require_sesskey();
            core_tag_collection::change_sortorder($tagcoll, -1);
            redirect($manageurl, get_string('changessaved', 'core_tag'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
        redirect($manageurl);
        break;

    case 'collmovedown':
        if ($tagcoll) {
            require_sesskey();
            core_tag_collection::change_sortorder($tagcoll, 1);
            redirect($manageurl, get_string('changessaved', 'core_tag'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
        redirect($manageurl);
        break;

    case 'delete':
        if ($tagid) {
            require_sesskey();
            core_tag_tag::delete_tags(array($tagid));
            \core\notification::success(get_string('deleted', 'core_tag'));
        }
        redirect($PAGE->url);
        break;

    case 'bulk':
        $tagschecked = explode(',', optional_param('tagschecked', '', PARAM_SEQUENCE));
        if (optional_param('bulkdelete', null, PARAM_RAW) !== null) {
            if ($tagschecked) {
                require_sesskey();
                core_tag_tag::delete_tags($tagschecked);
                \core\notification::success(get_string('deleted', 'core_tag'));
            }
            redirect($PAGE->url);
        } else if (optional_param('bulkcombine', null, PARAM_RAW) !== null) {
            $tags = core_tag_tag::get_bulk($tagschecked, '*');
            if (count($tags) > 1) {
                require_sesskey();
                if (($maintag = optional_param('maintag', 0, PARAM_INT)) && array_key_exists($maintag, $tags)) {
                    $tag = $tags[$maintag];
                } else {
                    $tag = array_shift($tags);
                }
                $tag->combine_tags($tags);
                \core\notification::success(get_string('combined', 'core_tag'));
            }
            redirect($PAGE->url);
        }
        break;

    case 'renamecombine':
        // Allows to rename the tag and if the tag with the new name already exists these tags will be combined.
        if ($tagid && ($newname = required_param('newname', PARAM_TAG))) {
            require_sesskey();
            $tag = core_tag_tag::get($tagid, '*', MUST_EXIST);
            $targettag = core_tag_tag::get_by_name($tag->tagcollid, $newname, '*');
            if ($targettag) {
                $targettag->combine_tags(array($tag));
                \core\notification::success(get_string('combined', 'core_tag'));
            } else {
                $tag->update(array('rawname' => $newname));
                \core\notification::success(get_string('changessaved', 'core_tag'));
            }
        }
        redirect($PAGE->url);
        break;

    case 'addstandardtag':
        require_sesskey();
        $tagobjects = array();
        if ($tagcoll) {
            $tagslist = optional_param('tagslist', '', PARAM_RAW);
            $newtags = preg_split('/\s*,\s*/', trim($tagslist), -1, PREG_SPLIT_NO_EMPTY);
            $tagobjects = core_tag_tag::create_if_missing($tagcoll->id, $newtags, true);
        }
        foreach ($tagobjects as $tagobject) {
            if (!$tagobject->isstandard) {
                $tagobject->update(array('isstandard' => 1));
            }
        }
        redirect($PAGE->url, $tagobjects ? get_string('added', 'core_tag') : null,
                null, \core\output\notification::NOTIFY_SUCCESS);
        break;
}

echo $OUTPUT->header();

if (!$tagcoll) {
    // Tag collection is not specified. Display the overview of tag collections and tag areas.
    $tagareastable = new core_tag_areas_table($manageurl);
    $colltable = new core_tag_collections_table($manageurl);

    echo $OUTPUT->heading(get_string('tagcollections', 'core_tag') . $OUTPUT->help_icon('tagcollection', 'tag'), 3);
    echo html_writer::table($colltable);
    $url = new moodle_url($manageurl, array('action' => 'colladd'));
    echo html_writer::div(html_writer::link('#', get_string('addtagcoll', 'tag'), array('data-url' => $url)),
            'mdl-right addtagcoll');

    echo $OUTPUT->heading(get_string('tagareas', 'core_tag'), 3);
    echo html_writer::table($tagareastable);

    $PAGE->requires->js_call_amd('core/tag', 'initManageCollectionsPage', array());

    echo $OUTPUT->footer();
    exit;
}

// Tag collection is specified. Manage tags in this collection.
echo html_writer::div(
    $OUTPUT->heading(core_tag_collection::display_name($tagcoll)) .
    html_writer::tag(
        'button',
        $OUTPUT->pix_icon('t/add', '') . get_string('addotags', 'core_tag'),
        [
            'type' => 'button',
            'class' => 'btn btn-primary my-auto',
            'data-action' => 'addstandardtag',
        ],
    ),
    'd-flex justify-content-between mb-2',
);

// Render the report.
$report = system_report_factory::create(tags::class, system::instance(), '', '', 0, ['collection' => $tagcoll->id]);
echo $report->output();

// Render bulk actions.
if ($DB->record_exists('tag', [])) {
    echo '<form class="tag-management-form" method="post" action="' . $PAGE->url->out_omit_querystring() . '">';
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'tc', 'value' => $tagcoll->id]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'bulk']);
    echo html_writer::tag('button', get_string('deleteselected', 'tag'),
            array('id' => 'tag-management-delete', 'type' => 'submit',
                  'class' => 'tagdeleteselected btn btn-secondary', 'name' => 'bulkdelete'));
    echo html_writer::tag('button', get_string('combineselected', 'tag'),
        array('id' => 'tag-management-combine', 'type' => 'submit',
              'class' => 'tagcombineselected btn btn-secondary', 'name' => 'bulkcombine'));
    echo '</form>';
}

$PAGE->requires->js_call_amd('core/tag', 'initManagePage');

echo $OUTPUT->footer();
