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
 * Configurable Reports a Moodle block for creating customizable reports
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @package    block_configurable_reports
 * @author     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = "Configurable Reports";
$string['blockname'] = "Configurable Reports";
$string['report_courses'] = "Courses report";
$string['report_users'] = "Users report";
$string['report_sql'] = "SQL Report";
$string['managereports'] = "Manage reports";

$string['report'] = "Report";
$string['reports'] = "Reports";

$string['columns'] = "Columns";
$string['conditions'] = "Conditions";
$string['permissions'] = "Permissions";
$string['plot'] = "Plot - Graphs";
$string['filters'] = "Filters	";
$string['calcs'] = "Calculations";
$string['ordering'] = "Ordering";
$string['customsql'] = "Custom SQL";
$string['addreport'] = "Add report";
$string['type'] = "Type of report";
$string['columncalculations'] = "Column Calculations";
$string['newreport'] = "New report";
$string['column'] = "Column";
$string['confirmdeletereport'] = "Are you sure you want to delete this report?";
$string['noreportsavailable'] = "No reports available";
$string['downloadreport'] = "Download report";
$string['reportlimit'] = "Report row limit";
$string['reportlimitinfo'] = "Limit the number of rows that are displayed in the report table
    (Default is 5000 rows. Better to have some limit, so users will not over load the DB engine)";

$string['configurable_reports:addinstance'] = 'Add a new configurable reports block';
$string['configurable_reports:myaddinstance'] = 'Add a new configurable reports block to MY HOME page';
$string['configurable_reports:manageownreports'] = "Manage own reports";
$string['configurable_reports:managereports'] = "Manage reports";
$string['configurable_reports:managesqlreports'] = "Manage SQL reports";
$string['configurable_reports:viewreports'] = "View reports";

$string['exportoptions'] = "Export options";
$string['embedoptions'] = "Embed options";
$string['field'] = "Field";

// Report form.
$string['typeofreport'] = "Type of report";
$string['enablejsordering'] = "Enable JavaScript ordering";
$string['enablejspagination'] = "Enable JavaScript Pagination";
$string['export_csv'] = "Export in CSV format";
$string['export_ods'] = "Export in ODS format";
$string['export_slk'] = "Export in SYLK format";
$string['export_xls'] = "Export in XLS format";
$string['export_json'] = "Export in JSON format";
$string['viewreport'] = "View report";
$string['norecordsfound'] = "No records found";
$string['jsordering'] = 'JavaScript Ordering';
$string['cron'] = 'Auto run daily';
$string['crondescription'] = 'Schedule this query to run each day (At night)';
$string['displaytotalrecords'] = 'Total Records';
$string['displaytotalrecordsdescription'] = 'Displays the total number of results in the report';
$string['displayprintbutton'] = 'Print Button';
$string['displayprintbuttondescription'] = 'Displays the print button at the bottom of the report';
$string['embedlink'] = 'Embed Link';
$string['embedlinkdescription'] = 'You can copy this link to embed the report in an HTML block';
$string['cron_help'] = 'Schedule this query to run each day (At night)';
$string['remote'] = 'Run on remote db';
$string['remotedescription'] = 'Do you want to run this query on the remote db';
$string['remote_help'] = 'Do you want to run this query on the remote db';
$string['setcourseid'] = 'Set courseid';

// Columns.
$string['column'] = "Column";
$string['nocolumnsyet'] = "No columns yet";
$string['tablealign'] = "Table align";
$string['tablecellspacing'] = "Table cellspacing";
$string['tablecellpadding'] = "Table cellpadding";
$string['tableclass'] = "Table class";
$string['tablewidth'] = "Table width";
$string['cellalign'] = "Cell align";
$string['cellwrap'] = "Cell wrap";
$string['cellsize'] = "Cell size";

// Conditions.
$string['conditionexpr'] = "Condition";
$string['conditionexprhelp'] = "Enter a valid condition i.e: (c1 and c2) or (c4 and c3)";
$string['noconditionsyet'] = "No conditions yet";
$string['operator'] = "Operator";
$string['value'] = "Value";

// Filter.
$string['filter'] = "Filter";
$string['nofilteryet'] = "No filters yet";
$string['courses'] = "Courses";
$string['nofiltersyet'] = "No filters yet";
$string['filter_all'] = 'All';
$string['filter_apply'] = 'Apply';
$string['filter_searchtext'] = 'Search text';
$string['searchtext'] = 'Search text';
$string['filter_searchtext_summary'] = 'Free text filter';
$string['years'] = 'Year (Numeric)';
$string['filteryears'] = 'Year (Numeric)';
$string['filteryears_summary'] = 'Filter by years (numeric representation, 2012...)';
$string['filteryears_list'] = '2010,2011,2012,2013,2014,2015';
$string['semester'] = 'Semester (Hebrew)';
$string['filtersemester'] = 'Semester (Hebrew)';
$string['filtersemester_summary'] = 'מאפשר סינון לפני סמסטרים (בעברית, למשל: סמסטר א,סמסטר ב)';
$string['filtersemester_list'] = 'סמסטר א,סמסטר ב,סמסטר ג,סמינריון';
$string['subcategories'] = 'Category (Include sub categories)';
$string['filtersubcategories'] = 'Category (Include sub categories)';
$string['filtersubcategories_summary'] = 'Use: %%FILTER_SUBCATEGORIES:mdl_course_category.path%%';
$string['yearnumeric'] = 'Year (Numeric)';
$string['filteryearnumeric'] = 'Year (Numeric)';
$string['filteryearnumeric_summary'] = 'Filter is using numeric years (2013,...)';
$string['yearhebrew'] = 'Year (Hebrew)';
$string['filteryearhebrew'] = 'Year (Hebrew)';
$string['filteryearhebrew_list'] = 'תשע,תשעא,תשעב,תשעג,תשעד,תשעה';
$string['filteryearhebrew_summary'] = 'Filter is using Hebrew years (תשעג,...)';
$string['role'] = 'Role';
$string['filterrole'] = 'role';
$string['filterrole_summary'] = 'Filter system Roles (Teacher, Student, ...)';
$string['coursemodules'] = 'Course module';
$string['filtercoursemodules'] = 'Course module';
$string['filtercoursemodules_summary'] = 'Filter course modules';
$string['user'] = 'Course user (id)';
$string['filteruser'] = 'Current course user';
$string['filteruser_summary'] = 'Filter a user (id) from current course users';
$string['users'] = 'System user (id)';
$string['filterusers'] = 'System user';
$string['enrolledstudents'] = 'Enrolled students';
$string['filterusers_summary'] = 'Filter a user (by id) from system user list';
$string['filterenrolledstudents'] = 'Enrolled course students';
$string['filterenrolledstudents_summary'] = 'Filter a user (by id) from enrolled course students';
$string['competencyframeworks'] = 'Competency Frameworks';
$string['filtercompetencyframeworks'] = 'Competency Frameworks';
$string['filtercompetencyframeworks_summary'] = 'Use: %%FILTER_COMPETENCYFRAMEWORKS:prefix_competency_framework.id%%';
$string['competencytemplates'] = 'Competency Templates';
$string['filtercompetencytemplates'] = 'Competency templates';
$string['filtercompetencytemplates_summary'] = 'Use: %%FILTER_COMPETENCYTEMPLATES:prefix_competency_template.id%%';
$string['cohorts'] = 'Cohorts';
$string['filtercohorts'] = 'Cohorts';
$string['filtercohorts_summary'] = 'Use: %%FILTER_COHORTS:prefix_cohort.id%%';
$string['student'] = 'Student';

// Calcs.
$string['nocalcsyet'] = "No calculations yet";

// Plot.
$string['noplotyet'] = "No plots yet";

// Permissions.

$string['nopermissionsyet'] = "No permissions yet";

// Ordering.

$string['noorderingyet'] = "No ordering yet";
$string['userfieldorder'] = "User field order";

// Plugins.
$string['coursefield'] = "Course field";
$string['ccoursefield'] = "Course field condition";
$string['roleusersn'] = "Number of users with role...";
$string['coursecategory'] = "Course in category";
$string['filtercourses'] = "Courses";
$string['filtercourses_summary'] = "This filter shows a list of courses. Only one course can be selected at the same time";
$string['roleincourse'] = "User with the selected role/s in the current report course";
$string['reportscapabilities'] = "Report Capabilities";
$string['reportscapabilities_summary'] = "Users with the capability moodle/site:viewreports enabled";
$string['sum'] = "Sum";
$string['max'] = "Maximum";
$string['min'] = "Minimum";
$string['percent'] = "Percentage";
$string['average'] = "Average";
$string['pie'] = "Pie";
$string['piesummary'] = "A pie graph";
$string['pieareaname'] = "Name";
$string['pieareavalue'] = "Value";
$string['piesummary'] = "A pie graph";

$string['bar'] = "Bar";
$string['barsummary'] = "A bar graph";
$string['label_field'] = "Label field";
$string['label_field_help'] = "The field that provides names for the things represented in the graph";
$string['value_fields'] = "Value fields";
$string['value_fields_help'] = "Fields that should be represented in the graph. Ctrl+click (Cmd+click on Mac) to select multiple.
If you select the Label field or a field with non-numeric values it will be ignored";

$string['width'] = "Width";
$string['height'] = "Height";
$string['head_data'] = "Graph data";
$string['head_size'] = "Graph size";
$string['head_color'] = "Graph background color";

$string['anyone'] = "Anyone";
$string['anyone_summary'] = "Any user in the Campus will be able to view this report";

$string['currentuserfinalgrade'] = "Current user final grade in course";

$string['currentuserfinalgrade_summary'] = "This column shows the final grade of the current user in the row-course";
$string['userfield'] = "User profile field";

$string['cuserfield'] = "User field condition";
$string['direction'] = "Direction";

$string['courseparent'] = "Courses whose parent is";
$string['coursechild'] = "Courses that are children of";

$string['currentusercourses'] = "Current user enrolled courses";
$string['currentusercourses_summary'] = "A list of the current users courses (only visible courses)";
$string['currentreportcourse'] = "Current report course";
$string['currentreportcourse_summary'] = "The course where the report has been created";

$string['coursefieldorder'] = "Course field order";

$string['fcoursefield'] = "Course field filter";
$string['usersincoursereport'] = "Any user in the current report course";

$string['groupvalues'] = "Group same values (sum)";
$string['fuserfield'] = "User field filter";
$string['fsearchuserfield'] = "User field search box";

$string['module'] = "Module";

$string['usersincurrentcourse'] = "Users in current report course";
$string['usersincurrentcourse_summary'] = "Users with the role/s selected in the report course";

$string['usermodoutline'] = "User module outline stats";
$string['donotshowtime'] = "Do not show date information";
$string['usermodactions'] = "User module actions";

$string['currentuser'] = "Current user";
$string['currentuser_summary'] = "The user that is viewing the report";

$string['puserfield'] = "User field value";
$string['puserfield_summary'] = "User with the selected value in the selected field";

$string['startendtime'] = "Start / End date filter";
$string['starttime'] = "Start date";
$string['endtime'] = "End date";

$string['template'] = "Template";
$string['availablemarks'] = "Available marks";
$string['header'] = "Header";
$string['footer'] = "Footer";
$string['templaterecord'] = "Record template";
$string['querysql'] = "SQL Query";
$string['filterstartendtime_summary'] = "Start / End date filter";

$string['pagination'] = "Pagination";
$string['disabled'] = "Disabled";
$string['enabled'] = "Enabled";

$string['reportcolumn'] = "Other report column";

$string['reporttable'] = "Report table";
$string['columnandcellproperties'] = "Column and cell properties";
$string['componenthelp'] = "Component help";

$string['badsize'] = 'Incorrect size, it must be in &#37; or px';
$string['badtablewidth'] = 'Incorrect width, it must be in &#37; or absolute value';
$string['missingcolumn'] = "A column is required";
$string['error_operator'] = "Operator not allowed";

$string['error_field'] = "Field not allowed";
$string['error_value_expected_integer'] = "Expected integer value";
$string['badconditionexpr'] = "Incorrect condition expression";

$string['notallowedwords'] = "Not allowed words";
$string['nosemicolon'] = "No semicolon";
$string['noexplicitprefix'] = "No explicit prefix";
$string['queryfailed'] = 'Query failed <code><pre>{$a}</pre></code>';
$string['norowsreturned'] = "No rows returned";

$string['listofsqlreports'] = 'Press F11 when cursor is in the editor to toggle full screen editing. Esc can also be used to exit
full screen editing.<br/><br/><a href="http://docs.moodle.org/en/ad-hoc_contributed_reports" target="_blank">List of SQL Contributed reports</a>';

$string['usersincoursereport_summary'] = "Any user in the current report course";

$string['printreport'] = 'Print report';

$string['importreport'] = "Import report";
$string['exportreport'] = "Export report";

$string['download'] = "Download";

$string['report_timeline'] = 'Timeline report';
$string['timeline'] = 'Timeline';
$string['timemode'] = 'Time mode';
$string['previousdays'] = 'Previous days';
$string['fixeddate'] = 'Fixed date';
$string['previousstart'] = 'Previous start';
$string['previousend'] = 'Previous end';
$string['forcemidnight'] = 'Force midnight';
$string['timeinterval'] = 'Time interval';
$string['date'] = 'Date';
$string['dateformat'] = 'Date format';
$string['customdateformat'] = 'Custom date format';
$string['custom'] = 'Custom';

$string['line'] = 'Line graph';
$string['userstats'] = 'User statistics';
$string['stat'] = 'Statistic';
$string['statslogins'] = 'Logins in the platform';
$string['activityview'] = 'Activity views';
$string['activitypost'] = 'Activity posts';
$string[''] = '';
$string['globalstatsshouldbeenabled'] = 'Site statistics must be enabled. Go to Admin -> Server -> Statistics';

$string['xaxis'] = 'X Axis';
$string['yaxis'] = 'Y Axis';
$string['serieid'] = 'Serie column';
$string['groupseries'] = 'Group series';
$string['linesummary'] = 'A line graph with multiple series of data';
$string['xandynotequal'] = 'X and Y axis need to be different';

$string['coursestats'] = 'Course stats';
$string['statstotalenrolments'] = 'Total enrolments';
$string['statsactiveenrolments'] = 'Active (last week) enrolments';
$string['youmustselectarole'] = 'At least a role is required';

$string['report_categories'] = 'Categories report';
$string['categoryfield'] = 'Category field';
$string['categoryfieldorder'] = 'Category field order';
$string['categories'] = 'Categories';
$string['parentcategory'] = 'Parent category';
$string['filtercategories'] = 'Filter categories';
$string['filtercategories_summary'] = 'To filter by category';

$string['includesubcats'] = 'Include subcategories';

$string['coursededicationtime'] = 'Course dedication time';

$string['jsordering_help'] = 'JavaScript Ordering allow you to order the report table without reloading the page';
$string['pagination_help'] = 'Number of records to show in each page. Zero means no pagination';
$string['typeofreport_help'] = 'Choose the type of report you want to create.
For security, SQL Report requires an additional capability';
$string['template_marks'] = 'Template marks';
$string['template_marks_help'] = '<p>You can use any of this replacement marks:</p>

<ul>
<li>##reportname## - For including the report name</li>
<li>##reportsummary## - For including the reports summary</li>
<li>##graphs## - For including the graphs</li>
<li>##exportoptions## - For including the export options</li>
<li>##calculationstable## - For including the calculations table</li>
<li>##pagination## - For including the pagination </li>

</ul>';

$string['conditionexpr_conditions'] = 'Condition';
$string['conditionexpr_conditions_help'] = '<p>You can combine conditions using a logic expression</p>

<p>Enter a valid logic expression with these operators: and, or.</p>';

$string['conditionexpr_permissions'] = 'Condition';
$string['conditionexpr_permissions_help'] = '<p>You can combine conditions using a logic expression</p>

<p>Enter a valid logic expression with these operators: and, or.</p>';

$string['reporttable_help'] = '<p>This is the width of the table that will display the report records.</p>

<p>If you use a Template this option has no effect</p>';

$string['comp_calcs'] = 'Calcs';
$string['comp_calcs_help'] = '<p>Here you can add calculations for columns, i.e: average of number of users enrolled in courses</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';

$string['comp_calculations'] = 'Calcs';
$string['comp_calculations_help'] =
    '<p>Here you can add calculations for columns, i.e: average of number of users enrolled in courses</p>';
$string['comp_conditions'] = 'Conditions';
$string['comp_conditions_help'] = '<p>Here you can define the conditions (i.e, only courses from this category, only users from Spain, etc.. </p>

<p>You can add a logical expression if you are using more than one condition.</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';
$string['comp_customsql'] = 'Custom SQL';
$string['comp_customsql_help'] = '<p>Add a working SQL query. Do no use the moodle database prefix $CFG->prefix instead use "prefix_" without quotes</p>
<p>Example: SELECT * FROM prefix_course</p>

<p>You can find a lot of SQL Reports here: <a href="http://docs.moodle.org/en/ad-hoc_contributed_reports" target="_blank">ad-hoc contributed reports</a></p>

<p>An updated layout of Moodle\'s tables and their interconnected relations: <a href="https://docs.moodle.org/dev/Database_Schema" target="_blank">Database schema</a></p>

<p>Since this block supports Tim Hunt\'s CustomSQL Queries Reports, you can use any query.</p>

<p>Remember to add a "Time filter" if you are going to use reports with time tokens. </p>

<p>For using filters see: <a href="http://docs.moodle.org/en/blocks/configurable_reports/#Creating_a_SQL_Report" target="_blank">Creating a SQL Report Tutorial</a></p>';

$string['comp_ordering'] = 'Ordering';
$string['comp_ordering_help'] = '<p>Here you can choose how to order the report using fields and directions</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';
$string['comp_permissions'] = 'Permissions';
$string['comp_permissions_help'] = '<p>Here you can choose who can view a report.</p>

<p>You can add a logical expression to calculate the final permission if you are using more than one condition.</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';
$string['comp_plot'] = 'Plot';
$string['comp_plot_help'] = '<p>Here you can add graphs to your report based on the report columns and values</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';
$string['comp_template'] = 'Template';
$string['comp_template_help'] = '<p>You can modify the report\'s layout by creating a template</p>

<p>For creating a template see the replacemnet marks you can use in header, footer and for each report record using the help buttons or the information displayed in the same page.</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';
$string['comp_filters'] = 'Filters';
$string['comp_filters_help'] = '<p>Here you can choose which filters will be displayed</p>

<p>A filter lets an user to choose columns from the report to filter the report results</p>

<p>For using filters if your report type is SQL see: <a href="http://docs.moodle.org/en/blocks/configurable_reports/#Creating_a_SQL_Report" target="_blank">Creating a SQL Report Tutorial</a></p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';
$string['comp_columns'] = 'Columns';
$string['comp_columns_help'] = '<p>Here you can choose the different columns of your report depending on the type of report</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/configurable_reports/" target="_blank">Plugin documentation</a></p>';

$string['coursecategories'] = 'Category course filter';
$string['filtercoursecategories'] = 'Category course filter';
$string['filtercoursecategories_summary'] = 'Filter courses by their any parent category';

$string['dbhost'] = "DB Host";
$string['dbhostinfo'] = "Remote Database host name (on which, we will be executing our SQL queries)";
$string['dbname'] = "DB Name";
$string['dbnameinfo'] = "Remote Database name (on which, we will be executing our SQL queries)";
$string['dbuser'] = "DB Username";
$string['dbuserinfo'] = "Remote Database username (should have SELECT privileges on above DB)";
$string['dbpass'] = "DB Password";
$string['dbpassinfo'] = "Remote Database password (for above username)";

$string['totalrecords'] = 'Total record count = {$a->totalrecords}';
$string['lastexecutiontime'] = 'Execution time = {$a} (Sec)';

$string['reportcategories'] = '1) Choose a remote report categories';
$string['reportsincategory'] = '2) Choose a report form the list';
$string['remotequerysql'] = 'SQL query';
$string['executeat'] = 'Execute at';
$string['executeatinfo'] = 'Moodle CRON will run scheduled SQL queries after selected time. Once in 24h';
$string['sharedsqlrepository'] = 'Shared sql repository';
$string['sharedsqlrepositoryinfo'] = 'Name of GitHub account owner + slash + repository name';
$string['sqlsyntaxhighlight'] = 'Highlight SQL syntax';
$string['sqlsyntaxhighlightinfo'] = 'Highlight SQL syntax in code editor (CodeMirror JS library)';
$string['datatables'] = 'Enable DataTables JS library';
$string['datatablesinfo'] = 'DataTables JS library (Column sort, fixed header, search, paging...)';
$string['reporttableui'] = 'Report table UI';
$string['reporttableuiinfo'] = 'Display the report table as: Simple scrollable HTML table, jQuery with column sorting Or
DataTables JS library (Column sort, fixed header, search, paging...)';

$string['email_subject'] = 'Subject';
$string['email_message'] = 'Message';
$string['email_send'] = 'Send';

$string['sqlsecurity'] = 'SQL Security';
$string['sqlsecurityinfo'] = 'Disable for executing SQL queries with statements for inserting data';
$string['allowedsqlusers'] = 'SQL report users';
$string['allowedsqlusersinfo'] =
    'If you wish to only allow certain admin users to manage sql reports, add a list of usernames separated by commas. They must also have the block/configurable_reports:managesqlreports capability.';
$string['global'] = 'Global report';
$string['enableglobal'] = 'This is a global report (accesible from any course)';
$string['global_help'] =
    'Global report can be accessed from any course in the platform just appending &courseid=MY_COURSE_ID in the report URL';

$string['crrepository'] = 'Reports repository';
$string['crrepositoryinfo'] =
    'Remote shared repository with sample reports fully functional (Name of GitHub account owner + slash + repository name)';
$string['importfromrepository'] = 'Import report from repository';
$string['repository'] = 'Reports repository';
$string['repository_help'] = 'You can import sample reports from a public shared repository.

Please, notice that there is a daily limit of calls to the repository.

If the connection to the repository is not working, you can download manually here <a href="https://github.com/jleyva/moodle-configurable_reports_repository" target="_blank">https://github.com/jleyva/moodle-configurable_reports_repository</a> a report and then import it using the "Import report" feature displayed bellow
';
$string['reportcreated'] = 'Report successfully created';
$string['usersincohorts'] = 'User who are member of a/several cohorts';
$string['usersincohorts_summary'] = 'Only the users who are members of the selected cohorts';
$string['displayglobalreports'] = 'Display global reports';
$string['displayreportslist'] = 'Display the reports list in the block body';

$string['usercompletion'] = 'User course completion status';
$string['usercompletionsummary'] = 'Course completion status';

$string['finalgradeincurrentcourse'] = 'Final grade in current course';
$string['legacylognotenabled'] = 'Legacy logs must be enabled.
 Go to Site administration / Plugins / Logging Enable the Legacy log and inside the log settings check Log legacy data';

$string['datatables_sortascending'] = ': activate to sort column ascending';
$string['datatables_sortdescending'] = ': activate to sort column descending';
$string['datatables_first'] = 'First';
$string['datatables_last'] = 'Last';
$string['datatables_next'] = 'Next';
$string['datatables_previous'] = 'Previous';
$string['datatables_emptytable'] = 'No data available in table';
$string['datatables_info'] = 'Showing _START_ to _END_ of _TOTAL_ entries';
$string['datatables_infoempty'] = 'Showing 0 to 0 of 0 entries';
$string['datatables_infofiltered'] = '(filtered from _MAX_ total entries)';
$string['datatables_lengthmenu'] = 'Show _MENU_ entries';
$string['datatables_loadingrecords'] = 'Loading...';
$string['datatables_processing'] = 'Processing...';
$string['datatables_search'] = 'Search:';
$string['datatables_zerorecords'] = 'No matching records found';
// New features: Graph new column.

$string['others'] = 'Others';
$string['limitcategories'] = 'Limit categories in a graph';
$string['decimals'] = 'Number of decimals';
$string['sessionlimittime'] = 'Limit between clicks (in minutes)';
$string['sessionlimittime_help'] = 'The limit between clicks defines if two clicks are part of the same session or not';

$string['excludedeletedusers'] = 'Exclude deleted users (only for SQL reports)';

// Privacy provider.
$string['privacy:metadata:block_configurable_reports'] = 'The configurable reports block contains customizable course reports.';
$string['privacy:metadata:block_configurable_reports:courseid'] = 'Course ID';
$string['privacy:metadata:block_configurable_reports:ownerid'] = 'The ID of the user who created the report';
$string['privacy:metadata:block_configurable_reports:visible'] = 'Whether the report is visible or not';
$string['privacy:metadata:block_configurable_reports:global'] = 'Whether the report is accessible from all the courses or not';
$string['privacy:metadata:block_configurable_reports:name'] = 'The name of the report';
$string['privacy:metadata:block_configurable_reports:summary'] = 'The description of the report';
$string['privacy:metadata:block_configurable_reports:type'] = 'The type of the report';
$string['privacy:metadata:block_configurable_reports:components'] = 'The configuration of the report. It contains the query,
 the filters...';
$string['privacy:metadata:block_configurable_reports:lastexecutiontime'] = 'Time this report took to run last time it was executed,
 in milliseconds.';
// Filter forms.
$string['add'] = 'Add';
$string['description'] = 'Description';
$string['description_help'] = 'Text used to describe the filter that will be displayed in the summary on the filters page.';
$string['label'] = 'Label';
$string['label_help'] = 'Text describing the filter to be displayed on the report page.';
$string['idnumber'] = 'ID Number';
$string['idnumber_help'] = 'Used to differentiate between filters of the same type. Case-sensitive.
Example usage: %%FILTER_SEARCHTEXT_username:u.username:~%%';

// Pie Chart Strings.
$string['description'] = 'Description';
$string['legendheader'] = 'Mapped Palette';
$string['legendheaderdesc'] = 'Map color codes to specific keys in the pie chart legend.';
$string['piechart_label'] = 'Key - {$a}';
$string['piechart_label_color'] = 'Color - {$a}';
$string['piechart_add_colors'] = 'Add color';
$string['invalidcolorcode'] = 'Invalid color code';
$string['generalcolorpaletteheader'] = 'General color palette';
$string['generalcolorpalette'] = 'Unmapped Palette';
$string['generalcolorpalette_help'] = 'Hexadecimal color codes for general use in the pie chart. Codes should be separated
by new lines in the order you wish them to be used in the pie chart.';

$string['checksql_execution'] = 'Block Configurable Reports SQL execution';
$string['checksql_execution_ok'] = 'SQL execution is disabled.';

$string['checksql_execution_warning'] = 'It is recommended to disable SQL execution to avoid execution of arbitrary SQL code in
your server.';
$string['checksql_execution_details'] = 'By allowing SQL code execution there is a potential security issue with users adding
arbitrary code. SQL code execution should be disable to only allow SQL queries for reading/retreaving data. SQL execution can
be disabled in your config.php file by setting $CFG->block_configurable_reports_enable_sql_execution to 0';
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvdelimiterinfo'] = 'CSV delimiter: "colon" for ":", "comma" for ",", semicolon for ";",  "tab" for "\t" and "cfg" for character configured in "CFG->CSV_DELIMITER" of the config.php file.';

