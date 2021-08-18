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
 * Import/Export Microsoft Word files library.
 *
 * @package    booktool_wordimport
 * @copyright  2020 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace booktool_wordimport;

defined('MOODLE_INTERNAL') || die;
define('DEBUG_WORDIMPORT', DEBUG_DEVELOPER);

require_once(__DIR__.'/xslemulatexslt.php');

/**
 * Import HTML pages from a Word file
 *
 * @param string $wordfilename Word file
 * @param stdClass $book
 * @param context_module $context
 * @param bool $splitonsubheadings
 * @return void
 */
class wordconverter {

    /*
     * @var string Stylesheet to convert WordML to XHTML
    */
    private $word2xmlstylesheet1 = __DIR__ . "/wordml2xhtmlpass1.xsl"; // Convert WordML into basic XHTML.
    /*
     * @var string Stylesheet to clean XHTML up and insert images as Base64-encoded data.
    */
    private $word2xmlstylesheet2 = __DIR__ . "/wordml2xhtmlpass2.xsl"; // Refine basic XHTML into Word-compatible XHTML.
    /*
     * @var string XHTML template for exporting content, with Word-compatible CSS style definitions.
    */
    private $wordfiletemplate = __DIR__ . '/wordfiletemplate.html';
    /*
     * @var string Stylesheet to export generic XHTML into Word-compatible XHTML.
    */
    private $exportstylesheet = __DIR__ . "/xhtml2wordpass2.xsl";
    /*
     * @var string How should images be handled: embedded as Base64-encoded data, or referenced (default).
    */
    private $imagehandling = 'referenced';
    /*
     * @var int Word heading style level to HTML element mapping, default "Heading 1" = <h3>
    */
    private $heading1styleoffset = 3;

    /**
     * Process XML using XSLT script
     *
     * @param string $xmldata XML-formatted content
     * @param string $xslfile Full path to XSLT script file
     * @param array $parameters array of parameters to pass into script
     * @return string Processed XML content
     */
    public function convert(string $xmldata, string $xslfile, array $parameters) {
        global $CFG;

        // Check that XSLT is installed.
        if (!class_exists('XSLTProcessor') || !function_exists('xslt_create')) {
            // PHP 'xsl' extension library is required for this action.
            throw new \moodle_exception(get_string('extensionrequired', 'tool_xmldb', 'xsl'));
        }

        // Check that the XSLT stylesheet exists.
        if (!file_exists($xslfile)) {
            throw new \moodle_exception(get_string('stylesheetunavailable', 'booktool_wordimport', $xslfile));
        }

        // Get a temporary file and store the XML content to transform.
        if (!($tempxmlfilename = tempnam($CFG->tempdir, "wcx")) || (file_put_contents($tempxmlfilename, $xmldata)) == 0) {
            throw new \moodle_exception(get_string('cannotopentempfile', 'booktool_wordimport', $tempxmlfilename));
        }

        // Run the XSLT script using the PHP xsl library.
        $xsltproc = xslt_create();
        if (!($xsltoutput = xslt_process($xsltproc, $tempxmlfilename, $xslfile, null, null, $parameters))) {
            // Transformation failed.
            $this->debug_unlink($tempxmlfilename);
            throw new \moodle_exception('transformationfailed', 'booktool_wordimport', $tempxmlfilename);
        }
        $this->debug_unlink($tempxmlfilename);

        $xsltoutput = $this->clean_namespaces($xsltoutput);
        return $xsltoutput;
    }

    /**
     * Extract the WordProcessingML XML files from the .docx file, and use a sequence of XSLT
     * steps to convert it into an XHTML file
     *
     * @param string $filename Word file
     * @param array $imagesforzipping array to store embedded image files
     * @return string XHTML content extracted from Word file and split into files
     */
    public function import(string $filename, array &$imagesforzipping) {
        global $CFG;

        // Check that we can unzip the Word .docx file into its component files.
        $zipres = zip_open($filename);
        if (!is_resource($zipres)) {
            // Cannot unzip file.
            $this->debug_unlink($filename);
            throw new \moodle_exception('cannotunzipfile', 'error');
        }

        // Uncomment next line to give XSLT as much memory as possible, to enable larger Word files to be imported.
        // @codingStandardsIgnoreLine raise_memory_limit(MEMORY_HUGE);

        if (!file_exists($this->word2xmlstylesheet1)) {
            // XSLT stylesheet to transform WordML into XHTML is missing.
            throw new \moodle_exception('filemissing', 'moodle', $CFG->wwwroot, $this->word2xmlstylesheet1);
        }

        // Pre-XSLT preparation: merge the WordML and image content from the .docx Word file into one large XML file.
        // Initialise an XML string to use as a wrapper around all the XML files.
        $xmldeclaration = '<?xml version="1.0" encoding="UTF-8"?>';
        $wordmldata = $xmldeclaration . "\n<pass1Container>\n";

        $zipentry = zip_read($zipres);
        while ($zipentry) {
            if (!zip_entry_open($zipres, $zipentry, "r")) {
                // Can't read the XML file from the Word .docx file.
                zip_close($zipres);
                throw new \moodle_exception('errorunzippingfiles', 'error');
            }

            $zefilename = zip_entry_name($zipentry);
            $zefilesize = zip_entry_filesize($zipentry);

            // Insert internal images into the array of images.
            if (strpos($zefilename, "media")) {
                $imagedata = zip_entry_read($zipentry, $zefilesize);
                $imagename = basename($zefilename);
                $imagesuffix = strtolower(pathinfo($zefilename, PATHINFO_EXTENSION));
                // Internet formats like GIF, PNG and JPEG are supported, but not non-Internet formats like BMP or EPS.
                if ($imagesuffix == 'gif' or $imagesuffix == 'png' or $imagesuffix == 'jpg' or $imagesuffix == 'jpeg') {
                    $imagesforzipping[$imagename] = $imagedata;
                }
            } else {
                // Look for required XML files, read and wrap it, remove the XML declaration, and add it to the XML string.
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
                    case "word/_rels/footnotes.xml.rels":
                        $wordmldata .= "<footnoteLinks>" . $xmlfiledata . "</footnoteLinks>\n";
                        break;
                    // @codingStandardsIgnoreLine case "word/_rels/settings.xml.rels":
                        // @codingStandardsIgnoreLine $wordmldata .= "<settingsLinks>" . $xmlfiledata . "</settingsLinks>\n";
                        // @codingStandardsIgnoreLine break;
                    default:
                        // @codingStandardsIgnoreLine debugging(__FUNCTION__ . ":" . __LINE__ . ": Ignore $zefilename", DEBUG_WORDIMPORT);
                }
            }
            // Get the next file in the Zip package.
            $zipentry = zip_read($zipres);
        }  // End while loop.
        zip_close($zipres);

        // Close the merged XML file.
        $wordmldata .= "</pass1Container>";

        // Set common parameters for both XSLT transformations.
        $parameters = array (
            'moodle_language' => current_language(),
            'moodle_textdirection' => (right_to_left()) ? 'rtl' : 'ltr',
            'heading1stylelevel' => $this->heading1styleoffset,
            'imagehandling' => $this->imagehandling, // Are images embedded or referenced.
            'debug_flag' => '1'
        );

        // Pass 1 - convert WordML into linear XHTML.
        $xsltoutput = $this->convert($wordmldata, $this->word2xmlstylesheet1, $parameters);

        // Pass 2 - tidy up linear XHTML.
        $xsltoutput = $this->convert($xsltoutput, $this->word2xmlstylesheet2, $parameters);
        // Remove 'mml:' prefix from child MathML element and attributes for compatibility with MathJax.
        $xsltoutput = $this->clean_mathml_namespaces($xsltoutput);

        // Keep the converted XHTML file for debugging if developer debugging enabled.
        if (DEBUG_WORDIMPORT == DEBUG_DEVELOPER and debugging(null, DEBUG_DEVELOPER)) {
            $tempxhtmlfilename = $CFG->tempdir . DIRECTORY_SEPARATOR . basename($filename, ".tmp") . ".xhtml";
            file_put_contents($tempxhtmlfilename, $xsltoutput);
        }

        return $xsltoutput;
    }   // End import function.

    /**
     * Export generic XHTML into Word-compatible XHTML format
     *
     * Use an XSLT script to do the job, as it is much easier to implement this,
     * and Moodle sites are guaranteed to have an XSLT processor available (I think).
     *
     * @param string $xhtmldata XHTML content from a book, book chapter, question bank category, glossary, etc.
     * @param string $module Where it is called from: book, glossary, lesson or question
     * @return string Word-compatible XHTML text
     */
    public function export(string $xhtmldata, string $module = 'book') {
        global $CFG, $USER, $COURSE, $OUTPUT;

        // Check the HTML template exists.
        if (!file_exists($this->wordfiletemplate)) {
            echo $OUTPUT->notification(get_string('stylesheetunavailable', 'booktool_wordimport', $this->wordfiletemplate));
            return false;
        }

        // Uncomment next line to give XSLT as much memory as possible, to enable larger Word files to be exported.
        // @codingStandardsIgnoreLine raise_memory_limit(MEMORY_HUGE);

        // Clean up the content to ensure it is well-formed XML and won't break the XSLT processing.
        $cleancontent = $this->clean_html_text($xhtmldata);

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
            'moodle_module' => $module,
            'debug_flag' => debugging('', DEBUG_WORDIMPORT),
            'transformationfailed' => get_string('transformationfailed', 'booktool_wordimport', $this->exportstylesheet)
        );

        // Assemble the book contents and the HTML template to a single XML file for easier XSLT processing.
        $xhtmloutput = "<container>\n<container><html xmlns='http://www.w3.org/1999/xhtml'><body>" .
                $cleancontent . "</body></html></container>\n<htmltemplate>\n" .
                file_get_contents($this->wordfiletemplate) . "\n</htmltemplate>\n</container>";

        // Do Pass 2 XSLT transformation (Pass 1 must be done in separate convert() call if necessary).
        $xsltoutput = $this->convert($xhtmloutput, $this->exportstylesheet, $parameters);
        $xsltoutput = $this->clean_comments($xsltoutput);
        $xsltoutput = $this->clean_xmldecl($xsltoutput);

        return $xsltoutput;
    }   // End export function.

    /**
     * Get images and write them as base64 inside the HTML content
     *
     * A string containing the HTML with embedded base64 images is returned
     *
     * @param string $contextid the context ID
     * @param string $filearea filearea: chapter or intro
     * @param string $chapterid the chapter ID (optional)
     * @return string the modified HTML with embedded images
     */
    public function base64_images($contextid, $filearea, $chapterid = null) {
        // Get the list of files embedded in the book or chapter.
        // Note that this will break on images in the Book Intro section.
        $imagestring = '';
        $fs = get_file_storage();
        if ($filearea == 'intro') {
            $files = $fs->get_area_files($contextid, 'mod_book', $filearea);
        } else {
            $files = $fs->get_area_files($contextid, 'mod_book', $filearea, $chapterid);
        }
        foreach ($files as $fileinfo) {
            // Process image files, converting them into Base64 encoding.
            debugging(__FUNCTION__ . ": $filearea file: " . $fileinfo->get_filename(), DEBUG_WORDIMPORT);
            $fileext = strtolower(pathinfo($fileinfo->get_filename(), PATHINFO_EXTENSION));
            if ($fileext == 'png' or $fileext == 'jpg' or $fileext == 'jpeg' or $fileext == 'gif') {
                $filename = $fileinfo->get_filename();
                $filetype = ($fileext == 'jpg') ? 'jpeg' : $fileext;
                $fileitemid = $fileinfo->get_itemid();
                $filepath = $fileinfo->get_filepath();
                $filedata = $fs->get_file($contextid, 'mod_book', $filearea, $fileitemid, $filepath, $filename);

                if (!$filedata === false) {
                    $base64data = base64_encode($filedata->get_content());
                    $filedata = 'data:image/' . $filetype . ';base64,' . $base64data;
                    // Embed the image name and data into the HTML.
                    $imagestring .= '<img title="' . $filename . '" src="' . $filedata . '"/>';
                }
            }
        }

        if ($imagestring != '') {
            return '<div class="ImageFile">' . $imagestring . '</div>';
        }
        return '';
    }

    /**
     * Replace escaped comment placeholders with original double-minus token
     *
     * @param string $xhtmldata data processed by XSLT
     * @return string cleaned XHTML content
     */
    public function clean_comments(string $xhtmldata) {
        // Unescape double minuses if they were substituted during CDATA content clean-up.
        $cleanxhtml = str_replace("WORDIMPORTMinusMinus", "--", $xhtmldata);
        return $cleanxhtml;
    }

    /**
     * Strip out any superfluous default namespaces inserted by XSLT processing
     *
     * @param string $xhtmldata data processed by XSLT
     * @return string cleaned XHTML content
     */
    public function clean_xmldecl(string $xhtmldata) {
        // Strip off the XML declaration, if present, since Word doesn't like it.
        if (strncasecmp($xhtmldata, "<?xml ", 5) == 0) {
            $cleanxhtml = substr($xhtmldata, strpos($xhtmldata, "\n"));
        } else {
            $cleanxhtml = $xhtmldata;
        }
        return $cleanxhtml;
    }

    /**
     * Get the HTML body from a converted Word file
     *
     * @param string $xhtmldata complete XHTML text including head element metadata
     * @return string XHTML text inside <body> element
     */
    public function body_only($xhtmldata) {
        $matches = null;
        if (preg_match('/<body[^>]*>(.+)<\/body>/is', $xhtmldata, $matches)) {
            return $matches[1];
        } else {
            return $xhtmldata;
        }
    }

    /**
     * Set the type of image handling to do
     *
     * @param string $imagehandling Embedded or Referenced images
     * @return void
     */
    public function set_imagehandling(string $imagehandling) {
        $this->imagehandling = $imagehandling;
    }

    /**
     * Set the mapping between Word Heading style level, and HTML heading element
     *
     * @param int $headinglevel Heading style level (e.g. 1, 2 or 3)
     * @return void
     */
    public function set_heading1styleoffset(int $headinglevel) {
        $this->heading1styleoffset = $headinglevel;
    }

    /**
     * Get text strings needed for messages and labels in a language-dependent way
     *
     * A string containing XML data, populated from the language folders, is returned
     *
     * @return string
     */
    private function get_text_labels() {

        // Release-independent list of all strings required in the XSLT stylesheets for labels etc.
        $textstrings = array(
            'booktool_wordimport' => array('transformationfailed')
            );

        $expout = "<moodlelabels>\n";
        foreach ($textstrings as $typegroup => $grouparray) {
            foreach ($grouparray as $stringid) {
                $namestring = $typegroup . '_' . $stringid;
                // Clean up question type explanation, in case the default text has been overridden on the site.
                $cleantext = get_string($stringid, $typegroup);
                $expout .= '<data name="' . $namestring . '"><value>' . $cleantext . "</value></data>\n";
            }
        }
        $expout .= "</moodlelabels>";
        $expout = str_replace("<br>", "<br/>", $expout);

        return $expout;
    }


    /**
     * Clean HTML content
     *
     * A string containing clean XHTML is returned
     *
     * @param string $cdatastring XHTML from inside a CDATA_SECTION in a question text element
     * @return string
     */
    private function clean_html_text(string $cdatastring) {

        // Escape double minuses, which cause XSLT processing to fail.
        $cdatastring = str_replace("--", "WORDIMPORTMinusMinus", $cdatastring);

        // Wrap the string in a HTML wrapper, load it into a new DOM document as HTML, but save as XML.
        $doc = new \DOMDocument();
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
     * Strip out any superfluous default namespaces inserted by XSLT processing
     *
     * @param string $xhtmldata data processed by XSLT
     * @return string cleaned XHTML content
     */
    private function clean_namespaces(string $xhtmldata) {
        $cleanxhtml = str_replace('<p xmlns="http://www.w3.org/1999/xhtml"', '<p', $xhtmldata);
        $cleanxhtml = str_replace('<span xmlns="http://www.w3.org/1999/xhtml"', '<span', $cleanxhtml);
        $cleanxhtml = str_replace(' xmlns=""', '', $cleanxhtml);
        return $cleanxhtml;
    }

    /**
     * Clean MathML markup to suit MathJax presentation on Moodle
     *
     * @param string $xhtmldata data processed by XSLT
     * @return string cleaned MathML content
     */
    private function clean_mathml_namespaces(string $xhtmldata) {
        $cleanmml = str_replace('<mml:', '<', $xhtmldata);
        $cleanmml = str_replace('</mml:', '</', $cleanmml);
        $cleanmml = str_replace(' mathvariant="normal"', '', $cleanmml);
        $cleanmml = str_replace(' xmlns:mml="http://www.w3.org/1998/Math/MathML"', '', $cleanmml);
        $cleanmml = str_replace('<math>', '<math xmlns="http://www.w3.org/1998/Math/MathML">', $cleanmml);
        return $cleanmml;
    }

    /**
     * Delete temporary files if debugging disabled
     *
     * @param string $filename name of file to be deleted
     * @return void
     */
    private function debug_unlink($filename) {
        if (DEBUG_WORDIMPORT !== DEBUG_DEVELOPER or !(debugging(null, DEBUG_DEVELOPER))) {
            unlink($filename);
        }
    }

}
