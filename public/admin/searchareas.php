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

$areaid = optional_param('areaid', null, PARAM_ALPHANUMEXT);
$action = optional_param('action', null, PARAM_ALPHA);
$indexingenabled = \core_search\manager::is_indexing_enabled(); // This restricts many of the actions on this page.

// Get a search manager instance, which we'll need for display and to handle some actions.
try {
    $searchmanager = \core_search\manager::instance();
} catch (core_search\engine_exception $searchmanagererror) {
    // In action cases, we'll throw this exception below. In non-action cases, we produce a lang string error.
}

$PAGE->set_primary_active_tab('siteadminnode');

// Handle all the actions.
if ($action) {
    // If dealing with an areaid, we need to check that the area exists.
    if ($areaid) {
        $area = \core_search\manager::get_search_area($areaid);
        if ($area === false) {
            throw new moodle_exception('invalidrequest');
        }
    }

    // All the indexing actions.
    if (in_array($action, ['delete', 'indexall', 'reindexall', 'deleteall'])) {

        // All of these actions require that indexing is enabled.
        if ($indexingenabled) {

            // For all of these actions, we strictly need a manager instance.
            if (isset($searchmanagererror)) {
                throw $searchmanagererror;
            }

            // Show confirm prompt for all these actions as they may be inadvisable, or may cause
            // an interruption in search functionality, on production systems.
            if (!optional_param('confirm', 0, PARAM_INT)) {
                // Display confirmation prompt.
                $a = null;
                if ($areaid) {
                    $a = html_writer::tag('strong', $area->get_visible_name());
                }

                $actionparams = ['sesskey' => sesskey(), 'action' => $action, 'confirm' => 1];
                if ($areaid) {
                    $actionparams['areaid'] = $areaid;
                }
                $actionurl = new moodle_url('/admin/searchareas.php', $actionparams);
                $cancelurl = new moodle_url('/admin/searchareas.php');
                echo $OUTPUT->header();
                echo $OUTPUT->confirm(get_string('confirm_' . $action, 'search', $a),
                    new single_button($actionurl, get_string('continue'), 'post', single_button::BUTTON_PRIMARY),
                    new single_button($cancelurl, get_string('cancel'), 'get'));
                echo $OUTPUT->footer();
                exit;
            } else {
                // Confirmed, so run the required action.
                require_sesskey();

                switch ($action) {
                    case 'delete':
                        $searchmanager->delete_index($areaid);
                        \core\notification::success(get_string('searchindexdeleted', 'admin'));
                        break;
                    case 'indexall':
                        $searchmanager->index();
                        \core\notification::success(get_string('searchindexupdated', 'admin'));
                        break;
                    case 'reindexall':
                        $searchmanager->index(true);
                        \core\notification::success(get_string('searchreindexed', 'admin'));
                        break;
                    case 'deleteall':
                        $searchmanager->delete_index();
                        \core\notification::success(get_string('searchalldeleted', 'admin'));
                        break;
                    default:
                        break;
                }

                // Redirect back to the main page after taking action.
                redirect(new moodle_url('/admin/searchareas.php'));
            }
        }
    } else if (in_array($action, ['enable', 'disable'])) {
        // Toggling search areas requires no confirmation.
        require_sesskey();

        switch ($action) {
            case 'enable':
                $area->set_enabled(true);
                \core\notification::success(get_string('searchareaenabled', 'admin'));
                break;
            case 'disable':
                $area->set_enabled(false);
                core\notification::success(get_string('searchareadisabled', 'admin'));
                break;
            default:
                break;
        }

        redirect(new moodle_url('/admin/searchareas.php'));
    } else {
        // Invalid action.
        throw new moodle_exception('invalidaction');
    }
}


// Display.
if (isset($searchmanager) && $indexingenabled) {
    \core\notification::info(get_string('indexinginfo', 'admin'));
} else if (isset($searchmanager)) {
    $params = (object) [
        'url' => (new moodle_url("/admin/settings.php?section=manageglobalsearch#admin-searchindexwhendisabled"))->out(false)
    ];
    \core\notification::error(get_string('indexwhendisabledfullnotice', 'search', $params));
} else {
    // In non-action cases, init errors are translated and displayed to the user as error notifications.
    $errorstr = get_string($searchmanagererror->errorcode, $searchmanagererror->module, $searchmanagererror->a);
    \core\notification::error($errorstr);
}

echo $OUTPUT->header();

$table = new html_table();
$table->id = 'core-search-areas';
$table->head = [
    get_string('searcharea', 'search'),
    get_string('searchareacategories', 'search'),
    get_string('enable'),
    get_string('newestdocindexed', 'admin'),
    get_string('searchlastrun', 'admin'),
    get_string('searchindexactions', 'admin')
];

$searchareas = \core_search\manager::get_search_areas_list();
core_collator::asort_objects_by_method($searchareas, 'get_visible_name');
$areasconfig = isset($searchmanager) ? $searchmanager->get_areas_config($searchareas) : false;
foreach ($searchareas as $area) {
    $areaid = $area->get_area_id();
    $columns = array(new html_table_cell($area->get_visible_name()));

    $areacategories = [];
    foreach (\core_search\manager::get_search_area_categories() as $category) {
        if (key_exists($areaid, $category->get_areas())) {
            $areacategories[] = $category->get_visiblename();
        }
    }
    $columns[] = new html_table_cell(implode(', ', $areacategories));

    if ($area->is_enabled()) {
        $columns[] = $OUTPUT->action_icon(admin_searcharea_action_url('disable', $areaid),
            new pix_icon('t/hide', get_string('disable'), 'moodle', array('title' => '', 'class' => 'iconsmall')),
            null, array('title' => get_string('disable')));

        if ($areasconfig && $indexingenabled) {
            $columns[] = $areasconfig[$areaid]->lastindexrun;

            if ($areasconfig[$areaid]->indexingstart) {
                $timediff = $areasconfig[$areaid]->indexingend - $areasconfig[$areaid]->indexingstart;
                $laststatus = $timediff . ' , ' .
                    $areasconfig[$areaid]->docsprocessed . ' , ' .
                    $areasconfig[$areaid]->recordsprocessed . ' , ' .
                    $areasconfig[$areaid]->docsignored;
                if ($areasconfig[$areaid]->partial) {
                    $laststatus .= ' ' . get_string('searchpartial', 'admin');
                }
            } else {
                $laststatus = '';
            }
            $columns[] = $laststatus;
            $accesshide = html_writer::span($area->get_visible_name(), 'accesshide');
            $actions = [];
            $actions[] = $OUTPUT->pix_icon('t/delete', '') .
                    html_writer::link(admin_searcharea_action_url('delete', $areaid),
                    get_string('deleteindex', 'search', $accesshide));
            if ($area->supports_get_document_recordset()) {
                $actions[] = $OUTPUT->pix_icon('i/reload', '') . html_writer::link(
                        new moodle_url('searchreindex.php', ['areaid' => $areaid]),
                        get_string('gradualreindex', 'search', $accesshide));
            }
            $columns[] = html_writer::alist($actions, ['class' => 'unstyled list-unstyled']);

        } else {
            if (!$areasconfig) {
                $blankrow = new html_table_cell(get_string('searchnotavailable', 'admin'));
            } else {
                $blankrow = new html_table_cell(get_string('indexwhendisabledshortnotice', 'search'));
            }
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
$options = (isset($searchmanager) && $indexingenabled) ? [] : ['disabled' => true];
echo $OUTPUT->box_start('search-areas-actions');
echo $OUTPUT->single_button(admin_searcharea_action_url('indexall'), get_string('searchupdateindex', 'admin'), 'get', $options);
echo $OUTPUT->single_button(admin_searcharea_action_url('reindexall'), get_string('searchreindexindex', 'admin'), 'get', $options);
echo $OUTPUT->single_button(admin_searcharea_action_url('deleteall'), get_string('searchdeleteindex', 'admin'), 'get', $options);
echo $OUTPUT->box_end();

echo html_writer::table($table);

if (isset($searchmanager)) {
    // Show information about queued index requests for specific contexts.
    $searchrenderer = $PAGE->get_renderer('core_search');
    echo $searchrenderer->render_index_requests_info($searchmanager->get_index_requests_info());
}

echo $OUTPUT->footer();

/**
 * Helper for generating url for management actions.
 *
 * @param string $action
 * @param string $areaid
 * @return moodle_url
 */
function admin_searcharea_action_url($action, $areaid = false) {
    $params = array('action' => $action);
    if ($areaid) {
        $params['areaid'] = $areaid;
    }
    if ($action === 'disable' || $action === 'enable') {
        $params['sesskey'] = sesskey();
    }
    return new moodle_url('/admin/searchareas.php', $params);
}
