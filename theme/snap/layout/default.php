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
 * Layout - default.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/header.php');

use theme_snap\local;
use theme_snap\output\shared;

// @codingStandardsIgnoreStart
// Note, coding standards ignore is required so that we can have more readable indentation under php tags.

$mastimage = '';
// Check we are in a course (not the site level course), and the course is using a cover image.
if ($COURSE->id != SITEID && !empty($coverimagecss)) {
    $mastimage = 'mast-image';
}
if ($PAGE->pagetype == 'admin-search') {
    $PAGE->set_secondary_navigation(false);
}
?>

<!-- Moodle js hooks -->
<div id="page">
<div id="page-content">

<!--
////////////////////////// MAIN  ///////////////////////////////
-->
<div id="moodle-page" class="clearfix">
<?php
echo $OUTPUT->custom_menu_spacer();
?>
<div id="page-header" class="clearfix <?php echo $mastimage; ?>">
    <?php if ($PAGE->pagetype !== 'site-index') { ?>
        <nav class="breadcrumb-nav" aria-label="breadcrumbs"><?php echo $OUTPUT->snapnavbar($mastimage); ?></nav>
    <?php }
        if ($carousel) {
            // Front page carousel.
            echo $carousel;
        } else {
            // Front page banner image.
    ?>
        <div id="page-mast">
        <?php
            echo $OUTPUT->page_heading();
            echo $OUTPUT->course_header();
            // Content bank for Snap.
            if ($PAGE->pagetype === 'contentbank') {
                echo $OUTPUT->snap_content_bank();
            }
        ?>
        </div>
        <?php
            if ($this->page->user_is_editing() && $PAGE->pagetype == 'site-index') {
                echo $OUTPUT->cover_image_selector();
            }
        } // End else.
        if ($PAGE->pagetype == 'admin-search') {
            echo implode('', $PAGE->get_header_actions());
        }
        ?>
</div>
<div id="region-main-box">
<section id="region-main">
<?php
if ($OUTPUT->snap_page_is_activity_view()) {
    echo $OUTPUT->context_header();
}
echo $OUTPUT->course_content_header();

// Ensure edit blocks button is only shown for appropriate pages.
if ($PAGE->button === null) {
    $hasadminbutton = false;
} else {
    $hasadminbutton = stripos($PAGE->button, '"adminedit"') || stripos($PAGE->button, '"edit"');
}

if ($hasadminbutton) {
    // List paths to black list for 'turn editting on' button here.
    // Note, to use regexs start and end with a pipe symbol - e.g. |^/report/| .
    $editbuttonblacklist = array(
        '/comment/',
        '/cohort/index.php',
        '|^/report/|',
        '|^/admin/|',
        '|^/mod/data/|',
        '/tag/manage.php',
        '/grade/edit/scale/index.php',
        '/outcome/admin.php',
        '/mod/assign/adminmanageplugins.php',
        '/theme/index.php',
        '/user/editadvanced.php',
        '/user/profile/index.php',
        '/mnet/service/enrol/index.php',
        '/local/mrooms/view.php',
    );
    $pagepath = local::current_url_path();

    foreach ($editbuttonblacklist as $blacklisted) {
        if ($blacklisted[0] == '|' && $blacklisted[strlen($blacklisted) - 1] == '|') {
            // Use regex to determine blacklisting.
            if (preg_match ($blacklisted, $pagepath) === 1) {
                // This url path is blacklisted, stop button from being displayed.
                $PAGE->set_button('');
            }
        } else if ($pagepath == $blacklisted) {
            // This url path is blacklisted, stop button from being displayed.
            $PAGE->set_button('');
        }
    }
}

echo "<div class='snap-page-heading-button' >";
if ($PAGE->pagelayout !== 'admin') {
    echo $OUTPUT->page_heading_button();
}
// Validation added to check if settings option should be displayed;
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !local::show_setting_menu() ;
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;
echo $regionmainsettingsmenu;
echo "</div>";
if ($PAGE->pagelayout === 'mycourses') {
    // Add course management options in my courses page.
    echo $OUTPUT->snap_my_courses_management_options();
}
if ($PAGE->pagelayout === 'frontpage' && $PAGE->pagetype === 'site-index') {
    require(__DIR__.'/faux_site_index.php');
} else {
    echo $OUTPUT->activity_header();
    if ($PAGE->has_secondary_navigation() && $PAGE->pagetype == 'mod-data-view') {
        $tablistnav = $PAGE->has_tablist_secondary_navigation();
        $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
        $secondarynavigation = $moremenu->export_for_template($OUTPUT);
        echo $OUTPUT->render_from_template('theme_snap/secondary_navigation', ['secondarymoremenu' => $secondarynavigation]);
    }
    echo $OUTPUT->main_content();
}

echo $OUTPUT->activity_navigation();
echo $OUTPUT->course_content_footer();
if (stripos($PAGE->bodyclasses, 'format-singleactivity') !== false ) {
    // Shared renderer is only loaded if required at this point.
    $output = \theme_snap\output\shared::course_tools();
    if (!empty($output)) {
        echo $output;
    }
}

?>

</section>
</div>

<?php
if($OUTPUT->snap_page_is_whitelisted_mod()){
    echo $OUTPUT->snap_blocks();
}

require __DIR__.'/blocks_drawer.php';
echo $OUTPUT->snap_feeds_side_menu();
?>
</div>

</div>
</div>

<?php echo $OUTPUT->standard_after_main_region_html() ?>
<!-- close moodle js hooks -->
<?php // @codingStandardsIgnoreEnd
require(__DIR__.'/footer.php');
