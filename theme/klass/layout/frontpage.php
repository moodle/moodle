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
 * frontpage.php
 *
 * @package   theme_klass
 * @copyright 2015 Lmsace Dev Team,lmsace.com
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
// Get the HTML for the settings bits.
$html = theme_klass_get_html_for_settings($OUTPUT, $PAGE);

if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}

$courserenderer = $PAGE->get_renderer('core', 'course');

echo $OUTPUT->doctype() ?>
<html <?php echo $OUTPUT->htmlattributes(); ?>>
<head>
    <title><?php echo $OUTPUT->page_title(); ?></title>
    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>" />
    <?php echo $OUTPUT->standard_head_html() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body <?php echo $OUTPUT->body_attributes(); ?>>

<?php echo $OUTPUT->standard_top_of_body_html() ?>

<?php
require_once(dirname(__FILE__) . '/includes/header.php');  ?>
<!--Custom theme header-->
<div class="">
    <?php
        $toggleslideshow = theme_klass_get_setting('toggleslideshow');
    if ($toggleslideshow == 1) {
            require_once(dirname(__FILE__) . '/includes/slideshow.php');
    }
    ?>
</div>
    <?php
    $whotitle = theme_klass_get_setting('whoweare_title');
    $whodesc = theme_klass_get_setting('whoweare_description');
    if (!empty($whotitle) || !empty($whodesc)) {
?>
<!--Custom theme slider-->
<div class="fp-site-customdesc">
    <div class="container">
    <h2><?php echo $whotitle; ?></h2>
    <?php if ($whodesc) { ?>
        <p><?php echo $whodesc; ?></p>
        <?php } ?>
  </div>
</div>
    <?php
    } ?>
<!--Custom theme Who We Are block-->
<div id="page" class="container">
    <header id="page-header" class="clearfix">
        <?php echo $html->heading; ?>
        <div id="page-navbar" class="clearfix">
            <nav class="breadcrumb-nav"><?php echo $OUTPUT->navbar(); ?></nav>
            <div class="breadcrumb-button"><?php echo $OUTPUT->page_heading_button(); ?></div>
        </div>
        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
    </header>
    <div id="page-content" class="row">
    <?php
    if (!empty($OUTPUT->blocks_for_region('side-pre'))) {
        $class = "col-md-9";
    } else {
        $class = "col-md-12";
    }
    ?>
        <div id="<?php echo $regionbsid ?>"  class="<?php echo $class; ?>">
                    <?php
                     echo $courserenderer->new_courses();
                        echo $OUTPUT->course_content_header();
                        echo $OUTPUT->main_content();
                        echo $OUTPUT->course_content_footer();
            ?>
        </div>

                <?php echo $OUTPUT->blocks('side-pre', 'col-md-3'); ?>
    </div>
    <?php echo (isset($flatnavbar)) ? $flatnavbar : ""; ?>
</div>
<?php  require_once(dirname(__FILE__) . '/includes/footer.php');  ?>
<!--Custom theme footer-->

</body>
</html>