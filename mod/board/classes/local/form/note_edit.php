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

namespace mod_board\local\form;

use mod_board\board;
use mod_board\local\note;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

/**
 * Form for note creatio nand updates.
 *
 * @package     mod_board
 * @author      Eric Merrill <eric.a.merrill@gmail.com>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class note_edit extends \moodleform {
    use \mod_board\local\ajax_form_trait;

    /**
     * Definition of the form elements.
     */
    public function definition() {
        global $PAGE;

        $config = get_config('mod_board');
        $data = $this->_customdata['data'];
        $formatted = $this->_customdata['formatted'];
        $column = $this->_customdata['column'];
        $board = $this->_customdata['board'];

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'columnid');
        $mform->setType('columnid', PARAM_INT);
        $mform->addElement('hidden', 'ownerid');
        $mform->setType('ownerid', PARAM_INT);
        $mform->addElement('hidden', 'groupid');
        $mform->setType('groupid', PARAM_INT);

        $maxlenheading = board::LENGTH_HEADING;
        $mform->addElement(
            'text',
            'heading',
            get_string('form_title', 'mod_board'),
            ['maxlength' => $maxlenheading]
        );
        $mform->setType('heading', PARAM_TEXT);
        $mform->addRule(
            'heading',
            get_string('maximumchars', '', $maxlenheading),
            'maxlength',
            $maxlenheading,
            'client'
        );

        $maxlen = $config->post_max_length;
        $options = ['maxlength' => $maxlen, 'cols' => 30, 'rows' => 4];
        $mform->addElement('textarea', 'content', get_string('form_body', 'mod_board'), $options);
        $mform->setType('content', PARAM_RAW);

        $mform->addElement('checkbox', 'mardownhelpcheckbox', get_string('limited_markdown_checkbox', 'mod_board'));
        $markdown = '<div class="alert alert-info limited_markdown_examples">'
            . get_string('limited_markdown_examples', 'mod_board') . '</div>';
        $mform->addElement('static', 'mardownhelpstatic', '', $markdown);
        $mform->hideIf('mardownhelpstatic', 'mardownhelpcheckbox', 'notchecked');

        $generalpickeroptions = note::get_general_picker_options();

        $options = [
            board::MEDIATYPE_NONE => get_string('option_empty', 'mod_board'),
            board::MEDIATYPE_URL => get_string('option_link', 'mod_board'),
            board::MEDIATYPE_IMAGE => get_string('option_image', 'mod_board'),
            board::MEDIATYPE_FILE => get_string('option_file', 'mod_board'),
            board::MEDIATYPE_YOUTUBE => get_string('option_youtube', 'mod_board'),
        ];
        if (!$generalpickeroptions) {
            unset($options[board::MEDIATYPE_FILE]);
        }
        if (!$config->allowyoutube) {
            unset($options[board::MEDIATYPE_YOUTUBE]);
        }
        $attr = ['class' => 'mod_board_type'];
        $mform->addElement('select', 'mediatype', get_string('form_mediatype', 'mod_board'), $options, $attr);

        if (get_config('mod_board', 'media_selection')) {
            if ($data->id) {
                $addmedia = get_string('aria_addmedia', 'mod_board');
            } else {
                $addmedia = get_string('aria_addmedianew', 'mod_board');
            }
            $titlelink = s(str_replace('{type}', get_string('option_link', 'mod_board'), $addmedia));
            $titleimage = s(str_replace('{type}', get_string('option_image', 'mod_board'), $addmedia));
            $titlefile = s(str_replace('{type}', get_string('option_file', 'mod_board'), $addmedia));
            $titleyoutube = s(str_replace('{type}', get_string('option_youtube', 'mod_board'), $addmedia));

            // phpcs:disable moodle.Files.LineLength.TooLong,moodle.Files.LineLength.MaxExceeded
            $html = '<div class="mod_board_note_buttons">';
            $html .= "<div class=\"mod_board_attachment_button link_button fa fa-link\" role=\"button\" tabindex=\"0\" title='$titlelink' aria-label='$titlelink'></div>";
            $html .= "<div class=\"mod_board_attachment_button image_button fa fa-picture-o\" role=\"button\" tabindex=\"0\"  title='$titleimage' aria-label='$titleimage'></div>";
            if ($generalpickeroptions) {
                $html .= "<div class=\"mod_board_attachment_button file_button fa fa-file-text\" role=\"button\" tabindex=\"0\" title='$titlefile' aria-label='$titlefile'></div>";
            }
            if ($config->allowyoutube) {
                $html .= "<div class=\"mod_board_attachment_button youtube_button fa fa-youtube\" role=\"button\" tabindex=\"0\" title='$titleyoutube' aria-label='$titleyoutube'></div>";
            }
            $html .= '</div>';
            // phpcs:enable moodle.Files.LineLength.TooLong,moodle.Files.LineLength.MaxExceeded

            if ($data->id) {
                $notename = $formatted->identifier;
                $html = str_replace('{post}', $notename, $html);
            }
            $html = str_replace('{column}', note::format_plain_text($column->name), $html);

            $mform->addElement('static', 'mediabuttons', get_string('form_mediatype', 'mod_board'), $html);

            $PAGE->requires->js_call_amd('mod_board/mediatype', 'init', [$mform->getAttribute('id')]);
        }

        // URL.
        $maxleninfo = board::LENGTH_INFO;
        $options = ['maxlength' => $maxleninfo, 'placeholder' => get_string('option_link_info', 'mod_board')];
        $mform->addElement('text', 'linktitle', get_string('option_link_info', 'mod_board'), $options);
        $mform->setType('linktitle', PARAM_TEXT);
        $mform->hideIf('linktitle', 'mediatype', 'neq', board::MEDIATYPE_URL);
        $mform->addRule('linktitle', get_string('maximumchars', '', $maxleninfo), 'maxlength', $maxleninfo, 'client');

        $maxlenurl = board::LENGTH_URL;
        $attr = ['maxlength' => $maxlenurl, 'placeholder' => get_string('option_link_url', 'mod_board'), 'size' => 80];
        $mform->addElement('url', 'linkurl', get_string('option_link_url', 'mod_board'), $attr, ['usefilepicker' => false]);
        $mform->setType('linkurl', PARAM_RAW_TRIMMED);
        $mform->hideIf('linkurl', 'mediatype', 'neq', board::MEDIATYPE_URL);
        $mform->addRule('linkurl', get_string('maximumchars', '', $maxlenurl), 'maxlength', $maxlenurl, 'client');

        // Image file.
        $options = ['maxlength' => $maxleninfo, 'placeholder' => get_string('option_image_info', 'mod_board')];
        $mform->addElement('text', 'imagetitle', get_string('option_image_info', 'mod_board'), $options);
        $mform->setType('imagetitle', PARAM_TEXT);
        $mform->hideIf('imagetitle', 'mediatype', 'neq', board::MEDIATYPE_IMAGE);
        $mform->addRule('imagetitle', get_string('maximumchars', '', $maxleninfo), 'maxlength', $maxleninfo, 'client');

        $imagepickeroptions = note::get_image_picker_options();
        $mform->addElement('filemanager', 'imagefile', get_string('form_image_file', 'mod_board'), null, $imagepickeroptions);
        $mform->hideIf('imagefile', 'mediatype', 'neq', board::MEDIATYPE_IMAGE);

        // General file.
        if ($generalpickeroptions) {
            $mform->addElement(
                'filemanager',
                'generalfile',
                get_string('form_general_file', 'mod_board'),
                null,
                $generalpickeroptions
            );
            $mform->hideIf('generalfile', 'mediatype', 'neq', board::MEDIATYPE_FILE);
        }

        if ($config->allowyoutube) {
            // YouTube video.
            $options = ['maxlength' => $maxleninfo, 'placeholder' => get_string('option_youtube_info', 'mod_board')];
            $mform->addElement('text', 'youtubetitle', get_string('option_youtube_info', 'mod_board'), $options);
            $mform->setType('youtubetitle', PARAM_TEXT);
            $mform->hideIf('youtubetitle', 'mediatype', 'neq', board::MEDIATYPE_YOUTUBE);
            $mform->addRule('youtubetitle', get_string('maximumchars', '', $maxleninfo), 'maxlength', $maxleninfo, 'client');

            $options = ['maxlength' => $maxlenurl, 'placeholder' => get_string('option_youtube_url', 'mod_board'), 'size' => 80];
            $mform->addElement('text', 'youtubeurl', get_string('option_youtube_url', 'mod_board'), $options);
            $mform->setType('youtubeurl', PARAM_RAW_TRIMMED);
            $mform->hideIf('youtubeurl', 'mediatype', 'neq', board::MEDIATYPE_YOUTUBE);
            $mform->addRule('youtubeurl', get_string('maximumchars', '', $maxlenurl), 'maxlength', $maxlenurl, 'client');
        }

        $this->set_data($data);
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $config = get_config('mod_board');
        if ($config->post_max_length) {
            // Unfortunately Moodle forms validation may count new-line characters
            // differently from text area maxlength attribute, so use server-side validation here.
            if (\core_text::strlen(str_replace("\r\n", "\n", $data['content'])) > $config->post_max_length) {
                $errors['content'] = get_string('maximumchars', '', $config->post_max_length);
            }
        }

        if ($data['mediatype'] == board::MEDIATYPE_NONE) {
            if (trim($data['heading']) === '' && trim($data['content']) === '') {
                $errors['heading'] = get_string('required');
            }
        } else if ($data['mediatype'] == board::MEDIATYPE_URL) {
            if (trim($data['linktitle']) === '') {
                $errors['linktitle'] = get_string('required');
            }
            if (trim($data['linkurl']) === '') {
                $errors['linkurl'] = get_string('required');
            } else if (clean_param($data['linkurl'], PARAM_URL) !== $data['linkurl']) {
                $errors['linkurl'] = get_string('invalidurl', 'core_error');
            }
        } else if ($data['mediatype'] == board::MEDIATYPE_IMAGE) {
            if (trim($data['imagetitle']) === '') {
                $errors['imagetitle'] = get_string('required');
            }
            if (!$data['imagefile'] || !note::is_draft_file_present($data['imagefile'])) {
                $errors['imagefile'] = get_string('required');
            }
        } else if ($data['mediatype'] == board::MEDIATYPE_FILE) {
            if (!$data['generalfile'] || !note::is_draft_file_present($data['generalfile'])) {
                $errors['generalfile'] = get_string('required');
            }
        } else if ($data['mediatype'] == board::MEDIATYPE_YOUTUBE) {
            if (trim($data['youtubetitle']) === '') {
                $errors['youtubetitle'] = get_string('required');
            }
            if (trim($data['youtubeurl']) === '') {
                $errors['youtubeurl'] = get_string('required');
            } else if (clean_param($data['youtubeurl'], PARAM_URL) !== $data['youtubeurl']) {
                $errors['youtubeurl'] = get_string('invalidurl', 'core_error');
            } else if (!note::is_youtube_url($data['youtubeurl'])) {
                $errors['youtubeurl'] = get_string('invalidurl', 'core_error');
            }
        }

        return $errors;
    }

    /**
     * Returns formatted attachment data.
     *
     * @param \stdClass $data form data
     * @return array
     */
    public static function get_attachment(\stdClass $data): array {
        // Extract the attachment data.
        $attachment = ['type' => board::MEDIATYPE_NONE];

        switch ($data->mediatype) {
            case board::MEDIATYPE_YOUTUBE:
                if (!empty($data->youtubeurl)) {
                    $attachment = [
                        'type' => board::MEDIATYPE_YOUTUBE,
                        'info' => $data->youtubetitle ?? '',
                        'url' => $data->youtubeurl,
                    ];
                }
                break;
            case board::MEDIATYPE_IMAGE:
                if (!empty($data->imagefile) && note::is_draft_file_present($data->imagefile)) {
                    $attachment = [
                        'type' => board::MEDIATYPE_IMAGE,
                        'info' => $data->imagetitle ?? '',
                        'draftitemid' => $data->imagefile,
                    ];
                }
                break;
            case board::MEDIATYPE_FILE:
                if (!empty($data->generalfile) & note::is_draft_file_present($data->generalfile)) {
                    if (note::get_accepted_general_file_extensions()) {
                        $attachment = [
                            'type' => board::MEDIATYPE_FILE,
                            'draftitemid' => $data->generalfile,
                        ];
                    }
                }
                break;
            case board::MEDIATYPE_URL:
                if (!empty($data->linkurl)) {
                    $attachment = [
                        'type' => board::MEDIATYPE_URL,
                        'info' => $data->linktitle ?? '',
                        'url' => $data->linkurl,
                    ];
                }
                break;
        }

        return $attachment;
    }
}
