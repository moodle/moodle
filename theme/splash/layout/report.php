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
 * General layout for the splash theme
 *
 * @package    theme_splash
 * @copyright  2012 Caroline Kennedy - Synergy Learning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

splash_check_colourswitch();
splash_initialise_colourswitcher($PAGE);

$courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
if (empty($PAGE->layout_options['nocourseheaderfooter'])) {
    $courseheader = $OUTPUT->course_header();
    $coursecontentheader = $OUTPUT->course_content_header();
    if (empty($PAGE->layout_options['nocoursefooter'])) {
        $coursecontentfooter = $OUTPUT->course_content_footer();
        $coursefooter = $OUTPUT->course_footer();
    }
}

$bodyclasses = array();
$bodyclasses[] = 'splash-'.splash_get_colour();
if ($hassidepre && !$hassidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($hassidepost && !$hassidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$hassidepost && !$hassidepre) {
    $bodyclasses[] = 'content-only';
}

$haslogo = (!empty($PAGE->theme->settings->logo));
$hasfootnote = (!empty($PAGE->theme->settings->footnote));
$hidetagline = (!empty($PAGE->theme->settings->hide_tagline) && $PAGE->theme->settings->hide_tagline == 1);

if (!empty($PAGE->theme->settings->tagline)) {
    $tagline = $PAGE->theme->settings->tagline;
} else {
    $tagline = get_string('defaulttagline', 'theme_splash');
}

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <meta name="description" content="<?php p(strip_tags(format_text($SITE->summary, FORMAT_HTML))) ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
<?php echo $OUTPUT->standard_top_of_body_html() ?>
<div id="page">
<?php if ($hasheading || $hasnavbar) { ?>
    <div id="page-header">
    <div id="page-header-wrapper" class="wrapper clearfix">
        <?php
    if ($hasheading) { ?>
        <div id="headermenu">
        <?php
        if (isloggedin()) {
            echo html_writer::start_tag('div', array('id'=>'userdetails'));
            echo html_writer::tag('h1', get_string('usergreeting', 'theme_splash', $USER->firstname));
            echo html_writer::start_tag('p', array('class'=>'prolog'));
            echo html_writer::link(new moodle_url('/user/profile.php', array('id'=>$USER->id)),
            get_string('myprofile')).' | ';
            echo html_writer::link(new moodle_url('/login/logout.php', array('sesskey'=>sesskey())),
            get_string('logout'));
            echo html_writer::end_tag('p');
            echo html_writer::end_tag('div');
            echo html_writer::tag('div', $OUTPUT->user_picture($USER, array('size'=>55)), array('class'=>'userimg'));
        } else {
            echo html_writer::start_tag('div', array('id'=>'userdetails_loggedout'));
            $loginlink = html_writer::link(new moodle_url('/login/'), get_string('loginhere', 'theme_splash'));
            echo html_writer::tag('h1', get_string('welcome', 'theme_splash', $loginlink));
            echo html_writer::end_tag('div');
        } ?>
        <div class="clearer"></div>
        <div id="colourswitcher">
        <ul>
        <li><img src="<?php echo $OUTPUT->pix_url('colour', 'theme'); ?>" alt="colour" /></li>
        <li><a href="<?php echo new moodle_url($PAGE->url, array('splashcolour'=>'red')); ?>" class="styleswitch
        colour-red"><img src="<?php echo $OUTPUT->pix_url('red-theme2', 'theme'); ?>" alt="red" /></a></li>
        <li><a href="<?php echo new moodle_url($PAGE->url, array('splashcolour'=>'green')); ?>"
        class="styleswitch colour-green"><img src="<?php echo $OUTPUT->pix_url('green-theme2', 'theme'); ?>"
        alt="green" /></a></li>
        <li><a href="<?php echo new moodle_url($PAGE->url, array('splashcolour'=>'blue')); ?>"
        class="styleswitch colour-blue"><img src="<?php echo $OUTPUT->pix_url('blue-theme2', 'theme'); ?>"
        alt="blue" /></a></li>
        <li><a href="<?php echo new moodle_url($PAGE->url, array('splashcolour'=>'orange')); ?>"
        class="styleswitch colour-orange"><img src="<?php echo $OUTPUT->pix_url('orange-theme2', 'theme');
        ?>"
        alt="orange" /></a></li>
        </ul>
        </div>
        <?php
        if (!empty($PAGE->layout_options['langmenu'])) {
            echo $OUTPUT->lang_menu();
        }
        echo $PAGE->headingmenu
        ?>
        </div>
        <div id="logobox">
        <?php
        if ($haslogo) {
            echo html_writer::link(new moodle_url('/'), "<img src='".$PAGE->theme->settings->logo."' alt='logo' />");
        } else {
            echo html_writer::link(new moodle_url('/'), $PAGE->heading, array('class'=>'nologoimage'));
        } ?>
        <?php
        if (!$hidetagline) { ?>
            <h4><?php echo $tagline ?></h4>
            <?php
        } ?>
        </div>
        <div class="clearer"></div>
        <?php
        if ($haslogo) { ?>
            <h4 class="headermain inside">&nbsp;</h4>
            <?php
        } else { ?>
                <h4 class="headermain inside"><?php echo $PAGE->heading ?></h4>
                <?php
        } ?>
        <?php
    } // End of if ($hasheading)?>
    <!-- CUSTOMMENU -->
    <div class="clearer"></div>
    <?php
    if ($hascustommenu) { ?>
        <div id="moodlemenu">
        <div id="custommenu"><?php echo $custommenu; ?></div>
        </div>
        <?php
    } ?>
    <!-- END CUSTOMMENU -->
    <?php if (!empty($courseheader)) { ?>
    <div id="course-header"><?php echo $courseheader; ?></div>
    <?php } ?>
    <div class="navbar">
    <div class="wrapper clearfix">
    <div class="breadcrumb">
    <?php
    if ($hasnavbar) {
        echo $OUTPUT->navbar();
    } ?>
    </div>
    <div class="navbutton"> <?php echo $PAGE->button; ?></div>
    </div>
    </div>
    </div>
    </div>
    <?php
} // if ($hasheading || $hasnavbar) ?>
        <!-- END OF HEADER -->
        <!-- START OF CONTENT -->

        <div id="page-content" class="clearfix">
            <div id="report-main-content">
                <div class="region-content">
                    <?php echo $coursecontentheader; ?>
                    <?php echo $OUTPUT->main_content() ?>
                    <?php echo $coursecontentfooter; ?>
                </div>
            </div>
            <?php if ($hassidepre) { ?>
            <div id="report-region-wrap">
                <div id="report-region-pre" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                    </div>
                </div>
            </div>
            <?php
} ?>
        </div>

        <!-- END OF CONTENT -->
        <?php if (!empty($coursefooter)) { ?>
        <div id="course-footer"><?php echo $coursefooter; ?></div>
        <?php } ?>
        <div class="clearfix"></div>
    <!-- END OF #Page -->
    </div>
    <!-- START OF FOOTER -->
    <?php if ($hasfooter) { ?>
    <div id="page-footer">
    <div id="footer-wrapper">
        <?php
    if ($hasfootnote) { ?>
            <div id="footnote"><?php echo $PAGE->theme->settings->footnote; ?></div>
            <?php
    } ?>
            <p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
            <?php
            echo $OUTPUT->login_info();
            echo $OUTPUT->home_link();
            echo $OUTPUT->standard_footer_html();
            ?>
        </div>
    </div>
    <?php
} ?>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
