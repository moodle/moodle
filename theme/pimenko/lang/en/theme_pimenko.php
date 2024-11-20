<?php
// This file is part of the Pimenko theme for Moodle
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
 * Theme Pimenko lang file.
 *
 * @package    theme_pimenko
 * @copyright  Pimenko 2020
 * @author     Sylvain Revenu - Pimenko 2020 <contact@pimenko.com> <pimenko.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
// This is the EN Lang package.
defined('MOODLE_INTERNAL') || die();

// A description shown in the admin theme selector.
$string['choosereadme'] = 'Theme pimenko is a child theme of Boost. It adds some new features';
// The name of our plugin.
$string['pluginname'] = 'Pimenko';
// The name of the second tab in the theme settings.
$string['advancedsettings'] = 'Advanced settings';
// The brand color setting.
$string['brandcolor'] = 'Brand color';
// The brand color setting description.
$string['brandcolor_desc'] = 'Define a brand color.';
// The button brand color setting.
$string['brandcolorbutton'] = 'Button brand color';
// The button brand color setting description.
$string['brandcolorbuttondesc'] = 'Define a brand color for button.';
// The button brand color setting.
$string['brandcolortextbutton'] = 'Button text color';
// The button brand color setting description.
$string['brandcolortextbuttondesc'] = 'Define a text color for button.';
// Name of the settings pages.
$string['configtitle'] = 'Pimenko settings';
// Name of the first settings tab.
$string['generalsettings'] = 'General settings';
// Preset files setting.
$string['presetfiles'] = 'Additional theme preset files';
// Preset files help text.
$string['presetfiles_desc'] =
    'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files, and see the <a href=https://moodle.net/boost>Presets repository</a> for presets that others have shared.';
// Preset setting.
$string['preset'] = 'Theme preset';
// Preset help text.
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
// Raw SCSS setting.
$string['rawscss'] = 'Raw SCSS';
// Raw SCSS setting help text.
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
// Raw initial SCSS setting.
$string['rawscsspre'] = 'Raw initial SCSS';
// Raw initial SCSS setting help text.
$string['rawscsspre_desc'] =
    'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Right';

// Favicon *******************************************************.

$string['favicon'] = 'Favicon';
$string['favicondesc'] = 'Add a favicon';

// Site logo *******************************************************.

$string['sitelogo'] = 'Site logo';
$string['sitelogodesc'] = 'Add a logo for ur site';

// Header picture *******************************************************.

$string['navbarpicture'] = 'Navbar background';
$string['navbarpicturedesc'] = 'Add a background image to the navigation bar, then you will have to adapt the style to your needs. You can target the "withnavbarpicture" element';

// Navbar *******************************************************.
$string['navbarsettings'] = 'Navbar';
$string['navbarcolor'] = 'Navbar color';
$string['navbarcolordesc'] = 'Add a background color to your navbar';
$string['navbartextcolor'] = 'Navbar text color';
$string['navbartextcolordesc'] = 'Add a text color to your navbar';
$string['hoovernavbarcolor'] = 'Navbar hoover link color';
$string['hoovernavbarcolordesc'] = 'Add a hoover text color to your navbar';

// Profile page.
$string['profile:joinedon'] = 'Joined on ';
$string['profile:lastaccess'] = 'Last access ';
$string['profile:basicinfo'] = 'Basic Information';
$string['profile:contactinfo'] = 'Contact Information';

// Login *******************************************************.
$string['settings:loginsettings:vanillalogintemplate'] = 'Moodle login page';
$string['settings:loginsettings:vanillalogintemplatedesc'] = 'Use the classic authentication page in the official Moodle "Boost" theme';
$string['loginsettings'] = 'Login Page';
$string['loginsettingsheading'] = 'Customize the login page';
$string['logindesc'] = 'Customize the login page with adding an image background and texts above and below the login box.';
$string['loginbgimage'] = 'Background image';
$string['loginbgimagedesc'] = 'Add a background image to the full size page.';
$string['loginbgstyle'] = 'Login background style';
$string['loginbgstyledesc'] = 'Select the style for the uploaded image.';
$string['loginbgopacity'] = 'Login page header, navbar, login box and footer background opacity when there is a background image';
$string['loginbgopacitydesc'] =
    'Login background opacity for the header, navbar, login box and footer when there is a background image.';
$string['logintextboxtop'] = 'Top text box';
$string['logintextboxtopdesc'] = 'Add a custom text above the login box.';
$string['logintextboxbottom'] = 'Bottom text box';
$string['logintextboxbottomdesc'] = 'Add a custom text below the login box.';

$string['stylecover'] = 'Cover';
$string['stylestretch'] = 'Stretch';

$string['hide'] = 'Hide';
$string['show'] = 'Show';

// Footer *******************************************************.
$string['footersettings'] = 'Footer';
$string['settings:footer:footercolumn'] = 'Footer column {$a}';
$string['settings:footer:footerheading'] = 'Footer heading {$a}';
$string['settings:footer:footertext'] = 'Footer text {$a}';
$string['settings:footer:footerheadingdesc'] = 'h3 header for column';
$string['settings:footer:footertextdesc'] = 'Add content for the footer.';
$string['settings:footer:footercolumndesc'] = '';
$string['footercolor'] = 'Footer color';
$string['footercolordesc'] = 'Add a background color to your footer';
$string['footertextcolor'] = 'Footer text color';
$string['footertextcolordesc'] = 'Add a text color to your footer';
$string['hooverfootercolor'] = 'Footer hoover link color';
$string['hooverfootercolordesc'] = 'Add a hoover text color to your footer';

// Completion.
$string['completion-alt-manual-n'] = 'Not complete';
$string['completion-alt-manual-n-override'] = 'Not complete';
$string['completion-alt-manual-y'] = 'Not complete';
$string['completion-alt-manual-y-override'] = 'Not complete';
$string['completion-alt-auto-n'] = 'Not complete';
$string['completion-alt-auto-n-override'] = 'Not complete';
$string['completion-alt-auto-y'] = 'Not complete';
$string['completion-alt-auto-y-override'] = 'Not complete';
$string['completion-tooltip-manual-n'] = 'Click to mark as complete';
$string['completion-tooltip-manual-n-override'] = 'Click to mark as complete';
$string['completion-tooltip-manual-y'] = 'Click to mark as not complete';
$string['completion-tooltip-manual-y-override'] = 'Click to mark as not complete';
$string['completion-tooltip-auto-n'] = 'Automatic completion';
$string['completion-tooltip-auto-n-override'] = 'Automatic completion';
$string['completion-tooltip-auto-y'] = 'Automatic completion';
$string['completion-tooltip-auto-y-override'] = 'Automatic completion';
$string['completion-tooltip-auto-pass'] = 'Automatic completion';
$string['completion-tooltip-auto-enabled'] = 'The system marks this item complete';
$string['completion-tooltip-manual-enabled'] = 'Students can manually mark this item complete';
$string['completion-alt-auto-enabled'] = 'The system marks this item complete';
$string['completion-alt-manual-enabled'] = 'Students can manually mark this item complete';

// Catalog.
$string['viewcat'] = 'View cat';
$string['viewcourse'] = 'View course';
$string['nextmod'] = 'Next Activity';

// Block Regions.
$string['frontpage'] = 'Frontpage Block Settings';
$string['settings:regions:frontpageblocksettingscription'] = '';
$string['settings:regions:frontpageblocksettingscriptiondesc'] =
    'On this page you can determine the composition of the homepage, which can be divided into 8 lines. For each line, you can determine the color and if it should be composed of one or more columns. Important : after making the changes, go to the homepage of your site to add content using blocks. You can find the homepage here : <a href= ' .
    new moodle_url($CFG->wwwroot . '/?redirect=0') . '>Homepage</a>.';
$string['settings:regions:blockrow'] = 'Block Region Row {$a}';
$string['settings:regions:blockrowdesc'] = 'Add / set layout for block region row on front page.';

// Block Regions colors.
$string['settings:regions:blockregionrowbackgroundcolor'] = 'Row {$a} color';
$string['settings:regions:blockregionrowbackgroundcolordesc'] = 'Add / set a color for the block region row on front page.';
$string['settings:regions:blockregionrowtextcolor'] = 'Row {$a} text color';
$string['settings:regions:blockregionrowtextcolordesc'] = 'Add / set a text color for the block region row on front page.';
$string['settings:regions:blockregionrowlinkcolor'] = 'Row {$a} link color';
$string['settings:regions:blockregionrowlinkcolordesc'] = 'Add / set a link color for the block region row on front page.';
$string['settings:regions:blockregionrowlinkhovercolor'] = 'Row {$a} link hover color';
$string['settings:regions:blockregionrowlinkhovercolordesc'] =
    'Add / set a link hover color for the block region row on front page.';

// Slide.
$string['settings:frontslider:enablecarousel'] = 'Enable carousel';
$string['settings:frontslider:enablecarouseldesc'] = 'Allows to display or not the carousel';
$string['settings:frontslider:slideimagenr'] = 'Number of slides';
$string['settings:frontslider:slideimagenrdesc'] = 'Define the number of slides you will use.
                                                <br>Note: you will have to save this option to display new settings field.';
$string['settings:frontslider:slideimage'] = 'Slide image {$a}';
$string['settings:frontslider:slideimagedesc'] = 'Set a picture for this slide';
$string['settings:frontslider:slidecaption'] = 'Slide caption {$a}';
$string['settings:frontslider:slidecaptiondesc'] = 'Set a text for this slide';

// Course card frontpage.
$string['settings:frontcoursecard:showcustomfields'] = 'show customs fields';
$string['settings:frontcoursecard:showcustomfieldsdesc'] = 'Show custom field in frontpage page course card';
$string['settings:frontcoursecard:showcontacts'] = 'Show contacts';
$string['settings:frontcoursecard:showcontactsdesc'] = 'Show contact in frontpage page course card';
$string['settings:frontcoursecard:showstartdate'] = 'Show start date';
$string['settings:frontcoursecard:showstartdatedesc'] = 'Show start date in frontpage page course card';

// Fonts.
$string['settings:font:googlefont'] = 'Google font';
$string['settings:font:googlefontdesc'] = 'Please refer to the page: https://fonts.google.com/ to find your typography';

// Frontpage Block Regions name.
$string['region-theme-front-a'] = 'Pimenko front-a';
$string['region-theme-front-b'] = 'Pimenko front-b';
$string['region-theme-front-c'] = 'Pimenko front-c';
$string['region-theme-front-d'] = 'Pimenko front-d';
$string['region-theme-front-e'] = 'Pimenko front-e';
$string['region-theme-front-f'] = 'Pimenko front-f';
$string['region-theme-front-g'] = 'Pimenko front-g';
$string['region-theme-front-h'] = 'Pimenko front-h';
$string['region-theme-front-i'] = 'Pimenko front-i';
$string['region-theme-front-j'] = 'Pimenko front-j';
$string['region-theme-front-k'] = 'Pimenko front-k';
$string['region-theme-front-l'] = 'Pimenko front-l';
$string['region-theme-front-m'] = 'Pimenko front-m';
$string['region-theme-front-n'] = 'Pimenko front-n';
$string['region-theme-front-o'] = 'Pimenko front-o';
$string['region-theme-front-p'] = 'Pimenko front-p';
$string['region-theme-front-q'] = 'Pimenko front-q';
$string['region-theme-front-r'] = 'Pimenko front-r';
$string['region-theme-front-s'] = 'Pimenko front-s';
$string['region-theme-front-t'] = 'Pimenko front-t';
$string['region-theme-front-u'] = 'Pimenko front-u';
$string['region-side-post'] = 'Right';
$string['region-side-pre'] = 'Left';

// Trad enter button in courselist.
$string['entercourse'] = 'Enter';

// Moodle activity completion design enabling setting.
$string['moodleactivitycompletion'] = "Enable display of moodle activity completion";
$string['moodleactivitycompletion_desc'] = "This option enables the default display of the activity completion used by moodle";

// Show or not navigation in mod in course.
$string['showactivitynavigation'] = "Show previous/next navigation for mods";
$string['showactivitynavigation_desc'] = "This option allows you to show or hide the previous/next navigation in the activities";

// Setting show participant tab or no.
$string['showparticipantscourse'] = "Display the participant section in the secondary menu visible in the courses";
$string['showparticipantscourse_desc'] =
    "This option allows you to show or hide the 'Participants' section which is displayed by default in the secondary menu of the home page of a course.";

$string['totop'] = 'Go to top';

$string['listuserrole'] = 'List of user role';
$string['listuserrole_desc'] =
    'If the option showparticipantscourse is activated define the users who can see the participants tab';

$string['unaddableblocks'] = 'Unneeded blocks';
$string['unaddableblocks_desc'] =
    'The blocks specified are not needed when using this theme and will not be listed in the \'Add a block\' menu.';

$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] =
    'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';

$string['pimenkofeature'] = 'Pimenko features';

// Catalog enabling setting.
$string['catalogsettings'] = "Catalog";
$string['catalogsettings_desc'] = "Configuring the catalog page";
$string['customfieldfilter'] = "Enabling of the custom field filter";
$string['customfieldfilter_desc'] = "Enable filters on course custom fields in the catalog";
$string['enablecatalog'] = "Enabling of the catalog";
$string['enablecatalog_desc'] = "Enable the catalog";

$string['titlecatalog'] = "Title of the catalog";
$string['titlecatalog_desc'] = "Define catalog title";

$string['tagfilter'] = "Activation of the filter by catalog tags";
$string['tagfilter_desc'] = "This option allows you to add a filter by tags at the level of the course catalog";

$string['allcategories'] = "All categories";
$string['alltags'] = "All tags";
$string['labelcategory'] = "Filter by category";
$string['labelsearch'] = "Or search";
$string['placeholdersearch'] = "Key words...";
$string['search'] = "Search";
$string['close'] = "Close";

// Show the count of subscribers.
$string['showsubscriberscount'] = 'Show the count of subscribers';
$string['showsubscriberscount_desc'] = 'Allows to show the count of subscribers on the cards of courses';
$string['subscribers'] = 'subscribers';

$string['viewallhiddencourses'] = "Show hidden courses on the 'course/index.php' page for synopsis enrol";
$string['viewallhiddencourses_desc'] = "Enable / Disable hidden courses";

$string['catalogsummarymodal'] = "Display catalog course summary as a modal";
$string['catalogsummarymodal_desc'] = "Allows the display of the summary of the courses of the catalog in the form of a modal";

// Other feature heading.
$string['otherfeature'] = "Other features";
$string['otherfeature_desc'] = "Configuring other features of Pimenko theme";

// Slider heading settings.
$string['slidersettings'] = "Options for slider";
$string['slidersettings_desc'] = "Configure the carousel for the homepage";

// Front page content settings heading.
$string['frontpagecontentsettings'] = "Options for front page content ";
$string['frontpagecontentsettings_desc'] = "Configure the front page content row";

// Card settings heading.
$string['frontpagecardsettings'] = "Options for course cards on the home page";
$string['frontpagecardsettings_desc'] = "Configure the display of cards on the home page";

// Hide site name setting.
$string['hidesitename'] = "Hide site name";
$string['hidesitename_desc'] = "This option allow to hide the site name";
$string['cardlabelformat'] = "Former";
$string['cardlabeldate'] = "Start date";

$string['contactsettings'] = "Contact us";
$string['contactheading'] = "About us";
$string['contactus_content'] = "Pimenko is based in France, in Lyon.<br>
We are a committed player with NGOs, associations, training organizations and in the OpenSource community.<br>
Want a custom development? Advice tailored to your needs? Contact us : <a href='mailto:support@pimenko.com' target='_blank' style='font-weight: bold;'>support@pimenko.com</a>";
$string['contactus_button_text'] = "Send us a mail";

// Custom navbar menu.
// Custom navbar menu.
$string['removedprimarynavitems'] = "Menu tabs to delete";
$string['removedprimarynavitems_desc'] = "You can also fill in the identifiers of the menus to be removed from the navbar. Each identifier must be separated by a ',' example:<br>
<pre>home,myhome,courses,siteadmin</pre>";
$string['customnavbarmenu'] = "Customizing the menu in the navigation bar";
$string['customnavbarmenu_desc'] = "The following options will allow you to change the appearance of the menu in the navigation bar";
$string['custommenuitemslogin'] = 'Custom menu items on login';
$string['configcustommenuitemslogin'] = "A custom menu available when you're login may be configured here. Enter each menu item on a new line with format: menu text, a link URL (optional, not for a top menu item with sub-items), a tooltip title (optional) and a language code or comma-separated list of codes (optional, for displaying the line to users of the specified language only), separated by pipe characters. Lines starting with a hyphen will appear as menu items in the previous top level menu and ### makes a divider. For example:
<pre>
Courses
-All courses|/course/
-Course search|/course/search.php
-###
-FAQ|https://someurl.xyz/faq
-Preguntas más frecuentes|https://someurl.xyz/pmf||es
Mobile app|https://someurl.xyz/app|Download our app
</pre>";

// Cover image for course.
$string['coursecover'] = "Course cover";
$string['coursecoversettings'] = "Determine the settings for the image display (thumbnail) at the top of the course pages";
$string['coursecoversettings_desc'] = "It is possible to add an image in the header of the pages of a course. The options below allow you to choose how these images are displayed.";
$string['editcoverimage'] = "Change cover image";
$string['gradienttextcolor'] = "
If you specify a color, it will be used to change the color of the course title displayed in the header. For example, you can put the color code of white (#fff) to have a more visible course title on a dark photo.";
$string['gradienttextcolor_desc'] = "This option allows you to change the color of the text displayed on the banner";
$string['displaycoverallpage'] = "Display the image in the header of all course pages";
$string['displaycoverallpage_desc'] = "If this option is activated, the image will be displayed both on the course home page but also in the activities, resources or administration pages of the course";
$string['displayasthumbnail'] = "Display the image as a thumbnail or as the full width of the course header";
$string['displayasthumbnail_desc'] = "If this option is activated, the image will be displayed as a thumbnail, i.e. a rectangle of approximately . If this option is not enabled, the image will be displayed as a banner that takes up the entire width of the course header.";
// Options pour la vignette des cours.
$string['gradientcovercolor'] = "Apply a color to the image";
$string['gradientcovercolor_desc'] = "If you specify a color, it will be displayed over the image with transparency to give a color mask effect above the image";
// Options d'affichage pour le menu des cate.
$string['menuheadercateg']                    = 'My categories';
$string['menuheadercategdesc']                = 'Show a dropdown menu with user\'s categories';
$string['menuheadercateg:excludehidden']      = 'Enable excluding hidden categories';
$string['menuheadercateg:includehidden']      = 'Enable including hidden categories';
$string['menuheadercateg:disabled']           = 'Disable';
$string['filterbycustomfilter'] = 'Filter by custom field';
$string['yes'] = 'Yes';
$string['no'] = 'No';

$string['optionloginhtmlcontent'] = 'Options specific to the login page in landscape';
$string['optionloginhtmlcontentdesc'] = 'These settings are displayed when you activate the display of authentication in landscape mode';
$string['leftblockloginhtmlcontent'] = 'Left block for the login page';
$string['leftblockloginhtmlcontentdesc'] = 'Allows the creation of an HTML content block that will be displayed in the left part of the login page';
$string['rightblockloginhtmlcontent'] = 'Right block for the login page';
$string['rightblockloginhtmlcontentdesc'] = 'Allows the creation of an HTML content block that will be displayed in the left part of the login page';

// H5P.
$string['h5pcss'] = 'CSS file for H5P';
$string['h5pcss_desc'] = 'Add a CSS file that will only be loaded by H5P to change the design';

$string['loadmore'] = 'Load more';

// Add deprecated Moodle.
$string['clearfilters'] = 'Clear filters';
$string['courseimage'] = 'Course image';

$string['displaytitlecourseunderimage'] = 'Display course title under image';
$string['displaytitlecourseunderimage_desc'] = 'If enabled, the course title will be displayed under the image.';

$string['hidemanuelauth'] = 'Hide manual authentification';
$string['hidemanuelauth_desc'] = 'If enabled, manual authentification will be hidden. Use param ?adminpage=true if you want to display it.';
