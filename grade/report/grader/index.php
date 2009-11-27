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

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/grader/lib.php';

require_js(array('yui_yahoo', 'yui_dom', 'yui_event', 'yui_container', 'yui_connection', 'yui_dragdrop', 'yui_element'));


$courseid      = required_param('id', PARAM_INT);        // course id
$page          = optional_param('page', 0, PARAM_INT);   // active page
$perpageurl    = optional_param('perpage', 0, PARAM_INT);
$edit          = optional_param('edit', -1, PARAM_BOOL); // sticky editting mode

$sortitemid    = optional_param('sortitemid', 0, PARAM_ALPHANUM); // sort by which grade item
$action        = optional_param('action', 0, PARAM_ALPHAEXT);
$move          = optional_param('move', 0, PARAM_INT);
$type          = optional_param('type', 0, PARAM_ALPHA);
$target        = optional_param('target', 0, PARAM_ALPHANUM);
$toggle        = optional_param('toggle', NULL, PARAM_INT);
$toggle_type   = optional_param('toggle_type', 0, PARAM_ALPHANUM);

/// basic access checks
if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}
require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);

require_capability('gradereport/grader:view', $context);
require_capability('moodle/grade:viewall', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'grader', 'courseid'=>$courseid, 'page'=>$page));

/// last selected report session tracking
if (!isset($USER->grade_last_report)) {
    $USER->grade_last_report = array();
}
$USER->grade_last_report[$course->id] = 'grader';

/// Build editing on/off buttons

if (!isset($USER->gradeediting)) {
    $USER->gradeediting = array();
}

if (has_capability('moodle/grade:edit', $context)) {
    if (!isset($USER->gradeediting[$course->id])) {
        $USER->gradeediting[$course->id] = 0;
    }

    if (($edit == 1) and confirm_sesskey()) {
        $USER->gradeediting[$course->id] = 1;
    } else if (($edit == 0) and confirm_sesskey()) {
        $USER->gradeediting[$course->id] = 0;
    }

    // page params for the turn editting on
    $options = $gpr->get_options();
    $options['sesskey'] = sesskey();

    if ($USER->gradeediting[$course->id]) {
        $options['edit'] = 0;
        $string = get_string('turneditingoff');
    } else {
        $options['edit'] = 1;
        $string = get_string('turneditingon');
    }

    $buttons = print_single_button('index.php', $options, $string, 'get', '_self', true);

} else {
    $USER->gradeediting[$course->id] = 0;
    $buttons = '';
}

$gradeserror = array();

// Handle toggle change request
if (!is_null($toggle) && !empty($toggle_type)) {
    set_user_preferences(array('grade_report_show'.$toggle_type => $toggle));
}

//first make sure we have proper final grades - this must be done before constructing of the grade tree
grade_regrade_final_grades($courseid);

// Perform actions
if (!empty($target) && !empty($action) && confirm_sesskey()) {
    grade_report_grader::process_action($target, $action);
}

$reportname = get_string('modulename', 'gradereport_grader');
// Initialise the grader report object
$report = new grade_report_grader($courseid, $gpr, $context, $page, $sortitemid);

// make sure separate group does not prevent view
if ($report->currentgroup == -2) {
    print_grade_page_head($COURSE->id, 'report', 'grader', $reportname, false, null, $buttons);
    print_heading(get_string("notingroup"));
    print_footer($course);
    exit;
}

/// processing posted grades & feedback here
if ($data = data_submitted() and confirm_sesskey() and has_capability('moodle/grade:edit', $context)) {
    $warnings = $report->process_data($data);
} else {
    $warnings = array();
}


// Override perpage if set in URL
if ($perpageurl) {
    $report->user_prefs['studentsperpage'] = $perpageurl;
}

// final grades MUST be loaded after the processing
$report->load_users();
$numusers = $report->get_numusers();
$report->load_final_grades();

/// Print header
print_grade_page_head($COURSE->id, 'report', 'grader', $reportname, false, null, $buttons);

echo $report->group_selector;
echo '<div class="clearer"></div>';
// echo $report->get_toggles_html();

//show warnings if any
foreach($warnings as $warning) {
    notify($warning);
}

$studentsperpage = $report->get_pref('studentsperpage');
// Don't use paging if studentsperpage is empty or 0 at course AND site levels
if (!empty($studentsperpage)) {
    print_paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl);
}

$reporthtml = '<script src="functions.js" type="text/javascript"></script>';
$reporthtml .= '<div class="gradeparent">';
$reporthtml .= $report->get_studentnameshtml();
$reporthtml .= $report->get_headerhtml();
$reporthtml .= $report->get_iconshtml();
$reporthtml .= $report->get_studentshtml();
$reporthtml .= $report->get_rangehtml();
$reporthtml .= $report->get_avghtml(true);
$reporthtml .= $report->get_avghtml();
$reporthtml .= $report->get_endhtml();
$reporthtml .= '</div>';

// print submit button
if ($USER->gradeediting[$course->id]) {
    echo '<form action="index.php" method="post">';
    echo '<div>';
    echo '<input type="hidden" value="'.$courseid.'" name="id" />';
    echo '<input type="hidden" value="'.sesskey().'" name="sesskey" />';
    echo '<input type="hidden" value="grader" name="report"/>';
}

echo $reporthtml;

// print submit button
if ($USER->gradeediting[$course->id] && ($report->get_pref('showquickfeedback') || $report->get_pref('quickgrading'))) {
    echo '<div class="submit"><input type="submit" value="'.get_string('update').'" /></div>';
    echo '</div></form>';
}

// prints paging bar at bottom for large pages
if (!empty($studentsperpage) && $studentsperpage >= 20) {
    print_paging_bar($numusers, $report->page, $studentsperpage, $report->pbarurl);
}

echo '<div id="hiddentooltiproot">tooltip panel</div>';
// Print YUI tooltip code
?>
<script type="text/javascript">
//<![CDATA[

YAHOO.namespace("graderreport");

function init() {
    // Adjust height of header c0
    //"heading_name_row" only exists if static students column is turned on
    var rows = YAHOO.util.Dom.getElementsByClassName('heading_name_row');
    if(rows && rows.length>0)
    {
        var header_cell_region = YAHOO.util.Dom.getRegion(rows[rows.length-1].firstChild);
        if(header_cell_region)
        {
            var height = header_cell_region.bottom - header_cell_region.top;
            if(!isNaN(height))
            {
                YAHOO.util.Dom.setStyle('studentheader', 'height', height + 'px');
            }
        }
    }

    // attach event listener to the table for mouseover and mouseout
    var table = document.getElementById('user-grades');
    YAHOO.util.Event.on(table, 'mouseover', YAHOO.graderreport.mouseoverHandler);
    YAHOO.util.Event.on(table, 'mouseout', YAHOO.graderreport.mouseoutHandler);

    // Make single panel that can be dynamically re-rendered wit the right data.
    YAHOO.graderreport.panelEl = new YAHOO.widget.Panel("tooltipPanel", {

        draggable: false,
        visible: false,
        close: false,
        preventcontextoverlap: true,
        underlay: 'none'

    });

    YAHOO.graderreport.panelEl.render(table);

    document.body.className += ' yui-skin-sam';
}

YAHOO.graderreport.mouseoverHandler = function (e) {

    var tempNode = '';
    var searchString = '';
    var tooltipNode = '';

    // get the element that we just moved the mouse over
    var elTarget = YAHOO.util.Event.getTarget(e);


    // if it was part of the yui panel, we don't want to redraw yet
    searchString = /fullname|itemname|feedback/;
    if (elTarget.className.search(searchString) > -1) {
        return false;
    }

    // move up until we are in the actual cell, not any other child div or span
    while (elTarget.id != 'user-grades') {
        if(elTarget.nodeName.toUpperCase() == "TD") {
            break;
        } else {
            elTarget = elTarget.parentNode;
        }
    }

    // only make a tooltip for cells with grades
    if (elTarget.className.search('grade cell') > -1) {

        // each time we go over a new cell, we need to put it's tooltip into a div to stop it from
        // popping up on top of the panel.

        // don't do anything if we have already made the tooltip div
        var makeTooltip = true
        for (var k=0; k < elTarget.childNodes.length; k++) {
            if (typeof(elTarget.childNodes[k].className) != 'undefined') {
                if (elTarget.childNodes[k].className.search('tooltipDiv') > -1) {
                    makeTooltip =  false;
                }
            }
        }

        // if need to, make the tooltip div and append it to the cell
        if (makeTooltip) {
            tempNode = document.createElement("div");
            tempNode.className = "tooltipDiv";
            tempNode.innerHTML = elTarget.title;
            elTarget.appendChild(tempNode);
            elTarget.title = null;
        }

        // Get the tooltip div
        elChildren = elTarget.childNodes;
        for (var m=0; m < elChildren.length; m++) {
            if (typeof(elChildren[m].className) != 'undefined') {
                if (elChildren[m].className.search('tooltipDiv') > -1) {
                    tooltipNode = elChildren[m];
                    break;
                }
            }
        }
        //build and show the tooltip
        YAHOO.graderreport.panelEl.setBody(tooltipNode.innerHTML);
        YAHOO.graderreport.panelEl.render(elTarget);
        YAHOO.graderreport.panelEl.show()
    }
}

// only hide the overlay if the mouse has not moved over it
YAHOO.graderreport.mouseoutHandler = function (e) {

    var classVar = '';
    var searchString = '';
    var newTargetClass = '';
    var newTarget = YAHOO.util.Event.getRelatedTarget(e);

    // deals with an error if the mouseout event is over the lower scrollbar
    try {
        classVar = newTarget.className;
    } catch (err) {
        YAHOO.graderreport.panelEl.hide()
        return false;
    }

    // if we are over any part of the panel, do not hide
    // do this by walking up the DOM till we reach table level, looking for panel tag
    while ((typeof(newTarget.id) == 'undefined') || (newTarget.id != 'user-grades')) {

        try {
            newTargetClass = newTarget.className;
        } catch (err) {
            // we've gone over the scrollbar again
            YAHOO.graderreport.panelEl.hide()
            return false;
        }
        searchString = /yui-panel|grade cell/;
        if (newTargetClass.search(searchString) > -1) {
            // we're in the panel so don't hide it
            return false;
        }

        if (newTarget.nodeName.toUpperCase() == "HTML") {
            // we missed the user-grades table altogether by moving down off screen to read a long one
            YAHOO.graderreport.panelEl.hide()
            break;
        }

        newTarget = newTarget.parentNode;
    }

    // no panel so far and we went up to the
    YAHOO.graderreport.panelEl.hide()

}


YAHOO.util.Event.onDOMReady(init);

//]]>
</script>
<?php

print_footer($course);

?>
