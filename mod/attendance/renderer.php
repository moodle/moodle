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
 * Attendance module renderering methods
 *
 * @package    mod_attendance
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/renderables.php');
require_once(dirname(__FILE__).'/renderhelpers.php');
require_once($CFG->libdir.'/tablelib.php');

/**
 * Attendance module renderer class
 *
 * @copyright  2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_attendance_renderer extends plugin_renderer_base {
    // External API - methods to render attendance renderable components.

    /**
     * Renders tabs for attendance
     *
     * @param attendance_tabs $atttabs - tabs to display
     * @return string html code
     */
    protected function render_attendance_tabs(attendance_tabs $atttabs) {
        return print_tabs($atttabs->get_tabs(), $atttabs->currenttab, null, null, true);
    }

    /**
     * Renders filter controls for attendance
     *
     * @param attendance_filter_controls $fcontrols - filter controls data to display
     * @return string html code
     */
    protected function render_attendance_filter_controls(attendance_filter_controls $fcontrols) {
        $filtertable = new html_table();
        $filtertable->attributes['class'] = ' ';
        $filtertable->width = '100%';
        $filtertable->align = array('left', 'center', 'right', 'right');

        $filtertable->data[0][] = $this->render_sess_group_selector($fcontrols);

        $filtertable->data[0][] = $this->render_curdate_controls($fcontrols);

        $filtertable->data[0][] = $this->render_paging_controls($fcontrols);

        $filtertable->data[0][] = $this->render_view_controls($fcontrols);

        $o = html_writer::table($filtertable);
        $o = $this->output->container($o, 'attfiltercontrols');

        return $o;
    }

    /**
     * Render group selector
     *
     * @param attendance_filter_controls $fcontrols
     * @return mixed|string
     */
    protected function render_sess_group_selector(attendance_filter_controls $fcontrols) {
        switch ($fcontrols->pageparams->selectortype) {
            case mod_attendance_page_with_filter_controls::SELECTOR_SESS_TYPE:
                $sessgroups = $fcontrols->get_sess_groups_list();
                if ($sessgroups) {
                    $select = new single_select($fcontrols->url(), 'group', $sessgroups,
                                                $fcontrols->get_current_sesstype(), null, 'selectgroup');
                    $select->label = get_string('sessions', 'attendance');
                    $output = $this->output->render($select);

                    return html_writer::tag('div', $output, array('class' => 'groupselector'));
                }
                break;
            case mod_attendance_page_with_filter_controls::SELECTOR_GROUP:
                return groups_print_activity_menu($fcontrols->cm, $fcontrols->url(), true);
        }

        return '';
    }

    /**
     * Render paging controls.
     *
     * @param attendance_filter_controls $fcontrols
     * @return string
     */
    protected function render_paging_controls(attendance_filter_controls $fcontrols) {
        $pagingcontrols = '';

        $group = 0;
        if (!empty($fcontrols->pageparams->group)) {
            $group = $fcontrols->pageparams->group;
        }

        $totalusers = count_enrolled_users(context_module::instance($fcontrols->cm->id), 'mod/attendance:canbelisted', $group);

        if (empty($fcontrols->pageparams->page) || !$fcontrols->pageparams->page || !$totalusers ||
            empty($fcontrols->pageparams->perpage)) {

            return $pagingcontrols;
        }

        $numberofpages = ceil($totalusers / $fcontrols->pageparams->perpage);

        if ($fcontrols->pageparams->page > 1) {
            $pagingcontrols .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->curdate,
                                                                       'page' => $fcontrols->pageparams->page - 1)),
                                                                 $this->output->larrow());
        }
        $pagingcontrols .= html_writer::tag('span', "Page {$fcontrols->pageparams->page} of $numberofpages",
                                            array('class' => 'attbtn'));
        if ($fcontrols->pageparams->page < $numberofpages) {
            $pagingcontrols .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->curdate,
                                                                       'page' => $fcontrols->pageparams->page + 1)),
                                                                 $this->output->rarrow());
        }

        return $pagingcontrols;
    }

    /**
     * Render date controls.
     *
     * @param attendance_filter_controls $fcontrols
     * @return string
     */
    protected function render_curdate_controls(attendance_filter_controls $fcontrols) {
        global $CFG;

        $curdatecontrols = '';
        if ($fcontrols->curdatetxt) {
            $this->page->requires->strings_for_js(array('calclose', 'caltoday'), 'attendance');
            $jsvals = array(
                    'cal_months'    => explode(',', get_string('calmonths', 'attendance')),
                    'cal_week_days' => explode(',', get_string('calweekdays', 'attendance')),
                    'cal_start_weekday' => $CFG->calendar_startwday,
                    'cal_cur_date'  => $fcontrols->curdate);
            $curdatecontrols = html_writer::script(js_writer::set_variable('M.attendance', $jsvals));

            $this->page->requires->js('/mod/attendance/calendar.js');

            $curdatecontrols .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->prevcur)),
                                                                         $this->output->larrow());
            $params = array(
                    'title' => get_string('calshow', 'attendance'),
                    'id'    => 'show',
                    'class' => 'btn btn-secondary',
                    'type'  => 'button');
            $buttonform = html_writer::tag('button', $fcontrols->curdatetxt, $params);
            foreach ($fcontrols->url_params(array('curdate' => '')) as $name => $value) {
                $params = array(
                        'type'  => 'hidden',
                        'id'    => $name,
                        'name'  => $name,
                        'value' => $value);
                $buttonform .= html_writer::empty_tag('input', $params);
            }
            $params = array(
                    'id'        => 'currentdate',
                    'action'    => $fcontrols->url_path(),
                    'method'    => 'post'
            );

            $buttonform = html_writer::tag('form', $buttonform, $params);
            $curdatecontrols .= $buttonform;

            $curdatecontrols .= html_writer::link($fcontrols->url(array('curdate' => $fcontrols->nextcur)),
                                                                         $this->output->rarrow());
        }

        return $curdatecontrols;
    }

    /**
     * Render view controls.
     *
     * @param attendance_filter_controls $fcontrols
     * @return string
     */
    protected function render_view_controls(attendance_filter_controls $fcontrols) {
        $views[ATT_VIEW_ALL] = get_string('all', 'attendance');
        $views[ATT_VIEW_ALLPAST] = get_string('allpast', 'attendance');
        if ($fcontrols->reportcontrol  && $fcontrols->att->grade > 0) {
            $views[ATT_VIEW_NOTPRESENT] = get_string('lowgrade', 'attendance');
        }
        $views[ATT_VIEW_MONTHS] = get_string('months', 'attendance');
        $views[ATT_VIEW_WEEKS] = get_string('weeks', 'attendance');
        $views[ATT_VIEW_DAYS] = get_string('days', 'attendance');
        if ($fcontrols->reportcontrol) {
            $views[ATT_VIEW_SUMMARY] = get_string('summary', 'attendance');
        }
        $viewcontrols = '';
        foreach ($views as $key => $sview) {
            if ($key != $fcontrols->pageparams->view) {
                $link = html_writer::link($fcontrols->url(array('view' => $key)), $sview);
                $viewcontrols .= html_writer::tag('span', $link, array('class' => 'attbtn'));
            } else {
                $viewcontrols .= html_writer::tag('span', $sview, array('class' => 'attcurbtn'));
            }
        }

        return html_writer::tag('nobr', $viewcontrols);
    }

    /**
     * Renders attendance sessions managing table
     *
     * @param attendance_manage_data $sessdata to display
     * @return string html code
     */
    protected function render_attendance_manage_data(attendance_manage_data $sessdata) {
        $o = $this->render_sess_manage_table($sessdata) . $this->render_sess_manage_control($sessdata);
        $o = html_writer::tag('form', $o, array('method' => 'post', 'action' => $sessdata->url_sessions()->out()));
        $o = $this->output->container($o, 'generalbox attwidth');
        $o = $this->output->container($o, 'attsessions_manage_table');

        return $o;
    }

    /**
     * Render session manage table.
     *
     * @param attendance_manage_data $sessdata
     * @return string
     */
    protected function render_sess_manage_table(attendance_manage_data $sessdata) {
        $this->page->requires->js_init_call('M.mod_attendance.init_manage');

        $table = new html_table();
        $table->width = '100%';
        $table->head = array(
                '#',
                get_string('date'),
                get_string('time'),
                get_string('sessiontypeshort', 'attendance'),
                get_string('description', 'attendance'),
                get_string('actions'),
                html_writer::checkbox('cb_selector', 0, false, '', array('id' => 'cb_selector'))
            );
        $table->align = array('', 'right', '', '', 'left', 'right', 'center');
        $table->size = array('1px', '1px', '1px', '', '*', '120px', '1px');

        $i = 0;
        foreach ($sessdata->sessions as $key => $sess) {
            $i++;

            $dta = $this->construct_date_time_actions($sessdata, $sess);

            $table->data[$sess->id][] = $i;
            $table->data[$sess->id][] = $dta['date'];
            $table->data[$sess->id][] = $dta['time'];
            if ($sess->groupid) {
                if (empty($sessdata->groups[$sess->groupid])) {
                    $table->data[$sess->id][] = get_string('deletedgroup', 'attendance');
                    // Remove actions and links on date/time.
                    $dta['actions'] = '';
                    $dta['date'] = userdate($sess->sessdate, get_string('strftimedmyw', 'attendance'));
                    $dta['time'] = $this->construct_time($sess->sessdate, $sess->duration);
                } else {
                    $table->data[$sess->id][] = get_string('group') . ': ' . $sessdata->groups[$sess->groupid]->name;
                }
            } else {
                $table->data[$sess->id][] = get_string('commonsession', 'attendance');
            }
            $table->data[$sess->id][] = $sess->description;
            $table->data[$sess->id][] = $dta['actions'];
            $table->data[$sess->id][] = html_writer::checkbox('sessid[]', $sess->id, false, '',
                                                              array('class' => 'attendancesesscheckbox'));
        }

        return html_writer::table($table);
    }

    /**
     * Implementation of user image rendering.
     *
     * @param attendance_password_icon $helpicon A help icon instance
     * @return string HTML fragment
     */
    protected function render_attendance_password_icon(attendance_password_icon $helpicon) {
        return $this->render_from_template('attendance/attendance_password_icon', $helpicon->export_for_template($this));
    }
    /**
     * Construct date time actions.
     *
     * @param attendance_manage_data $sessdata
     * @param stdClass $sess
     * @return array
     */
    private function construct_date_time_actions(attendance_manage_data $sessdata, $sess) {
        $actions = '';
        if (!empty($sess->studentpassword) &&
            (has_capability('mod/attendance:manageattendances', $sessdata->att->context) ||
            has_capability('mod/attendance:takeattendances', $sessdata->att->context) ||
            has_capability('mod/attendance:changeattendances', $sessdata->att->context))) {

            $icon = new attendance_password_icon($sess->studentpassword, $sess->id);
            $actions .= $this->render($icon);
        }

        $date = userdate($sess->sessdate, get_string('strftimedmyw', 'attendance'));
        $time = $this->construct_time($sess->sessdate, $sess->duration);
        if ($sess->lasttaken > 0) {
            if (has_capability('mod/attendance:changeattendances', $sessdata->att->context)) {
                $url = $sessdata->url_take($sess->id, $sess->groupid);
                $title = get_string('changeattendance', 'attendance');

                $date = html_writer::link($url, $date, array('title' => $title));
                $time = html_writer::link($url, $time, array('title' => $title));

                $actions .= $this->output->action_icon($url, new pix_icon('redo', $title, 'attendance'));
            } else {
                $date = '<i>' . $date . '</i>';
                $time = '<i>' . $time . '</i>';
            }
        } else {
            if (has_capability('mod/attendance:takeattendances', $sessdata->att->context)) {
                $url = $sessdata->url_take($sess->id, $sess->groupid);
                $title = get_string('takeattendance', 'attendance');
                $actions .= $this->output->action_icon($url, new pix_icon('t/go', $title));
            }
        }

        if (has_capability('mod/attendance:manageattendances', $sessdata->att->context)) {
            $url = $sessdata->url_sessions($sess->id, mod_attendance_sessions_page_params::ACTION_UPDATE);
            $title = get_string('editsession', 'attendance');
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', $title));

            $url = $sessdata->url_sessions($sess->id, mod_attendance_sessions_page_params::ACTION_DELETE);
            $title = get_string('deletesession', 'attendance');
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', $title));
        }

        return array('date' => $date, 'time' => $time, 'actions' => $actions);
    }

    /**
     * Render session manage control.
     *
     * @param attendance_manage_data $sessdata
     * @return string
     */
    protected function render_sess_manage_control(attendance_manage_data $sessdata) {
        $table = new html_table();
        $table->attributes['class'] = ' ';
        $table->width = '100%';
        $table->align = array('left', 'right');

        $table->data[0][] = $this->output->help_icon('hiddensessions', 'attendance',
                get_string('hiddensessions', 'attendance').': '.$sessdata->hiddensessionscount);

        if (has_capability('mod/attendance:manageattendances', $sessdata->att->context)) {
            if ($sessdata->hiddensessionscount > 0) {
                $attributes = array(
                        'type'  => 'submit',
                        'name'  => 'deletehiddensessions',
                        'class' => 'btn btn-secondary',
                        'value' => get_string('deletehiddensessions', 'attendance'));
                $table->data[1][] = html_writer::empty_tag('input', $attributes);
            }

            $options = array(mod_attendance_sessions_page_params::ACTION_DELETE_SELECTED => get_string('delete'),
                mod_attendance_sessions_page_params::ACTION_CHANGE_DURATION => get_string('changeduration', 'attendance'));

            $controls = html_writer::select($options, 'action');
            $attributes = array(
                    'type'  => 'submit',
                    'name'  => 'ok',
                    'value' => get_string('ok'),
                    'class' => 'btn btn-secondary');
            $controls .= html_writer::empty_tag('input', $attributes);
        } else {
            $controls = get_string('youcantdo', 'attendance'); // You can't do anything.
        }
        $table->data[0][] = $controls;

        return html_writer::table($table);
    }

    /**
     * Render take data.
     *
     * @param attendance_take_data $takedata
     * @return string
     */
    protected function render_attendance_take_data(attendance_take_data $takedata) {
        $controls = $this->render_attendance_take_controls($takedata);
        $table = html_writer::start_div('no-overflow');
        if ($takedata->pageparams->viewmode == mod_attendance_take_page_params::SORTED_LIST) {
            $table .= $this->render_attendance_take_list($takedata);
        } else {
            $table .= $this->render_attendance_take_grid($takedata);
        }
        $table .= html_writer::input_hidden_params($takedata->url(array('sesskey' => sesskey(),
                                                                        'page' => $takedata->pageparams->page)));
        $table .= html_writer::end_div();
        $params = array(
                'type'  => 'submit',
                'class' => 'btn btn-primary',
                'value' => get_string('save', 'attendance'));
        $table .= html_writer::tag('center', html_writer::empty_tag('input', $params));
        $table = html_writer::tag('form', $table, array('method' => 'post', 'action' => $takedata->url_path(),
                                                        'id' => 'attendancetakeform'));

        foreach ($takedata->statuses as $status) {
            $sessionstats[$status->id] = 0;
        }
        // Calculate the sum of statuses for each user.
        $sessionstats[] = array();
        foreach ($takedata->sessionlog as $userlog) {
            foreach ($takedata->statuses as $status) {
                if ($userlog->statusid == $status->id) {
                    $sessionstats[$status->id]++;
                }
            }
        }

        $statsoutput = '<br/>';
        foreach ($takedata->statuses as $status) {
            $statsoutput .= "$status->description = ".$sessionstats[$status->id]." <br/>";
        }

        return $controls.$table.$statsoutput;
    }

    /**
     * Render take controls.
     *
     * @param attendance_take_data $takedata
     * @return string
     */
    protected function render_attendance_take_controls(attendance_take_data $takedata) {
        $table = new html_table();
        $table->attributes['class'] = ' ';

        $table->data[0][] = $this->construct_take_session_info($takedata);
        $table->data[0][] = $this->construct_take_controls($takedata);

        return $this->output->container(html_writer::table($table), 'generalbox takecontrols');
    }

    /**
     * Construct take session info.
     *
     * @param attendance_take_data $takedata
     * @return string
     */
    private function construct_take_session_info(attendance_take_data $takedata) {
        $sess = $takedata->sessioninfo;
        $date = userdate($sess->sessdate, get_string('strftimedate'));
        $starttime = attendance_strftimehm($sess->sessdate);
        $endtime = attendance_strftimehm($sess->sessdate + $sess->duration);
        $time = html_writer::tag('nobr', $starttime . ($sess->duration > 0 ? ' - ' . $endtime : ''));
        $sessinfo = $date.' '.$time;
        $sessinfo .= html_writer::empty_tag('br');
        $sessinfo .= html_writer::empty_tag('br');
        $sessinfo .= $sess->description;

        return $sessinfo;
    }

    /**
     * Construct take controls.
     *
     * @param attendance_take_data $takedata
     * @return string
     */
    private function construct_take_controls(attendance_take_data $takedata) {

        $controls = '';
        $context = context_module::instance($takedata->cm->id);
        $group = 0;
        if ($takedata->pageparams->grouptype != mod_attendance_structure::SESSION_COMMON) {
            $group = $takedata->pageparams->grouptype;
        } else {
            if ($takedata->pageparams->group) {
                $group = $takedata->pageparams->group;
            }
        }

        if (!empty($takedata->cm->groupingid)) {
            if ($group == 0) {
                $groups = array_keys(groups_get_all_groups($takedata->cm->course, 0, $takedata->cm->groupingid, 'g.id'));
            } else {
                $groups = $group;
            }
            $users = get_users_by_capability($context, 'mod/attendance:canbelisted',
                            'u.id, u.firstname, u.lastname, u.email',
                            '', '', '', $groups,
                            '', false, true);
            $totalusers = count($users);
        } else {
            $totalusers = count_enrolled_users($context, 'mod/attendance:canbelisted', $group);
        }
        $usersperpage = $takedata->pageparams->perpage;
        if (!empty($takedata->pageparams->page) && $takedata->pageparams->page && $totalusers && $usersperpage) {
            $controls .= html_writer::empty_tag('br');
            $numberofpages = ceil($totalusers / $usersperpage);

            if ($takedata->pageparams->page > 1) {
                $controls .= html_writer::link($takedata->url(array('page' => $takedata->pageparams->page - 1)),
                                                              $this->output->larrow());
            }
            $controls .= html_writer::tag('span', "Page {$takedata->pageparams->page} of $numberofpages",
                                          array('class' => 'attbtn'));
            if ($takedata->pageparams->page < $numberofpages) {
                $controls .= html_writer::link($takedata->url(array('page' => $takedata->pageparams->page + 1,
                            'perpage' => $takedata->pageparams->perpage)), $this->output->rarrow());
            }
        }

        if ($takedata->pageparams->grouptype == mod_attendance_structure::SESSION_COMMON and
                ($takedata->groupmode == VISIBLEGROUPS or
                ($takedata->groupmode and has_capability('moodle/site:accessallgroups', $context)))) {
            $controls .= groups_print_activity_menu($takedata->cm, $takedata->url(), true);
        }

        $controls .= html_writer::empty_tag('br');

        $options = array(
            mod_attendance_take_page_params::SORTED_LIST   => get_string('sortedlist', 'attendance'),
            mod_attendance_take_page_params::SORTED_GRID   => get_string('sortedgrid', 'attendance'));
        $select = new single_select($takedata->url(), 'viewmode', $options, $takedata->pageparams->viewmode, null);
        $select->set_label(get_string('viewmode', 'attendance'));
        $select->class = 'singleselect inline';
        $controls .= $this->output->render($select);

        if ($takedata->pageparams->viewmode == mod_attendance_take_page_params::SORTED_LIST) {
            $options = array(
                    0 => get_string('donotusepaging', 'attendance'),
                   get_config('attendance', 'resultsperpage') => get_config('attendance', 'resultsperpage'));
            $select = new single_select($takedata->url(), 'perpage', $options, $takedata->pageparams->perpage, null);
            $select->class = 'singleselect inline';
            $controls .= $this->output->render($select);
        }

        if ($takedata->pageparams->viewmode == mod_attendance_take_page_params::SORTED_GRID) {
            $options = array (1 => '1 '.get_string('column', 'attendance'), '2 '.get_string('columns', 'attendance'),
                                   '3 '.get_string('columns', 'attendance'), '4 '.get_string('columns', 'attendance'),
                                   '5 '.get_string('columns', 'attendance'), '6 '.get_string('columns', 'attendance'),
                                   '7 '.get_string('columns', 'attendance'), '8 '.get_string('columns', 'attendance'),
                                   '9 '.get_string('columns', 'attendance'), '10 '.get_string('columns', 'attendance'));
            $select = new single_select($takedata->url(), 'gridcols', $options, $takedata->pageparams->gridcols, null);
            $select->class = 'singleselect inline';
            $controls .= $this->output->render($select);
        }

        if (count($takedata->sessions4copy) > 0) {
            $controls .= html_writer::empty_tag('br');
            $controls .= html_writer::empty_tag('br');

            $options = array();
            foreach ($takedata->sessions4copy as $sess) {
                $start = attendance_strftimehm($sess->sessdate);
                $end = $sess->duration ? ' - '.attendance_strftimehm($sess->sessdate + $sess->duration) : '';
                $options[$sess->id] = $start . $end;
            }
            $select = new single_select($takedata->url(array(), array('copyfrom')), 'copyfrom', $options);
            $select->set_label(get_string('copyfrom', 'attendance'));
            $select->class = 'singleselect inline';
            $controls .= $this->output->render($select);
        }

        return $controls;
    }

    /**
     * Render take list.
     *
     * @param attendance_take_data $takedata
     * @return string
     */
    protected function render_attendance_take_list(attendance_take_data $takedata) {
        global $PAGE, $CFG;
        $table = new html_table();
        $table->width = '0%';
        $table->head = array(
                '#',
                $this->construct_fullname_head($takedata)
            );
        $table->align = array('left', 'left');
        $table->size = array('20px', '');
        $table->wrap[1] = 'nowrap';
        // Check if extra useridentity fields need to be added.
        $extrasearchfields = array();
        if (!empty($CFG->showuseridentity) && has_capability('moodle/site:viewuseridentity', $takedata->att->context)) {
            $extrasearchfields = explode(',', $CFG->showuseridentity);
        }
        foreach ($extrasearchfields as $field) {
            $table->head[] = get_string($field);
            $table->align[] = 'left';
        }
        foreach ($takedata->statuses as $st) {
            $table->head[] = html_writer::link("#", $st->acronym, array('id' => 'checkstatus'.$st->id,
                'title' => get_string('setallstatusesto', 'attendance', $st->description)));
            $table->align[] = 'center';
            $table->size[] = '20px';
            // JS to select all radios of this status and prevent default behaviour of # link.
            $PAGE->requires->js_amd_inline("
                require(['jquery'], function($) {
                    $('#checkstatus".$st->id."').click(function(e) {
                        $('#attendancetakeform').find('.st".$st->id."').prop('checked', true);
                        e.preventDefault();
                    });
                });");

        }

        $table->head[] = get_string('remarks', 'attendance');
        $table->align[] = 'center';
        $table->size[] = '20px';
        $table->attributes['class'] = 'generaltable takelist';

        // Show a 'select all' row of radio buttons.
        $row = new html_table_row();
        $row->cells[] = '';
        $row->attributes['class'] = 'setallstatusesrow';
        foreach ($extrasearchfields as $field) {
            $row->cells[] = '';
        }
        $row->cells[] = html_writer::div(get_string('setallstatuses', 'attendance'), 'setallstatuses');
        foreach ($takedata->statuses as $st) {
            $attribs = array(
                'id' => 'radiocheckstatus'.$st->id,
                'type' => 'radio',
                'title' => get_string('setallstatusesto', 'attendance', $st->description),
                'name' => 'setallstatuses',
                'class' => "st{$st->id}",
            );
            $row->cells[] = html_writer::empty_tag('input', $attribs);
            // Select all radio buttons of the same status.
            $PAGE->requires->js_amd_inline("
                require(['jquery'], function($) {
                    $('#radiocheckstatus".$st->id."').click(function(e) {
                        $('#attendancetakeform').find('.st".$st->id."').prop('checked', true);
                    });
                });");
        }
        $row->cells[] = '';
        $table->data[] = $row;

        $i = 0;
        foreach ($takedata->users as $user) {
            $i++;
            $row = new html_table_row();
            $row->cells[] = $i;
            $fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
            $fullname = $this->user_picture($user).$fullname; // Show different picture if it is a temporary user.

            $ucdata = $this->construct_take_user_controls($takedata, $user);
            if (array_key_exists('warning', $ucdata)) {
                $fullname .= html_writer::empty_tag('br');
                $fullname .= $ucdata['warning'];
            }
            $row->cells[] = $fullname;
            foreach ($extrasearchfields as $field) {
                $row->cells[] = $user->$field;
            }

            if (array_key_exists('colspan', $ucdata)) {
                $cell = new html_table_cell($ucdata['text']);
                $cell->colspan = $ucdata['colspan'];
                $row->cells[] = $cell;
            } else {
                $row->cells = array_merge($row->cells, $ucdata['text']);
            }

            if (array_key_exists('class', $ucdata)) {
                $row->attributes['class'] = $ucdata['class'];
            }

            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    /**
     * Render take grid.
     *
     * @param attendance_take_data $takedata
     * @return string
     */
    protected function render_attendance_take_grid(attendance_take_data $takedata) {
        global $PAGE;
        $table = new html_table();
        for ($i = 0; $i < $takedata->pageparams->gridcols; $i++) {
            $table->align[] = 'center';
            $table->size[] = '110px';
        }
        $table->attributes['class'] = 'generaltable takegrid';
        $table->headspan = $takedata->pageparams->gridcols;
        $head = array();
        foreach ($takedata->statuses as $st) {
            $head[] = html_writer::link("#", $st->acronym, array('id' => 'checkstatus'.$st->id,
                                              'title' => get_string('setallstatusesto', 'attendance', $st->description)));
            // JS to select all radios of this status and prevent default behaviour of # link.
            $PAGE->requires->js_amd_inline("
                 require(['jquery'], function($) {
                     $('#checkstatus".$st->id."').click(function(e) {
                         $('#attendancetakeform').find('.st".$st->id."').prop('checked', true);
                         e.preventDefault();
                     });
                 });");
        }
        $table->head[] = implode('&nbsp;&nbsp;', $head);

        $i = 0;
        $row = new html_table_row();
        foreach ($takedata->users as $user) {
            $celltext = $this->user_picture($user, array('size' => 100));  // Show different picture if it is a temporary user.
            $celltext .= html_writer::empty_tag('br');
            $fullname = html_writer::link($takedata->url_view(array('studentid' => $user->id)), fullname($user));
            $celltext .= html_writer::tag('span', $fullname, array('class' => 'fullname'));
            $celltext .= html_writer::empty_tag('br');
            $ucdata = $this->construct_take_user_controls($takedata, $user);
            $celltext .= is_array($ucdata['text']) ? implode('', $ucdata['text']) : $ucdata['text'];
            if (array_key_exists('warning', $ucdata)) {
                $celltext .= html_writer::empty_tag('br');
                $celltext .= $ucdata['warning'];
            }

            $cell = new html_table_cell($celltext);
            if (array_key_exists('class', $ucdata)) {
                $cell->attributes['class'] = $ucdata['class'];
            }
            $row->cells[] = $cell;

            $i++;
            if ($i % $takedata->pageparams->gridcols == 0) {
                $table->data[] = $row;
                $row = new html_table_row();
            }
        }
        if ($i % $takedata->pageparams->gridcols > 0) {
            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    /**
     * Construct full name.
     *
     * @param stdClass $data
     * @return string
     */
    private function construct_fullname_head($data) {
        global $CFG;

        $url = $data->url();
        if ($data->pageparams->sort == ATT_SORT_LASTNAME) {
            $url->param('sort', ATT_SORT_FIRSTNAME);
            $firstname = html_writer::link($url, get_string('firstname'));
            $lastname = get_string('lastname');
        } else if ($data->pageparams->sort == ATT_SORT_FIRSTNAME) {
            $firstname = get_string('firstname');
            $url->param('sort', ATT_SORT_LASTNAME);
            $lastname = html_writer::link($url, get_string('lastname'));
        } else {
            $firstname = html_writer::link($data->url(array('sort' => ATT_SORT_FIRSTNAME)), get_string('firstname'));
            $lastname = html_writer::link($data->url(array('sort' => ATT_SORT_LASTNAME)), get_string('lastname'));
        }

        if ($CFG->fullnamedisplay == 'lastname firstname') {
            $fullnamehead = "$lastname / $firstname";
        } else {
            $fullnamehead = "$firstname / $lastname ";
        }

        return $fullnamehead;
    }

    /**
     * Construct take user controls.
     *
     * @param attendance_take_data $takedata
     * @param stdClass $user
     * @return array
     */
    private function construct_take_user_controls(attendance_take_data $takedata, $user) {
        $celldata = array();
        if ($user->enrolmentend and $user->enrolmentend < $takedata->sessioninfo->sessdate) {
            $celldata['text'] = get_string('enrolmentend', 'attendance', userdate($user->enrolmentend, '%d.%m.%Y'));
            $celldata['colspan'] = count($takedata->statuses) + 1;
            $celldata['class'] = 'userwithoutenrol';
        } else if (!$user->enrolmentend and $user->enrolmentstatus == ENROL_USER_SUSPENDED) {
            // No enrolmentend and ENROL_USER_SUSPENDED.
            $celldata['text'] = get_string('enrolmentsuspended', 'attendance');
            $celldata['colspan'] = count($takedata->statuses) + 1;
            $celldata['class'] = 'userwithoutenrol';
        } else {
            if ($takedata->updatemode and !array_key_exists($user->id, $takedata->sessionlog)) {
                $celldata['class'] = 'userwithoutdata';
            }

            $celldata['text'] = array();
            foreach ($takedata->statuses as $st) {
                $params = array(
                        'type'  => 'radio',
                        'name'  => 'user'.$user->id,
                        'class' => 'st'.$st->id,
                        'value' => $st->id);
                if (array_key_exists($user->id, $takedata->sessionlog) and $st->id == $takedata->sessionlog[$user->id]->statusid) {
                    $params['checked'] = '';
                }

                $input = html_writer::empty_tag('input', $params);

                if ($takedata->pageparams->viewmode == mod_attendance_take_page_params::SORTED_GRID) {
                    $input = html_writer::tag('nobr', $input . $st->acronym);
                }

                $celldata['text'][] = $input;
            }
            $params = array(
                    'type'  => 'text',
                    'name'  => 'remarks'.$user->id,
                    'maxlength' => 255);
            if (array_key_exists($user->id, $takedata->sessionlog)) {
                $params['value'] = $takedata->sessionlog[$user->id]->remarks;
            }
            $celldata['text'][] = html_writer::empty_tag('input', $params);

            if ($user->enrolmentstart > $takedata->sessioninfo->sessdate + $takedata->sessioninfo->duration) {
                $celldata['warning'] = get_string('enrolmentstart', 'attendance',
                                                  userdate($user->enrolmentstart, '%H:%M %d.%m.%Y'));
                $celldata['class'] = 'userwithoutenrol';
            }
        }

        return $celldata;
    }

    /**
     * Render header.
     *
     * @param mod_attendance_header $header
     * @return string
     */
    protected function render_mod_attendance_header(mod_attendance_header $header) {
        if (!$header->should_render()) {
            return '';
        }

        $attendance = $header->get_attendance();

        $heading = format_string($header->get_title(), false, ['context' => $attendance->context]);
        $o = $this->output->heading($heading);

        $o .= $this->output->box_start('generalbox boxaligncenter', 'intro');
        $o .= format_module_intro('attendance', $attendance, $attendance->cm->id);
        $o .= $this->output->box_end();

        return $o;
    }

    /**
     * Render user data.
     *
     * @param attendance_user_data $userdata
     * @return string
     */
    protected function render_attendance_user_data(attendance_user_data $userdata) {
        global $USER;

        $o = $this->render_user_report_tabs($userdata);

        if ($USER->id == $userdata->user->id) {

            $o .= $this->construct_user_data($userdata);

        } else {

            $table = new html_table();

            $table->attributes['class'] = 'userinfobox';
            $table->colclasses = array('left side', '');
            // Show different picture if it is a temporary user.
            $table->data[0][] = $this->user_picture($userdata->user, array('size' => 100));
            $table->data[0][] = $this->construct_user_data($userdata);

            $o .= html_writer::table($table);
        }

        return $o;
    }

    /**
     * Render user report tabs.
     *
     * @param attendance_user_data $userdata
     * @return string
     */
    protected function render_user_report_tabs(attendance_user_data $userdata) {
        $tabs = array();

        $tabs[] = new tabobject(mod_attendance_view_page_params::MODE_THIS_COURSE,
                        $userdata->url()->out(true, array('mode' => mod_attendance_view_page_params::MODE_THIS_COURSE)),
                        get_string('thiscourse', 'attendance'));

        // Skip the 'all courses' tab for 'temporary' users.
        if ($userdata->user->type == 'standard') {
            $tabs[] = new tabobject(mod_attendance_view_page_params::MODE_ALL_COURSES,
                            $userdata->url()->out(true, array('mode' => mod_attendance_view_page_params::MODE_ALL_COURSES)),
                            get_string('allcourses', 'attendance'));
        }

        return print_tabs(array($tabs), $userdata->pageparams->mode, null, null, true);
    }

    /**
     * Construct user data.
     *
     * @param attendance_user_data $userdata
     * @return string
     */
    private function construct_user_data(attendance_user_data $userdata) {
        global $USER;
        $o = '';
        if ($USER->id <> $userdata->user->id) {
            $o = html_writer::tag('h2', fullname($userdata->user));
        }

        if ($userdata->pageparams->mode == mod_attendance_view_page_params::MODE_THIS_COURSE) {
            $o .= $this->render_attendance_filter_controls($userdata->filtercontrols);
            $o .= $this->construct_user_sessions_log($userdata);
            $o .= html_writer::empty_tag('hr');
            $o .= construct_user_data_stat($userdata->summary->get_all_sessions_summary_for($userdata->user->id),
                $userdata->pageparams->view);
        } else {
            $table = new html_table();
            $table->head  = array(get_string('course'),
                get_string('pluginname', 'mod_attendance'),
                get_string('sessionscompleted', 'attendance'),
                get_string('pointssessionscompleted', 'attendance'),
                get_string('percentagesessionscompleted', 'attendance'));
            $table->align = array('left', 'left', 'center', 'center', 'center');
            $table->colclasses = array('colcourse', 'colatt', 'colsessionscompleted',
                                       'colpointssessionscompleted', 'colpercentagesessionscompleted');

            $table2 = clone($table); // Duplicate table for ungraded sessions.
            $totalattendance = 0;
            $totalpercentage = 0;
            foreach ($userdata->coursesatts as $ca) {
                $row = new html_table_row();
                $courseurl = new moodle_url('/course/view.php', array('id' => $ca->courseid));
                $row->cells[] = html_writer::link($courseurl, $ca->coursefullname);
                $attendanceurl = new moodle_url('/mod/attendance/view.php', array('id' => $ca->cmid,
                                                                                      'studentid' => $userdata->user->id,
                                                                                      'view' => ATT_VIEW_ALL));
                $row->cells[] = html_writer::link($attendanceurl, $ca->attname);
                $usersummary = new stdClass();
                if (isset($userdata->summary[$ca->attid])) {
                    $usersummary = $userdata->summary[$ca->attid]->get_all_sessions_summary_for($userdata->user->id);

                    $row->cells[] = $usersummary->numtakensessions;
                    $row->cells[] = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                        format_float($usersummary->takensessionsmaxpoints, 1, true, true);
                    if (empty($usersummary->numtakensessions)) {
                        $row->cells[] = '-';
                    } else {
                        $row->cells[] = format_float($usersummary->takensessionspercentage * 100) . '%';
                    }

                }
                if (empty($ca->attgrade)) {
                    $table2->data[] = $row;
                } else {
                    $table->data[] = $row;
                    if ($usersummary->numtakensessions > 0) {
                        $totalattendance++;
                        $totalpercentage = $totalpercentage + format_float($usersummary->takensessionspercentage * 100);
                    }
                }
            }
            $row = new html_table_row();
            if (empty($totalattendance)) {
                $average = '-';
            } else {
                $average = format_float($totalpercentage / $totalattendance).'%';
            }

            $col = new html_table_cell(get_string('averageattendancegraded', 'mod_attendance'));
            $col->attributes['class'] = 'averageattendance';
            $col->colspan = 4;

            $col2 = new html_table_cell($average);
            $col2->style = 'text-align: center';
            $row->cells = array($col, $col2);
            $table->data[] = $row;

            if (!empty($table2->data) && !empty($table->data)) {
                // Print graded header if both tables are being shown.
                $o .= html_writer::div("<h3>".get_string('graded', 'mod_attendance')."</h3>");
            }
            if (!empty($table->data)) {
                // Don't bother printing the table if no sessions are being shown.
                $o .= html_writer::table($table);
            }

            if (!empty($table2->data)) {
                // Don't print this if it doesn't contain any data.
                $o .= html_writer::div("<h3>".get_string('ungraded', 'mod_attendance')."</h3>");
                $o .= html_writer::table($table2);
            }
        }

        return $o;
    }

    /**
     * Construct user sessions log.
     *
     * @param attendance_user_data $userdata
     * @return string
     */
    private function construct_user_sessions_log(attendance_user_data $userdata) {
        global $OUTPUT, $USER;
        $context = context_module::instance($userdata->filtercontrols->cm->id);

        $shortform = false;
        if ($USER->id == $userdata->user->id) {
            // This is a user viewing their own stuff - hide non-relevant columns.
            $shortform = true;
        }

        $table = new html_table();
        $table->attributes['class'] = 'generaltable attwidth boxaligncenter';
        $table->head = array();
        $table->align = array();
        $table->size = array();
        $table->colclasses = array();
        if (!$shortform) {
            $table->head[] = get_string('sessiontypeshort', 'attendance');
            $table->align[] = '';
            $table->size[] = '1px';
            $table->colclasses[] = '';
        }
        $table->head[] = get_string('date');
        $table->head[] = get_string('description', 'attendance');
        $table->head[] = get_string('status', 'attendance');
        $table->head[] = get_string('points', 'attendance');
        $table->head[] = get_string('remarks', 'attendance');

        $table->align = array_merge($table->align, array('', 'left', 'center', 'center', 'center'));
        $table->colclasses = array_merge($table->colclasses, array('datecol', 'desccol', 'statuscol', 'pointscol', 'remarkscol'));
        $table->size = array_merge($table->size, array('1px', '*', '*', '1px', '*'));

        if (has_capability('mod/attendance:takeattendances', $context)) {
            $table->head[] = get_string('action');
            $table->align[] = '';
            $table->size[] = '';
        }

        $statussetmaxpoints = attendance_get_statusset_maxpoints($userdata->statuses);

        $i = 0;
        foreach ($userdata->sessionslog as $sess) {
            $i++;

            $row = new html_table_row();
            if (!$shortform) {
                if ($sess->groupid) {
                    $sessiontypeshort = get_string('group') . ': ' . $userdata->groups[$sess->groupid]->name;
                } else {
                    $sessiontypeshort = get_string('commonsession', 'attendance');
                }

                $row->cells[] = html_writer::tag('nobr', $sessiontypeshort);
            }
            $row->cells[] = userdate($sess->sessdate, get_string('strftimedmyw', 'attendance')) .
             " ". $this->construct_time($sess->sessdate, $sess->duration);
            $row->cells[] = $sess->description;
            if (!empty($sess->statusid)) {
                $status = $userdata->statuses[$sess->statusid];
                $row->cells[] = $status->description;
                $row->cells[] = format_float($status->grade, 1, true, true) . ' / ' .
                                    format_float($statussetmaxpoints[$status->setnumber], 1, true, true);
                $row->cells[] = $sess->remarks;
            } else if ($sess->sessdate < $userdata->user->enrolmentstart) {
                $cell = new html_table_cell(get_string('enrolmentstart', 'attendance',
                                            userdate($userdata->user->enrolmentstart, '%d.%m.%Y')));
                $cell->colspan = 3;
                $row->cells[] = $cell;
            } else if ($userdata->user->enrolmentend and $sess->sessdate > $userdata->user->enrolmentend) {
                $cell = new html_table_cell(get_string('enrolmentend', 'attendance',
                                            userdate($userdata->user->enrolmentend, '%d.%m.%Y')));
                $cell->colspan = 3;
                $row->cells[] = $cell;
            } else {
                if (attendance_can_student_mark($sess)) {
                    // Student can mark their own attendance.
                    // URL to the page that lets the student modify their attendance.

                    $url = new moodle_url('/mod/attendance/attendance.php',
                            array('sessid' => $sess->id, 'sesskey' => sesskey()));
                    $cell = new html_table_cell(html_writer::link($url, get_string('submitattendance', 'attendance')));
                    $cell->colspan = 3;
                    $row->cells[] = $cell;
                } else { // Student cannot mark their own attendace.
                    $row->cells[] = '?';
                    $row->cells[] = '? / ' . format_float($statussetmaxpoints[$sess->statusset], 1, true, true);
                    $row->cells[] = '';
                }
            }

            if (has_capability('mod/attendance:takeattendances', $context)) {
                $params = array('id' => $userdata->filtercontrols->cm->id,
                    'sessionid' => $sess->id,
                    'grouptype' => $sess->groupid);
                $url = new moodle_url('/mod/attendance/take.php', $params);
                $icon = $OUTPUT->pix_icon('redo', get_string('changeattendance', 'attendance'), 'attendance');
                $row->cells[] = html_writer::link($url, $icon);
            }

            $table->data[] = $row;
        }

        return html_writer::table($table);
    }

    /**
     * Construct time for display.
     *
     * @param int $datetime
     * @param int $duration
     * @return string
     */
    private function construct_time($datetime, $duration) {
        $time = html_writer::tag('nobr', construct_session_time($datetime, $duration));

        return $time;
    }

    /**
     * Render report data.
     *
     * @param attendance_report_data $reportdata
     * @return string
     */
    protected function render_attendance_report_data(attendance_report_data $reportdata) {
        global $PAGE, $COURSE;

        // Initilise Javascript used to (un)check all checkboxes.
        $this->page->requires->js_init_call('M.mod_attendance.init_manage');

        $table = new html_table();
        $table->attributes['class'] = 'generaltable attwidth attreport';

        $userrows = $this->get_user_rows($reportdata);

        if ($reportdata->pageparams->view == ATT_VIEW_SUMMARY) {
            $sessionrows = array();
        } else {
            $sessionrows = $this->get_session_rows($reportdata);
        }

        $setnumber = -1;
        $statusetcount = 0;
        foreach ($reportdata->statuses as $sts) {
            if ($sts->setnumber != $setnumber) {
                $statusetcount++;
                $setnumber = $sts->setnumber;
            }
        }

        $acronymrows = $this->get_acronym_rows($reportdata, true);
        $startwithcontrast = $statusetcount % 2 == 0;
        $summaryrows = $this->get_summary_rows($reportdata, $startwithcontrast);

        // Check if the user should be able to bulk send messages to other users on the course.
        $bulkmessagecapability = has_capability('moodle/course:bulkmessaging', $PAGE->context);
        if ($bulkmessagecapability) {
            $bulkmessagingrows = $this->get_bulkmessage_rows($reportdata);
        }

        // Extract rows from each part and collate them into one row each.
        $sessiondetailsleft = $reportdata->pageparams->sessiondetailspos == 'left';
        foreach ($userrows as $index => $row) {
            $summaryrow = isset($summaryrows[$index]->cells) ? $summaryrows[$index]->cells : array();
            $bulkmessagingrow = isset($bulkmessagingrows[$index]->cells) ? $bulkmessagingrows[$index]->cells : array();
            $sessionrow = isset($sessionrows[$index]->cells) ? $sessionrows[$index]->cells : array();
            if ($sessiondetailsleft) {
                $row->cells = array_merge($row->cells, $sessionrow, $acronymrows[$index]->cells, $summaryrow, $bulkmessagingrow);
            } else {
                $row->cells = array_merge($row->cells, $acronymrows[$index]->cells, $summaryrow, $sessionrow, $bulkmessagingrow);
            }
            $table->data[] = $row;
        }

        if ($bulkmessagecapability) { // Require that the user can bulk message users.
            // Display check boxes that will allow the user to send a message to the students that have been checked.
            $output = html_writer::empty_tag('input', array('name' => 'sesskey', 'type' => 'hidden', 'value' => sesskey()));
            $output .= html_writer::empty_tag('input', array('name' => 'id', 'type' => 'hidden', 'value' => $COURSE->id));
            $output .= html_writer::empty_tag('input', array('name' => 'returnto', 'type' => 'hidden', 'value' => s(me())));
            $output .= html_writer::table($table).html_writer::tag('div', get_string('users').': '.count($reportdata->users));;
            $output .= html_writer::tag('div',
                    html_writer::empty_tag('input', array('type' => 'submit',
                                                                   'value' => get_string('messageselectadd'),
                                                                   'class' => 'btn btn-secondary')),
                    array('class' => 'buttons'));
            $url = new moodle_url('/mod/attendance/messageselect.php');
            return html_writer::tag('form', $output, array('action' => $url->out(), 'method' => 'post'));
        } else {
            return html_writer::table($table).html_writer::tag('div', get_string('users').': '.count($reportdata->users));
        }
    }

    /**
     * Build and return the rows that will make up the left part of the attendance report.
     * This consists of student names and icons, as well as header cells for these columns.
     *
     * @param attendance_report_data $reportdata the report data
     * @return array Array of html_table_row objects
     */
    protected function get_user_rows(attendance_report_data $reportdata) {
        global $OUTPUT;
        $rows = array();
        $extrafields = get_extra_user_fields($reportdata->att->context);
        $showextrauserdetails = $reportdata->pageparams->showextrauserdetails;
        $params = $reportdata->pageparams->get_significant_params();
        $text = get_string('users');
        if ($extrafields) {
            if ($showextrauserdetails) {
                $params['showextrauserdetails'] = 0;
                $url = $reportdata->att->url_report($params);
                $text .= $OUTPUT->action_icon($url, new pix_icon('t/switch_minus',
                            get_string('hideextrauserdetails', 'attendance')), null, null);
            } else {
                $params['showextrauserdetails'] = 1;
                $url = $reportdata->att->url_report($params);
                $text .= $OUTPUT->action_icon($url, new pix_icon('t/switch_plus',
                            get_string('showextrauserdetails', 'attendance')), null, null);
                $extrafields = array();
            }
        }
        $usercolspan = 1 + count($extrafields);

        $row = new html_table_row();
        $row->cells[] = $this->build_header_cell('');
        $row->cells[] = $this->build_header_cell($text, false, false, $usercolspan);
        $rows[] = $row;

        $row = new html_table_row();
        $row->cells[] = $this->build_header_cell('');
        $row->cells[] = $this->build_header_cell($this->construct_fullname_head($reportdata), false, false);

        foreach ($extrafields as $field) {
            $row->cells[] = $this->build_header_cell(get_string($field), false, false);
        }

        $rows[] = $row;

        foreach ($reportdata->users as $user) {
            $row = new html_table_row();
            $row->cells[] = $this->build_data_cell($this->user_picture($user));
            $text = html_writer::link($reportdata->url_view(array('studentid' => $user->id)), fullname($user));
            $row->cells[] = $this->build_data_cell($text, false, false, null, null, false);
            foreach ($extrafields as $field) {
                $row->cells[] = $this->build_data_cell($user->$field, false, false);
            }
            $rows[] = $row;
        }

        $row = new html_table_row();
        $row->cells[] = $this->build_data_cell('');
        $text = ($reportdata->pageparams->view == ATT_VIEW_SUMMARY) ? '' : get_string('summary');
        $row->cells[] = $this->build_data_cell($text, false, true, $usercolspan);
        $rows[] = $row;

        return $rows;
    }

    /**
     * Build and return the rows that will make up the summary part of the attendance report.
     * This consists of countings for each status set acronyms, as well as header cells for these columns.
     *
     * @param attendance_report_data $reportdata the report data
     * @param boolean $startwithcontrast true if the first column must start with contrast (bgcolor)
     * @return array Array of html_table_row objects
     */
    protected function get_acronym_rows(attendance_report_data $reportdata, $startwithcontrast=false) {
        $rows = array();

        $summarycells = array();

        $row1 = new html_table_row();
        $row2 = new html_table_row();

        $setnumber = -1;
        $contrast = !$startwithcontrast;
        foreach ($reportdata->statuses as $sts) {
            if ($sts->setnumber != $setnumber) {
                $contrast = !$contrast;
                $setnumber = $sts->setnumber;
                $text = attendance_get_setname($reportdata->att->id, $setnumber, false);
                $cell = $this->build_header_cell($text, $contrast);
                $row1->cells[] = $cell;
            }
            $cell->colspan++;
            $sts->contrast = $contrast;
            $row2->cells[] = $this->build_header_cell($sts->acronym, $contrast);
            $summarycells[] = $this->build_data_cell('', $contrast);
        }

        $rows[] = $row1;
        $rows[] = $row2;

        foreach ($reportdata->users as $user) {
            if ($reportdata->pageparams->view == ATT_VIEW_SUMMARY) {
                $usersummary = $reportdata->summary->get_all_sessions_summary_for($user->id);
            } else {
                $usersummary = $reportdata->summary->get_taken_sessions_summary_for($user->id);
            }

            $row = new html_table_row();
            foreach ($reportdata->statuses as $sts) {
                if (isset($usersummary->userstakensessionsbyacronym[$sts->setnumber][$sts->acronym])) {
                    $text = $usersummary->userstakensessionsbyacronym[$sts->setnumber][$sts->acronym];
                } else {
                    $text = 0;
                }
                $row->cells[] = $this->build_data_cell($text, $sts->contrast);
            }

            $rows[] = $row;
        }

        $rows[] = new html_table_row($summarycells);

        return $rows;
    }

    /**
     * Build and return the rows that will make up the summary part of the attendance report.
     * This consists of counts and percentages for taken sessions (all sessions for summary report),
     * as well as header cells for these columns.
     *
     * @param attendance_report_data $reportdata the report data
     * @param boolean $startwithcontrast true if the first column must start with contrast (bgcolor)
     * @return array Array of html_table_row objects
     */
    protected function get_summary_rows(attendance_report_data $reportdata, $startwithcontrast=false) {
        $rows = array();

        $contrast = $startwithcontrast;
        $summarycells = array();

        $row1 = new html_table_row();
        $helpicon = $this->output->help_icon('oversessionstaken', 'attendance');
        $row1->cells[] = $this->build_header_cell(get_string('oversessionstaken', 'attendance') . $helpicon, $contrast, true, 3);

        $row2 = new html_table_row();
        $row2->cells[] = $this->build_header_cell(get_string('sessions', 'attendance'), $contrast);
        $row2->cells[] = $this->build_header_cell(get_string('points', 'attendance'), $contrast);
        $row2->cells[] = $this->build_header_cell(get_string('percentage', 'attendance'), $contrast);
        $summarycells[] = $this->build_data_cell('', $contrast);
        $summarycells[] = $this->build_data_cell('', $contrast);
        $summarycells[] = $this->build_data_cell('', $contrast);

        if ($reportdata->pageparams->view == ATT_VIEW_SUMMARY) {
            $contrast = !$contrast;

            $helpicon = $this->output->help_icon('overallsessions', 'attendance');
            $row1->cells[] = $this->build_header_cell(get_string('overallsessions', 'attendance') . $helpicon, $contrast, true, 3);

            $row2->cells[] = $this->build_header_cell(get_string('sessions', 'attendance'), $contrast);
            $row2->cells[] = $this->build_header_cell(get_string('points', 'attendance'), $contrast);
            $row2->cells[] = $this->build_header_cell(get_string('percentage', 'attendance'), $contrast);
            $summarycells[] = $this->build_data_cell('', $contrast);
            $summarycells[] = $this->build_data_cell('', $contrast);
            $summarycells[] = $this->build_data_cell('', $contrast);

            $contrast = !$contrast;
            $helpicon = $this->output->help_icon('maxpossible', 'attendance');
            $row1->cells[] = $this->build_header_cell(get_string('maxpossible', 'attendance') . $helpicon, $contrast, true, 2);

            $row2->cells[] = $this->build_header_cell(get_string('points', 'attendance'), $contrast);
            $row2->cells[] = $this->build_header_cell(get_string('percentage', 'attendance'), $contrast);
            $summarycells[] = $this->build_data_cell('', $contrast);
            $summarycells[] = $this->build_data_cell('', $contrast);
        }

        $rows[] = $row1;
        $rows[] = $row2;

        foreach ($reportdata->users as $user) {
            if ($reportdata->pageparams->view == ATT_VIEW_SUMMARY) {
                $usersummary = $reportdata->summary->get_all_sessions_summary_for($user->id);
            } else {
                $usersummary = $reportdata->summary->get_taken_sessions_summary_for($user->id);
            }

            $contrast = $startwithcontrast;
            $row = new html_table_row();
            $row->cells[] = $this->build_data_cell($usersummary->numtakensessions, $contrast);
            $text = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                                format_float($usersummary->takensessionsmaxpoints, 1, true, true);
            $row->cells[] = $this->build_data_cell($text, $contrast);
            $row->cells[] = $this->build_data_cell(format_float($usersummary->takensessionspercentage * 100) . '%', $contrast);

            if ($reportdata->pageparams->view == ATT_VIEW_SUMMARY) {
                $contrast = !$contrast;
                $row->cells[] = $this->build_data_cell($usersummary->numallsessions, $contrast);
                $text = format_float($usersummary->takensessionspoints, 1, true, true) . ' / ' .
                                format_float($usersummary->allsessionsmaxpoints, 1, true, true);
                $row->cells[] = $this->build_data_cell($text, $contrast);
                $row->cells[] = $this->build_data_cell(format_float($usersummary->allsessionspercentage * 100) . '%', $contrast);

                $contrast = !$contrast;
                $text = format_float($usersummary->maxpossiblepoints, 1, true, true) . ' / ' .
                                format_float($usersummary->allsessionsmaxpoints, 1, true, true);
                $row->cells[] = $this->build_data_cell($text, $contrast);
                $row->cells[] = $this->build_data_cell(format_float($usersummary->maxpossiblepercentage * 100) . '%', $contrast);
            }

            $rows[] = $row;
        }

        $rows[] = new html_table_row($summarycells);

        return $rows;
    }

    /**
     * Build and return the rows that will make up the attendance report.
     * This consists of details for each selected session, as well as header and summary cells for these columns.
     *
     * @param attendance_report_data $reportdata the report data
     * @param boolean $startwithcontrast true if the first column must start with contrast (bgcolor)
     * @return array Array of html_table_row objects
     */
    protected function get_session_rows(attendance_report_data $reportdata, $startwithcontrast=false) {
        global $OUTPUT;

        $rows = array();

        $row = new html_table_row();

        $showsessiondetails = $reportdata->pageparams->showsessiondetails;
        $text = get_string('sessions', 'attendance');
        $params = $reportdata->pageparams->get_significant_params();
        if (count($reportdata->sessions) > 1) {
            if ($showsessiondetails) {
                $params['showsessiondetails'] = 0;
                $url = $reportdata->att->url_report($params);
                $text .= $OUTPUT->action_icon($url, new pix_icon('t/switch_minus',
                            get_string('hidensessiondetails', 'attendance')), null, null);
                $colspan = count($reportdata->sessions);
            } else {
                $params['showsessiondetails'] = 1;
                $url = $reportdata->att->url_report($params);
                $text .= $OUTPUT->action_icon($url, new pix_icon('t/switch_plus',
                            get_string('showsessiondetails', 'attendance')), null, null);
                $colspan = 1;
            }
        } else {
            $colspan = 1;
        }

        $params = $reportdata->pageparams->get_significant_params();
        if ($reportdata->pageparams->sessiondetailspos == 'left') {
            $params['sessiondetailspos'] = 'right';
            $url = $reportdata->att->url_report($params);
            $text .= $OUTPUT->action_icon($url, new pix_icon('t/right', get_string('moveright', 'attendance')), null, null);
        } else {
            $params['sessiondetailspos'] = 'left';
            $url = $reportdata->att->url_report($params);
            $text = $OUTPUT->action_icon($url, new pix_icon('t/left', get_string('moveleft', 'attendance')), null, null) . $text;
        }

        $row->cells[] = $this->build_header_cell($text, '', true, $colspan);
        $rows[] = $row;

        $row = new html_table_row();
        if ($showsessiondetails && !empty($reportdata->sessions)) {
            foreach ($reportdata->sessions as $sess) {
                $sesstext = userdate($sess->sessdate, get_string('strftimedm', 'attendance'));
                $sesstext .= html_writer::empty_tag('br');
                $sesstext .= attendance_strftimehm($sess->sessdate);
                $capabilities = array(
                    'mod/attendance:takeattendances',
                    'mod/attendance:changeattendances'
                );
                if (is_null($sess->lasttaken) and has_any_capability($capabilities, $reportdata->att->context)) {
                    $sesstext = html_writer::link($reportdata->url_take($sess->id, $sess->groupid), $sesstext,
                        array('class' => 'attendancereporttakelink'));
                }
                $sesstext .= html_writer::empty_tag('br', array('class' => 'attendancereportseparator'));
                if (!empty($sess->description) &&
                    !empty(get_config('attendance', 'showsessiondescriptiononreport'))) {
                    $sesstext .= html_writer::tag('small', format_text($sess->description),
                        array('class' => 'attendancereportcommon'));
                }
                if ($sess->groupid) {
                    if (empty($reportdata->groups[$sess->groupid])) {
                        $sesstext .= html_writer::tag('small', get_string('deletedgroup', 'attendance'),
                            array('class' => 'attendancereportgroup'));
                    } else {
                        $sesstext .= html_writer::tag('small', $reportdata->groups[$sess->groupid]->name,
                            array('class' => 'attendancereportgroup'));
                    }

                } else {
                    $sesstext .= html_writer::tag('small', get_string('commonsession', 'attendance'),
                        array('class' => 'attendancereportcommon'));
                }

                $row->cells[] = $this->build_header_cell($sesstext, false, true, null, null, false);
            }
        } else {
            $row->cells[] = $this->build_header_cell('');
        }
        $rows[] = $row;

        foreach ($reportdata->users as $user) {
            $row = new html_table_row();
            if ($showsessiondetails && !empty($reportdata->sessions)) {
                $cellsgenerator = new user_sessions_cells_html_generator($reportdata, $user);
                foreach ($cellsgenerator->get_cells(true) as $cell) {
                    if ($cell instanceof html_table_cell) {
                        $cell->attributes['class'] .= ' center';
                        $row->cells[] = $cell;
                    } else {
                        $row->cells[] = $this->build_data_cell($cell);
                    }
                }
            } else {
                $row->cells[] = $this->build_data_cell('');
            }
            $rows[] = $row;
        }

        $row = new html_table_row();
        if ($showsessiondetails && !empty($reportdata->sessions)) {
            foreach ($reportdata->sessions as $sess) {
                $sessionstats = array();
                foreach ($reportdata->statuses as $status) {
                    if ($status->setnumber == $sess->statusset) {
                        $status->count = 0;
                        $sessionstats[$status->id] = $status;
                    }
                }

                foreach ($reportdata->users as $user) {
                    if (!empty($reportdata->sessionslog[$user->id][$sess->id])) {
                        $statusid = $reportdata->sessionslog[$user->id][$sess->id]->statusid;
                        if (isset($sessionstats[$statusid]->count)) {
                            $sessionstats[$statusid]->count++;
                        }
                    }
                }

                $statsoutput = '';
                foreach ($sessionstats as $status) {
                    $statsoutput .= "$status->description: {$status->count}<br/>";
                }
                $row->cells[] = $this->build_data_cell($statsoutput);
            }
        } else {
            $row->cells[] = $this->build_header_cell('');
        }
        $rows[] = $row;

        return $rows;
    }

    /**
     * Build and return the rows that will make up the right part of the attendance report.
     * This consists of checkbox column for bulk message.
     *
     * @param attendance_report_data $reportdata the report data
     * @return array Array of html_table_row objects
     */
    protected function get_bulkmessage_rows(attendance_report_data $reportdata) {
        $rows = array();

        $row = new html_table_row();
        $row->cells[] = $this->build_header_cell('');
        $rows[] = $row;

        // Display the table header for bulk messaging.
        // The checkbox must have an id of cb_selector so that the JavaScript will pick it up.
        $row = new html_table_row();
        $text = html_writer::checkbox('cb_selector', 0, false, '', array('id' => 'cb_selector'));
        $row->cells[] = $this->build_header_cell($text);
        $rows[] = $row;

        foreach ($reportdata->users as $user) {
            // Create the checkbox for bulk messaging.
            $row = new html_table_row();
            $text = html_writer::checkbox('user'.$user->id, 'on', false, '', array('class' => 'attendancesesscheckbox'));
            $row->cells[] = $this->build_data_cell($text);
            $rows[] = $row;
        }

        $row = new html_table_row();
        $row->cells[] = $this->build_data_cell('');
        $rows[] = $row;

        return $rows;
    }

    /**
     * Build and return a html_table_cell for header rows
     *
     * @param html_table_cell|string $cell the cell or a label for a cell
     * @param boolean $contrast true menans the cell must be shown with bgcolor contrast
     * @param boolean $center true means the cell text should be centered. Othersiwe it should be left-aligned.
     * @param int $colspan how many columns should cell spans
     * @param int $rowspan how many rows should cell spans
     * @param boolean $nowrap true means the cell text must be shown with nowrap option
     * @return html_table_cell a html table cell
     */
    protected function build_header_cell($cell, $contrast=false, $center=true, $colspan=null, $rowspan=null, $nowrap=true) {
        $classes = array('header', 'bottom');
        if ($center) {
            $classes[] = 'center';
            $classes[] = 'narrow';
        } else {
            $classes[] = 'left';
        }
        if ($contrast) {
            $classes[] = 'contrast';
        }
        if ($nowrap) {
            $classes[] = 'nowrap';
        }
        return $this->build_cell($cell, $classes, $colspan, $rowspan, true);
    }

    /**
     * Build and return a html_table_cell for data rows
     *
     * @param html_table_cell|string $cell the cell or a label for a cell
     * @param boolean $contrast true menans the cell must be shown with bgcolor contrast
     * @param boolean $center true means the cell text should be centered. Othersiwe it should be left-aligned.
     * @param int $colspan how many columns should cell spans
     * @param int $rowspan how many rows should cell spans
     * @param boolean $nowrap true means the cell text must be shown with nowrap option
     * @return html_table_cell a html table cell
     */
    protected function build_data_cell($cell, $contrast=false, $center=true, $colspan=null, $rowspan=null, $nowrap=true) {
        $classes = array();
        if ($center) {
            $classes[] = 'center';
            $classes[] = 'narrow';
        } else {
            $classes[] = 'left';
        }
        if ($nowrap) {
            $classes[] = 'nowrap';
        }
        if ($contrast) {
            $classes[] = 'contrast';
        }
        return $this->build_cell($cell, $classes, $colspan, $rowspan, false);
    }

    /**
     * Build and return a html_table_cell for header or data rows
     *
     * @param html_table_cell|string $cell the cell or a label for a cell
     * @param Array $classes a list of css classes
     * @param int $colspan how many columns should cell spans
     * @param int $rowspan how many rows should cell spans
     * @param boolean $header true if this should be a header cell
     * @return html_table_cell a html table cell
     */
    protected function build_cell($cell, $classes, $colspan=null, $rowspan=null, $header=false) {
        if (!($cell instanceof html_table_cell)) {
            $cell = new html_table_cell($cell);
        }
        $cell->header = $header;
        $cell->scope = 'col';

        if (!empty($colspan) && $colspan > 1) {
            $cell->colspan = $colspan;
        }

        if (!empty($rowspan) && $rowspan > 1) {
            $cell->rowspan = $rowspan;
        }

        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (empty($cell->attributes['class'])) {
                $cell->attributes['class'] = $classes;
            } else {
                $cell->attributes['class'] .= ' ' . $classes;
            }
        }

        return $cell;
    }

    /**
     * Output the status set selector.
     *
     * @param attendance_set_selector $sel
     * @return string
     */
    protected function render_attendance_set_selector(attendance_set_selector $sel) {
        $current = $sel->get_current_statusset();
        $selected = null;
        $opts = array();
        for ($i = 0; $i <= $sel->maxstatusset; $i++) {
            $url = $sel->url($i);
            $display = $sel->get_status_name($i);
            $opts[$url->out(false)] = $display;
            if ($i == $current) {
                $selected = $url->out(false);
            }
        }
        $newurl = $sel->url($sel->maxstatusset + 1);
        $opts[$newurl->out(false)] = get_string('newstatusset', 'mod_attendance');
        if ($current == $sel->maxstatusset + 1) {
            $selected = $newurl->out(false);
        }

        return $this->output->url_select($opts, $selected, null);
    }

    /**
     * Render preferences data.
     *
     * @param stdClass $prefdata
     * @return string
     */
    protected function render_attendance_preferences_data($prefdata) {
        $this->page->requires->js('/mod/attendance/module.js');

        $studentscanmark = false;
        if (!empty(get_config('attendance', 'studentscanmark'))) {
            $studentscanmark = true;
        }

        $table = new html_table();
        $table->width = '100%';
        $table->head = array('#',
                             get_string('acronym', 'attendance'),
                             get_string('description'),
                             get_string('points', 'attendance'));
        $table->align = array('center', 'center', 'center', 'center', 'center', 'center');
        if ($studentscanmark) {
            $table->head[] = get_string('studentavailability', 'attendance').
                $this->output->help_icon('studentavailability', 'attendance');
            $table->align[] = 'center';

            $table->head[] = get_string('setunmarked', 'attendance').
                $this->output->help_icon('setunmarked', 'attendance');
            $table->align[] = 'center';
        }
        $table->head[] = get_string('action');

        $i = 1;
        foreach ($prefdata->statuses as $st) {
            $emptyacronym = '';
            $emptydescription = '';
            if (isset($prefdata->errors[$st->id]) && !empty(($prefdata->errors[$st->id]))) {
                if (empty($prefdata->errors[$st->id]['acronym'])) {
                    $emptyacronym = $this->construct_notice(get_string('emptyacronym', 'mod_attendance'), 'notifyproblem');
                }
                if (empty($prefdata->errors[$st->id]['description'])) {
                    $emptydescription = $this->construct_notice(get_string('emptydescription', 'mod_attendance') , 'notifyproblem');
                }
            }
            $cells = array();
            $cells[] = $i;
            $cells[] = $this->construct_text_input('acronym['.$st->id.']', 2, 2, $st->acronym) . $emptyacronym;
            $cells[] = $this->construct_text_input('description['.$st->id.']', 30, 30, $st->description) .
                                 $emptydescription;
            $cells[] = $this->construct_text_input('grade['.$st->id.']', 4, 4, $st->grade);
            if ($studentscanmark) {
                $checked = '';
                if ($st->setunmarked) {
                    $checked = ' checked ';
                }
                $cells[] = $this->construct_text_input('studentavailability['.$st->id.']', 4, 5, $st->studentavailability);
                $cells[] = '<input type="radio" name="setunmarked" value="'.$st->id.'"'.$checked.'>';
            }
            $cells[] = $this->construct_preferences_actions_icons($st, $prefdata);

            $table->data[$i] = new html_table_row($cells);
            $table->data[$i]->id = "statusrow".$i;
            $i++;
        }

        $table->data[$i][] = '*';
        $table->data[$i][] = $this->construct_text_input('newacronym', 2, 2);
        $table->data[$i][] = $this->construct_text_input('newdescription', 30, 30);
        $table->data[$i][] = $this->construct_text_input('newgrade', 4, 4);
        if ($studentscanmark) {
            $table->data[$i][] = $this->construct_text_input('newstudentavailability', 4, 5);
        }
        $table->data[$i][] = $this->construct_preferences_button(get_string('add', 'attendance'),
            mod_attendance_preferences_page_params::ACTION_ADD);

        $o = html_writer::table($table);
        $o .= html_writer::input_hidden_params($prefdata->url(array(), false));
        // We should probably rewrite this to use mforms but for now add sesskey.
        $o .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()))."\n";

        $o .= $this->construct_preferences_button(get_string('update', 'attendance'),
                                                  mod_attendance_preferences_page_params::ACTION_SAVE);
        $o = html_writer::tag('form', $o, array('id' => 'preferencesform', 'method' => 'post',
                                                'action' => $prefdata->url(array(), false)->out_omit_querystring()));
        $o = $this->output->container($o, 'generalbox attwidth');

        return $o;
    }

    /**
     * Render default statusset.
     *
     * @param attendance_default_statusset $prefdata
     * @return string
     */
    protected function render_attendance_default_statusset(attendance_default_statusset $prefdata) {
        return $this->render_attendance_preferences_data($prefdata);
    }

    /**
     * Render preferences data.
     *
     * @param stdClass $prefdata
     * @return string
     */
    protected function render_attendance_pref($prefdata) {

    }

    /**
     * Construct text input.
     *
     * @param string $name
     * @param integer $size
     * @param integer $maxlength
     * @param string $value
     * @return string
     */
    private function construct_text_input($name, $size, $maxlength, $value='') {
        $attributes = array(
                'type'      => 'text',
                'name'      => $name,
                'size'      => $size,
                'maxlength' => $maxlength,
                'value'     => $value,
                'class' => 'form-control');
        return html_writer::empty_tag('input', $attributes);
    }

    /**
     * Construct action icons.
     *
     * @param stdClass $st
     * @param stdClass $prefdata
     * @return string
     */
    private function construct_preferences_actions_icons($st, $prefdata) {
        global $OUTPUT;
        $params = array('sesskey' => sesskey(),
                        'statusid' => $st->id);
        if ($st->visible) {
            $params['action'] = mod_attendance_preferences_page_params::ACTION_HIDE;
            $showhideicon = $OUTPUT->action_icon(
                    $prefdata->url($params),
                    new pix_icon("t/hide", get_string('hide')));
        } else {
            $params['action'] = mod_attendance_preferences_page_params::ACTION_SHOW;
            $showhideicon = $OUTPUT->action_icon(
                    $prefdata->url($params),
                    new pix_icon("t/show", get_string('show')));
        }
        if (empty($st->haslogs)) {
            $params['action'] = mod_attendance_preferences_page_params::ACTION_DELETE;
            $deleteicon = $OUTPUT->action_icon(
                    $prefdata->url($params),
                    new pix_icon("t/delete", get_string('delete')));
        } else {
            $deleteicon = '';
        }

        return $showhideicon . $deleteicon;
    }

    /**
     * Construct preferences button.
     *
     * @param string $text
     * @param string $action
     * @return string
     */
    private function construct_preferences_button($text, $action) {
        $attributes = array(
                'type'      => 'submit',
                'value'     => $text,
                'class'     => 'btn btn-secondary',
                'onclick'   => 'M.mod_attendance.set_preferences_action('.$action.')');
        return html_writer::empty_tag('input', $attributes);
    }

    /**
     * Construct a notice message
     *
     * @param string $text
     * @param string $class
     * @return string
     */
    private function construct_notice($text, $class = 'notifymessage') {
        $attributes = array('class' => $class);
        return html_writer::tag('p', $text, $attributes);
    }

    /**
     * Show different picture if it is a temporary user.
     *
     * @param stdClass $user
     * @param array $opts
     * @return string
     */
    protected function user_picture($user, array $opts = null) {
        if ($user->type == 'temporary') {
            $attrib = array(
                'width' => '35',
                'height' => '35',
                'class' => 'userpicture defaultuserpic',
            );
            if (isset($opts['size'])) {
                $attrib['width'] = $attrib['height'] = $opts['size'];
            }
            return $this->output->pix_icon('ghost', '', 'mod_attendance', $attrib);
        }

        return $this->output->user_picture($user, $opts);
    }
}
