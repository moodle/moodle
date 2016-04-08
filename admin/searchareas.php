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
 * Manage global search areas.
 *
 * @package   core_search
 * @copyright 2016 Dan Poltawski <dan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('searchareas');

$areaid = optional_param('searcharea', null, PARAM_ALPHAEXT);
$action = optional_param('action', null, PARAM_ALPHA);

if ($action) {
    require_sesskey();

    $area = \core_search\manager::get_search_area($areaid);

    if ($area === false) {
        throw new moodle_exception('invalidrequest');
    }
    // FIXME: lang strings.
    switch ($action) {
        case 'enable':
            $area->set_enabled(true);
            redirect($PAGE->url, 'Search area enabled', null, \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'disable':
            $area->set_enabled(false);
            redirect($PAGE->url, 'Search area disabled', null, \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'delete':
            $search = \core_search\manager::instance();
            $search->delete_index($areaid);
            redirect($PAGE->url, 'Index deleted', null, \core\output\notification::NOTIFY_SUCCESS);
            break;
        default:
            throw new moodle_exception('invalidaction');
            break;
    }
}

$searchareas = \core_search\manager::get_search_areas_list();
try {
    $searchmanager = \core_search\manager::instance();
    $areasconfig = $searchmanager->get_areas_config($searchareas);
} catch (core_search\engine_exception $e) {
    $areasconfig = false;
}

$areasbycomponent = array();
foreach ($searchareas as $area) {
    $component = $area->get_component_name();
    if (isset($areasbycomponent[$component])) {
        $areasbycomponent[$component][] = $area;
    } else {
        $areasbycomponent[$component] = array($area);
    }
}

echo $OUTPUT->header();

$table = new html_table();
$table->id = 'core-search-areas';
// FIXME: lang string moves.
$table->head = array(get_string('searcharea', 'search'), get_string('enable'),
    get_string('newestdocindexed', 'report_search'), get_string('lastrun', 'report_search'), 'Index actions');

foreach ($areasbycomponent as $component => $areas) {
    $header = new html_table_cell(get_string('pluginname', $component));
    $header->header = true;
    $header->colspan = count($table->head);
    $table->data[] = new html_table_row(array($header));

    foreach ($areas as $area) {
        $areaid = $area->get_area_id();
        $columns = array(new html_table_cell($area->get_visible_name()));

        if ($area->is_enabled()) {
            $columns[] = $OUTPUT->action_icon(admin_searcharea_action_url($areaid, 'disable'),
                new pix_icon('t/hide', get_string('disable'), 'moodle', array('title' => '', 'class' => 'iconsmall')),
                null, array('title' => get_string('disable')));

            if ($areasconfig) {
                $columns[] = $areasconfig[$areaid]->lastindexrun;

                if ($areasconfig[$areaid]->indexingstart) {
                    $timediff = $areasconfig[$areaid]->indexingend - $areasconfig[$areaid]->indexingstart;
                    $laststatus = $timediff . ' , ' .
                        $areasconfig[$areaid]->docsprocessed . ' , ' .
                        $areasconfig[$areaid]->recordsprocessed . ' , ' .
                        $areasconfig[$areaid]->docsignored;
                } else {
                    $laststatus = '';
                }
                $columns[] = $laststatus;
                $columns[] = html_writer::link(admin_searcharea_action_url($areaid, 'delete'), 'Delete index');

            } else {
                $blankrow = new html_table_cell('Global search is disabled'); // FIXME.
                $blankrow->colspan = 3;
                $columns[] = $blankrow;
            }

        } else {
            $columns[] = $OUTPUT->action_icon(admin_searcharea_action_url($areaid, 'enable'),
                new pix_icon('t/show', get_string('enable'), 'moodle', array('title' => '', 'class' => 'iconsmall')),
                    null, array('title' => get_string('enable')));

            $blankrow = new html_table_cell('Search area disabled'); // FIXME.
            $blankrow->colspan = 3;
            $columns[] = $blankrow;
        }
        $row = new html_table_row($columns);
        $table->data[] = $row;
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();

/**
 * Helper for generating url for management actions
 * @param $searcharea
 * @param $action
 * @return moodle_url
 */
function admin_searcharea_action_url($searcharea, $action) {
    return new moodle_url('/admin/searchareas.php', array('action' => $action, 'searcharea' => $searcharea,
        'sesskey' => sesskey()));
}