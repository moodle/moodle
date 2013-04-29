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
 * Embedded layout for the mymobile theme
 *
 * @package    theme_mymobile
 * @copyright  John Stabinger
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!empty($PAGE->theme->settings->colourswatch)) {
    $showswatch = $PAGE->theme->settings->colourswatch;
} else {
    $showswatch = "light";
}
if ($showswatch == "light") {
    $datatheme = 'b';
    $databodytheme = 'd';
} else {
    $datatheme = 'a';
    $databodytheme = 'c';
}
$bodyclasses = array();
$mypagetype = $PAGE->pagetype;

echo $OUTPUT->doctype() ?>
<html id="mymobile" <?php echo $OUTPUT->htmlattributes() ?>>
<head>
    <title><?php echo $PAGE->title ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme')?>" />
    <?php if ($mypagetype != 'mod-chat-gui_ajax-index') { ?>
        <meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1" />
    <?php } ?>
    <?php echo $OUTPUT->standard_head_html() ?>
</head>
<body id="<?php p($PAGE->bodyid) ?>" class="<?php p($PAGE->bodyclasses.' '.join(' ', $bodyclasses)) ?>">
    <?php echo $OUTPUT->standard_top_of_body_html() ?>
    <!-- END OF HEADER -->
    <?php if ($mypagetype == 'mod-chat-gui_ajax-index') { ?>
    <div data-role="page" id="chatpage" data-fullscreen="true" data-title="<?php p($SITE->shortname) ?>">
        <?php echo $OUTPUT->main_content(); ?>
        <input type="button" value="back" data-role="none" id="chatback" onClick="history.back()">
    <?php } else { ?>
    <div id="content2" data-role="page" data-title="<?php p($SITE->shortname) ?>" data-theme="<?php echo $datatheme;?>">
        <div data-role="header" data-theme="<?php echo $datatheme;?>">
            <h1><?php echo $PAGE->heading ?>&nbsp;</h1>
            <?php if ($mypagetype != "help") { ?>
                <a class="ui-btn-right" data-ajax="false" data-icon="home" href="<?php p($CFG->wwwroot) ?>" data-iconpos="notext"><?php p(get_string('home')); ?></a>
            <?php } ?>
        </div>
        <div data-role="content" class="mymobilecontent" data-theme="<?php echo $databodytheme;?>">
            <?php echo $OUTPUT->main_content(); ?>
        </div>
    <?php } ?>
        <!-- START OF FOOTER -->
        <?php echo $OUTPUT->standard_end_of_body_html() ?>
    </div>
</body>
</html>