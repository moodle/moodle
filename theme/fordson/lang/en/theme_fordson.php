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
 * @package    theme_fordson
 * @copyright  2016 Chris Kenniburg
 * @credits    theme_boost - MoodleHQ
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Theme strings.
$string['choosereadme'] = 'Fordson provides a unique visual experience to the default Boost theme with customization features such as color choosers, improved navigation, and enhanced homepage experience.';
$string['configtitle'] = 'Fordson';
$string['pluginname'] = 'Fordson';
$string['region-side-pre'] = 'Right';
$string['generalsettings'] = 'General settings';
$string['advancedsettings'] = 'Advanced settings';
$string['iconnavheading'] = 'Icon Navigation';
$string['presetadjustmentsettings'] = 'Preset Adjustments';
$string['customloginheading'] = 'Custom Login Page';
$string['iconnavheadingsub'] = 'Create Buttons with Icons for use on the homepage.  Links can go anywhere.';
$string['section_mods'] = 'Modules:';
$string['nomycourses'] = 'You are not enrolled in any courses.';
$string['coursehome'] = 'Home';
$string['navdrawerbtn'] = 'Navigation';
$string['region-fp-a'] = 'Column A';
$string['region-fp-b'] = 'Column B';
$string['region-fp-c'] = 'Column C';
$string['courseblockpanelbtn'] = 'Course Blocks';
$string['courseblockpanelbtnclose'] = 'Close';
$string['showblockregions'] = 'Show Additional Frontpage Block Regions';
$string['showblockregions_desc'] = 'Turn on three more block regions on the site frontpage.  These appear just below the icon navigation bar.';
$string['viewsectionmodules'] = 'View Section Modules';
$string['privacy:metadata'] = 'The Fordson theme does not store any individual user data.';
$string['blockdisplay'] = 'Block Display Location Options';
$string['blockdisplay_desc'] = 'Choose how to display blocks on the homepage and course pages.  Fordson adds a 3 column collapsible block panel that can be hidden by the user.  Choose the Boost default option to use a single right side column for blocks.  The Boost default option also moves the Add a Block button back to the Nav Drawer on the left of the page.  We also recommend using "Single Column Boost Default" when using any of the Boost page layouts from the settings above.';
$string['blockdisplay_off'] = 'Single Column Boost Default';
$string['blockdisplay_on'] = 'Three Column Fordson Default';
$string['ilearnsecurebrowser'] ='This Quiz is secured with iLearn Secure Browser (A Chromebook using the iLearn app must be used to attempt this quiz)';

// Presets Settings.
$string['presets_settings'] = 'Presets';
$string['currentinparentheses'] = '(current)';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme.
    See https://docs.moodle.org/dev/Boost_Presets for information on creating and sharing your own preset files.';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme. <a href="https://goo.gl/fMXzSo" target="_new">Instructions for Fordson </a>';
$string['favicon'] = 'Favicon';
$string['favicon_desc'] = 'Change the favicon for Fordson. Images with a transparent background and 32px height will work best.  Allowed types: PNG, JPG, ICO';
$string['enhancedmydashboard'] = 'Enhanced MyDashboard';
$string['enhancedmydashboard_desc'] = 'Turning this on will enhance the MyDashboard page to include all the fordson features such as the Easy Enrollment Form, block sliders, Icon Navigation bar, Custom Homepage Text, Slideshow, and more.';


// Colours Settings.
$string['colours_settings'] = 'Colours';
$string['colours_headingsub'] = 'Colour Settings';
$string['colours_desc'] = 'Colour choosers will allow you to customize the look and feel of the main elements on the page.  If you are using a Preset other than the default, you will need to remove any custom colors below for best results as these will over-ride the Preset with undesireable results.  Generally, the Preset will have default colors that you will want to see before customizing them here.';
$string['brandColour'] = 'Brand Colour';
$string['brandColour_desc'] = 'Your main brand colour';
$string['brandprimary'] = 'Brand Primary';
$string['brandprimary_desc'] = 'Your main brand colour';
$string['brandsuccess'] = 'Brand Success';
$string['brandsuccess_desc'] = 'Brand colour for succesful alerts, postive panels, buttons, etc';
$string['brandinfo'] = 'Brand info';
$string['brandinfo_desc'] = 'Brand colour information alerts and panels, etc';
$string['brandwarning'] = 'Brand Warning';
$string['brandwarning_desc'] = 'Brand colour for warning alerts and panels, etc';
$string['branddanger'] = 'Brand Danger';
$string['branddanger_desc'] = 'Brand colour for danger alerts and panels, etc';
$string['breadcrumbbkg'] = 'Breadcrumb Background Colour';
$string['breadcrumbbkg_desc'] = 'Breadcrumb background colour.';
$string['drawerbkg'] = 'Side Drawer Background Colour';
$string['drawerbkg_desc'] = 'Side Drawer background colour for the menu on the left side of the page.';
$string['cardbkg'] = 'Content Background Colour';
$string['cardbkg_desc'] = 'Content background colour for course content and blocks.';
$string['bodybackground'] = 'Body Background Colour';
$string['bodybackground_desc'] = 'The main colour to use for the background.';
$string['footerbkg'] = 'Footer Background Colour';
$string['footerbkg_desc'] = 'Footer background colour for the bottom of the page.';
$string['topnavbarbg'] = 'Top Navigation Navbar Default';
$string['topnavbarbg_desc'] = 'Content background colour for the top navigation bar.';
$string['topnavbarteacherbg'] = 'Top Navigation Navbar Teacher Role';
$string['topnavbarteacherbg_desc'] = 'Content background colour for the top navigation bar when a user is a teacher.  This feature must be turned on in Menu Settings. Please see the navbarcolorswitch setting.';

$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS code which will be injected at the end of the style sheet.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else.
    Most of the time you will use this setting to define variables.';

// Image Settings.
$string['imagesettings'] = 'Custom image settings';
$string['headerdefaultimage'] = 'Default header image';
$string['headerdefaultimage_desc'] = 'Default image for course headers and non-course pages';
$string['backgroundimage'] = 'Default page background image';
$string['backgroundimage_desc'] = 'Background image for pages';
$string['loginimage'] = 'Default Login image';
$string['loginimage_desc'] = 'Background image for login page';
$string['learningcontentpadding'] = 'Learning Content Spacing';
$string['learningcontentpadding_desc'] = 'This controls how much space between the top of the page and the main course content. Generally, you want this to be less than the height of the header image.';
$string['showcourseheaderimage'] = 'Show Course Images';
$string['showcourseheaderimage_desc'] = 'Allow teachers to customize the course header image by uploading an image file into course settings.';
$string['headerlogo'] = 'Header Logo';
$string['headerlogo_desc'] = 'This logo will be displayed at the top of the page in the header area. It uses bootstrap responsive image scaling.';

//Slideshow
$string['slideshowsettings'] = 'Slideshow';
$string['slideshowheight'] = 'Slideshow Height';
$string['slideshowheight_desc'] = 'Adjust the height of the slideshow on the Site Home/Dashboard pages. This setting is ignored on the Custom Login Page.  On the Custom Login Page the slide height is determined by page size and width.';
$string['slideshowspacer'] = 'Slideshow Spacer on Custom Login Page';
$string['slideshowspacer_desc'] = 'On the custom login page this setting is used to add separation below the login form in case you put text into the slides.';
$string['showslideshow'] = 'Activate Slideshow';
$string['showslideshow_desc'] = 'Check this option to turn on the slideshow feature.';
$string['slide1info'] = 'Slide 1';
$string['slide1infodesc'] = 'Slide 1 details.';
$string['slide2info'] = 'Slide 2';
$string['slide2infodesc'] = 'Slide 2 details.';
$string['slide3info'] = 'Slide 3';
$string['slide3infodesc'] = 'Slide 3 details.';
$string['slidetitle'] = 'Slide Title';
$string['slidetitle_desc'] = 'Enter a title for this slide.';
$string['slidecontent'] = 'Slide Description';
$string['slidecontent_desc'] = 'Add a description for this slide.';
$string['slideimage'] = 'Slide Image';
$string['slideimage_desc'] = 'Add a background image for this slide.';
$string['slideshowpages'] = 'Slideshow Pages';
$string['slideshowpages_desc'] = 'Determine what main pages the slideshow should appear on.  If using the slideshow on custom login page, it is recommended to only upload images and do not use the slide text options below. NOTE: The slideshow will not appear on mobile devices.';
$string['slideshowpages0'] = 'Show on custom login page only';
$string['slideshowpages1'] = 'Show on Site Home and Dashboard only';
$string['slideshowpages2'] = 'Show everywhere';

// Footer
$string['footerheading'] = 'Footer';
$string['brandorganization'] = 'Organization Name';
$string['brandorganizationdesc'] = 'Organization name to appear in the footer.';
$string['brandwebsite'] = 'Organization Website';
$string['brandwebsitedesc'] = 'Website address to appear in footer for organization.';
$string['brandphone'] = 'Organization Phone';
$string['brandphonedesc'] = 'Phone number to appear in footer.';
$string['brandemail'] = 'Organization Email';
$string['brandemaildesc'] = 'Email address for organization that appears in footer.';
$string['footerheadingsub'] = 'Customize the footer of the homepage';
$string['footerdesc'] = 'The items below allow you provide branding to the theme footer.';
$string['footerheadingsocial'] ='Social Icons';
$string['socialnetworks'] = 'Social Networks';
$string['facebook'] = 'Facebook URL';
$string['facebookdesc'] = 'Enter the URL of your Facebook page. (i.e http://www.facebook.com/)';
$string['twitter'] = 'Twitter URL';
$string['twitterdesc'] = 'Enter the URL of your Twitter feed. (i.e http://www.twitter.com/)';
$string['googleplus'] = 'Google+ URL';
$string['googleplusdesc'] = 'Enter the URL of your Google+ profile. (i.e https://google.com/)';
$string['linkedin'] = 'LinkedIn URL';
$string['linkedindesc'] = 'Enter the URL of your LinkedIn profile. (i.e http://www.linkedin.com/)';
$string['youtube'] = 'YouTube URL';
$string['youtubedesc'] = 'Enter the URL of your YouTube channel. (i.e http://www.youtube.com/)';
$string['tumblr'] = 'Tumblr URL';
$string['tumblrdesc'] = 'Enter the URL of your Tumblr. (i.e http://www.tumblr.com)';
$string['vimeo'] = 'Vimeo URL';
$string['vimeodesc'] = 'Enter the URL of your Vimeo channel. (i.e http://vimeo.com/)';
$string['flickr'] = 'Flickr URL';
$string['flickrdesc'] = 'Enter the URL of your Flickr page. (i.e http://www.flickr.com/)';
$string['vk'] = 'VKontakte URL';
$string['vkdesc'] = 'Enter the URL of your Vkontakte page. (i.e http://www.vk.com/)';
$string['skype'] = 'Skype Account';
$string['skypedesc'] = 'Enter the Skype username of your organisations Skype account';
$string['pinterest'] = 'Pinterest URL';
$string['pinterestdesc'] = 'Enter the URL of your Pinterest page. (i.e http://pinterest.com/)';
$string['instagram'] = 'Instagram URL';
$string['instagramdesc'] = 'Enter the URL of your Instagram page. (i.e http://instagram.com/)';
$string['website'] = 'Website URL';
$string['websitedesc'] = 'Enter the URL of your own website. (i.e http://dearbornschools.org)';
$string['blog'] = 'Blog URL';
$string['blogdesc'] = 'Enter the URL of your institution blog. (i.e http://dearbornschools.org)';
$string['sociallink'] = 'Custom Social Link';
$string['sociallinkdesc'] = 'Enter the URL of your your custom social media link. (i.e http://dearbornschools.org)';
$string['sociallinkicon'] = 'Link Icon';
$string['sociallinkicondesc'] = 'Enter the fontawesome name of the icon for your link<br />A full list of FontAwesome icons can be found at https://fontawesome.com/v4.7.0/icons/';

// Content settings.
$string['coursetileinfo'] = 'Course Display Options';
$string['coursetileinfodesc'] = 'These settings allow you to customize how courses will be displayed on the frontpage as well as course categories.';
$string['textcontentinfo'] = 'Custom Content';
$string['textcontentinfodesc'] = 'Use the textboxes below to add a customized information for users.';
$string['generalcontentinfo'] = 'General Content Display Settings';
$string['generalcontentinfodesc'] = 'The options below help you customize the way content is displayed and turn on additional features for Fordson.';
$string['enrollcoursecard'] = 'Access';
$string['layoutinfo'] = 'Layout Settings';
$string['layoutinfodesc'] = 'Control page layout by chooseing a design.';

$string['pagelayout'] = 'Layout Chooser';
$string['pagelayout_desc'] = 'Choose from the following layouts. Certain page layouts require additional adjustments on the Preset Adjustments Page.  Be sure to pay attention to: Learning Content Spacing, Header Image Height, and Content Padding as these will help adjust header image placement and padding to the left and right of the main learning content. <a href="https://goo.gl/fMXzSo" target="_new">Instructions for Fordson</a>';
$string['pagelayout1'] = 'Default Boost Layout';
$string['pagelayout2'] = 'Full-Width / Top Header Image';
$string['pagelayout3'] = 'Centered Content / Overlapping Top Header Image';
$string['pagelayout4'] = 'Centered Content / Full Screen Header Image';
$string['pagelayout5'] = 'Default Boost Layout / Header Image in Course Title Box';

$string['sectionlayout'] = 'Section Style Chooser';
$string['sectionlayout_desc'] = 'Choose from the following topic/weekly section styles.  <a href="https://goo.gl/fMXzSo" target="_new">Instructions for Fordson</a>';
$string['sectionlayout1'] = 'Boost Default';
$string['sectionlayout2'] = 'Bold Notecard';
$string['sectionlayout3'] = 'Folder Tabs';
$string['sectionlayout4'] = 'Clip Board';
$string['sectionlayout5'] = 'Simple Box';
$string['sectionlayout6'] = 'Highlighted Section Title';
$string['sectionlayout7'] = 'University Learner';
$string['sectionlayout8'] = 'Corporate Learner';

$string['marketingstyle'] = 'Marketing Tile Style Chooser';
$string['marketingstyle_desc'] = 'Choose from the following marketing styles.  These will change the look and style of the marketing boxes on the site homepage.';
$string['marketingstyle1'] = 'Top Bar Highlight';
$string['marketingstyle2'] = 'Postit Note';
$string['marketingstyle3'] = 'Simplicity';
$string['marketingstyle4'] = 'Boxed Shadow';

$string['contentsettings'] = 'Content areas';
$string['footnote'] = 'Footnote';
$string['footnotedesc'] = 'Footnote content editor for main footer';
$string['fptextbox'] = 'Homepage Textbox Authenticated User';
$string['fptextbox_desc'] = 'This textbox appears on the homepage once a user authenticates. It is ideal for putting a welcome message and providing instructions for the learner.';
$string['fptextboxlogout'] = 'Homepage Textbox Visitor';
$string['fptextboxlogout_desc'] = 'This textbox appears on the homepage for visitors and is ideal for putting a welcome message or link to the login page.';
$string['slidetextbox'] = 'Slide Textbox';
$string['slidetextbox_desc'] = 'This textbox content will be displayed when the Slide Button is pressed.';
$string['courseboxheight'] = 'Course Tile Height';
$string['courseboxheight_desc'] = 'Control the height of the Course tile on the frontpage and course categories.';
$string['catsicon'] = 'Category Icon';
$string['catsicon_desc'] = 'Choose an icon to represent course categories.';
$string['trimtitle'] = 'Trim Course Title';
$string['trimtitle_desc'] = 'Enter a number to trim the title length.  This number represents characters that will be displayed.';
$string['trimsummary'] = 'Trim Course Summary';
$string['trimsummary_desc'] = 'Enter a number to trim the summary length.  This number represents characters that will be displayed.';
$string['titletooltip'] = 'Course Title Tooltip';
$string['titletooltip_desc'] = 'If using Trim Course Title you can use tooltips which will display the entire course title in a tooltip.  Check this box to turn on tooltips.';
$string['dashactivityoverview'] = 'ACTIVITIES OVERVIEW';
$string['blockwidthfordson'] = 'Block Column Width';
$string['blockwidthfordson_desc'] = 'Adjust the width of the block column.';
$string['fpsignup'] = 'Sign In';
$string['showloginform'] = 'Show Login Form';
$string['showloginform_desc'] = 'Uncheck this to hide the custom login form on the homepage for logged out users.';
$string['backtotop'] = 'Back to Top and Scrollspy';
$string['activityiconsize'] = 'Activity Icon Size';
$string['activityiconsize_desc'] = 'Adjust the size of the activity icons used in courses.';
$string['enablecategoryicon'] = 'Category Display Icons';
$string['enablecategoryicon_desc'] = 'When checked this will display course categories as icons';
$string['coursestyle1'] = 'Tile Style One';
$string['coursestyle2'] = 'Tile Style Two';
$string['coursestyle3'] = 'Tile Style Three';
$string['coursestyle4'] = 'Tile Style Four w/course summary';
$string['coursestyle5'] = 'Horizontal Style One';
$string['coursestyle6'] = 'Horizontal Image Background Full Details';
$string['coursestyle7'] = 'Horizontal Image Background Title & Teacher Only';
$string['coursestyle8'] = 'Horizontal Two Column';
$string['coursestyle9'] = 'Corporate Training - minimal with completion progressbar';
$string['coursestyle10'] = 'Default Moodle Course Display';
$string['coursetilestyle'] = 'Course Tile Display';
$string['coursetilestyle_desc'] = 'When viewing course categories you can choose from the following styles to display courses. <a href="https://goo.gl/fMXzSo" target="_new">Instructions for Fordson </a>';
$string['gutterwidth'] = 'Content Padding';
$string['gutterwidth_desc'] = 'This setting controls how much spacing is used on the left and right of the main content.';
$string['frontpagemycoursessorting'] = 'My Courses Sort Order by Last Access';
$string['frontpagemycoursessorting_desc'] = 'When checked this feature will sort My Courses (enrolled courses) display by last access for the user.  This will override the "Sort my courses" setting under Navigation.  If unchecked then Frontpage My Courses will display as normal. This includes My Courses displayed in the drop down at the top of the page as well as My Enrolled Courses display on Site Homepage. This does not affect the Dashboard or Dashboard blocks.';
$string['showactivitynav'] = 'Show Activity Navigation';
$string['showactivitynav_desc'] = 'Uncheck this to turn off activity navigation at the bottom of the activity pages.';
$string['navbarcolorswitch'] = 'Navbar Color Switch';
$string['navbarcolorswitch_desc'] = 'This feature changes the color of the navbar based on user role.  A student will see one color and a teacher will see another color.  This is useful when a teacher changes roles and helps distinguish between a student view and a teacher view.';
$string['navbarcolorswitch_on'] = 'Change navbar color based on role.';
$string['navbarcolorswitch_off'] = 'Do not change navbar color based on role.';

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
$string['activitylinkstitle'] = 'Activities';
$string['activitylinkstitle_desc'] = 'View All Activities in Course';
$string['myprogresstext'] = 'My Progress';
$string['myprogresspercentage'] = '% Complete';
$string['mygradestext'] = 'My Grades';


// Menu Settings
$string['menusettings'] = 'Menu settings';
$string['thiscourse'] = 'This Course';
$string['courseactivities'] = 'Course Activites';
$string['headerimagepadding'] = 'Header Image Height';
$string['headerimagepadding_desc'] = 'Control the padding and height of the header image for courses.';
$string['activitymenu'] = 'Show Grouped Activities Menu';
$string['activitymenu_desc'] = 'Show the grouped activity listings in the student and teacher panels.  This menu displays a grouped list of all activities for the student and teacher.';

$string['coursemanagementinfo'] = 'Course Administration Panel Menu';
$string['coursemanagementinfodesc'] = 'Fordson provides a unique and organized Course Administration Panel which can be accessed from anywhere within a course by teachers to access their course management links. Students also can access a Student Course Dashboard which includes information relevant to the course.  It is highly recommended you keep this turned on.  ';
$string['coursemanagementtoggle'] = 'Show Student and Teacher Course Management Panels';
$string['coursemanagementtoggle_desc'] = 'This displays the Course Administration links in an organized panel for teachers that provides a dashboard of all the links they need to manage their course and users. It will also display a course overview panel for students with grades, course completion, and other items from the course.';
$string['coursemanagementtextbox'] = 'Course Management Message';
$string['coursemanagementtextbox_desc'] = 'Add a message for teachers in the course management panel on every course page.';
$string['studentdashboardtextbox'] = 'Student Dashboard Message';
$string['studentdashboardtextbox_desc'] = 'Add a message for students in the student dashboard panel on every course page.';
$string['courseeditingcog'] = 'Show Default Course Settings Menu';
$string['courseeditingcog_desc'] = 'If using the Course Management Panel the default menu is hidden.  By checking this you can show the default menu as well as the teacher course management panel. This is ideal if using a third party plugin which uses the course menu for access to settings.';
$string['showstudentcompletion'] = 'Show Student Completion';
$string['showstudentcompletion_desc'] = 'Show student completion radial in student dashboard panel.  Even with this checked the course must have completion turned on in order to display.';
$string['showstudentgrades'] = 'Show Student Grades';
$string['showstudentgrades_desc'] = 'Show student gradebook link in student dashboard panel.  Even with this checked the course must have Show Student Grades turned on in order to display.';
$string['showonlygroupteachers'] = 'Only Show Group Teachers';
$string['showonlygroupteachers_desc'] = 'When enabled, only teachers in the same group as the student will be shown on the Student Course Management Panel.';
$string['showcourseadminstudents'] = 'Show Student Course Admin Cog';
$string['showcourseadminstudents_desc'] = 'This displays the course settings to students.  This is needed if you want to allow them to unenroll from courses.';

$string['setting_navdrawersettings'] = 'Nav Drawer Settings';
$string['setting_navdrawersettings_desc'] = 'Enable the Boost nav drawer feature.  Fordson does not require the nav drawer for navigation.  We have replaced it with a Jump-to-section dropdown. You may re-enable the nav drawer below.';
$string['shownavdrawer'] = 'Show Navdrawer';
$string['shownavdrawer_desc'] = 'Fordson has eliminated the need for the nav drawer by utilizing a drop-down menu for in-course navigation.  If you must use the nav drawer you can check this box to re-enable it.';

$string['mycoursesinfo'] = 'Dynamic Enrolled Courses List & Course Navigation Menus';
$string['mycoursesinfodesc'] = 'Displays a dynamic list of enrolled courses to the user in the top navigation bar.  This will also control the course navigation dropdown for each individual course.';
$string['displaymycourses'] = 'Display enrolled courses';
$string['displaymycoursesdesc'] = 'Display enrolled courses for users in the top navigation bar.';
$string['displaythiscourse'] = 'Display This Course Menu';
$string['displaythiscoursedesc'] = 'Display jump-to-section in course menu for users in the top navigation bar.  This contains menu items previously found in the nav drawer.';

$string['mycoursetitle'] = 'Terminology';
$string['mycoursetitledesc'] = 'Change the terminology for the "My Courses" link in the dropdown menu';

$string['mycourses'] = 'My Courses';
$string['myunits'] = 'My Units';
$string['mymodules'] = 'My Modules';
$string['myclasses'] = 'My Classes';
$string['mytraining'] = 'My Training';
$string['myprofessionaldevelopment'] = 'My PD';
$string['mycred'] = 'My Credentials';
$string['myplans'] = 'My Plans';
$string['mycomp'] = 'My Competencies';
$string['myprograms'] = 'My Programs';
$string['mylectures'] = 'My Lectures';
$string['mylessons'] = 'My Lessons';

$string['homemycourses'] = 'Course Home';
$string['homemyunits'] = 'Unit Home';
$string['homemymodules'] = 'Module Home';
$string['homemyclasses'] = 'Class Home';
$string['homemytraining'] = 'Training Home';
$string['homemyprofessionaldevelopment'] = 'PD Home';
$string['homemycred'] = 'Credential Home';
$string['homemyplans'] = 'Plan Home';
$string['homemycomp'] = 'Competency Home';
$string['homemyprograms'] = 'Program Home';
$string['homemylectures'] = 'Lecture Home';
$string['homemylessons'] = 'Lesson Home';


$string['thismycourses'] = 'This Course';
$string['thismyunits'] = 'This Unit';
$string['thismymodules'] = 'This Module';
$string['thismyclasses'] = 'This Class';
$string['thismytraining'] = 'This Training';
$string['thismyprofessionaldevelopment'] = 'This PD';
$string['thismycred'] = 'This Credential';
$string['thismyplans'] = 'This Plan';
$string['thismycomp'] = 'This Competency';
$string['thismyprograms'] = 'This Program';
$string['thismylectures'] = 'This Lecture';
$string['thismylessons'] = 'This Lesson';

$string['noenrolments'] = 'You have no current enrolments';
$string['siteadminquicklink'] = 'Site Administration';
$string['shownavclosed'] = 'Nav Drawer Closed by Default';
$string['shownavclosed_desc'] = 'Show the navigation drawer collapsed for all users by default on each page.';

//FP Icon Nav
$string['navicon1'] = 'Homepage Icon One';
$string['navicon2'] = 'Homepage Icon Two';
$string['navicon3'] = 'Homepage Icon Three';
$string['navicon4'] = 'Homepage Icon Four';
$string['navicon5'] = 'Homepage Icon Five';
$string['navicon6'] = 'Homepage Icon Six';
$string['navicon7'] = 'Homepage Icon Seven';
$string['navicon8'] = 'Homepage Icon Eight';

// Custom Login Icon Nav
$string['loginnavicon1'] = 'Icon One';
$string['loginnavicon2'] = 'Icon Two';
$string['loginnavicon3'] = 'Icon Three';
$string['loginnavicon4'] = 'Icon Four';
$string['loginnavicontitletext'] = 'Icon Title';
$string['loginnavicontitletextdesc'] = 'Text to appear below the icon as a title.';
$string['loginnavicontext'] = 'Icon Text';
$string['loginnavicontextdesc'] = 'Text that will appear below the icon.  Keep things short for best results.';
$string['featureimage'] = 'Feature Image';
$string['featureimage_desc'] = 'This image will appear next to the featured text in a row.';
$string['featuretext'] = 'Feature Text';
$string['featuretext_desc'] = 'This text will appear next to the featured image in a row. Use a Heading4 to generate a special title within the textbox.  In the Atto Editor H4 is Heading Medium.';
$string['feature1info'] = 'Feature One';
$string['feature2info'] = 'Feature Two';
$string['feature3info'] = 'Feature Three';
$string['featureinfo_desc'] = 'A feature consists of an image and text which will appear on the custom login page in a row. You must add both an image and text in order for the feature to appear.';
$string['customlogininfo'] = 'Custom Login Page Settings';
$string['customlogininfo_desc'] = 'This allows you to create a custom login page.  Other settings in the theme that will display on the login page include the following:<br>
<b>* Site Administration > Security > Site Policies > Force Users To Login = Make sure this is checked so that users will be taken to your custom login page.<br>
* Site Administration > Appearance > Logos > Logo = Upload an image here and it will appear above the login form.<br>
* Fordson > Custom Image Settings > Default Login Image can be used to change the background image for the login page. <br>
* Fordson > Content Areas > Homepage Alert can be used to provide a notice on the top of the page. </b>';
$string['showcustomlogin'] = 'Turn on Custom Login';
$string['showcustomlogin_desc'] = 'You must turn this on to activate the custom settings below.';
$string['logintopimage'] = 'Login Page Banner Image';
$string['logintopimage_desc'] = 'This image appears on the login page to the right of the login form.  This is ideal for a logo or banner with a transparent background.';
$string['fploginform'] = 'Login Form Color';
$string['fploginform_desc'] = 'Background color of the login form on the custom homepage.';


//FP Icon Nav default text for buttons
$string['naviconbutton1textdefault'] = 'Dashboard';
$string['naviconbutton2textdefault'] = 'Calendar';
$string['naviconbutton3textdefault'] = 'Badges';
$string['naviconbutton4textdefault'] = 'All Courses';
$string['naviconbuttoncreatetextdefault'] = 'Create a Course';

$string['createinfo'] = 'Special Course Creator Button';
$string['createinfodesc'] = 'This button appears on the homepage when a user can create new courses.  Those with the role of Course Creator at the site level will see this button.';
$string['iconwidthinfo'] = 'Icon Button Width Setting';
$string['iconwidthinfodesc'] = 'Select a width that will allow your link text to fit inside the icon navigation buttons.';
$string['sliderinfo'] = 'Special Slide Icon Button';
$string['sliderinfodesc'] = 'This button will show/hide a special textbox which slides down from the icon navigation bar.  This is ideal for featuring courses, providing help, or listing required staff training.';

$string['iconwidth'] = 'Homepage Icon Width';
$string['iconwidth_desc'] = 'Width of the 8 individual icons in the icon navigation bar on the homepage.';

$string['navicon'] = 'Icon';
$string['navicondesc'] = 'Name of the icon you wish to use. List is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['naviconslidedesc'] = 'Suggested icon text: arrow-circle-down . Or choose from the list is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';

$string['naviconbuttontext'] = 'Link Text';
$string['naviconbuttontextdesc'] = 'Text to appear below the icon.';
$string['naviconbuttonurl'] = 'Link URL';
$string['naviconbuttonurldesc'] = 'URL the button will point to. You can link to anywhere including outside websites  just enter the proper URL.  If your Moodle site is in a subdirectory the default URL will not work.  Please adjust the URL to reflect the subdirectory. Example if "moodle" was your subdirectory folder then the URL would need to be changed to /moodle/my/ ';

//Edit Button Text
$string['editon'] = 'Turn Edit On';
$string['editoff'] = 'Turn Edit Off';

//Marketing Tiles
$string['marketingheading'] = 'Marketing Tiles';
$string['marketinginfodesc'] = 'Enter the settings for your marketing spot.  You must include a title in order for the Marketing Spot to appear.  The title will activate the individual Marketing Spots.';
$string['marketingheadingsub'] = 'Three locations on the front page to add information and links';
$string['marketboxcolor'] = 'Marketing Box Background Color';
$string['marketboxcolor_desc'] = 'The color of the background for the marketing box.';
$string['marketboxbuttoncolor'] = 'Marketing Box Button Color';
$string['marketboxbuttoncolor_desc'] = 'The color of the button background for the marketing box.';
$string['marketboxcontentcolor'] = 'Marketing Box Content Background Color';
$string['marketboxcontentcolor_desc'] = 'The color of the background for the marketing box content. This is where the text appears in the marketing spot and can be different from the box background color to draw attention to the text.';
$string['marketingheight'] = 'Height of Marketing Images';
$string['marketingheightdesc'] = 'If you want to display images in the Marketing boxes you can specify their hight here.';
$string['marketingdesc'] = 'This theme provides the option of enabling three "marketing" or "ad" spots just under the slideshow.  These allow you to easily identify core information to your users and provide direct links.';
$string['marketing1'] = 'Marketing Spot One';
$string['marketing2'] = 'Marketing Spot Two';
$string['marketing3'] = 'Marketing Spot Three';
$string['marketing4'] = 'Marketing Spot Four';
$string['marketing5'] = 'Marketing Spot Five';
$string['marketing6'] = 'Marketing Spot Six';
$string['marketing7'] = 'Marketing Spot Seven';
$string['marketing8'] = 'Marketing Spot Eight';
$string['marketing9'] = 'Marketing Spot Nine';
$string['marketingtitle'] = 'Title';
$string['marketingtitledesc'] = 'Title to show in this marketing spot.  You must include a title in order for the Marketing Tile to appear.';
$string['marketingicon'] = 'Link Icon';
$string['marketingicondesc'] = 'Name of the icon you wish to use in the marketing URL Button. List is <a href="https://fontawesome.com/v4.7.0/icons/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['marketingimage'] = 'Image';
$string['marketingimage_desc'] = 'This provides the option of displaying an image in the marketing spot';
$string['marketingcontent'] = 'Content';
$string['marketingcontentdesc'] = 'Content to display in the marketing box. Keep it short and sweet.';
$string['marketingbuttontext'] = 'Link Text';
$string['marketingbuttontextdesc'] = 'Text to appear on the button.';
$string['marketingbuttonurl'] = 'Link URL';
$string['marketingbuttonurldesc'] = 'URL the button will point to.';
$string['marketingurltarget'] = 'Link Target';
$string['marketingurltargetdesc'] = 'Choose how the link should be opened';
$string['marketingurltargetself'] = 'Current Page';
$string['marketingurltargetnew'] = 'New Page';
$string['marketingurltargetparent'] = 'Parent Frame';
$string['togglemarketing'] = 'Marketing Tile Position';
$string['togglemarketing_desc'] = 'Determine where the marketing tiles will be located on the homepage.';
$string['displaytop'] = 'Display at Top of Page';
$string['displaybottom'] = 'Display at Bottom of Page';
$string['markettextbg'] = 'Marketing Tile Text Background';
$string['markettextbg_desc'] = 'Background colour for the text area of the marketing tiles.';

//Alerts
$string['alert'] = 'Homepage Alert';
$string['alert_desc'] = 'This is a special alert message that will appear on the homepage.';

// Fordson Plugin Integration Enhancements.
$string['integrationinfo'] = 'Plugin Integrations';
$string['integrationinfo_desc'] = 'Fordson can activate enhanced features and better integrate with certain plugins. The settings below will allow you to use the default plugin behavior or turn on advanced features that the developers of Fordson have implemented.';
$string['integrationon'] = 'Turn Integration On';
$string['integrationoff'] = 'Turn Integration Off';

$string['collapsibletopics'] = 'Collapsible Topics Course Format';
$string['collapsibletopics_desc'] = 'If you install the Collapsible Topics Format ( <a href="https://moodle.org/plugins/format_collapsibletopics">format_collapsibletopics</a> ) the Fordson theme can provide an enhanced course format view and special styling that better integrates with the overall look and feel of Fordson. Special care and attention has been developed to provide a smoother user experience for users of all ages and skills.';
$string['viewfcfmodules'] = 'View Activities and Resources';

$string['easyenrollmentintegration'] = 'Easy Enrollment Plugin';
$string['easyenrollmentintegration_desc'] = 'The Easy Enrollment plugin (<a href="https://moodle.org/plugins/enrol_easy"> Easy Enrollment Plugin</a>) allows students to enroll in courses directly from the Moodle homepage.  The enrollment plugin uses a 6 digit code or a auto-generated QR code with webcam support to enroll students.  The student enters the code or scans the QR code and is instantly enrolled into the intended course or group within the course.  This plugin auto-activates once you install Easy Enrollment and activate it in Site Administration.  It is only activated if the plugin is installed and properly configured.';
$string['jitsibuttontext'] = 'Automatic Jitsi Meeting Header Button';
$string['jitsibuttontextdesc'] = 'Add text for a button that will be added to the top of every Moodle Course.  This button will take the users to a Jitsi Meeting room which forms the URL from the Course Name and Course ID number.  You must have a Jitsi Web Conferencing Server.';
$string['jitsibuttonurl'] = 'Jitsi Server URL';
$string['jitsibuttonurldesc'] = 'Example public server: https://meet.jit.si <br> DO NOT ADD A TRAILING BACKSLASH <br> This url will be used for the button that will automatically be added to each Moodle Course in the Header area. We strongly recommend you setup your own Jitsi server and force login to the Jitsi Meeting Room.';
