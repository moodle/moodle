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
 * Question Import for H5P Quiz content type
 *
 * @package    qformat_h5p
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use qformat_h5p\local;

/**
 * Question Import for H5P Quiz content type
 *
 * @copyright  2020 Daniel Thies <dethies@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qformat_h5p extends qformat_default {
    /** @var $tempdir Tempory directory path */
    protected $tempdir = '';

    /**
     * Imoport functionality
     *
     * @return bool whether this plugin provides import functionality.
     */
    public function provide_import() {
        return true;
    }

    /**
     * Export functionality
     *
     * @return bool whether this plugin provides export functionality.
     */
    public function provide_export() {
        return false;
    }

    /**
     * File extension
     *
     * @return string the file extension (including .) that is normally used for
     * files handled by this plugin.
     */
    public function export_file_extension() {
        return '.h5p';
    }

    /**
     * The string mime-type of the files that this plugin reads or writes.
     *
     * @return string
     */
    public function mime_type() {
        // This is a hack to support version before h5p support.
        if (mimeinfo('type', $this->export_file_extension()) == 'document/unknown') {
            return mimeinfo('type', '.zip');
        }
        return mimeinfo('type', $this->export_file_extension());
    }

    /**
     * Return the content of a file given by its path in the tempdir directory.
     *
     * @param string $path path to the file inside tempdir
     * @return mixed contents array or false on failure
     */
    public function get_filecontent($path) {
        $fullpath = $this->tempdir . '/' . $path;
        if (is_file($fullpath) && is_readable($fullpath)) {
            return file_get_contents($fullpath);
        }
        return false;
    }


    /**
     * Return content of all files containing questions,
     * as an array one element for each file found,
     * For each file, the corresponding element is an array of lines.
     *
     * @param string $filename name of file
     * @return mixed contents array or false on failure
     */
    public function readdata($filename) {
        $this->tempdir = make_request_directory();
        if (is_readable($filename)) {
            if (!copy($filename, $this->tempdir . '/content.zip')) {
                $this->error(get_string('cannotcopybackup', 'question'));
                fulldelete($this->tempdir);
                return false;
            }
            $packer = get_file_packer('application/zip');
            if ($packer->extract_to_pathname($this->tempdir . '/content.zip', $this->tempdir)) {
                $h5p = json_decode($this->get_filecontent('h5p.json'));

                $content = json_decode($this->get_filecontent('content/content.json'));

                return $this->read_content($h5p->mainLibrary, $content, $h5p->title);
            } else {
                $this->error(get_string('cannotunzip', 'question'));
                fulldelete($this->temp_dir);
            }
        } else {
            $this->error(get_string('cannotreaduploadfile', 'error'));
            fulldelete($this->tempdir);
        }
        return false;
    }

    /**
     * Parse the a content object
     *
     * @param string $library The main library for content.
     * @param object $content The content object.
     * @param string $title default title for question
     * @return array (of objects) question objects.
     */
    public function read_content($library, $content, $title) {

        $questions = [];

        switch ($library) {
            case 'H5P.InteractiveBook':
                $content->content = array_reduce(
                    $content->chapters,
                    function ($carry, $content) {
                        return array_merge($carry, $content->params->content);
                    },
                    []
                );
                return array_column($content->content, 'content');
            case 'H5P.BranchingScenario':
                $questions = [];
                foreach ($content->branchingScenario->content as $subcontent) {
                    $questions += $this->read_content(
                        preg_replace('/ .*/', '', $subcontent->type->library),
                        $subcontent->type->params,
                        $title
                    );
                }
                return $questions;
            case 'H5P.Column':
                return array_column($content->content, 'content');
            case 'H5P.CoursePresentation':
                $actions = [];

                foreach ($content->presentation->slides as $slide) {
                    foreach (array_column($slide->elements, 'action') as $action) {
                        $actions = array_merge($actions, $this->read_subcontent($action));
                    }
                }
                return $actions;
            case 'H5P.Crossword':
                $dialogs = [];
                foreach ($content->words as $word) {
                    $dialogs[] = (object) [
                        'params' => (object) [
                            'question' => $word->clue,
                            'answer' => $word->answer,
                        ],
                        'library' => 'Dialogcards',
                        'metadata' => (object) [
                            'title' => $word->answer,
                        ],
                    ];
                }
                return $dialogs;
            case 'H5P.Flashcards':
                $content->dialogs = $content->cards;
                // Handle like dialog cards.
            case 'H5P.Dialogcards':
                $dialogs = [];
                foreach ($content->dialogs as $dialog) {
                    $dialogs[] = (object) [
                        'params' => (object) [
                            'question' => $dialog->text,
                            'answer' => $dialog->answer,
                            'audio' => $dialog->audio ?? null,
                            'media' => (object) [
                                'type' => (object) [
                                    'params' => (object) [
                                        'file' => $dialog->image,
                                        'contentName' => 'Image',
                                    ],
                                ],
                            ],
                        ],
                        'library' => 'Dialogcards',
                        'metadata' => (object) [
                            'title' => $dialog->text,
                        ],
                    ];
                }
                return $dialogs;
            case 'H5P.InteractiveVideo':
                return array_column($content->interactiveVideo->assets->interactions, 'action');
            case 'H5P.QuestionSet':
                return $content->questions;
            case 'H5P.SingleChoiceSet':
                return $this->read_choices($content);
            default:
                $question = new stdClass();
                $question->params = $content;
                $question->metadata = (object) [
                    'title' => $title,
                ];
                $question->library = $library;
                return [$question];
        }
    }
    /**
     * Parse the array of objects into an array of questions.
     *
     * @param array $lines array of json decoded h5p content objects for each input file.
     * @return array (of objects) question objects.
     */
    public function readquestions($lines) {

        // Set up array to hold all our questions.
        $questions = [];

        // Each element of $lines is a h5p content type with data.
        foreach ($lines as $content) {
            if (($type = $this->create_content_type($content)) && $qo = $type->import_question()) {
                $questions[] = $qo;
            }
        }
        return $questions;
    }

    /**
     * Extract questions from subcontent of course presentation data
     *
     * @param object $content
     * @return array question data to be imported
     */
    public function read_subcontent($content) {
        switch (preg_replace('/ .*/', '', $content->library)) {
            case 'H5P.QuestionSet':
                return $content->questions;
            case 'H5P.SingleChoiceSet':
                return $this->read_choices($content->params);
            default:
                return [$content];
        }
    }

    /**
     * Reformat Single choice set subcontent as multiple choice struncture
     *
     * @param object $content
     * @return array multichoice content to be imported
     */
    public function read_choices($content) {
        $questions = [];
        foreach ($content->choices as $choice) {
            $answers = [];
            foreach ($choice->answers as $key => $answer) {
                $answers[] = (object) [
                    'text' => $answer,
                    'correct' => empty($key),
                    'tipsAndFeedback' => (object) [
                        'chosenFeedback' => empty($key) ? ($content->l10n->correctText ?? '') : $content->l10n->incorrectText ?? '',
                    ],
                ];
            }
            $questions[] = (object) [
                'params' => (object) [
                    'question' => $choice->question,
                    'answers' => $answers,
                ],
                'metadata' => (object) [
                    'title' => $choice->question,
                ],
                'library' => 'H5P.MultiChoice',
            ];
        };
        return $questions;
    }

    /**
     * Find read question type from content and provide appropriate converter
     *
     * @param object $content question data
     * @return object import object
     */
    public function create_content_type($content) {
        if (empty($content->library)) {
            return '';
        }
        switch (preg_replace('/ .*/', '', $content->library)) {
            case 'H5P.AdvancedBlanks':
                return new local\type_afib($content, $this->tempdir);
            case 'H5P.Blanks':
                return new local\type_fib($content, $this->tempdir);
            case 'Dialogcards':
                return new local\type_card($content, $this->tempdir);
            case 'H5P.GuessTheAnswer':
                return new local\type_guess($content, $this->tempdir);
            case 'H5P.ImageMultipleHotspotQuestion':
            case 'H5P.ImageHotspotQuestion':
                return new local\type_hotspot($content, $this->tempdir);
            case 'H5P.ImageSequencing':
                return new local\type_imagesequence($content, $this->tempdir);
            case 'H5P.MultiChoice':
                return new local\type_mc($content, $this->tempdir);
            case 'H5P.TrueFalse':
                return new local\type_tf($content, $this->tempdir);
            case 'H5P.DragQuestion':
                return new local\type_dnd($content, $this->tempdir);
            case 'H5P.DragText':
                return new local\type_dtw($content, $this->tempdir);
            case 'H5P.Essay':
                return new local\type_essay($content, $this->tempdir);
            case 'H5P.MarkTheWords':
                return new local\type_mtw($content, $this->tempdir);
            default:
                return '';
                return new local\type_desc($content, $this->tempdir); // This is more helpful for debugging.
        }
    }
}
