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
 * Parent theme: Bootstrapbase by Bas Brands
 * Built on: Essential by Julian Ridden
 *
 * @package   theme_lambda
 * @copyright 2020 redPIthemes
 *
 */

/* Core */
$string['configtitle'] = 'lambda';
$string['pluginname'] = 'lambda';
$string['choosereadme'] = '
<div class="clearfix">
<div style="margin-bottom:20px;">
<p style="text-align:center;"><img class="img-polaroid" src="lambda/pix/screenshot.jpg" /></p>
</div>
<hr />
<h2>Lambda - Responsive Moodle Theme</h2>
<div class="divider line-01"></div>
<div style="color: #888; text-transform: uppercase; margin-bottom:20px;">
<p>created by RedPiThemes<br />Online documentation: <a href="http://redpithemes.com/Documentation/assets/index.html" target="_blank">http://redpithemes.com/Documentation/assets/index.html</a><br />Support is provided via ticket at the support forum: <a href="https://redpithemes.ticksy.com" target="_blank">https://redpithemes.ticksy.com</a></p>
</div>
<hr />
<p style="text-align:center;"><img class="img-polaroid" src="lambda/pix/redPIthemes.jpg" /></p>';

/* Settings - General */
$string['settings_general'] = 'General';
$string['logo'] = 'Logo';
$string['logodesc'] = 'Please upload your custom logo here. If you upload a logo it will appear in the header.';
$string['logo_res'] = 'Standard logo dimension';
$string['logo_res_desc'] = 'Sets the dimension of your logo to a maximum height of 90px. Using this setting, your logo will adapt to different screen resolutions and you can also use a @2x version for high-res screens.';
$string['favicon'] = 'Favivon';
$string['favicon_desc'] = 'Change the favicon for Lambda. Images with a transparent background and 32px height will work best. Allowed types: PNG, JPG, ICO';
$string['pagewidth'] = 'Set Page Width';
$string['pagewidthdesc'] = 'Choose from the list of availble page layouts.';
$string['boxed_wide'] = 'Boxed - fixed width wide';
$string['boxed_narrow'] = 'Boxed - fixed width narrow';
$string['boxed_variable'] = 'Boxed - variable width';
$string['full_wide'] = 'Wide - variable width';
$string['page_centered_logo'] = 'Header with centered logo';
$string['page_centered_logo_desc'] = 'Mark the checkbox to use a variation for the header with a centered logo.';
$string['category_layout'] = 'Course category view';
$string['category_layout_desc'] = 'Choose a layout for the courses in the course category view. You can select to show your courses in a list or in a grid view.';
$string['category_layout_list'] = 'Course list';
$string['category_layout_grid'] = 'Course grid';
$string['footnote'] = 'Footnote';
$string['footnotedesc'] = 'Whatever you add to this textarea will be displayed in the footer throughout your Moodle site, e.g. Copyright and the name of your organisation.';
$string['customcss'] = 'Custom CSS';
$string['customcssdesc'] = 'Whatever CSS rules you add to this textarea will be reflected in every page, making for easier customization of this theme.';

/* Settings - Background images */
$string['settings_background'] = 'Background images';
$string['list_bg'] = 'Page Background';
$string['list_bg_desc'] = 'Select the Page Background from a list of included background images.<br /><strong>Note: </strong>If you upload an image below, your choice here on the list will be discarded.';
$string['pagebackground'] = 'Upload custom background image';
$string['pagebackgrounddesc'] = 'Upload your own background image. If none is uploaded a default image from the above list is used.';
$string['page_bg_repeat'] = 'Repeat uploaded image?';
$string['page_bg_repeat_desc'] = 'If you have uploaded a tiled background (like a pattern), you should mark the checkbox to repeat the image over the page background.<br />Otherwise, if you leave the box unchecked, the image will be used as a full page background image that covers the entire browser window.';
$string['header_background'] = 'Upload custom header image';
$string['header_background_desc'] = 'Upload your own header image. If none is uploaded a default white background will be used for the header.';
$string['header_bg_repeat'] = 'Repeat header image?';
$string['header_bg_repeat_desc'] = 'If you have uploaded a tiled background (like a pattern), you should mark the checkbox to repeat the image over the background at the header.<br />Otherwise the image will scaled to be as large as possible so that the header area is completely covered by the background image.';
$string['category_background'] = 'Course category background banner';
$string['category_background_desc'] = 'Upload your own background banner image for the Moodle course category view. If none is uploaded a default image is used.';
$string['banner_font_color'] = 'Font color for the banner';
$string['banner_font_color_desc'] = 'The default background banner image for the Moodle course category view is dimmed. Therefore is white font color is used there. If you upload your own banner image, you might want to use a different font color.';
$string['banner_font_color_opt0'] = 'white (default)';
$string['banner_font_color_opt1'] = 'dark';
$string['banner_font_color_opt2'] = 'main theme color';
$string['hide_category_background'] = 'Hide the category background banner?';
$string['hide_category_background_desc'] = 'Mark the checkbox if you want to completely hide the category background banner.';

/* Settings - Colors */
$string['settings_colors'] = 'Colors';
$string['maincolor'] = 'Theme Color';
$string['maincolordesc'] = 'The main color of your theme - this will change mulitple components to produce the colour you wish across the moodle site';
$string['linkcolor'] = 'Link Color';
$string['linkcolordesc'] = 'The color of the links. You can use the main color of your theme here too, but some bright colors may be hard to read with this setting. In this case you can select a darker color here.';
$string['mainhovercolor'] = 'Theme Hover Color';
$string['mainhovercolordesc'] = 'Color for hover effects - this is used for links, menus, etc';
$string['def_buttoncolor'] = 'Default Button';
$string['def_buttoncolordesc'] = 'Color for the default button used in moodle';
$string['def_buttonhovercolor'] = 'Default Button (Hover)';
$string['def_buttonhovercolordesc'] = 'Color for the hover effect on the default button';
$string['headercolor'] = 'Header Color';
$string['headercolor_desc'] = 'Color for the header area';
$string['menufirstlevelcolor'] = 'Menu 1. Level';
$string['menufirstlevelcolordesc'] = 'Color for the navigation bar';
$string['menufirstlevel_linkcolor'] = 'Menu 1. Level - Links';
$string['menufirstlevel_linkcolordesc'] = 'Color for the links in the navigation bar';
$string['menusecondlevelcolor'] = 'Menu 2. Level';
$string['menusecondlevelcolordesc'] = 'Color for the drop down menu in the navigation bar';
$string['menusecondlevel_linkcolor'] = 'Menu 2. Level - Links';
$string['menusecondlevel_linkcolordesc'] = 'Color for the links in the drop down menu';
$string['footercolor'] = 'Footer Background Color';
$string['footercolordesc'] = 'Set what color the background of the footer box should be';
$string['footerheadingcolor'] = 'Footer Heading Color';
$string['footerheadingcolordesc'] = 'Set the color for block headings in the footer';
$string['footertextcolor'] = 'Footer Text Color';
$string['footertextcolordesc'] = 'Set the color you want your text to be in the footer';
$string['copyrightcolor'] = 'Footer Copyright Color';
$string['copyrightcolordesc'] = 'Set what color the background of the copyright box in the footer should be';
$string['copyright_textcolor'] = 'Copyright Text Colour';
$string['copyright_textcolordesc'] = 'Set the color you want your text to be in the copyright box';

/* Settings - blocks */
$string['settings_blocks'] = 'Moodle Blocks';
$string['block_layout'] = 'Choose block layout';
$string['block_layout_opt0'] = 'Default Lambda block layout';
$string['block_layout_opt1'] = 'Standard Moodle block layout';
$string['block_layout_opt2'] = 'Collapsible left block region';
$string['block_layout_desc'] = 'You can choose between:<br /><ul><li>Default Lambda block layout: both block columns left and right on the side of the main content area</li><li>Standard Moodle block layout: block regions left and right of the main content</li><li>Collapsible left block region: You can use a collapsible sidebar for the left block region</li></ul><strong>Please note:</strong>The Moodle dock for the blocks can only be used with the <em>Default Lambda block layout</em> or the <em>Standard Moodle block layout</em>.';
$string['sidebar_frontpage'] = 'Enable collapsible sidebar for the front page';
$string['sidebar_frontpage_desc'] = 'If you have selected the collapsible sidebar for the block layout from the dropdown above, you can choose whether this sidebar should also be enabled for the Moodle front page or not. The front page provides an additional block region for admins, so you might find that the sidebar is not necessary there.<br /><strong>Please note: </strong>If you have selected any other block layout than the collapsible sidebar, then this setting will not have any effect.';
$string['block_style'] = 'Choose block style';
$string['block_style_opt0'] = 'block style 01';
$string['block_style_opt1'] = 'block style 02';
$string['block_style_opt2'] = 'block style 03';
$string['block_style_desc'] = 'You can choose between the following block style variations:<div class="row-fluid"><div class="span4"><p><img class="img-responsive img-polaroid" src="https://redpithemes.com/Documentation/assets/img/options-blocks-1.jpg" /><p>block style 01</div><div class="span4"><p><img class="img-responsive img-polaroid" src="https://redpithemes.com/Documentation/assets/img/options-blocks-2.jpg" /><p>block style 02</div><div class="span4"><p><img class="img-responsive img-polaroid" src="https://redpithemes.com/Documentation/assets/img/options-blocks-3.jpg" /><p>block style 03</div></div>';
$string['block_icons'] = 'Theme Lambda block icons';
$string['block_icons_opt0'] = 'colored (default)';
$string['block_icons_opt1'] = 'monochrome';
$string['block_icons_opt2'] = 'none (hide block icons)';
$string['block_icons_desc'] = 'Choose a style for the block icons.';

/* Settings - Socials */
$string['settings_socials'] = 'Social Media';
$string['socialsheadingsub'] = 'Engage your users with Social Networking';
$string['socialsdesc'] = 'Provide direct links to the core social networks that promote your brand.';
$string['facebook'] = 'Facebook URL';
$string['facebookdesc'] = 'Enter the URL of your Facebook page. (i.e https://www.facebook.com/mycollege)';
$string['twitter'] = 'Twitter URL';
$string['twitterdesc'] = 'Enter the URL of your Twitter feed. (i.e https://www.twitter.com/mycollege)';
$string['googleplus'] = 'Google+ URL';
$string['googleplusdesc'] = 'Enter the URL of your Google+ profile. (i.e https://plus.google.com/+mycollege)';
$string['youtube'] = 'YouTube URL';
$string['youtubedesc'] = 'Enter the URL of your YouTube channel. (i.e https://www.youtube.com/user/mycollege)';
$string['flickr'] = 'Flickr URL';
$string['flickrdesc'] = 'Enter the URL of your Flickr page. (i.e http://www.flickr.com/photos/mycollege)';
$string['pinterest'] = 'Pinterest URL';
$string['pinterestdesc'] = 'Enter the URL of your Pinterest page. (i.e http://pinterest.com/mycollege/mypinboard)';
$string['instagram'] = 'Instagram URL';
$string['instagramdesc'] = 'Enter the URL of your Instagram page. (i.e http://instagram.com/mycollege)';
$string['linkedin'] = 'LinkedIn URL';
$string['linkedindesc'] = 'Enter the URL of your LinkedIn page. (i.e http://www.linkedin.com/company/mycollege)';
$string['website'] = 'Website URL';
$string['websitedesc'] = 'Enter the URL of your own website. (i.e http://www.mycollege.com)';
$string['socials_mail'] = 'Email Address';
$string['socials_mail_desc'] = 'Enter the HTML Email Address Hyperlink Code. (i.e info@mycollege.com)';
$string['socials_color'] = 'Social Icons Color';
$string['socials_color_desc'] = 'Set the color for your social media icons.';
$string['socials_position'] = 'Icons Position';
$string['socials_position_desc'] = 'Choose where to place the social media icons: at the bottom of the page (footer) or at the top (header).';
$string['socials_header_bg'] = 'Social Icons Header Background';
$string['socials_header_bg_desc'] = 'Here you can select how you would like to separate the background color for the social icons at the header.';
$string['socials_header_bg_0'] = 'fully transparent (use header background)';
$string['socials_header_bg_1'] = 'slightly dimmed';
$string['socials_header_bg_2'] = 'darkened';
$string['socials_header_bg_3'] = 'use main theme color';
$string['socials_header_bg_4'] = 'use footer copyright background';

/* Settings - Fonts */
$string['settings_fonts'] = 'Fonts';
$string['fontselect_heading'] = 'Font Selector - Headings';
$string['fontselectdesc_heading'] = 'Choose from the list of availble fonts.';
$string['fontselect_body'] = 'Font Selector - Body';
$string['fontselectdesc_body'] = 'Choose from the list of availble fonts.';
$string['font_body_size'] = 'Body Text Size';
$string['font_body_size_desc'] = 'Adjust the global font size for the body text.';
$string['font_languages'] = 'Additional character sets';
$string['font_languages_desc'] = 'Some of the fonts in the Google Font Directory support additional character sets for different languages. Using many character sets can slow down your Moodle, so only select the character sets that you actually need.<br /><strong>Please note: </strong>The Google Font Directory does not provide each additional character sets for every font. In case of doubt you should select <i>Open Sans</i>.';
$string['font_languages_latinext'] = 'Latin Extended';
$string['font_languages_cyrillic'] = 'Cyrillic';
$string['font_languages_cyrillicext'] = 'Cyrillic Extended';
$string['font_languages_greek'] = 'Greek';
$string['font_languages_greekext'] = 'Greek Extended';
$string['use_fa5'] = 'Font Awesome 5';
$string['use_fa5_desc'] = 'Use the new Font Awesome 5 Web Font icons.<br /><strong>Please note:</strong> Font Awesome Version 5 has been re-written and re-designed completely from scratch. So there will be a few steps you will need to do if you have used the icons before with a previous version of Theme Lambda. It will be necessary to find and replace any icons that have different names between version 4 and 5. Make sure to read this <a href="https://fontawesome.com/how-to-use/upgrading-from-4#icon-name-changes" target="_blank">list with name changes</a>.<br />If you are installing Lambda for the first time on your Moodle site, the new version 5 is recommended.';
$string['fonts_source'] = 'Font type selector';
$string['fonts_source_desc'] = 'Choose if you want to use a Google web font or if you would like to upload your own custom font file.<br /><strong>Please note:</strong> You have to <em>Save Changes</em> first to show the new options for your choice.';
$string['fonts_source_google'] = 'Google Fonts';
$string['fonts_source_file'] = 'Custom font file';
$string['fonts_file_body'] = 'Body font file';
$string['fonts_file_body_desc'] = 'Upload your body font file here. For best compatibility, you should use a True Type or Web Open Font Format.';
$string['fonts_file_headings'] = 'Heading font file';
$string['fonts_file_headings_desc'] = 'Upload your heading font file here. For best compatibility, you should use a True Type or Web Open Font Format.';
$string['font_headings_weight'] = 'Heading font weight';
$string['font_headings_weight_desc'] = 'You can select a suitable weight for your heading font. Defines from thick to thin characters: 700 is the same as bold, 400 is the same as normal and 300 is for fonts with lighter characters.';

/* Settings - Slider */
$string['settings_slider'] = 'Slideshow';
$string['slideshowheading'] = 'Frontpage Slideshow';
$string['slideshowheadingsub'] = 'Dynamic Slideshow for the frontpage';
$string['slideshowdesc'] = 'This creates a dynamic slideshow of up to 5 slides for you to promote important elements of your site.<br /><b>NOTE: </b>You have to upload at least one image to make the slideshow appear. Heading, caption and URL are optional.';
$string['slideshow_slide1'] = 'Slideshow - Slide 1';
$string['slideshow_slide2'] = 'Slideshow - Slide 2';
$string['slideshow_slide3'] = 'Slideshow - Slide 3';
$string['slideshow_slide4'] = 'Slideshow - Slide 4';
$string['slideshow_slide5'] = 'Slideshow - Slide 5';
$string['slideshow_options'] = 'Slideshow - Options';
$string['slidetitle'] = 'Slide Heading';
$string['slidetitledesc'] = 'Enter a descriptive heading for your slide';
$string['slideimage'] = 'Slide Image';
$string['slideimagedesc'] = 'Upload an image.';
$string['slidecaption'] = 'Slide Caption';
$string['slidecaptiondesc'] = 'Enter the caption text to use for the slide';
$string['slide_url'] = 'Slide URL';
$string['slide_url_desc'] = 'If you enter an URL, a "Read more" button will be displayed in your slide.';
$string['slideshow_height'] = 'Height for the slideshow';
$string['slideshow_height_desc'] = 'Select a height for the slideshow that will be used for desktop resolutions. This height will be adapted and decreased for tablets and mobiles.';
$string['slideshow_hide_captions'] = 'hide captions on mobile devices';
$string['slideshow_hide_captions_desc'] = 'In case you use a decreased height for the slideshow or if you have chosen the <em>responsive</em> setting, it may be necessary to hide the headings and captions for mobile devices. Otherwise, the captions might not fit to the adapted image height for mobile devices.';
$string['slideshowpattern'] = 'Pattern/Overlay';
$string['slideshowpatterndesc'] = 'Select a pattern as a transparent overlay on your images';
$string['pattern1'] = 'none';
$string['pattern2'] = 'dotted - narrow';
$string['pattern3'] = 'dotted - wide';
$string['pattern4'] = 'lines - horizontal';
$string['pattern5'] = 'lines - vertical';
$string['slideshow_advance'] ='AutoAdvance';
$string['slideshow_advance_desc'] ='Select if you want to make a slide automatically advance after a certain amount of time';
$string['slideshow_nav'] ='Navigation Hover';
$string['slideshow_nav_desc'] ='If true the navigation button (prev, next and play/stop buttons) will be visible on hover state only, if false they will be visible always';
$string['slideshow_loader'] ='Slideshow Loader';
$string['slideshow_loader_desc'] ='Select pie, bar, none (even if you choose "pie", old browsers like IE8- can not display it... they will display always a loading bar)';
$string['slideshow_imgfx'] ='Image Effects';
$string['slideshow_imgfx_desc'] ='Choose a transition effect for your images:<br /><i>random, simpleFade, curtainTopLeft, curtainTopRight, curtainBottomLeft, curtainBottomRight, curtainSliceLeft, curtainSliceRight, blindCurtainTopLeft, blindCurtainTopRight, blindCurtainBottomLeft, blindCurtainBottomRight, blindCurtainSliceBottom, blindCurtainSliceTop, stampede, mosaic, mosaicReverse, mosaicRandom, mosaicSpiral, mosaicSpiralReverse, topLeftBottomRight, bottomRightTopLeft, bottomLeftTopRight, bottomLeftTopRight, scrollLeft, scrollRight, scrollHorz, scrollBottom, scrollTop</i>';
$string['slideshow_txtfx'] ='Text Effects';
$string['slideshow_txtfx_desc'] ='Choose a transition effect text in your slides:<br /><i>moveFromLeft, moveFromRight, moveFromTop, moveFromBottom, fadeIn, fadeFromLeft, fadeFromRight, fadeFromTop, fadeFromBottom</i>';

/* Settings - Carousel */
$string['settings_carousel'] = 'Carousel';
$string['carouselheadingsub'] = 'Settings for the Frontpage Carousel';
$string['carouseldesc'] = 'Here you can setup a carousel slider for your Frontpage.<br /><strong>Please note: </strong>You have to upload at least the images to make the slider appear. The caption settings will appear as a hover effect for the images and are optional.';
$string['carousel_position'] = 'Carousel Position';
$string['carousel_positiondesc'] = 'Select a position for the carousel slider.<br />You can choose to place the slider at the top or bottom of the content area.';
$string['carousel_h'] = 'Heading';
$string['carousel_h_desc'] = 'A heading for the frontpage carousel.';
$string['carousel_hi'] = 'Heading Tag';
$string['carousel_hi_desc'] = 'Define your heading: &lt;h1&gt; defines the most important heading. &lt;h6&gt; defines the least important heading.';
$string['carousel_add_html'] = 'Additional HTML Content';
$string['carousel_add_html_desc'] = 'Any content you enter here will be placed left to the frontpage carousel.<br /><strong>Note: </strong>You have to use HTML formatting elements to format your text.';
$string['carousel_slides'] = 'Number of Slides';
$string['carousel_slides_desc'] = 'Select the number of slides for your carousel';
$string['carousel_image'] = 'Image';
$string['carousel_imagedesc'] = 'Upload the image to appear in the slide.';
$string['carousel_heading'] = 'Caption - Heading';
$string['carousel_heading_desc'] = 'Enter a heading for your image - this will create a caption with a hover effect.<br /><strong>Note: </strong>You must at least enter the heading to make the caption appear.';
$string['carousel_caption'] = 'Caption - Text';
$string['carousel_caption_desc'] = 'Enter the caption text to use for the hover effect.';
$string['carousel_url'] = 'Caption - URL';
$string['carousel_urldesc'] = 'This will create a button for your caption with a link to the entered URL.';
$string['carousel_btntext'] = 'Caption - Link Text';
$string['carousel_btntextdesc'] = 'Enter a link text for the URL.';
$string['carousel_color'] = 'Caption - Color';
$string['carousel_colordesc'] = 'Select a color for the caption.';
$string['carousel_img_dim'] = 'Carousel Image Dimensions';
$string['carousel_img_dim_desc'] = 'Set the width for the carousel images';

/* Settings - Login */
$string['settings_login'] = 'Login and Navigation';
$string['custom_login'] = 'Custom login page';
$string['custom_login_desc'] = 'Mark the checkbox to display a customized version of the default Moodle login page.';
$string['mycourses_dropdown'] = 'MyCourses dropdown menu';
$string['mycourses_dropdown_desc'] = 'Shows the enrolled courses for a user as a dropdown entry in the Custom Menu.';
$string['hide_breadcrumb'] = 'Hide Breadcrumb';
$string['hide_breadcrumb_desc'] = 'Hide the Moodle breadcrumb navigation for non-logged in and guest users?';
$string['shadow_effect'] = 'Shadow Effect';
$string['shadow_effect_desc'] = 'Use a shadow effect for the Moodle custom menu bar and the slideshow?';
$string['login_link'] = 'Additional Login Link';
$string['login_link_desc'] = 'Shows an additional link at the login form of the theme.';
$string['moodle_login_page'] = 'Moodle Login Page';
$string['custom_login_link_url'] = 'Custom Login Link URL';
$string['custom_login_link_url_desc'] = 'Here you can enter a custom URL for your additional link at the login form. This will override the setting from the dropdown.';
$string['custom_login_link_txt'] = 'Custom Login Link Text';
$string['custom_login_link_txt_desc'] = 'Here you can enter a custom text for your additional link at the login form. This will override the setting from the dropdown.';
$string['auth_googleoauth2'] = 'Oauth2';
$string['auth_googleoauth2_desc'] = 'Use the Moodle Oauth2 authentication plugin instead of the default login form?<br /><strong>Please note: </strong>For all Moodle versions prior to 3.3 you have to install this additional plugin first from the Moodle plugins directory. This plugin allows your users to sign-in with a Google / Facebook / Github / Linkedin / Windows Live / VK / Battle.net account. The first time a user signs in, a new account is created.';
$string['home_button'] = 'Home button';
$string['home_button_desc'] = 'Choose from the list of available texts for the home button (the first button in the custom menu)';
$string['home_button_shortname'] = 'Short site name';
$string['home_button_frontpagedashboard'] = 'Frontpage (for non-logged-in users) / Dashboard (for logged-in users)';
$string['navbar_search_form'] = 'Search box on Navigation Bar';
$string['navbar_search_form_desc'] = 'Here you can choose whether the search box at the navigation bar should be always visible, hidden for non-logged in guest users or always hidden.';
$string['navbar_search_form_0'] = 'always visible';
$string['navbar_search_form_1'] = 'hide for non-logged in and guest users';
$string['navbar_search_form_2'] = 'always hidden';

/* Theme */
$string['visibleadminonly'] ='Blocks moved into the area below will only be seen by admins';
$string['region-side-post'] = 'Right';
$string['region-side-pre'] = 'Left';
$string['region-footer-left'] = 'Footer (Left)';
$string['region-footer-middle'] = 'Footer (Middle)';
$string['region-footer-right'] = 'Footer (Right)';
$string['region-hidden-dock'] = 'Hidden from users';
$string['nextsection'] = '';
$string['previoussection'] = '';
$string['backtotop'] = '';
$string['responsive'] = 'responsive';
$string['privacy:metadata:preference:sidebarstat'] = 'The user\'s preference for hiding or showing the drawer menu navigation.';
$string['privacy_sidebar_closed'] = 'The current preference for the collapsible sidebar is closed.';
$string['privacy_sidebar_open'] = 'The current preference for the collapsible sidebar is open.';