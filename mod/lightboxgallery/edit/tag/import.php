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

require_once(dirname(__FILE__).'/../../../../config.php');
require_once(dirname(__FILE__).'/../../lib.php');

$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

if (!$gallery = $DB->get_record('lightboxgallery', array('id' => $id))) {
    print_error('invalidlightboxgalleryid', 'lightboxgallery');
}
if (!$course = $DB->get_record('course', array('id' => $gallery->course))) {
    print_error('invalidcourseid');
}
if (!$cm = get_coursemodule_from_instance('lightboxgallery', $gallery->id, $course->id)) {
    print_error('invalidcoursemodule');
}

require_login($course->id);

$context = context_module::instance($cm->id);
$galleryurl = $CFG->wwwroot . '/mod/lightboxgallery/view.php?id=' . $cm->id;

require_capability('mod/lightboxgallery:edit', $context);

$PAGE->set_cm($cm);
$PAGE->set_url('/mod/lightboxgallery/edit/tag/import.php', array('id' => $id));
$PAGE->set_title($gallery->name);
$PAGE->set_heading($course->shortname);
echo $OUTPUT->header();

$disabledplugins = explode(',', get_config('lightboxgallery', 'disabledplugins'));
if (in_array('tag', $disabledplugins)) {
    print_error(get_string('tagsdisabled', 'lightboxgallery'));
}

if ($confirm && confirm_sesskey()) {
    // For each image, get tags using iptcparse.

    $fs = get_file_storage();
    $storedfiles = $fs->get_area_files($context->id, 'mod_lightboxgallery', 'gallery_images', false, 'itemid', false);

    $a = new stdClass();
    $a->tags = 0;
    $a->images = count($storedfiles);

    if ($a->images > 0) {
        foreach ($storedfiles as $storedfile) {
            if (!$storedfile->is_valid_image()) {
                continue;
            }

            $path = $storedfile->copy_content_to_temp();
            $size = getimagesize($path, $info);
            if (isset($info['APP13'])) {
                $iptc = iptcparse($info['APP13']);
                if (isset($iptc['2#025'])) {
                    sort($iptc['2#025']);
                    $errorlevel = error_reporting(E_PARSE);

                    foreach ($iptc['2#025'] as $tag) {
                        $tag = utf8_encode($tag);
                        $tag = clean_param($tag, PARAM_TAG);
                        $tag = trim(strip_tags($tag));
                        if (empty($tag)) {
                            continue;
                        }
                        $select = "gallery = :gallery AND image = :image
                                   AND metatype = :metatype AND ".$DB->sql_compare_text('description', 100).' = :description';
                        $params = array(
                            'gallery' => $gallery->id,
                            'image' => $storedfile->get_filename(),
                            'metatype' => 'tag',
                            'description' => $tag,
                        );
                        if (!$DB->record_exists_select('lightboxgallery_image_meta', $select, $params)) {
                            $record = new stdClass();
                            $record->gallery = $gallery->id;
                            $record->image = $storedfile->get_filename();
                            $record->metatype = 'tag';
                            $record->description = $tag;
                            if ($DB->insert_record('lightboxgallery_image_meta', $record)) {
                                $a->tags++;
                            }
                        }
                    }
                    error_reporting($errorlevel);
                }
            }
        }
    }

    foreach (array_keys((array)$a) as $b) {
        $a->{$b} = number_format($a->{$b});
    }

    notice(get_string('tagsimportfinish', 'lightboxgallery', $a), $galleryurl);
} else {
    $confirmurl = new moodle_url('/mod/lightboxgallery/edit/tag/import.php',
        array('id' => $gallery->id, 'confirm' => 1, 'sesskey' => sesskey()));
    $cancelurl = new moodle_url('/mod/lightboxgallery/view.php',
        array('id' => $cm->id, 'editing' => 1));
    echo $OUTPUT->confirm(get_string('tagsimportconfirm', 'lightboxgallery'), $confirmurl, $cancelurl);
}

echo $OUTPUT->footer();
