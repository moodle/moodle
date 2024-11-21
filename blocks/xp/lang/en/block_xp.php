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
 * Language file.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @codingStandardsIgnoreFile
 */

$string['actions'] = 'Actions';
$string['activityname'] = 'Activity name';
$string['activityname_help'] = 'The text that the activity name must contain or be equal to. This is not case sensitive.';
$string['activityoresourceis'] = 'The activity or resource is {$a}';
$string['addacondition'] = 'Add a condition';
$string['addarule'] = 'Add a rule';
$string['addonnotactivated'] = 'The addon is not activated.';
$string['adminnoticeoutofsyncmessage'] = 'Level Up XP and Level Up XP+ incompatibility notice!

You are receiving this notice as a warning because the two plugins Level Up XP (block_xp) and Level Up XP+ (local_xp) are currently "out of sync" and incompatible with each other. This issue occurs when XP has been upgraded to a new major version while XP+ remains outdated.

This mismatch can lead to loss of functionality, bugs, and other unexpected consequences. To resolve this, you must upgrade Level Up XP+.

**Important!** In the future, if these plugins are out of sync, Level Up XP+ will automatically disable itself. To prevent this from happening, ensure you do not upgrade Level Up XP to a new major version without also upgrading Level Up XP+.

- Level Up XP (block_xp) version: {$a->blockxpversion}
- Level Up XP+ (local_xp) version: {$a->localxpversion}
- Level Up XP+ expected version: {$a->localxpversionexpected}

Additional resources:

- [Upgrading documentation](https://docs.levelup.plus/xp/docs/upgrade)
- [Compatibility documentation](https://docs.levelup.plus/xp/docs/requirements-compatibility)

--

This notice was sent to all administrators. To disable all admin notices, please visit Level Up XP\'s admin settings.';
$string['adminnoticeoutofsyncsubject'] = "XP plugins incompatibility notice!";
$string['adminnotices'] = 'Admin notices';
$string['adminnotices_desc'] = 'When enabled, site administrators may occasionally receive important notifications regarding compatibility, security, and the availability of newer versions of Level Up XP+.';
$string['adminscanearnxp'] = 'Admins can earn points';
$string['adminscanearnxp_desc'] = 'By default, administrators are not included in the group of users who can earn points. This is because administrators always have the permission _block/xp:earnxp_, allowing them to continuously collect points everywhere. You can use this setting to let administrators also earn points.';
$string['admindefaultrulesintro'] = 'The following rules will be used as default for courses in which the block is added.';
$string['admindefaultsettingsintro'] = 'The settings below will be used as defaults when the block is newly added to a course. Some settings can be locked,  in which case their value is strictly enforced in all instances of the plugin.';
$string['admindefaultvisualsintro'] = 'The following will be used as defaults when the block is newly added to a course.';
$string['additionalresources'] = 'Additional resources';
$string['addlevel'] = 'Add level';
$string['addoninstallationerror'] = 'We are detecting an issue with the add-on (local_xp), it does not seem to be installed properly. An administrator should finalise its installation.';
$string['allcoursesreset'] = 'All courses have been reset.';
$string['anonymity'] = 'Anonymity';
$string['anonymity_help'] = 'This setting controls whether participants can see each other\'s name and avatar.';
$string['apply'] = 'Apply';
$string['awardaxpwhen'] = '<strong>{$a}</strong> points are earned when:';
$string['badgeaward'] = 'Badge to award';
$string['badgeawarddesc'] = 'A badge to award when the user attains the level.';
$string['basepoints'] = 'Base points';
$string['basepointslineardesc'] = 'The minimum increment between each level.';
$string['basepointsrelativedesc'] = 'The number of points to start with.';
$string['blockappearance'] = 'Block appearance';
$string['blockappearancemovedtopluginsettings'] = 'The block appearance settings were moved to the plugin\'s settings page.';
$string['cachedef_filters'] = 'Level filters';
$string['cachedef_ruleevent_eventslist'] = 'List of some events';
$string['cannotbesetindefaults'] = 'This cannot be set in the default values.';
$string['cannotearnpoints'] = 'Cannot earn points.';
$string['cannotshowblockconfig'] = 'I would usually display the appearance settings here, but I could not find your block. To change the block\'s appearance, head back [here]({$a}) (or where you added the block), turn editing mode on, and follow the "Configure" option in the block\'s dropdown. If you cannot find the block, add it to your course again.';
$string['cannotshowblockconfigsys'] = 'I would usually display the appearance settings here, but I could not find your block. It may be missing from the [front page]({$a->fp}) and the [default dashboard]({$a->mysys}) of your users, or present in both. To edit the settings from here, make sure it only appears in one of them.';
$string['changecourse'] = 'Change course';
$string['changetocourse'] = 'Change to course';
$string['changetositewide'] = 'Back to sitewide';
$string['cheatguard'] = 'Cheat guard';
$string['cheatguardsettingsmovednotice'] = 'The cheat guard settings were moved to the [event rules page]({$a->url}).';
$string['chooseacondition'] = 'Choose a condition';
$string['clearfilter'] = 'Clear filter';
$string['clicktoselectcm'] = 'Click to select an activity or resource';
$string['cmselector'] = 'Course module selector';
$string['coefxp'] = 'Algorithm coefficient';
$string['colon'] = '{$a->a}: {$a->b}';
$string['compatibilitycheck'] = 'Compatibility check';
$string['completionrules'] = 'Completion rules';
$string['completionrules_help'] = '
The completion rules are separated in three categories: activity completion, section completion and course completion. Adding conditions to the categories will determine when and how many points are awarded.

The rules are evaluated in the order in which they are presented on screen. As soon as a condition is met, the corresponding points will be awarded and further rules will not be evaluated.

[Learn more](https://docs.levelup.plus/xp/docs/completion-rules?ref=blockxp_help)
';
$string['completionrulesintro'] = 'Attribute points to students as they complete activities, sections or courses.';
$string['completionruleslegacyusednotice'] = 'You have existing "Event rules" using completion conditions. We strongly recommend removing them in favour of the methods below as using both "Event rules" and "Completion rules" could double the points awarded.';
$string['condition'] = 'Condition';
$string['configdescription'] = 'Introduction';
$string['configdescription_help'] = 'A short introduction message displayed in the block. Students have the ability to dismiss the message, in which case they won\'t see it again.';
$string['configheader'] = 'Settings';
$string['configtitle'] = 'Title';
$string['configtitle_help'] = 'The title of the block.';
$string['configblockrankingsnapshot'] = 'Display leaderboard snapshot';
$string['configblockrankingsnapshot_help'] = 'The leaderboard snapshot displays the rank of the user. It will also attempt to display the two people surrounding the user. This feature requires the leaderboard to be enabled, and its ranks to be displayed.';
$string['configrecentactivity'] = 'Display recent rewards';
$string['configrecentactivity_help'] = 'When enabled, the block will display a short list of recent events which rewarded the student with points.';
$string['congratulationsyouleveledup'] = 'Congratulations!';
$string['coolthanks'] = 'Cool, thanks!';
$string['coursea'] = 'Course "{$a}"';
$string['courselog'] = 'Log';
$string['courselogintro'] = 'The log displays the actions observed, and how many points they awarded.';
$string['coursereport'] = 'Report';
$string['coursereportintro'] = 'The report provides details on each participant, and supports acting on them individually or as a whole.';
$string['courseselectedcolon'] = 'Course selected:';
$string['coursesettings'] = 'Course settings';
$string['currencysign'] = 'Points symbol';
$string['currencysign_help'] = 'With this setting you can change the meaning of the points. It will be displayed next to the amount of points each user has as a substitute for the reference to _experience points_.

Choose one of the provided symbols, or upload your own!';
$string['currencysignxp'] = 'XP (Experience points)';
$string['customizelevels'] = 'Customise the levels';
$string['dangerzone'] = 'Danger zone';
$string['dataformat'] = 'Format';
$string['defaultlevels'] = 'Default levels';
$string['defaultrules'] = 'Default rules';
$string['defaultsettings'] = 'Default settings';
$string['defaultvisuals'] = 'Default appearance';
$string['deletecondition'] = 'Delete condition';
$string['deleterule'] = 'Delete rule';
$string['description'] = 'Description';
$string['difference'] = 'Diff.';
$string['difficulty'] = 'Point calculation method';
$string['difficultyflat'] = 'Equal';
$string['difficultyflatdesc'] = 'All levels require the same number of points to attain.';
$string['difficultylinear'] = 'Increasing';
$string['difficultylineardesc'] = 'Levels progressively take longer to attain.';
$string['difficultylinearincrdesc'] = 'The number of points used for the progressive difficulty.';
$string['difficultypointincrease'] = 'Point increase';
$string['difficultyrelative'] = 'Snowballing';
$string['difficultyrelativedesc'] = 'Levels become exponentially more difficult to attain.';
$string['difficultyrelativeincrdesc'] = 'The percentage of points to increase from the previous level.';
$string['discoverlevelupplus'] = 'Discover Level Up XP+';
$string['dismissnotice'] = 'Dismiss notice';
$string['displayeveryone'] = 'Display everyone';
$string['displaynneighbours'] = 'Display {$a} neighbours';
$string['displayoneneigbour'] = 'Display one neighbour';
$string['displayparticipantsidentity'] = 'Display participants identity';
$string['displayrank'] = 'Display rank';
$string['displayrelativerank'] = 'Display a relative rank';
$string['documentation'] = 'Documentation';
$string['drops'] = 'Drops';
$string['dropsintro'] = 'Drops are code snippets directly placed in content that award points when encountered by a user.';
$string['drops_help'] = '
In video games, some characters can _drop_ items or experience points on the ground for the player to pick up. These items and points are commonly referred to as drops.

In Level Up XP, drops are shortcodes (e.g. `[xpdrop id=1 secret=abcdef]`) that an instructor can place in regular Moodle content. When encountered by a user, these drops will be _picked up_ and a certain amount of points will be awarded.

At present, drops are invisible to the user and passively award points the first time they are encountered.

Drops can be used to cleverly award points when certain type of content is consumed by a student. Here are some ideas:

- Place a drop in the feedback of a quiz only visible for perfect scores
- Place a drop in deep content to reward their consumption
- Place a drop in an interesting forum discussion
- Place a drop in a hard-to-get-to page in a lesson module

[More info](https://docs.levelup.plus/xp/docs/how-to/use-drops?ref=blockxp_help)
';
$string['editcondition'] = 'Edit condition';
$string['editingdefaultsettingsinwholesitemodenotice'] = '**Attention!** You are not modifying the currently active settings, instead you are editing the default values. As Level Up XP is used sitewide, your intention is most likely to change the sitewide settings. [Navigate here]({$a->url}) to change those settings, or follow the "Settings" link from the XP block itself.';
$string['enablecheatguard'] = 'Enable cheat guard';
$string['enablecheatguard_help'] = 'The cheat guard offers a simple inexpensive mechanism for preventing students to abuse the system using obvious techniques, such as refreshing the same page endlessly, or repeating the same action over and over again.

[More info](https://docs.levelup.plus/xp/docs/getting-started/cheat-guard?ref=blockxp_help)
';
$string['enableinfos'] = 'Enable info page';
$string['enableinfos_help'] = 'When set to \'No\', students will not be able to view the information page.';
$string['enableladder'] = 'Enable the leaderboard';
$string['enableladder_help'] = 'When set to \'No\', students will not be able to view the leaderboard.';
$string['enablelevelupnotif'] = 'Enable level up notification';
$string['enablelevelupnotif_help'] = 'When set to \'Yes\', students will be displayed a popup congratulating them for the new level reached.';
$string['enablexpgain'] = 'Enable points gain';
$string['enablexpgain_help'] = 'When set to \'No\', nobody will earn points in the course. This is useful to freeze the points gained, or to enable it at a certain point in time.

Please note that this can also be controlled more granularity using the capability _block/xp:earnxp_.';
$string['entersearchterm'] = 'Enter a search term';
$string['errorcontextcoursemismatchforwholesite'] = 'The URL of this <em>Level Up XP</em> page does not match the current plugin configuration. Your current configuration declares <em>Level Up XP</em> to be used \'Sitewide\', however this page expected it to be used \'Per course\'. Please <a href="{$a->nexturl}">click here</a> to navigate to the right page. Search for the admin setting \'block_xp_context\' if you wish to change your configuration.';
$string['errorcontextcoursemismatchpercourse'] = 'The URL of this <em>Level Up XP</em> page does not match the current plugin configuration. Your current configuration declares <em>Level Up XP</em> to be used \'Per course\', but this page expects it to be used \'Sitewide\'. It most likely originates from a <em>block</em> that was added to the dashboard or front page while in a different configuration. You should remove the block from the latter pages, and only use the block from within individual courses.';
$string['errorformvalues'] = 'There are some issues in the form values, please fix them.';
$string['errorlevelsincorrect'] = 'The minimum number of levels is 2';
$string['errornotalllevelsbadgesprovided'] = 'Not all the level badges have been provided. Missing: {$a}';
$string['errorunknownevent'] = 'Error: unknown event';
$string['errorunknownmodule'] = 'Error: unknown module';
$string['errorxprequiredlowerthanpreviouslevel'] = 'The points required are lower than or equal to the previous level.';
$string['eventsrules'] = 'Event rules';
$string['eventsrules_help'] = 'This plugin is making use of the events to attribute points to actions performed by the students.
You can use the form below to add your own rules and modify the default ones.

It is advised to check the plugin\'s _Log_ page to identify which events are triggered as students perform actions in the course.

Additional resources:

- [How are experience points calculated?](https://docs.levelup.plus/xp/docs/getting-started/points-calculation?ref=blockxp_help)
- [Troubleshooting rules](https://docs.levelup.plus/xp/docs/troubleshooting/event-rule-not-working?ref=blockxp_help)
';
$string['eventsrulesintro'] = 'Observe actions and attribute points to students as they perform them.';
$string['event_user_leveledup'] = 'User levelled up';
$string['eventis'] = 'The event is {$a}';
$string['eventname'] = 'Event name';
$string['eventproperty'] = 'Event property';
$string['eventtime'] = 'Event time';
$string['export'] = 'Export';
$string['exportdata'] = 'Export data';
$string['filterbyuser'] = 'Filter by user';
$string['filterellipsis'] = 'Filter...';
$string['filtermodules'] = 'Filter modules';
$string['filterparticipants'] = 'Filter participants';
$string['forever'] = 'Forever';
$string['give'] = 'give';
$string['gotofullladder'] = 'Go to full leaderboard';
$string['graderules'] = 'Grade rules';
$string['graderules_help'] = '
Students will earn as many points as their grade.
A grade of 5/10, and a grade of 5/100 will both award the student 5 points.
When a student\'s grade changes multiple times, they will earn points equal to the maximum grade they have received.
Points are never taken away from students, and negative grades are ignored.

Example: Alice submits an assignment, and receives the mark of 40/100. In _Level Up XP_, Alice receives 40 points for her grade.
Alice reattempts her assignment, but this time her grade is lowered to 25/100. Alice\'s points in _Level Up XP_ do not change.
For her final attempt, Alice scores 60/100, she earns 20 additional points in _Level Up XP_, her total of points earned is 60.

[More at _Level Up XP_ documentation](https://docs.levelup.plus/xp/docs/how-to/grade-based-rewards?ref=blockxp_help)
';
$string['graderulesintro'] = 'The grade rules allow users to receive points equal to the grades they receive.';
$string['hasname'] = 'Name set';
$string['hasnoname'] = 'No name';
$string['hasdescription'] = 'Description set';
$string['hasnodescription'] = 'No description';
$string['haspopupmessage'] = 'Popup message set';
$string['hasnopopupmessage'] = 'No popup message';
$string['hasbadgeaward'] = 'Badge to award set';
$string['hasnobadgeaward'] = 'No badge to award';
$string['hideparticipantsidentity'] = 'Hide participants identity';
$string['hiderank'] = 'Hide rank';
$string['importpoints'] = 'Import points';
$string['importpoints_help'] = 'The import may be used to _increase_ students\' points, or to _override_ them with the provided value.

Note that the import __does not__ use the same format as the exported report. The required format is described in the [documentation](https://docs.levelup.plus/xp/docs/how-to/import-points/importing-points-from-csv?ref=localxp_help), where a [sample file](https://docs.levelup.plus/xp/docs/how-to/import-points/importing-points-from-csv?ref=localxp_help#sample-file) is available.';
$string['importpointsintro'] = 'Import points from a CSV file, and optionally send a message to the recipient.';
$string['incourses'] = 'In courses';
$string['ineffective'] = 'Ineffective';
$string['infos'] = 'Information';
$string['infos_help'] = 'The information page gives students an overview of the levels, and the points needed to attain them. It also displays the name of each level, as well as the level description.';
$string['infosintro'] = 'The information page displays the list of levels, and some of their details.';
$string['installed'] = 'Installed';
$string['instructions'] = 'Instructions';
$string['instructions_help'] = 'The instructions will be displayed on the information page. You may use them to share information and instructions regarding the levels, how to obtain points, etc.';
$string['invalidxp'] = 'Invalid points value';
$string['keeplogs'] = 'Keep logs';
$string['ladder'] = 'Leaderboard';
$string['ladder_help'] = 'The leaderboard ranks students based on their points. When used in a course with groups, it can create a ranking for each group of student.

Several options are available to customise the leaderboard and the experience it will give to the participants.';
$string['ladderintro'] = 'The leaderboard shows a ranking of individuals based on their total points.';
$string['ladderadditionalcols'] = 'Additional columns';
$string['ladderadditionalcols_help'] = 'This setting determines which additional columns are displayed on the leaderboard. Press the CTRL or CMD key while clicking to select more than one column, or to unselect a selected column.';
$string['ladderempty'] = 'The leaderboard is currently empty, make sure to come back later!';
$string['ladderisodefault'] = 'Default (group mode)';
$string['ladderisocohorts'] = 'Using cohorts';
$string['ladderiso'] = 'Isolate participants';
$string['ladderiso_help'] = "Create separate leaderboards for different groups of people.

- Default (group mode): Follows the course's group mode to create leaderboards for each group.
- Using cohorts: Only members of the same cohort will appear in a person's leaderboard.

[More info](https://docs.levelup.plus/xp/docs/leaderboard-isolation)";
$string['laddersettingsmovednotice'] = 'The leaderboard settings were moved to the [leaderboard page]({$a->url}).';
$string['learnmore'] = 'Learn more';
$string['level'] = 'Level';
$string['levelbadge'] = 'Level badge';
$string['levelbadges'] = 'Level badges';
$string['levelbadges_help'] = 'Upload images to replace the appearance of individual levels.

The files must be named [level].[file extension], for instance 1.png, 2.jpg, etc.

We recommend images of a size of 100x100 pixels, in any of the following types: GIF, JPEG, PNG and SVG.';
$string['leveldescriptiondesc'] = 'A short description of the level, displayed to students on the information page.';
$string['levelpointsstart'] = 'Start';
$string['levelpointslength'] = 'Length';
$string['levels'] = 'Levels';
$string['levelsappearance'] = 'Levels appearance';
$string['levelssaved'] = 'The levels have been saved.';
$string['levelup'] = 'Level up!'; // The action, not the brand!
$string['levelupoptionsunavailableforlevelone'] = 'Options related to attaining the level are unavailable for the first level.';
$string['levelupplus'] = 'Level Up XP+';
$string['levelx'] = 'Level #{$a}';
$string['likenotice'] = '<strong>Do you like the plugin?</strong> Please take a moment to <a href="{$a->moodleorg}" target="_blank">add it to your favourites</a> on Moodle.org and <a href="{$a->github}" target="_blank">star it on GitHub</a>.';
$string['limitparticipants'] = 'Limit participants';
$string['limitparticipants_help'] = 'This setting controls who is displayed in the leaderboard. Neighbours are the participants ranked above and below the current user. For instance, when choosing \'Display 2 neighbours\', only the two participants ranked directly higher and lower than the current user will be displayed.';
$string['logging'] = 'Logging';
$string['manually'] = 'Manually';
$string['maxactionspertime'] = 'Max. actions in time frame';
$string['maxactionspertime_help'] = 'The maximum number of actions that will count for points during the time frame given. Any subsequent action will be ignored. When this value is empty, or equals to zero, it does not apply.';
$string['maxlevelexcl'] = 'max level!';
$string['menu'] = 'Menu';
$string['messageprovider:adminnotice'] = 'Admin notice';
$string['missing'] = 'Missing';
$string['movecondition'] = 'Move condition';
$string['moverule'] = 'Move rule';
$string['name'] = 'Name';
$string['namecontains'] = 'Contains "{$a}"';
$string['nameequalsto'] = 'Is equal to "{$a}"';
$string['navbardisplay'] = 'Show in navbar';
$string['navbardisplay_desc'] = 'When enabled, the level of the user will be displayed in the top navigation bar. If the plugin is used "Per courses", it will only appear in courses. Please note that this functionality is heavily dependent on the theme and may not work well, or not at all, with 3rd party themes. [Learn more](https://docs.levelup.plus/xp/docs/navbar-display)';
$string['navcompletionrules'] = 'Completion';
$string['navdrops'] = 'Drops';
$string['naveventrules'] = 'Event rules';
$string['navgraderules'] = 'Grade rules';
$string['navimport'] = 'Import';
$string['navinfos'] = 'Info';
$string['navladder'] = 'Leaderboard';
$string['navlevels'] = 'Levels';
$string['navlevelssetup'] = 'Setup';
$string['navlog'] = 'Log';
$string['navpoints'] = 'Points';
$string['navpromo'] = 'XP+';
$string['navreport'] = 'Report';
$string['navsettings'] = 'Settings';
$string['navvisuals'] = 'Appearance';
$string['nextlevelin'] = 'next level in';
$string['noconditionsyet'] = 'No conditions, yet!';
$string['noconditionsyetintro'] = 'Get started by adding a condition.';
$string['nodescription'] = 'No description';
$string['noissuesidentified'] = 'No issues identified';
$string['nologsrecordedyet'] = 'Logs have not been recorded yet.';
$string['noname'] = 'No name';
$string['noneareavailable'] = 'None are available.';
$string['notecompatibilityissues'] = 'Please note the compatibility issues identified below:';
$string['notesomesettingslocked'] = 'Note that some settings may not be editable when they have been locked by an administrator.';
$string['nothingmatchesfilter'] = 'Nothing matches the filter.';
$string['numberoflevels'] = 'Number of levels';
$string['occasionally'] = 'Occasionally';
$string['outofsync'] = 'XP plugins incompatibility';
$string['outofsyncinfo'] = 'The XP plugins are incompatible with one another which can lead to unexpected issues. In the future, XP+ will automatically disable itself. The version {$a->localxpversion} of Level Up XP+ (local_xp) is expected.';
$string['pagecurrentnotvisibletoviewers'] = 'This page is not currently visible to students.';
$string['pagecurrentvisibletoviewers'] = 'This page is currently visible to students.';
$string['pagesettings'] = 'Page settings';
$string['participant'] = 'Participant';
$string['participants'] = 'Participants';
$string['participatetolevelup'] = 'Participate in the course to gain experience points and level up!';
$string['perpagecolon'] = 'Per page:';
$string['pickaconditiontype'] = 'Pick a condition type';
$string['pluginavailabilityxpdesc'] = 'This plugin lets instructors restrict access to activities based on students\' levels.';
$string['pluginenrolxpdesc'] = 'This plugin enables automatic enrolment into courses based on a student\'s level in another course.';
$string['pluginshortcodesdesc'] = 'This plugin lets instructors customise their material by including XP related elements (points, level, leaderboard, …) in the content, as well as hiding or showing content based on a student\'s level.';
$string['pluginname'] = 'Level Up XP';
$string['pluginsoutofsync'] = '__XP plugins incompatibility!__

There are compatibility issues between Level Up XP and Level Up XP+. In the future, Level Up XP+ will automatically disable itself if it is not compatible. To prevent this, please contact your site administrator. [Read more]({$a->url})';
$string['pluginxmaybeincompatible'] = 'This version of {$a->name} ({$a->component}) may be incompatible with Moodle {$a->version}.';
$string['pointstoaward'] = 'Points to award';
$string['pointstoaward_help'] = 'The number of points to award when the condition is met.';
$string['pointsintimelinker'] = 'per';
$string['pointsperlevel'] = 'Points per level';
$string['pointsrequired'] = 'Points required';
$string['popupnotificationmessage'] = 'Popup notification message';
$string['popupnotificationmessagedesc'] = 'Optional message to display within the popup notification that congratulates the user for attaining the level.';
$string['potentialmoodleincompatibility'] = 'Potential Moodle incompatibility';
$string['previewpopupnotification'] = 'Preview notification';
$string['privacy:path:addon'] = 'Add-on';
$string['privacy:path:level'] = 'Level';
$string['privacy:path:logs'] = 'Logs';
$string['privacy:metadata:log'] = 'Stores a log of events';
$string['privacy:metadata:log:eventname'] = 'The event name';
$string['privacy:metadata:log:time'] = 'The date at which it happened';
$string['privacy:metadata:log:userid'] = 'The user who gained the points';
$string['privacy:metadata:log:xp'] = 'The points awarded for the event';
$string['privacy:metadata:prefintro'] = 'Records whether the user dismissed the block\'s intro';
$string['privacy:metadata:preflevelup'] = 'Records whether the user should see the level up notification';
$string['privacy:metadata:prefnotices'] = 'Records whether the user closed the support notice';
$string['privacy:metadata:prefseenpromo'] = 'Records when the user viewed the promo page';
$string['privacy:metadata:prefladderpagesize'] = 'The user\'s preferred page size when viewing the leaderboard';
$string['privacy:metadata:xp'] = 'Stores the points and level of users';
$string['privacy:metadata:xp:xp'] = 'The user\'s points';
$string['privacy:metadata:xp:userid'] = 'The user';
$string['progress'] = 'Progress';
$string['progressbar'] = 'Progress bar';
$string['property:action'] = 'Event action';
$string['property:component'] = 'Event component';
$string['property:crud'] = 'Event CRUD';
$string['property:eventname'] = 'Event name';
$string['property:target'] = 'Event target';
$string['promocheatguard'] = 'This cheat guard is not designed to cover long time frames. Please consider upgrading to <em>Level Up XP+</em> to unlock greater time frames and other features. <a href="{$a->url}">Read more here</a>.';
$string['promogetnow'] = 'Get XP+ now!';
$string['promointro'] = 'Become the game master! Unlock additional features and take gamification to a whole new level with Level Up XP+!';
$string['promointroinstalled'] = 'The add-on _Level Up XP+_ is installed on your system and all of its features have been enabled.';
$string['promorulesdidyouknow'] = 'Did you know that with <em>Level Up XP+</em> students can receive points for <em>completing courses</em> and <em>activities</em>, or even receive points according to their <em>grades</em>? <a href="{$a->url}">Discover more here</a>.';
$string['questreleasenotice'] = '🎉 We are excited to announce our **new gamification plugin** called **Level Up Quest** 🥳. Turn your courses into **exciting adventures**, filled with **re-engagement strategies** and **celebrations** 🤯! Check out [Quest\'s website]({$a->questurl}) and our [launch post here]({$a->questblogurl}). 👈';
$string['quickeditpoints'] = 'Quick edit points';
$string['rank'] = 'Rank';
$string['ranking'] = 'Ranking';
$string['ranking_help'] = 'The rank is the absolute position of the current user in the leaderboard. The relative rank is the difference in experience points between a user and their neighbours.';
$string['reallydeleteuserstate'] = 'Deleting a user is only useful to remove them from the leaderboard. For any other reasons, we recommend setting their points to 0 instead. Note that deleting them does not affect their ability to earn points in the future.

Importantly, when using _Level Up XP_ sitewide, deleting them will make them disappear from the report, in which case you will not be able to re-assign them points. However, if you are using _Level Up XP_ per course, the student may still appear in the report if they are enrolled in the course.

Do you really want to delete the points of this user?';
$string['reallydeleteuserstateandlogs'] = 'Deleting a user removes them from the leaderboard and removes all of their associated logs.

Removing logs may enable a user to re-earn points for previous actions. If your intention is only to reset their points, we recommend setting their points to 0 instead. Note that deleting a user does not affect their ability to earn points in the future.

Importantly, when using _Level Up XP_ sitewide, deleting them will make them disappear from the report, in which case you will not be able to re-assign them points. However, if you are using _Level Up XP_ per course, the student may still appear in the report if they are enrolled in the course.

Do you really want to delete the points and logs of this user?';
$string['reallyresetallcoursestodefaults'] = 'Are you sure that you want to reset the rules in ALL courses to the default rules? This action is not reversible.';
$string['reallyresetcourserulestodefaults'] = 'Are you sure that you would like to reset the rules to the default rules? This action is not reversible.';
$string['reallyresetallcourselevelstodefaults'] = 'Are you sure that you want to reset the levels in ALL courses to the default levels? This action is not reversible.';
$string['reallyresetcourselevelstodefaults'] = 'Are you sure that you want to reset the levels to the default levels? This action is not reversible.';
$string['reallyresetallcoursevisualstodefaults'] = 'Are you sure that you want to appearance of the levels in ALL courses to the default appearance? This action is not reversible.';
$string['reallyresetcoursevisualstodefaults'] = 'Are you sure that you want to reset the appearance of the levels to the default appearance? This action is not reversible.';
$string['reallyresetdata'] = 'Are you sure that you want to reset the levels and points of everyone in this course? This action is not reversible.';
$string['reallyresetgroupdata'] = 'Really reset the levels and points of everyone in this group?';
$string['reallyreverttopluginsdefaults'] = 'Really reset the default rules to the defaults suggested by the plugin? This action is not reversible.';
$string['recentrewards'] = 'Recent rewards';
$string['recommended'] = 'Recommended';
$string['recommendedplugins'] = 'Recommended plugins';
$string['releasenotes'] = 'Release notes';
$string['remaining'] = 'remaining';
$string['removefilter'] = 'Remove filter';
$string['reportisempty'] = 'The report is empty, student have yet to earn points.';
$string['reportisemptyenrolstudents'] = 'The report is empty, have students been enrolled in this course?';
$string['resetcoursedata'] = 'Reset course data';
$string['resetallcoursestodefaults'] = 'Reset all courses to defaults';
$string['resetallcoursestodefaultsintro'] = 'Click the button below to reset all courses to the above defaults.';
$string['resetgroupdata'] = 'Reset group data';
$string['resettodefaults'] = 'Reset to defaults';
$string['resultsfilteredforn'] = 'Results filtered for {$a}.';
$string['reward'] = 'Reward';
$string['requires'] = 'Requires';
$string['reverttopluginsdefaults'] = 'Revert to plugin\'s defaults';
$string['reverttopluginsdefaultsintro'] = 'Use the button below if you would like to revert the above defaults to the plugin\'s defaults. This does not affect the rules in existing courses.';
$string['rule'] = 'Rule';
$string['rule:contains'] = 'contains';
$string['rule:eq'] = 'is equal to';
$string['rule:eqs'] = 'is strictly equal to';
$string['rule:gt'] = 'is greater than';
$string['rule:gte'] = 'is greater or equal to';
$string['rule:lt'] = 'is less than';
$string['rule:lte'] = 'is less or equal to';
$string['rule:regex'] = 'matches the regex';
$string['ruleadded'] = 'The condition has been added.';
$string['rulecm'] = 'Activity or resource';
$string['rulecm_help'] = 'This condition is met when the event occurs in the activity or resource specified.';
$string['rulecmdesc'] = 'The activity or resource is \'{$a->contextname}\'.';
$string['rulecmdescwithcourse'] = 'The activity or resource is: \'{$a->contextname}\' in \'{$a->coursename}\'.';
$string['rulecminfo'] = 'This condition requires that the action takes place in a specific activity or resource.';
$string['ruleevent'] = 'Specific event';
$string['ruleeventdesc'] = 'The event is \'{$a->eventname}\'';
$string['ruleeventinfo'] = 'Choose the action that users must perform out of a curated list of events.';
$string['rulefiltercm'] = 'Specific activity';
$string['rulefiltercmdesc'] = 'Target a specific activity or resource in the course.';
$string['rulefiltercmname'] = 'Activity name';
$string['rulefiltercmnamedesc'] = 'Condition based on the name of the activity.';
$string['rulefilteranycm'] = 'Any activity';
$string['rulefilteranycmdesc'] = 'This condition will match with any activity.';
$string['rulefilteranycourse'] = 'Any course';
$string['rulefilteranycoursedesc'] = 'This condition will match with any course.';
$string['rulefilteranysection'] = 'Any section';
$string['rulefilteranysectiondesc'] = 'This condition will match with any section.';
$string['rulefilterany'] = 'Any';
$string['rulefilteranydesc'] = 'This condition matches with anything.';
$string['rulefilternone'] = 'Nothing';
$string['rulefiltersection'] = 'Specific section';
$string['rulefiltersectiondesc'] = 'Target a specific section in the course.';
$string['rulefilterthiscourse'] = 'This course';
$string['rulefilterthiscoursedesc'] = 'Target the current course.';
$string['ruleproperty'] = 'Event property';
$string['rulepropertydesc'] = 'The property \'{$a->property}\' {$a->compare} \'{$a->value}\'.';
$string['rulepropertyinfo'] = 'This condition is for power users with a technical understanding of the events and their properties.';
$string['ruleset'] = 'Set of conditions';
$string['ruleset:all'] = 'ALL of the conditions are true';
$string['ruleset:any'] = 'ANY of the conditions are true';
$string['ruleset:none'] = 'NONE of the conditions are true';
$string['rulesetinfo'] = 'Combine multiple conditions into one.';
$string['ruletypecmcompletion'] = 'Activity completion';
$string['ruletypecmcompletiondesc'] = 'Award points when an activity is marked as complete.';
$string['ruletypecoursecompletion'] = 'Course completion';
$string['ruletypecoursecompletiondesc'] = 'Award points when a course is marked as complete.';
$string['ruletypesectioncompletion'] = 'Section completion';
$string['ruletypesectioncompletiondesc'] = 'Award points when a course section is marked as complete.';
$string['rulesscope'] = 'Scope';
$string['rulesscope_help'] = 'The scope of rules determine when they apply.

Rules can be created in two scopes: sitewide and course-specific. Whenever possible, course-specific rules are assessed first, followed by sitewide rules. This approach enables educators to create broad rules applicable across the site (sitewide) and then adjust them for individual courses.

- Sitewide: These rules are effective across the entire site, except when course-specific rules are in place.
- Course: These rules are applicable only within a specific course. They have priority over sitewide rules.
';
$string['searchandselectcourse'] = 'Search and select a course';
$string['searchandselectmodule'] = 'Search and select an activity or resource';
$string['selectcourse'] = 'Select course';
$string['send'] = 'Send';
$string['setpoints'] = 'Set points';
$string['settingsoutdatedxppnotice'] = 'If you are seeing settings below, this means that an outdated version of XP+ is installed. Please ask your administrator to resolve the issue by installing the latest versions.';
$string['shortcode:xpbadge'] = 'The badge matching the current user\'s level.';
$string['shortcode:xpladder'] = 'Display a portion of the leaderboard.';
$string['shortcode:xpladder_help'] = '
By default, a portion of the leaderboard surrounding the current user will be displayed.

```
[xpladder]
```

To display the top 10 students instead of the neighbours of the current user, set the parameter `top`. You can optionally set the number of users to display like so `top=20`.

```
[xpladder top]
[xpladder top=15]
```

A link to the full leaderboard will automatically be displayed below the table, if you do not want to display such link, add the argument `hidelink`.

```
[xpladder hidelink]
```

By default, the table does not include the progress column which displays the progress bar. If such column has been selected in the additional columns in the leaderboard\'s settings, you can use the argument `withprogress` to display it.

```
[xpladder withprogress]
```

Note that when a course is using groups, the leaderboard will best guess which group to display the leaderboard of.
';
$string['shortcode:xpiflevel'] = 'Display the content when the current user\'s level matches.';
$string['shortcode:xpiflevel_help'] = '
Refer to the examples below to format this shortcode. When a level is strictly specified, the content will be displayed regardless of the other rules.
The _greater_ and _less than_ rules must all match for the content to be displayed. Watch out as that may sometimes result in the content to never be displayed!
Note that teachers, or otherwise users with editing capabilities, will always see everything.

```
[xpiflevel 1 3 5]
    Displayed if the user\'s level is exactly 1, 3 or 5.
[/xpiflevel]

[xpiflevel >3]
    Displayed if the user\'s level is greater than 3.
[/xpiflevel]

[xpiflevel >=3]
    Displayed if the user\'s level is greater or equal to 3.
[/xpiflevel]

[xpiflevel >=10 <20 30]
    Displayed if the user\'s level is greater or equal to 10 AND is strictly less than 20
    OR is exactly equal to 30.
[/xpiflevel]

[xpiflevel <=10 >=20]
    Never displayed because the user\'s level can never be less or equal to 10 AND more or equal to 20.
[/xpiflevel]
```

Note that these shortcodes CANNOT be nested within one another.
';
$string['shortcode:xplevelname'] = 'Display the level name.';
$string['shortcode:xplevelname_help'] = '
By default the tag displays the name of the current user\'s level.
Alternatively, you can use the `level` argument to display the name of a specific level.

```
[xplevelname]
[xplevelname level=5]
```

If the `level` argument is provided and the level does not exist, nothing will be displayed.
';
$string['shortcode:xppoints'] = 'Display a number of points formatted as experience points.';
$string['shortcode:xppoints_help'] = '
By default, this shows the number of points of the current user. Alternatively, you can specify a number to override this value.

The styling of the points will depend on whether an arbitrary value is displayed or the current user\'s points. The argument `plain` can be used to remove any styling.

```
[xppoints]
[xppoints 500]
[xppoints 123 plain]
```
';
$string['shortcode:xpprogressbar'] = 'The current user\'s progress bar towards the next level.';
$string['sitewide'] = 'Sitewide';
$string['somefeaturesrequireotherplugins'] = 'Some features require additional plugins to be installed.';
$string['someoneelse'] = 'Someone else';
$string['somethinghappened'] = 'Something happened';
$string['taskadminnotices'] = 'Admin notices';
$string['taskcollectionloggerpurge'] = 'Purge collection logs';
$string['taskusagereport'] = 'Usage report';
$string['teamleaderboard'] = 'Team leaderboard';
$string['teamleaderboard_help'] = 'The team leaderboard shows a ranking of teams based on the total cumulated points of their members.

The teams can be made out of course groups or cohorts. Options are also to accomodate for different team sizes.

[Learn more](https://docs.levelup.plus/xp/docs/how-to/setup-team-leaderboard/team-leaderboard?ref=blockxp_help)';
$string['teamleaderboardintro'] = 'The team leaderboard is a ranking of teams based on their members\' points';
$string['teams'] = 'Teams';
$string['total'] = 'Total';
$string['thankyou'] = 'Thank you!';
$string['timebetweensameactions'] = 'Time required between identical actions';
$string['timebetweensameactions_help'] = 'The minimum time required before an action that already happened previously is accepted again. An action is considered identical if it was placed in the same context and object, reading a forum post will be considered identical if the same post is read again. When this value is empty, or equals to zero, it does not apply.';
$string['timeformaxactions'] = 'Time frame for max. actions';
$string['timeformaxactions_help'] = 'The time frame (in seconds) during which the user should not exceed a maximum number of actions.';
$string['tinytimenow'] = 'now';
$string['tinytimeseconds'] = '{$a}s';
$string['tinytimeminutes'] = '{$a}m';
$string['tinytimehours'] = '{$a}h';
$string['tinytimedays'] = '{$a}d';
$string['tinytimeweeks'] = '{$a}w';
$string['tinytimewithinayearformat'] = '%b %e';     // No, this is not a regex! @codingStandardsIgnoreLine.
$string['tinytimeolderyearformat'] = '%b %Y';
$string['tryme'] = 'Try me';
$string['unlockfeaturewithxpplus'] = 'Unlock this feature with XP+. <a href="{$a}">Learn more</a>';
$string['unavailable'] = 'Unavailable';
$string['unstableversioninstalledinfo'] = 'This version of Level Up XP (block_xp) is still in development and considered unstable, please use an official release.';
$string['upgradingplugins'] = 'Upgrading the plugins';
$string['unstableversioninstalled'] = 'Unstable version installed';
$string['value'] = 'Value';
$string['visualsintro'] = 'Customise the appearance of the levels, and the meaning of the points.';
$string['wherearexpused'] = 'Where are points used?';
$string['wherearexpused_desc'] = 'When set to \'In courses\', the points gained will only account for the course in which the block was added to. When set to \'Sitewide\', a user will "level up" in the site rather than selectively per course, all the points gained throughout the site will be used.';
$string['updateandpreview'] = 'Update and preview';
$string['urlaccessdeprecated'] = 'Access via this URL is deprecated, please update your links.';
$string['usagereport'] = 'Share usage report';
$string['usagereport_desc'] = 'Periodically share anonymous usage information with the plugin developers. This information will help better understand how the plugin is being used and will influence its development. The information shared contains basic information about the Moodle site (URL, version), and usage information about the plugin (number of users earning points, settings overview, rules used, ...).';
$string['usealgo'] = 'Use the algorithm';
$string['usecustomlevelbadges'] = 'Use custom level badges';
$string['usecustomlevelbadges_help'] = 'When set to yes, you must provide an image for each level.';
$string['unknownactivitya'] = 'Unknown activity ({$a})';
$string['unknownbadgea'] = 'Unknown badge ({$a})';
$string['unknownconditiona'] = 'Unknown condition ({$a})';
$string['unknowneventa'] = 'Unknown event ({$a})';
$string['unknowntypea'] = 'Unknown type ({$a})';
$string['unknownsectiona'] = 'Unknown section ({$a})';
$string['viewas'] = 'View as';
$string['viewlogs'] = 'View logs';
$string['when'] = 'When';
$string['whoops'] = 'Whoops!';
$string['wewillreplyat'] = 'We will reply at: _{$a}_.';
$string['xp:addinstance'] = 'Add a new block';
$string['xp:earnxp'] = 'Earning points';
$string['xp:manage'] = 'Manage all aspects of experience points';
$string['xp:myaddinstance'] = 'Add the block to my dashboard';
$string['xp:view'] = 'View the block and its related pages';
$string['xp:viewlogs'] = 'View the logs';
$string['xp:viewreport'] = 'View the report';
$string['xpplusrequired'] = 'XP+ required';
$string['xpgaindisabled'] = 'Points gain disabled';
$string['youleveledupexcl'] = 'You levelled up!';
$string['youreachedlevel'] = 'You have reached the level:';
$string['youreachedlevela'] = 'You have reached level {$a}!';
$string['yourmessage'] = 'Your message';
$string['yourownrules'] = 'Your own rules';

// Deprecated since 3.0.0.
$string['addrulesformhelp'] = 'The last column defines the amount of experience points gained when the criteria is met.';
$string['changelevelformhelp'] = 'If you change the number of levels, the custom level badges will be temporarily disabled to prevent levels without badges. If you change the level count go to the page \'Visuals\' to re-enable the custom badges once you have saved this form.';
$string['enablelogging'] = 'Enable logging';
$string['levelswillbereset'] = 'Warning! Saving this form will recalculate the levels of everyone!';
$string['viewtheladder'] = 'View the ladder';
$string['xp'] = 'Experience points';
$string['xprequired'] = 'XP required';

// Deprecated since 3.1.0.
$string['promocontactintro'] = 'Contact us for more information. We don\'t bite and we reply quickly!';
$string['promocontactus'] = 'Get in touch';
$string['promoemailusat'] = 'E-mail us at _levelup@branchup.tech_.';
$string['promoerrorsendingemail'] = 'Ouch! We could not send the message... please e-mail us directly at: {$a}. Thanks!';
$string['promoifpreferemailusat'] = 'Psst! If you prefer, e-mail us directly at _{$a}_.';
$string['promoyourmessagewassent'] = 'Thank you, your message was sent. We will get back to you very shortly.';

// Deprecated since 3.8.1.
$string['rulesformhelp'] = '<p>This plugin is making use of the events to attribute points to actions performed by the students. You can use the form below to add your own rules and view the default ones.</p>
<p>It is advised to check the plugin\'s <a href="{$a->log}">log</a> to identify what events are triggered as you perform actions in the course, and also to read more about events themselves: <a href="{$a->list}">list of all events</a>, <a href="{$a->doc}">developer documentation</a>.</p>
<p>Finally, please note that the plugin always ignores:
<ul>
    <li>The actions performed by administrators, guests or non-logged in users.</li>
    <li>The actions performed by users not having the capability <em>block/xp:earnxp</em>.</li>
    <li>Repeated actions within a short time interval, to prevent cheating.</li>
    <li>Events that are flagged as <em>anonymous</em>, e.g. in an anonymous Feedback.</li>
    <li>And the events of educational level not equal to <em>Participating</em>.</li>
</ul>
</p>';

// Deprecated since 3.10.1.
$string['for1day'] = 'For 1 day';
$string['for1month'] = 'For a month';
$string['for1week'] = 'For a week';
$string['for3days'] = 'For 3 days';

// Deprecated since 3.13.0.
$string['xptogo'] = '[[{$a}]] to go';

// Deprecated since 3.15.0
$string['basexp'] = 'Algorithm base';
$string['coursevisuals'] = 'Course visuals';
$string['levelbadgesformhelp'] = 'Name the files [level].[file extension], for instance: 1.png, 2.jpg, etc... The recommended image size is 100x100.';
$string['levelcount'] = 'Level count';
$string['leveldesc'] = 'Level description';
$string['leveldesc_help'] = 'A short description of the level, this is displayed on the information page alongside the level itself. You may use this to describe a reward for learners who attain the level, to include instructions on how to work towards this level, to describe the level in a playful manner (e.g. _Only the bravest souls have been known to attain this level_), etc.';
$string['levelname'] = 'Level name';
$string['levelname_help'] = 'A short name to display instead of the default _Level #1_, _Level #2_, etc. that is sometimes displayed. If you give names to some levels, we recommend that you give a name to all of them!';
$string['privacy:metadata:xp:lvl'] = 'The user\'s level';
$string['usingalgo'] = 'Using algorithm';
$string['valuessaved'] = 'The values have been successfully saved.';

// Deprecated since 3.16.0
$string['forthewholesite'] = 'For the whole site';

// Deprecated since 3.17.0
$string['addinstructions'] = 'Add more information';
$string['courserules'] = 'Course rules';
$string['defaultrulesformhelp'] = 'Those are the default rules provided by the plugin, they automatically give default points and ignore some redundant events. Your own rules take precedence over them.';
$string['editinstructions'] = 'Edit information';
$string['grid'] = 'Grid';
$string['list'] = 'List';
$string['navrules'] = 'Rules';
$string['outofsyncexcessive'] = 'Excessive out of sync';
$string['outofsyncexcessiveinfo'] = 'XP+ is excessively older than XP which can lead to unexpected issues. In the future, XP+ will automatically disable itself.';
$string['resetcourserulestodefaults'] = 'Reset course rules to defaults';
$string['resetlevelstodefaults'] = 'Reset levels to defaults';
$string['resetvisualstodefaults'] = 'Reset appearance to defaults';
