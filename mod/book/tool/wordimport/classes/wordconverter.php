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

defined('MOODLE_INTERNAL') || die();

use moodle_exception;
use ZipArchive;
require_once(__DIR__.'/xslemulatexslt.php');
require_once($CFG->dirroot.'/mod/book/tool/importhtml/locallib.php');

/**
 * Import HTML pages from a Word file
 *
 * To import Word files, process a .docx Zip file, extracting the text and images, and convert it into XHTML.
 * To export HTML pages to Word, wrap them in a standard template, and apply Words pre-defined class names to headings,
 * paragraphs, lists, etc. For example, <h1> becomes <h1 class="MsoHeading1"> and <p> becomes <p class="MsoBodyText">.
 */
class wordconverter {
    /** @var string Stylesheet to convert WordML to XHTML */
    private $word2xmlstylesheet1 = __DIR__ . "/wordml2xhtmlpass1.xsl"; // Convert WordML into basic XHTML.

    /** @var string Stylesheet to clean XHTML up and insert images as Base64-encoded data. */
    private $word2xmlstylesheet2 = __DIR__ . "/wordml2xhtmlpass2.xsl"; // Refine basic XHTML into Word-compatible XHTML.

    /** @var string XHTML template for exporting content, with Word-compatible CSS style definitions. */
    private $wordfiletemplate = __DIR__ . '/wordfiletemplate.html';

    /** @var string Stylesheet to export generic XHTML into Word-compatible XHTML. */
    private $exportstylesheet = __DIR__ . "/xhtml2wordpass2.xsl";

    /**
     * Class constructor
     *
     * @param string $plugin Name of plugin module
     */
    public function __construct(string $plugin = 'booktool_wordimport') {
        global $CFG, $USER, $COURSE;

        // Set common parameters for all XSLT transformations. Note that the XSLT processor doesn't support $arguments.
        $this->xsltparameters = array(
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
            'imagehandling' => 'referenced', // Atto, Books and Lessons are referenced, Glossaries and Question are embedded.
            'heading1stylelevel' => 3, // Atto, Books and Lessons are 3, Glossaries and Question banks should be overridden to 1.
            'pluginname' => $plugin,
            'debug_flag' => (debugging(null, DEBUG_DEVELOPER)) ? '1' : '0'
            );
    }

    /**
     * Transform XML using XSLT script
     *
     * @param string $xmldata XML-formatted content
     * @param string $xslfile Full path to XSLT script file
     * @param array $parameters Extra XSLT parameters, if any
     * @return string Processed XML content
     */
    public function xsltransform(string $xmldata, string $xslfile, array $parameters = array()) {
        global $CFG;

        // Set common parameters for all XSLT transformations. Note that the XSLT processor doesn't support $arguments.
        $this->xsltparameters = array_merge($this->xsltparameters, $parameters);

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
        if (!($xsltoutput = xslt_process($xsltproc, $tempxmlfilename, $xslfile, null, null, $this->xsltparameters))) {
            // Transformation failed.
            unlink($tempxmlfilename);
            throw new \moodle_exception('transformationfailed', 'booktool_wordimport', $tempxmlfilename);
        }
        unlink($tempxmlfilename);

        // Clean namespaces.
        $xsltoutput = $this->clean_namespaces($xsltoutput);
        $xsltoutput = $this->clean_mathml_namespaces($xsltoutput);
        return $xsltoutput;
    }

    /**
     * Extract the WordProcessingML XML files from the .docx file, and use a sequence of XSLT
     * steps to convert it into an XHTML file
     *
     * @param string $filename Word file
     * @param array $imagesforzipping array to store embedded image files
     * @param bool $convertgifs Convert GIF images to PNG.
     * @return string XHTML content extracted from Word file and split into files
     */
    public function import(string $filename, array &$imagesforzipping, bool $convertgifs = false) {
        global $CFG;

        // Check that we can unzip the Word .docx file into its component files.
        $zipres = zip_open($filename);
        if (!is_resource($zipres)) {
            // Cannot unzip file.
            unlink($filename);
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
        $xmldeclaration = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $wordmldata = $xmldeclaration . "\n<pass1Container>\n";
        $imagestring = "";
        $gifimagefilenames = array();
        $pngimagefilenames = array();

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
            if (!(strpos($zefilename, "media") === false)) {
                $imagedata = zip_entry_read($zipentry, $zefilesize);
                $imagename = basename($zefilename);
                $imagesuffix = strtolower(pathinfo($zefilename, PATHINFO_EXTENSION));
                if ($imagesuffix == 'jpg') {
                    $imagesuffix = "jpeg";
                } else if ($imagesuffix == "gif" && $convertgifs) {
                    // For Glossaries and Questions, convert GIF images to PNG on import, not on export.
                    // This is because it is too hard to identify them in PHP when exporting.
                    list ($pngfilename, $imagedata) = $this->giftopng($imagedata, $imagename);
                    // Map old GIF image name to a new PNG name, derived from the temporary PNG file name.
                    $pngimagefilenames[] = basename($imagename, '.gif') . $pngfilename;
                    $gifimagefilenames[] = $imagename;
                }

                // Internet formats like GIF, PNG and JPEG are supported, but not non-Internet formats like BMP or EPS.
                if ($imagesuffix == 'gif' or $imagesuffix == 'png' or $imagesuffix == 'jpeg') {
                    $imagesforzipping[$imagename] = $imagedata;
                    $imagemimetype = "image/" . $imagesuffix;
                    $imagestring .= '<file filename="media/' . $imagename . '" mime-type="' . $imagemimetype . '">'
                        . base64_encode($imagedata) . "</file>\n";
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
                        $documentlinks = "<documentLinks>" . $xmlfiledata . "</documentLinks>\n";
                        break;
                    case "word/footnotes.xml":
                        $wordmldata .= "<footnotesContainer>" . $xmlfiledata . "</footnotesContainer>\n";
                        break;
                    case "word/_rels/footnotes.xml.rels":
                        $wordmldata .= "<footnoteLinks>" . $xmlfiledata . "</footnoteLinks>\n";
                        break;
                }
            }
            // Get the next file in the Zip package.
            $zipentry = zip_read($zipres);
        }  // End while loop.
        zip_close($zipres);

        // Preprocess the document links and images to rename any re-formatted GIF images to be PNGs instead.
        // Fixing filenames in the document links is the simplest solution for this issue.
        if (count($gifimagefilenames) > 0) {
            $documentlinks = str_replace($gifimagefilenames, $pngimagefilenames, $documentlinks);
            $imagestring = str_replace($gifimagefilenames, $pngimagefilenames, $imagestring);
            $imagestring = str_replace("image/gif", "image/png", $imagestring);
        }

        // Add Base64 images section and close the merged XML file.
        $wordmldata .= $documentlinks . "<imagesContainer>\n" . $imagestring . "</imagesContainer>\n";
        $wordmldata .= "</pass1Container>";

        // Pass 1 - convert WordML into linear XHTML.
        $xsltoutput = $this->xsltransform($wordmldata, $this->word2xmlstylesheet1);

        // Pass 2 - tidy up linear XHTML.
        $xsltoutput = $this->xsltransform($xsltoutput, $this->word2xmlstylesheet2);

        // Remove extra superfluous markup included in the Word to XHTML conversion.
        $xsltoutput = str_replace("</strong><strong>", "", $xsltoutput);
        $xsltoutput = str_replace("</em><em>", "", $xsltoutput);
        $xsltoutput = str_replace("</u><u>", "", $xsltoutput);

        unlink($filename);
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
     * @param string $moodlelabels User-readable text strings that may be included in the output file
     * @param string $imagehandling Embedded or encoded image data
     * @return string Word-compatible XHTML text
     */
    public function export(string $xhtmldata, string $module, string $moodlelabels, string $imagehandling = 'embedded') {

        // Check the HTML template exists.
        if (!file_exists($this->wordfiletemplate)) {
            throw new \moodle_exception(get_string('stylesheetunavailable', 'booktool_wordimport', $this->wordfiletemplate));
            return false;
        }

        // Uncomment next line to give XSLT as much memory as possible, to enable larger Word files to be exported.
        // @codingStandardsIgnoreLine raise_memory_limit(MEMORY_HUGE);

        // Clean up the content to ensure it is well-formed XML and won't break the XSLT processing.
        $cleancontent = $this->clean_html_text($xhtmldata);

        // Set parameters for XSLT transformation. Note that we cannot use $arguments though.
        $parameters = array (
            'pluginname' => $module,
            'exportimagehandling' => $imagehandling // Embedded or appended images.
        );

        // Append the Moodle text labels with the local ones for error and warning messages.
        $moodlelabels = str_ireplace('</moodlelabels>', $this->get_text_labels() . "\n</moodlelabels>", $moodlelabels);

        // Assemble the book contents, the HTML template and Moodle text labels to a single XML file for easier XSLT processing.
        $xhtmloutput = "<container>\n<container><html xmlns='http://www.w3.org/1999/xhtml'><body>" .
                $cleancontent . "</body></html></container>\n" .
                "<htmltemplate>\n" . file_get_contents($this->wordfiletemplate) . "\n</htmltemplate>\n" .
                $moodlelabels . "</container>";

        // Do Pass 2 XSLT transformation (Pass 1 must be done in separate convert() call if necessary).
        $xsltoutput = $this->xsltransform($xhtmloutput, $this->exportstylesheet, $parameters);
        $xsltoutput = $this->clean_comments($xsltoutput);
        $xsltoutput = $this->clean_xmldecl($xsltoutput);

        return $xsltoutput;
    }   // End export function.

    /**
     * Split HTML into multiple sections based on headings
     *
     * The HTML content must have been created by mapping Heading 1 styles in Word into h3 elements in the HTML.
     *
     * @param string $htmlcontent HTML from a single Word file
     * @param ZipArchive $zipfile Zip file to insert HTML sections into
     * @param bool $splitonsubheadings Split on Heading 2 (h4) as well as Heading 1 (h3) styles/elements
     * @param bool $verbose Display extra progress messages
     * @return void
     */
    public function split_html(string $htmlcontent, ZipArchive $zipfile, bool $splitonsubheadings, bool $verbose = false) {

        // Split the single HTML file into multiple chapters based on h3 elements.
        $h3matches = null;
        $chaptermatches = null;
        $htmlelement = '<html xmlns="http://www.w3.org/1999/xhtml" ' .
            'xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math">';
        // Grab title and contents of each 'Heading 1' section, which is mapped to h3.
        $chaptermatches = preg_split('#<h3>.*</h3>#isU', $htmlcontent);
        $langmatches = array();
        preg_match('#<meta name="moodleLanguage" content="(.*)"/>#i', $htmlcontent, $langmatches);
        preg_match_all('#<h3>(.*)</h3>#i', $htmlcontent, $h3matches);

        // If no h3 elements are present, treat the whole file as a single chapter.
        if (count($chaptermatches) == 1) {
            $zipfile->addFromString("index.htm", $htmlcontent);
        }

        // Create a separate HTML file in the Zip file for each section of content.
        for ($i = 1; $i < count($chaptermatches); $i++) {
            // Remove any tags from heading, as it prevents proper import of the chapter title.
            $chaptitle = strip_tags($h3matches[1][$i - 1]);
            $chapcontent = $chaptermatches[$i];
            $chapfilename = sprintf("index%02d.htm", $i);

            // Remove the closing HTML markup from the last section.
            if ($i == (count($chaptermatches) - 1)) {
                $chapcontent = substr($chapcontent, 0, strpos($chapcontent, "</div></body>"));
            }

            if ($splitonsubheadings) {
                // Save each subsection as a separate HTML file with a '_sub.htm' suffix.
                $h4matches = null;
                $subchaptermatches = null;
                // Grab title and contents of each subsection.
                preg_match_all('#<h4>(.*)</h4>#i', $chapcontent, $h4matches);
                $subchaptermatches = preg_split('#<h4>.*</h4>#isU', $chapcontent);

                // First save the initial chapter content.
                $chapcontent = $subchaptermatches[0];
                $chapfilename = sprintf("index%02d_00.htm", $i);
                $htmlfilecontent = $htmlelement . "<head><title>{$chaptitle}</title>" . $langmatches[0] . "</head>" .
                    "<body>{$chapcontent}</body></html>";
                $zipfile->addFromString($chapfilename, $htmlfilecontent);

                // Save each subsection to a separate file.
                for ($j = 1; $j < count($subchaptermatches); $j++) {
                    $subchaptitle = strip_tags($h4matches[1][$j - 1]);
                    $subchapcontent = $subchaptermatches[$j];
                    $subsectionfilename = sprintf("index%02d_%02d_sub.htm", $i, $j);
                    $htmlfilecontent = $htmlelement . "<head><title>{$subchaptitle}</title>" . $langmatches[0] . "</head>" .
                        "<body>{$subchapcontent}</body></html>";
                    $zipfile->addFromString($subsectionfilename, $htmlfilecontent);
                }
            } else {
                // Save each section as a HTML file.
                $htmlfilecontent = $htmlelement . "<head><title>{$chaptitle}</title>" . $langmatches[0] . "</head>" .
                    "<body>{$chapcontent}</body></html>";
                $zipfile->addFromString($chapfilename, $htmlfilecontent);
            }
        }
        $zipfile->close();
    }

    /**
     * Store the Zip file in the temporary file storage area
     *
     * @param string $zipfilename Name of temporary Zip file
     * @param ZipArchive $zipfile Zip file to insert HTML sections into
     * @param context_module $context
     * @return file_info Stored Zip file information
     */
    public function store_html(string $zipfilename, ZipArchive $zipfile, \context_module $context) {

        // Create a record for the file store.
        $fs = get_file_storage();
        $zipfilerecord = array(
            'contextid' => $context->id,
            'component' => 'user',
            'filearea' => 'draft',
            'itemid' => 0,
            'filepath' => "/",
            'filename' => basename($zipfilename)
            );
        // Copy the temporary Zip file into the store, and then delete it.
        $zipfile = $fs->create_file_from_pathname($zipfilerecord, $zipfilename);
        return $zipfile;
    }

    /**
     * Store images in a Zip file
     *
     * @param string $zipfilename Name and location of Zip file to create
     * @param array $images Array of image data
     * @return \ZipArchive Stored Zip file information
     */
    public function zip_images(string $zipfilename, array $images) {
        // Create a temporary Zip file.
        $zipfile = new \ZipArchive();
        if (!($zipfile->open($zipfilename, ZipArchive::CREATE))) {
            // Cannot open zip file.
            throw new \moodle_exception('cannotopenzip', 'error');
        }

        // Add any images to the Zip file.
        if (count($images) > 0) {
            foreach ($images as $imagename => $imagedata) {
                $zipfile->addFromString($imagename, $imagedata);
            }
        }
        return $zipfile;
    }

    /**
     * Get images and write them as base64 inside the HTML content for Word export
     *
     * A string containing the HTML with embedded base64 images is returned.
     * If the images are in GIF format, convert them to PNG for Word compatibility.
     *
     * @param string $contextid the context ID
     * @param string $component File component: book, question, glossary, lesson
     * @param string $filearea File area within component
     * @param array $giffilenames array to store GIF image file names
     * @param string $chapterid the chapter or page ID (optional)
     * @return string the modified HTML with embedded images
     */
    public function base64_images(string $contextid, string $component, string $filearea, $chapterid = null, array &$giffilenames) {
        // Get the list of files embedded in the book or chapter.
        // Note that this will break on images in the Book Intro section.
        $imagestring = '';
        $fs = get_file_storage();
        if ($filearea == 'intro') {
            $files = $fs->get_area_files($contextid, $component, $filearea);
        } else {
            $files = $fs->get_area_files($contextid, $component, $filearea, $chapterid);
        }

        // Keep a count of the GIF images that need to be converted to PNG for Word compatibility.
        $i = 0;
        foreach ($files as $fileinfo) {
            // Process image files, converting them into Base64 encoding.
            $fileext = strtolower(pathinfo($fileinfo->get_filename(), PATHINFO_EXTENSION));
            if ($fileext == 'png' or $fileext == 'jpg' or $fileext == 'jpeg' or $fileext == 'gif') {
                $filename = $fileinfo->get_filename();
                $filetype = ($fileext == 'jpg') ? 'jpeg' : $fileext;
                $fileitemid = $fileinfo->get_itemid();
                $filepath = $fileinfo->get_filepath();
                $filedata = $fs->get_file($contextid, $component, $filearea, $fileitemid, $filepath, $filename);
                $imagedata = $filedata->get_content();
                if (!$filedata === false) {
                    // Convert GIF images to PNG for Word compatibility.
                    if ($fileext == 'gif') {
                        list ($pngfilename, $imagedata) = $this->giftopng($imagedata, $filename);
                        $giffilenames['png'][$i] = basename($filename, '.gif') . "-" . $pngfilename;
                        $giffilenames['gif'][$i] = $filename;
                        // Change image1.gif to image1XXX.png", where XXX is taken from the temporary PNG file name.
                        $filetype = 'png';
                        $filename = $giffilenames['png'][$i];
                        $i++;
                    }
                    $base64data = base64_encode($imagedata);
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
     * Convert a GIF image to PNG format for Word compatibility
     *
     * A string containing the PNG instead of GIF image data is returned
     *
     * @param string $imagedata GIF image data
     * @param string $filename GIF image filename to use in case of errors
     * @return array containing the temporary PNG file name and the PNG image data
     */
    private function giftopng(string $imagedata, string $filename) {
        global $CFG;

        // Store the GIF data in a temporary file.
        if (($giffile = tempnam($CFG->tempdir, "gif")) === false) {
            return false;
        }
        file_put_contents($giffile, $imagedata);
        if (($gdimagedata = imagecreatefromgif($giffile)) === false) {
            unlink($giffile);
            return false;
        }

        // Convert the GIF data into a temporary PNG file.
        if (($pngfile = tempnam($CFG->tempdir, "png")) === false) {
            unlink($giffile);
            return false;
        }
        if ((imagepng($gdimagedata, $pngfile)) === true) {
            $imagedata = file_get_contents($pngfile);
        } else { // Use invalid.png if the image function failed.
            $imagedata = base64_decode("iVBORw0KGgoAAAANSUhEUgAAABAAAAAQBAMAAADt3eJSAAAAMFBMVEUAAAD/QDz/QDz/QDz/QDz/QDz/" .
                "QDz/QDz/QDz/QDz/QDz/QDz/QDz/QDz/QDz/QDxu8djTAAAAD3RSTlMAECAwQFBgcICPn6+/z+/qVnWSAAAAcklEQVQIHQXBzQ3BAAA" .
                "G0Nf4KeLQBawgFpDYgBG6QTuCARyMYAMjcHQVA0gs4CI9ED7vUbSUFWZvjleKe7ajfCuDZ7omvw3LJLmhOCbvCsbJHpRJC+bJA5y65g" .
                "P91MOsMO1ozljXDF+4wI7eASYLfWD1B++9KixJ5BTzAAAAAElFTkSuQmCC");
        }
        unlink($giffile);
        unlink($pngfile);
        // Change "pngXXXX.tmp" into "XXXX.png" for appending to the original GIF file name.
        $pngfilename = substr(basename($pngfile, ".tmp"), 3) . ".png";
        return array($pngfilename, $imagedata);
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
    public function htmlbody($xhtmldata) {
        ;
        if (($htmlbody = toolbook_importhtml_parse_body($xhtmldata)) != '') {
            return $htmlbody;
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
        $this->xsltparameters['imagehandling'] = $imagehandling;
    }

    /**
     * Set the mapping between Word Heading style level, and HTML heading element
     *
     * @param int $headinglevel Heading style level (e.g. 1, 2 or 3)
     * @return void
     */
    public function set_heading1styleoffset(int $headinglevel) {
        $this->xsltparameters['heading1stylelevel'] = $headinglevel;
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
            'booktool_wordimport' => array('transformationfailed', 'embeddedimageswarning', 'encodedimageswarning')
            );

        $expout = "";
        foreach ($textstrings as $typegroup => $grouparray) {
            foreach ($grouparray as $stringid) {
                $namestring = $typegroup . '_' . $stringid;
                // Clean up question type explanation, in case the default text has been overridden on the site.
                $cleantext = $this->convert_to_xml(get_string($stringid, $typegroup));
                $expout .= '<data name="' . $namestring . '"><value>' . $cleantext . "</value></data>\n";
            }
        }
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
    public function clean_html_text(string $cdatastring) {

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
     * Convert content into well-formed XML
     *
     * A string containing clean XHTML is returned
     *
     * @param string $cdatastring XHTML from questions or help text
     * @return string well-formed XML
     */
    public function convert_to_xml($cdatastring) {

        // Escape double minuses, which cause XSLT processing to fail.
        $cdatastring = str_replace("--", "WordTableMinusMinus", $cdatastring);

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
     * Write contents to a file for debugging purposes
     *
     * @param string $content text to save
     * @param string $prefix file prefix
     * @return void
     */
    public function debug_write(string $content, string $prefix) {
        global $CFG;

        if (debugging(null, DEBUG_DEVELOPER)) {
            $tempxmlfilename = tempnam($CFG->tempdir, $prefix);
            file_put_contents($tempxmlfilename, $content);
        }
    }

    /**
     * Delete temporary files if debugging disabled
     *
     * @param string $filename name of file to be deleted
     * @return void
     */
    public function debug_unlink($filename) {
        if (!debugging(null, DEBUG_DEVELOPER)) { // Not Parenthesis debugging(null, DEBUG_DEVELOPER) parenthesis.
            unlink($filename);
        }
    }
}
