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
 * General layout for the mymobile theme
 *
 * @package    theme_mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get blocks?
$toblock = optional_param('mymobile_blocks', false, PARAM_BOOL);
// Get settings?
$toset = optional_param('mymobile_settings', false, PARAM_BOOL);

$mypagetype = $PAGE->pagetype;
$mylayoutype = $PAGE->pagelayout;
$mydevice = $PAGE->devicetypeinuse;

if (!empty($PAGE->theme->settings->colourswatch)) {
    $showswatch = $PAGE->theme->settings->colourswatch;
} else {
    $showswatch = 'light';
}

if ($showswatch == 'light') {
    $dtheme = 'd';
    $dthemeb = 'd';
    $datatheme = 'data-theme="b"';
    $databodytheme = 'data-theme="d"';
} else {
    $dtheme = 'd';
    $dthemeb = 'c';
    $datatheme = 'data-theme="a"';
    $databodytheme = '';
}

// Custom settings.
$hasshowmobileintro = (!empty($PAGE->theme->settings->showmobileintro));

if (!empty($PAGE->theme->settings->showfullsizeimages)) {
    $hasithumb = $PAGE->theme->settings->showfullsizeimages;
} else {
    $hasithumb = 'ithumb';
}

if (!empty($PAGE->theme->settings->showsitetopic)) {
    $showsitetopic = $PAGE->theme->settings->showsitetopic;
} else {
    $showsitetopic = 'topicnoshow';
}

if (!empty($PAGE->theme->settings->usetableview)) {
    $showusetableview = $PAGE->theme->settings->usetableview;
} else {
    $showusetableview = 'tabshow';
}

// TODO: Fix this hardcoding there are other course formats that peopleuse.
//       Probably changing to an appropriate regex will do.
if ($mypagetype == 'course-view-topics' || $mypagetype == 'course-view-weeks') {
    // Jump to current topic only in course pages.
    $jumptocurrent = 'true';
} else {
    $jumptocurrent = 'false';
}

// Below sets a URL variable to use in some links.
$urlblocks = new moodle_url($PAGE->url, array('mymobile_blocks' => 'true'));
$urlsettings = new moodle_url($PAGE->url, array('mymobile_settings' => 'true'));

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hasmyblocks = $PAGE->blocks->region_has_content('myblocks', $OUTPUT);

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
$bodyclasses[] = (string)$hasithumb;
$bodyclasses[] = (string)$showsitetopic;
// Add ithumb class to decide whether to show or hide images and site topic.

// TODO: Better illustrate preceedence.
$gowide = ($mydevice == 'default' && $showusetableview == 'tabshow' || $mydevice == 'tablet' && $showusetableview == 'tabshow');
if ($gowide) {
    // Initialize column position choices.
    mymobile_initialise_colpos($PAGE);
}
$usercol = (mymobile_get_colpos() == 'on');

$renderer = $PAGE->get_renderer('theme_mymobile');

echo $OUTPUT->doctype() ?>
<html id="mymobile" <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $OUTPUT->pix_url('m2m2x', 'theme')?>" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $OUTPUT->pix_url('m2m', 'theme')?>" />
    <link rel="apple-touch-icon-precomposed" href="<?php echo $OUTPUT->pix_url('m2m', 'theme')?>" />

    <meta name="description" content="<?php echo strip_tags(format_text($SITE->summary, FORMAT_HTML)) ?>" />
    <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />

    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
    <?php echo $OUTPUT->standard_top_of_body_html() ?>
    <div id="<?php p($PAGE->bodyid) ?>PAGE" data-role="page" class="generalpage <?php echo 'ajaxedclass '; p($PAGE->bodyclasses.' '.join(' ', $bodyclasses));  ?> <?php if ($hasmyblocks && $usercol) { echo 'has-myblocks'; } ?> " data-title="<?php p($SITE->shortname) ?>">
        <!-- start header -->
        <div id="page-header" data-role="header" <?php echo($datatheme);?> class="mymobileheader" data-position="fixed">
            <h1><?php echo $PAGE->heading ?></h1>
            <?php if (isloggedin() && $mypagetype != 'site-index') { ?>
            <a class="ui-btn-right" data-icon="home" href="<?php p($CFG->wwwroot) ?>" data-iconpos="notext" data-ajax="false"><?php p(get_string('home')); ?></a>
            <?php } else if (!isloggedin()) {
                echo $OUTPUT->login_info();
            } ?>
            <?php if (!empty($courseheader)) { ?>
            <div data-role="course-header"><?php echo $courseheader; ?></div>
            <?php } ?>
            <!-- start navbar -->
            <div data-role="navbar">
                <ul>
                <?php if (!$gowide && !$hasmyblocks && !$toblock && $mypagetype == "mod-quiz-attempt" || !$gowide && !$hasmyblocks && !$toblock && $mylayoutype != "incourse") { ?>
                    <li><a data-theme="c" class="blockload" href="<?php echo $urlblocks->out(); ?>"><?php p(get_string('blocks')); ?></a></li>
                <?php } ?>
                <?php if (!$toset) { ?>
                    <li><a data-theme="c" href="<?php echo $urlsettings->out(); ?>"><?php p(get_string('settings')); ?></a></li>
                <?php } ?>
                <?php if ($jumptocurrent == 'true' && !$toblock && !$toset) { ?>
                    <li><a data-theme="c" class="jumptocurrent" href="#"><?php p(get_string('jump')); ?></a></li>
                <?php } ?>
                <?php if (isloggedin() && $hasnavbar) { ?>
                    <li><?php echo $OUTPUT->navbar(); ?></li>
                <?php } ?>
                </ul>
            </div>
            <!-- end navbar -->
        </div>
        <!-- end header -->

        <!-- main content -->
        <div id="page-content" data-role="content" class="mymobilecontent block-region" <?php echo $databodytheme; ?>>
          <?php if($toset) {  // If we get the true, that means load/show settings only. ?>
            <h2 class="jsets"><?php p(get_string('settings')); ?></h2>
            <?php
            // Load lang menu if available.
            echo $OUTPUT->lang_menu();
            ?>
            <ul data-role="listview" data-theme="<?php echo $dthemeb;?>" data-dividertheme="<?php echo $dtheme;?>" data-inset="true" class="settingsul">
                <?php echo $renderer->settings_tree($PAGE->settingsnav); ?>
            </ul>
            <?php echo $OUTPUT->login_info(); ?>
          <?php } ?>

            <div class="content-primary">
                <div class="region-content <?php if ($toblock) { echo 'mobile_blocksonly'; } ?>" id="themains">
                <?php
                // Only show main content if we are not showing anything else.
                if (!$toblock && !$toset) { ?>
                    <?php if ($hasshowmobileintro && $mypagetype == 'site-index') { ?>
                        <?php echo $PAGE->theme->settings->showmobileintro; ?>
                    <?php } ?>
                    <?php echo $coursecontentheader; ?>
                    <?php echo $OUTPUT->main_content(); ?>
                    <?php echo $coursecontentfooter; ?>
                <?php } ?>
                </div>
            </div>

            <?php if ($gowide && $hasmyblocks && !$toset) {
            // If we get the true, that means load/show blocks only for tablet views only. ?>
            <div class="content-secondary">
                <div class="tablets">
                    <h1><?php echo $PAGE->heading ?></h1>
                    <span><?php echo $PAGE->course->summary; ?></span>
                </div>

                <?php if ($hasmyblocks) { ?>
                <div data-role="collapsible-set" data-theme="<?php echo $dthemeb;?>">
                    <?php echo $OUTPUT->blocks_for_region('myblocks') ?>
                </div>
                <?php } ?>

                <?php if ($gowide && isloggedin() && !isguestuser()) { ?>

                <div data-role="collapsible" data-collapsed="false" <?php echo $datatheme;?> data-content-theme="<?php echo $dthemeb;?>" id="profcol">
                    <h3><?php p(''.$USER->firstname.' '.$USER->lastname.''); ?></h3>
                    <div class="ui-grid-a">
                        <div class="ui-block-a">
                            <?php echo html_writer::tag('div', $OUTPUT->user_picture($USER, array('size'=>80)), array('class'=>'userimg'));?>
                        </div>
                        <div class="ui-block-b">
                            <a data-role="button" data-icon="home" href="<?php p($CFG->wwwroot) ?>/my/"><?php p(get_string('myhome')); ?></a>
                            <a data-role="button" data-icon="info" href="<?php p($CFG->wwwroot) ?>/user/profile.php"><?php p(get_string('myprofile')); ?></a>
                            <a data-role="button" data-icon="back" data-ajax="false" href="<?php p($CFG->wwwroot) ?>/login/logout.php"><?php p(get_string('logout')); ?></a>
                        </div>
                    </div>
                </div>

                <div data-role="fieldcontain" id="sliderdiv">
                    <label for="slider"><?php p(get_string('mtoggle', 'theme_mymobile')); ?>:</label>
                    <select name="slider" class="slider" data-role="slider" id="slider">
                        <option value="on">On</option>
                        <option value="off">Off</option>
                    </select>
                </div>

                <?php } else if (!isloggedin() || isguestuser()) { ?>
                <a data-role="button" <?php echo $datatheme;?> data-ajax="false" href="<?php p($CFG->wwwroot) ?>/login/index.php"><?php p(get_string('login')); ?></a>
                 <?php } ?>
            </div>
            <?php } ?>

            <?php
            if ($toblock && !$gowide) {
                // Regular block load for phones + handhelds.
                if ($hasmyblocks) {
                    ?><div class="headingwrap ui-bar-<?php echo $dtheme;?> ui-footer jsetsbar">
                        <h2 class="jsets ui-title"><?php p(get_string('blocks')); ?></h2>
                    </div>
                    <div data-role="collapsible-set"><?php echo $OUTPUT->blocks_for_region('myblocks') ?></div><?php
                }
            }
            ?>
        </div>
        <!-- end main content -->

        <!-- start footer -->
        <?php if (!empty($coursefooter)) { ?>
        <div data-role="course-footer"><?php echo $coursefooter; ?></div>
        <?php } ?>

        <div id="page-footer">
            <div data-role="footer" class="mobilefooter" <?php echo $datatheme;?>>
                <div data-role="navbar" class="jnav" >
                    <ul>
                        <li><a id="mycal" class="callink" href="<?php p($CFG->wwwroot) ?>/calendar/view.php" data-icon="info" data-iconpos="top" ><?php p(get_string('calendar', 'calendar')); ?></a></li>
                        <?php if (!empty($CFG->messaging)) { ?>
                        <li><a id="mymess" href="<?php p($CFG->wwwroot) ?>/message/index.php" data-iconpos="top" data-icon="mymessage" ><?php p(get_string('messages', 'message')); ?></a></li>
                        <?php } ?>
                        <?php if ($mypagetype != 'site-index') { ?>
                        <li><a href="#" data-inline="true" data-role="button" data-iconpos="top" data-icon="arrow-u" id="uptotop"><?php p(get_string('up')); ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>

            <div id="underfooter">
                <?php
                echo $OUTPUT->login_info_footer();
                echo '<div class="noajax">';
                echo $OUTPUT->standard_footer_html();
                echo '</div>';
                ?>
            </div>
        </div>
        <!-- end footer -->
        <!-- empty divs with info for the JS to use -->
        <div id="<?php echo sesskey(); ?>" class="mobilesession"></div>
        <div id="<?php p($CFG->wwwroot); ?>" class="mobilesiteurl"></div>
        <div id="<?php echo $dtheme;?>" class="datatheme"></div>
        <div id="<?php echo $dthemeb;?>" class="datathemeb"></div>
        <!-- end js divs -->

        <?php echo $OUTPUT->standard_end_of_body_html() ?>
    </div><!-- ends page -->
</body>
</html>