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
 * @package    core
 * @subpackage tag
 * @copyright  2007 Luiz Cruz <luiz.laydner@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');
require_once($CFG->libdir.'/adminlib.php');

define('SHOW_ALL_PAGE_SIZE', 50000);
define('DEFAULT_PAGE_SIZE', 30);

$tagschecked = optional_param_array('tagschecked', array(), PARAM_INT);
$tagid       = optional_param('tagid', null, PARAM_INT);
$tagtype     = optional_param('tagtype', null, PARAM_ALPHA);
$action      = optional_param('action', '', PARAM_ALPHA);
$perpage     = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);
$page        = optional_param('page', 0, PARAM_INT);
$notice      = optional_param('notice', '', PARAM_ALPHA);

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$params = array();
if ($perpage != DEFAULT_PAGE_SIZE) {
    $params['perpage'] = $perpage;
}
if ($page > 0) {
    $params['page'] = $page;
}
admin_externalpage_setup('managetags', '', $params, '', array('pagelayout' => 'report'));

$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');

switch($action) {

    case 'delete':
        require_sesskey();
        if (!$tagschecked && $tagid) {
            $tagschecked = array($tagid);
        }
        tag_delete($tagschecked);
        redirect(new moodle_url($PAGE->url, array('notice' => 'deleted')));
        break;

    case 'setflag':
        require_sesskey();
        tag_set_flag($tagid);
        redirect(new moodle_url($PAGE->url, array('notice' => 'flagged')));
        break;

    case 'resetflag':
        require_sesskey();
        tag_unset_flag($tagid);
        redirect(new moodle_url($PAGE->url, array('notice' => 'resetflag')));
        break;

    case 'changetype':
        require_sesskey();
        if ($tagtype === 'official' || $tagtype === 'default') {
            if (tag_type_set($tagid, $tagtype)) {
                redirect(new moodle_url($PAGE->url, array('notice' => 'typechanged')));
            }
        }
        redirect($PAGE->url);
        break;

    case 'addofficialtag':
        require_sesskey();
        $otagsadd = optional_param('otagsadd', '', PARAM_RAW);
        $newtags = preg_split('/\s*,\s*/', trim($otagsadd), -1, PREG_SPLIT_NO_EMPTY);
        $newtags = array_filter(tag_normalize($newtags, TAG_CASE_ORIGINAL));
        if (!$newtags) {
            redirect($PAGE->url);
        }
        foreach ($newtags as $newotag) {
            if ($newotagid = tag_get_id($newotag) ) {
                // Tag exists, change the type.
                tag_type_set($newotagid, 'official');
            } else {
                tag_add($newotag, 'official');
            }
        }
        redirect(new moodle_url($PAGE->url, array('notice' => 'added')));
        break;
}

echo $OUTPUT->header();

if ($notice && get_string_manager()->string_exists($notice, 'tag')) {
    echo $OUTPUT->notification(get_string($notice, 'tag'), 'notifysuccess');
}

// Small form to add an official tag.
print('<form class="tag-addtags-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php">');
print('<input type="hidden" name="action" value="addofficialtag" />');
print('<input type="hidden" name="perpage" value="'.$perpage.'" />');
print('<input type="hidden" name="page" value="'.$page.'" />');
print('<div class="tag-management-form generalbox"><label class="accesshide" for="id_otagsadd">' .
        get_string('addotags', 'tag') .'</label>'.
    '<input name="otagsadd" id="id_otagsadd" type="text" />'.
    '<input type="hidden" name="sesskey" value="'.sesskey().'">'.
    '<input name="addotags" value="'. get_string('addotags', 'tag') .
        '" onclick="skipClientValidation = true;" id="id_addotags" type="submit" />'.
    '</div>');
print('</form>');

$table = new core_tag_manage_table();
echo '<form class="tag-management-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php">';
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'action', 'value' => 'delete'));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'perpage', 'value' => $perpage));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'page', 'value' => $page));
echo $table->out($perpage, true);

echo html_writer::start_tag('p');
echo html_writer::tag('button', get_string('deleteselected', 'tag'),
        array('id' => 'tag-management-delete', 'type' => 'submit', 'class' => 'tagdeleteselected'));
echo html_writer::end_tag('p');
echo '</form>';

$totalcount = $table->totalcount;
if ($perpage == SHOW_ALL_PAGE_SIZE) {
    echo html_writer::start_tag('div', array('id' => 'showall'));
    $params = array('perpage' => DEFAULT_PAGE_SIZE, 'page' => 0);
    $url = new moodle_url($PAGE->url, $params);
    echo html_writer::link($url, get_string('showperpage', '', DEFAULT_PAGE_SIZE));
    echo html_writer::end_tag('div');
} else if ($totalcount > 0 and $perpage < $totalcount) {
    echo html_writer::start_tag('div', array('id' => 'showall'));
    $params = array('perpage' => SHOW_ALL_PAGE_SIZE, 'page' => 0);
    $url = new moodle_url($PAGE->url, $params);
    echo html_writer::link($url, get_string('showall', '', $totalcount));
    echo html_writer::end_tag('div');
}

echo $OUTPUT->footer();
