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
require_once(__DIR__ . '/../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('searchareas');

$areaid = optional_param('areaid', null, PARAM_ALPHAEXT);
$action = optional_param('action', null, PARAM_ALPHA);

try {
    $searchmanager = \core_search\manager::instance();
} catch (core_search\engine_exception $searchmanagererror) {
    // Continue, we return an error later depending on the requested action.
}

echo $OUTPUT->header();

if ($action) {
    require_sesskey();

    if ($areaid) {
        // We need to check that the area exists.
        $area = \core_search\manager::get_search_area($areaid);
        if ($area === false) {
            throw new moodle_exception('invalidrequest');
        }
    }

    // All actions but enable/disable need the search engine to be ready.
    if ($action !== 'enable' && $action !== 'disable') {
        if (!empty($searchmanagererror)) {
            throw $searchmanagererror;
        }
    }

    switch ($action) {
        case 'enable':
            $area->set_enabled(true);
            echo $OUTPUT->notification(get_string('searchareaenabled', 'admin'), \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'disable':
            $area->set_enabled(false);
            echo $OUTPUT->notification(get_string('searchareadisabled', 'admin'), \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'delete':
            $search = \core_search\manager::instance();
            $search->delete_index($areaid);
            echo $OUTPUT->notification(get_string('searchindexdeleted', 'admin'), \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'indexall':
            $searchmanager->index();
            echo $OUTPUT->notification(get_string('searchindexupdated', 'admin'), \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'reindexall':
            $searchmanager->index(true);
            echo $OUTPUT->notification(get_string('searchreindexed', 'admin'), \core\output\notification::NOTIFY_SUCCESS);
            break;
        case 'deleteall':
            $searchmanager->delete_index();
            echo $OUTPUT->notification(get_string('searchalldeleted', 'admin'), \core\output\notification::NOTIFY_SUCCESS);
            break;
        default:
            throw new moodle_exception('invalidaction');
            break;
    }
}

$searchareas = \core_search\manager::get_search_areas_list();
if (empty($searchmanagererror)) {
    $areasconfig = $searchmanager->get_areas_config($searchareas);
} else {
    $areasconfig = false;
}

if (!empty($searchmanagererror)) {
    $errorstr = get_string($searchmanagererror->errorcode, $searchmanagererror->module, $searchmanagererror->a);
    echo $OUTPUT->notification($errorstr, \core\output\notification::NOTIFY_ERROR);
} else {
    echo $OUTPUT->notification(get_string('indexinginfo', 'admin'), \core\output\notification::NOTIFY_INFO);
}

$table = new html_table();
$table->id = 'core-search-areas';

$table->head = array(get_string('searcharea', 'search'), get_string('enable'), get_string('newestdocindexed', 'admin'),
    get_string('searchlastrun', 'admin'), get_string('searchindexactions', 'admin'));

foreach ($searchareas as $area) {
    $areaid = $area->get_area_id();
    $columns = array(new html_table_cell($area->get_visible_name()));

    if ($area->is_enabled()) {
        $columns[] = $OUTPUT->action_icon(admin_searcharea_action_url('disable', $areaid),
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
            $columns[] = html_writer::link(admin_searcharea_action_url('delete', $areaid), 'Delete index');

        } else {
            $blankrow = new html_table_cell(get_string('searchnotavailable', 'admin'));
            $blankrow->colspan = 3;
            $columns[] = $blankrow;
        }

    } else {
        $columns[] = $OUTPUT->action_icon(admin_searcharea_action_url('enable', $areaid),
            new pix_icon('t/show', get_string('enable'), 'moodle', array('title' => '', 'class' => 'iconsmall')),
                null, array('title' => get_string('enable')));

        $blankrow = new html_table_cell(get_string('searchareadisabled', 'admin'));
        $blankrow->colspan = 3;
        $columns[] = $blankrow;
    }
    $row = new html_table_row($columns);
    $table->data[] = $row;
}

// Cross-search area tasks.
$options = array();
if (!empty($searchmanagererror)) {
    $options['disabled'] = true;
}
echo $OUTPUT->box_start('search-areas-actions');
echo $OUTPUT->single_button(admin_searcharea_action_url('indexall'), get_string('searchupdateindex', 'admin'), 'get', $options);
echo $OUTPUT->single_button(admin_searcharea_action_url('reindexall'), get_string('searchreindexindex', 'admin'), 'get', $options);
echo $OUTPUT->single_button(admin_searcharea_action_url('deleteall'), get_string('searchdeleteindex', 'admin'), 'get', $options);
echo $OUTPUT->box_end();

echo html_writer::table($table);
echo $OUTPUT->footer();

/**
 * Helper for generating url for management actions.
 *
 * @param string $action
 * @param string $areaid
 * @return moodle_url
 */
function admin_searcharea_action_url($action, $areaid = false) {
    $params = array('action' => $action, 'sesskey' => sesskey());
    if ($areaid) {
        $params['areaid'] = $areaid;
    }
    return new moodle_url('/admin/searchareas.php', $params);
}
