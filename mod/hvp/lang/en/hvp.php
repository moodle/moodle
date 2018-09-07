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

$string['modulename'] = 'Interactive Content';
$string['modulename_help'] = 'The H5P activity module enables you to create interactive content such as Interactive Videos, Question Sets, Drag and Drop Questions, Multi-Choice Questions, Presentations and much more.

In addition to being an authoring tool for rich content, H5P enables you to import and export H5P files for effective reuse and sharing of content.

User interactions and scores are tracked using xAPI and are available through the Moodle Gradebook.

You add interactive H5P content by creating content using the built-in authoring tool or uploading H5P files found on other H5P enabled sites.';
$string['modulename_link'] = 'https://h5p.org/moodle-more-help';
$string['modulenameplural'] = 'Interactive Content';
$string['pluginadministration'] = 'H5P';
$string['pluginname'] = 'H5P';
$string['intro'] = 'Introduction';
$string['h5pfile'] = 'H5P File';
$string['fullscreen'] = 'Fullscreen';
$string['disablefullscreen'] = 'Disable fullscreen';
$string['download'] = 'Download';
$string['copyright'] = 'Rights of use';
$string['embed'] = 'Embed';
$string['showadvanced'] = 'Show advanced';
$string['hideadvanced'] = 'Hide advanced';
$string['resizescript'] = 'Include this script on your website if you want dynamic sizing of the embedded content:';
$string['size'] = 'Size';
$string['close'] = 'Close';
$string['title'] = 'Title';
$string['author'] = 'Author';
$string['year'] = 'Year';
$string['source'] = 'Source';
$string['license'] = 'License';
$string['thumbnail'] = 'Thumbnail';
$string['nocopyright'] = 'No copyright information available for this content.';
$string['downloadtitle'] = 'Download this content as a H5P file.';
$string['copyrighttitle'] = 'View copyright information for this content.';
$string['embedtitle'] = 'View the embed code for this content.';
$string['h5ptitle'] = 'Visit H5P.org to check out more cool content.';
$string['contentchanged'] = 'This content has changed since you last used it.';
$string['startingover'] = "You'll be starting over.";
$string['confirmdialogheader'] = 'Confirm action';
$string['confirmdialogbody'] = 'Please confirm that you wish to proceed. This action is not reversible.';
$string['cancellabel'] = 'Cancel';
$string['confirmlabel'] = 'Confirm';
$string['noh5ps'] = 'There\'s no interactive content available for this course.';

$string['lookforupdates'] = 'Look for H5P updates';
$string['updatelibraries'] = 'Update All Libraries';
$string['removetmpfiles'] = 'Remove old H5P temporary files';
$string['removeoldlogentries'] = 'Remove old H5P log entries';

// Admin settings.
$string['displayoptionnevershow'] = 'Never show';
$string['displayoptionalwaysshow'] = 'Always show';
$string['displayoptionpermissions'] = 'Show only if user has permissions to export H5P';
$string['displayoptionpermissionsembed'] = 'Show only if user has permissions to embed H5P';
$string['displayoptionauthoron'] = 'Controlled by author, default is on';
$string['displayoptionauthoroff'] = 'Controlled by author, default is off';
$string['displayoptions'] = 'Display Options';
$string['enableframe'] = 'Display action bar and frame';
$string['enabledownload'] = 'Download button';
$string['enableembed'] = 'Embed button';
$string['enablecopyright'] = 'Copyright button';
$string['enableabout'] = 'About H5P button';
$string['hubsettingsheader'] = 'Content Types';
$string['enablehublabel'] = 'Use H5P Hub';
$string['disablehubdescription'] = "It's strongly encouraged to keep this option enabled. The H5P Hub provides an easy interface for getting new content types and keeping existing content types up to date. In the future, it will also make it easier to share and reuse content. If this option is disabled you'll have to install and update content types through file upload forms.";
$string['empty'] = 'Empty';
$string['reveal'] = 'Reveal';
$string['hide'] = 'Hide';
$string['sitekey'] = 'Site Key';
$string['sitekeydescription'] = 'The site key is a secret that uniquely identifies this site with the Hub.';

$string['sendusagestatistics'] = 'Contribute usage statistics';
$string['sendusagestatistics_help'] = 'Usage statistics numbers will automatically be reported to help the developers better understand how H5P is used and to determine potential areas of improvement. Read more about which <a {$a}>data is collected on h5p.org</a>.';
$string['enablesavecontentstate'] = 'Save content state';
$string['enablesavecontentstate_help'] = 'Automatically save the current state of interactive content for each user. This means that the user may pick up where he left off.';
$string['contentstatefrequency'] = 'Save content state frequency';
$string['contentstatefrequency_help'] = 'In seconds, how often do you wish the user to auto save their progress. Increase this number if you\'re having issues with many ajax requests';
$string['enabledlrscontenttypes'] = 'Enable LRS dependent content types';
$string['enabledlrscontenttypes_help'] = 'Makes it possible to use content types that rely upon a Learning Record Store to function properly, like the Questionnaire content type.';

// Admin menu.
$string['contenttypecacheheader'] = 'Content Type Cache';
$string['settings'] = 'H5P Settings';
$string['libraries'] = 'H5P Libraries';

// Content type cache section.
$string['ctcacheconnectionfailed'] = "Couldn't communicate with the H5P Hub. Please try again later.";
$string['ctcachenolibraries'] = 'No content types were received from the H5P Hub. Please try again later.';
$string['ctcachesuccess'] = 'Library cache was successfully updated!';
$string['ctcachelastupdatelabel'] = 'Last update';
$string['ctcachebuttonlabel'] = 'Update content type cache';
$string['ctcacheneverupdated'] = 'Never';
$string['ctcachetaskname'] = 'Update content type cache';
$string['ctcachedescription'] = 'Making sure the content type cache is up to date will ensure that you can view, download and use the latest libraries. This is different from updating the libraries themselves.';

// Upload libraries section.
$string['uploadlibraries'] = 'Upload Libraries';
$string['options'] = 'Options';
$string['onlyupdate'] = 'Only update existing libraries';
$string['disablefileextensioncheck'] = 'Disable file extension check';
$string['disablefileextensioncheckwarning'] = "Warning! Disabling the file extension check may have security implications as it allows for uploading of php files. That in turn could make it possible for attackers to execute malicious code on your site. Please make sure you know exactly what you're uploading.";
$string['upload'] = 'Upload';

// Installed libraries section.
$string['installedlibraries'] = 'Installed Libraries';
$string['invalidtoken'] = 'Invalid security token.';
$string['missingparameters'] = 'Missing parameters';
$string['nocontenttype'] = 'No content type was specified.';
$string['invalidcontenttype'] = 'The chosen content type is invalid.';
$string['installdenied'] = 'You do not have permission to install content types. Contact the administrator of your site.';
$string['downloadfailed'] = 'Downloading the requested library failed.';
$string['validationfailed'] = 'The requested H5P was not valid';
$string['validatingh5pfailed'] = 'Validating h5p package failed.';

// H5P library list headers on admin page.
$string['librarylisttitle'] = 'Title';
$string['librarylistrestricted'] = 'Restricted';
$string['librarylistinstances'] = 'Instances';
$string['librarylistinstancedependencies'] = 'Instance dependencies';
$string['librarylistlibrarydependencies'] = 'Library dependencies';
$string['librarylistactions'] = 'Actions';

// H5P library page labels.
$string['addlibraries'] = 'Add libraries';
$string['installedlibraries'] = 'Installed libraries';
$string['notapplicable'] = 'N/A';
$string['upgradelibrarycontent'] = 'Upgrade library content';

// Upgrade H5P content page.
$string['upgrade'] = 'Upgrade H5P';
$string['upgradeheading'] = 'Upgrade {$a} content';
$string['upgradenoavailableupgrades'] = 'There are no available upgrades for this library.';
$string['enablejavascript'] = 'Please enable JavaScript.';
$string['upgrademessage'] = 'You are about to upgrade {$a} content instance(s). Please select upgrade version.';
$string['upgradeinprogress'] = 'Upgrading to %ver...';
$string['upgradeerror'] = 'An error occurred while processing parameters:';
$string['upgradeerrordata'] = 'Could not load data for library %lib.';
$string['upgradeerrorscript'] = 'Could not load upgrades script for %lib.';
$string['upgradeerrorcontent'] = 'Could not upgrade content %id:';
$string['upgradeerrorparamsbroken'] = 'Parameters are broken.';
$string['upgradedone'] = 'You have successfully upgraded {$a} content instance(s).';
$string['upgradereturn'] = 'Return';
$string['upgradenothingtodo'] = "There's no content instances to upgrade.";
$string['upgradebuttonlabel'] = 'Upgrade';
$string['upgradeinvalidtoken'] = 'Error: Invalid security token!';
$string['upgradelibrarymissing'] = 'Error: Your library is missing!';

// Results / report page.
$string['user'] = 'User';
$string['score'] = 'Score';
$string['maxscore'] = 'Maximum Score';
$string['finished'] = 'Finished';
$string['loadingdata'] = 'Loading data.';
$string['ajaxfailed'] = 'Failed to load data.';
$string['nodata'] = "There's no data available that matches your criteria.";
$string['currentpage'] = 'Page $current of $total';
$string['nextpage'] = 'Next page';
$string['previouspage'] = 'Previous page';
$string['search'] = 'Search';
$string['empty'] = 'No results available';
$string['viewreportlabel'] = 'Report';
$string['dataviewreportlabel'] = 'View Answers';
$string['invalidxapiresult'] = 'No xAPI results were found for the given content and user id combination';
$string['reportnotsupported'] = 'Not supported';
$string['reportingscorelabel'] = 'Score:';
$string['reportingscaledscorelabel'] = 'Gradebook score:';
$string['reportingscoredelimiter'] = 'out of';
$string['reportingscaledscoredelimiter'] = ',';
$string['reportingquestionsremaininglabel'] = 'questions remaining to grade';
$string['reportsubmitgradelabel'] = 'Submit grade';
$string['noanswersubmitted'] = 'This user hasn\'t submitted an answer to the H5P yet';

// Editor.
$string['javascriptloading'] = 'Waiting for JavaScript...';
$string['action'] = 'Action';
$string['upload'] = 'Upload';
$string['create'] = 'Create';
$string['editor'] = 'Editor';

$string['invalidlibrary'] = 'Invalid library';
$string['nosuchlibrary'] = 'No such library';
$string['noparameters'] = 'No parameters';
$string['invalidparameters'] = 'Invalid Parameters';
$string['missingcontentuserdata'] = 'Error: Could not find content user data';

$string['maximumgrade'] = 'Maximum grade';
$string['maximumgradeerror'] = 'Please enter a valid positive integer as the max points available for this activity';

// Capabilities.
$string['hvp:view'] = 'See and interact with H5P activities';
$string['hvp:addinstance'] = 'Create new H5P activites';
$string['hvp:manage'] = 'Edit existing H5P activites';
$string['hvp:getexport'] = 'Download .h5p file when \'controlled by permission\' option is set';
$string['hvp:getembedcode'] = 'View H5P embed code when \'controlled by permission\' option is set';
$string['hvp:saveresults'] = 'Save the results from completed H5P activities';
$string['hvp:savecontentuserdata'] = 'Save the users\'s progress for H5P activities';
$string['hvp:viewresults'] = 'View own results for completed H5P activities';
$string['hvp:viewallresults'] = 'View all results for completed H5P activites';
$string['hvp:restrictlibraries'] = 'Restrict access to certain H5P content types';
$string['hvp:userestrictedlibraries'] = 'Use restricted H5P content types';
$string['hvp:updatelibraries'] = 'Install new H5P content types or update existing ones';
$string['hvp:getcachedassets'] = 'Required for viewing H5P activities';
$string['hvp:installrecommendedh5plibraries'] = 'Install new safe H5P content types recommended by H5P.org';

// Capabilities error messages.
$string['nopermissiontoupgrade'] = 'You do not have permission to upgrade libraries.';
$string['nopermissiontorestrict'] = 'You do not have permission to restrict libraries.';
$string['nopermissiontosavecontentuserdata'] = 'You do not have permission to save content user data.';
$string['nopermissiontosaveresult'] = 'You do not have permission to save result for this content.';
$string['nopermissiontoviewresult'] = 'You do not have permission to view results for this content.';
$string['nopermissiontouploadfiles'] = 'You do not have permission to upload files here.';
$string['nopermissiontouploadcontent'] = 'You do not have permission to upload content here.';
$string['nopermissiontoviewcontenttypes'] = 'You do not have permission to view the content types.';

// Editor translations.
$string['noziparchive'] = 'Your PHP version does not support ZipArchive.';
$string['noextension'] = 'The file you uploaded is not a valid HTML5 Package (It does not have the .h5p file extension)';
$string['nounzip'] = 'The file you uploaded is not a valid HTML5 Package (We are unable to unzip it)';
$string['noparse'] = 'Could not parse the main h5p.json file';
$string['nojson'] = 'The main h5p.json file is not valid';
$string['invalidcontentfolder'] = 'Invalid content folder';
$string['nocontent'] = 'Could not find or parse the content.json file';
$string['librarydirectoryerror'] = 'Library directory name must match machineName or machineName-majorVersion.minorVersion (from library.json). (Directory: {$a->%directoryName} , machineName: {$a->%machineName}, majorVersion: {$a->%majorVersion}, minorVersion: {$a->%minorVersion})';
$string['missingcontentfolder'] = 'A valid content folder is missing';
$string['invalidmainjson'] = 'A valid main h5p.json file is missing';
$string['missinglibrary'] = 'Missing required library {$a->@library}';
$string['missinguploadpermissions'] = "Note that the libraries may exist in the file you uploaded, but you're not allowed to upload new libraries. Contact the site administrator about this.";
$string['invalidlibraryname'] = 'Invalid library name: {$a->%name}';
$string['missinglibraryjson'] = 'Could not find library.json file with valid json format for library {$a->%name}';
$string['invalidsemanticsjson'] = 'Invalid semantics.json file has been included in the library {$a->%name}';
$string['invalidlanguagefile'] = 'Invalid language file {$a->%file} in library {$a->%library}';
$string['invalidlanguagefile2'] = 'Invalid language file {$a->%languageFile} has been included in the library {$a->%name}';
$string['missinglibraryfile'] = 'The file "{$a->%file}" is missing from library: "{$a->%name}"';
$string['missingcoreversion'] = 'The system was unable to install the <em>{$a->%component}</em> component from the package, it requires a newer version of the H5P plugin. This site is currently running version {$a->%current}, whereas the required version is {$a->%required} or higher. You should consider upgrading and then try again.';
$string['invalidlibrarydataboolean'] = 'Invalid data provided for {$a->%property} in {$a->%library}. Boolean expected.';
$string['invalidlibrarydata'] = 'Invalid data provided for {$a->%property} in {$a->%library}';
$string['invalidlibraryproperty'] = 'Can\'t read the property {$a->%property} in {$a->%library}';
$string['missinglibraryproperty'] = 'The required property {$a->%property} is missing from {$a->%library}';
$string['invalidlibraryoption'] = 'Illegal option {$a->%option} in {$a->%library}';
$string['addedandupdatedss'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old one.';
$string['addedandupdatedsp'] = 'Added {$a->%new} new H5P library and updated {$a->%old} old ones.';
$string['addedandupdatedps'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old one.';
$string['addedandupdatedpp'] = 'Added {$a->%new} new H5P libraries and updated {$a->%old} old ones.';
$string['addednewlibrary'] = 'Added {$a->%new} new H5P library.';
$string['addednewlibraries'] = 'Added {$a->%new} new H5P libraries.';
$string['updatedlibrary'] = 'Updated {$a->%old} H5P library.';
$string['updatedlibraries'] = 'Updated {$a->%old} H5P libraries.';
$string['missingdependency'] = 'Missing dependency {$a->@dep} required by {$a->@lib}.';
$string['invalidstring'] = 'Provided string is not valid according to regexp in semantics. (value: \"{$a->%value}\", regexp: \"{$a->%regexp}\")';
$string['invalidfile'] = 'File "{$a->%filename}" not allowed. Only files with the following extensions are allowed: {$a->%files-allowed}.';
$string['invalidmultiselectoption'] = 'Invalid selected option in multi-select.';
$string['invalidselectoption'] = 'Invalid selected option in select.';
$string['invalidsemanticstype'] = 'H5P internal error: unknown content type "{$a->@type}" in semantics. Removing content!';
$string['unabletocreatedir'] = 'Unable to create directory.';
$string['unabletogetfieldtype'] = 'Unable to get field type.';
$string['filetypenotallowed'] = 'File type isn\'t allowed.';
$string['invalidfieldtype'] = 'Invalid field type.';
$string['invalidimageformat'] = 'Invalid image file format. Use jpg, png or gif.';
$string['filenotimage'] = 'File is not an image.';
$string['invalidaudioformat'] = 'Invalid audio file format. Use mp3 or wav.';
$string['invalidvideoformat'] = 'Invalid video file format. Use mp4 or webm.';
$string['couldnotsave'] = 'Could not save file.';
$string['couldnotcopy'] = 'Could not copy file.';
$string['librarynotselected'] = 'You must select a content type.';

// Welcome messages.
$string['welcomeheader'] = 'Welcome to the world of H5P!';
$string['welcomegettingstarted'] = 'To get started with H5P and Moodle take a look at our <a {$a->moodle_tutorial}>tutorial</a> and check out the <a {$a->example_content}>example content</a> at H5P.org for inspiration.';
$string['welcomecommunity'] = 'We hope you will enjoy H5P and get engaged in our growing community through our <a {$a->forums}>forums</a>.';
$string['welcomecontactus'] = 'If you have any feedback, don\'t hesitate to <a {$a}>contact us</a>. We take feedback very seriously and are dedicated to making H5P better every day!';
$string['missingmbstring'] = 'The mbstring PHP extension is not loaded. H5P need this to function properly';
$string['wrongversion'] = 'The version of the H5P library {$a->%machineName} used in this content is not valid. Content contains {$a->%contentLibrary}, but it should be {$a->%semanticsLibrary}.';
$string['invalidlibrarynamed'] = 'The H5P library {$a->%library} used in the content is not valid';

// Setup errors.
$string['oldphpversion'] = 'Your PHP version is outdated. H5P requires version 5.2 to function properly. Version 5.6 or later is recommended.';
$string['maxuploadsizetoosmall'] = 'Your PHP max upload size is quite small. With your current setup, you may not upload files larger than {$a->%number} MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB.';
$string['maxpostsizetoosmall'] = 'Your PHP max post size is quite small. With your current setup, you may not upload files larger than {$a->%number} MB. This might be a problem when trying to upload H5Ps, images and videos. Please consider to increase it to more than 5MB';
$string['sslnotenabled'] = 'Your server does not have SSL enabled. SSL should be enabled to ensure a secure connection with the H5P hub.';
$string['hubcommunicationdisabled'] = 'H5P hub communication has been disabled because one or more H5P requirements failed.';
$string['reviseserversetupandretry'] = 'When you have revised your server setup you may re-enable H5P hub communication in H5P Settings.';
$string['disablehubconfirmationmsg'] = 'Do you still want to enable the hub ?';
$string['nowriteaccess'] = 'A problem with the server write access was detected. Please make sure that your server can write to your data folder.';
$string['uploadsizelargerthanpostsize'] = 'Your PHP max upload size is bigger than your max post size. This is known to cause issues in some installations.';
$string['sitecouldnotberegistered'] = 'Site could not be registered with the hub. Please contact your site administrator.';
$string['hubisdisableduploadlibraries'] = 'The H5P Hub has been disabled until this problem can be resolved. You may still upload libraries through the "H5P Libraries" page.';
$string['successfullyregisteredwithhub'] = 'Your site was successfully registered with the H5P Hub.';
$string['sitekeyregistered'] = 'You have been provided a unique key that identifies you with the Hub when receiving new updates. The key is available for viewing in the "H5P Settings" page.';

// Ajax messages.
$string['hubisdisabled'] = 'The hub is disabled. You can re-enable it in the H5P settings.';
$string['invalidh5ppost'] = 'Could not get posted H5P.';
$string['filenotfoundonserver'] = 'File not found on server. Check file upload settings.';
$string['failedtodownloadh5p'] = 'Failed to download the requested H5P.';
$string['postmessagerequired'] = 'A post message is required to access the given endpoint';

// Licensing.
$string['copyrightinfo'] = 'Copyright information';
$string['years'] = 'Year(s)';
$string['undisclosed'] = 'Undisclosed';
$string['attribution'] = 'Attribution 4.0';
$string['attributionsa'] = 'Attribution-ShareAlike 4.0';
$string['attributionnd'] = 'Attribution-NoDerivs 4.0';
$string['attributionnc'] = 'Attribution-NonCommercial 4.0';
$string['attributionncsa'] = 'Attribution-NonCommercial-ShareAlike 4.0';
$string['attributionncnd'] = 'Attribution-NonCommercial-NoDerivs 4.0';
$string['gpl'] = 'General Public License v3';
$string['pd'] = 'Public Domain';
$string['pddl'] = 'Public Domain Dedication and Licence';
$string['pdm'] = 'Public Domain Mark';
$string['copyrightstring'] = 'Copyright';
$string['by'] = 'by';
$string['showmore'] = 'Show more';
$string['showless'] = 'Show less';
$string['sublevel'] = 'Sublevel';
$string['noversionattribution'] = 'Attribution';
$string['noversionattributionsa'] = 'Attribution-ShareAlike';
$string['noversionattributionnd'] = 'Attribution-NoDerivs';
$string['noversionattributionnc'] = 'Attribution-NonCommercial';
$string['noversionattributionncsa'] = 'Attribution-NonCommercial-ShareAlike';
$string['noversionattributionncnd'] = 'Attribution-NonCommercial-NoDerivs';
$string['licenseCC40'] = '4.0 International';
$string['licenseCC30'] = '3.0 Unported';
$string['licenseCC25'] = '2.5 Generic';
$string['licenseCC20'] = '2.0 Generic';
$string['licenseCC10'] = '1.0 Generic';
$string['licenseGPL'] = 'General Public License';
$string['licenseV3'] = 'Version 3';
$string['licenseV2'] = 'Version 2';
$string['licenseV1'] = 'Version 1';
$string['licenseCC010'] = 'CC0 1.0 Universal (CC0 1.0) Public Domain Dedication';
$string['licenseCC010U'] = 'CC0 1.0 Universal';
$string['licenseversion'] = 'License Version';

// Embed.
$string['embedloginfailed'] = 'You do not have access to this content. Try logging in.';

// Privacy.
$string['privacy:metadata:core_files'] = 'The H5P activity stores files which have been uploaded as part of H5P content.';
$string['privacy:metadata:core_grades'] = 'The H5P activity stores grades of users that have answered H5P content.';

$string['privacy:metadata:hvp_content_user_data'] = 'Describes the current state that content is in for a user. Used to restore content to a previous state.';
$string['privacy:metadata:hvp_content_user_data:id'] = 'The ID of the content user data relationship.';
$string['privacy:metadata:hvp_content_user_data:user_id'] = 'The ID of the user that the data belongs to.';
$string['privacy:metadata:hvp_content_user_data:hvp_id'] = 'The ID of the H5P content that the data belongs to.';
$string['privacy:metadata:hvp_content_user_data:sub_content_id'] = 'Sub-content of H5P, 0 if this is not sub-content.';
$string['privacy:metadata:hvp_content_user_data:data_id'] = 'Data type identifier.';
$string['privacy:metadata:hvp_content_user_data:data'] = 'User data that was stored.';
$string['privacy:metadata:hvp_content_user_data:preloaded'] = 'Flag determining if data should be pre-loaded into content.';
$string['privacy:metadata:hvp_content_user_data:delete_on_content_change'] = 'Flag determining if data should be deleted when content changes.';

$string['privacy:metadata:hvp_events'] = 'Keeps track of logged H5P events.';
$string['privacy:metadata:hvp_events:id'] = 'The unique ID of the event.';
$string['privacy:metadata:hvp_events:user_id'] = 'The ID of the user that performed the action.';
$string['privacy:metadata:hvp_events:created_at'] = 'The time that the event was created.';
$string['privacy:metadata:hvp_events:type'] = 'The type of event.';
$string['privacy:metadata:hvp_events:sub_type'] = 'The sub-type of event, or action of event.';
$string['privacy:metadata:hvp_events:content_id'] = 'The content ID that the action was performed on, 0 if new or no content.';
$string['privacy:metadata:hvp_events:content_title'] = 'Title of the content.';
$string['privacy:metadata:hvp_events:library_name'] = 'The library the event affected.';
$string['privacy:metadata:hvp_events:library_version'] = 'The library version the event affected.';

$string['privacy:metadata:hvp_xapi_results'] = 'Stores xAPI events in H5P content.';
$string['privacy:metadata:hvp_xapi_results:id'] = 'The unique ID of the xAPI event.';
$string['privacy:metadata:hvp_xapi_results:content_id'] = 'The ID of the content the event was performed on.';
$string['privacy:metadata:hvp_xapi_results:user_id'] = 'The ID of the user that performed the action.';
$string['privacy:metadata:hvp_xapi_results:parent_id'] = 'The ID of the parent of the content that this event was performed on. Null if it has no parent.';
$string['privacy:metadata:hvp_xapi_results:interaction_type'] = 'The type of interaction.';
$string['privacy:metadata:hvp_xapi_results:description'] = 'The description, task or question of the content that action was performed on.';
$string['privacy:metadata:hvp_xapi_results:correct_responses_pattern'] = 'The correct answer pattern.';
$string['privacy:metadata:hvp_xapi_results:response'] = 'The response the user sent in.';
$string['privacy:metadata:hvp_xapi_results:additionals'] = 'Additional information that the H5P can send in.';
$string['privacy:metadata:hvp_xapi_results:raw_score'] = 'Achieved score for the event.';
$string['privacy:metadata:hvp_xapi_results:max_score'] = 'Max achievable score for the event.';
