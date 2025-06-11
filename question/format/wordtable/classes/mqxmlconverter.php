<?php
// This file is part of Moodle - http://moodle.org/
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Import/Export Moodle Question XML library.
 *
 * @package    qformat_wordtable
 * @copyright  2021 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace qformat_wordtable;

use booktool_wordimport\wordconverter;
use moodle_exception;

/**
 * Convert XHTML into XML and vice versa.
 *
 * Class to convert XHTML strings into Moodle Question XML (import), and convert Moodle Question XML into XHTML strings (export).
 */
class mqxmlconverter {
    /** @var string Stylesheet to export Moodle Question XML into XHTML */
    private $mqxml2xhtmlstylesheet = __DIR__ . "/mqxml2xhtml.xsl";

    /** @var string Stylesheet to import XHTML into question XML */
    private $xhtml2mqxmlstylesheet = __DIR__ . "/xhtml2mqxml.xsl";

    /**
     * Class constructor
     *
     * @param string $plugin Name of plugin module
     */
    public function __construct(string $plugin = 'qformat_wordtable') {
        global $CFG, $USER, $COURSE;

        // Set common parameters for all XSLT transformations.
        $this->xsltparameters = [
            'course_id' => $COURSE->id,
            'course_name' => $COURSE->fullname,
            'author_name' => $USER->firstname . ' ' . $USER->lastname,
            'moodle_country' => $USER->country,
            'moodle_language' => current_language(),
            'moodle_textdirection' => (right_to_left()) ? 'rtl' : 'ltr',
            'moodle_release' => $CFG->release, // Moodle version, e.g. 3.5, 3.10.
            'moodle_release_date' => substr($CFG->version, 0, 8), // The Moodle major version release date, for testing.
            'moodle_url' => $CFG->wwwroot . "/",
            'moodle_username' => $USER->username,
            'imagehandling' => 'embedded', // Question banks are embedded, Lessons are referenced.
            'heading1stylelevel' => 1, // Question banks are 1, Lessons should be overridden to 3.
            'pluginname' => $plugin,
            'debug_flag' => (debugging(null, DEBUG_DEVELOPER)) ? '1' : '0',
            ];
    }

    /**
     * Convert XHTML into Moodle Question XML.
     *
     * @param string $xhtmldata XHTML-formatted content
     * @param string $imagedata Base64-encoded images
     * @param array $parameters Extra XSLT parameters, if any
     * @return string Processed XML content
     */
    public function import(string $xhtmldata, string $imagedata, array $parameters = []) {
        global $CFG;

        // Set common parameters for all XSLT transformations. Note that the XSLT processor doesn't support $arguments.
        $this->xsltparameters = array_merge($this->xsltparameters, $parameters);

        // Check that the XSLT stylesheet exists.
        if (!file_exists($this->xhtml2mqxmlstylesheet)) {
            throw new \moodle_exception(get_string('stylesheetunavailable', 'qformat_wordtable', $this->xhtml2mqxmlstylesheet));
        }

        // Merge and wrap all the required input data into a single string to simplify XSLT processing.
        $xhtmldata = "<pass3Container>\n" . $xhtmldata .
            "<imagesContainer>\n" . $imagedata . "</imagesContainer>\n" .
            $this->get_question_labels() . "\n</pass3Container>";

        // Use the Book tool wordimport plugin to do the actual XSLT transformation.
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);
        $mqxml = $word2xml->xsltransform($xhtmldata, $this->xhtml2mqxmlstylesheet);
        return $mqxml;
    }  // End import function.

    /**
     * Export Moodle Question XML into Word-compatible XHTML
     *
     * This export function is for Question bank questions only, not for Lesson questions.
     * It exports all questions in a category.
     *
     * @param string $mqxml Moodle Question XML
     * @param string $imagehandling Embedded or encoded image data
     * @return string XHTML text
     */
    public function export(string $mqxml, string $imagehandling = 'embedded') {
        $this->xsltparameters['exportimagehandling'] = $imagehandling;
        // Check that the XSLT stylesheet exists.
        if (!file_exists($this->mqxml2xhtmlstylesheet)) {
            throw new \moodle_exception(get_string('stylesheetunavailable', 'qformat_wordtable', $this->mqxml2xhtmlstylesheet));
        }

        // Clean up the Question XML to ensure it is well-formed XML and won't break the XSLT processing.
        $mqxml = $this->clean_all_questions($mqxml);

        // Merge and wrap all the required input data into a single string to simplify XSLT processing.
        $moodlelabels = $this->get_question_labels();
        $mqxml = "<container>\n<quiz>" . $mqxml . "</quiz>\n" . $moodlelabels . "\n</container>";

        // Use the Book tool wordimport plugin to do the actual XSLT transformation.
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);
        $xhtmldata = $word2xml->xsltransform($mqxml, $this->mqxml2xhtmlstylesheet);

        // Embed the XHTML tables into a Word-compatible template document with styling information, etc.
        $content = $word2xml->export($xhtmldata, $this->xsltparameters['pluginname'], $moodlelabels, 'embedded');
        return $content;
    }   // End export function.

    /**
     * Convert Moodle Question XML into generic XHTML
     *
     * This function converts a single Lesson question into generic XHTML.
     *
     * @param string $mqxml Moodle Question XML
     * @param string $imagehandling Embedded or encoded image data
     * @return string XHTML text
     */
    public function convert_mqx2htm(string $mqxml, string $imagehandling = 'embedded') {
        $this->xsltparameters['exportimagehandling'] = $imagehandling;
        // Check that the XML to XHTML conversion XSLT stylesheet exists.
        if (!file_exists($this->mqxml2xhtmlstylesheet)) {
            throw new \moodle_exception(get_string('stylesheetunavailable', 'qformat_wordtable', $this->mqxml2xhtmlstylesheet));
        }

        // Clean up the Question XML to ensure it is well-formed XML and won't break the XSLT processing.
        $mqxml = $this->clean_all_questions($mqxml);

        // Merge and wrap all the required input data into a single string to simplify XSLT processing.
        $moodlelabels = $this->get_core_question_labels();
        $mqxml = "<container>\n<quiz>" . $mqxml . "</quiz>\n" . $moodlelabels . "\n</container>";

        // Use the Book tool wordimport plugin to do the actual XSLT transformation.
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);
        $xhtmldata = $word2xml->xsltransform($mqxml, $this->mqxml2xhtmlstylesheet, $this->xsltparameters);
        $matches = null;
        if (preg_match('/<div[^>]*>(.+)<\/div>/is', $xhtmldata, $matches)) {
            $xhtmldata = $matches[1];
        }
        return $xhtmldata;
    }   // End convert_mqx2htm function.

    /**
     * Convert a question in a generic XHTML table into Moodle Question XML
     *
     * This function converts a single XHTML question table into a question.
     *
     * @param string $xhtmltable Question table
     * @param string $imagehandling Embedded or encoded image data
     * @return string XHTML text
     */
    public function convert_htm2mqx(string $xhtmltable, string $imagehandling = 'referenced') {
        $this->xsltparameters['imagehandling'] = $imagehandling;
        // Check that the XML to XHTML conversion XSLT stylesheet exists.
        if (!file_exists($this->xhtml2mqxmlstylesheet)) {
            throw new \moodle_exception(get_string('stylesheetunavailable', 'qformat_wordtable', $this->xhtml2mqxmlstylesheet));
        }

        // Merge and wrap all the required input data into a single string to simplify XSLT processing.
        $moodlelabels = $this->get_core_question_labels();
        $xhtmltable = "<pass3Container>\n" . $xhtmltable . "<imagesContainer></imagesContainer>\n" .
                $moodlelabels . "\n</pass3Container>";

        // Use the Book tool wordimport plugin to do the actual XSLT transformation.
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);
        $mqxml = $word2xml->xsltransform($xhtmltable, $this->xhtml2mqxmlstylesheet, $this->xsltparameters);
        return $mqxml;
    }   // End convert_htm2mqx function.

    /**
     * Get the core question text strings needed to fill in table labels
     *
     * A string containing XML data, populated from the language folders, is returned
     *
     * @return string
     */
    public function get_core_question_labels() {
        global $CFG;

        // Release-independent list of all strings required in the XSLT stylesheets for labels etc.
        $textstrings = [
            'grades' => ['item'],
            'moodle' => ['categoryname', 'no', 'yes', 'feedback', 'format', 'formathtml', 'formatmarkdown',
                            'formatplain', 'formattext', 'gradenoun', 'question', 'tags'],
            'qformat_wordtable' => ['cloze_instructions', 'cloze_distractor_column_label', 'cloze_feedback_column_label',
                            'cloze_mcformat_label', 'description_instructions', 'essay_instructions',
                            'interface_language_mismatch', 'multichoice_instructions', 'truefalse_instructions',
                            'transformationfailed', 'unsupported_instructions'],
            'qtype_description' => ['pluginnamesummary'],
            'qtype_ddimageortext' => ['pluginnamesummary', 'bgimage', 'dropbackground', 'dropzoneheader',
                    'draggableitem', 'infinite', 'label', 'shuffleimages', 'xleft', 'ytop'],
            'qtype_ddmarker' => ['pluginnamesummary', 'bgimage', 'clearwrongparts', 'coords',
                'dropbackground', 'dropzoneheader', 'infinite', 'marker', 'noofdrags', 'shape_circle',
                'shape_polygon', 'shape_rectangle', 'shape', 'showmisplaced', 'stateincorrectlyplaced'],
            'qtype_ddwtos' => ['pluginnamesummary', 'infinite'],
            'qtype_essay' => ['acceptedfiletypes', 'allowattachments', 'attachmentsrequired', 'formatnoinline',
                            'graderinfo', 'formateditor', 'formateditorfilepicker',
                            'formatmonospaced', 'formatplain', 'pluginnamesummary', 'responsefieldlines', 'responseformat',
                            'responseisrequired', 'responsenotrequired',
                            'responserequired', 'responsetemplate', 'responsetemplate_help'],
            'qtype_gapselect' => ['pluginnamesummary', 'errornoslots', 'group', 'shuffle'],
            'qtype_match' => ['blanksforxmorequestions', 'filloutthreeqsandtwoas'],
            'qtype_multichoice' => ['answernumbering', 'choiceno', 'correctfeedback', 'incorrectfeedback',
                            'partiallycorrectfeedback', 'pluginnamesummary', 'shuffleanswers'],
            'qtype_shortanswer' => ['casesensitive', 'filloutoneanswer'],
            'qtype_truefalse' => ['false', 'true'],
            'question' => ['addmorechoiceblanks', 'category', 'clearwrongparts', 'correctfeedbackdefault',
                            'defaultmark', 'generalfeedback', 'hintn', 'hintnoptions', 'idnumber',
                            'incorrectfeedbackdefault', 'partiallycorrectfeedbackdefault',
                            'penaltyforeachincorrecttry', 'questioncategory', 'shownumpartscorrect',
                            'shownumpartscorrectwhenfinished'],
            'quiz' => ['answer', 'answers', 'casesensitive', 'correct', 'correctanswers',
                            'defaultgrade', 'incorrect', 'shuffle'],
            ];

        // Get the question type field labels, to populate table cell labels on export, recognise cell labels on import.
        $questionlabels = "<moodlelabels>\n";
        foreach ($textstrings as $typegroup => $grouparray) {
            foreach ($grouparray as $stringid) {
                // Use 'grade' instead of 'gradenoun' in Moodle versions prior to 3.11.6.
                if ($CFG->version < 2021051706 && $stringid === 'gradenoun') {
                    $labeltext = get_string('grade', 'moodle');
                } else {
                    $labeltext = get_string($stringid, $typegroup);
                }
                $namestring = $typegroup . '_' . $stringid;
                $questionlabels .= '<data name="' . $namestring . '"><value>' . $labeltext . "</value></data>\n";
            }
        }
        $questionlabels .= "</moodlelabels>";

        // Ensure the XML is well-formed, as the standard label and help text strings may have been overridden on some sites.
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);
        $questionlabels = $word2xml->convert_to_xml($questionlabels);
        $questionlabels = str_replace("<br>", "<br/>", $questionlabels);

        return $questionlabels;
    }

    /**
     * Get the core and contributed question text strings needed to fill in table labels
     *
     * A string containing XML data, populated from the language folders, is returned.
     * We need to split core from contributed questions to support the Core questions in Lessons.
     *
     * @return string
     */
    private function get_question_labels() {
        global $CFG;

        // Get the core question labels first.
        $questionlabels = $this->get_core_question_labels();

        // Append All-or-Nothing MCQ question type strings if present.
        if (\question_bank::is_qtype_installed('multichoiceset')) {
            // Strip out the closing element first so that we can insert the extra labels.
            $questionlabels = str_replace("</moodlelabels>", "", $questionlabels);

            $textstrings['qtype_multichoiceset'] = ['pluginnamesummary', 'showeachanswerfeedback'];
            foreach ($textstrings as $typegroup => $grouparray) {
                foreach ($grouparray as $stringid) {
                    $namestring = $typegroup . '_' . $stringid;
                    // Get the question type field label text.
                    $labeltext = get_string($stringid, $typegroup);
                    $questionlabels .= '<data name="' . $namestring . '"><value>' . $labeltext . "</value></data>\n";
                }
            }
            $questionlabels .= "</moodlelabels>";
        }

        // Ensure the XML is well-formed, as the standard label and help text strings may have been overridden on some sites.
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);
        $questionlabels = $word2xml->convert_to_xml($questionlabels);
        $questionlabels = str_replace("<br>", "<br/>", $questionlabels);

        return $questionlabels;
    }

    /**
     * Clean HTML markup inside question text element content
     *
     * A string containing Moodle Question XML with clean HTML inside the text elements is returned.
     *
     * @param string $questionxmlstring Question XML text
     * @return string
     */
    private function clean_all_questions($questionxmlstring) {
        // Start assembling the cleaned output string, starting with empty.
        $cleanquestionxml = "";
        $word2xml = new wordconverter($this->xsltparameters['pluginname']);

        // Split the string into questions in order to check the text fields for clean HTML.
        $foundquestions = preg_match_all('~(.*?)<question type="([^"]*)"[^>]*>(.*?)</question>~s', $questionxmlstring,
                            $questionmatches, PREG_SET_ORDER);
        $numquestions = count($questionmatches);
        if ($foundquestions === false || $foundquestions == 0) {
            return $questionxmlstring;
        }

        // Split the questions into text strings to check the HTML.
        for ($i = 0; $i < $numquestions; $i++) {
            $qtype = $questionmatches[$i][2];
            $questioncontent = $questionmatches[$i][3];
            // Split the question into chunks at CDATA boundaries, using ungreedy (?) and matching across newlines (s modifier).
            $foundcdatasections = preg_match_all('~(.*?)<\!\[CDATA\[(.*?)\]\]>~s', $questioncontent, $cdatamatches, PREG_SET_ORDER);
            if ($foundcdatasections === false) {
                $cleanquestionxml .= $questionmatches[$i][0];
            } else if ($foundcdatasections != 0) {
                $numcdatasections = count($cdatamatches);
                // Found CDATA sections, so first add the question start tag and then process the body.
                $cleanquestionxml .= '<question type="' . $qtype . '">';

                // Process content of each CDATA section to clean the HTML.
                for ($j = 0; $j < $numcdatasections; $j++) {
                    $cleancdatacontent = $word2xml->clean_html_text($cdatamatches[$j][2]);

                    // Add all the text before the first CDATA start boundary, and the cleaned string, to the output string.
                    $cleanquestionxml .= $cdatamatches[$j][1] . '<![CDATA[' . $cleancdatacontent . ']]>';
                } // End CDATA section loop.

                // Add the text after the last CDATA section closing delimiter.
                $textafterlastcdata = substr($questionmatches[$i][0], strrpos($questionmatches[$i][0], "]]>") + 3);
                $cleanquestionxml .= $textafterlastcdata;
            } else {
                $cleanquestionxml .= $questionmatches[$i][0];
            }
        } // End question element loop.

        return $cleanquestionxml;
    }
}
