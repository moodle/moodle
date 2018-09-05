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
 * Moodle's crisp theme, an example of how to make a Bootstrap theme
 *
 * DO NOT MODIFY THIS THEME!
 * COPY IT FIRST, THEN RENAME THE COPY AND MODIFY IT INSTEAD.
 *
 * For full information about creating Moodle themes, see:
 * http://docs.moodle.org/dev/Themes_2.0
 *
 * @package   theme_crisp
 * @copyright 2014 dualcube {@link http://dualcube.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Get the HTML for the settings bits.
$html = theme_crisp_get_html_for_settings($OUTPUT, $PAGE);
if (right_to_left()) {
    $regionbsid = 'region-bs-main-and-post';
} else {
    $regionbsid = 'region-bs-main-and-pre';
}

?>
<?php require('header.php'); ?>
<div id="show-admin">
  <a class="admin-sets" href="#">
    <span></span>
  </a>
  <div class="adminset">
    <?php 
      echo $OUTPUT->blocks('side-pre');
    ?>
  </div>  
</div> 
<div id="page-content" class="row-fluid">
  <div id="<?php echo $regionbsid ?>" class="span9">
    <div class="row-fluid">
      <section id="region-main" class="span8 pull-right main-span-content">
        <?php
        echo $OUTPUT->course_content_header();
        echo $OUTPUT->main_content();
        echo $OUTPUT->course_content_footer();
        ?>
      </section>
    </div>
  </div>
  <?php echo $OUTPUT->blocks('side-post', 'span3'); ?>
</div>
<?php $PAGE->requires->js_call_amd('theme_crisp/accordeon_cursos','init');?>
<?php require('footer.php');
