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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2017 Gareth J Barnard
 * @copyright   2016 Gareth J Barnard
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* Core */
$string['configtitle'] = 'Essential';
$string['pluginname'] = 'Essential';
$string['choosereadme'] = '
<div class="clearfix">
<div class="well">
<h2>Essential</h2>
<p><img class="img-polaroid" src="essential/pix/screenshot.jpg" alt="Essential screen shot"/></p>
</div>
<div class="well">
<h3>About Essential</h3>
<p>Essential is based upon the Bootstrap theme, which was created for Moodle 2.5, with the help of:<br>
Bas Brands, Stuart Lamour, Mark Aberdour, Paul Hibbitts, Mary Evans.</p>
<h3>Theme Credits</h3>
<p>Original Author: Julian Ridden<br>
Work taken over in July 2014 by:<br>
Gareth J. Barnard<br>
David Bezemer<br>
Work taken over on the 9th October 2014 by:<br>
Gareth J. Barnard<br>
</p>
<h3>Sponsorships</h3>
<p>This theme is provided to you for free, and if you want to express your gratitude for using this theme, please consider sponsoring by:
<h4>PayPal</h4>
<p>Please contact me via my <a href="http://moodle.org/user/profile.php?id=442195" target="_blank">\'Moodle profile\'</a> for details as I am an individual and therefore am unable to have \'buy me now\' buttons under their terms.</p>
<br>Sponsorships help to facilitate maintenance and allow me to provide you with more and better features.  Without your support the theme cannot be maintained.</p>
<p>
<h3>Sponsors</h3>
<p>Sponsorships gratefully received with thanks from:</p>
<ul>
<li>Mihai Bojonca, TCM International Institute.</li>
<li>Guido Hornig, actXcellence <a href="//actxcellence.de" target="_blank">actxcellence.de</a></li>
<li>Delvon Forrester, Esparanza co uk</li>
<li>iZone</li>
<li>Anis Jradah</li>
<li>Ute Hlasek, <a href="//hlasek-it.de/moodle" target="_blank">hlasek-it.de/moodle</a></li>
</ul>
</p>
<p>
<h3>Essential for Moodle 3.3 kindly sponsored by:</h3>
<ul>
<li>ClassroomRevolution, LLC -- Moodle Partner</li>
<li>Daniel Méthot - e-learning-facile.com/formations/</li>
<li>Floyd Saner, Learning Contexts, LLC</li>
<li>Gemma Lesterhuis</li>
<li>Mihai Bojonca, TCM International Institute</li>
</ul>
</p>
<h3>Customisation</h3>
<p>If you like this theme and would like me to customise it, transpose functionality to another theme, build a new theme from scratch or create a child theme then I offer competitive rates.  Please contact me via \'http://moodle.org/user/profile.php?id=442195\' to discuss your requirements.</p>
</div></div>';

// General.
$string['left'] = 'Left';
$string['right'] = 'Right';

$string['perfinfoheading'] = 'Performance Information';
$string['extperfinfoheading'] = 'Extended Performance Information';
$string['loadtime'] = 'Load Time';
$string['memused'] = 'Memory Used';
$string['peakmem'] = 'Peak Memory';
$string['included'] = 'Files Included';
$string['dbqueries'] = 'DB Read/Write';
$string['dbtime'] = 'DB Queries Time';
$string['serverload'] = 'Server Load';
$string['cachesused'] = 'Cached Used';
$string['sessionsize'] = 'Session Size';

$string['visibleadminonly'] = 'Blocks moved into the area below will only be seen by admins';
$string['backtotop'] = 'Back to top';

$string['nextsection'] = 'Next section';
$string['previoussection'] = 'Previous section';

$string['pagewidth'] = 'Set page width';
$string['pagewidthdesc'] = 'Choose from the list of available page widths for your site.';
$string['fixedwidthwide'] = 'Fixed width - Wide';
$string['fixedwidthnormal'] = 'Fixed width - Normal';
$string['fixedwidthnarrow'] = 'Fixed width - Narrow';
$string['variablewidth'] = 'Variable width';

$string['alwaysdisplay'] = 'Always show';
$string['displaybeforelogin'] = 'Show before login only';
$string['displayafterlogin'] = 'Show after login only';
$string['dontdisplay'] = 'Never show';

// Regions.
$string['region-side-post'] = 'Right';
$string['region-side-pre'] = 'Left';
$string['region-header'] = 'Header';
$string['region-home'] = 'Home';
$string['region-page-top'] = 'Page top';
$string['region-footer-left'] = 'Footer (Left)';
$string['region-footer-middle'] = 'Footer (Middle)';
$string['region-footer-right'] = 'Footer (Right)';
$string['region-hidden-dock'] = 'Hidden from users';

// Sponsor.
$string['sponsor_title'] = 'Sponsor Essential';
$string['sponsor_desc'] = 'Please sponsor via PayPal by contacting me via my \'';
$string['sponsor_desc2'] = ' to keep the Essential development going, or simply to express your gratitude.';
$string['paypal_desc'] = '{$a->url}\' for details as I am an individual and therefore am unable to have \'buy me now\' buttons under their terms or ';
$string['paypal_click'] = 'Moodle profile';

// Readme.
$string['readme_title'] = 'Essential read-me';
$string['readme_desc'] = 'Please {$a->url} file, which contains more information about the Essential theme including customisation.';
$string['readme_click'] = 'click here for the README.txt';

// Advert.
$string['advert_heading'] = 'Theme Design Level 1';
$string['advert_tagline'] = 'Want to know how to customise themes, but do not have a background in development, experience with php, and experience with Moodle Themes, then \'MoodleBites Theme Design Level 1\' is for you!  It will give you a gentle introduction to Moodle Theme development, and provide a good grounding should you wish to progress further.  Please click here for more information.';
$string['advert_alttext'] = 'Theme Design Level 1 advertising banner';

// General settings.
$string['genericsettings'] = 'General';
$string['generalheadingsub'] = 'General settings';
$string['generalheadingdesc'] = 'Configure the general settings for the theme here.';

$string['flatnavigation'] = 'Enable flat navigation.';
$string['flatnavigationdesc'] = 'If enabled flat navigation will be used instead of the navigation and settings blocks.';
$string['coursesettingstitle'] = 'Course settings';
$string['coursecategorysettingstitle'] = 'Course category settings';
$string['frontpagesettingstitle'] = 'Frontpage settings';
$string['modulesettingstitle'] = 'Module settings';
$string['usersettingstitle'] = 'User settings';

$string['pagebackground'] = 'Page background image';
$string['pagebackgrounddesc'] = 'Upload your own background image.  Select the style of the image below.';
$string['pagebackgroundstyle'] = 'Page background style';
$string['pagebackgroundstyledesc'] = 'Select the style for the uploaded image.';
$string['stylecover'] = 'Cover';
$string['stylefixed'] = 'Fixed';
$string['stylestretch'] = 'Stretch';
$string['styletiled'] = 'Tiled';

$string['pagetopblocks'] = 'Enable additional page \'Page top\' blocks';
$string['pagetopblocksdesc'] = 'If enabled this will display an additional block location beside the side blocks and above the content area on all pages except the \'Front page\' which has its own setting.  Note: The number of blocks per row depends on the setting \'pagetopblocksperrow\'.';
$string['pagetopblocksperrow'] = 'Page top blocks per row';
$string['pagetopblocksperrowdesc'] = 'State up to how many blocks per row between {$a->lower} and {$a->upper} for pages with \'Page top blocks\'.  Current pages are: Course, Course Category, Dashboard, My Public and Print.';
$string['pagebottomblocksperrow'] = 'Page bottom blocks per row';
$string['pagebottomblocksperrowdesc'] = 'State up to how many blocks per row between {$a->lower} and {$a->upper} for pages with \'Page bottom blocks\'.  Current pages are: Admin, Course management, Grading and Quiz edit.';

$string['logo'] = 'Logo';
$string['logodesc'] = 'Please upload your custom logo here if you want to add it to the header.<br>The image will be scaled to fit into the available percentage width specified for the device below.  This gives a responsive solution.  If you have a lot of social / app icons then do double check the result.<br>If you upload a logo it will replace the standard icon and name that was displayed by default.';

$string['logodesktopwidth'] = 'Logo desktop width';
$string['logodesktopwidthdesc'] = 'The width of the logo image container on a desktop, >= 980px.<br>Please state as a percentage of the available space between {$a->lower} and {$a->upper}.<br>The available space is the width allowed by the \'pagewidth\' setting.  The image will fill the stated percentage up to its resolution width.<br>Note:  The minimum height of the container is 64 pixels, so there will be a bottom gap if the image height is calculated to be less.';

$string['logomobilewidth'] = 'Logo mobile width';
$string['logomobilewidthdesc'] = 'The width of the logo image container on a mobile, < 980px.<br>Please state as a percentage of the available space between {$a->lower} and {$a->upper}.<br>The available space is the width allowed by the \'pagewidth\' setting.  The image will fill the stated percentage up to its resolution width.<br>Note:  The minimum height of the container is 52 pixels, so there will be a bottom gap if the image height is calculated to be less.';

$string['logodimerror'] = ' is invalid.  Please state \'px\' or \'em\' immediately after the unit value and nothing before the unit value.';

$string['profilebarcustomtitle'] = 'Profile bar custom block title';
$string['profilebarcustomtitledesc'] = 'Title for custom profile bar block.';

$string['contactinfo'] = 'Contact information';
$string['contactinfodesc'] = 'Enter your contact information';

$string['userimageborderradius'] = 'User picture border radius';
$string['userimageborderradiusdesc'] = 'Specify the border radius between {$a->lower} and {$a->upper} pixels of the user picture throughout the site execept the header which uses the setting \'usermenuuserimageborderradius\'.';

$string['favicon'] = 'Custom favicon';
$string['favicondesc'] = 'Upload your own favicon.  It should be an .ico file.';

$string['customcss'] = 'Custom CSS';
$string['customcssdesc'] = 'Whatever CSS rules you add to this text area will be reflected in every page, making for easier customisation of this theme.';

// Courses menu.
$string['mycoursesinfo'] = 'Courses menu';
$string['mycoursesinfodesc'] = 'Displays a dynamic list of enrolled courses to the user.';
$string['displaymycourses'] = 'Display courses';
$string['displaymycoursesdesc'] = 'Display enrolled courses for users on the \'Navbar\'.';
$string['displayhiddenmycourses'] = 'Display hidden courses';
$string['displayhiddenmycoursesdesc'] = 'Display hidden courses for users in the \'Courses menu\' if they have permission to view hidden courses';
$string['mycoursescatsubmenu'] = 'Category and course sub-menu';
$string['mycoursescatsubmenudesc'] = 'Organise courses into a sub-menu based upon the top level category they are in.  When \'enablecategoryicon\' is \'false\' then the category icon used will be the theme default, when \'true\' then will be either the value of \'defaultcategoryicon\' or the category\'s itself if \'enablecustomcategoryicon\' is true.  The \'mycoursesmax\' setting will still apply but on a per-category level.';

$string['mycoursesorder'] = 'Courses order';
$string['mycoursesorderdesc'] = 'State how the courses should be ordered.  The course sort order can be is set by the core navigation setting \'navsortmycoursessort\'.';
$string['mycoursesordersort'] = 'Course sort order';
$string['mycoursesorderid'] = 'Course ID';
$string['mycoursesorderlast'] = 'Last accessed time or enrolment start time if never accessed';
$string['mycoursesorderidorder'] = 'Course ID order';
$string['mycoursesorderidorderdesc'] = 'Course ID order for when \'Course ID\' is set as the \'Course sort order\'.';
$string['mycoursesorderidasc'] = 'Ascending';
$string['mycoursesorderiddes'] = 'Descending';
$string['mycoursesmax'] = 'Max courses';
$string['mycoursesmaxdesc'] = 'State up to how many courses should be listed between {$a->lower} and {$a->upper} where \'{$a->lower}\' represents all.';
$string['mycoursesorderenrolbackcolour'] = 'Enrolled and not accessed course background colour';
$string['mycoursesorderenrolbackcolourdesc'] = 'The background colour for enrolled but not accessed courses.  For ehen \'mycoursesorder\' is set to \'Last accessed...\'.';

$string['mycoursetitle'] = 'Terminology';
$string['mycoursetitledesc'] = 'Change the terminology for the "My courses" menu title.  When \'mycoursesorder\' is set to \'Last accessed...\' then the word \'latest\' will be added.';
$string['mycourses'] = 'My courses';
$string['mylatestcourses'] = 'My latest courses';
$string['myunits'] = 'My units';
$string['mylatestunits'] = 'My latest units';
$string['mymodules'] = 'My modules';
$string['mylatestmodules'] = 'My latest modules';
$string['myclasses'] = 'My classes';
$string['mylatestclasses'] = 'My latest classes';
$string['allcourses'] = 'All courses';
$string['allunits'] = 'All units';
$string['allmodules'] = 'All modules';
$string['allclasses'] = 'All classes';
$string['noenrolments'] = 'You have no current enrolments';
$string['thiscourse'] = 'This course';
$string['people'] = 'People';

// User menu.
$string['usermenuuserimageborderradius'] = 'User menu picture border radius';
$string['usermenuuserimageborderradiusdesc'] = 'Specify the border radius between {$a->lower} and {$a->upper} pixels of the user picture on the user menu.';
$string['helplinktype'] = 'Enable help link in menu';
$string['helplinktypedesc'] = 'Choose whether you want to enable a help option in the user menu, you can choose to either provide an URL that will be opened in a new window or an email address.';
$string['helplink'] = 'Help link';
$string['helplinkdesc'] = 'If you chose URL above fill in the complete URL to your help site (must include http:// or https://). If you chose Email address fill in your email address.';

$string['usermenu'] = 'User menu';
$string['usermenudesc'] = 'The menu for the user.';
$string['loggedinas'] = ' logged in as ';
$string['loggedinfrom'] = 'Logged in from ';

$string['mygrades'] = 'My grades';
$string['coursegrades'] = 'Course grades';

$string['gotobottom'] = 'Go to the bottom of the page';

// Breadcrumb Style.
$string['breadcrumbstyle'] = 'Breadcrumb style';
$string['breadcrumbstyledesc'] = 'Here you can change the style of the breadcrumbs.';
$string['breadcrumbstyled'] = 'Fancy';
$string['breadcrumbstylednocollapse'] = 'Fancy with no collapse';
$string['breadcrumbsimple'] = 'Simple';
$string['breadcrumbthin'] = 'Thin';
$string['nobreadcrumb'] = 'Hide';

// Features.
$string['featureheading'] = 'Features';
$string['featureheadingsub'] = 'Set the features used in your theme';
$string['featuredesc'] = 'Here you can find various settings to change many of the features found in this theme.';

$string['customscrollbars'] = 'Custom scrollbars';
$string['customscrollbarsdesc'] = 'Use custom scrollbars. This will replace the standard browser scrollbars.';

$string['coursecontentsearch'] = 'Course content search';
$string['coursecontentsearchdesc'] = "Enable course content search on the 'Dashboard' page.  Only works when Essential is not in '\$CFG->themedir'.";

$string['fitvids'] = 'Use FitVids';
$string['fitvidsdesc'] = 'Enable FitVids (fitvidsjs.com) to make your embedded videos responsive.  If FitVids is on and you want a video to be excluded then add \'class="fitvidsignore"\' to the \'iframe\' tag in the HTML mode of the editor.  For example: \'iframe class="fitvidsignore" width="420" height="315" src="//www.youtube.com/embed/enmEmym85xc" frameborder="0" allowfullscreen=""></iframe\'.';

$string['floatingsubmitbuttons'] = 'Floating submit buttons';
$string['floatingsubmitbuttonsdesc'] = 'Have a \'floating\' area that contains the buttons used when submitting a form on desktop devices.  This helps to reduce scrolling on some pages.  Not used on course enrolment or forum posts.';

$string['layout'] = 'Use a standard course layout';
$string['layoutdesc'] = 'This theme is designed to put both block columns on the side.  If you prefer the standard Moodle course layout you can check this box and be returned to the old three column layout.';

$string['coursetitleposition'] = 'Course title postition';
$string['coursetitlepositiondesc'] = 'Choose between \'Above\' and \'Within\' for the course.  Where \'Above\' is above the side-pre, page-top and course-content regions and \'Within\' is within the course-content region as it was before this setting was introduced.';
$string['above'] = 'Above';
$string['within'] = 'Within';

$string['categoryincoursebreadcrumbfeature'] = 'Categories in the course breadcrumb';
$string['categoryincoursebreadcrumbfeaturedesc'] = 'Show the category links in the breadcrumb of the course.';

$string['returntosectionfeature'] = 'Return to section';
$string['returntosectionfeaturedesc'] = "Enable return to section feature within course modules.";

$string['returntosectiontextlimitfeature'] = 'Return to section name text limit';
$string['returntosectiontextlimitfeaturedesc'] = 'Length limit for the \'name\' of the section on the button between {$a->lower} and {$a->upper} characters.';

$string['loginbackground'] = 'Login background image';
$string['loginbackgrounddesc'] = 'Upload your own login background image.  Select the style of the image below.';
$string['loginbackgroundstyle'] = 'Login background style';
$string['loginbackgroundstyledesc'] = 'Select the style for the uploaded image.';
$string['loginbackgroundopacity'] = 'Login box background opacity when there is a background image';
$string['loginbackgroundopacitydesc'] = 'Lofin background opacity for the login box when there is a background image.';

// Colours.
$string['colorheading'] = 'Colour';
$string['colorheadingsub'] = 'Set the colours used in your theme';
$string['colordesc'] = 'Here you can find various settings to change many of the colours found in this theme.';

$string['footercolors'] = 'Footer colours';
$string['footercolorsdesc'] = 'Change the colours on the page footer.';

$string['themecolor'] = 'Theme colour';
$string['themecolordesc'] = 'What colour should your theme be.  This will change multiple components to produce the colour you wish across the Moodle site';

$string['themetextcolor'] = 'Text colour';
$string['themetextcolordesc'] = 'Set the colour for your text.';
$string['themeurlcolor'] = 'Link colour';
$string['themeurlcolordesc'] = 'Set the colour for your linked text.';
$string['themehovercolor'] = 'Theme hover colour';
$string['themehovercolordesc'] = 'What colour should your theme hovers be. This is used for links, menus, etc.';
$string['themedefaultbuttontextcolour'] = 'Default button text colour';
$string['themedefaultbuttontextcolourdesc'] = 'Set the text colour for all default buttons.';
$string['themedefaultbuttontexthovercolour'] = 'Default button text hover colour';
$string['themedefaultbuttontexthovercolourdesc'] = 'Set the text hover colour for all default buttons.';
$string['themedefaultbuttonbackgroundcolour'] = 'Default button background colour';
$string['themedefaultbuttonbackgroundcolourdesc'] = 'Set the background colour for all default buttons.';
$string['themedefaultbuttonbackgroundhovercolour'] = 'Default button background hover colour';
$string['themedefaultbuttonbackgroundhovercolourdesc'] = 'Set the background hover colour for all default buttons.';
$string['themeiconcolor'] = 'Icon colour';
$string['themeiconcolordesc'] = 'Set the colour for all icons.';
$string['themesidepreblockbackgroundcolour'] = '\'side-pre\' block background colour';
$string['themesidepreblockbackgroundcolourdesc'] = 'Set the background colour for the \'side-pre\' block.';
$string['themesidepreblocktextcolour'] = '\'side-pre\' block text colour';
$string['themesidepreblocktextcolourdesc'] = 'Set the text colour for the \'side-pre\' block.';
$string['themesidepreblockurlcolour'] = '\'side-pre\' block link colour';
$string['themesidepreblockurlcolourdesc'] = 'Set the link colour for the \'side-pre\' block.';
$string['themesidepreblockhovercolour'] = '\'side-pre\' link hover background colour';
$string['themesidepreblockhovercolourdesc'] = 'Set the link hover colour for the \'side-pre\' block.';
$string['themenavcolor'] = 'Navigation text colour';
$string['themenavcolordesc'] = 'Set the text colour for navigation.  Being the navigation bar and the breadcrumb fancy style.';
$string['themestripetextcolour'] = 'Stripe text colour';
$string['themestripetextcolourdesc'] = 'Set the text colour for stripes in tables.';
$string['themestripeurlcolour'] = 'Stripe url colour';
$string['themestripeurlcolourdesc'] = 'Set the url colour for stripes in tables.';
$string['themestripebackgroundcolour'] = 'Stripe background colour';
$string['themestripebackgroundcolourdesc'] = 'Set the background colour for stripes in tables.';
$string['themequizsubmittextcolour'] = 'Quiz \'Submit all and finish\' text colour';
$string['themequizsubmittextcolourdesc'] = 'Set the text colour for the quiz \'Submit all and finish\' button.';
$string['themequizsubmitbackgroundcolour'] = 'Quiz \'Submit all and finish\' background colour';
$string['themequizsubmitbackgroundcolourdesc'] = 'Set the background colour for the quiz \'Submit all and finish\' button.';
$string['themequizsubmittexthovercolour'] = 'Quiz \'Submit all and finish\' text hover colour';
$string['themequizsubmittexthovercolourdesc'] = 'Set the text hover colour for the quiz \'Submit all and finish\' button.';
$string['themequizsubmitbackgroundhovercolour'] = 'Quiz \'Submit all and finish\' background hover colour';
$string['themequizsubmitbackgroundhovercolourdesc'] = 'Set the background hover colour for the quiz \'Submit all and finish\' button.';

$string['footercolor'] = 'Footer background colour';
$string['footercolordesc'] = 'Set what colour the background of the Footer box should be.';
$string['footersepcolor'] = 'Footer separator colour';
$string['footersepcolordesc'] = 'Separators are lines used to separate content.  Set their colour here.';
$string['footertextcolor'] = 'Footer text colour';
$string['footertextcolordesc'] = 'Set the colour you want your text to be in the footer.';
$string['footerurlcolor'] = 'Footer link colour';
$string['footerurlcolordesc'] = 'Set the colour for your linked text in the footer.';
$string['footerhovercolor'] = 'Footer link hover colour';
$string['footerhovercolordesc'] = 'Set the colour for your linked text when hovered over in the footer.';
$string['footerblockbackgroundcolour'] = 'Footer block background colour';
$string['footerblockbackgroundcolourdesc'] = 'Set the colour for the block background in the footer.';
$string['footerheadingcolor'] = 'Footer heading colour';
$string['footerheadingcolordesc'] = 'Set the colour for headings in the footer.';
$string['footerblocktextcolour'] = 'Footer block text colour';
$string['footerblocktextcolourdesc'] = 'Set the colour you want your block text to be in the footer.';
$string['footerblockurlcolour'] = 'Footer block link colour';
$string['footerblockurlcolourdesc'] = 'Set the colour for your linked block text in the footer.';
$string['footerblockhovercolour'] = 'Footer block link hover colour';
$string['footerblockhovercolourdesc'] = 'Set the colour for your linked block text when hovered over in the footer.';

// Alternate Colour Switcher.
$string['themecolors'] = 'Theme colours';
$string['defaultcolors'] = 'Default colours';
$string['alternativecolors'] = 'Alternative colours {$a}';
$string['alternativethemecolor'] = 'Alternative theme colour {$a}';
$string['alternativethemecolordesc'] = 'What colour should your theme be for the alternative theme colours {$a}.';
$string['alternativethemename'] = 'Colour scheme name';
$string['alternativethemenamedesc'] = 'Provide a name for your alternative theme colours';
$string['alternativethemecolors'] = 'Alternative theme colours';
$string['alternativethemecolorsdesc'] = 'Defines alternative theme colours that the user may select.';
$string['alternativethemecolorname'] = 'Name of alternative colour set {$a}';
$string['alternativethemecolornamedesc'] = 'Provide a recognisable name for this set of alternative theme colours';
$string['alternativethemetextcolor'] = 'Alternative text colour {$a}';
$string['alternativethemetextcolordesc'] = 'Set the colour for your alternative text {$a}.';
$string['alternativethemeurlcolor'] = 'Alternative link colour {$a}';
$string['alternativethemeurlcolordesc'] = 'Set the colour for your alternative linked text {$a}.';
$string['alternativethemedefaultbuttontextcolour'] = 'Default button text colour {$a}';
$string['alternativethemedefaultbuttontextcolourdesc'] = 'Set the text colour for all default buttons {$a}.';
$string['alternativethemedefaultbuttontexthovercolour'] = 'Default button hover text colour {$a}';
$string['alternativethemedefaultbuttontexthovercolourdesc'] = 'Set the hover text colour for all default buttons {$a}.';
$string['alternativethemedefaultbuttonbackgroundcolour'] = 'Default button background colour {$a}';
$string['alternativethemedefaultbuttonbackgroundcolourdesc'] = 'Set the background colour for all default buttons {$a}.';
$string['alternativethemedefaultbuttonbackgroundhovercolour'] = 'Default button background hover colour {$a}';
$string['alternativethemedefaultbuttonbackgroundhovercolourdesc'] = 'Set the background hover colour for all default buttons {$a}.';
$string['alternativethemeiconcolor'] = 'Alternative icon colour {$a}';
$string['alternativethemeiconcolordesc'] = 'Set the alternative {$a} colour for all icons.';
$string['alternativethemesidepreblockbackgroundcolour'] = 'Alternative {$a} \'side-pre\' block background colour';
$string['alternativethemesidepreblockbackgroundcolourdesc'] = 'Set the alternative {$a} background colour for the \'side-pre\' block.';
$string['alternativethemesidepreblocktextcolour'] = 'Alternative {$a} \'side-pre\' block text colour';
$string['alternativethemesidepreblocktextcolourdesc'] = 'Set the alternative {$a} text colour for the \'side-pre\' block.';
$string['alternativethemesidepreblockurlcolour'] = 'Alternative {$a} \'side-pre\' block link colour';
$string['alternativethemesidepreblockurlcolourdesc'] = 'Set the alternative {$a} link colour for the \'side-pre\' block.';
$string['alternativethemesidepreblockhovercolour'] = 'Alternative {$a} \'side-pre\' block link hover colour';
$string['alternativethemesidepreblockhovercolourdesc'] = 'Set the alternative {$a} link hover colour for the \'side-pre\' block.';
$string['alternativethemenavcolor'] = 'Navigation text colour {$a}';
$string['alternativethemenavcolordesc'] = 'Set the alternative {$a} text colour for navigation.  Being the navigation bar and the breadcrumb fancy style.';
$string['alternativethemehovercolor'] = 'Alternative theme hover colour {$a}';
$string['alternativethemehovercolordesc'] = 'What colour should your theme hovers be for the alternative theme colours {$a}.';
$string['alternativethemestripetextcolour'] = 'Alternative stripe text colour {$a}';
$string['alternativethemestripetextcolourdesc'] = 'Set the alternative {$a} text colour for stripes in tables.';
$string['alternativethemestripeurlcolour'] = 'Alternative stripe url colour {$a}';
$string['alternativethemestripeurlcolourdesc'] = 'Set the alternative {$a} url colour for stripes in tables.';
$string['alternativethemestripebackgroundcolour'] = 'Alternative stripe background colour {$a}';
$string['alternativethemestripebackgroundcolourdesc'] = 'Set the alternative {$a} background colour for stripes in tables.';
$string['alternativethemequizsubmittextcolour'] = 'Quiz \'Submit all and finish\' text colour {$a}';
$string['alternativethemequizsubmittextcolourdesc'] = 'Set the alternative {$a} text colour for the quiz \'Submit all and finish\' button.';
$string['alternativethemequizsubmitbackgroundcolour'] = 'Quiz \'Submit all and finish\' background colour {$a}';
$string['alternativethemequizsubmitbackgroundcolourdesc'] = 'Set the alternative {$a} background colour for the quiz \'Submit all and finish\' button.';
$string['alternativethemequizsubmittexthovercolour'] = 'Quiz \'Submit all and finish\' text hover colour {$a}';
$string['alternativethemequizsubmittexthovercolourdesc'] = 'Set the alternative {$a} text hover colour for the quiz \'Submit all and finish\' button.';
$string['alternativethemequizsubmitbackgroundhovercolour'] = 'Quiz \'Submit all and finish\' background hover colour {$a}';
$string['alternativethemequizsubmitbackgroundhovercolourdesc'] = 'Set the alternative {$a} background hover colour for the quiz \'Submit all and finish\' button.';

$string['alternativethememycoursesorderenrolbackcolour'] = 'Alternative {$a} enrolled and not accessed course background colour';
$string['alternativethememycoursesorderenrolbackcolourdesc'] = 'Set alternative {$a} background colour for enrolled but not accessed courses.  For ehen \'mycoursesorder\' is set to \'Last accessed...\'.';

$string['alternativethemefootercolor'] = 'Footer background colour for the alternative theme colours {$a}';
$string['alternativethemefootercolordesc'] = 'Set what colour the background of the Footer box should be for the alternative theme colours {$a}.';
$string['alternativethemefootersepcolor'] = 'Footer separator colour for the alternative theme colours {$a}';
$string['alternativethemefootersepcolordesc'] = 'Separators are lines used to separate content.  Set their colour here for the alternative theme colours {$a}.';
$string['alternativethemefootertextcolor'] = 'Footer text colour for the alternative theme colours {$a}';
$string['alternativethemefootertextcolordesc'] = 'Set the colour you want your text to be in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterheadingcolor'] = 'Footer heading colour for the alternative theme colours {$a}';
$string['alternativethemefooterheadingcolordesc'] = 'Set the colour for headings in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterurlcolor'] = 'Footer link colour for the alternative theme colours {$a}';
$string['alternativethemefooterurlcolordesc'] = 'Set the colour for your linked text in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterhovercolor'] = 'Footer link hover colour for the alternative theme colours {$a}';
$string['alternativethemefooterhovercolordesc'] = 'Set the colour for your linked text when hovered over in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterblockbackgroundcolour'] = 'Footer block background colour for the alternative theme colours {$a}';
$string['alternativethemefooterblockbackgroundcolourdesc'] = 'Set the colour for the block background in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterblocktextcolour'] = 'Footer block text colour for the alternative theme colours {$a}';
$string['alternativethemefooterblocktextcolourdesc'] = 'Set the colour you want your block text to be in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterblockurlcolour'] = 'Footer block link colour for the alternative theme colours {$a}';
$string['alternativethemefooterblockurlcolourdesc'] = 'Set the colour for your linked block text in the footer for the alternative theme colours {$a}.';
$string['alternativethemefooterblockhovercolour'] = 'Footer block link hover colour for the alternative theme colours {$a}';
$string['alternativethemefooterblockhovercolourdesc'] = 'Set the colour for your linked block text when hovered over in the footer for the alternative theme colours {$a}.';

$string['alternativethemeslidecolors'] = 'Alternative theme slide colours';
$string['alternativethemeslidecolorsdesc'] = 'Defines alternative theme slide colours that the user may select.';
$string['alternativethemeslidecaptiontextcolor'] = 'Alternative theme slide caption text colour {$a}';
$string['alternativethemeslidecaptiontextcolordesc'] = 'What colour should your theme slide caption text be for the alternative theme colours {$a}.  Does not apply to \'Beside\' slide caption option.';
$string['alternativethemeslidecaptionbackgroundcolor'] = 'Alternative theme slide caption background colour {$a}';
$string['alternativethemeslidecaptionbackgroundcolordesc'] = 'What colour should your theme slide caption background be for the alternative theme colours {$a}.  Does not apply to \'Beside\' slide caption option.';
$string['alternativethemeslidebuttoncolor'] = 'Alternative theme slide button colour {$a}';
$string['alternativethemeslidebuttoncolordesc'] = 'What colour should your theme slide button be for the alternative theme colours {$a}.';
$string['alternativethemeslidebuttonhovercolor'] = 'Alternative theme slide button hover colour {$a}';
$string['alternativethemeslidebuttonhovercolordesc'] = 'What hover colour should your theme slide button be for the alternative theme colours {$a}.';
$string['enablealternativethemecolors'] = 'Enable alternative theme colours {$a}';
$string['enablealternativethemecolorsdesc'] = 'If enabled, the user will be able to choose the alternative theme colours {$a}.';

// Frontpage Settings.
$string['frontpageheading'] = 'Front page';
$string['frontpageheadingdesc'] = 'Configure here what additional items you want to show on the front page.';

$string['courselistteachericon'] = 'Course list teacher icon';
$string['courselistteachericondesc'] = 'Name of the icon you wish to use or empty for none.  List is <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';

$string['frontcontentheading'] = 'Front page content area';
$string['frontcontent'] = 'Enable front page content area';
$string['frontcontentdesc'] = 'If enabled this adds a custom content area between the \'Slide show\' and the \'Marketing boxes\' for your own custom content.';
$string['frontcontentarea'] = 'Front page content area contents';
$string['frontcontentareadesc'] = 'Whatever is typed into this box will display across the full width of the page in between the slide show and the Marketing spots.';

$string['frontpageblocksheading'] = 'Front page blocks';
$string['frontpageblocks'] = 'Front page blocks alignment';
$string['frontpageblocksdesc'] = 'Here you can determine if the standard Moodle blocks on the frontpage align before or after the content.';
$string['beforecontent'] = 'Before content';
$string['aftercontent'] = 'After content';

$string['frontpagemiddleblocks'] = 'Enable additional front page \'Home\' (was \'Middle\') blocks';
$string['frontpagemiddleblocksdesc'] = 'If enabled this will display an additional block location just under the marketing spots.';
$string['frontpagehomeblocksperrow'] = 'Home (was \'Middle\') blocks per row';
$string['frontpagehomeblocksperrowdesc'] = 'State up to how many blocks per row between {$a->lower} and {$a->upper} for the \'Home blocks\' block region.';
$string['fppagetopblocks'] = 'Enable additional front page \'Page top\' blocks';
$string['fppagetopblocksdesc'] = 'If enabled this will display an additional block location beside the side blocks and above the content area.  Note: The number of blocks per row depends on the setting \'fppagetopblocksperrow\'.';
$string['fppagetopblocksperrow'] = 'Page top blocks per row';
$string['fppagetopblocksperrowdesc'] = 'State up to how many blocks per row between {$a->lower} and {$a->upper} for the \'Page top\' block region on the front page.';

// Slideshow.
$string['slideshowheading'] = 'Slide show';
$string['slideshowheadingsub'] = 'Dynamic slide show for the front page';
$string['slideshowdesc'] = 'This creates a dynamic slide show of up to sixteen slides for you to promote important elements of your site.  The show is responsive where image height is set according to screen size.  The recommended height is 300px.  The width is set at 100% and therefore the actual height will be smaller if the width is greater than the screen size.  At smaller screen sizes the height is reduced dynamically without the need to provide separate images.  For reference screen width < 767px = height 165px, width between 768px and 979px = height 225px and width > 980px = height 300px.  If no image is selected for a slide, then the default_slide image in the pix folder is used.';

$string['toggleslideshow'] = 'Toggle slide show display';
$string['toggleslideshowdesc'] = 'Choose if you wish to hide or show the slide show.';

$string['numberofslides'] = 'Number of slides';
$string['numberofslides_desc'] = 'Number of slides on the slider.';

$string['hideonphone'] = 'Hide slide show on mobiles';
$string['hideonphonedesc'] = 'Choose if you wish to disable slide show on mobiles.';

$string['hideontablet'] = 'Hide slide show on tablets';
$string['hideontabletdesc'] = 'Choose if you wish to disable the slide show on tablets.';

$string['readmore'] = 'Read more';

$string['slideinterval'] = 'Slide interval';
$string['slideintervaldesc'] = 'Slide transition interval in milliseconds.';

// New...
$string['slidecaptiontextcolor'] = 'Slide caption text colour';
$string['slidecaptiontextcolordesc'] = 'What colour the slide caption text should be.  Does not apply to \'Beside\' slide caption option.';
$string['slidecaptionbackgroundcolor'] = 'Slide caption background colour';
$string['slidecaptionbackgroundcolordesc'] = 'What colour the slide caption background should be.  Does not apply to \'Beside\' slide caption option.';

// Old...
$string['slidecolor'] = 'Slide text colour';
$string['slidecolordesc'] = 'What colour the slide caption text should be.';

$string['slidecaptionoptions'] = 'Slide caption options';
$string['slidecaptionoptionsdesc'] = 'Where the captions should appear in relation to the image.';
$string['slidecaptionbeside'] = 'Beside';
$string['slidecaptionontop'] = 'On top';
$string['slidecaptionunderneath'] = 'Underneath';

// Backward compatibility.
$string['slidecaptionbelow'] = 'Slide caption below image';
$string['slidecaptionbelowdesc'] = 'If the slide caption should be below the image.';

$string['slidecaptioncentred'] = 'Slide caption centred';
$string['slidecaptioncentreddesc'] = 'If the slide caption should be centred.';

$string['slidebuttoncolor'] = 'Slide button colour';
$string['slidebuttoncolordesc'] = 'What colour the slide navigation button should be.';
$string['slidebuttonhovercolor'] = 'Slide button hover colour';
$string['slidebuttonhovercolordesc'] = 'What colour the slide navigation button hover should be.';

$string['slideno'] = 'Slide {$a->slide}';
$string['slidenodesc'] = 'Enter the settings for slide {$a->slide}.';
$string['slidetitle'] = 'Slide title';
$string['slidetitledesc'] = 'Enter a descriptive title for your slide';
$string['noslidetitle'] = 'No title for slide {$a->slide}';
$string['slideimage'] = 'Slide image';
$string['slideimagedesc'] = 'Image works best if it is transparent.';
$string['slidecaption'] = 'Slide caption';
$string['slidecaptiondesc'] = 'Enter the caption text to use for the slide';
$string['slideurl'] = 'Slide link';
$string['slideurldesc'] = 'Enter the target destination of the slide\'s image link';
$string['slideurltarget'] = 'Link target';
$string['slideurltargetdesc'] = 'Choose how the link should be opened';
$string['slideurltargetself'] = 'Current page';
$string['slideurltargetnew'] = 'New page';
$string['slideurltargetparent'] = 'Parent frame';

// Marketing Spots.
$string['marketingheading'] = 'Marketing spots';
$string['marketinginfodesc'] = 'Enter the settings for your marketing spot.';
$string['marketingheadingsub'] = 'Three locations on the front page to add information and links.'; // Legacy only.

$string['marketingheight'] = 'Height of marketing spot container (px)';
$string['marketingheightdesc'] = 'Specify the height of the marketing spot container in pixels.  Adjust this to suit your content.  If any spot has an image or link, then that will be added to this for all spots.';
$string['marketingimageheight'] = 'Height of marketing images (px)';
$string['marketingimageheightdesc'] = 'If you want to display images in the marketing boxes you can specify their height in pixels here.';
$string['marketingdesc'] = 'This theme provides the option of enabling three "marketing" or "ad" spots just under the slide show.  These allow you to easily identify core information to your users and provide direct links.';

$string['togglemarketing'] = 'Toggle marketing spot display';
$string['togglemarketingdesc'] = 'Choose if you wish to hide or show the three marketing spots.';

$string['marketing1'] = 'Marketing spot one';
$string['marketing2'] = 'Marketing spot two';
$string['marketing3'] = 'Marketing spot three';

$string['marketingtitle'] = 'Title';
$string['marketingtitledesc'] = 'Title to show in this marketing spot';
$string['marketingicon'] = 'Icon';
$string['marketingicondesc'] = 'Name of the icon you wish to use.  List is <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_new">here</a>.  Just enter what is after "fa-", e.g. "star".';
$string['marketingimage'] = 'Image';
$string['marketingimagedesc'] = 'This provides the option of displaying an image above the text in the marketing spot';
$string['marketingcontent'] = 'Content';
$string['marketingcontentdesc'] = 'Content to display in the marketing box.  Keep it short and sweet.';
$string['marketingbuttontext'] = 'Link text';
$string['marketingbuttontextdesc'] = 'Text to appear on the button.';
$string['marketingbuttonurl'] = 'Link URL';
$string['marketingbuttonurldesc'] = 'URL the button will point to.';
$string['marketingurltarget'] = 'Link target';
$string['marketingurltargetdesc'] = 'Choose how the link should be opened';
$string['marketingurltargetself'] = 'Current page';
$string['marketingurltargetnew'] = 'New page';
$string['marketingurltargetparent'] = 'Parent frame';

// Social Networks.
$string['socialheading'] = 'Social networking';
$string['socialheadingsub'] = 'Engage your users with social networking';
$string['socialdesc'] = 'Provide direct links to the core social networks that promote your brand.  These will appear in the header of every page.';
$string['socialnetworks'] = 'Social networks';

$string['facebook'] = 'Facebook';
$string['facebookurl'] = 'Facebook URL';
$string['facebookdesc'] = 'Enter the URL of your Facebook page. (i.e https://www.facebook.com/mycollege)';

$string['twitter'] = 'Twitter';
$string['twitterurl'] = 'Twitter URL';
$string['twitterdesc'] = 'Enter the URL of your Twitter feed. (i.e https://www.twitter.com/mycollege)';

$string['googleplus'] = 'Google+';
$string['googleplusurl'] = 'Google+ URL';
$string['googleplusdesc'] = 'Enter the URL of your Google+ profile. (i.e https://plus.google.com/+mycollege)';

$string['linkedin'] = 'LinkedIn';
$string['linkedinurl'] = 'LinkedIn URL';
$string['linkedindesc'] = 'Enter the URL of your LinkedIn profile. (i.e https://www.linkedin.com/company/mycollege)';

$string['youtube'] = 'YouTube';
$string['youtubeurl'] = 'YouTube URL';
$string['youtubedesc'] = 'Enter the URL of your YouTube channel. (i.e https://www.youtube.com/user/mycollege)';

$string['flickr'] = 'Flickr';
$string['flickrurl'] = 'Flickr URL';
$string['flickrdesc'] = 'Enter the URL of your Flickr page. (i.e http://www.flickr.com/photos/mycollege)';

$string['vk'] = 'VKontakte';
$string['vkurl'] = 'VKontakte URL';
$string['vkdesc'] = 'Enter the URL of your Vkontakte page. (i.e http://www.vk.com/mycollege)';

$string['skype'] = 'Skype Account';
$string['skypeuri'] = 'Skype Account URI';
$string['skypedesc'] = 'Enter the Skype user name URI of your organisations Skype account (i.e skype://my.college)';

$string['pinterest'] = 'Pinterest';
$string['pinteresturl'] = 'Pinterest URL';
$string['pinterestdesc'] = 'Enter the URL of your Pinterest page. (i.e http://pinterest.com/mycollege/mypinboard)';

$string['instagram'] = 'Instagram';
$string['instagramurl'] = 'Instagram URL';
$string['instagramdesc'] = 'Enter the URL of your Instagram page. (i.e http://instagram.com/mycollege)';

$string['website'] = 'Website';
$string['websiteurl'] = 'Website URL';
$string['websitedesc'] = 'Enter the URL of your own website. (i.e http://about.me/gjbarnard)';


// Category Course Title Image.
$string['categoryctiheading'] = 'Category course title images';
$string['categoryctiheadingcs'] = 'Category course title images configuration';
$string['categoryctiheadingsub'] = 'Use images to represent your categories in a course';
$string['categoryctidesc'] = 'If enabled this will allow you to set images for each category.';

$string['enablecategorycti'] = 'Enable category course title images';
$string['enablecategoryctidesc'] = 'If enabled you will be able to select category course title images after clicking "Save changes".';

$string['enablecategoryctics'] = 'Enable category course title image category setting pages';
$string['enablecategorycticsdesc'] = 'If enabled each top level category will get its own setting page.';

$string['categoryctiheadingcategory'] = 'Category course title images for: {$a->category}';

$string['categoryctiinfo'] = '{$a->category} settings';
$string['categoryctiinfodesc'] = 'Category course title image settings for: {$a->category}.';

$string['categoryctimage'] = 'Category \'{$a->category}\' course title image file';
$string['categoryctimagedesc'] = 'Image file for the course title in category \'{$a->category}\'.';

$string['categoryctimageurl'] = 'Category \'{$a->category}\' course title image URL';
$string['categoryctimageurldesc'] = 'Image URL for the course title in category \'{$a->category}\'.  If the file is uploaded then that will override this.';

$string['categoryctiheight'] = 'Category \'{$a->category}\' course title image height';
$string['categoryctiheightdesc'] = 'Image height for the course title in category \'{$a->category}\' between {$a->lower} and {$a->upper} pixels.  Do not postfix with \'px\', only enter the number.';

$string['categoryctitextcolour'] = 'Category \'{$a->category}\' course title text colour';
$string['categoryctitextcolourdesc'] = 'Text colour for the course title in category \'{$a->category}\'.';

$string['categoryctitextbackgroundcolour'] = 'Category \'{$a->category}\' course title text background colour';
$string['categoryctitextbackgroundcolourdesc'] = 'Text background colour for the course title in category \'{$a->category}\'.';

$string['categoryctitextbackgroundopacity'] = 'Category \'{$a->category}\' course title text background opacity';
$string['categoryctitextbackgroundopacitydesc'] = 'Text background opacity for the course title in category \'{$a->category}\'.';

$string['ctioverride'] = 'Overriding category images in a course';
$string['ctioverridedesc'] = 'If you wish to override the category course title image in a course when this is enabled with the \'enablecategorycti\' setting, then edit the course summary in the course settings and add an image.  Then edit in HTML mode, remove the surrounding \'p\' tags and \'br\' tag, then remove the \'style\', \'width\' and \'height\' attributes and any \'classes\' added by the text editor on the \'img\' tag.  Then add the class \'categorycti\'.  To specifiy the height (px) and the contained title text colour, background colour and opacity, use the following attributes: \'ctih\', \'ctit\', \'ctib\' and \'ctio\' respectively, for example:<br/><br/>&lt;img src=&quot;https://mymoodleinstall.me/pluginfile.php/493/course/section/237/myimage.jpg&quot; alt=&quot;Replacement image&quot; class=&quot;categorycti&quot; ctih=&quot;250&quot; ctit=&quot;#afafaf&quot; ctib=&quot;#222222&quot; ctio=&quot;0.5&quot;&gt;<br/><br/>This image will not be shown in the summary itself when viewing the list of courses.';

$string['ctioverrideheight'] = 'Default overridden course title image height';
$string['ctioverrideheightdesc'] = 'Default overridden image height for the course title between {$a->lower} and {$a->upper} pixels.  Do not postfix with \'px\', only enter the number.';

$string['ctioverridetextcolour'] = 'Default overridden course title text colour';
$string['ctioverridetextcolourdesc'] = 'Default overridden text colour for the course title.';

$string['ctioverridetextbackgroundcolour'] = 'Default overridden course title text background colour';
$string['ctioverridetextbackgroundcolourdesc'] = 'Default overridden text background colour for the course title.';

$string['ctioverridetextbackgroundopacity'] = 'Default overridden course title text background opacity';
$string['ctioverridetextbackgroundopacitydesc'] = 'Default overridden text background opacity for the course title.';

// Category Icons.
$string['categoryiconheading'] = 'Category icons / images';
$string['categoryiconheadingsub'] = 'Use icons to represent your categories';
$string['categoryicondesc'] = 'If enabled this will allow you to set icons / images for each category.';
$string['categoryiconcategory'] = 'The icon for the category: {$a->category}.';
$string['categoryimagecategory'] = 'The image for the category: {$a->category}.';

$string['enablecategoryicon'] = 'Enable category icons / images';
$string['enablecategoryicondesc'] = 'If enabled you will be able to select category icons / images after clicking "Save changes".';

$string['defaultcategoryicon'] = 'Default category icon';
$string['defaultcategoryicondesc'] = 'Set a default category icon.';
$string['defaultcategoryimage'] = 'Default category image';
$string['defaultcategoryimagedesc'] = 'Set a default category image.  Will override the icon when populated.';

$string['enablecustomcategoryicon'] = 'Enable custom category icons / images';
$string['enablecustomcategoryicondesc'] = 'If enabled below this section you will see each category with a customizable option behind each category, please save after enabling and disabling this option.';
$string['icon'] = 'icon';
$string['image'] = 'image';

$string['categoryiconinfo'] = 'Set custom category icons';
$string['categoryiconinfodesc'] = 'Enter the name of the icon or upload an image you wish to use.  List is <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_new">here</a>. Just enter what is after "fa-".';

$string['numberofcoursesandsubcatcourses'] = 'Number of courses - sub category courses';

// Header Settings.
$string['headerheading'] = 'Header';
$string['headertitle'] = 'Header title';
$string['headertitledesc'] = 'Configure here what title to output in the header.  Note: The header title will only be used if there is no logo.';
$string['navbartitle'] = 'Navigation bar title';
$string['navbartitledesc'] = 'Configure here what title to output in the navigation bar.';
$string['notitle'] = 'No Title';
$string['fullname'] = 'Site full name';
$string['shortname'] = 'Site short name';
$string['fullnamesummary'] = 'Full name and summary';
$string['shortnamesummary'] = 'Short name and summary';

$string['oldnavbar'] = 'Use the old navbar position';
$string['oldnavbardesc'] = 'Enable this option to use the old navbar position, placing it below the header.';
$string['navbarabove'] = 'Navbar above the header';
$string['navbarbelow'] = 'Navbar below the header';

$string['dropdownmenuscroll'] = 'Scrollbars on the dropdown menus';
$string['dropdownmenuscrolldesc'] = 'Have a scrollbar on the dropdown menu where the height of the menu is limited.';
$string['dropdownmenumaxheight'] = 'Dropdown menu maximum height';
$string['dropdownmenumaxheightdesc'] = 'Dropdown menu maximum height when scrollbars are enabled.  Between {$a->lower} and {$a->upper} pixels.';

$string['usesiteicon'] = 'Use site icon';
$string['usesiteicondesc'] = 'Use the site icon if there is no logo.';

$string['siteicon'] = 'Site icon';
$string['siteicondesc'] = 'Do not have a logo? Enter the name of the icon you wish to use.  List is <a href="http://fortawesome.github.io/Font-Awesome/cheatsheet/" target="_new">here</a>. Just enter what is after the "fa-".';

$string['headertextcolor'] = 'Header text colour';
$string['headertextcolordesc'] = 'Set the text colour for the header.';

$string['headerbackground'] = 'Header background image';
$string['headerbackgrounddesc'] = 'Upload your own background image.';

$string['headerbackgroundstyle'] = 'Header background style';
$string['headerbackgroundstyledesc'] = 'Select the style for the header background.';

$string['editingmenu'] = 'Page editing switch';
$string['editingmenudesc'] = 'Displays a button to switch the editing of the current page on/off if the user is allowed to edit the page.  The same functionality as the normal page editing button.';
$string['displayeditingmenu'] = 'Display editing button';
$string['displayeditingmenudesc'] = 'Displays a button with the same functionality as the default page editing button on the header.';
$string['hidedefaulteditingbutton'] = 'Hide default page editing button';
$string['hidedefaulteditingbuttondesc'] = 'Hides the default page editing button from any page.  This setting only takes effect if the "Display editing button" setting is enabled.';

$string['haveheaderblock'] = 'Header block region';
$string['haveheaderblockdesc'] = 'Have a header block region just below the breadcrumb.';
$string['headerblocksperrow'] = 'Header blocks per row';
$string['headerblocksperrowdesc'] = 'State up to how many blocks per row between {$a->lower} and {$a->upper} for pages with the \'Header block region\'.';

// Font settings.
$string['fontsettings'] = 'Font';
$string['fontheadingsub'] = 'Font settings';
$string['fontheadingdesc'] = 'Select and enter the fonts that you want to use in your Moodle environment.';
$string['fontselect'] = 'Font type selector';
$string['fontselectdesc'] = 'Choose from the list of available font defining mechanisms:<ul><li>\'User fonts\' are where the font is already installed at the users machine and you just specify its name.</li><li>\'Google web fonts\' are where you find a font on \'{$a->googlewebfonts}\' and specify its name.</li><li>\'Custom fonts\' are where you specify the name and upload the font files for the font.</li></ul>Please save to show the options for your choice.';
$string['fonttypeuser'] = 'User fonts';
$string['fonttypegoogle'] = 'Google web fonts';
$string['fonttypecustom'] = 'Custom fonts';
$string['fontnameheading'] = 'Heading font';
$string['fontnameheadingdesc'] = 'Enter the exact name of the font to use for headings.';
$string['fontnamebody'] = 'Text font';
$string['fontnamebodydesc'] = 'Enter the exact name of the font to use for all other text.';

// Font files.
$string['fontfiles'] = 'Font files';
$string['fontfilesdesc'] = 'Upload your font files here.';
$string['fontfilettfheading'] = 'Heading TTF font file';
$string['fontfileotfheading'] = 'Heading OTF font file';
$string['fontfilewoffheading'] = 'Heading WOFF font file';
$string['fontfilewofftwoheading'] = 'Heading WOFF2 font file';
$string['fontfileeotheading'] = 'Heading EOT font file';
$string['fontfilesvgheading'] = 'Heading SVG font file';
$string['fontfilettfbody'] = 'Body TTF font file';
$string['fontfileotfbody'] = 'Body OTF font file';
$string['fontfilewoffbody'] = 'Body WOFF font file';
$string['fontfilewofftwobody'] = 'Body WOFF2 font file';
$string['fontfileeotbody'] = 'Body EOT font file';
$string['fontfilesvgbody'] = 'Body SVG font file';

$string['fontcharacterset'] = 'Google font additional character set';
$string['fontcharactersetdesc'] = 'Pick additional character sets for different languages.
                                   Using many character sets can slow down your Moodle, so only select the character sets that you actually need.';
$string['fontcharactersetlatinext'] = 'Latin Extended';
$string['fontcharactersetcyrillic'] = 'Cyrillic';
$string['fontcharactersetcyrillicext'] = 'Cyrillic Extended';
$string['fontcharactersetgreek'] = 'Greek';
$string['fontcharactersetgreekext'] = 'Greek Extended';
$string['fontcharactersetvietnamese'] = 'Vietnamese';

// Footer Settings.
$string['footerheading'] = 'Footer';

$string['copyright'] = 'Copyright';
$string['copyrightdesc'] = 'The name of your organisation.';

$string['footnote'] = 'Footnote';
$string['footnotedesc'] = 'Whatever you add to this textarea will be displayed in the footer throughout your Moodle site.';

$string['perfinfo'] = 'Performance information mode';
$string['perfinfodesc'] = 'Many sites don\'t need the fully detailed performance info.  Especially when viewed by users.  When enabled, this shows a cleaned up minimal form with basic page load information.';
$string['perf_max'] = 'Detailed';
$string['perf_min'] = 'Minimal';

// Mobile Apps.
$string['mobileappsheading'] = 'Apps';
$string['mobileappsheadingsub'] = 'Link to your app to get your students using mobiles';
$string['mobileappsdesc'] = 'Have you got a web app on the App Store or Google Play Store?  Provide a link here so your users can grab the apps online.';

$string['android'] = 'Android (Google Play)';
$string['androidurl'] = 'Android (Google Play) URL';
$string['androiddesc'] = 'Provide an URL to your mobile App on the Google Play Store.  If you do not have one of your own maybe consider linking to the official Moodle Mobile app. (https://play.google.com/store/apps/details?id=com.moodle.moodlemobile)';

$string['windows'] = 'Windows Desktop';
$string['windowsurl'] = 'Windows Desktop URL';
$string['windowsdesc'] = 'Provide an URL to your mobile App on the Windows Store.  If you do not have one of your own maybe consider linking to the official Moodle Mobile app. (http://apps.microsoft.com/windows/en-us/app/9df51338-015c-41b7-8a85-db2fdfb870bc)';

$string['winphone'] = 'Windows Mobile';
$string['winphoneurl'] = 'Windows Mobile URL';
$string['winphonedesc'] = 'Provide an URL to your mobile App on the Google Play Store.  If you do not have one of your own maybe consider linking to the official Moodle Mobile app. (http://www.windowsphone.com/en-us/store/app/moodlemobile/d0732b88-3c6d-4127-8f24-3fca2452a4dc)';

$string['ios'] = 'iPhone/iPad (App Store)';
$string['iosurl'] = 'iPhone/iPad (App Store) URL';
$string['iosdesc'] = 'Provide an URL to your mobile App on the App Store.  If you do not have one of your own maybe consider linking to the official Moodle Mobile app (https://itunes.apple.com/en/app/moodle-mobile/id633359593).';

// The iOS Icons.
$string['iosicon'] = 'iOS home screen icons';
$string['iosicondesc'] = 'The theme does provide a default icon for iOS and android home screens.  You can upload your custom icons if you wish.';

$string['iphoneicon'] = 'iPhone/iPod Touch icon (Non Retina)';
$string['iphoneicondesc'] = 'Icon should be a PNG files sized 57px by 57px.';

$string['iphoneretinaicon'] = 'iPhone/iPod Touch icon (Retina)';
$string['iphoneretinaicondesc'] = 'Icon should be a PNG files sized 114px by 114px.';

$string['ipadicon'] = 'iPad Icon (Non retina)';
$string['ipadicondesc'] = 'Icon should be a PNG files sized 72px by 72px.';

$string['ipadretinaicon'] = 'iPad Icon (Retina)';
$string['ipadretinaicondesc'] = 'Icon should be a PNG files sized 144px by 144px.';

// Properties.
$string['properties'] = 'Properties';
$string['propertiessub'] = 'The properties';
$string['propertiesdesc'] = 'List of properties.';
$string['propertiesproperty'] = 'Property';
$string['propertiesvalue'] = 'Value';
$string['propertiesexport'] = 'Export properties as a JSON string';
$string['propertiesreturn'] = 'Return';
$string['putpropertiesname'] = 'Put properties - Experimental!';
$string['putpropertiesdesc'] = 'Paste the JSON string and \'Save changes\'.  Warning!  Does not validate setting values and performs a \'Purge all caches\'.';

$string['putpropertyreport'] = 'Report:';
$string['putpropertyversion'] = 'version:';
$string['putpropertyproperties'] = 'Properties';
$string['putpropertyour'] = 'Our';
$string['putpropertiesignorecti'] = 'Ignoring all course title image settings.';
$string['putpropertiesreportfiles'] = 'Remember to upload the following files to their settings:';
$string['putpropertiessettingsreport'] = 'Settings report:';
$string['putpropertiesvalue'] = '->';
$string['putpropertiesfrom'] = 'from';
$string['putpropertieschanged'] = 'Changed:';
$string['putpropertiesunchanged'] = 'Unchanged:';
$string['putpropertiesadded'] = 'Added:';
$string['putpropertiesignored'] = 'Ignored:';

// Style guide.
$string['styleguide'] = 'Style guide';
$string['styleguidesub'] = 'Bootstrap V2.3.2 Style guide';
$string['styleguidedesc'] = 'Original documentation code \'{$a->origcodelicenseurl}\' licensed.  Holder.js is \'{$a->holderlicenseurl}\' licensed.  Additional code \'{$a->thiscodelicenseurl}\' licensed, which is a \'{$a->compatible}\' license.  Content \'{$a->contentlicenseurl}\' licensed.  The documentation has been formatted for Moodle output with addition of FontAwesome icons where appropriate.  Additional CSS can be found in the file \'essential_admin_setting_styleguide.php\' under the comment \'// Beyond docs.css.\'.  The \'{$a->globalsettings}\' section has been removed.';

// Alerts.
$string['alertsheading'] = 'User alerts';
$string['alertsheadingsub'] = 'Display important messages to your users on the front page';
$string['alertsdesc'] = 'This will display an alert (or multiple) in three different styles to your users on the Moodle frontpage. Please remember to disable these when no longer needed.';

$string['enablealert'] = 'Enable alerts';
$string['enablealertdesc'] = 'Enable or disable alerts';

$string['alert1'] = 'First alert';
$string['alert2'] = 'Second alert';
$string['alert3'] = 'Third alert';
$string['alertinfodesc'] = 'Enter the settings for your alert.';

$string['alerttitle'] = 'Title';
$string['alerttitledesc'] = 'Main title/heading for your alert.';

$string['alerttype'] = 'Level';
$string['alerttypedesc'] = 'Set the appropriate alert level/type to best inform your users.';

$string['alerttext'] = 'Alert text';
$string['alerttextdesc'] = 'What is the text you wish to display in your alert.';

$string['alert_info'] = 'Information';
$string['alert_warning'] = 'Warning';
$string['alert_general'] = 'Announcement';

$string['alert_edit'] = 'Edit alerts';

$string['versionalerttitle'] = 'Version warning: ';
$string['versionalerttext1'] = 'Theme not designed for Moodle version.';
$string['versionalerttext2'] = 'Unexpected issues may occur, please get the correct theme version for your Moodle version.';

// Preferences.
$string['badgepreferences'] = 'Badge';

// Incourse.
$string['returntosection'] = 'Return to: {$a->section}';

// Course fullname and course content search.
$string['findcoursecontent'] = 'Course search: ';
$string['searchallcoursecontent'] = 'Search all course content';
$string['searchallcoursecontentdefault'] = 'Default search all course content';
$string['searchallcoursecontentdefaultdesc'] = 'Sets the value of the \'Search all course content\' checkbox on the course content search.  If \'Search all course content\' is unticked, then only the course fullname is searched for a match.';

// essential_admin_setting_configinteger.
$string['asconfigintlower'] = '{$a->value} is less than the lower range limit of {$a->lower}';
$string['asconfigintupper'] = '{$a->value} is greater than the upper range limit of {$a->upper}';
$string['asconfigintnan'] = '{$a->value} is not a number';

// Privacy.
$string['privacy:metadata:preference:courseitemsearchtype'] = 'The users choice of course search (\'course fullname\' - \'0\' or all course content - \'1\') if ever stored when \'coursecontentsearch\' was ticked.  If nothing stated then either the course search is off or the user has never viewed the dsahboard it when it was on.';
$string['privacy:request:preference:courseitemsearchtype'] = 'The user has chosen "{$a->value}" for the course search user preference "{$a->name}" where \'0\' is the \'course fullname only\' and \'1\' is \'all course content\'.';
