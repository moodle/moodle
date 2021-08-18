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
 * Convert Word tables into Moodle Question XML format
 *
 * The wordtable class inherits from the XML question import class, rather than the
 * default question format class, as this minimises code duplication.
 *
 * This code converts quiz questions between structured Word tables and Moodle
 * Question XML format.
 *
 * The export facility also converts questions into Word files using an XSLT script
 * and an XSLT processor. The Word files are really just XHTML files with some
 * extra markup to get Word to open them and apply styles and formatting properly.
 *
 * @package qformat_wordtable
 * @copyright 2010-2015 Eoin Campbell
 * @author Eoin Campbell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */


defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/xmlize.php");
require_once($CFG->dirroot.'/lib/uploadlib.php');

// Development: turn on all debug messages and strict warnings.
// define('DEBUG_WORDTABLE', E_ALL | E_STRICT);.
define('DEBUG_WORDTABLE', DEBUG_NONE);

// The wordtable plugin just extends XML import/export.
require_once("$CFG->dirroot/question/format/xml/format.php");

// Include XSLT processor functions.
require_once(__DIR__ . "/xslemulatexslt.inc");

/**
 * Importer for Microsoft Word table question format.
 *
 * See {@link https://docs.moodle.org/en/Word_table_format} for a description of the format.
 *
 * @copyright 2010-2015 Eoin Campbell
 * @author Eoin Campbell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */
class qformat_wordtable extends qformat_xml {
    /** @var string export template with Word-compatible CSS style definitions */
    private $wordfiletemplate = 'wordfiletemplate.html';
    /** @var string Stylesheet to export Moodle Question XML into XHTML */
    private $mqxml2wordstylesheet1 = 'mqxml2wordpass1.xsl';
    /** @var string Stylesheet to export XHTML into Word-compatible XHTML */
    private $mqxml2wordstylesheet2 = 'mqxml2wordpass2.xsl';

    /** @var string Stylesheet to import XHTML into Word-compatible XHTML */
    private $word2mqxmlstylesheet1 = 'wordml2xhtmlpass1.xsl';
    /** @var string Stylesheet to process XHTML during import */
    private $word2mqxmlstylesheet2 = 'wordml2xhtmlpass2.xsl';
    /** @var string Stylesheet to import XHTML into question XML */
    private $word2mqxmlstylesheet3 = 'xhtml2mqxml.xsl';
    /** @var string Stylesheet to clean up text inside Cloze questions */

    /**
     * Define required MIME-Type
     *
     * @return string MIME-Type
     */
    public function mime_type() {
        return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    }

    // IMPORT FUNCTIONS START HERE.

    /**
     * Perform required pre-processing, i.e. convert Word file into XML
     *
     * Extract the WordProcessingML XML files from the .docx file, and use a sequence of XSLT
     * steps to convert it into Moodle Question XML
     *
     * @return bool Success
     */
    public function importpreprocess() {
        global $CFG, $USER, $COURSE, $OUTPUT;
        $realfilename = "";
        $filename = "";

        // Handle question imports in Lesson module by using mform, not the question/format.php qformat_default class.
        if (property_exists('qformat_default', 'realfilename')) {
            $realfilename = $this->realfilename;
        } else {
            global $mform;
            $realfilename = $mform->get_new_filename('questionfile');
        }
        if (property_exists('qformat_default', 'filename')) {
            $filename = $this->filename;
        } else {
            global $mform;
            $filename = "{$CFG->tempdir}/questionimport/{$realfilename}";
        }
        $basefilename = basename($filename);
        $baserealfilename = basename($realfilename);

        // Uncomment next line to give XSLT as much memory as possible, to enable larger Word files to be imported.
        // @codingStandardsIgnoreLine raise_memory_limit(MEMORY_HUGE);

        // Check that the file is in Word 2010 format, not HTML, XML, or Word 2003.
        if ((substr($realfilename, -3, 3) == 'doc')) {
            echo $OUTPUT->notification(get_string('docnotsupported', 'qformat_wordtable', $baserealfilename));
            return false;
        } else if ((substr($realfilename, -3, 3) == 'xml')) {
            echo $OUTPUT->notification(get_string('xmlnotsupported', 'qformat_wordtable', $baserealfilename));
            return false;
        } else if ((stripos($realfilename, 'htm'))) {
            echo $OUTPUT->notification(get_string('htmlnotsupported', 'qformat_wordtable', $baserealfilename));
            return false;
        } else if ((stripos(file_get_contents($filename, 0, null, 0, 100), 'html'))) {
            echo $OUTPUT->notification(get_string('htmldocnotsupported', 'qformat_wordtable', $baserealfilename));
            return false;
        }

        // Stylesheet to convert WordML into initial XHTML format.
        $stylesheet = __DIR__ . "/" . $this->word2mqxmlstylesheet1;

        // Check that XSLT is installed, and the XSLT stylesheet is present.
        if (!class_exists('XSLTProcessor') || !function_exists('xslt_create')) {
            throw new moodle_exception(get_string('xsltunavailable', 'qformat_wordtable'));
        } else if (!file_exists($stylesheet)) {
            // Stylesheet to transform WordML into XHTML doesn't exist.
            throw new moodle_exception(get_string('stylesheetunavailable', 'qformat_wordtable', $stylesheet));
        }

        // Set common parameters for all XSLT transformations. Note that the XSLT processor doesn't support $arguments.
        $parameters = array(
            'course_id' => $COURSE->id,
            'course_name' => $COURSE->fullname,
            'author_name' => $USER->firstname . ' ' . $USER->lastname,
            'moodle_country' => $USER->country,
            'moodle_language' => current_language(),
            'moodle_textdirection' => (right_to_left()) ? 'rtl' : 'ltr',
            'moodle_release' => $CFG->release,
            'moodle_url' => $CFG->wwwroot . "/",
            'moodle_username' => $USER->username,
            'pluginname' => 'qformat_wordtable',
            'heading1stylelevel' => '1', // Default HTML heading element level for 'Heading 1' Word style.
            'debug_flag' => DEBUG_WORDTABLE
            );

        // Pre-XSLT conversion preparation merge the document XML and image content from the .docx Word file.

        // Initialise an XML string to use as a wrapper around all the XML files.
        $xmldeclaration = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $wordmldata = $xmldeclaration . "\n<pass1Container>\n";
        $imagestring = "";

        // Open the Word 2010 Zip-formatted file and extract the WordProcessingML XML files.
        $zfh = zip_open($filename);
        if (is_resource($zfh)) {
            $zipentry = zip_read($zfh);
            while ($zipentry) {
                if (zip_entry_open($zfh, $zipentry, "r")) {
                    $zefilename = zip_entry_name($zipentry);
                    $zefilesize = zip_entry_filesize($zipentry);

                    // Look for internal images.
                    if (strpos($zefilename, "media")) {
                        $imagedata = zip_entry_read($zipentry, $zefilesize);
                        $imagename = basename($zefilename);
                        $imagesuffix = strtolower(substr(strrchr($zefilename, "."), 1));
                        // Suffixes gif, png, jpg and jpeg handled OK, but bmp and other non-Internet formats are not.
                        $imagemimetype = "image/";
                        if ($imagesuffix == 'gif' or $imagesuffix == 'png') {
                            $imagemimetype .= $imagesuffix;
                        }
                        if ($imagesuffix == 'jpg' or $imagesuffix == 'jpeg') {
                            $imagemimetype .= "jpeg";
                        }
                        if ($imagesuffix == 'wmf') {
                            $imagemimetype .= "x-wmf";
                        }
                        // Handle recognised Internet formats only.
                        if ($imagemimetype != '') {
                            $imagestring .= '<file filename="media/' . $imagename . '" mime-type="' . $imagemimetype . '">';
                            $imagestring .= base64_encode($imagedata) . "</file>\n";
                        }
                    } else {
                        // Look for required XML files.
                        // Read and wrap XML files, remove the XML declaration, and add them to the XML string.
                        $xmlfiledata = preg_replace('/<\?xml version="1.0" ([^>]*)>/', "", zip_entry_read($zipentry, $zefilesize));
                        switch ($zefilename) {
                            case "word/document.xml":
                                $wordmldata .= "<wordmlContainer>" . $xmlfiledata . "</wordmlContainer>\n";
                                break;
                            case "docProps/core.xml":
                                $wordmldata .= "<dublinCore>" . $xmlfiledata . "</dublinCore>\n";
                                break;
                            case "docProps/custom.xml":
                                $wordmldata .= "<customProps>" . $xmlfiledata . "</customProps>\n";
                                break;
                            case "word/styles.xml":
                                $wordmldata .= "<styleMap>" . $xmlfiledata . "</styleMap>\n";
                                break;
                            case "word/_rels/document.xml.rels":
                                $wordmldata .= "<documentLinks>" . $xmlfiledata . "</documentLinks>\n";
                                break;
                            case "word/footnotes.xml":
                                $wordmldata .= "<footnotesContainer>" . $xmlfiledata . "</footnotesContainer>\n";
                                break;
                            case "word/_rels/footnotes.xml.rels" . $xmlfiledata . "</footnoteLinks>\n";
                                break;
                        }
                    }
                } else { // Can't read the file from the Word .docx file.
                    zip_close($zfh);
                    throw new moodle_exception(get_string('cannotreadzippedfile', 'qformat_wordtable', $basefilename));
                }
                // Get the next file in the Zip package.
                $zipentry = zip_read($zfh);
            }  // End while loop.
            zip_close($zfh);
        } else { // Can't open the Word .docx file for reading.
            $this->debug_unlink($filename);
            throw new moodle_exception(get_string('cannotopentempfile', 'qformat_wordtable', $basefilename));
        }

        // Add Base64 images section and close the merged XML file.
        $wordmldata .= "<imagesContainer>\n" . $imagestring . "</imagesContainer>\n"  . "</pass1Container>";

        // Pass 1 - convert WordML into linear XHTML.
        // Create a temporary file to store the merged WordML XML content to transform.
        if (!($tempwordmlfilename = tempnam($CFG->tempdir, "w2x")) || (file_put_contents($tempwordmlfilename, $wordmldata)) == 0) {
            throw new moodle_exception(get_string('cannotwritetotempfile', 'qformat_wordtable', basename($tempwordmlfilename)));
        }

        $xsltproc = xslt_create();
        if (!($xsltoutput = xslt_process($xsltproc, $tempwordmlfilename, $stylesheet, null, null, $parameters))) {
            $this->debug_unlink($tempwordmlfilename);
            throw new moodle_exception(get_string('transformationfailed', 'qformat_wordtable', $stylesheet));
        }
        $this->debug_unlink($tempwordmlfilename);

        // Strip out superfluous namespace declarations on paragraph elements, which Moodle 2.7/2.8 on Windows seems to throw in.
        $xsltoutput = str_replace('<p xmlns="http://www.w3.org/1999/xhtml"', '<p', $xsltoutput);
        $xsltoutput = str_replace(' xmlns=""', '', $xsltoutput);

        // Write output of Pass 1 to a temporary file, for use in Pass 2.
        if (!($tempxhtmlfilename = tempnam($CFG->tempdir, "x2i")) || (file_put_contents($tempxhtmlfilename, $xsltoutput)) == 0) {
            throw new moodle_exception(get_string('cannotwritetotempfile', 'qformat_wordtable', basename($tempxhtmlfilename)));
        }

        // Pass 2 - tidy up linear XHTML a bit.
        // Prepare for Import Pass 2 XSLT transformation.
        $stylesheet = __DIR__ . "/" . $this->word2mqxmlstylesheet2;
        if (!($xsltoutput = xslt_process($xsltproc, $tempxhtmlfilename, $stylesheet, null, null, $parameters))) {
            $this->debug_unlink($tempxhtmlfilename);
            throw new moodle_exception(get_string('transformationfailed', 'qformat_wordtable', $stylesheet));
        }
        $this->debug_unlink($tempxhtmlfilename);

        // Write the Pass 2 XHTML output to a temporary file.
        $xhtmlfragment = "<pass3Container>\n" . $xsltoutput . $this->get_text_labels() . "\n</pass3Container>";
        if (!($tempxhtmlfilename = tempnam($CFG->tempdir, "i2q")) || (file_put_contents($tempxhtmlfilename, $xhtmlfragment)) == 0) {
            throw new moodle_exception(get_string('cannotwritetotempfile', 'qformat_wordtable', basename($tempxhtmlfilename)));
        }

        // Pass 3 - convert XHTML into Moodle Question XML.
        // Prepare for Import Pass 3 XSLT transformation.
        $stylesheet = __DIR__ . "/" . $this->word2mqxmlstylesheet3;
        if (!($mqxmldata = xslt_process($xsltproc, $tempxhtmlfilename, $stylesheet, null, null, $parameters))) {
            $this->debug_unlink($tempxhtmlfilename);
            throw new moodle_exception(get_string('transformationfailed', 'qformat_wordtable', $stylesheet));
        }
        $this->debug_unlink($tempxhtmlfilename);

        // Strip out most MathML element and attributes for compatibility with MathJax.
        $mqxmldata = str_replace('<mml:', '<', $mqxmldata);
        $mqxmldata = str_replace('</mml:', '</', $mqxmldata);
        $mqxmldata = str_replace(' mathvariant="normal"', '', $mqxmldata);
        $mqxmldata = str_replace(' xmlns:mml="http://www.w3.org/1998/Math/MathML"', '', $mqxmldata);
        $mmltextdirection = (right_to_left()) ? ' dir="rtl"' : '';
        $mqxmldata = str_replace('<math>', "<math xmlns=\"http://www.w3.org/1998/Math/MathML\" $mmltextdirection>", $mqxmldata);

        // Now over-write the original Word file with the XML file, so that default XML file handling will work.
        if (($fp = fopen($filename, "wb"))) {
            if (($nbytes = fwrite($fp, $mqxmldata)) == 0) {
                throw new moodle_exception(get_string('cannotwritetotempfile', 'qformat_wordtable', $basefilename));
            }
            fclose($fp);
        }

        return true;
    }   // End importpreprocess function.


    // EXPORT FUNCTIONS START HERE.

    /**
     * Use a .doc file extension when exporting, so that Word is used to open the file
     * @return string file extension
     */
    public function export_file_extension() {
        return ".doc";
    }


    /**
     * Convert the Moodle Question XML into Word-compatible XHTML format
     * just prior to the file being saved
     *
     * Use an XSLT script to do the job, as it is much easier to implement this,
     * and Moodle sites are guaranteed to have an XSLT processor available (I think).
     *
     * @param string $content Question XML text
     * @return string Word-compatible XHTML text
     */
    public function presave_process( $content ) {
        // Override method to allow us convert to Word-compatible XHTML format.
        global $CFG, $USER, $COURSE;
        global $OUTPUT;

        // Stylesheet to convert Moodle Question XML into Word-compatible XHTML format.
        $stylesheet = __DIR__ . "/" . $this->mqxml2wordstylesheet1;
        // XHTML template for Word file CSS styles formatting.
        $htmltemplatefilepath = __DIR__ . "/" . $this->wordfiletemplate;

        // Check that XSLT is installed, and the XSLT stylesheet and XHTML template are present.
        if (!class_exists('XSLTProcessor') || !function_exists('xslt_create')) {
            throw new moodle_exception(get_string('xsltunavailable', 'qformat_wordtable'));
        } else if (!file_exists($stylesheet)) {
            // Stylesheet to transform Moodle Question XML into Word doesn't exist.
            throw new moodle_exception(get_string('stylesheetunavailable', 'qformat_wordtable', $stylesheet));
        }

        // Check that there is some content to convert into Word.
        if (!strlen($content)) {
            echo $OUTPUT->notification(get_string('noquestions', 'qformat_wordtable'));
        }

        // Create a temporary file to store the XML content to transform.
        if (!($tempxmlfilename = tempnam($CFG->tempdir, "q2x"))) {
            throw new moodle_exception(get_string('cannotopentempfile', 'qformat_wordtable', basename($tempxmlfilename)));
        }

        // Maximise memory available so that very large question banks can be exported.
        raise_memory_limit(MEMORY_HUGE);

        $cleancontent = $this->clean_all_questions($content);

        // Write the XML contents to be transformed, and also include labels data, to avoid having to use document() inside XSLT.
        $xmloutput = "<container>\n<quiz>" . $cleancontent . "</quiz>\n" . $this->get_text_labels() . "\n</container>";
        if (($nbytes = file_put_contents($tempxmlfilename, $xmloutput)) == 0) {
            throw new moodle_exception(get_string('cannotwritetotempfile', 'qformat_wordtable', basename($tempxmlfilename)));
        }

        // Set parameters for XSLT transformation. Note that we cannot use $arguments though.
        $parameters = array (
            'course_id' => $COURSE->id,
            'course_name' => $COURSE->fullname,
            'author_name' => $USER->firstname . ' ' . $USER->lastname,
            'moodle_country' => $USER->country,
            'moodle_language' => current_language(),
            'moodle_textdirection' => (right_to_left()) ? 'rtl' : 'ltr',
            'moodle_release' => $CFG->release,
            'moodle_url' => $CFG->wwwroot . "/",
            'moodle_username' => $USER->username,
            'debug_flag' => debugging('', DEBUG_WORDTABLE),
            'transformationfailed' => get_string('transformationfailed', 'qformat_wordtable', $this->mqxml2wordstylesheet2)
        );

        $xsltproc = xslt_create();
        if (!($xsltoutput = xslt_process($xsltproc, $tempxmlfilename, $stylesheet, null, null, $parameters))) {
            $this->debug_unlink($tempxmlfilename);
            throw new moodle_exception(get_string('transformationfailed', 'qformat_wordtable', $stylesheet));
        }
        $this->debug_unlink($tempxmlfilename);

        $tempxhtmlfilename = tempnam($CFG->tempdir, "x2w");
        // Write the intermediate (Pass 1) XHTML contents to be transformed in Pass 2, this time including the HTML template too.
        $xmloutput = "<container>\n" . $xsltoutput . "\n<htmltemplate>\n" . file_get_contents($htmltemplatefilepath) .
                     "\n</htmltemplate>\n" . $this->get_text_labels() . "\n</container>";
        if (($nbytes = file_put_contents($tempxhtmlfilename, $xmloutput)) == 0) {
            throw new moodle_exception(get_string('cannotwritetotempfile', 'qformat_wordtable', basename($tempxhtmlfilename)));
        }

        // Prepare for Pass 2 XSLT transformation.
        $stylesheet = __DIR__ . "/" . $this->mqxml2wordstylesheet2;
        if (!($xsltoutput = xslt_process($xsltproc, $tempxhtmlfilename, $stylesheet, null, null, $parameters))) {
            $this->debug_unlink($tempxhtmlfilename);
            throw new moodle_exception(get_string('transformationfailed', 'qformat_wordtable', $stylesheet));
        }
        $this->debug_unlink($tempxhtmlfilename);

        // Strip out any redundant namespace attributes, which XSLT on Windows seems to add.
        $xsltoutput = str_replace(' xmlns=""', '', $xsltoutput);
        $xsltoutput = str_replace(' xmlns="http://www.w3.org/1999/xhtml"', '', $xsltoutput);
        // Unescape double minuses if they were substituted during CDATA content clean-up.
        $xsltoutput = str_replace("WordTableMinusMinus", "--", $xsltoutput);

        // Strip off the XML declaration, if present, since Word doesn't like it.
        if (strncasecmp($xsltoutput, "<?xml ", 5) == 0) {
            $content = substr($xsltoutput, strpos($xsltoutput, "\n"));
        } else {
            $content = $xsltoutput;
        }

        return $content;
    }   // End presave_process function.

    /**
     * Delete temporary files if debugging disabled
     *
     * @param string $filename Filename to delete
     * @return void
     */
    private function debug_unlink($filename) {
        if (!debugging(null, DEBUG_WORDTABLE)) {
            unlink($filename);
        }
    }

    /**
     * Get all the text strings needed to fill in the Word file labels in a language-dependent way
     *
     * A string containing XML data, populated from the language folders, is returned
     *
     * @return string
     */
    private function get_text_labels() {
        global $CFG;

        // Release-independent list of all strings required in the XSLT stylesheets for labels etc.
        $textstrings = array(
            'grades' => array('item'),
            'moodle' => array('categoryname', 'no', 'yes', 'feedback', 'format', 'formathtml', 'formatmarkdown',
                            'formatplain', 'formattext', 'grade', 'question', 'tags'),
            'qformat_wordtable' => array('cloze_instructions', 'cloze_distractor_column_label', 'cloze_feedback_column_label',
                            'cloze_mcformat_label', 'description_instructions', 'essay_instructions',
                            'interface_language_mismatch', 'multichoice_instructions', 'truefalse_instructions',
                            'transformationfailed', 'unsupported_instructions'),
            'qtype_description' => array('pluginnamesummary'),
            'qtype_essay' => array('allowattachments', 'graderinfo', 'formateditor', 'formateditorfilepicker',
                            'formatmonospaced', 'formatplain', 'pluginnamesummary', 'responsefieldlines', 'responseformat'),
            'qtype_match' => array('filloutthreeqsandtwoas'),
            'qtype_multichoice' => array('answernumbering', 'choiceno', 'correctfeedback', 'incorrectfeedback',
                            'partiallycorrectfeedback', 'pluginnamesummary', 'shuffleanswers'),
            'qtype_shortanswer' => array('casesensitive', 'filloutoneanswer'),
            'qtype_truefalse' => array('false', 'true'),
            'question' => array('category', 'clearwrongparts', 'defaultmark', 'generalfeedback', 'hintn',
                            'penaltyforeachincorrecttry', 'questioncategory', 'shownumpartscorrect',
                            'shownumpartscorrectwhenfinished'),
            'quiz' => array('answer', 'answers', 'casesensitive', 'correct', 'correctanswers',
                            'defaultgrade', 'incorrect', 'shuffle')
            );

        // Append Moodle release-specific text strings, to avoid PHP errors when absent strings are requested.
        if ($CFG->release < '2.0') {
            $textstrings['quiz'][] = 'choice';
            $textstrings['quiz'][] = 'penaltyfactor';
        } else if ($CFG->release >= '2.5') {
            // Add support for new Essay fields added in Moodle 2.5.
            $textstrings['qtype_essay'][] = 'responsetemplate';
            $textstrings['qtype_essay'][] = 'responsetemplate_help';
            $textstrings['qtype_match'][] = 'blanksforxmorequestions';
            // Add support for new generic question fields added in Moodle 2.5.
            $textstrings['question'][] = 'addmorechoiceblanks';
            $textstrings['question'][] = 'correctfeedbackdefault';
            $textstrings['question'][] = 'hintnoptions';
            $textstrings['question'][] = 'incorrectfeedbackdefault';
            $textstrings['question'][] = 'partiallycorrectfeedbackdefault';
        }
        if ($CFG->release >= '2.7') {
            // Add support for new Essay fields added in Moodle 2.7.
            $textstrings['qtype_essay'][] = 'attachmentsrequired';
            $textstrings['qtype_essay'][] = 'responserequired';
            $textstrings['qtype_essay'][] = 'responseisrequired';
            $textstrings['qtype_essay'][] = 'responsenotrequired';
            $textstrings['qtype_essay'][] = 'formatnoinline';

        }
        if ($CFG->release >= '3.5') {
            // Add support for new Essay accepted file type added in Moodle 3.5.
            $textstrings['qtype_essay'][] = 'acceptedfiletypes';
        }
        if ($CFG->release >= '3.6') {
            // Add support for new optional ID number field added in Moodle 3.6.
            $textstrings['question'][] = 'idnumber';
        }

        // Add All-or-Nothing MCQ question type strings if present.
        if (is_object(question_bank::get_qtype('multichoiceset', false))) {
            $textstrings['qtype_multichoiceset'] = array('pluginnamesummary', 'showeachanswerfeedback');
        }
        // Add 'Select missing word' question type (not the Missing Word format), added to core in 2.9, downloadable before then.
        if (is_object(question_bank::get_qtype('gapselect', false))) {
            $textstrings['qtype_gapselect'] = array('pluginnamesummary', 'errornoslots', 'group', 'shuffle');
        }
        // Add 'Drag and drop onto image' question type, added to core in 2.9, downloadable before then.
        if (is_object(question_bank::get_qtype('ddimageortext', false))) {
            $textstrings['qtype_ddimageortext'] = array('pluginnamesummary', 'bgimage', 'dropbackground', 'dropzoneheader',
                    'draggableitem', 'infinite', 'label', 'shuffleimages', 'xleft', 'ytop');
        }
        // Add 'Drag and drop markers' question type, added to core in 2.9, downloadable before then.
        if (is_object(question_bank::get_qtype('ddmarker', false))) {
            $textstrings['qtype_ddmarker'] = array('pluginnamesummary', 'bgimage', 'clearwrongparts', 'coords',
                'dropbackground', 'dropzoneheader', 'infinite', 'marker', 'noofdrags', 'shape_circle',
                'shape_polygon', 'shape_rectangle', 'shape', 'showmisplaced', 'stateincorrectlyplaced');
        }
        // Add 'Drag and drop into text' question type, added to core in 2.9, downloadable before then.
        if (is_object(question_bank::get_qtype('ddwtos', false))) {
            $textstrings['qtype_ddwtos'] = array('pluginnamesummary', 'infinite');
        }

        $expout = "<moodlelabels>\n";
        foreach ($textstrings as $typegroup => $grouparray) {
            foreach ($grouparray as $stringid) {
                $namestring = $typegroup . '_' . $stringid;
                // Clean up question type explanation, in case the default text has been overridden on the site.
                if ($stringid == 'pluginnamesummary') {
                    $cleantext = $this->convert_to_xml(get_string($stringid, $typegroup));
                } else {
                    $cleantext = get_string($stringid, $typegroup);
                }
                $expout .= '<data name="' . $namestring . '"><value>' . $cleantext . "</value></data>\n";
            }
        }
        $expout .= "</moodlelabels>";
        $expout = str_replace("<br>", "<br/>", $expout);

        return $expout;
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

        // Split the string into questions in order to check the text fields for clean HTML.
        $foundquestions = preg_match_all('~(.*?)<question type="([^"]*)"[^>]*>(.*?)</question>~s', $questionxmlstring,
                            $questionmatches, PREG_SET_ORDER);
        $numquestions = count($questionmatches);
        if ($foundquestions === false or $foundquestions == 0) {
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
                    $cleancdatacontent = $this->clean_html_text($cdatamatches[$j][2]);

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

    /**
     * Clean HTML content
     *
     * A string containing clean XHTML is returned
     *
     * @param string $cdatastring XHTML from inside a CDATA_SECTION in a question text element
     * @return string
     */
    private function clean_html_text($cdatastring) {
        // Escape double minuses, which cause XSLT processing to fail.
        $cleanxhtml = $this->convert_to_xml($cdatastring);

        // Fix up filenames after @@PLUGINFILE@@ to replace URL-encoded characters with ordinary characters.
        $foundpluginfilenames = preg_match_all('~(.*?)<img src="@@PLUGINFILE@@/([^"]*)(.*)~s', $cleanxhtml,
                                    $pluginfilematches, PREG_SET_ORDER);
        $nummatches = count($pluginfilematches);
        if ($foundpluginfilenames and $foundpluginfilenames != 0) {
            $urldecodedstring = "";
            // Process the possibly-URL-escaped filename so that it matches the name in the file element.
            for ($i = 0; $i < $nummatches; $i++) {
                // Decode the filename and add the surrounding text.
                $decodedfilename = urldecode($pluginfilematches[$i][2]);
                $urldecodedstring .= $pluginfilematches[$i][1] . '<img src="@@PLUGINFILE@@/' . $decodedfilename .
                                        $pluginfilematches[$i][3];
            }
            $cleanxhtml = $urldecodedstring;
        }

        // Strip soft hyphens (0xAD, or decimal 173).
        $cleanxhtml = preg_replace('/\xad/u', '', $cleanxhtml);
        return $cleanxhtml;
    }

    /**
     * Convert content into well-formed XML
     *
     * A string containing clean XHTML is returned
     *
     * @param string $cdatastring XHTML from questions or help text
     * @return string well-formed XML
     */
    private function convert_to_xml($cdatastring) {

        // Escape double minuses, which cause XSLT processing to fail.
        $cdatastring = str_replace("--", "WordTableMinusMinus", $cdatastring);

        // Wrap the string in a HTML wrapper, load it into a new DOM document as HTML, but save as XML.
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><html><body>' . $cdatastring . '</body></html>');
        $doc->getElementsByTagName('html')->item(0)->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $xml = $doc->saveXML();

        $bodystart = stripos($xml, '<body>') + strlen('<body>');
        $bodylength = strripos($xml, '</body>') - $bodystart;

        if ($bodystart || $bodylength) {
            $cleanxhtml = substr($xml, $bodystart, $bodylength);
        } else {
            $cleanxhtml = $cdatastring;
        }

        return $cleanxhtml;
    }
}
