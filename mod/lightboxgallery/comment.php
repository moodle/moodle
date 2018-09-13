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

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/comment_form.php');

$id      = required_param('id', PARAM_INT);
$delete  = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

if (!$gallery = $DB->get_record('lightboxgallery', array('id' => $id))) {
    print_error('invalidlightboxgalleryid', 'lightboxgallery');
}
list($course, $cm) = get_course_and_cm_from_instance($gallery, 'lightboxgallery');

if ($delete && ! $comment = $DB->get_record('lightboxgallery_comments', array('gallery' => $gallery->id, 'id' => $delete))) {
    print_error('Invalid comment ID');
}

require_login($course, true, $cm);

$PAGE->set_cm($cm);
$PAGE->set_url('/mod/lightboxgallery/view.php', array('id' => $id));
$PAGE->set_title($gallery->name);
$PAGE->set_heading($course->shortname);

$context = context_module::instance($cm->id);

$galleryurl = $CFG->wwwroot.'/mod/lightboxgallery/view.php?id='.$cm->id;

if ($delete && has_capability('mod/lightboxgallery:edit', $context)) {
    if ($confirm && confirm_sesskey()) {
        $DB->delete_records('lightboxgallery_comments', array('id' => $comment->id));
        redirect($galleryurl);
    } else {
        echo $OUTPUT->header();
        lightboxgallery_print_comment($comment, $context);
        echo('<br />');
        $paramsyes = array('id' => $gallery->id, 'delete' => $comment->id, 'sesskey' => sesskey(), 'confirm' => 1);
        $paramsno = array('id' => $cm->id);
        echo $OUTPUT->confirm(get_string('commentdelete', 'lightboxgallery'),
                              new moodle_url('/mod/lightboxgallery/comment.php', $paramsyes),
                              new moodle_url('/mod/lightboxgallery/view.php', $paramsno));
        echo $OUTPUT->footer();
        die();
    }
}

require_capability('mod/lightboxgallery:addcomment', $context);

if (! $gallery->comments) {
    print_error('Comments disabled', $galleryurl);
}

$mform = new mod_lightboxgallery_comment_form(null, $gallery);

if ($mform->is_cancelled()) {
    redirect($galleryurl);
} else if ($formadata = $mform->get_data()) {
    $newcomment = new stdClass;
    $newcomment->gallery = $gallery->id;
    $newcomment->userid = $USER->id;
    $newcomment->commenttext = $formadata->comment['text'];
    $newcomment->timemodified = time();
    if ($DB->insert_record('lightboxgallery_comments', $newcomment)) {
        $params = array(
            'context' => $context,
            'other' => array(
                'lightboxgalleryid' => $gallery->id,
            ),
        );
        $event = \mod_lightboxgallery\event\gallery_comment_created::create($params);
        $event->trigger();

        redirect($galleryurl, get_string('commentadded', 'lightboxgallery'));
    } else {
        print_error('Comment creation failed');
    }
}


echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
