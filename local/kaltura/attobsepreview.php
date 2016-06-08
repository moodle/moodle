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
 * Kaltura LTI service script used receive data sent from the Kaltura content provider.
 *
 * @package    local_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

$PAGE->set_pagelayout('embedded');
echo $OUTPUT->header();
$playurl = urldecode($url);

echo html_writer::tag('h2', get_string('preview', 'local_kaltura'));
?>
<div id="KalturaAttoPreview"></div>
<script>
    var data = {
        'url': "<?php echo $playurl; ?>",
        'width': <?php echo $width; ?>,
        'height': <?php echo $height; ?>,
        'title': "<?php echo addcslashes($title, '"'); ?>"
    };
    parent.kaltura_atto_embed_callback(data);
    var iframe = Y.Node.create('<iframe></iframe>');
    iframe.setAttribute('src', '<?php echo 'bsepreview_ltilaunch.php?playurl=' . urlencode($url); ?>');
    iframe.setAttribute('alt', '<?php echo addcslashes($title, "'"); ?>');
    iframe.setAttribute('allowfullscreen', '');
    iframe.setStyles({
            height: '<?php echo $height; ?>px',
            border: 'none',
            width: '<?php echo $width; ?>px'
        });
        Y.one('#KalturaAttoPreview').setHTML(iframe);
</script>
