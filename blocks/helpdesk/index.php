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
 *
 * @package    block_helpdesk
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');
require_once('searchform.php');
require_once('classes/event/helpdesk_course.php');

require_login();

$mode = optional_param('mode', 'course', PARAM_ALPHA);

$blockname = get_string('pluginname', 'block_helpdesk');

if ($mode == 'user') {
    $header = get_string('search_users', 'block_helpdesk');
    $criterion = array('username' => get_string('username')
                     , 'idnumber' => get_string('idnumber')
                     , 'firstname' => get_string('firstname')
                     , 'lastname' => get_string('lastname')
    );
    $fields = user_picture::fields().', idnumber';

    $results = function($data, &$count) use ($criterion, $mode, $fields, $DB) {
        $sql = $data ? hdesk_get_results_sql($data, $criterion) : null;
        $count = $sql ? $DB->count_records_select($mode, $sql) : 0;
        return $sql ? $DB->get_records_select($mode, $sql, null, '', $fields) : array();
    };

    $followlink = new moodle_url('/user/view.php');
} else {
    $header = get_string('search_courses', 'block_helpdesk');
    $criterion = array('fullname' => get_string('fullname'));
    $results = function($data, &$count) {
        $fullnames = explode(' ', $data->fullname_terms);
        return $data ?
            get_courses_search($fullnames, 'fullname ASC', 0, 50, $count) :
            array();
    };
    $followlink = 'participants.php';
}

$context = context_system::instance();
$PAGE->set_context($context);

$PAGE->navbar->add($blockname);
$PAGE->navbar->add($header);
$PAGE->set_title($blockname . ': ' .$header);
$PAGE->set_heading($SITE->shortname. ": " . $blockname);
$PAGE->set_url('/blocks/helpdesk/', array('mode' => $mode));

$availability = array('contains' => get_string('contains', 'block_helpdesk')
                    , 'equal' => get_string('equal', 'block_helpdesk')
                    , 'starts' => get_string('starts', 'block_helpdesk')
                    , 'ends' => get_string('ends', 'block_helpdesk')
                     );

$form = new helpdesk_searchform(null, array('availability' => $availability
                                          , 'criterion' => $criterion
                                          , 'mode' => $mode
                                           )
                               );

if ($form->is_cancelled()) {
    redirect(new moodle_url('/my'));
} else if ($data = $form->get_data()) {
    $return = $results($data, $count);

    if ($count) {
        $table = new html_table();
        $table->head = array(get_string('fullname'));
        if ($mode == 'user') {
            $table->head[] = get_string('idnumber');
        }
        $table->head[] = get_string('action');

        $uesReprocessInstalled = isPluginVersionInstalled('block_ues_reprocess', 2016022912);

        foreach ($results($data, $count) as $obj) {

            $fn = !empty($obj->fullname) ? $obj->fullname : fullname($obj);

            $outputLinks = array();
            $help = new stdClass;
            $help->{$mode . 'id'} = $obj->id;
            $help->links = array();
            if ($mode == "user")
                $context = context_user::instance($obj->id);
            else
                $context = context_course::instance($obj->id);
            $help->context = $context;
            $help->$mode = $obj;

            $eventdata = array(($mode . 'id') => $help->{$mode . 'id'}
                             , 'context' => $help->context
                             , 'other' => array('links' => $help->links)
                              );

            $event = blocks_helpdesk\event\helpdesk_course::create($eventdata);
            $event->trigger();

            // do "stuff" depending on the 'mode' that is currently being displayed (ex: course, user)
            switch ($mode) {
                case 'course':

                    if ($uesReprocessInstalled) {

                        // if this user has the ability to reprocess this course, add a link to the output
                        if (has_capability('block/ues_reprocess:canreprocess', $context)) {

                            $linkText = get_string('pluginname', 'block_ues_reprocess');

                            $whereThisCourse = array('id' => $obj->id, 'type' => 'course');

                            $url = new moodle_url('/blocks/ues_reprocess/reprocess.php', $whereThisCourse);

                            $outputLinks[] = html_writer::link($url, $linkText);
                        }
                    }

                    break;
                
                case 'user':
                    // do nothing for users...
                    break;

                default:
                    break;
            }

            $url = new moodle_url($followlink, array('id' => $obj->id));

            $attrs = (isset($obj->visible) && !$obj->visible) ?
                array('class' => 'dimmed') : array();

            $line = array(html_writer::link($url, $fn, $attrs));
            if ($mode == 'user') {
                $line[] = $obj->idnumber;
            }
            $line[] = implode(' | ', $outputLinks);

            $table->data[] = new html_table_row($line);
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($header);

$form->display();

if ($data) {
    if (empty($count)) {
        $OUTPUT->notification(get_string('no_results', 'block_helpdesk'));
    } else {
        echo html_writer::tag('div',
            html_writer::table($table),
            array('class' => 'box'));
    }
}

echo $OUTPUT->footer();

/**
 * Checks to see that a specified version of a specified plugin is installed
 *
 * If a specified version is not installed, there will be no minimum version check.
 * 
 * @param  string  $pluginName       the moodle plugin name
 * @param  int     $requiredVersion  moodle version number (ex: 2016022912)
 * @return boolean
 */
function isPluginVersionInstalled($pluginName = '', $requiredVersion = false) {
    
    if ( ! $pluginName)
        return false;

    $pluginInfo = core_plugin_manager::instance()->get_plugin_info($pluginName);

    if ( is_null($pluginInfo) && ! is_object($pluginInfo))
        return false;

    if ( ! $requiredVersion)
        return true;

    $requiredVersionInstalled = ($pluginInfo->versiondisk >= $requiredVersion) ? true : false;

    return $requiredVersionInstalled;
}