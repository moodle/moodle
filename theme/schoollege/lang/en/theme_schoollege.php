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
 * @package   theme_schoollege
 * @copyright 2020 Chris Kenniburg
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

// The name of the second tab in the theme settings.
$string['advancedsettings'] = 'Colors';
$string['courseadminmenusettings'] = 'Navigation Menu Settings';
$string['loginsettings'] = 'Login Settings';
$string['brandingsettings'] = 'Branding Settings';
$string['iconnavheading'] = 'Icon Navigation';
$string['iconnavinfo'] = 'Dashboard Icon Navigation';
$string['iconnavinfo_desc'] = 'Create buttons with icons for use on the homepage. These appear at the top of the page on the Dashboard.';

$string['courseadmininfo'] = 'Teacher Course Management Links';
$string['courseadminreportinfo'] = 'Teacher Course Management Report Links';
$string['courseadmininfo_desc'] = 'Determine which links you would like to display to teachers in the Course Management navigation drawer.';
$string['headermenuinfo'] = 'Header Menu Items';
$string['headermenuinfo_desc'] = 'Determine which links you would like to display in the header area.';
$string['customlogininfo'] = 'Enhance the Login Page';
$string['customlogininfo_desc'] = 'Use the options below to enhance the login page for schoollege.';
$string['brandinginfo'] = 'Branding Options';
$string['brandinginfo_desc'] = 'Use the options below to provide branding for your site.';

// Misc strings
$string['nomycourses'] = 'You are not enrolled in any courses.';
$string['thiscourse'] = 'Course Sections';
$string['nothiscourse'] = 'We cannot identify any course sections or topics';

// Privacy.
$string['privacy:metadata'] = 'The schoollege theme does not store any individual user data.';

// The backgrounds tab name.
$string['backgrounds'] = 'Backgrounds';
// The brand colour setting.
$string['brandcolor'] = 'Brand colour';
// The brand colour setting description.
$string['brandcolor_desc'] = 'The accent colour.';
// A description shown in the admin theme selector.
$string['choosereadme'] = 'Theme schoollege is a child theme of Boost. It adds the ability to upload background schoollege.';
// Name of the settings pages.
$string['configtitle'] = 'Schoollege Theme';

// Show Header Images toggle.
$string['showheaderimages'] = 'Show Header Images';
$string['showheaderimages_desc'] = 'Allow schoollege to use custom images for the header area. You may still set a Course Tile image below but it will not be used as a header image within a course. This setting turns off using header images completely.';
// Show Header Overlay textures.
$string['headeroverlay'] = 'Header Texture';
$string['headeroverlay_desc'] = 'Recommended overlay: Brushed.png.  You can choose to change the header background texture.  We download transparent background images from here: <a href="https://www.transparenttextures.com/">https://www.transparenttextures.com/</a> ';
$string['footeroverlay'] = 'Footer Texture';
$string['footeroverlay_desc'] = 'Recommended overlay: Brushed.png.  You can choose to change the footer background texture.  We download transparent background images from here: <a href="https://www.transparenttextures.com/">https://www.transparenttextures.com/</a> ';
$string['dashboardtextbox'] = 'Dashboard Textbox';
$string['dashboardtextbox_desc'] = 'This is a custom textbox that is displayed on the site Dashboard.  Use it to welcome learners or provide instructions.';
// Background image for coursetiles.
$string['coursetilebg'] = 'Default Course Image';
$string['coursetilebg_desc'] = 'This is the default image for course tiles on the Dashboard and the default header image for all courses where a teacher has not uploaded an image into Course Settings.  Even with Header Images turned off you can use an image here to be the default Dashboard Course Tile image.  ';
// Background image for dashboard page.
$string['dashboardbackgroundimage'] = 'Dashboard page background image';
// Background image for dashboard page.
$string['dashboardbackgroundimage_desc'] = 'An image that will be stretched to fill the background of the dashboard page.';
// Background image for default page.
$string['defaultbackgroundimage'] = 'Site Page Header Image';
// Background image for default page.
$string['defaultbackgroundimage_desc'] = 'This image is used in the header area for all of the main Moodle pages: Dashboard, Profile, Site Home, all pages besides course pages.';
// Background image for front page.
$string['frontpagebackgroundimage'] = 'Front page background image';
// Background image for front page.
$string['frontpagebackgroundimage_desc'] = 'An image that will be stretched to fill the background of the front page.';
// Name of the first settings tab.
$string['generalsettings'] = 'General settings';
// Background image for incourse page.
$string['incoursebackgroundimage'] = 'Course page background image';
// Background image for incourse page.
$string['incoursebackgroundimage_desc'] = 'An image that will be stretched to fill the background of course pages.';
// Background image for login page.
$string['loginbackgroundimage'] = 'Login page background image';
// Background image for login page.
$string['loginbackgroundimage_desc'] = 'An image that will be stretched to fill the background of the login page.';
// The name of our plugin.
$string['pluginname'] = 'Schoollege';
// Preset files setting.
$string['presetfiles'] = 'Additional theme preset files';
// Preset files help text.
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a> for information on creating and sharing your own preset files. schoollege requires certain SCSS Preset variables.  You can view the schoollege theme presets here: <a href=https://github.com/dbnschools/moodle-theme_schoollege/tree/master/scss/preset target=_blank>schoollege Github Presets repository</a>. ';
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
$string['rawscsspre_desc'] = '// Top Navbar area</br>
$navbar-bg: #e3eaf5 ;</br>
$navbartextcolor: #333;</br>

// Top Header area</br>
$header-bg: #eef5f9;</br>

// Breadcrumbs in schoollege</br>
$breadcrumbblock: #607d8b;</br>
$breadcrumbblock-darken: #213561;</br>
$breadcrumbblock-highlight: #213561;</br>
$breadcrumbblock-highlight-darken: #607d8b;</br>
$breadcrumblinkcolor: $white;</br>
$breadcrumblinkcolor-hover: $white;</br>

//Sidebar icons menu</br>
$sidebar-bg: $body-bg;</br>
$sidebar-iconcolor: $white;</br>
$sidebar-ahover-bg: #1f77b2;</br>
$sidebar-aattention: #4caf50;</br>
$sidebar-borderright-color: $white;</br>

// Bottom Footer area</br>
$footer-bg: #e5ebef;</br>
$footerlinkcolor: #333;</br>
$footertextcolor: #333;</br>

//Used to style Easy Enrollment plugin</br>
$easyenrolltextcolor: $white;</br>
$easyenroll-bg: #4caf50;</br>

//Other Important Colors</br>
$card-bg: rgba(255, 255, 255, 0.98)!default;</br>
$body-bg: #213561;</br>
$primary:       #1968BE;</br>
$success:       $green;</br>
$info:          #4caf50;</br>
$warning:       $orange;</br>
$danger:        $red;</br>
$secondary:     $gray-400;</br>

// Tabs</br>
$nav-tabs-border-color:             $gray-300;</br>
$nav-tabs-link-hover-border-color:  $gray-200 $gray-200 $nav-tabs-border-color;</br>
$nav-tabs-link-active-color:        $gray-700;</br>
$nav-tabs-link-active-bg:           $gray-200;</br>
$nav-tabs-link-active-border-color: $gray-300 $gray-300 $nav-tabs-link-active-bg;';

// We need to include a lang string for each block region.
$string['region-side-pre'] = 'Right';

//Edit Button Text
$string['editon'] = 'Turn Edit On';
$string['editoff'] = 'Turn Edit Off';

// Easy Enrollment.
$string['easyenrol_title'] = 'Enrollment Form';
$string['easyenrol_btn'] = 'Enroll in a Course';
$string['easyenrol_blurp'] = 'Enter your course code below to enroll in new courses.  Your teacher provides a course code. ';

// Settings Pages
$string['courseadminlinktoggle'] = 'Show or hide this link in the Course Management navigation drawer.';
$string['courseheaderlinktoggle'] = 'Show or hide this link in the header area.';

// Courseadmin links
$string['courseadminmenutitle'] = 'Course Management';
$string['coursereportmenutitle'] = 'Course Reports';
$string['moreoptions'] = 'More Options...';

// Custom Login Page
$string['showcustomlogin'] = 'Use Custom Login Page';
$string['showcustomlogin_desc'] = 'Enable the features below to be displayed on the Moodle login page.';
$string['logintopimage'] = 'Login Page Image';
$string['logintopimage_desc'] = 'Upload an image that will be placed to the right of the login form.';
$string['featuretext'] = 'Featured Text Box';
$string['featuretext_desc'] = 'One of three featured textboxes that appear below the login form.';
$string['logintoptext'] = 'Top Textbox';
$string['logintoptext_desc'] = 'This is a full-width textbox that appears just below the image on the login page.';
$string['loginbottomtext'] = 'Bottom Textbox';
$string['loginbottomtext_desc'] = 'This is a full-width textbox that appears at the very bottom of the login page.';
$string['alert'] = 'Login Page Alert';
$string['alert_desc'] = 'Add a special alert on your homepage such as an emergency.';

//teacher and student dashboard slider
$string['userlinks'] = 'User Links';
$string['userlinks_desc'] = 'Manage your students';
$string['qbank'] = 'Question Bank';
$string['qbank_desc'] = 'Create and organize quiz questions';
$string['badges'] = 'Badges';
$string['badges_desc'] = 'Award your students';
$string['coursemanage'] = 'Course Settings';
$string['coursemanage_desc'] = 'Manage your entire course';
$string['coursemanagementbutton'] = 'Course Management';
$string['studentdashbutton'] = 'Course Dashboard';
$string['courseinfo'] = 'Course Description';
$string['coursestaff'] = 'Course Teachers';
$string['cmnotetitle'] = 'Course Dashboard';
$string['cmnotetitle_desc'] = 'Course Dashboard notes';
$string['myprogresstext'] = 'My Progress';
$string['myprogresspercentage'] = '% Complete';
$string['mygradestext'] = 'My Grades';
$string['cbank'] = 'Content Bank';
$string['cbank_desc'] = 'Manage interactive content';

// Branding.
$string['footnote'] = 'Footnote';
$string['footnotedesc'] = 'Footnote content editor for main footer';
$string['brandorganization'] = 'Organization Name';
$string['brandorganizationdesc'] = 'Organization name to appear in the footer.';
$string['brandwebsite'] = 'Organization Website';
$string['brandwebsitedesc'] = 'Website address to appear in footer for organization.';
$string['brandphone'] = 'Organization Phone';
$string['brandphonedesc'] = 'Phone number to appear in footer.';
$string['brandemail'] = 'Organization Email';
$string['brandemaildesc'] = 'Email address for organization that appears in footer.';
$string['brandlogo'] = 'Brand Logo Image';
$string['brandlogo_desc'] = 'This image is displayed in the header area and footer area.  It should be a small sized logo.';

$string['cmnotestudent'] = 'Student Dashboard Message';
$string['cmnotestudent_desc'] = 'Provide a message for all students in the course management panel.';
$string['cmnoteteacher'] = 'Teacher Dashboard Message';
$string['cmnoteteacher_desc'] = 'Provide a message for all teachers in the course management panel.';

// Colors.
$string['color_desc'] = 'Choose a new background color for this variable.';
$string['header-bg'] = 'Header Background';
$string['footer-bg'] = 'Footer Background';
$string['navbar-bg'] = 'Top Navbar Background';
$string['sidebar-ahover-bg'] = 'Sidebar Icon Background Hover';
$string['sidebar-bg'] = 'Sidebar Background';

$string['loginiconbutton'] = 'Login to Site';
$string['dashboardiconbutton'] = 'Course Dashboard';

// Section styling chooser.
$string['sectionlayout'] = 'Section Style Chooser';
$string['sectionlayout_desc'] = 'Choose from the following topic/weekly section styles.';
$string['sectionlayout1'] = 'Boost Moodle Default';
$string['sectionlayout2'] = 'Bold Section Title';
$string['sectionlayout3'] = 'Simple Rounded Box';
$string['sectionlayout4'] = 'Topic 0 Boxed';
$string['sectionlayout5'] = '';
$string['sectionlayout6'] = '';
$string['sectionlayout7'] = '';
$string['sectionlayout8'] = '';

//FP Icon Nav
$string['navicon1'] = 'Homepage Icon One';
$string['navicon2'] = 'Homepage Icon Two';
$string['navicon3'] = 'Homepage Icon Three';
$string['navicon4'] = 'Homepage Icon Four';
$string['navicon5'] = 'Homepage Icon Five';
$string['navicon6'] = 'Homepage Icon Six';
$string['navicon7'] = 'Homepage Icon Seven';
$string['navicon8'] = 'Homepage Icon Eight';

$string['createinfo'] = 'Special Course Creator Button';
$string['createinfodesc'] = 'This button appears on the homepage when a user can create new courses.  Those with the role of Course Creator at the site level will see this button.';
$string['iconwidthinfo'] = 'Icon Button Width Setting';
$string['iconwidthinfodesc'] = 'Select a width that will allow your link text to fit inside the icon navigation buttons.';
$string['sliderinfo'] = 'Special Slide Icon Button';
$string['sliderinfodesc'] = 'This button will show/hide a special textbox which slides down from the icon navigation bar.  This is ideal for featuring courses, providing help, or listing required staff training.';
$string['slidetextbox'] = 'Slide Textbox';
$string['slidetextbox_desc'] = 'This textbox content will be displayed when the Slide Button is pressed.';

$string['iconwidth'] = 'Homepage Icon Width';
$string['iconwidth_desc'] = 'Width of the 8 individual icons in the icon navigation bar on the homepage.';

$string['navicon'] = 'Icon';
$string['navicondesc'] = 'Name of the icon you wish to use. List is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['naviconslidedesc'] = 'Suggested icon text: arrow-circle-down . Or choose from the list is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';

$string['naviconbuttontext'] = 'Link Text';
$string['naviconbuttontextdesc'] = 'Text to appear below the icon.';
$string['naviconbuttonurl'] = 'Link URL';
$string['naviconbuttonurldesc'] = 'URL the button will point to. You can link to anywhere including outside websites  just enter the proper URL.  If your Moodle site is in a subdirectory the default URL will not work.  Please adjust the URL to reflect the subdirectory. Example if "moodle" was your subdirectory folder then the URL would need to be changed to /moodle/my/ ';
$string['marketingurltarget'] = 'Link Target';
$string['marketingurltargetdesc'] = 'Choose how the link should be opened';
$string['marketingurltargetself'] = 'Current Page';
$string['marketingurltargetnew'] = 'New Page';
$string['marketingurltargetparent'] = 'Parent Frame';