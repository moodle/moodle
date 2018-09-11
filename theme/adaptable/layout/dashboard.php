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

?>

<?php

if (!empty($PAGE->theme->settings->dashblocksenabled)) { ?>
    <div id="frontblockregion" class="container">
        <div class="row-fluid">
            <?php echo $OUTPUT->get_block_regions('dashblocklayoutlayoutrow'); ?>
        </div>
    </div>
<?php
}
?>

<div class="container outercont">
    <div id="page-content" class="row-fluid">
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
