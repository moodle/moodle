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

$string['modulename'] = 'תוכן אינטראקטיבי';
$string['modulename_help'] = 'פעילות H5P מאפשר יצירה של תוכן דינמי ואינטראקטיבי כגון: סרטוני וידאו משובצים בשאלות מסוגים שונים, מצגות הכוללות שאלות מגוונות, משחקוני למידה, שאלות גרירה למיניהן, רב־בררה, ועוד סוגים רבים...
בנוסף, ניתן ליבא וליצא חבילות לומדה לשימוש חוזר בתכנים קיימים.  הפעילות מבצעת מקב למידה מבוסס על תקן xAPI ומדווחת ציונים לגליון הציונים של ה Moodle.
ניתן להעלות קבצי h5p ממאגר עצמי הלמידה (OER) של אתר h5p.org';
$string['modulename_link'] = 'https://h5p.org/moodle-more-help';
$string['modulenameplural'] = 'תוכן אינטראקטיבי';
$string['pluginadministration'] = 'H5P';
$string['pluginname'] = 'H5P';
$string['intro'] = 'הנחיה לתלמידים';
$string['h5pfile'] = 'קובץ H5P';
$string['fullscreen'] = 'מסך־מלא';
$string['disablefullscreen'] = 'תצוגה רגילה';
$string['download'] = 'הורדה';
$string['copyright'] = 'זכויות יוצרים';
$string['embed'] = 'שיבוץ';
$string['showadvanced'] = 'תצוגת תכונות מתקדמות';
$string['hideadvanced'] = 'הסתרת תכונות מתקדמות';
$string['resizescript'] = 'Include this script on your website if you want dynamic sizing of the embedded content:';
$string['size'] = 'גודל';
$string['close'] = 'סגירה';
$string['title'] = 'כותרת';
$string['author'] = 'יוצר/ת';
$string['year'] = 'שנה';
$string['source'] = 'מקור';
$string['license'] = 'רשיון';
$string['thumbnail'] = 'תמונה ממוזערת';
$string['nocopyright'] = 'לא קיים מידע אודות זכויות יוצרים עבור תוכן זה.';
$string['downloadtitle'] = 'הורדת תוכן זה כקובץ H5P.';
$string['copyrighttitle'] = 'צפיה בזכויות היוצרים של תוכן זה.';
$string['embedtitle'] = 'תצוגת קוד השיבוץ של תוכן זה.';
$string['h5ptitle'] = 'בקרו באתר H5P.org לחיפוש ואיחזור של תוכן נוסף.';
$string['contentchanged'] = 'התוכן של רכיב זה עודכן מאז השימוש האחרון שלכם בפעילות זו.';
$string['startingover'] = "הפעילות תוצג מההתחלה.";
$string['confirmdialogheader'] = 'אישור פעולה';
$string['confirmdialogbody'] = 'יש לאשר פעולה זו. שימו לב! לא ניתן יהיה לחזור למצב הנוכחי.';
$string['cancellabel'] = 'ביטול';
$string['confirmlabel'] = 'אישור';
$string['noh5ps'] = 'לא קיים תוכן H5P אינטראקטיבי בקורס זה.';

$string['lookforupdates'] = 'Look for H5P updates';
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

$string['sendusagestatistics'] = 'Contribute usage statistics';
$string['sendusagestatistics_help'] = 'Usage statistics numbers will automatically be reported to help the developers better understand how H5P is used and to determine potential areas of improvement. Read more about which <a {$a}>data is collected on h5p.org</a>.';
$string['enablesavecontentstate'] = 'Save content state';
$string['enablesavecontentstate_help'] = 'Automatically save the current state of interactive content for each user. This means that the user may pick up where he left off.';
$string['contentstatefrequency'] = 'Save content state frequency';
$string['contentstatefrequency_help'] = 'In seconds, how often do you wish the user to auto save their progress. Increase this number if you\'re having issues with many ajax requests';

// Admin menu.
$string['settings'] = 'H5P Settings';
$string['libraries'] = 'H5P Libraries';

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
$string['user'] = 'משתמש';
$string['score'] = 'ניקוד';
$string['maxscore'] = 'ניקוד מירבי';
$string['finished'] = 'הסתיים';
$string['loadingdata'] = 'מאחזר את התוכן.';
$string['ajaxfailed'] = 'התרחשה שגיאה, התוכן לא זמין.';
$string['nodata'] = "לא קיימים תכנים העונים על בקשת החיפוש שלך.";
$string['currentpage'] = 'עמוד $current מתוך $total';
$string['nextpage'] = 'לעמוד הבא';
$string['previouspage'] = 'לעמוד הקודם';
$string['search'] = 'חיפוש';
$string['empty'] = 'לא נמצאו תכנים';
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
$string['javascriptloading'] = 'מחכים ל JavaScript...';
$string['action'] = 'פעולה';
$string['upload'] = 'העלאה';
$string['create'] = 'יצירה';
$string['editor'] = 'עורך';

$string['invalidlibrary'] = 'ספריה לא תקינה';
$string['nosuchlibrary'] = 'לא קיימת ספריה כזו';
$string['noparameters'] = 'חסרים משתני אתחול';
$string['invalidparameters'] = 'משתני אתחול לא תקינים';
$string['missingcontentuserdata'] = 'התרחשה תקלה: לא זמינים נתוני השימוש של התלמיד עבור פעילות זו';

$string['maximumgrade'] = 'Maximum grade';
$string['maximumgradeerror'] = 'Please enter a valid positive integer as the max points available for this activity';

// Capabilities.
$string['hvp:view'] = 'See and interact with H5P activities';
$string['hvp:addinstance'] = 'הוספת פעילות H5P חדשה';
$string['hvp:manage'] = 'Edit existing H5P activites';
$string['hvp:getexport'] = 'יצוא תוכן פעילות H5P מתוך הקורס';
$string['hvp:getembedcode'] = 'View H5P embed code when \'controlled by permission\' option is set';
$string['hvp:saveresults'] = 'שמירת תוצאות שימוש ברכיב H5P';
$string['hvp:savecontentuserdata'] = 'שמירת נתוני משתמש מתוך פעילות H5P';
$string['hvp:viewresults'] = 'צפיה בתוצאות שימוש ברכיב H5P';
$string['hvp:viewallresults'] = 'View result for all users in course';
$string['hvp:restrictlibraries'] = 'הגבלת גישה לספריית H5P';
$string['hvp:userestrictedlibraries'] = 'שימוש בספריות H5P שמורות';
$string['hvp:updatelibraries'] = 'עדכון גרסה של ספריית H5P';
$string['hvp:getcachedassets'] = 'אחזור משאבי מטמון של רכיב H5P';
$string['hvp:installrecommendedh5plibraries'] = 'Install new safe H5P content types recommended by H5P.org';

// Capabilities error messages.
$string['nopermissiontoupgrade'] = 'You do not have permission to upgrade libraries.';
$string['nopermissiontorestrict'] = 'You do not have permission to restrict libraries.';
$string['nopermissiontosavecontentuserdata'] = 'You do not have permission to save content user data.';
$string['nopermissiontosaveresult'] = 'You do not have permission to save result for this content.';
$string['nopermissiontoviewresult'] = 'You do not have permission to view results for this content.';

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
$string['invalidsemantics'] = 'Library used in content is not a valid library according to semantics';
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
$string['welcomeheader'] = 'ברוכים הבאים לעולם של H5P!';
$string['welcomegettingstarted'] = 'כדי להתחיל בהכרת רכיב H5P במערכת Moodle ניתן לבחור ב<a {$a->moodle_tutorial}>מדריך</a> וגם <a {$a->example_content}>תוכן לדוגמה</a> באתר H5P.org לקבלת השראה.';
$string['welcomecommunity'] = 'אנו מקווים שתהנו פעילות ברכיב H5P ושתמצאו עניין וזמן להצטרף לקהילת המשתמשים ויצרני התוכן העולמית שלנו בעזרת הקישורים הבאים  <a {$a->forums}>פורומים, קבוצות דיון</a>';
$string['welcomecontactus'] = 'נשמח לקבל כל משוב <a {$a}>יצירת קשר</a>. אנו מתייחסים למשוב באופן מאוד רציני ומחוייבים ליצירת חווית שימוש איכותית ומשופרת ברכיב H5P !';
$string['invalidlibrarynamed'] = 'The H5P library {$a->%library} used in the content is not valid';

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
