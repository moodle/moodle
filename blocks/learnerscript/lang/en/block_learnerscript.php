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

/** LearnerScript
 * A Moodle block for creating customizable reports
 * @package blocks
 * @subpackage learnerscript
 * @author: Sreekanth<sreekanth@eabyas.in>
 * @date: 2017
 */
$string['pluginname'] = "Learner Script";
$string['blockname'] = "Learner Script";
// Capabilities.
$string['learnerscript:addinstance'] = 'Add a new LearnerScript Reports block';
$string['learnerscript:myaddinstance'] = 'Add a new LearnerScript Reports block to My home';
$string['learnerscript:manageownreports'] = "Manage own reports";
$string['learnerscript:managereports'] = "Mange reports";
$string['learnerscript:managesqlreports'] = "Manage SQL reports";
$string['learnerscript:viewreports'] = "View reports";
$string['learnerscript:designreport'] = "Design reports";
// Reports.
$string['report_assignment'] = 'Assignment';
$string['report_badges'] = 'Badges';
$string['report_categories'] = 'Categories';
$string['report_competencycompletion'] = 'Competency Completion';
$string['report_courseactivities'] = 'Course Activities';
$string['report_courseprofile'] = "Course profile";
$string['report_courses'] = "Courses";
$string['report_coursesoverview'] = 'Learner\'s Courses Overview';
$string['report_courseviews'] = 'Course Views';
$string['report_gradedactivity'] = 'Graded Activity';
$string['report_grades'] = 'Course Activity Grades';
$string['report_myassignments'] = 'My Assignments';
$string['report_myforums'] = 'My Forums';
$string['report_myquizs'] = 'My Quizzes';
$string['report_myscorm'] = 'My Scorm';
$string['report_noofviews'] = 'Activity Views';
$string['report_pageresourcetimespent'] = 'Content page Average timespent';
$string['report_quizzes'] = 'Quizzes';
$string['report_resources'] = 'Resources';
$string['report_resources_accessed'] = 'Resources Accessed';
$string['report_scorm'] = 'Scorm\'s';
$string['report_sql'] = "SQL";
$string['report_statistics'] = 'Statistic';
$string['report_timespent'] = 'Users TimeSpent on LMS';
$string['report_topic_wise_performance'] = 'Course Topic-wise Performance';
$string['report_useractivities'] = 'Learner\'s Course Activities';
$string['report_userassignments'] = 'Learner\'s Assignment Summary';
$string['report_userbadges'] = 'User Badges';
$string['report_usercourses'] = 'Course Learner\'s Summary';
$string['report_userprofile'] = "Users profile";
$string['report_userquizzes'] = 'Learner\'s Quizzes summary';
$string['report_users'] = "Users";
$string['report_usersresources'] = 'Learner\'s Resources Summary';
$string['report_usersscorm'] = 'Learner\'s Scorm Summary';

$string['managereports'] = "Manage reports";
$string['userprofile'] = "User Profile";
$string['report'] = "Report";
$string['reports'] = "Reports";
$string['calendar'] = "Calendar";
$string['graph'] = "Graph";
$string['filter'] = "Filter";

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
$string['reportlimitinfo'] = "Limit the number of rows that are displayed in the report table (Default is 5000 rows. Better to have some limit, so users will not over load the DB engine)";

$string['learnerscript:addinstance'] = 'Add a new LearnerScript Reports block';
$string['learnerscript:manageownreports'] = "Manage own reports";
$string['learnerscript:managereports'] = "Mange reports";
$string['learnerscript:managesqlreports'] = "Manage SQL reports";
$string['learnerscript:viewreports'] = "View reports";

$string['exportoptions'] = "Export options";
$string['field'] = "Field";

// Report form
$string['typeofreport'] = "Type of report";
$string['enablejsordering'] = "Enable JavaScript ordering";
$string['enablejspagination'] = "Enable JavaScript Pagination";
$string['export_csv'] = "Export in CSV format";
$string['export_ods'] = "Export in ODS format";
$string['export_xls'] = "Export in XLS format";
$string['export_pdf'] = "Export in PDF format";
$string['viewreport'] = "View report";
$string['norecordsfound'] = "No records found";
$string['jsordering'] = 'JavaScript Ordering';
$string['cron'] = 'Auto run daily';
$string['crondescription'] = 'Schedule this query to run each day (At night)';
$string['cron_help'] = 'Schedule this query to run each day (At night)';
$string['setcourseid'] = 'Set courseid';

// Columns
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

// Conditions
$string['conditionexpr'] = "Condition";
$string['conditionexprhelp'] = "Enter a valid condition i.e: (c1 and c2) or (c4 and c3)";
$string['noconditionsyet'] = "No conditions yet";
$string['operator'] = "Operator";
$string['value'] = "Value";

// Filter
$string['filter'] = "Filter";
$string['nofilteryet'] = "No filters yet";
$string['courses'] = "Courses";
$string['nofiltersyet'] = "No filters yet";
$string['filter_all'] = 'All';
$string['filter_apply'] = 'Apply';
$string['filter_clear'] = 'Clear';
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
$string['filtersubcategories_summary'] = 'Use: %%FILTER_CATEGORIES:mdl_course_category.path%%';
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
$string['users'] = 'Users';
$string['filterusers'] = 'System user';
$string['enrolledstudents'] = 'Enrolled students';
$string['filterusers_summary'] = 'Filter a user (by id) from system user list';
$string['filterenrolledstudents'] = 'Enrolled course students';
$string['filterenrolledstudents_summary'] = 'Filter a user (by id) from enrolled course students';
$string['student'] = 'Student';
$string['filterappnoagentcode'] = "Appno Agentcode";
$string['filterbelt'] = 'Belt';
$string['filteremployeecode'] = 'Employee Code';
$string['filterprimarytrainer'] = 'Primary Trainer';
$string['filterprimarytrainercode'] = 'Primary Trainer Code';
$string['filtertrainercode'] = 'Trainer Code';
$string['appnoagentcode'] = "Appno Agentcode";
$string['belt'] = 'Belt';
$string['employeecode'] = 'Employee Code';
$string['primarytrainer'] = 'Primary Trainer';
$string['primarytrainercode'] = 'Primary Trainer Code';
$string['trainercode'] = 'Trainer Code';
$string['customroles'] = 'Roles';

// Calcs
$string['nocalcsyet'] = "No calculations yet";

// Plot
$string['noplotyet'] = "No plots yet";

// Permissions
$string['nopermissionsyet'] = "No permissions yet";
$string['chartname'] = "Chart Name";
$string['chartnamerequired'] = "Please enter the chart name";
$string['year'] = 'Year';
$string['custom'] = 'Custom';
$string['all'] = 'All';
// Ordering
$string['noorderingyet'] = "No ordering yet";
$string['userfieldorder'] = "User field order";
// Plugins
$string['coursefield'] = "Course field";
$string['ccoursefield'] = "Course field condition";
$string['roleusersn'] = "Number of users with role...";
$string['coursecategory'] = "Course in category";
$string['filtercourses'] = "Courses";
$string['filtercourses_summary'] = "This filter shows a list of courses. Only one course can be selected at the same time";
$string['roleincourse'] = "User with the selected role/s";
$string['reportscapabilities'] = "Report Capabilities";
$string['reportscapabilities_summary'] = "Users with the capability moodle/site:viewreports enabled";
$string['sum'] = "Sum";
$string['max'] = "Maximum";
$string['min'] = "Minimum";
$string['average'] = "Average";
$string['pie'] = "Pie";
$string['piesummary'] = "A pie graph";
$string['pieareaname'] = "Name";
$string['pieareavalue'] = "Value";
$string['piesummary'] = "A pie graph";
$string['serieslabel'] = "Series Label";
$string['showlegend'] = "Show legend";
$string['datalabels'] = "Data Labels";

$string['anyone'] = "Anyone";
$string['anyone_summary'] = "Any user in the LMS will be able to view this report";

$string['currentuserfinalgrade'] = "Current user final grade in course";

$string['currentuserfinalgrade_summary'] = "This column shows the final grade of the current user in the row-course";
$string['userfield'] = "User profile field";

$string['cuserfield'] = "User field condition";
$string['direction'] = "Direction";

$string['courseparent'] = "Courses whose parent is";
$string['coursechild'] = "Courses that are children of";
$string['table'] = 'Report table';
$string['currentusercourses'] = "Current user enrolled courses";
$string['currentusercourses_summary'] = "A list of the current users courses (only visible courses)";
$string['currentreportcourse'] = "Current report course";
$string['currentreportcourse_summary'] = "The course where the report has been created";

$string['coursefieldorder'] = "Course field order";

$string['fcoursefield'] = "Course field filter";
$string['usersincoursereport'] = "Any user in the current report course";

$string['groupvalues'] = "Group same values (sum)";
$string['fuserfield'] = "User field filter";

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
$string['starttime'] = "Start Date";
$string['endtime'] = "End Date";

$string['fromtime'] = "From";
$string['totime'] = "To";

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

$string['badsize'] = 'Incorrect size, it must be numeric.';
$string['badtablewidth'] = 'Incorrect width, it must be in &#37; or absolute value';
$string['missingcolumn'] = "A column is required";
$string['error_operator'] = "Operator not allowed";

$string['error_field'] = "Field not allowed";
$string['error_value_expected_integer'] = "Expected integer value";
$string['badconditionexpr'] = "Incorrect condition expression";

$string['notallowedwords'] = "Not allowed words";
$string['nosemicolon'] = "No semicolon";
$string['noexplicitprefix'] = "No explicit prefix";
$string['queryfailed'] = "Query failed";
$string['norowsreturned'] = "No rows returned";

$string['listofsqlreports'] = 'Press F11 when cursor is in the editor to toggle full screen editing. Esc can also be used to exit full screen editing.<br/><br/><a href="http://docs.moodle.org/en/ad-hoc_contributed_reports" target="_blank">List of SQL Contributed reports</a>';

$string['usersincoursereport_summary'] = "Any user in the current report course";

$string['printreport'] = 'Print report';

$string['importreport'] = "Import report";
$string['exportreport'] = "Export report";

$string['download'] = "Download";

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

$string['line'] = 'Line';
$string['userstats'] = 'User statistics';
$string['stat'] = 'Statistic';
$string['statslogins'] = 'Logins in the platform';
$string['activityview'] = 'Activity views';
$string['activitypost'] = 'Activity posts';
$string[''] = '';
$string['globalstatsshouldbeenabled'] = 'Site statistics must be enabled. Go to Admin -> Server -> Statistics';

$string['xaxis'] = 'X Axis';
$string['yaxis'] = 'Y Axis';
$string['yaxis_line'] = 'Line - Y Axis';
$string['yaxis_bar'] = 'Column - Y Axis';
$string['barlinecolumnsequal'] = 'Same values not allowed on both types.';
$string['serieid'] = 'Series column';
$string['groupseries'] = 'Group series';
$string['linesummary'] = 'A line graph with multiple series of data';

$string['bar'] = 'Bar';
$string['barsummary'] = 'A bar graph with multiple series of data';

$string['coursestats'] = 'Course stats';
$string['statstotalenrolments'] = 'Total enrolments';
$string['statsactiveenrolments'] = 'Active (last week) enrolments';
$string['youmustselectarole'] = 'At least a role is required';

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

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';

$string['comp_calculations'] = 'Calcs';
$string['comp_calculations_help'] = '<p>Here you can add calculations for columns, i.e: average of number of users enrolled in courses</p>';
$string['comp_conditions'] = 'Conditions';
$string['comp_conditions_help'] = '<p>Here you can define the conditions (i.e, only courses from this category, only users from Spain, etc.. </p>

<p>You can add a logical expression if you are using more than one condition.</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_customsql'] = 'Custom SQL';
$string['comp_customsql_help'] = '<p>Add a working SQL query. Do no use the moodle database prefix $CFG->prefix instead use "prefix_" without quotes</p>
<p>Example: SELECT * FROM prefix_course</p>

<p>You can find a lot of SQL Reports here: <a href="http://docs.moodle.org/en/ad-hoc_contributed_reports" target="_blank">ad-hoc contributed reports</a></p>

<p>Since this block supports Tim Hunt\'s CustomSQL Queries Reports, you can use any query.</p>

<p>Remember to add a "Time filter" if you are going to use reports with time tokens. </p>

<p>For using filters see: <a href="http://docs.moodle.org/en/blocks/learnerscript/#Creating_a_SQL_Report" target="_blank">Creating a SQL Report Tutorial</a></p>';

$string['comp_ordering'] = 'Ordering';
$string['comp_ordering_help'] = '<p>Here you can choose how to order the report using fields and directions</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_permissions'] = 'Permissions';
$string['comp_permissions_help'] = '<p>Here you can choose who can view a report.</p>

<p>You can add a logical expression to calculate the final permission if you are using more than one condition.</p>
<p>Final condition is the combination of conditions and role conditions</p>';
$string['comp_plot'] = 'Plot';
$string['comp_plot_help'] = '<p>Here you can add graphs to your report based on the report columns and values</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_template'] = 'Template';
$string['comp_template_help'] = '<p>You can modify the report\'s layout by creating a template</p>

<p>For creating a template see the replacemnet marks you can use in header, footer and for each report record using the help buttons or the information displayed in the same page.</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_filters'] = 'Filters';
$string['comp_filters_help'] = '<p>Here you can choose which filters will be displayed</p>

<p>A filter lets an user to choose columns from the report to filter the report results</p>

<p>For using filters if your report type is SQL see: <a href="http://docs.moodle.org/en/blocks/learnerscript/#Creating_a_SQL_Report" target="_blank">Creating a SQL Report Tutorial</a></p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';
$string['comp_columns'] = 'Columns';
$string['comp_columns_help'] = '<p>Here you can choose the different columns of your report depending on the type of report</p>

<p>More help: <a href="http://docs.moodle.org/en/blocks/learnerscript/" target="_blank">Plugin documentation</a></p>';

$string['coursecategories'] = 'Categories';
$string['filtercoursecategories'] = 'Select Category';
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
$string['reporttableuiinfo'] = 'Display the report table as: Simple scrollable HTML table, jQuery with column sorting Or DataTables JS library (Column sort, fixed header, search, paging...)';
$string['reportchartui'] = 'Report chart UI';
$string['reportchartuiinfo'] = 'Display the report chart as: Simple image graphs, Using highcharts JS library Or d3 JS library';

$string['email_subject'] = 'Subject';
$string['email_message'] = 'Message';
$string['email_send'] = 'Send';

$string['sqlsecurity'] = 'SQL Security';
$string['sqlsecurityinfo'] = 'Disable for executing SQL queries with statements for inserting data (GitHub account owner + slash + repository name)';

$string['global'] = 'Global report';
$string['enableglobal'] = 'This is a global report (accesible from any course)';
$string['global_help'] = 'Global report can be accessed from any course in the platform just appending &courseid=MY_COURSE_ID in the report URL';
$string['disabletable'] = 'Disable table';
$string['enabletable'] = 'Disable for Report table';

$string['crrepository'] = 'Reports repository';
$string['crrepositoryinfo'] = 'Remote shared repository with sample reports fully functional';
$string['importfromrepository'] = 'Import report from repository';
$string['repository'] = 'Reports repository';
$string['repository_help'] = 'You can import sample reports from a public shared repository.

Please, notice that there is a daily limit of calls to the repository.

If the connection to the repository is not working, you can download manually here <a href="https://github.com/jleyva/moodle-learnerscript_repository" target="_blank">https://github.com/jleyva/moodle-learnerscript_repository</a> a report and then import it using the "Import report" feature displayed bellow
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

$string['scheduledreportsettings'] = 'Scheduled report settings';
$string['export'] = 'Export';
$string['schedule'] = 'Schedule';
$string['schedulereport'] = 'Schedule Report';
$string['updatefrequency'] = 'Update Frequency';
$string['scormtimespent'] = 'Scorm TimeSpent';
$string['userscormtimespent'] = 'User Scorm TimeSpent';
$string['userquiztimespent'] = 'User quiz timespent';
$string['userbigbluebuttonbnspent'] = 'User bigbluebuttonbn timespent';
$string['daily'] = 'Daily';
$string['weekly'] = 'Weekly';
$string['monthly'] = 'Monthly';
$string['at'] = 'at';
$string['on'] = 'on';
$string['onthe'] = 'on the';
$string['reportname'] = 'Name';
$string['exportformat'] = 'Export Format';
///////sarath added this string////
$string['users_data'] = 'User';
$string['reportname'] = 'Name';
$string['PleaseSelectRole'] = 'Please Select Role';
$string['PleaseSelectUser'] = 'Please  Select User';
$string['addmoreusers'] = '';
$string['viewusers'] = '';
$string['bulkupload'] = 'Bulkupload';
$string['uploadusers'] = 'Upload Users';
$string['sample_excel'] = 'Sample Excel';
$string['sample_csv'] = 'Sample Csv';
$string['deletescheduledreport'] = 'Delete Schedule Report';
$string['delconfirm'] = 'Are you sure you want to delete this schedule';
$string['frequency'] = 'Frequency';
$string['Wednesday'] = 'Wednesday';
$string['Sunday'] = 'Sunday';
$string['Tuesday'] = 'Tuesday';
$string['Thursday'] = 'Thursday';
$string['Friday'] = 'Friday';
$string['Saturday'] = 'Saturday';
$string['Monday'] = 'Monday';
$string['dependency'] = 'Schedule';
$string['schedule'] = 'Schedule';

////sarath endeed/////
//$string['schedulereportdescription'] = 'Scheduled Report';
$string['addschedulereport'] = 'Add schedule report';
$string['editscheduledreport'] = 'Edit scheduled report';
$string['exportfilesystem'] = 'Export to File system';
$string['exportfilesystempath'] = 'Export file system path';
$string['exportfilesystempathdesc'] = 'Absolute file system path to a writeable directory where reports can be exported and stored.';
$string['exporttoemail'] = 'Send report to mail';
$string['exporttoemailandsave'] = 'Save to file system and send email';
$string['exporttosave'] = 'Save to file system';
$string['exportfilesystemoptions'] = 'Export process';
$string['odsformat'] = 'ODS Format';
$string['pdfformat'] = 'PDF Format';
$string['xlsformat'] = 'Excel Format';
$string['csvformat'] = 'CSV Format';
$string['scheduledreportmessage'] = '<p>Hi,</p>
<p>Here attached a copy of the \'{$a->reportname}\' report in {$a->exporttype}.</p>
<p>You can also view this report online at: {$a->reporturl}.</p>
<p>You are scheduled to receive this report {$a->schedule}.</p>

<p>{$a->nodata}</p>

<p>Regards,</p>
<p>{$a->admin}</p>';
$string['error:failedtoremovetempfile'] = 'Failed to remove temporary report export file';

/* Added by sowmya */
// Strings for Total Trained Reports
$string['state'] = 'States';
$string['month'] = 'Training Month';
$string['reports_view'] = "Reports View";
$string['startyear'] = 'Year';
$string['filter_year'] = 'Filter Year';
$string['filteryear_summary'] = 'Filter Year Summary';
$string['trainertype'] = 'Trainer Type';
$string['courseduration'] = 'Course Duration';
$string['coursename'] = 'Course Name';
/* settings strings added by anusha */
$string['learnerscriptreports'] = "LearnerScript Reports";
$string['serialkey'] = "Serial Key";
$string['serialkeyinfo'] = "Serial key need to be valid.";
$string['url'] = "URL";
$string['licence'] = 'Licence';
$string['urlinfo'] = "Enter the url for access.";
$string['analytics_color'] = "Export Header Color";
$string['analytics_color_desc'] = "Export Header Color for Reports";
$string['logo'] = "Logo";
$string['logo_desc'] = "Logo for Reports";
$string['filteryearnumeric_list'] = "2010,2011,2012,2013,2014,2015,2016,2017";
$string['selectusers'] = 'Select Users Here';
$string['viewschusers'] = 'View scheduled Users List';
$string['combination'] = 'Combination';
$string['combinationsummary'] = 'A combination graph with multiple graphs';
$string['columnsummary'] = 'A column grpah with multiple values';
$string['listofcharts'] = 'List of charts';
$string['enabletabs'] = 'Enable tabs for charts';
$string['enabletabs_help'] = 'To enable and view charts in tabs format';

// Schedule BulkUpload strings
$string['uploaddec'] = 'Upload list of users to schedule reports for a report.';
$string['uploaddec_help'] = 'Upload list of users to schedule reports for a report.';
$string['dailysampleinfo'] = ' This following details given example for daily schedule type only.';
$string['weeklysampleinfo'] = ' This following details given example for weekly schedule type only.';
$string['monthlysampleinfo'] = ' This following details given example for monthly schedule type only.';
$string['mandatoryinfo'] = ' All fields  are the mandatory fields. Please delete the rows are 2, 6, 14, 20 before uploading.';
$string['csvdelimiter'] = 'CSV delimiter';
$string['csvdelimiter_help'] = 'CSV delimiter of the CSV file.';
$string['csvfileerror'] = 'There is something wrong with the format of the CSV file. Please check the number of headings and columns match, and that the delimiter and file encoding are correct: {$a}';
$string['csvline'] = 'Line';
$string['encoding'] = 'Encoding';
$string['encoding_help'] = 'Encoding of the CSV file.';
$string['rowpreviewnum'] = 'Preview rows';
$string['rowpreviewnum_help'] = 'Number of rows from the CSV file that will be previewed in the next page. This option exists in
order to limit the next page size.';
$string['noschedule'] = 'Schedules Not Available.';
$string['bulk_upload'] = 'Bulk Upload';
$string['exporttofilesystem'] = 'Export to filesystem';
$string['nocourseexist'] = 'coursedoesnotexists';
$string['noreportexists'] = 'reportdoesnotexists';
$string['nocourseid'] = 'No such course id';
$string['badpermissions'] = 'badpermissions';
$string['badcomponent'] = 'badcomponent';
$string['noplugin'] = 'nosuchplugin';
$string['errorsaving'] = 'errorsaving';
$string['Pluginnotfound'] = 'Plugin not found';
$string['errorimporting'] = 'errorimporting';
$string['nodirectaccess'] = 'Direct access to this script is forbidden.';
$string['databaseconnectionerror'] = 'An error occurred while connecting to the database.';
$string['errorinfo'] = 'The error reported by the server was: ';
$string['sentemailforreport'] = 'Sent email for report ';
$string['noreportemailsent'] = 'No scheduled report email has been send';
$string['sendingemailreportfailed'] = 'Failed to send email for report';
$string['listofusers'] = 'List of Users';
$string['reporttypeerror'] = 'report type error';
$string['altreportimage'] = 'Alt Image Text';
$string['reportheader'] = 'Report Header';
$string['schedule_reports'] = 'Schedule Reports';
$string['badpermissions'] = 'Bad permission';
$string['missingparam'] = '{$a} missing value.';
$string['selectroles'] = 'Select a Role.';
$string['selectusers'] = 'Select Users';
$string['sendemails'] = 'Send Emails';
$string['fsearchuserfield'] = 'User Fields';
$string['licencemissing'] = 'Licence Key Missing';
$string['totalcount'] = 'Total Count';
$string['completed'] = 'Completed';
$string['progress'] = 'Progress';
$string['avggrade'] = 'Avg. Grade';
$string['dynamiccolumn'] = 'Column';
$string['filteractivities'] = 'Activities';
$string['activities'] = 'Activities';
$string['filtermodules'] = 'Module';
$string['modules'] = 'Module';
$string['coursesoverview'] = 'Courses Overview';
$string['activityinfo'] = 'Activity Information';
$string['coursesoverview'] = 'Courses Overview';
$string['courseparticipation'] = 'Course Participation';
$string['detailusercourseinfo'] = 'User Courses Information';
$string['listofactivities'] = 'List of Activities in a course';
$string['courseactivitiesinfo'] = 'Course Activity Info';
$string['detailcourseinfo'] = 'Detailed Course Info';
$string['userlist'] = 'Users List';
$string['topicwiseperformance'] = 'Topic Wise Performance Columns';
$string['scormactivitiescourse'] = 'SCORM Activities Course Columns';
$string['competencycompletion'] = 'Competency Completion Reports';
$string['competencycompletioncolumns'] = 'Competency Completion Columns';

$string['myquizs'] = 'My Quizzes';
$string['userquizzes'] = 'User Quizzes';
$string['modules'] = 'Modules';
$string['activitystatuscolumns'] = 'Activity Status Columns';
$string['id'] = 'id';
$string['percentage'] = 'Percentage';
$string['uploaderrors'] = 'Uploaded Errors';
$string['filtermodules_summary'] = 'Module Filters';
$string['filteractivities_summary'] = 'Activity Filters';
$string['gradecolumns'] = 'User Activity Grade Columns';
$string['usercoursescolumns'] = 'User Courses Columns';
$string['studentoverallperformancecolumns'] = 'Student Overall Performance Columns';
$string['listofactivitiescolumns'] = 'List Of activities Columns';
$string['detailusercourseinfocolumns'] = 'User Courses Information Columns';
$string['courseactivitiesinfocolumns'] = 'Course Activities Information Columns';
$string['coursesoverviewcolumns'] = 'Courses Overview Columns';
$string['filtermodules_summary'] = 'This filter shows a list of modules. Only one module can be selected at the same time';
$string['courseparticipationcolumns'] = 'Course Participation Columns';
$string['uniqueloginscolumns'] = 'Log Report Columns';
$string['myquizscolumns'] = 'My Quiz Columns';
$string['userquizzescolumns'] = 'User Quizzes Columns';
$string['quizs'] = 'Quizs';
$string['userassignments'] = 'User Assignments';
$string['assignmentcolumns'] = 'User Assignments Columns';
$string['assignstatuscolumns'] = 'Assignments Columns';
$string['myassignments'] = 'My Assignments';
$string['myassignmentscolumns'] = 'My Assignments Columns';
$string['myforumscolumns'] = 'My Forums Columns';
$string['myforums'] = 'My Forums';
$string['design'] = 'Design';
$string['statisticsreportsnotavailable'] = 'Reports not available';
$string['reportsnotavaliable'] = 'Reports not available';
$string['nodataavailable'] = 'No data available';
$string['graphnotfound'] = 'Graph Not Found';
$string['startdateerror'] = 'Start date should not more than current date.';
$string['enddateerror'] = 'Start date should not more than end date.';
$string['xandynotequal'] = 'Series column and Y-Axis should not equal.';
$string['supplyvalue'] = 'You must supply a value here.';
$string['deleteallconfirm'] = 'Are you sure, want to delete this?';
$string['eventcreate_report'] = 'Report Created';
$string['eventupdate_report'] = 'Report Updated';
$string['eventdelete_report'] = 'Report Deleted';
$string['eventview_report'] = 'Report Viewed';
$string['eventschedule_report'] = 'Reprot Scheduled';
$string['spacevalidation'] = 'You must supply the value without space';
$string['save'] = 'Save';
$string['enable_exports'] = 'Enable Exports';
$string['preview'] = 'Preview';
$string['courseaveragecolumns'] = 'Course Average Columns';
$string['noyaxis'] = 'Previously configured Y-axis elements <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['areaname'] = 'Previously configured area name <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['areavalue'] = 'Previously configured area value <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['noseries'] = 'Previously configured series <b>{$a}</b> not available now. Please reconfigure graph <br />';
$string['applypurify'] = 'Please select below required parameters to get report.';
$string['nolsinstance'] = 'LearnerScript Report Instances not configured in this page.';
$string['getreport'] = 'Get Report';
$string['worldmap'] = 'World Map';
$string['worldmapareaname'] = 'Area';
$string['worldmapareavalue'] = 'Value';
$string['activityfield'] = 'Activity Field';
$string['activitytype'] = 'Activity Type';
$string['finalgrade'] = 'Final Grade';
$string['grademax'] = 'Max grade';
$string['resourcescolumns'] = 'Resources Columns';
$string['resourcesaccessedcolumns'] = 'Resources Accessed Columns';
$string['resourcesaccessed'] = 'Resources Accessed';
$string['badges'] = 'Badges';
$string['badgename'] = 'Badge Name';
$string['userbadges'] = 'My Badges';
$string['timecreated'] = 'Time Created';
$string['criteria'] = 'Criteria';
$string['issuername'] = 'Issuer Name';
$string['description'] = 'Description';
$string['course'] = 'Course';
$string['recipients'] = 'Recipients';
$string['treemap'] = 'Tree Map';
$string['no_report_columns'] = 'Add columns in design to view the report.';
$string['help_1'] = '
<table border="1" width="80%">
	<thead>
		<tr><th style="text-align:center;" colspan=2>Mandatory Fields</th><tr>
		<tr><th>Field</th><th>Restriction</th></tr>
	</thead>
	<tbody>
		<tr><td>Email</td><td>Enter email, avoid additional spaces.</td></tr>
		<tr><td>Export Format</td><td>Enter export format, avoid additional spaces.</td></tr>
		<tr><td>Export to filesystem</td><td>Enter export to filesystem, avoid additional spaces.</td></tr>
		<tr><td>Frequency</td><td>Enter frequency, avoid additional spaces.</td></tr>
		<tr><td>Schedule</td><td>Enter schedule time, avoid additional spaces.</td></tr>
		<tr><td>Role</td><td>Enter role id ({$a->rolelist}), avoid additional spaces.</td></tr>
		<tr><td>Context Level</td><td>Enter context level (10 : System level, 40 : Category level, 50 : course level), avoid additional spaces. 
</td></tr>
	</tbody>
</table>';
$string['manual'] = 'Help Manual';
$string['back_upload'] = 'Back To Upload';
$string['helpmanual'] = 'Download sample CSV sheet and fill the field values in the format specified below.';
$string['uploadscheduletime'] = 'Upload schedule times';
$string['treemapareaid'] = 'Area Code';
$string['treemapareaname'] = 'Area Name';
$string['treemapareavalue'] = 'Value';
$string['assignment'] = 'Assignment Columns';
$string['activitystats'] = 'Activity Stats';
$string['gradepass'] = 'Pass Grade';
$string['grademin'] = 'Min Grade';
$string['gradedactivity'] = 'Graded Activities';
$string['usercolumns'] = 'User Columns';
$string['enabled'] = 'Enabled';
$string['disabled'] = 'Disabled';
$string['timespentcolumns'] = 'TimeSpent Columns';

$string['useractivities'] = 'User Activities';
$string['useractivitiescolumns'] = 'User Activity Columns';
$string['myscormcolumns'] = 'My Scorm Columns';
$string['scorm'] = 'Scorm Columns';
$string['usersresources'] = 'Users Resources Columns';
$string['usersscormcolumns'] = 'Users Scorm Columns';
$string['coursescolumns'] = 'Courses columns';
$string['scormfield'] = 'SCORM fields';
$string['quizzescolumns'] = 'Quizzes Columns';
$string['quizzes'] = 'Quizzes';
$string['assignmentfield'] = 'Assignment Fields';
$string['quizfield'] = 'Quiz Field';
$string['courseactivitiescolumns'] = 'Course Activities Columns';
$string['configcoursedashboard'] = 'Config Course Dashboard';
$string['pageresourcetimespentcolumns'] = 'PAGE Resource TimeSpent Columns';
$string['filter_course'] = 'Select Course';
$string['filter_courses'] = 'Select Course';
$string['filter_user'] = 'Select User';
$string['filter_category'] = 'Select Category';
$string['filter_role'] = 'Select Role';
$string['filter_module'] = 'Select Module';
$string['inprogress'] = 'In Progress';
$string['notyetstarted'] = 'Not Yet Started';
$string['notattempted'] = 'Not attempted';
$string['nocomponent'] = 'Component Not Available';
$string['notcompleted'] = 'In Progress';
$string['nouserrole'] = 'User Not Available for the selected role.';
$string['select_activity'] = 'Select Activity';
$string['roleincourseconditionexpr'] = 'Roles Condition';
$string['lsresetconfig'] = 'Reset LearnerScript Config';
$string['deleteconfirmation'] = 'Delete Confirmation';
$string['dashboard'] = 'LearnerScript Dashboard';
$string['courseviews'] = 'Course Views Columns';
$string['noofviews'] = 'Activity Views Columns';
$string['installreqplugins'] = 'Install {$a} plugin for Learnerscript';
$string['enablereqplugins'] = 'Enable {$a} plugin for Learnerscript';
$string['selectedfilter'] = '{$a} :';
$string['reqlicencekey'] = '<div class="alert alert-danger">License Key Is Required</div>';
$string['lsconfigtitle'] = 'LearnerScript Configuration';
$string['lsreportsconfig'] = 'Configuring LearnerScript';
$string['lsreportsconfigdone'] = 'LearnerScript Reports already Configured';
$string['limit'] = 'Limit';
$string['sortby'] = 'Sort By';
$string['sendmessage'] = 'Send Message';
$string['messageconformation'] = 'Message Sent';
$string['messageconformationsent'] = 'Message Sent Successfully';
$string['manageschusers'] = 'Manage Scheduled Users';
$string['adv'] = 'Adv';
$string['customdate'] = 'Start Date - End Date ';
$string['licencekeyrequired'] = 'License Key Is Required';
$string['selectcalc'] = 'Select Calc';
$string['selectordering'] = 'Select Ordering';
$string['generatedmodel'] = 'Generated Model';
$string['graphcannotbedeleted'] = 'You Cannot Delete Last Graph in this Report';
$string['report_users_help'] = '<p><strong>Description:</strong>Users report displays the details of the user course enrollments and their progress. This report enables the user to know the learner activities details</p><table class = "help_icon_table" border="1"><tbody><tr><th><p>Columns</p></th><th><p>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of user fields like full name, username, email etc..</p></td></tr><tr><td><p>Enrolled Courses</p></td><td><p>Courses enrolled by each user</p></td></tr><tr><td><p>In progress</p></td><td><p>Courses in progress by each user</p></td></tr><tr><td><p>Completed</p></td><td><p>Course completed by each user</p></td></tr><tr><td><p>Progress</p></td><td><p>(Number of course enrolled - Number of courses completed) / 100</p></td></tr><tr><td><p>Badges</p></td><td><p>Total number of badges of each learner from all enrolled courses and site level</p></td></tr><tr><td><p>Status</p></td><td><p>Status of each learner</p></td></tr></tbody></table>';
$string['report_assignment_help'] = '<p><strong>Description: </strong>Using this report user can have the complete overview of the Assignment user can check the no. of users submitted, completed assignments, grading, non-graded user and total time spent on the assignment.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Assignment</p></td><td><p>List of Assignment fields like name, course, due date etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each assignment</p></td></tr><tr><td><p>Total Learners</p></td><td><p>Total number of learners in each assignment</p></td></tr><tr><td><p>Submitted Learners</p></td><td><p>Total number of learners submitted in each assignment</p></td></tr><tr><td><p>Completed Learners</p></td><td><p>Total number of learners completed in each assignment</p></td></tr><tr><td><p>Non- Graded Learners</p></td><td><p>Total number of learners Non- Graded in each assignment</p></td></tr><tr><td><p>Pass Grade</p></td><td><p>Pass grade of each Assignment</p></td></tr><tr><td><p>Max Grade</p></td><td><p>Maximum grade of each Assignment</p></td></tr><tr><td><p>Average Grade</p></td><td><p>Average grade of learners in each Assignment</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners in each Assignment</p></td></tr><tr><td><p>Number of Views</p></td><td><p>Number of hits by all learners in each Assignment</p></td></tr></tbody></table>';
$string['report_badges_help'] = '<p><strong>Description: </strong>This report shows the complete badge summary. Using this report user can have a complete overview of badge details like badge issuer, course to which the badge belongs to, completion criteria, description of badge, recipients of the particular badge and expiry date.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Badge</p></td><td><p>Badge name</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each badge</p></td></tr><tr><td><p>Issuer</p></td><td><p>Badge issuer name</p></td></tr><tr><td><p>Recipients</p></td><td><p>Badge achieved by number of learners</p></td></tr><tr><td><p>Created On</p></td><td><p>Badge created date and time</p></td></tr><tr><td><p>Issued On</p></td><td><p>Badge issued date and time</p></td></tr><tr><td><p>Description</p></td><td><p>Badge description</p></td></tr><tr><td><p>Completion Criteria</p></td><td><p>Completion criteria for each badge</p></td></tr><tr><td><p>Expiry date</p></td><td><p>Badge expiry date</p></td></tr></tbody></table>';
$string['report_courseactivities_help'] = '<p><strong>Description: </strong>This report gives the overview of the course activities and the activity types, learner status whether user has completed the activity or not, there grading, time spent and the number of views.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Activity</p></td><td><p>List of activity fields like name, course etc&hellip;</p></td></tr><tr><td><p>Activity Type</p></td><td><p>Type of activity for each activity in selected course</p></td></tr><tr><td><p>Learners Completed</p></td><td><p>Total number of completed learners of each activity in selected course</p></td></tr><tr><td><p>Pass Grade</p></td><td><p>Pass grade of each selected course activity</p></td></tr><tr><td><p>Max Grade</p></td><td><p>Maximum grade of each selected course activity</p></td></tr><tr><td><p>Lowest Grade</p></td><td><p>Lowest grade of each selected course activity</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade of each selected course activity</p></td></tr><tr><td><p>Average Grade</p></td><td><p>Average grade of each selected course activity</p></td></tr><tr><td><p>Progress</p></td><td><p>(Total number of learners - completed number of learners)/100</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by selected for each selected course activity</p></td></tr><tr><td><p>Number of Views by learner</p></td><td><p>Total number of hits for each selected course activity</p></td></tr><tr><td><p>Grades</p></td><td><p>Grade achieved for each selected course activity</p></td></tr></tbody></table>';
$string['report_courses_help']='<p><strong>Description:</strong> Course report helps user to understand the status and progress of functions which are carried within a course. (like users, completions, grading in a course and summary of activities)</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Course field</p></td><td><p>List of course fields like full name, short name, category etc&hellip;</p></td></tr><tr><td><p>Enrolled Learners</p></td><td><p>Enrolled students for each course</p></td></tr><tr><td><p>Completed Learners</p></td><td><p>Completed students for each course</p></td></tr><tr><td><p>Total Activities</p></td><td><p>Count of activities for each course</p></td></tr><tr><td><p>Lowest Grade</p></td><td><p>Lowest grade achieved by learner in a course</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade achieved by learner in a course</p></td></tr><tr><td><p>Number of views</p></td><td><p>Number of hits by all learners for each course</p></td></tr><tr><td><p>Overall Average Grade</p></td><td><p>Average grade of learners for each course</p></td></tr><tr><td><p>Progress</p></td><td><p>(Total number of learners - Completed learners) /100</p></td></tr><tr><td><p>Enrolment methods</p></td><td><p>Number of methods to enrol each course</p></td></tr><tr><td><p>Badges</p></td><td><p>Number of badges enabled for each course</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners for each course</p></td></tr></tbody></table>';
$string['report_coursesoverview_help'] = '<p><strong>Description: </strong>Using this report user can see the list of courses which are enrolled by a particular learner and the course status by column in progress, completed and the overall grading achieved by the learner for that particular course. A learner can be select by using filter.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Course name</p></td><td><p>List of course fields like full name, Short name, category etc&hellip;</p></td></tr><tr><td><p>Total activities</p></td><td><p>Total number of activities in each course for selected learner</p></td></tr><tr><td><p>In-progress activities</p></td><td><p>Total number of in-progress activities in each course for selected learner</p></td></tr><tr><td><p>Completed activities</p></td><td><p>Total number of completed activities in each course for selected learner</p></td></tr><tr><td><p>Overall Grade</p></td><td><p>Final grade for each course for selected learner</p></td></tr></tbody></table>';
$string['report_gradedactivity_help'] = '<p><strong>Description:</strong> Graded Activity Information shows the list of activities which are graded items and there detailed gradings. </p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Activity</p></td><td><p>Activity name</p></td></tr><tr><td><p>Activity Type</p></td><td><p>Type of activity</p></td></tr><tr><td><p>Course</p></td><td><p>Course name of each activity</p></td></tr><tr><td><p>Lowest grade</p></td><td><p>Lowest grade achieved by learner in an activity</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade achieved by learner in an activity</p></td></tr><tr><td><p>Average Grade</p></td><td><p>Average grade of learners for each activity</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners for each activity</p></td></tr><tr><td><p>Number of views</p></td><td><p>Number of hits by all learners for each activity</p></td></tr></tbody></table>';
$string['report_grades_help'] = '<p><Strong>Description:</strong>This report helps the user to know the grades of the learners according to the course wise and activity wise. User can select the course and activity from the filter drilldown.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc&hellip;</p></td></tr><tr><td><p>Grade</p></td><td><p>Grade of activity for selected learner</p></td></tr><tr><td><p>Status</p></td><td><p>Selected learner activity completion status</p></td></tr></tbody></table>';
$string['report_myassignments_help'] = '<p><strong>Description: </strong>This report shows the list of Assignment in a course to which user has enrolled to the in detailed information like number of submissions by user, pass grade, max grade in the assignment, lowest and the status of assignment whether it is started or not.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Assignment</p></td><td><p>List of Assignment fields like name, course, duedate etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each assignment</p></td></tr><tr><td><p>Number of Submissions</p></td><td><p>Total number of submitted assignment by current logged-in learner</p></td></tr><tr><td><p>Pass Grade</p></td><td><p>Pass grade of each Assignment</p></td></tr><tr><td><p>Max Grade</p></td><td><p>maximum grade of each Assignment</p></td></tr><tr><td><p>Final Grade</p></td><td><p>Final grade of each Assignment</p></td></tr><tr><td><p>Lowest Grade</p></td><td><p>Lowest grade of each Assignment</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade of each Assignment</p></td></tr><tr><td><p>Status</p></td><td><p>Status of Learner for each Assignment</p></td></tr></tbody></table>';
$string['report_myquizs_help'] = '<p><strong>Description: </strong>This report shows the Quizzes in the courses which user has enrolled to and the number of attempts he performed the quiz, minimum pas grade, highest grade in the quiz, status of the quiz whether it is attempted or not etc.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Quiz</p></td><td><p>List of quiz fields like name, course, time open etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name of each quiz</p></td></tr><tr><td><p>Number of Attempts</p></td><td><p>Total number of attempts by current logged-in learner in each quiz</p></td></tr><tr><td><p>Pass Grade</p></td><td><p>Pass grade of each quiz for current learner</p></td></tr><tr><td><p>Max Grade</p></td><td><p>Maximum grade of each quiz for current learner</p></td></tr><tr><td><p>Min Grade</p></td><td><p>Minimum grade of each quiz for current learner</p></td></tr><tr><td><p>Final Grade</p></td><td><p>Final grade of each quiz for currents learner</p></td></tr><tr><td><p>Lowest Grade</p></td><td><p>Lowest grade of each quiz for current learner</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade of each quiz for current learner</p></td></tr><tr><td><p>Activity State</p></td><td><p>Activity state of current learner in each quiz</p></td></tr><tr><td><p>Status</p></td><td><p>Status of current learner in each quiz</p></td></tr></tbody></table>';
$string['report_myscorm_help'] = '<p><strong>Description: </strong>With this report user can have a view of SCORMs in the enrolled courses and the detailed overview of attempted SCORMs, number of attempts made, gradings and accessed information.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>SCORM</p></td><td><p>List of SCORM fields like name, version, SCORM type etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each SCORM</p></td></tr><tr><td><p>Attempt</p></td><td><p>Total number of attempts made by current logged-in learner in each SCORM</p></td></tr><tr><td><p>Grade</p></td><td><p>Grade achieved by current learner in each SCORM</p></td></tr><tr><td><p>Activity State</p></td><td><p>Activity state of current learner in each SCORM</p></td></tr><tr><td><p>First Access</p></td><td><p>Time accessed by current logged in learner for the first time in all SCORM\'s</p></td></tr><tr><td><p>Last Access</p></td><td><p>Time accessed by current logged in learner for the last time in all SCORM\'s</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by current learner in each SCORM</p></td></tr><tr><td><p>No. of views</p></td><td><p>Number of hits by current learner in each SCORM</p></td></tr></tbody></table>';
$string['report_quizzes_help'] = '<p><strong>Description:</strong> Quiz overview report show the complete summary of the quizzes this enables the user to have a glance on grading (average grade of attempted, max, pass) user completed the quiz and the average of the first attempted.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Quiz Fields</p></td><td><p>List of quiz fields like name, course, time open etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name of each quiz</p></td></tr><tr><td><p>Not Yet Started Learners</p></td><td><p>Total number of learners not yet started in each quiz</p></td></tr><tr><td><p>In progress Learners</p></td><td><p>Total number of learners in-progress in each quiz</p></td></tr><tr><td><p>Completed Learners</p></td><td><p>Total number of learners completed in each quiz</p></td></tr><tr><td><p>Attempted Learners</p></td><td><p>Total number of learners attempted in each quiz</p></td></tr><tr><td><p>Pass Grade</p></td><td><p>Pass grade of each quiz</p></td></tr><tr><td><p>Max Grade</p></td><td><p>Max grade of each quiz</p></td></tr><tr><td><p>Average no. Of Attempts</p></td><td><p>Average no of attempts in each quiz</p></td></tr><tr><td><p>No. of complete graded first Attempts</p></td><td><p>Total no. of learner who achieved grade in first Attempt In each quiz</p></td></tr><tr><td><p>Total no. of complete graded Attempts</p></td><td><p>Total no. of learner who achieved grades in each quiz</p></td></tr><tr><td><p>Average grade of first attempts</p></td><td><p>Average of who achieved grade in first Attempt In each quiz</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners in each Quiz</p></td></tr><tr><td><p>No. of views</p></td><td><p>Number of hits by all learners in each quiz</p></td></tr></tbody></table>';
$string['report_resources_help'] = '<p><strong>Description: </strong>The Resources Report shows the Activities under a course and the total learner, access Details and the total and average time spent on the Activity.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Resources</p></td><td><p>List of Resource fields like name, course etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each resource</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners for each resource</p></td></tr><tr><td><p>Number of Views</p></td><td><p>Number of hits by all learners for each resource</p></td></tr></tbody></table>';
$string['report_scorm_help'] = '<p><strong>Description:</strong>The SCORM Report show the detailed information about the SCORMS in a course and the total learners and there grading with the total Time Spent.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>SCORM</p></td><td><p>List of SCORM fields like name, version, SCORM type etc&hellip;</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each SCORM</p></td></tr><tr><td><p>Completed Learners</p></td><td><p>Total number of learners completed in each SCORM</p></td></tr><tr><td><p>No. of Attempts</p></td><td><p>Total number of attempts in each SCORM</p></td></tr><tr><td><p>Lowest Grade</p></td><td><p>Lowest grade achieved by learner in a SCORM</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade achieved by learner in a SCORM</p></td></tr><tr><td><p>Average Grade</p></td><td><p>Average grade of learners in each SCORM</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners in each SCORM</p></td></tr><tr><td><p>Number of Views</p></td><td><p>Number of hits by all learners for each SCORM</p></td></tr></tbody></table>';
$string['report_topic_wise_performance_help'] = '<p><strong>Description: </strong></p><p>This report shows the performance of the learners according to the sections columns involved in the reports are learner name, email of the learner and the sections.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc&hellip;</p></td></tr><tr><td><p>Total Grade in Section/Topic</p></td><td><p>Average of each grade of all activities in each topic for selected course</p></td></tr></tbody></table>';
$string['report_useractivities_help'] = '<p><strong>Description: </strong>Using this Report user can see a course Activities of a learners, their gradings, Activity Access details, completed activities and the total time spent.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Activity</p></td><td><p>List of activity fields like name, course etc&hellip;</p></td></tr><tr><td><p>Activity Type</p></td><td><p>Type of activity for each activity in selected course</p></td></tr><tr><td><p>Final Grade</p></td><td><p>Final grade achieved by selected learner in each activity</p></td></tr><tr><td><p>Lowest Grade</p></td><td><p>Lowest grade achieved by the learners in each activity</p></td></tr><tr><td><p>Highest Grade</p></td><td><p>Highest grade achieved by the learners in each activity</p></td></tr><tr><td><p>First Access</p></td><td><p>Time accessed by the selected learner for the first time for each activity</p></td></tr><tr><td><p>Last Access</p></td><td><p>Time accessed by the selected learner for the Last time for each activity</p></td></tr><tr><td><p>Completed On</p></td><td><p>Activity completion date of selected learner in each activity</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by selected learners for each course</p></td></tr><tr><td><p>Number of views</p></td><td><p>Number of hits by selected learner for each activity</p></td></tr></tbody></table>';
$string['report_userassignments_help']='<p><strong>Description: </strong>Using this report user can have an overview of learner and the assignments which are completed, attempted, submitted, total number of assignment and the time spent on these assignment by them. User can see the individual Assignment and quizzes summary choosing a learner.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like name, full name, email etc&hellip;</p></td></tr><tr><td><p>Completed Assignments</p></td><td><p>Total number of assignments completed by each learner</p></td></tr><tr><td><p>Submitted Assignments</p></td><td><p>Total number of assignments submitted by each learner</p></td></tr><tr><td><p>In-progress Assignments</p></td><td><p>Total number of assignments started but not submitted by each learner</p></td></tr><tr><td><p>Not yet Started Assignments</p></td><td><p>Total number of assignments not yet started by each learner</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learner for all Assignments</p></td></tr><tr><td><p>Number of Views</p></td><td><p>Number of hits by each learner for all assignments in a course</p></td></tr></tbody></table>';
$string['report_userbadges_help'] = '<p><strong>Description: </strong>This report helps the user to know about the badges achieved by the learners and the detailed overview of the badges. Using columns like course, issuer, created on, completion criteria for the course and the expiry date. </p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Badge</p></td><td><p>Badge name</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each badge</p></td></tr><tr><td><p>Issuer</p></td><td><p>Badge issuer name</p></td></tr><tr><td><p>Created On</p></td><td><p>Badge created date and time</p></td></tr><tr><td><p>Issued On</p></td><td><p>Badge issued date and time</p></td></tr><tr><td><p>Description</p></td><td><p>Badge description</p></td></tr><tr><td><p>Completion Criteria</p></td><td><p>Completion criteria for each badge</p></td></tr><tr><td><p>Expire date</p></td><td><p>Badge expiry date</p></td></tr></tbody></table>';
$string['report_usercourses_help'] = '<p><strong>Description: </strong>This report enables the user to have the complete details of the learners as list of learners, date of enrollment, total Activities and the completion information, badges and Time Spent etc.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like Full name, email  etc…</p></td></tr><tr><td><p>Enrolled On</p></td><td><p>Learner enrolled date and time for selected course</p></td></tr><tr><td><p>Completed Activities</p></td><td><p>Total number of completed activities for selected course</p></td></tr><tr><td><p>Completed Assignments</p></td><td><p>Total number of completed assignments for selected course</p></td></tr><tr><td><p>Completed Quizzes</p></td><td><p>Total number of completed quizzes for selected course</p></td></tr><tr><td><p>Completed SCORM’s</p></td><td><p>Total number of completed SCORMs for selected course</p></td></tr><tr><td><p>Grade</p></td><td><p>Grade achieved for selected course</p></td></tr><tr><td><p>Badges Issued</p></td><td><p>Number of badges achieved for selected course</p></td></tr><tr><td><p>Status</p></td><td><p>User completion status for selected course</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learners for each selected course</p></td></tr></tbody></table>';
$string['report_userquizzes_help'] = '<p><strong>Description: </strong>This report shows the list of learners and their quizzes overview whether they have started or completed or in progress and the total no of quizzes they have completed, and the total time spent on the quizzes.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like name, full name, email etc&hellip;</p></td></tr><tr><td><p>Completed</p></td><td><p>Total number of quizzes completed by learners</p></td></tr><tr><td><p>Finished</p></td><td><p>Total number of quizzes finished by learners</p></td></tr><tr><td><p>In-progress</p></td><td><p>Total number of quizzes in-progress</p></td></tr><tr><td><p>Not yet Started</p></td><td><p>Total number of quizzes which are not yet started</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learner for all quizzes</p></td></tr><tr><td><p>Number of Views</p></td><td><p>Number of hits by each learner for all quizzes in a course</p></td></tr></tbody></table>';
$string['report_usersresources_help'] = '<p><strong>Description: </strong>This report shows the list of learners and the resources they have enrolled user can check the list of learners no. of resources, time spent on total resources and no. of views.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like name, full name, email etc&hellip;</p></td></tr><tr><td><p>No. of Resources</p></td><td><p>Total number of resources for each learner</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learner on all resources</p></td></tr><tr><td><p>Number of views</p></td><td><p>Total number of hits by learner on all resources</p></td></tr></tbody></table>';
$string['report_usersscorm_help'] = '<p><strong>Description: </strong>Using this report user can have the overview of SCORMS by the list of learners and the SCORMs status using in progress, completed, failed SCORMs, last access and the average time spent on the SCORMs.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like name, full name, email etc&hellip;</p></td></tr><tr><td><p>Not Attempted SCORM&rsquo;s</p></td><td><p>Total number of not attempted SCORM\'s for each learner</p></td></tr><tr><td><p>In progress SCORM&rsquo;s,</p></td><td><p>Total number of in-progress SCORM\'s for each learner</p></td></tr><tr><td><p>Completed SCORM&rsquo;s</p></td><td><p>Total number of completed SCORM\'s for each learner</p></td></tr><tr><td><p>First Access</p></td><td><p>Time accessed by the selected learner for the first time in all SCORM\'s</p></td></tr><tr><td><p>Last Access</p></td><td><p>Time accessed by the selected learner for the last time in all SCORM\'s</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by learner for all SCORM\'s</p></td></tr><tr><td><p>No. of Views</p></td><td><p>Total number of hits by each learner in all SCORM\'s</p></td></tr></tbody></table>';
$string['report_competencycompletion_help'] = '<p><strong>Description: </strong>This report helps the user to know the competency Rating and Status of each Learner in each course.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Competency Name</p></td><td><p>Competency Name</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each Competency</p></td></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc…</p></td></tr><tr><td><p>Rating</p></td><td><p>Rating for Learner of each competency</p></td></tr><tr><td><p>Completion Date</p></td><td><p>Competency completion date of Learner</p></td></tr><tr><td><p>Status</p></td><td><p>Status of Learner for each Competency</p></td></tr></tbody></table>';
$string['report_courseprofile_help'] = '<p><strong>Description: </strong>This report provides the information like enrollments, activities, badges etc., of the each course . Using this report multiple courses information can be compared.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Fullname</p></td><td><p>Course Name</p></td></tr><tr><td><p>Student Enrolments</p></td><td><p>Total number of students enrolled in the course</p></td></tr><tr><td><p>Completed Students</p></td><td><p>Total number of students completed the course</p></td></tr><tr><td><p>Total Activities</p></td><td><p>Total number of activities in the course</p></td></tr><tr><td><p>Progress</p></td><td><p>Progress of learners in course</p></td></tr><tr><td><p>Average Grade</p></td><td><p>Average grade of all learners in each course</p></td></tr><tr><td><p>Highgrade</p></td><td><p>Highest grade of learner in each course</p></td></tr><tr><td><p>Lowgrade</p></td><td><p>Lowest grade of learner in each course</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Timespent by the learner in each course</p></td></tr><tr><td><p>No. Of Views</p></td><td><p>Total number of hits by learners in a course</p></td></tr><tr><td><p>Status</p></td><td><p>Status of each course</p></td></tr></tbody></table>';
$string['report_courseviews_help'] = '<p><strong>Description: </strong>This report helps the user to know the number of hits by learners in each course.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc…</p></td></tr><tr><td><p>Views</p></td><td><p>Total number of hits by learners in a course</p></td></tr></tbody></table>';
$string['report_noofviews_help'] = '<p><strong>Description: </strong>This report helps the user to know the number of hits by learners in each activity.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc…</p></td></tr><tr><td><p>Views</p></td><td><p>Total number of hits by learners in each activity</p></td></tr></tbody></table>';
$string['report_pageresourcetimespent_help'] = '<p><strong>Description: </strong>This report helps the user to know the Timespent by all the learners on a page in a course using the columns total timespent etc.,</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Page</p></td><td><p>Page name</p></td></tr><tr><td><p>Course</p></td><td><p>Course name of each page</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total timespent by the learners in each page</p></td></tr></tbody></table>';
$string['report_resources_accessed_help'] = '<p><strong>Description: </strong>This report shows the resource accessed overview of each learner using the columns like start date, end date, activity, action etc.,</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc…</p></td></tr><tr><td><p>Category</p></td><td><p>Category name for each resource</p></td></tr><tr><td><p>Course</p></td><td><p>Course name for each resource</p></td></tr><tr><td><p>Start date</p></td><td><p>Start date of Learner for each resource</p></td></tr><tr><td><p>End date</p></td><td><p>End date of Learner for each resource</p></td></tr><tr><td><p>Action</p></td><td><p>Action performed by the Learner</p></td></tr><tr><td><p>Activity</p></td><td><p>Activity name of resource</p></td></tr><tr><td><p>Type</p></td><td><p>Activity type of resource</p></td></tr><tr><td><p>Lastaccess</p></td><td><p>Time accessed by learner for the last time in each resource</p></td></tr></tbody></table>';
$string['report_timespent_help'] = '<p><strong>Description: </strong>This report shows the timespent by the user in LMS using the columns total timespent.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc…</p></td></tr><tr><td><p>Total Timespent</p></td><td><p>Total Timespent by the user in LMS</p></td></tr></tbody></table>';
$string['report_userprofile_help'] = '<p><strong>Description: </strong>This report provides the information like enrolled courses, grades, badges etc., of the user. Using this report multiple users information can be compared.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Learner</p></td><td><p>List of learner fields like full name, email etc…</p></td></tr><tr><td><p>Progress</p></td><td><p>Progress of each Learner</p></td></tr><tr><td><p>Enrolled</p></td><td><p>Total number of enrolled courses of each learner</p></td></tr><tr><td><p>Inprogress</p></td><td><p>Total number of inprogress courses of each learner</p></td></tr><tr><td><p>Completed</p></td><td><p>Total number of completed courses of each learner</p></td></tr><tr><td><p>Overall Completed Courses Grade</p></td><td><p>Overall Grade of each learner in completed courses</p></td></tr><tr><td><p>Quizzes</p></td><td><p>Total number of quizzes of each learner</p></td></tr><tr><td><p>Assignments</p></td><td><p>Total number of assignments of each learner</p></td></tr><tr><td><p>Badges</p></td><td><p>Total number of badges of each learner</p></td></tr><tr><td><p>Status</p></td><td><p>Status of each Learner</p></td></tr></tbody></table>';

$string['report_categories_help'] = '<p><strong>Description: </strong>This report provides the information like name, parent category, description etc., of the category.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Id</p></td><td><p>Category ID</p></td></tr><tr><td><p>Name</p></td><td><p>Category Name</p></td></tr><tr><td><p>ID Number</p></td><td><p>The ID Number of course category</p></td></tr><tr><td><p>Description</p></td><td><p>Category Description</p></td></tr><tr><td><p>Parent</p></td><td><p>Parent category name</p></td></tr><tr><td><p>Descriptionformat</p></td><td><p>Description format of a category</p></td></tr><tr><td><p>Sortorder</p></td><td><p>Sorting order of categories</p></td></tr><tr><td><p>Coursecount</p></td><td><p>Count of courses in a category</p></td></tr><tr><td><p>Timemodified</p></td><td><p>Modiifed time of a category</p></td></tr></tbody></table>';
$string['lsreportconfigimport'] = 'LearnerScript Config Status';
$string['graphdeleted'] = 'Graph Successfully Deleted';
$string['reportschedule'] = 'Report Scheduled Successfully';
$string['deleteschedulereport'] = 'Schedule Report Deleted Successfully';
$string['updateschedulereport'] = 'Schedule Report Updated Successfully';
$string['graphcreated'] = 'Graph Created Successfully';
$string['graphupdated'] = 'Graph Updated Successfully';
$string['mailscheduled'] = 'Mails Scheduled Successfully, will be delivered in 5mins.';
$string['messagesent'] = 'Message Sent Successfully to ';
$string['graphdelete'] = 'Graph Deleted Successfully';
$string['installplugins'] = 'Install Plugins.';
$string['notasssignedrole'] = 'You are not assigned to any role';
$string['columntype'] = 'Column Type:';
$string['clickhere'] = 'Click here';
$string['tocontinue'] = 'to continue.';
$string['addgraph'] = 'Add Graph';
$string['jumpto'] = 'Jump To';
$string['addusers'] = 'Add Users';
$string['lsdashboard'] = 'LearnerScript Dashboard';
$string['columntype'] = 'Column Type:';
// LearnerScript CLI
$string['ls_cli_version'] = 'LearnerScript Version : {$a}.';
$string['ls_cli_missing'] = 'Missing {$a} name.';
$string['ls_cli_create'] = '{$a} created successfully.';
$string['ls_cli_exists'] = '{$a} already exists.';
// LearnerScript privacy.
// $string['privacy:metadata'] = 'The LearnerScript block only provides reports data.';
$string['privacy:metadata:block_devicels'] = 'This stores the user access information.';
$string['privacy:metadata:block_devicels:userid'] = 'The ID of the user.';
$string['privacy:metadata:block_devicels:accessip'] = 'The IP address from which a user access the site..';
$string['privacy:metadata:block_devicels:country'] = 'The country from which a user browses the site.';
$string['privacy:metadata:block_devicels:countrycode'] = 'The Country Code of the accessing user of the site.';
$string['privacy:metadata:block_devicels:region'] = 'The region from which a user visits the site.';
$string['privacy:metadata:block_devicels:city'] = 'The city from which a user access the site.';
$string['privacy:metadata:block_devicels:browser'] = 'The browser that user uses to visit the site.';
$string['privacy:metadata:block_devicels:platform'] = 'The platform which a user uses to access.';
$string['privacy:metadata:block_devicels:browserversion'] = 'The browser version a user currently uses.';
$string['privacy:metadata:block_devicels:devicetype'] = 'The device from which a user access the site.';
$string['privacy:metadata:block_devicels:browserparent'] = 'The parent of user accessed browser';
$string['privacy:metadata:block_devicels:regionname'] = 'The regionname from which a user visits the site.';
$string['privacy:metadata:block_devicels:pointingmethod'] = 'Type of browser pointing method';
$string['privacy:metadata:block_devicels:ismobiledevice'] = 'Mobile device information';
$string['privacy:metadata:block_devicels:istablet'] = 'Tablet Information';
$string['privacy:metadata:block_devicels:timemodified'] = 'User accessed time';

$string['resetingls'] = 'Resetting LearnerScript';
$string['usertimepsent'] = 'Learner Script';
$string['contextid'] = 'Context level';
$string['report_forum'] = 'Forum';
$string['forumfield'] = 'Forum fields';
$string['forum'] = 'Forum columns';
$string['report_forum_help'] = '<p><strong>Description: </strong>This report provides the forum information like discussions count, posts/replies, words count.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Forum</p></td><td><p>Forum name</p></td></tr><tr><td><p>Course</p></td><td><p>Course Name</p></td></tr><tr><td><p>Discussions count</p></td><td><p>Total count of discussions in each forum.</p></td></tr><tr><td><p>Posts/Replies</p></td><td><p>Total number of posts and replies in each forum.</p></td></tr><tr><td><p>Words count</p></td><td><p>Total numbers of words in all posts and replies for each forum</p></td></tr></tbody></table>';
$string['report_assignstatus'] = ' Assignment Status';
$string['report_assignstatus_help'] = '<p><strong>Description: </strong>This Table Shows Status of Student Assignment</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Course</p></td><td><p>Total Courses of Student Registered ></td></tr><tr><td><p>Module</p></td><td><p>Total number of Assignments That Student Registered</p></td></tr><tr><td><p>Completed</p></td><td><p>Total number of Assignments completed by Student</p></td></tr><tr><td><p>Pending </p></td><td><p>This Coloumn Shows the Count of Pending Assignments</p></td></tr></tbody></table>';
$string['assignstatus'] = 'User Assignment coloumns';
$string['report_myforums_help'] = '<p><strong>Description: </strong>This report provides the forum information like discussions count, replies, words count.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Forum</p></td><td><p>Forum name</p></td></tr><tr><td><p>Course</p></td><td><p>Course Name</p></td></tr><tr><td><p>Discussions count</p></td><td><p>Total count of discussions in each forum.</p></td></tr><tr><td><p>Replies</p></td><td><p>Total number of replies in each forum.</p></td></tr><tr><td><p>Words count</p></td><td><p>Total numbers of words in all replies for each forum</p></td></tr></tbody></table>';
$string['closegraph'] = 'Close graph';
$string['lsreportconfigstatus'] = 'Learnerscript reports configuration status';
$string['report_attendanceoverview'] = 'Attendance overview';
$string['report_userattendance'] = 'User attendance';
$string['attendanceoverview'] = 'Attendance overview columns';
$string['userattendance'] = 'User attendance columns';
$string['report_userattendance_help'] = '<p><strong>Description: </strong>This report provides the students attendance count in the last month.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Date</p></td><td><p>Date</p></td></tr><tr><td><p>Learners count</p></td><td><p>Total no. learners attended the selected course.</p></td></tr></tbody></table>';
$string['report_attendanceoverview_help'] = '<p><strong>Description: </strong>This report provides the students and teachers attendance count in the last month.</p><table class = "help_icon_table" border="1"><tbody><tr><th><p><strong>Columns</strong></p></th><th><p><strong>Description</strong></p></th></tr><tr><td><p>Date</p></td><td><p>Date</p></td></tr><tr><td><p>Teachers count</p></td><td><p>Total no. teachers attended the selected course.</p></td></tr><tr><td><p>Learners count</p></td><td><p>Total no. learners attended the selected course.</p></td></tr></tbody></table>';
$string['report_coursecompetency'] = 'Course competency';
$string['coursecompetency'] = 'Course competency';
$string['report_coursecompetency_help'] = '<p><strong>Description: </strong>This report provides the course competency information.</p>';

$string['report_competency'] = 'Competency summary';
$string['competencycolumns'] = 'Competency columns';
$string['report_competency_help'] = '<p><strong>Description: </strong>This report provides the competency information.</p>';

$string['report_bigbluebutton'] = 'BigBlueButton summary';
$string['bigbluebuttonfields'] = 'BigBlueButton columns'; 
$string['report_bigbluebutton_help'] = '<p><strong>Description: </strong>This report provides the sessions information.</p>'; 

$string['session'] = 'Session';
$string['sessions'] = 'Sessions';
$string['filtersession'] = 'Select session';
$string['filter_session'] = 'Select session'; 

$string['filtercohort'] = 'Select cohort';
$string['filter_cohort'] = 'Select cohort';
$string['cohort'] = 'Cohort';
$string['report_cohortusers'] = 'Cohort users';
$string['cohortusercolumns'] = 'Cohort user columns';
$string['report_cohortusers_help'] = '<p><strong>Description: </strong>This report shows the performance of learners in each course</p>';

$string['report_activestudents'] = 'Active students summary';
$string['activestudents'] = 'Active students'; 
$string['report_activestudents_help'] = '<p><strong>Description: </strong>This report provides the information of learners joined the selected session.</p>'; 

$string['activestudentscolumns'] = 'Active students columns';

$string['report_monthlysessions'] = 'Monthly sessions';
$string['monthlysessions'] = 'Monthly sessions columns';
$string['report_monthlysessions_help'] = 'Monthly sessions overview';
$string['report_courseparticipation'] = 'Course Participation';
$string['courseparticipationcolumns'] = 'Course Participation Columns';
$string['report_assignmentparticipation'] = 'Assignment Participation';
$string['assignmentparticipationcolumns'] = 'Assignment Participation Columns';
$string['report_quizzparticipation'] = 'Quizz Participation';
$string['quizzparticipationcolumns'] = 'Quizz Participation Columns';
$string['report_scormparticipation'] = 'Scorm Participation';
$string['scormparticipationcolumns'] = 'Scorm Participation Columns';

$string['report_weeklysessions'] = 'Weekly sessions';
$string['weeklysessions'] = 'Weekly sessions columns';
$string['report_weeklysessions_help'] = 'Weekly sessions overview';
$string['report_dailysessions'] = 'Daily sessions';
$string['dailysessionscolumns'] = 'Daily sessions columns';
$string['report_dailysessions_help'] = 'Daily sessions';

$string['report_upcomingactivities'] = 'Upcoming Activities';
$string['upcomingactivities'] = 'Upcoming Activities columns';
$string['report_upcomingactivities_help'] = 'Upcoming Activities';

$string['schedulecourses'] = 'Course Completed Users';
$string['report_schedulecourses'] = 'Course Completed Users';
$string['report_schedulecourses_help'] = 'Course Completed Users';
$string['inprogressusers'] = 'Course Inprogress Users';
$string['report_inprogressusers'] = 'Incompleted Courses';
$string['report_inprogressusers_help'] = 'Incompleted Courses';
$string['inprogressuserscolumns'] = 'Incompleted Courses';

$string['report_pendingactivities'] = 'Pending Activities';
$string['pendingactivities'] = 'Pending Activities';
$string['report_pendingactivities_help'] = 'Pending Activities';
$string['report_needgrading'] = 'Need Grading';
$string['needgrading'] = 'Need Grading';
$string['report_needgrading_help'] = 'Need Grading';
$string['views'] = 'Views';
