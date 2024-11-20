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
 * Strings for tool_usertours.
 *
 * @package   tool_usertours
 * @copyright 2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['above'] = 'Above';
$string['actions'] = 'Actions';
$string['appliesto'] = 'Applies to';
$string['below'] = 'Below';
$string['block'] = 'Block';
$string['block_named'] = 'Block named \'{$a}\'';
$string['cachedef_stepdata'] = 'List of user tour steps';
$string['cachedef_tourdata'] = 'List of enabled user tours information which is fetched on every page';
$string['description'] = 'Description';
$string['description_help'] = 'The description of a tour may be added as plain text, enclosed in multilang tags (for use with the multi-language content filter) if required.

Alternatively, a language string ID may be entered in the format identifier,component (with no brackets or space after the comma).';
$string['displaystepnumbers'] = 'Display step numbers';
$string['displaystepnumbers_help'] = 'Whether to display a step number count e.g. 1/4, 2/4 etc. to indicate the length of the user tour.';
$string['showtourwhen'] = 'Show tour';
$string['showtoureachtime'] = 'each time a filter matches it';
$string['showtouruntilcomplete'] = 'until it has been closed';
$string['confirmstepremovalquestion'] = 'Are you sure that you wish to remove this step?';
$string['confirmstepremovaltitle'] = 'Confirm step removal';
$string['confirmtourremovalquestion'] = 'Are you sure that you wish to remove this tour?';
$string['confirmtourremovaltitle'] = 'Confirm tour removal';
$string['content'] = 'Content';
$string['content_heading'] = 'Content';
$string['content_help'] = 'Content describing the step may be added as plain text, enclosed in multilang tags (for use with the multi-language content filter) if required.';
$string['content_type'] = 'Content type';
$string['content_type_help'] = '* Manual - content is entered using a text editor
* Language string ID - in the format string identifier,component (with no space after the comma)';
$string['content_type_langstring'] = 'Language string ID';
$string['content_type_manual'] = 'Manual';
$string['cssselector'] = 'CSS selector';
$string['defaultvalue'] = 'Default ({$a})';
$string['delay'] = 'Delay before showing the step';
$string['done'] = 'Done';
$string['duplicatetour'] = 'Duplicate tour';
$string['duplicatetour_name'] = '{$a} (copy)';
$string['editstep'] = 'Editing "{$a}"';
$string['tourisenabled'] = 'Tour is enabled';
$string['enabled'] = 'Enabled';
$string['endtourlabel'] = 'End tour button\'s label';
$string['endtourlabel_help'] = 'You can optionally specify a custom label for the end tour button. The default label is \'Got it\' for single-step and \'End tour\' for multiple-step tours.

Alternatively, a language string ID may be entered in the format identifier,component (with no brackets or space after the comma).';
$string['event_tour_started'] = 'Tour started';
$string['event_tour_reset'] = 'Tour reset';
$string['event_tour_ended'] = 'Tour ended';
$string['event_step_shown'] = 'Step shown';
$string['exporttour'] = 'Export tour';
$string['filter_accessdate'] = 'Access date';
$string['filter_accessdate_enabled'] = 'Enable access date filter';
$string['filter_accessdate_enabled_help'] = 'Only show the tour to new users or users who have accessed the site recently.';
$string['filter_category'] = 'Category';
$string['filter_category_help'] = 'Show the tour on a page that is associated with a course in the selected category.';
$string['filter_course'] = 'Courses';
$string['filter_course_help'] = 'Show the tour on a page that is associated with the selected course.';
$string['filter_courseformat'] = 'Course format';
$string['filter_courseformat_help'] = 'Show the tour on a page that is associated with a course using the selected course format.';
$string['filter_cssselector'] = 'CSS selector';
$string['filter_cssselector_help'] = 'Only show the tour when the specified CSS selector is found on the page.';
$string['filter_header'] = 'Tour filters';
$string['filter_help'] = 'Select the conditions under which the tour will be shown. All of the filters must match for a tour to be shown to a user.';
$string['filter_date_account_creation'] = 'User account creation date within';
$string['filter_date_first_login'] = 'User\'s first access date within';
$string['filter_date_last_login'] = 'User\'s last access date within';
$string['filter_theme'] = 'Theme';
$string['filter_theme_help'] = 'Show the tour when the user is using one of the selected themes.';
$string['filter_role'] = 'Role';
$string['filter_role_help'] = 'A tour may be restricted to users with selected roles in the context where the tour is shown. For example, restricting a Dashboard tour to users with the role of student won\'t work if users have the role of student in a course (as is generally the case). A Dashboard tour can only be restricted to users with a system role.';
$string['importtour'] = 'Import tour';
$string['invalid_lang_id'] = 'Invalid language string ID';
$string['left'] = 'Left';
$string['modifyshippedtourwarning'] = 'This is a user tour that has shipped with Moodle. Any modifications you make may be overridden during your next site upgrade.';
$string['moodle_language_identifier'] = 'Language string ID';
$string['movestepdown'] = 'Move step down';
$string['movestepup'] = 'Move step up';
$string['movetourdown'] = 'Move tour down';
$string['movetourup'] = 'Move tour up';
$string['name'] = 'Name';
$string['name_help'] = 'The name of a tour may be added as plain text, enclosed in multilang tags (for use with the multi-language content filter) if required.

Alternatively, a language string ID may be entered in the format identifier,component (with no brackets or space after the comma).';
$string['newstep'] = 'Create step';
$string['newstep'] = 'New step';
$string['newtour'] = 'Create a new tour';
$string['next'] = 'Next';
$string['nextstep'] = 'Next';
$string['nextstep_sequence'] = 'Next ({$a->position}/{$a->total})';
$string['options_heading'] = 'Options';
$string['pathmatch'] = 'Apply to URL match';
$string['pathmatch_help'] = 'Tours will be displayed on any page whose URL matches this value.

You can use the % character as a wildcard to mean anything.
Some example values include:

* /my/% - to match the Dashboard
* /course/view.php?id=2 - to match a specific course
* /mod/forum/view.php% - to match the forum discussion list
* /user/profile.php% - to match the user profile page

If you wish to display a tour on the site home, you can use the value: "FRONTPAGE".';
$string['placement'] = 'Placement';
$string['pluginname'] = 'User tours';
$string['resettouronpage'] = 'Reset user tour on this page';
$string['right'] = 'Right';
$string['select_block'] = 'Select a block';
$string['targettype_help'] = 'Each step is associated with a part of the page - the target. Target types are:

* Block - for displaying a step next to a specified block
* CSS selector - for accurately defining the target area using CSS
* Display in middle of page - for a step which does not need to be associated with a specific part of the page';
$string['selector_defaulttitle'] = 'Enter a descriptive title';
$string['selectordisplayname'] = 'A CSS selector matching \'{$a}\'';
$string['skip'] = 'Skip';
$string['skip_tour'] = 'Skip tour';
$string['target'] = 'Target';
$string['target_heading'] = 'Step target';
$string['target_block'] = 'Block';
$string['target_selector'] = 'Selector';
$string['target_unattached'] = 'Display in middle of page';
$string['targettype'] = 'Target type';
$string['title'] = 'Title';
$string['title_help'] = 'The title of a step may be added as plain text, enclosed in multilang tags (for use with the multi-language content filter) if required.

Alternatively, a language string ID may be entered in the format identifier,component (with no brackets or space after the comma).';
$string['tourconfig'] = 'Tour configuration file to import';
$string['tourlist_explanation'] = 'You can create as many tours as you like and enable them for different parts of Moodle. Only one tour can be created per page.';
$string['tours'] = 'Tours';
$string['pausetour'] = 'Pause';
$string['resumetour'] = 'Resume';
$string['endonesteptour'] = 'Got it';
$string['endtour'] = 'End tour';
$string['orphan'] = 'Show if target not found';
$string['orphan_help'] = 'Show the step if the target could not be found on the page.';
$string['backdrop'] = 'Show with backdrop';
$string['backdrop_help'] = 'You can use a backdrop to highlight the part of the page that you are pointing to.

Note: Backdrops are not compatible with some parts of the page such as the navigation bar.
';
$string['reflex'] = 'Proceed on click';
$string['reflex_help'] = 'Proceed to the next step when the target is clicked on.';
$string['placement_help'] = 'A step may be placed above, below, left or right of the target. Above or below is recommended, as these adjust better for mobile display.

If the step does not fit on a particular page in the specified place, it will be automatically placed elsewhere.';
$string['delay_help'] = 'You can optionally choose to add a delay before the step is displayed.

This delay is in milliseconds.';
$string['selecttype'] = 'Select step type';
$string['sharedtourslink'] = 'Tour repository';
$string['usertours'] = 'User tours';
$string['usertours:managetours'] = 'Create, edit, and remove user tours';
$string['target_selector_targetvalue'] = 'CSS selectors';
$string['target_selector_targetvalue_help'] = 'A CSS selector can be used to target almost any element on the page. The appropriate selector can be easily found using the developer tools for your web browser.';
$string['viewtour_info'] = 'This is the \'{$a->tourname}\' tour. It applies to the path \'{$a->path}\'.';
$string['viewtour_edit'] = 'You can <a href="{$a->editlink}">edit the tour defaults</a> and <a href="{$a->resetlink}">force the tour to be displayed</a> to all users again.';
$string['tour_resetforall'] = 'The state of the tour has been reset. It will be displayed to all users again.';

// Boost - administrator tour.
$string['tour1_title_welcome'] = 'Welcome';
$string['tour1_content_welcome'] = 'Welcome to the Boost theme. If you\'ve upgraded from an earlier version, you might find some things look a bit different with this theme.';
$string['tour1_title_navigation'] = 'Navigation';
$string['tour1_content_navigation'] = 'Major navigation is now through this nav drawer. The contents update depending on where you are in the site. Use the button at the top to hide or show it.';
$string['tour1_title_customisation'] = 'Customisation';
$string['tour1_content_customisation'] = 'To customise the look of your site and the site home, use the settings menu in the corner of this header. Try turning editing on right now.';
$string['tour1_title_blockregion'] = 'Block region';
$string['tour1_content_blockregion'] = 'There is still a block region over here. We recommend removing the Navigation and Administration blocks completely, as all the functionality is elsewhere in the Boost theme.';
$string['tour1_title_addingblocks'] = 'Adding blocks';
$string['tour1_content_addingblocks'] = 'In fact, think carefully about including any blocks on your pages. Blocks are not shown in the Moodle app, so as a general rule it\'s much better to make sure your site works well without any blocks.';
$string['tour1_title_end'] = 'End of tour';
$string['tour1_content_end'] = 'This is the end of your user tour. It won\'t show again unless you reset it using the link in the footer. As an admin you can also create your own tours like this!';

// Boost - course view tour.
$string['tour2_title_welcome'] = 'Welcome';
$string['tour2_content_welcome'] = 'Welcome to the Boost theme. If your site has been upgraded from an earlier version, you might find things look a bit different here on the course page.';
$string['tour2_title_customisation'] = 'Customisation';
$string['tour2_content_customisation'] = 'To change any course settings, use the settings menu in the corner of this header. You will find a similar settings menu on the home page of every activity, too. Try turning editing on right now.';
$string['tour2_title_navigation'] = 'Navigation';
$string['tour2_content_navigation'] = 'Navigation is now through this nav drawer. Use the button at the top to hide or show it. You will see that there are links for sections of your course.';
$string['tour2_title_opendrawer'] = 'Open the nav drawer';
$string['tour2_content_opendrawer'] = 'Try opening the nav drawer now.';
$string['tour2_title_participants'] = 'Course participants';
$string['tour2_content_participants'] = 'View participants here. This is also where you go to add or remove students.';
$string['tour2_title_addblock'] = 'Add a block';
$string['tour2_content_addblock'] = 'If you turn editing on you can add blocks from the nav drawer. However, think carefully about including any blocks on your pages. Blocks are not shown in the Moodle app, so for the best user experience it is better to make sure your course works well without any blocks.';
$string['tour2_title_addingblocks'] = 'Adding blocks';
$string['tour2_content_addingblocks'] = 'You can add blocks to this page using this button. However, think carefully about including any blocks on your pages. Blocks are not shown in the Moodle app, so for the best user experience it is better to make sure your course works well without any blocks.';
$string['tour2_title_end'] = 'End of tour';
$string['tour2_content_end'] = 'This is the end of your user tour. It won\'t show again unless you reset it using the link in the footer. The site admin can also create further tours for this site if required.';
$string['privacy:metadata:preference:requested'] = 'The time that a user last manually requested a user tour.';
$string['privacy:metadata:preference:completed'] = 'The time that a user last completed a user tour.';
$string['privacy:request:preference:requested'] = 'You last requested the "{$a->name}" user tour on {$a->time}';
$string['privacy:request:preference:completed'] = 'You last marked the "{$a->name}" user tour as completed on {$a->time}';

// 3.6 Dashboard tour.
$string['tour3_title_dashboard'] = 'Your Dashboard';
$string['tour3_content_dashboard'] = 'Your new Dashboard has many features to help you easily access the information most important to you.';
$string['tour3_title_timeline'] = 'Timeline block';
$string['tour3_content_timeline'] = 'The Timeline block shows your important upcoming events.

You can choose to show activities in the next week, month, or further into the future.

You can also show items which are overdue.';
$string['tour3_title_recentcourses'] = 'Recently accessed courses';
$string['tour3_content_recentcourses'] = 'The Recently accessed courses block shows the courses that you last visited, allowing you to jump straight back in.';
$string['tour3_title_overview'] = 'Course overview';
$string['tour3_content_overview'] = 'The Course overview block shows all of the courses that you are enrolled in.

You can choose to show courses currently in progress, or in the past or the future, or courses which you have starred.';
$string['tour3_title_starring'] = 'Starring and hiding courses';
$string['tour3_content_starring'] = 'You can choose to star a course to make it stand out, or hide a course which is no longer important to you.

These actions only affect your view.

You can also choose to display the courses in a list, or with summary information, or the default \'card\' view.';
$string['tour3_title_displayoptions'] = 'Display options';
$string['tour3_content_displayoptions'] = 'Courses may be sorted by course name, course short name or last access date.

You can also choose to display the courses in a list, with summary information, or the default \'card\' view.';

// 3.6 Messaging tour.
$string['tour4_title_messaging'] = 'New messaging interface';
$string['tour4_content_messaging'] = 'New messaging features include group messaging within a course and better control over who can message you.';
$string['tour4_title_icon'] = 'Messaging';
$string['tour4_content_icon'] = 'You can access your messages from any page using this icon.

If you have any unread messages, the number of unread messages will show here too.

Click on the icon to open the messaging drawer and continue the tour.';
$string['tour4_title_groupconvo'] = 'Group messages';
$string['tour4_content_groupconvo'] = 'If you are a member of a group with group messaging enabled, you\'ll see group conversations here.

Course group conversations allow you to interact with the others in your group in a private and convenient location.';
$string['tour4_title_starred'] = 'Starred';
$string['tour4_content_starred'] = 'You can choose to star particular conversations to make them easier to find.';
$string['tour4_title_settings'] = 'Messaging settings';
$string['tour4_content_settings'] = 'You can access your messaging settings via the cog icon. A new privacy setting allows you to restrict who can message you.';

// 3.11 Activity information tour.
$string['tour_activityinfo_activity_student_title'] = 'New: Activity information';
$string['tour_activityinfo_activity_student_content'] = 'Activity dates plus what to do to complete the activity are shown on the activity page.';
$string['tour_activityinfo_activity_teacher_title'] = 'New: Activity information';
$string['tour_activityinfo_activity_teacher_content'] = 'Activity dates and completion conditions are now displayed for students on each activity page (and optionally on the course page).

For activities requiring students to manually mark an activity as completed, a \'Mark as done\' button is shown on the activity page.';
$string['tour_activityinfo_course_student_title'] = 'New: Activity information';
$string['tour_activityinfo_course_student_content'] = 'Activity dates and/or what to do to complete the activity are displayed on the course page.';
$string['tour_activityinfo_course_teacher_title'] = 'New: Activity information';
$string['tour_activityinfo_course_teacher_content'] = 'New course settings \'Show completion conditions\' and \'Show activity dates\' enable you to choose whether activity completion conditions (if set) and/or dates are displayed for students on the course page.';

// 4.0 New navigation tour.
$string['tour_navigation_course_announcements_teacher_content'] = '@@PIXICON::tour/tour_course_admin_3::tool_usertours@@<br>Post important news here.';
$string['tour_navigation_course_announcements_teacher_title'] = 'Something to tell everyone?';
$string['tour_navigation_course_edit_teacher_content'] = '@@PIXICON::tour/tour_course_admin_1::tool_usertours@@<br>Add new content or edit existing content.';
$string['tour_navigation_course_edit_teacher_title'] = 'Activate edit mode';
$string['tour_navigation_course_index_student_content'] = '@@PIXICON::tour/tour_course_student::tool_usertours@@<br>Browse through activities and track your progress.';
$string['tour_navigation_course_index_student_title'] = 'Find your way around';
$string['tour_navigation_course_index_teacher_content'] = '@@PIXICON::tour/tour_course_admin_2::tool_usertours@@<br>Drag and drop activities to re-order course content.';
$string['tour_navigation_course_index_teacher_title'] = 'Course index';
$string['tour_navigation_course_student_tour_des'] = 'Where to browse through activities in a course';
$string['tour_navigation_course_student_tour_name'] = 'Course index';
$string['tour_navigation_course_teacher_tour_des'] = 'Edit mode, drag and drop of activities and posting announcements in a course';
$string['tour_navigation_course_teacher_tour_name'] = 'Course editing';
$string['tour_navigation_dashboard_content'] = '@@PIXICON::tour/tour_dashboard::tool_usertours@@<br>This side panel can contain more features.';
$string['tour_navigation_dashboard_title'] = 'Expand to explore';
$string['tour_navigation_dashboard_tour_des'] = 'Where blocks can be found';
$string['tour_navigation_dashboard_tour_name'] = 'Block drawer';
$string['tour_navigation_mycourses_content'] = '@@PIXICON::tour/tour_mycourses::tool_usertours@@<br>Add, copy, delete and hide courses from this menu.';
$string['tour_navigation_mycourses_endtourlabel'] = 'I understand';
$string['tour_navigation_mycourses_title'] = 'Courses and categories';
$string['tour_navigation_mycourses_tour_des'] = 'Course management options on the My courses page';
$string['tour_navigation_mycourses_tour_name'] = 'Course management';

// 4.2 New gradebook tour.
$string['tour_gradebook_action_content'] = '<div class="text-center">@@PIXICON::tour/tour_grader_report_action_menu::tool_usertours@@</div>Sort columns and choose which ones to display. In Edit mode, use this shortcut to access frequent tasks related to viewing and editing grade items.';
$string['tour_gradebook_action_title'] = 'Quick links to actions';
$string['tour_gradebook_filter_content'] = '<div class="text-center">@@PIXICON::tour/tour_grader_report_initials::tool_usertours@@</div>Filter students by the initials of their first or last name.';
$string['tour_gradebook_filter_title'] = 'Filter by name';
$string['tour_gradebook_search_content'] = '<div class="text-center">@@PIXICON::tour/tour_grader_report_search::tool_usertours@@</div>Use the search box to quickly find specific students.';
$string['tour_gradebook_search_title'] = 'Find students easily';
$string['tour_gradebook_tour_description'] = 'Search and navigation features in Gradebook grader report';
$string['tour_gradebook_tour_name'] = 'Gradebook Grader Report';
$string['tour_final_step_title'] = 'End of tour';
$string['tour_final_step_content'] = 'This is the end of your user tour. It won\'t show again unless you reset it using the link in the footer.';
