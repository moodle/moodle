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
 * The frontpage layout for the FormFactor theme.
 *
 * @package   theme_formfactor
 * @copyright 2010 Patrick Malley
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($hassidepre && !$hassidepost) {
    $bodyclasses[] = 'side-pre-only';
} else if ($hassidepost && !$hassidepre) {
    $bodyclasses[] = 'side-post-only';
} else if (!$hassidepost && !$hassidepre) {
    $bodyclasses[] = 'content-only';
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

<!-- START OF HEADER -->

    <div id="page-header" class="clearfix">
        <div id="page-header-wrapper">
            <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
            <div class="headermenu">
                <?php
                    echo $OUTPUT->login_info();
                    echo $OUTPUT->lang_menu();
                    echo $PAGE->headingmenu;
                ?>
            </div>
        </div>
    </div>

    <?php if ($hascustommenu) { ?>
      <div id="custommenu"><?php echo $custommenu; ?></div>
    <?php } else { ?>
      <ul id="page-navigation" class="clearfix">
        <li>&nbsp;</li>
      </ul>
    <?php } ?>

<!-- END OF HEADER -->

<!-- START OF CONTENT -->

<div id="page-content-wrapper">
    <div id="page-content">
        <div id="region-main-box">
            <div id="region-post-box">

                <div id="region-main-wrap">
                    <div id="region-main">
                        <div class="region-content">
                            <?php echo $OUTPUT->main_content() ?>
                        </div>
                    </div>
                </div>

                <?php if ($hassidepre) { ?>
                <div id="region-pre" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-pre') ?>
                    </div>
                </div>
                <?php } ?>

                <?php if ($hassidepost) { ?>
                <div id="region-post" class="block-region">
                    <div class="region-content">
                        <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<!-- END OF CONTENT -->

<!-- START OF FOOTER -->

    <div id="page-footer">
        <p class="helplink">
        <?php echo page_doc_link(get_string('moodledocslink')) ?>
        </p>

        <?php
        echo $OUTPUT->login_info();
        echo $OUTPUT->home_link();
        echo $OUTPUT->standard_footer_html();
        ?>
    </div>

<!-- END OF FOOTER -->

</div>
<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
