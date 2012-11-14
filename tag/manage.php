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

define('SHOW_ALL_PAGE_SIZE', 50000);
define('DEFAULT_PAGE_SIZE', 30);

$tagschecked = optional_param_array('tagschecked', array(), PARAM_INT);
$newnames    = optional_param_array('newname', array(), PARAM_TAG);
$tagtypes    = optional_param_array('tagtypes', array(), PARAM_ALPHA);
$action      = optional_param('action', '', PARAM_ALPHA);
$perpage     = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$systemcontext = context_system::instance();
require_capability('moodle/tag:manage', $systemcontext);

$params = array();
if ($perpage != DEFAULT_PAGE_SIZE) {
    $params['perpage'] = $perpage;
}
$PAGE->set_url('/tag/manage.php', $params);
$PAGE->set_context($systemcontext);
$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');
$PAGE->navbar->add(get_string('tags', 'tag'), new moodle_url('/tag/search.php'));
$PAGE->navbar->add(get_string('managetags', 'tag'));
$PAGE->set_title(get_string('managetags', 'tag'));
$PAGE->set_heading($COURSE->fullname);
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();

$err_notice = '';
$notice = '';

// get all the possible tag types from db
$existing_tagtypes = array();
if ($ptypes = $DB->get_records_sql("SELECT DISTINCT(tagtype) FROM {tag}")) {
    foreach ($ptypes as $ptype) {
        $existing_tagtypes[$ptype->tagtype] = $ptype->tagtype;
    }
}
$existing_tagtypes['official'] = get_string('tagtype_official', 'tag');
$existing_tagtypes['default'] = get_string('tagtype_default', 'tag');

switch($action) {

    case 'delete':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }

        $str_tagschecked = implode(', ', tag_get_name($tagschecked));
        tag_delete($tagschecked);
        $notice = $str_tagschecked.' --  '.get_string('deleted','tag');
        break;

    case 'reset':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }
        $str_tagschecked = implode(', ', tag_get_name($tagschecked));
        tag_unset_flag($tagschecked);
        $notice = $str_tagschecked .' -- '. get_string('reset', 'tag');
        break;

    case 'changetype':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }

        $changed = array();
        foreach ($tagschecked as $tag_id) {
            if (!array_key_exists($tagtypes[$tag_id], $existing_tagtypes)) {
                //can not add new types here!!
                continue;
            }

            // update tag type;
            if (tag_type_set($tag_id, $tagtypes[$tag_id])) {
                $changed[] = $tag_id;
            }
        }

        if (!empty($changed)) {
            $str_changed = implode(', ', tag_get_name($changed));
            $notice = $str_changed .' --  '. get_string('typechanged','tag');
        }
        break;

    case 'changename':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }

        $tags_names_changed = array();
        foreach ($tagschecked as $tag_id) {
            if ($newnames[$tag_id] != '') {
                if (! $tags_names_updated[] = tag_rename($tag_id, $newnames[$tag_id]) ) {
                    // if tag already exists, or is not a valid tag name, etc.
                    $err_notice .= $newnames[$tag_id]. '-- ' . get_string('namesalreadybeeingused','tag').'<br />';
                } else {
                    $tags_names_changed[$tag_id] = $newnames[$tag_id];
                }
            }
        }

        //notice to inform what tags had their names effectively updated
        if ($tags_names_changed){
            $notice = implode($tags_names_changed, ', ');
            $notice .= ' -- ' . get_string('updated','tag');
        }
        break;
    case 'addofficialtag':
        if (!data_submitted() or !confirm_sesskey()) {
            break;
        }

        $new_otags = explode(',', optional_param('otagsadd', '', PARAM_TAG));
        $notice = '';
        foreach ( $new_otags as $new_otag ) {
            if ( $new_otag_id = tag_get_id($new_otag) ) {
                // tag exists, change the type
                tag_type_set($new_otag_id, 'official');
            } else {
                require_capability('moodle/tag:create', context_system::instance());
                tag_add($new_otag, 'official');
            }
            $notice .= get_string('addedotag', 'tag', $new_otag) .' ';
        }
        break;
}

if ($err_notice) {
    echo $OUTPUT->notification($err_notice, 'red');
}
if ($notice) {
    echo $OUTPUT->notification($notice, 'green');
}

// small form to add an official tag
print('<form class="tag-management-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php">');
print('<input type="hidden" name="action" value="addofficialtag" />');
print('<div class="tag-management-form generalbox"><label class="accesshide" for="id_otagsadd">'. get_string('addotags', 'tag') .'</label>'.
    '<input name="otagsadd" id="id_otagsadd" type="text" />'.
    '<input type="hidden" name="sesskey" value="'.sesskey().'">'.
    '<input name="addotags" value="'. get_string('addotags', 'tag') .'" onclick="skipClientValidation = true;" id="id_addotags" type="submit" />'.
    '</div>');
print('</form>');

//setup table

$tablecolumns = array('id', 'name', 'fullname', 'count', 'flag', 'timemodified', 'rawname', 'tagtype', '');
$tableheaders = array(get_string('id', 'tag'),
                      get_string('name', 'tag'),
                      get_string('owner', 'tag'),
                      get_string('count', 'tag'),
                      get_string('flag', 'tag'),
                      get_string('timemodified', 'tag'),
                      get_string('newname', 'tag'),
                      get_string('tagtype', 'tag'),
                      get_string('select', 'tag'));

$table = new flexible_table('tag-management-list-'.$USER->id);

$baseurl = $CFG->wwwroot.'/tag/manage.php?perpage='.$perpage;

$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->define_baseurl($baseurl);

$table->sortable(true, 'flag', SORT_DESC);

$table->set_attribute('cellspacing', '0');
$table->set_attribute('id', 'tag-management-list');
$table->set_attribute('class', 'admintable generaltable');

$table->set_control_variables(array(
TABLE_VAR_SORT    => 'ssort',
TABLE_VAR_HIDE    => 'shide',
TABLE_VAR_SHOW    => 'sshow',
TABLE_VAR_IFIRST  => 'sifirst',
TABLE_VAR_ILAST   => 'silast',
TABLE_VAR_PAGE    => 'spage'
));

$table->setup();

if ($table->get_sql_sort()) {
    $sort = 'ORDER BY '. $table->get_sql_sort();
} else {
    $sort = '';
}

list($where, $params) = $table->get_sql_where();
if ($where) {
    $where = 'WHERE '. $where;
}

$query = "
        SELECT tg.id, tg.name, tg.rawname, tg.tagtype, tg.flag, tg.timemodified,
               u.id AS owner, u.firstname, u.lastname,
               COUNT(ti.id) AS count
          FROM {tag} tg
     LEFT JOIN {tag_instance} ti ON ti.tagid = tg.id
     LEFT JOIN {user} u ON u.id = tg.userid
               $where
      GROUP BY tg.id, tg.name, tg.rawname, tg.tagtype, tg.flag, tg.timemodified,
               u.id, u.firstname, u.lastname
         $sort";

$totalcount = $DB->count_records_sql("
        SELECT COUNT(DISTINCT(tg.id))
          FROM {tag} tg
     LEFT JOIN {user} u ON u.id = tg.userid
        $where", $params);

$table->initialbars(true); // always initial bars
$table->pagesize($perpage, $totalcount);

//@todo MDL-35474 convert to mform
echo '<form class="tag-management-form" method="post" action="'.$CFG->wwwroot.'/tag/manage.php"><div>';

//retrieve tags from DB
if ($tagrecords = $DB->get_records_sql($query, $params, $table->get_page_start(),  $table->get_page_size())) {

    //populate table with data
    foreach ($tagrecords as $tag) {
        $id             = $tag->id;
        $params         = array('id' => $tag->id);
        $taglink        = new moodle_url($CFG->wwwroot . '/tag/index.php', $params);
        $name           = html_writer::link($taglink, tag_display_name($tag));
        $params         = array('id' => $tag->owner);
        $ownerlink      = new moodle_url($CFG->wwwroot . '/user/view.php', $params);
        $owner          = html_writer::link($ownerlink, fullname($tag));
        $count          = $tag->count;
        $flag           = $tag->flag;
        $timemodified   = format_time(time() - $tag->timemodified);
        $checkbox       = html_writer::tag('input', '', array('type' => 'checkbox', 'name' => 'tagschecked[]', 'value' => $tag->id));
        $attrs          = array('type' => 'text', 'id' => 'newname_' . $tag->id, 'name' => 'newname['.$tag->id.']');
        $text           = html_writer::label(get_string('newname', 'tag'), 'newname_' . $tag->id, false, array('class' => 'accesshide'));
        $text          .= html_writer::empty_tag('input', $attrs);
        $tagtype        = html_writer::label(get_string('tagtype', 'tag'), 'menutagtypes'. $tag->id, false, array('class' => 'accesshide'));
        $tagtype       .= html_writer::select($existing_tagtypes, 'tagtypes['.$tag->id.']', $tag->tagtype, false, array('id' => 'menutagtypes'. $tag->id));

        //if the tag if flagged, highlight it
        if ($tag->flag > 0) {
            $id = html_writer::tag('span', $id, array('class' => 'flagged-tag'));
            $name = html_writer::tag('span', $name, array('class' => 'flagged-tag'));
            $owner = html_writer::tag('span', $owner, array('class' => 'flagged-tag'));
            $count = html_writer::tag('span', $count, array('class' => 'flagged-tag'));
            $flag = html_writer::tag('span', $flag, array('class' => 'flagged-tag'));
            $timemodified = html_writer::tag('span', $timemodified, array('class' => 'flagged-tag'));
            $tagtype = html_writer::tag('span', $tagtype, array('class' => 'flagged-tag'));
        }

        $data = array($id, $name, $owner, $count, $flag, $timemodified, $text, $tagtype, $checkbox);

        $table->add_data($data);
    }

    echo html_writer::empty_tag('input', array('type' => 'button', 'onclick' => 'checkall()', 'value' => get_string('selectall')));
    echo html_writer::empty_tag('input', array('type' => 'button', 'onclick' => 'checknone()', 'value' => get_string('deselectall')));
    echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
    echo html_writer::empty_tag('br');
    echo html_writer::empty_tag('br');

    echo html_writer::label(get_string('withselectedtags', 'tag'), 'menuformaction', false, array('class' => 'accesshide'));
    $options = array('' => get_string('withselectedtags', 'tag'),
                     'reset' => get_string('resetflag', 'tag'),
                     'delete' => get_string('delete', 'tag'),
                     'changetype' => get_string('changetype', 'tag'),
                     'changename' => get_string('changename', 'tag'));
    echo html_writer::select($options, 'action', '', array(), array('id' => 'menuformaction'));

    echo html_writer::tag('button', get_string('ok'), array('id' => 'tag-management-submit', 'type' => 'submit'));
}

$table->print_html();

//@todo MDL-35474 convert to mform
echo '</div></form>';

if ($perpage == SHOW_ALL_PAGE_SIZE) {
    echo html_writer::start_tag('div', array('id' => 'showall'));
    $params = array('perpage' => DEFAULT_PAGE_SIZE);
    $url = new moodle_url($baseurl, $params);
    echo html_writer::link($url, get_string('showperpage', '', DEFAULT_PAGE_SIZE));
    echo html_writer::end_tag('div');

} else if ($totalcount > 0 and $perpage < $totalcount) {
    echo html_writer::start_tag('div', array('id' => 'showall'));
    $params = array('perpage' => SHOW_ALL_PAGE_SIZE);
    $url = new moodle_url($baseurl, $params);
    echo html_writer::link($url, get_string('showall', '', $totalcount));
    echo html_writer::end_tag('div');
}

echo html_writer::empty_tag('br');

echo $OUTPUT->footer();
