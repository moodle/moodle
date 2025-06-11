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
 * Layout - course-index-category.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2017 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require(__DIR__.'/header.php');

// @codingStandardsIgnoreStart
// Note, coding standards ignore is required so that we can have more readable indentation under php tags.

$mastimage = '';
// Check we are in a course (not the site level course), and the course is using a cover image.
if (!empty($coverimagecss)) {
    $mastimage = 'mast-image';
}
?>

<!-- moodle js hooks -->
<div id="page">
    <div id="page-content">
    <!--
    ////////////////////////// MAIN  ///////////////////////////////
    -->
        <div id="moodle-page" class="clearfix">
        <div id="page-header" class="clearfix snap-category-header <?php echo $mastimage; ?>">
        <nav class="breadcrumb-nav" aria-label="breadcrumbs"><?php echo $OUTPUT->snapnavbar($mastimage); ?></nav>
            <div id="page-mast">
            <?php
                $categories = $PAGE->categories;
                if (empty($categories)) {
                    $catname = get_string('courses', 'theme_snap');
                    $catname = format_text($catname);
                    echo '<h1>' . html_to_text(s($catname)) . '</h1>';
                } else {
                    // Get the current category name and description.
                    $cat = reset($categories);
                    $catid = $cat->id;
                    $catname = format_text($cat->name);
                    $catdescription = $cat->description;

                    // Category edit link.
                    $editcatagory = '';
                    if (can_edit_in_category($catid)) {
                        $editurl = new moodle_url('/course/editcategory.php', ['id' => $catid]);
                        $editcatagory = '<div class="ms-3"><a href=" '.$editurl.' " class="btn btn-secondary">'
                                .get_string('categoryedit', 'theme_snap').'</a></div>';
                    }

                    // Category summary.
                    $catsummary = '';
                    if ($catdescription) {
                        $content = context_coursecat::instance($cat->id);
                        $catdescription = file_rewrite_pluginfile_urls($catdescription,
                            'pluginfile.php', $content->id, 'coursecat', 'description', null);
                        $options = array('noclean' => true, 'overflowdiv' => false);
                        $catsummary = '<div class="snap-category-description">'
                            .format_text($catdescription, $cat->descriptionformat, $options).'</div>';
                    }
                    echo '<h1>' . html_to_text(s($catname)) . '</h1>';
                    echo $catsummary;
                }

                $iscoursecat = $PAGE->context->contextlevel === CONTEXT_COURSECAT;
                $manageurl = false;
                if (has_capability('moodle/category:manage', $PAGE->context)) {
                    $manageurl = new moodle_url('/course/management.php');
                    if ($iscoursecat) {
                        echo '<div class="text-right">' . $OUTPUT->cover_image_selector() . '</div>';
                    }
                }
                ?>
            </div>
        </div>
        <section id="region-main">
            <div class="d-inline-flex">
                <?php
                global $OUTPUT;
                $context = get_category_or_system_context(empty($cat) ? 0 : $cat->id);
                if($iscoursecat) {
                    if (has_capability('moodle/course:create', $context)) {
                        // Print link to create a new course, for the 1st available category.
                        if ($cat->id) {
                            $url = new moodle_url('/course/edit.php', ['category' => $cat->id, 'returnto' => 'category']);
                        } else {
                            $url = new moodle_url('/course/edit.php', ['category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat']);
                        }
                        echo '<div><a class="btn btn-secondary" href="' . $url . '">' .
                            get_string('addnewcourse', 'moodle') . '</a></div>';
                    }
                    if (has_capability('moodle/category:manage', $context)) {
                        $addsubcaturl = new moodle_url('/course/editcategory.php', array('parent' => $cat->id));
                        echo '<div><a class="btn btn-secondary ms-3" href="' . $addsubcaturl . '">' .
                            get_string('addsubcategory', 'moodle') . '</a></div>';
                    }
                    if ($manageurl) {
                        echo '<p><a class="btn btn-secondary ms-3" href="' . $manageurl . '">';
                        echo get_string('managecourses', 'moodle') . '</a></p>';
                    }
                    if (!empty($editcatagory)) {
                        echo $editcatagory;
                    }
                    echo $OUTPUT->container_start('buttons ms-3');
                    if (\core_course_category::is_simple_site() == 1) {
                        snap_print_course_request_buttons(\context_system::instance());
                    } else {
                        snap_print_course_request_buttons($context);
                    }
                    echo $OUTPUT->container_end();
                    echo "</div>";
                }else {
                    if ($manageurl) {
                        echo '<p><a class="btn btn-secondary" href="' . $manageurl . '">';
                        echo get_string('managecourses', 'moodle') . '</a></p>';
                    }
                    if (has_capability('moodle/course:create', $context)) {
                        // Print link to create a new course, for the 1st available category.
                        $url = new moodle_url('/course/edit.php', ['category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat']);
                        echo '<div><a class="btn btn-secondary ms-3" href="' . $url . '">' .
                            get_string('addnewcourse', 'moodle') . '</a></div>';
                    }
                    echo "</div>";
                }
                
                echo $OUTPUT->main_content();
                ?>
        </section>
        </div>
        <div id="moodle-blocks" class="clearfix">
            <?php echo $OUTPUT->custom_block_region('side-pre'); ?>
        </div>
        <?php
        require __DIR__.'/blocks_drawer.php';
        echo $OUTPUT->snap_feeds_side_menu();
        ?>
    </div>
</div>
<?php echo $OUTPUT->standard_after_main_region_html() ?>
    <!-- close moodle js hooks -->
<?php
// @codingStandardsIgnoreEnd
require(__DIR__.'/footer.php');
