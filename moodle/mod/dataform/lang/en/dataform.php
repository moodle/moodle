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
 * @package mod
 * $subpackage dataform
 * @copyright 2014 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * The Dataform has been developed as an enhanced counterpart
 * of Moodle's Database activity module (1.9.11+ (20110323)).
 * To the extent that Dataform code corresponds to Database code,
 * certain copyrights on the Database module may obtain.
 */

$string['modulename'] = 'Dataform';
$string['modulename_help'] = 'The dataform module may be used for creating a wide range of activities/resources by allowing the instructor/manager to design and create a custom content form from various input elements (e.g.  texts, numbers, images, files, urls, etc.), and participants to submit content and view submitted content.';
$string['modulenameplural'] = 'Dataforms';

// GENERAL.
$string['dataformnone'] = 'No Dataforms found';
$string['dataformnotready'] = 'This activity is not ready for viewing';
$string['dataformearly'] = 'This activity is scheduled to start {$a}';
$string['dataformpastdue'] = 'This activity was due {$a}';

// ACTIVITY SETTINGS.
$string['activityadministration'] = 'Activity administration';
$string['dataformnew'] = 'New Dataform';
// Appreance.
$string['activityicon'] = 'Activity icon';
$string['activityicon_help'] = 'You can upload an image file to replace the Dataform default activity icon displayed on the course page next to the activity link.';
$string['inlineview'] = 'Inline view';
$string['inlineview_help'] = 'You can select one of this Dataform\'s views to displayed on the course page instead of the activity link. If this is a new instance you need save it and create at least one view before selecting an inline view.';
$string['embedded'] = 'Embedded';
$string['embedded_help'] = 'A selected inline view can be embedded on the course page in an iframe to allow interacting with the view without leaving the course page.';
$string['noautocompletioninline'] = 'Automatic completion on viewing of activity can not be selected together with "Inline view" option';
// Timing.
$string['timing'] = 'Timing and Intervals';
$string['timeavailable'] = 'Available from';
$string['timeavailable_help'] = 'The designated start time of the activity. This time appears in the course calendar. The \'earlyentry\' set of capabilities allows for controling entry permissions before this time.';
$string['timedue'] = 'Due';
$string['timedue_help'] = 'The designated end time of the activity. This time appears in the course calendar.  The \'lateentry\' set of capabilities allows for controling entry permissions after this time.';
$string['timeinterval'] = 'Duration';
$string['timeinterval_help'] = 'The duration of the activity from its Available-from time.';
$string['timeinterval_err'] = 'Activity duration is required for multiple intervals.';
$string['intervalcount'] = 'Number of intervals';
$string['intervalcount_help'] = 'If the activity is set to have more than 1 interval then the availability settings and the entry settings apply to each interval separately.';

// Completion.
$string['completionentries'] = 'Participants must add entries:';
$string['completionentriesgroup'] = 'Require entries';
$string['completionentrieshelp'] = 'requiring entries to complete';
$string['completionspecificgrade'] = 'Participants must receive the grade:';
$string['completionspecificgradegroup'] = 'Require specific grade';
$string['completionspecificgradehelp'] = 'requiring specific grade to complete';

// Entries.
$string['entriesmax'] = 'Maximum entries';
$string['entriesmax_help'] = 'The max number of entries a user without manageentries capability can add to the activity.
<ul>
<li><b>-1:</b> Unlimited number of entries allowed.
<li><b> 0:</b> No entries allowed.
<li><b> N:</b> N entries allowed (where N is any positive number, e.g. 10).
</ul>
If the activity has intervals this number applies to each interval, and the max number of entries for the whole activity is this number times the number of intervals.';
$string['entriesrequired'] = 'Required entries';
$string['entriesrequired_help'] = 'The number of entries a user without manageentries capability is required to add for the activity to be considered complete (before consideration of other completion criteria such as grade). If the activity has intervals this number applies to each interval, and the number of required entries for the whole activity is this number times the number of intervals.';
$string['groupentries'] = 'Group entries';
$string['groupentries_help'] = 'Entries will be added with group info but without author info. This settings requires group mode.';
$string['anonymousentries'] = 'Allow anonymous entries';
$string['anonymousentries_help'] = 'If enabled guests and not-logged-in users will be able to add entries in this activity.
 This may be useful for applications such as \'contact us\' where visitors to the site can submit a contact request. The option must be enabled by admin in the module settings.';
$string['entrytimelimit'] = 'Editing time limit (minutes)';
$string['entrytimelimit_help'] = 'The time limit (minutes) within which a user without manage entries capability can update or delete a new entry.
<ul>
<li><b>-1:</b> Unlimited.
<li><b>&nbsp;0:</b> The entry cannot be updated or deleted after submission.
<li><b>&nbsp;N:</b> The entry can be updated or deleted within N minutes (where N is any positive number, e.g. 30).
</ul>';
$string['notapplicable'] = 'N/A';

// Grading.
$string['gradeguide'] = 'Grade guide/rubric';
$string['gradeguide_help'] = 'Choose a grading guide or rubric that should be used for assigning grades in this activity/grade item.';
$string['gradecalc'] = 'Grade calculation';
$string['gradecalc_help'] = 'A grade calculation is a formula that determines the activity grade. The formula may use common mathematical operators, such as max, min and sum. It can also use certain field patterns to determine the activity grade based on user content.';
$string['gradeitems'] = 'Grade items';
$string['gradeitemsin'] = 'Grade items in {$a}';
$string['gradeitems_help'] = 'This page lets you add/edit grade items for this activity.';
$string['usegradeitemsform'] = 'This instance contains multiple grade items. To edit these items, please use the <a href="{$a}">grade items form</a>.';

// ROLES.
$string['entriesmanager'] = 'Entries Manager';

$string['dfupdatefailed'] = 'Failed to update dataform!';

$string['fieldtemplate'] = 'Template';
$string['fieldtemplate_help'] = 'The field template allows for specifying a designated field label that can be added to the view by means of the [[fieldname@]] field pattern. This field pattern observes the field visibility and is hidden if the field is set to be hidden. The field template can also serve as a field display template and it interprets patterns of that field if included in the label. For example, with a number field called Number and the field label defined as \'You have earned [[Number]] credits.\' and an entry where the number value is 47 the pattern [[Number@]] would be displayed as \'You have earned 47 credits.\'';
$string['actions'] = 'Entry actions';
$string['alignment'] = 'Alignment';
$string['ascending'] = 'Ascending';
$string['authorinfo'] = 'Author info';
$string['browse'] = 'Browse';
$string['columns'] = 'columns';
$string['commentadd'] = 'Add comment';
$string['commentbynameondate'] = 'by {$a->name} - {$a->date}';
$string['comment'] = 'Comment';
$string['commentdelete'] = 'Are you sure you want to delete this comment?';
$string['commentdeleted'] = 'Comment deleted';
$string['commentedit'] = 'Edit comment';
$string['commentempty'] = 'Comment was empty';
$string['commentinputtype'] = 'Comment input type';
$string['commentsallow'] = 'Allow comments?';
$string['commentsaved'] = 'Comment saved';
$string['comments'] = 'Comments';
$string['commentsn'] = '{$a} comments';
$string['commentsnone'] = 'No comments';
$string['configanonymousentries'] = 'This switch will enable the possibility of guest/anonymous entries for all dataforms. You will still need to turn anonymous on manually in the settings for each dataform.';
$string['configenablerssfeeds'] = 'This switch will enable the possibility of RSS feeds for all dataforms. You will still need to add rss views in the Dataform instance to generate a feed.';
$string['configmaxentries'] = 'This value determines the maximum number of entries that may be added to a dataform activity.';
$string['configmaxfields'] = 'This value determines the maximum number of fields that may be added to a dataform activity.';
$string['configmaxfilters'] = 'This value determines the maximum number of filters that may be added to a dataform activity.';
$string['configmaxviews'] = 'This value determines the maximum number of views that may be added to a dataform activity.';
$string['correct'] = 'Correct';
$string['csscode'] = 'CSS code';
$string['cssinclude'] = 'CSS';
$string['cssincludes'] = 'Include external CSS';
$string['csssaved'] = 'CSS saved';
$string['cssupload'] = 'Upload CSS files';

// GRADE.
$string['multigradeitems'] = 'Allow multiple grade items';
$string['configmultigradeitems'] = 'Set to Yes to allow multiple grade items in a Dataform activity.';

// CSV.
$string['csvdelimiter'] = 'delimiter';
$string['csvenclosure'] = 'enclosure';
$string['csvfailed'] = 'Unable to read the raw data from the CSV file';
$string['csvoutput'] = 'CSV output';
$string['csvsettings'] = 'CSV settings';
$string['csvwithselecteddelimiter'] = '<acronym title=\"Comma Separated Values\">CSV</acronym> text with selected delimiter:';

// RESET.
$string['deletenotenrolled'] = 'Delete entries by users not enrolled';

$string['descending'] = 'Descending';

$string['documenttype'] = 'Document type';
$string['dots'] = '...';
$string['download'] = 'Download';
$string['editordisable'] = 'Disable editor';
$string['editorenable'] = 'Enable editor';
$string['embed'] = 'Embed';
$string['enabled'] = 'Enabled';
$string['entriesadded'] = '{$a} entry(s) added';
$string['entriesconfirmadd'] = 'You are about to duplicate {$a} entry(s). Would you like to proceed?';
$string['entriesconfirmduplicate'] = 'You are about to duplicate {$a} entry(s). Would you like to proceed?';
$string['entriesconfirmdelete'] = 'You are about to delete {$a} entry(s). Would you like to proceed?';
$string['entriesconfirmupdate'] = 'You are about to update {$a} entry(s). Would you like to proceed?';
$string['entriescount'] = '{$a} entry(s)';
$string['entriesdeleteall'] = 'Delete all entries';
$string['entriesdeleted'] = '{$a} entry(s) deleted';
$string['entriesduplicated'] = '{$a} entry(s) duplicated';
$string['entries'] = 'Entries';
$string['entriesfound'] = '{$a} entry(s) found';
$string['entriesimport'] = 'Import entries';
$string['entrieslefttoaddtoview'] = 'You must add {$a} more entry/entries before you can view other participants\' entries.';
$string['entrieslefttoadd'] = 'You must add {$a} more entry/entries in order to complete this activity';
$string['entriesnotsaved'] = 'No entry was saved. Please check the format of the uploaded file.';
$string['entriespending'] = 'Pending';
$string['entriesupdated'] = '{$a} entry(s) updated';
$string['entriessaved'] = '{$a} entry(s) saved';
$string['entryaddmultinew'] = 'Add new entries';
$string['entryaddnew'] = 'Add a new entry';
$string['entry'] = 'Entry';
$string['entryinfo'] = 'Entry info';
$string['entrynew'] = 'New entry';
$string['entrynoneforaction'] = 'No entries were found for the requested action';
$string['entrynoneindataform'] = 'No entries in dataform';
$string['entryrating'] = 'Entry rating';
$string['entrysaved'] = 'Your entry has been saved';
$string['entrysettings'] = 'Entry settings';
$string['entrysettingsupdated'] = 'Entry settings updated';
$string['exportcontent'] = 'Export content';
$string['export'] = 'Export';

$string['firstdayofweek'] = 'Monday';
$string['first'] = 'First';
$string['formemptyadd'] = 'You did not fill out any fields!';
$string['fromfile'] = 'Import from zip file';
$string['generalactions'] = 'General actions';
$string['getstarted'] = 'This dataform appears to be new or with incomplete setup.';
$string['getstartedpresets'] = 'Apply a preset in the {$a} section';
$string['getstartedfields'] = 'Add fields in the {$a} section';
$string['getstartedviews'] = 'Add views in the {$a} section';

$string['headercss'] = 'Custom CSS styles for all views';
$string['headerjs'] = 'Custom javascript for all views';
$string['horizontal'] = 'Horizontal';
$string['importadd'] = 'Add a new Import view';
$string['import'] = 'Import';
$string['importnoneindataform'] = 'There are no imports defined for this dataform.';
$string['incorrect'] = 'Incorrect';
$string['index'] = 'Index';
$string['insufficiententries'] = 'more entries needed to view this dataform';
$string['internal'] = 'Internal';
$string['intro'] = 'Introduction';
$string['invalidname'] = 'Please choose another name for this {$a}';
$string['invalidrate'] = 'Invalid dataform rate ({$a})';
$string['invalidurl'] = 'The URL you just entered is not valid';
$string['jscode'] = 'Javascript code';
$string['jsinclude'] = 'JS';
$string['jsincludes'] = 'Include external javascript';
$string['jssaved'] = 'Javascript saved';
$string['jsupload'] = 'Upload javascript files';
$string['lock'] = 'Lock';
$string['manage'] = 'Manage';
$string['mappingwarning'] = 'All old fields not mapped to a new field will be lost and all data in that field will be removed.';
$string['max'] = 'Maximum';
$string['maxsize'] = 'Maximum size';
$string['mediafile'] = 'Media file';
$string['reference'] = 'Reference';

// DATAFORM..
$string['min'] = 'Minimum';
$string['more'] = 'More';
$string['moreurl'] = 'More URL';
$string['movezipfailed'] = 'Can\'t move zip';
$string['multidelete'] = ' Delete  ';
$string['multidownload'] = 'Download';
$string['multiduplicate'] = 'Duplicate';
$string['multiedit'] = '  Edit   ';
$string['multiexport'] = 'Export';
$string['multishare'] = 'Share';
$string['newvalueallow'] = 'Allow new values';
$string['newvalue'] = 'New value';
$string['noaccess'] = 'You do not have access to this page';
$string['nomatch'] = 'No matching entries found!';
$string['nomaximum'] = 'No maximum';
$string['notopenyet'] = 'Sorry, this activity is not available until {$a}';

// EVENT.
$string['event'] = 'Event';
$string['events'] = 'Events';

$string['event_view_created'] = 'View created';
$string['event_view_updated'] = 'View updated';
$string['event_view_deleted'] = 'View deleted';
$string['event_view_viewed'] = 'View accessed';

$string['event_field_created'] = 'Field created';
$string['event_field_updated'] = 'Field updated';
$string['event_field_deleted'] = 'Field deleted';
$string['event_field_content_updated'] = 'Field content updated';

$string['event_filter_created'] = 'Filter created';
$string['event_filter_updated'] = 'Filter updated';
$string['event_filter_deleted'] = 'Filter deleted';

$string['event_entry_created'] = 'Entry created';
$string['event_entry_updated'] = 'Entry updated';
$string['event_entry_deleted'] = 'Entry deleted';

// CAPABILITY.
// Deprecated.
$string['dataform:viewaccesshidden'] = '** Deprecated **';
$string['dataform:viewanonymousentry'] = '** Deprecated **';
$string['dataform:viewentry'] = '** Deprecated **';
$string['dataform:writeentry'] = '** Deprecated **';
$string['dataform:exportallentries'] = '** Deprecated **';
$string['dataform:exportentry'] = '** Deprecated **';
$string['dataform:exportownentry'] = '** Deprecated **';
$string['dataform:manageratings'] = '** Deprecated **';
$string['dataform:rate'] = '** Deprecated **';
$string['dataform:ratingsviewall'] = '** Deprecated **';
$string['dataform:ratingsviewany'] = '** Deprecated **';
$string['dataform:ratingsview'] = '** Deprecated **';
$string['dataform:comment'] = '** Deprecated **';
$string['dataform:managecomments'] = '** Deprecated **';
$string['dataform:approve'] = '** Deprecated **';

// Dataform.
$string['dataform:addinstance'] = 'Add a new dataform';
$string['dataform:indexview'] = 'View index';
$string['dataform:messagingview'] = 'View messaging';
$string['dataform:managetemplates'] = 'Manage templates';
$string['dataform:manageviews'] = 'Manage views';
$string['dataform:managefields'] = 'Manage fields';
$string['dataform:managefilters'] = 'Manage filters';
$string['dataform:manageaccess'] = 'Manage access rules';
$string['dataform:managenotifications'] = 'Manage notification rules';
$string['dataform:managecss'] = 'Manage css';
$string['dataform:managejs'] = 'Manage js';
$string['dataform:managetools'] = 'Manage tools';

// Presets.
$string['dataform:managepresets'] = 'Manage presets';
$string['dataform:presetsviewall'] = 'View presets from all users';
// View.
$string['dataform:viewaccess'] = 'View - Access';
$string['dataform:viewaccessdisabled'] = 'View - Access disabled';
$string['dataform:viewaccessearly'] = 'View - Access early';
$string['dataform:viewaccesslate'] = 'View - Access late';
$string['dataform:viewfilteroverride'] = 'View - Filter override';
// Entries.
$string['dataform:manageentries'] = 'Manage entries';

$string['dataform:entryearlyview'] = 'Early Entry - View';
$string['dataform:entryearlyadd'] = 'Early Entry - Add';
$string['dataform:entryearlyupdate'] = 'Early Entry - Update';
$string['dataform:entryearlydelete'] = 'Early Entry - Delete';

$string['dataform:entrylateview'] = 'Late Entry - View';
$string['dataform:entrylateadd'] = 'Late Entry - Add';
$string['dataform:entrylateupdate'] = 'Late Entry - Update';
$string['dataform:entrylatedelete'] = 'Late Entry - Delete';

$string['dataform:entryownview'] = 'Own Entry - View';
$string['dataform:entryownexport'] = 'Own Entry - Export';
$string['dataform:entryownadd'] = 'Own Entry - Add';
$string['dataform:entryownupdate'] = 'Own Entry - Update';
$string['dataform:entryowndelete'] = 'Own Entry - Delete';

$string['dataform:entrygroupview'] = 'Group Entry - View';
$string['dataform:entrygroupexport'] = 'Group Entry - Export';
$string['dataform:entrygroupadd'] = 'Group Entry - Add';
$string['dataform:entrygroupupdate'] = 'Group Entry - Update';
$string['dataform:entrygroupdelete'] = 'Group Entry - Delete';
$string['dataform:entryanyview'] = 'Any Entry - View';
$string['dataform:entryanyexport'] = 'Any Entry - Export';
$string['dataform:entryanyadd'] = 'Any Entry - Add';
$string['dataform:entryanyupdate'] = 'Any Entry - Update';
$string['dataform:entryanydelete'] = 'Any Entry - Delete';
$string['dataform:entryanonymousview'] = 'Anonymous Entry - View';
$string['dataform:entryanonymousexport'] = 'Anonymous Entry - Export';
$string['dataform:entryanonymousadd'] = 'Anonymous Entry - Add';
$string['dataform:entryanonymousupdate'] = 'Anonymous Entry - Update';
$string['dataform:entryanonymousdelete'] = 'Anonymous Entry - Delete';

// MESSAGE.
$string['messageprovider:dataform_notification'] = 'Dataform notifications';
$string['subject'] = 'Subject';
$string['message'] = 'Message';
$string['contentview'] = 'Content from view';
$string['notification'] = 'Notification';
$string['conversation'] = 'Conversation';
$string['noreply'] = 'No-reply';
$string['sender'] = 'Sender';
$string['recipient'] = 'Recipient';

// VIEW.
$string['viewadd'] = 'Add a view';
$string['viewcreate'] = 'Create a new view';
$string['viewcurrent'] = 'Current view';
$string['viewcustomdays'] = 'Custom refresh interval: days';
$string['viewcustomhours'] = 'Custom refresh interval: hours';
$string['viewcustomminutes'] = 'Custom refresh interval: minutes';
$string['viewdescription'] = 'View description';
$string['viewdescription_help'] = 'Short description of the view purpose and characteristics to allow managers to see at a glance what each view is intended for. This description is displayed only in the view management list.';
$string['viewedit'] = 'Editing \'{$a}\'';
$string['vieweditthis'] = 'Edit this view';
$string['viewfilter'] = 'Filter';
$string['viewfilter_help'] = 'A predefined filter from the Filters list (if any) that will be enforced in the view. ';
$string['viewforedit'] = 'View for \'edit\'';
$string['viewformore'] = 'View for \'more\'';
$string['viewfromdate'] = 'Viewable from';
$string['viewintervalsettings'] = 'Interval settings';
$string['viewinterval'] = 'When to refresh view content';
$string['entrytemplate'] = 'Entry template';
$string['entrytemplate_help'] = 'The entry template allows you to determine content, behaviour and general layout of an entry both for browsing and for editing. This template typically contains field elements for displaying and updating the entry content. When creating a new view, the template is populated automatically with a default layout that consists of base field patterns, edit action and delete action. You can then add or remove patterns as needed. The WYSIWYG editor also allows you to decorate the template and create your own look and feel with color, font and images.';
$string['viewname'] = 'View name';
$string['viewname_help'] = 'Short name for the view. The view name can be used in certain view patterns in which case the format of the name should as simple as possible, only alpha or alphanumeric characters, to avoid pattern parsing problems.';
$string['viewnew'] = 'New {$a} view';
$string['viewnodefault'] = 'Default view is not set. Choose one of the views in the {$a} list as the default view.';
$string['viewnoneforaction'] = 'No views were found for the requested action';
$string['viewnoneindataform'] = 'There are no views defined for this dataform.';
$string['viewoptions'] = 'View options';
$string['viewpagingfield'] = 'Paging field';
$string['viewperpage'] = 'Per page';
$string['viewperpage_help'] = 'The max number of entries to display in the view at any given time. If the number of viewable entries is higher than the selected per-page a paging bar will be displayed (provided the ##paging:bar## is added to the view template).';
$string['viewresettodefault'] = 'Reset to default';
$string['viewreturntolist'] = 'Return to list';
$string['viewsadded'] = '{$a} view(s) added';
$string['viewsconfirmdelete'] = 'You are about to delete {$a} view(s). Would you like to proceed?';
$string['viewsconfirmduplicate'] = 'You are about to duplicate {$a} view(s). Would you like to proceed?';
$string['viewsdeleted'] = '{$a} view(s) deleted';
$string['viewtemplate'] = 'View template';
$string['viewtemplate_help'] = 'The view template allows you to determine content, behaviour and general layout of a front view page in the Dataform activity. This template typically contains elements for activity navigation, search and sort, and entries display. When creating a new view, the template is populated automatically with a default layout and you can then add or remove elements as needed. The WYSIWYG editor also allows you to decorate the template and create your own look and feel with color, font and images.';
$string['viewtiming'] = 'View timing';
$string['viewtiming_help'] = 'Set From and/or To dates to restrict access to the view only between the specified dates to any one without accessanytime capability. Tick the \'Set as default\' checkbox to set the view as the default view when it becomes available.';
$string['viewgeneral'] = 'View general settings';
$string['viewgeneral_help'] = 'View general settings';
$string['viewsectionpos'] = 'Section position';
$string['viewslidepaging'] = 'Slide paging';
$string['viewsmax'] = 'Maximum views';
$string['viewsupdated'] = '{$a} view(s) updated';
$string['views'] = 'Views';
$string['view'] = 'view';
$string['viewvisibility'] = 'Visibility';
$string['viewvisibility_help'] = 'A Disabled view is accessible only with viewaccessdisabled capability (typically teachers and managers).
A Visible view can be accessed by participants and it appears in navigation.
A Hidden view can be accessed by participants but does not appear in navigation.';
$string['viewdisabled'] = 'Disabled';
$string['viewvisible'] = 'Visible';
$string['viewhidden'] = 'Hidden';

$string['wrongdataid'] = 'Wrong dataform id provided';
$string['submission'] = 'Submission';

// VIEW SETTING.
$string['editing'] = 'Editing';
$string['modeeditonly'] = 'Only edited entries';
$string['modeeditseparate'] = 'Edited entries separated from other entries';
$string['modeeditinline'] = 'Edited entries inline with other entries';
$string['submissiondisplay'] = 'Display when editing';
$string['availablefrom'] = 'Available from';
$string['availableto'] = 'Available to';
$string['savebutton'] = 'Save';
$string['savebutton_label'] = 'Save button';
$string['savebutton_help'] = 'Saves the edited entry and returns to the view.';
$string['savecontbutton'] = 'Save and Continue';
$string['savecontbutton_label'] = 'Save Continue button';
$string['savecontbutton_help'] = 'Saves the edited entry and remains in the form to continue editing the entry.';
$string['savecontnewbutton'] = 'Save and Start New';
$string['savecontnewbutton_label'] = 'Save Continue New button';
$string['savecontnewbutton_help'] = 'Saves the edited entry and opens a new entry form.';
$string['savenewbutton'] = 'Save as New';
$string['savenewbutton_label'] = 'Save New button';
$string['savenewbutton_help'] = 'Saves the edited entry as a new entry (the original entry is not changed) and returns to the view.';
$string['savenewcontbutton'] = 'Save as New and Continue';
$string['savenewcontbutton_label'] = 'Save New Continue button';
$string['savenewcontbutton_help'] = 'Saves the edited entry as a new entry (the original entry is not changed) and remains in the form to continue editing the new entry.';
$string['cancelbutton'] = 'Cancel';
$string['cancelbutton_label'] = 'Cancel button';
$string['cancelbutton_help'] = 'Cancels submission and returns to the view.';
$string['submissionredirect'] = 'Redirect to another view';
$string['submissionredirect_help'] = 'By default the user remains in the same view after entry submission. If an alternate view is selected, the user will be redirect to that view after submission.';
$string['submissiontimeout'] = 'Response timeout';
$string['submissiontimeout_help'] = 'The delay (in seconds) after submission and before the user is redirected to the target view.';
$string['submissionmessage'] = 'Response to submission';
$string['submissionmessage_help'] = 'A message to display to user after successful submission and before the user is redirected to teh target view.';
$string['submissiondefaultmessage'] = 'Thank you';
$string['submitfailure'] = 'Your submission could not be saved. Please try again.';
$string['submissiondisplayafter'] = 'Display only edited entries';
$string['submissiondisplayafter_help'] = 'By default all the viewable entries will be displayed after submission. Set to \'Yes\' if you want to display only the edited entry(s).';

$string['patternsreplacement'] = 'Patterns Replacement';

// FIELDS.
$string['fieldadd'] = 'Add a field';
$string['fieldallowautolink'] = 'Allow autolink';
$string['fieldattributes'] = 'Field attributes';
$string['fieldcreate'] = 'Create a new field';
$string['fielddescription'] = 'Field description';
$string['fieldeditable'] = 'Editable';
$string['fieldedit'] = 'Editing \'{$a}\'';
$string['field'] = 'field';
$string['fieldids'] = 'Field ids';
$string['fieldmappings'] = 'Field Mappings';
$string['fieldname'] = 'Field name';
$string['fieldnew'] = 'New {$a} field';
$string['fieldnoneforaction'] = 'No fields were found for the requested action';
$string['fieldnoneindataform'] = 'There are no fields defined for this dataform.';
$string['fieldnonematching'] = 'No matching fields found';
$string['fieldnotmatched'] = 'The following fields in your file are not known in this dataform: {$a}';
$string['fieldrequired'] = 'You must supply a value here.';
$string['fieldrules'] = 'Field edit rules';
$string['fieldsadded'] = 'Fields added';
$string['fieldsconfirmdelete'] = 'You are about to delete {$a} field(s). Would you like to proceed?';
$string['fieldsconfirmduplicate'] = 'You are about to duplicate {$a} field(s). Would you like to proceed?';
$string['fieldsdeleted'] = 'Fields deleted. You may need to update the default sort settings.';
$string['fields'] = 'Fields';
$string['fieldsinternal'] = 'Internal fields';
$string['fieldsmax'] = 'Maximum fields';
$string['fieldsnonedefined'] = 'No fields defined';
$string['fieldsupdated'] = 'Fields updated';
$string['fieldvisibility'] = 'Visibile to';
$string['fieldvisibleall'] = 'Everyone';
$string['fieldvisiblenone'] = 'Managers only';
$string['fieldvisibleowner'] = 'Owner and managers';
$string['fieldwidth'] = 'Width';
$string['err_lowername'] = 'The name cannot contain uppercase letters.';
$string['fielddefaultcontent'] = 'Default content';
$string['fielddefaultvalue'] = 'Default value';
$string['fieldapplydefault'] = 'Apply default when editing';
$string['fielddefaultnew'] = 'New entries only';
$string['fielddefaultany'] = 'Any entry';

$string['filesettings'] = 'File settings';
$string['filemaxsize'] = 'Total size of uploded files';
$string['filesmax'] = 'Max number of uploaded files';
$string['filetypeany'] = 'Any file type';
$string['filetypeaudio'] = 'Audio files';
$string['filetypegif'] = 'gif files';
$string['filetypehtml'] = 'Html files';
$string['filetypeimage'] = 'Image files';
$string['filetypejpg'] = 'jpg files';
$string['filetypepng'] = 'png files';
$string['filetypes'] = 'Accepted file types';

// FILTER.
$string['filtersortfieldlabel'] = 'Sort field ';
$string['filtersearchfieldlabel'] = 'Search field ';
$string['filteradvanced'] = 'Advanced filter';
$string['filteradd'] = 'Add a filter';
$string['filterbypage'] = 'By page';
$string['filtercancel'] = 'Cancel filter';
$string['filtercreate'] = 'Create a new filter';
$string['filtercurrent'] = 'Current filter';
$string['filtercustomsearch'] = 'Search options';
$string['filtercustomsort'] = 'Sort options';
$string['filterdescription'] = 'Filter description';
$string['filteredit'] = 'Editing \'{$a}\'';
$string['filter'] = 'Filter';
$string['filtergroupby'] = 'Group by';
$string['filterincomplete'] = 'Search condition must be completed.';
$string['filtername'] = 'Dataform auto-linking';
$string['filternew'] = 'New filter';
$string['filternoneforaction'] = 'No filters were found for the requested action ({$a})';
$string['filterperpage'] = 'Per page';
$string['filtersadded'] = '{$a} filter(s) added';
$string['filtersave'] = 'Save filter';
$string['filtersconfirmdelete'] = 'You are about to delete {$a} filter(s). Would you like to proceed?';
$string['filtersconfirmduplicate'] = 'You are about to duplicate {$a} filter(s). Would you like to proceed?';
$string['filtersdeleted'] = '{$a} filter(s) deleted';
$string['filtersduplicated'] = '{$a} filter(s) duplicated';
$string['filterselection'] = 'Selection';
$string['filters'] = 'Filters';
$string['filtersimplesearch'] = 'Simple search';
$string['filtersmax'] = 'Maximum filters';
$string['filtersnonedefined'] = 'No filters defined';
$string['filtersnoneindataform'] = 'There are no filters defined for this dataform.';
$string['filtersupdated'] = '{$a} filter(s) updated';
$string['filterupdate'] = 'Update an existing filter';
$string['filterurlquery'] = 'Url query';
$string['filtersaved'] = 'My saved filters';
$string['filtersavedreset'] = '* Reset saved filters';
$string['filterquick'] = 'Quick filter';
$string['filterquickreset'] = '* Reset quick filter';

// FILTER OPERATORS.
$string['empty'] = 'Empty';
$string['equal'] = 'Equal';
$string['greaterthan'] = 'Greater than';
$string['lessthan'] = 'Less than';
$string['greaterorequal'] = 'Greater or equal';
$string['lessorequal'] = 'Less or equal';
$string['between'] = 'Between';
$string['contains'] = 'Contains';
$string['in'] = 'In';
$string['andor'] = 'and/or';
$string['and'] = 'AND';
$string['or'] = 'OR';
$string['is'] = 'IS';
$string['not'] = 'NOT';

// RULE.
$string['ruleadd'] = 'Add a rule';
$string['rulenew'] = 'New {$a} rule';
$string['rule'] = 'rule';
$string['rules'] = 'Rules';
$string['ruleenabled'] = 'Enabled';
$string['rulesnone'] = 'No rules';
$string['scope'] = 'Scope';
$string['acccesstypesnotfound'] = 'Sorry, no access types are installed or enabled. Please contact your administrator for details.';
$string['notificationtypesnotfound'] = 'Sorry, no notification types are installed or enabled. Please contact your administrator for details.';

// ACCESS.
$string['access'] = 'Access';
$string['accessadd'] = 'Add access context';
$string['accessedit'] = 'Editing \'{$a}\'';
$string['accessnew'] = 'New {$a}';
$string['accessenabled'] = 'Enabled';
$string['errornoitemsselected'] = 'At least one item should be selected';
$string['errorinvalidtimeto'] = 'Time-to must be later than Time-from';

// TOOL.
$string['tools'] = 'Tools';
$string['toolnoneindataform'] = 'There are no tools defined for this dataform.';
$string['toolrun'] = 'Run';

$string['patterns'] = 'Patterns';
$string['patternsnonebroken'] = 'No broken patterns found';
$string['patternvalid'] = 'Patterns valid';
$string['patternbroken'] = 'Broken patterns found';
$string['patternsuspect'] = 'Suspect patterns found';
$string['patterncleanup'] = 'Clean up';
$string['characterpatterns'] = 'Character patterns';
$string['characterpatterns_help'] = 'Character patternss';
$string['fieldpatterns'] = 'Entry patterns';
$string['fieldpatterns_help'] = 'Patterns whose content is entry sensitive.';
$string['viewpatterns'] = 'View patterns';
$string['viewpatterns_help'] = 'Patterns whose content is typically entry insensitive.';

// PRESET.
$string['presetadd'] = 'Add presets';
$string['presetapply'] = 'Apply';
$string['presetavailableincourse'] = 'Course presets';
$string['presetavailableinsite'] = 'Site presets';
$string['presetchoose'] = 'choose a predfined preset';
$string['presetdataanon'] = 'with user data anonymized';
$string['presetdata'] = 'with user data';
$string['presetfaileddelete'] = 'Error deleting a preset!';
$string['presetfromdataform'] = 'Make a preset of this dataform';
$string['presetfromfile'] = 'Upload preset from file';
$string['presetimportsuccess'] = 'The preset has been successfully applied.';
$string['presetinfo'] = 'Saving as a preset will publish this view. Other users may be able to use it in their dataforms.';
$string['presetmap'] = 'map fields';
$string['presetnodata'] = 'without user data';
$string['presetnodefinedfields'] = 'New preset has no defined fields!';
$string['presetnodefinedviews'] = 'New preset has no defined views!';
$string['presetnoneavailable'] = 'No available presets to display';
$string['presetplugin'] = 'Plug in';
$string['presetrefreshlist'] = 'Refresh list';
$string['presetshare'] = 'Share';
$string['presetsharesuccess'] = 'Saved successfully. Your preset will now be available across the site.';
$string['presetsource'] = 'Preset source';
$string['presets'] = 'Presets';
$string['presetusestandard'] = 'Use a preset';

$string['page-mod-dataform-x'] = 'Any Dataform activity module page';
$string['page-mod-dataform-view-index'] = 'Dataform activity views index page';
$string['page-mod-dataform-field-index'] = 'Dataform activity fields index page';
$string['page-mod-dataform-access-index'] = 'Dataform activity access rules index page';
$string['page-mod-dataform-notification-index'] = 'Dataform activity notification rules index page';
$string['pagesize'] = 'Entries per page';
$string['pagingbar'] = 'Paging bar';
$string['pluginadministration'] = 'Dataform activity administration';
$string['pluginname'] = 'Dataform';
$string['random'] = 'Random';
$string['range'] = 'Range';
$string['reference'] = 'Reference';
$string['renewactivity'] = 'Renew activity';
$string['renewconfirm'] = 'You are about to completely reset this activity. All the activity structure and user data will be deleted. Would you like to proceed?';
$string['deleteactivity'] = 'Delete activity';
$string['requiredall'] = 'all required';
$string['requirednotall'] = 'not all required';
$string['resetsettings'] = 'Reset filters';
$string['returntoimport'] = 'Return to import';

$string['author'] = 'Author';
$string['email'] = 'Email';

$string['save'] = 'Save';
$string['savenew'] = 'Save new';
$string['savenewcont'] = 'Save new and continue';
$string['savecont'] = 'Save and continue';
$string['savecontnew'] = 'Save and start new';
$string['cancel'] = 'Cancel';

$string['search'] = 'Search';
$string['sendinratings'] = 'Send in my latest ratings';
$string['separateentries'] = 'Each entry in a separate file';
$string['separateparticipants'] = 'Separate participants';
$string['separateparticipants_help'] = 'Separate participants';
$string['settings'] = 'Settings';
$string['spreadsheettype'] = 'Spreadsheet type';
$string['submissionsinpopup'] = 'Submissions in popup';
$string['submission'] = 'Submission';
$string['submissionsview'] = 'Submissions view';
$string['subplugintype_dataformfield'] = 'Dataform field type';
$string['subplugintype_dataformfield_plural'] = 'Dataform field types';
$string['subplugintype_dataformtool'] = 'Dataform tool type';
$string['subplugintype_dataformtool_plural'] = 'Dataform tool types';
$string['subplugintype_dataformview'] = 'Dataform view type';
$string['subplugintype_dataformview_plural'] = 'Dataform view types';

$string['type'] = 'Type';
$string['unlock'] = 'Unlock';
$string['userpref'] = 'User preferences';

$string['modulesettings'] = 'Module settings';
$string['fieldplugins'] = 'Field plugins';
$string['viewplugins'] = 'View plugins';
$string['managefields'] = 'Manage field plugins';
$string['manageviews'] = 'Manage view plugins';
$string['availableplugins'] = 'Available plugins';
$string['instances'] = 'Instances';
$string['pluginhasinstances'] = 'ATTENTION: This plugin has {$a} instances.';
$string['configplugins'] = 'Please enable all required plugins and arrange them in appropriate order.';

// ERRORS.
$string['error:cannotbenegative'] = 'Value cannot be negative';
