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
 *
 * @package   theme_lambda
 * @copyright 2020 redPIthemes
 *
 */
 
$footerl = 'footer-left';
$footerm = 'footer-middle';
$footerr = 'footer-right';

$hasfootnote = (empty($PAGE->theme->settings->footnote)) ? false : $PAGE->theme->settings->footnote;
$hasfooterleft = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('footer-left', $OUTPUT));
$hasfootermiddle = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('footer-middle', $OUTPUT));
$hasfooterright = (empty($PAGE->layout_options['noblocks']) && $PAGE->blocks->region_has_content('footer-right', $OUTPUT));

?>
	<div class="row-fluid">
		<?php
            		echo $OUTPUT->footerblocks($footerl, 'span4');

            		echo $OUTPUT->footerblocks($footerm, 'span4');

            		echo $OUTPUT->footerblocks($footerr, 'span4');
		?>
 	</div>

	<div class="footerlinks">
    	<div class="row-fluid">
    		<p class="helplink"><?php echo page_doc_link(get_string('moodledocslink')); ?></p>
    		<?php if ($hasfootnote) {
				$footnote_HTML = format_text($hasfootnote,FORMAT_HTML);
        		echo '<div class="footnote">'.$footnote_HTML.'</div>';
    		} ?>
		</div>
        
        <?php if($PAGE->theme->settings->socials_position==0) { ?>
    		<?php require_once(dirname(__FILE__).'/socials.php');?>
    	<?php
		} ?>
        
   	</div>