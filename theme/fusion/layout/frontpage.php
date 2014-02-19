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
 * The frontpage layout.
 *
 * @package    theme_fusion
 * @copyright  2010 Patrick Malley (http://newschoollearning.com/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$hasheading = ($PAGE->heading);
$hasnavbar = (empty($PAGE->layout_options['nonavbar']) && $PAGE->has_navbar());
$hasfooter = (empty($PAGE->layout_options['nofooter']));
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$showsidepost = ($hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT));

$custommenu = $OUTPUT->custom_menu();
$hascustommenu = (empty($PAGE->layout_options['nocustommenu']) && !empty($custommenu));

$bodyclasses = array();
if ($showsidepost) {
    $bodyclasses[] = 'side-post-only';
} else if (!$showsidepost) {
    $bodyclasses[] = 'content-only';
}
if ($hascustommenu) {
    $bodyclasses[] = 'has_custom_menu';
}


if (!empty($PAGE->theme->settings->tagline)) {
    $tagline = $PAGE->theme->settings->tagline;
} else {
    $tagline = '<!-- There was no custom tagline set -->';
}

if (!empty($PAGE->theme->settings->footertext)) {
    $footnote = $PAGE->theme->settings->footertext;
} else {
    $footnote = '<!-- There was no custom footnote set -->';
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

  <div id="page-wrap1">
    <div id="page-wrap2">

    <div id="wrapper" class="clearfix">

<!-- START OF HEADER -->

        <div id="page-header">
            <div id="page-header-wrapper" class="wrapper clearfix">

                <div id="headermenus" class="clearfix">
                    <div class="headermenu clearfix">
                        <?php
                            echo $OUTPUT->lang_menu();
                            echo $OUTPUT->login_info();
                            echo $PAGE->headingmenu;
                        ?>
                    </div>
                    <?php if ($hascustommenu) { ?>
                        <div id="custommenu"><?php echo $custommenu; ?></div>
                    <?php } else { ?>
                        <div id="custommenu" style="line-height:1em;">&nbsp;</div> <!-- temporary until I find a better fix -->
                    <?php } ?>

                </div>

            </div>
        </div>

<!-- END OF HEADER -->

<!-- START OF CONTENT -->

        <div id="page-content-wrapper" class="wrapper clearfix">
            <div id="page-content">
                <div id="region-main-box">
                    <div id="region-post-box">

                        <div id="region-main-wrap">
                            <div id="region-main">
                                <div class="region-content">

                                    <div id="region-header">
                                        <h1 class="headermain"><?php echo $PAGE->heading ?></h1>
                                        <p class="tagline"><?php echo $tagline ?></p>
                                    </div>

                                    <?php echo $OUTPUT->main_content() ?>

                                </div>
                            </div>
                        </div>

                        <?php if ($hassidepost) { ?>
                        <div id="region-post" class="block-region">
                            <div id="region-post-wrap-1">
                                <div id="region-post-wrap-2">
                                    <div class="region-content">
                                        <?php echo $OUTPUT->blocks_for_region('side-post') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>

<!-- END OF CONTENT -->

    </div> <!-- END #wrapper -->

        </div>
    </div>

<!-- START OF FOOTER -->
    <div id="page-footer" class="wrapper clearfix">
    <?php echo $footnote ?>
        <p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')) ?></p>
        <?php
            echo $OUTPUT->login_info();
            echo $OUTPUT->home_link();
            echo $OUTPUT->standard_footer_html();
        ?>
    </div>

<!-- END OF FOOTER -->


</div> <!-- END #page -->

<?php echo $OUTPUT->standard_end_of_body_html() ?>
</body>
</html>
