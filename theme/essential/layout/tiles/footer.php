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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2017 Gareth J Barnard
 * @copyright   2016 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (empty($PAGE->layout_options['nofooter'])) { ?>
    <footer role="contentinfo" id="page-footer">
        <div class="container-fluid">
            <?php echo $OUTPUT->essential_edit_button('footer'); ?>
            <div class="row-fluid footerblocks">
                <div class="footerblock span4">
                    <?php echo $OUTPUT->essential_blocks('footer-left'); ?>
                </div>
                <div class="footerblock span4">
                    <?php echo $OUTPUT->essential_blocks('footer-middle'); ?>
                </div>
                <div class="footerblock span4">
                    <?php echo $OUTPUT->essential_blocks('footer-right'); ?>
                </div>
            </div>
            <div class="row-fluid">
                <div class="tool_usertours-resettourcontainer span12"></div>
            </div>
            <div class="footerlinks row-fluid">
                <hr/>
                <span class="helplink"><?php echo page_doc_link(get_string('moodledocslink')); ?></span>
                <?php if ($hascopyright) { ?>
                    <span class="copy">&copy;<?php echo userdate(time(), '%Y').' '.$hascopyright; ?></span>
                <?php
}
?>
                <?php if ($hasfootnote) {
                    echo '<div class="footnote span12">'.$hasfootnote.'</div>';
}
?>
            </div>
            <div class="footerperformance row-fluid">
                <?php echo $OUTPUT->standard_footer_html(); ?>
            </div>
        </div>
    </footer>
    <a href="#top" class="back-to-top" aria-label="<?php echo get_string('backtotop', 'theme_essential'); ?>">
        <span aria-hidden="true" class="fa fa-angle-up "></span></a>
<?php }
echo $OUTPUT->standard_end_of_body_html();
