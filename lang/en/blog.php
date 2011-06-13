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
 * Strings for core subsystem 'blog'
 *
 * @package    core
 * @subpackage blog
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addnewentry'] = 'Add a new entry';
$string['addnewexternalblog'] = 'Register an external blog';
$string['assocdescription'] = 'If you are writing about a course and/or activity modules, select them here.';
$string['associated'] = 'Associated {$a}';
$string['associatewithcourse'] = 'Blog about course {$a->coursename}';
$string['associatewithmodule'] = 'Blog about {$a->modtype}: {$a->modname}';
$string['association'] = 'Association';
$string['associations'] = 'Associations';
$string['associationunviewable'] = 'This entry cannot be viewed by others until a course is associated with it or the \'Publish to\' field is changed';
$string['autotags'] = 'Add these tags';
$string['autotags_help'] = 'Enter one or more local tags (separated by commas) that you want to automatically add to each blog entry copied from the external blog into your local blog.';
$string['backupblogshelp'] = 'If enabled then blogs will be included in SITE automated backups';
$string['blockexternalstitle'] = 'External blogs';
$string['blocktitle'] = 'Blog tags block title';
$string['blog'] = 'Blog';
$string['blogaboutthis'] = 'Blog about this {$a->type}';
$string['blogaboutthiscourse'] = 'Add an entry about this course';
$string['blogaboutthismodule'] = 'Add an entry about this {$a}';
$string['blogadministration'] = 'Blog administration';
$string['blogdeleteconfirm'] = 'Delete this blog?';
$string['blogdisable'] = 'Blogging is disabled!';
$string['blogentries'] = 'Blog entries';
$string['blogentriesabout'] = 'Blog entries about {$a}';
$string['blogentriesbygroupaboutcourse'] = 'Blog entries about {$a->course} by {$a->group}';
$string['blogentriesbygroupaboutmodule'] = 'Blog entries about {$a->mod} by {$a->group}';
$string['blogentriesbyuseraboutcourse'] = 'Blog entries about {$a->course} by {$a->user}';
$string['blogentriesbyuseraboutmodule'] = 'Blog entries about this {$a->mod} by {$a->user}';
$string['blogentrybyuser'] = 'Blog entry by {$a}';
$string['blogpreferences'] = 'Blog preferences';
$string['blogs'] = 'Blogs';
$string['blogtags'] = 'Blog tags';
$string['cannotviewcourseblog'] = 'You do not have the required permissions to view blogs in this course';
$string['cannotviewcourseorgroupblog'] = 'You do not have the required permissions to view blogs in this course/group';
$string['cannotviewsiteblog'] = 'You do not have the required permissions to view all site blogs';
$string['cannotviewuserblog'] = 'You do not have the required permissions to read user blogs';
$string['configexternalblogcrontime'] = 'How often Moodle checks the external blogs for new entries.';
$string['configmaxexternalblogsperuser'] = 'The number of external blogs each user is allowed to link to their Moodle blog.';
$string['configuseblogassociations'] = 'Enables the association of blog entries with courses and course modules.';
$string['configuseexternalblogs'] = 'Enables users to specify external blog feeds. Moodle regularly checks these blog feeds and copies new entries to the local blog of that user.';
$string['courseblog'] = 'Course blog: {$a}';
$string['courseblogdisable'] = 'Course blogs are not enabled';
$string['courseblogs'] = 'Users can only see blogs for people who share a course';
$string['deleteblogassociations'] = 'Delete blog associations';
$string['deleteblogassociations_help'] = 'If ticked then blog entries will no longer be associated with this course or any course activities or resources.  The blog entries themselves will not be deleted.';
$string['deleteexternalblog'] = 'Unregister this external blog';
$string['deleteotagswarn'] = 'Are you sure you want to remove these tags from all blog posts and remove it from the system?';
$string['description'] = 'Description';
$string['description_help'] = 'Enter a sentence or two summarising the contents of your external blog. (If no description is supplied, the description recorded in your external blog will be used).';
$string['disableblogs'] = 'Disable blog system completely';
$string['donothaveblog'] = 'You do not have your own blog, sorry.';
$string['editentry'] = 'Edit a blog entry';
$string['editexternalblog'] = 'Edit this external blog';
$string['emptybody'] = 'Blog entry body can not be empty';
$string['emptyrssfeed'] = 'The URL you entered does not point to a valid RSS feed';
$string['emptytitle'] = 'Blog entry title can not be empty';
$string['emptyurl'] = 'You must specify a URL to a valid RSS feed';
$string['entrybody'] = 'Blog entry body';
$string['entrybodyonlydesc'] = 'Entry description';
$string['entryerrornotyours'] = 'This entry is not yours';
$string['entrysaved'] = 'Your entry has been saved';
$string['entrytitle'] = 'Entry title';
$string['entryupdated'] = 'Blog entry updated';
$string['externalblogcrontime'] = 'External blog cron schedule';
$string['externalblogdeleteconfirm'] = 'Unregister this external blog?';
$string['externalblogdeleted'] = 'External blog unregistered';
$string['externalblogs'] = 'External blogs';
$string['feedisinvalid'] = 'This feed is invalid';
$string['feedisvalid'] = 'This feed is valid';
$string['filterblogsby'] = 'Filter entries by...';
$string['filtertags'] = 'Filter tags';
$string['filtertags_help'] = 'You can use this feature to filter the entries you want to use. If you specify tags here (separated by commas) then only entries with these tags will be copied from the external blog.';
$string['groupblog'] = 'Group blog: {$a}';
$string['groupblogdisable'] = 'Group blog is not enabled';
$string['groupblogentries'] = 'Blog entries associated with {$a->coursename} by group {$a->groupname}';
$string['groupblogs'] = 'Users can only see blogs for people who share a group';
$string['incorrectblogfilter'] = 'Incorrect blog filter type specified';
$string['intro'] = 'This RSS feed was automatically generated from one or more blogs.';
$string['invalidgroupid'] = 'Invalid group ID';
$string['invalidurl'] = 'This URL is unreachable';
$string['linktooriginalentry'] = 'Link to original blog entry';
$string['maxexternalblogsperuser'] = 'Maximum number of external blogs per user';
$string['mustassociatecourse'] = 'If you are publishing to course or group members, you must associate a course with this entry';
$string['name'] = 'Name';
$string['name_help'] = 'Enter a descriptive name for your external blog. (If no name is supplied, the title of your external blog will be used).';
$string['noentriesyet'] = 'No visible entries here';
$string['noguestpost'] = 'Guest can not post blogs!';
$string['nopermissionstodeleteentry'] = 'You lack the permissions required to delete this blog entry';
$string['norighttodeletetag'] = 'You have no rights to delete this tag - {$a}';
$string['nosuchentry'] = 'No such blog entry';
$string['notallowedtoedit'] = 'You are not allowed to edit this entry';
$string['numberofentries'] = 'Entries: {$a}';
$string['numberoftags'] = 'Number of tags to display';
$string['pagesize'] = 'Blog entries per page';
$string['permalink'] = 'Permalink';
$string['personalblogs'] = 'Users can only see their own blog';
$string['preferences'] = 'Preferences';
$string['publishto'] = 'Publish to';
$string['publishto_help'] = 'There are 3 options:

* Yourself (draft) - Only you and the administrators can see this entry
* Anyone on this site - Anyone who is registered on this site can read this entry
* Anyone in the world - Anyone, including guests, could read this entry';
$string['publishtocourse'] = 'Users sharing a course with you';
$string['publishtocourseassoc'] = 'Members of the associated course';
$string['publishtocourseassocparam'] = 'Members of {$a}';
$string['publishtogroup'] = 'Users sharing a group with you';
$string['publishtogroupassoc'] = 'Your group members in the associated course';
$string['publishtogroupassocparam'] = 'Your group members in {$a}';
$string['publishtonoone'] = 'Yourself (draft)';
$string['publishtosite'] = 'Anyone on this site';
$string['publishtoworld'] = 'Anyone in the world';
$string['readfirst'] = 'Read this first';
$string['relatedblogentries'] = 'Related blog entries';
$string['retrievedfrom'] = 'Retrieved from';
$string['searchterm'] = 'Search: {$a}';
$string['settingsupdatederror'] = 'An error has occurred, blog preference setting could not be updated';
$string['siteblog'] = 'Site blog: {$a}';
$string['siteblogdisable'] = 'Site blog is not enabled';
$string['siteblogs'] = 'All site users can see all blog entries';
$string['tagdatelastused'] = 'Date tag was last used';
$string['tagparam'] = 'Tag: {$a}';
$string['tags'] = 'Tags';
$string['tagsort'] = 'Sort the tag display by';
$string['tagtext'] = 'Tag text';
$string['timefetched'] = 'Time of last sync';
$string['timewithin'] = 'Display tags used within this many days';
$string['updateentrywithid'] = 'Updating entry';
$string['url'] = 'URL';
$string['url_help'] = 'Enter the RSS feed URL for your external blog.';
$string['useblogassociations'] = 'Enable blog associations';
$string['useexternalblogs'] = 'Enable external blogs';
$string['userblog'] = 'User blog: {$a}';
$string['userblogentries'] = 'Blog entries by {$a}';
$string['valid'] = 'Valid';
$string['viewallblogentries'] = 'All entries about this {$a}';
$string['viewallmodentries'] = 'View all entries about this {$a->type}';
$string['viewallmyentries'] = 'View all of my entries';
$string['viewentriesbyuseraboutcourse'] = 'View entries about this course by {$a}';
$string['viewblogentries'] = 'Entries about this {$a->type}';
$string['viewblogsfor'] = 'View all entries for...';
$string['viewcourseblogs'] = 'View all entries for this course';
$string['viewgroupblogs'] = 'View entries for group...';
$string['viewgroupentries'] = 'Group entries';
$string['viewmodblogs'] = 'View entries for module...';
$string['viewmodentries'] = 'Module entries';
$string['viewmyentries'] = 'My entries';
$string['viewmyentriesaboutmodule'] = 'View my entries about this {$a}';
$string['viewmyentriesaboutcourse'] = 'View my entries about this course';
$string['viewsiteentries'] = 'View all entries';
$string['viewuserentries'] = 'View all entries by {$a}';
$string['worldblogs'] = 'The world can read entries set to be world-accessible';
$string['wrongpostid'] = 'Wrong blog post id';
$string['page-blog-edit'] = 'Blog editing pages';
$string['page-blog-index'] = 'Blog listing pages';
$string['page-blog-x'] = 'All blog pages';
