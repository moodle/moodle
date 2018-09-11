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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2015-2016 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Include header.
require_once(dirname(__FILE__) . '/includes/header.php');

// Set layout.
$left = $PAGE->theme->settings->blockside;
$hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
$regions = theme_adaptable_grid($left, $hassidepost);

$hasfootnote = (!empty($PAGE->theme->settings->footnote));
$hideslidermobile = $PAGE->theme->settings->hideslidermobile;

// Include slider.
if (!empty($PAGE->theme->settings->sliderenabled)) {

    // If it is a mobile and the header is not hidden or it is a desktop then load and show the header.
    if (((theme_adaptable_is_mobile()) && ($hideslidermobile == 1)) || (theme_adaptable_is_desktop())) {
        echo $OUTPUT->get_frontpage_slider();
    }
}

// Infobox 1.
if (!empty($PAGE->theme->settings->infobox)) {
    if (!empty($PAGE->theme->settings->infoboxfullscreen)) {
        echo '<div id="theinfo">';
    } else {
        echo '<div id="theinfo" class="container">';
    }
?>
            <div class="row-fluid">
<?php
    echo $OUTPUT->get_setting('infobox', 'format_html');
?>
            </div>
        </div>
<?php
}
?>

<?php if (!empty($PAGE->theme->settings->frontpagemarketenabled)) {
    echo $OUTPUT->get_marketing_blocks();
}

if (!empty($PAGE->theme->settings->frontpageblocksenabled)) { ?>
    <div id="frontblockregion" class="container">
        <div class="row-fluid">
            <?php echo $OUTPUT->get_block_regions(); ?>
        </div>
    </div>
<?php
}

// Infobox 2.
if (!empty($PAGE->theme->settings->infobox2)) {
    if (!empty($PAGE->theme->settings->infoboxfullscreen)) {
        echo '<div id="theinfo2">';
    } else {
        echo '<div id="theinfo2" class="container">';
    }
?>
        <div class="row-fluid">
<?php
            echo $OUTPUT->get_setting('infobox2', 'format_html');
?>
        </div>
</div>
<?php
}
?>

<div class="container outercont">
    <div id="page-content" class="row-fluid">
     <div id="page-navbar" class="span12">
            <nav class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></nav>
            <?php echo $OUTPUT->navbar(); ?>

    </div>
    <section id="region-main" class="<?php echo $regions['content'];?>">
        <?php
        echo $OUTPUT->course_content_header();
        echo $OUTPUT->main_content();
        echo $OUTPUT->course_content_footer();
        ?>
    </section>
    <?php
        echo $OUTPUT->blocks('side-post', $regions['blocks']);
    ?>
</div>

<?php
if (is_siteadmin()) {
?>
      <div class="hidden-blocks">
        <div class="row-fluid">

        <?php
        if (!empty($PAGE->theme->settings->coursepageblocksliderenabled) ) {
            echo $OUTPUT->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
        }
        ?>

          <h3><?php echo get_string('frnt-footer', 'theme_adaptable') ?></h3>
            <?php
            echo $OUTPUT->blocks('frnt-footer', 'span10');
            ?>
        </div>
      </div>
    <?php
}
?>
</div>

<?php
// Include footer.
require_once(dirname(__FILE__) . '/includes/footer.php');
