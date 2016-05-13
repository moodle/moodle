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
 * Library code for manipulating PDFs
 *
 * @package assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdf;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/pdflib.php');
require_once($CFG->dirroot.'/mod/assign/feedback/editpdf/fpdi/fpdi.php');

/**
 * Library code for manipulating PDFs
 *
 * @package assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdf extends \FPDI {

    /** @var int the number of the current page in the PDF being processed */
    protected $currentpage = 0;
    /** @var int the total number of pages in the PDF being processed */
    protected $pagecount = 0;
    /** @var float used to scale the pixel position of annotations (in the database) to the position in the final PDF */
    protected $scale = 0.0;
    /** @var string the path in which to store generated page images */
    protected $imagefolder = null;
    /** @var string the path to the PDF currently being processed */
    protected $filename = null;

    /** No errors */
    const GSPATH_OK = 'ok';
    /** Not set */
    const GSPATH_EMPTY = 'empty';
    /** Does not exist */
    const GSPATH_DOESNOTEXIST = 'doesnotexist';
    /** Is a dir */
    const GSPATH_ISDIR = 'isdir';
    /** Not executable */
    const GSPATH_NOTEXECUTABLE = 'notexecutable';
    /** Test file missing */
    const GSPATH_NOTESTFILE = 'notestfile';
    /** Any other error */
    const GSPATH_ERROR = 'error';
    /** Min. width an annotation should have */
    const MIN_ANNOTATION_WIDTH = 5;
    /** Min. height an annotation should have */
    const MIN_ANNOTATION_HEIGHT = 5;

    /**
     * Combine the given PDF files into a single PDF. Optionally add a coversheet and coversheet fields.
     * @param string[] $pdflist  the filenames of the files to combine
     * @param string $outfilename the filename to write to
     * @return int the number of pages in the combined PDF
     */
    public function combine_pdfs($pdflist, $outfilename) {

        raise_memory_limit(MEMORY_EXTRA);
        $olddebug = error_reporting(0);

        $this->setPageUnit('pt');
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->scale = 72.0 / 100.0;
        $this->SetFont('helvetica', '', 16.0 * $this->scale);
        $this->SetTextColor(0, 0, 0);

        $totalpagecount = 0;

        foreach ($pdflist as $file) {
            $pagecount = $this->setSourceFile($file);
            $totalpagecount += $pagecount;
            for ($i = 1; $i<=$pagecount; $i++) {
                $this->create_page_from_source($i);
            }
        }

        $this->save_pdf($outfilename);
        error_reporting($olddebug);

        return $totalpagecount;
    }

    /**
     * The number of the current page in the PDF being processed
     * @return int
     */
    public function current_page() {
        return $this->currentpage;
    }

    /**
     * The total number of pages in the PDF being processed
     * @return int
     */
    public function page_count() {
        return $this->pagecount;
    }

    /**
     * Load the specified PDF and set the initial output configuration
     * Used when processing comments and outputting a new PDF
     * @param string $filename the path to the PDF to load
     * @return int the number of pages in the PDF
     */
    public function load_pdf($filename) {
        raise_memory_limit(MEMORY_EXTRA);
        $olddebug = error_reporting(0);

        $this->setPageUnit('pt');
        $this->scale = 72.0 / 100.0;
        $this->SetFont('helvetica', '', 16.0 * $this->scale);
        $this->SetFillColor(255, 255, 176);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(1.0 * $this->scale);
        $this->SetTextColor(0, 0, 0);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->pagecount = $this->setSourceFile($filename);
        $this->filename = $filename;

        error_reporting($olddebug);
        return $this->pagecount;
    }

    /**
     * Sets the name of the PDF to process, but only loads the file if the
     * pagecount is zero (in order to count the number of pages)
     * Used when generating page images (but not a new PDF)
     * @param string $filename the path to the PDF to process
     * @param int $pagecount optional the number of pages in the PDF, if known
     * @return int the number of pages in the PDF
     */
    public function set_pdf($filename, $pagecount = 0) {
        if ($pagecount == 0) {
            return $this->load_pdf($filename);
        } else {
            $this->filename = $filename;
            $this->pagecount = $pagecount;
            return $pagecount;
        }
    }

    /**
     * Copy the next page from the source file and set it as the current page
     * @return bool true if successful
     */
    public function copy_page() {
        if (!$this->filename) {
            return false;
        }
        if ($this->currentpage>=$this->pagecount) {
            return false;
        }
        $this->currentpage++;
        $this->create_page_from_source($this->currentpage);
        return true;
    }

    /**
     * Create a page from a source PDF.
     *
     * @param int $pageno
     */
    protected function create_page_from_source($pageno) {
        // Get the size (and deduce the orientation) of the next page.
        $template = $this->importPage($pageno);
        $size = $this->getTemplateSize($template);
        $orientation = 'P';
        if ($size['w'] > $size['h']) {
            $orientation = 'L';
        }
        // Create a page of the required size / orientation.
        $this->AddPage($orientation, array($size['w'], $size['h']));
        // Prevent new page creation when comments are at the bottom of a page.
        $this->setPageOrientation($orientation, false, 0);
        // Fill in the page with the original contents from the student.
        $this->useTemplate($template);
    }

    /**
     * Copy all the remaining pages in the file
     */
    public function copy_remaining_pages() {
        $morepages = true;
        while ($morepages) {
            $morepages = $this->copy_page();
        }
    }

    /**
     * Add a comment to the current page
     * @param string $text the text of the comment
     * @param int $x the x-coordinate of the comment (in pixels)
     * @param int $y the y-coordinate of the comment (in pixels)
     * @param int $width the width of the comment (in pixels)
     * @param string $colour optional the background colour of the comment (red, yellow, green, blue, white, clear)
     * @return bool true if successful (always)
     */
    public function add_comment($text, $x, $y, $width, $colour = 'yellow') {
        if (!$this->filename) {
            return false;
        }
        $this->SetDrawColor(51, 51, 51);
        switch ($colour) {
            case 'red':
                $this->SetFillColor(249, 181, 179);
                break;
            case 'green':
                $this->SetFillColor(214, 234, 178);
                break;
            case 'blue':
                $this->SetFillColor(203, 217, 237);
                break;
            case 'white':
                $this->SetFillColor(255, 255, 255);
                break;
            default: /* Yellow */
                $this->SetFillColor(255, 236, 174);
                break;
        }

        $x *= $this->scale;
        $y *= $this->scale;
        $width *= $this->scale;
        $text = str_replace('&lt;', '<', $text);
        $text = str_replace('&gt;', '>', $text);
        // Draw the text with a border, but no background colour (using a background colour would cause the fill to
        // appear behind any existing content on the page, hence the extra filled rectangle drawn below).
        $this->MultiCell($width, 1.0, $text, 0, 'L', 0, 4, $x, $y); /* width, height, text, border, justify, fill, ln, x, y */
        if ($colour != 'clear') {
            $newy = $this->GetY();
            // Now we know the final size of the comment, draw a rectangle with the background colour.
            $this->Rect($x, $y, $width, $newy - $y, 'DF');
            // Re-draw the text over the top of the background rectangle.
            $this->MultiCell($width, 1.0, $text, 0, 'L', 0, 4, $x, $y); /* width, height, text, border, justify, fill, ln, x, y */
        }
        return true;
    }

    /**
     * Add an annotation to the current page
     * @param int $sx starting x-coordinate (in pixels)
     * @param int $sy starting y-coordinate (in pixels)
     * @param int $ex ending x-coordinate (in pixels)
     * @param int $ey ending y-coordinate (in pixels)
     * @param string $colour optional the colour of the annotation (red, yellow, green, blue, white, black)
     * @param string $type optional the type of annotation (line, oval, rectangle, highlight, pen, stamp)
     * @param int[]|string $path optional for 'pen' annotations this is an array of x and y coordinates for
     *              the line, for 'stamp' annotations it is the name of the stamp file (without the path)
     * @param string $imagefolder - Folder containing stamp images.
     * @return bool true if successful (always)
     */
    public function add_annotation($sx, $sy, $ex, $ey, $colour = 'yellow', $type = 'line', $path, $imagefolder) {
        global $CFG;
        if (!$this->filename) {
            return false;
        }
        switch ($colour) {
            case 'yellow':
                $colourarray = array(255, 207, 53);
                break;
            case 'green':
                $colourarray = array(153, 202, 62);
                break;
            case 'blue':
                $colourarray = array(125, 159, 211);
                break;
            case 'white':
                $colourarray = array(255, 255, 255);
                break;
            case 'black':
                $colourarray = array(51, 51, 51);
                break;
            default: /* Red */
                $colour = 'red';
                $colourarray = array(239, 69, 64);
                break;
        }
        $this->SetDrawColorArray($colourarray);

        $sx *= $this->scale;
        $sy *= $this->scale;
        $ex *= $this->scale;
        $ey *= $this->scale;

        $this->SetLineWidth(3.0 * $this->scale);
        switch ($type) {
            case 'oval':
                $rx = abs($sx - $ex) / 2;
                $ry = abs($sy - $ey) / 2;
                $sx = min($sx, $ex) + $rx;
                $sy = min($sy, $ey) + $ry;

                // $rx and $ry should be >= min width and height
                if ($rx < self::MIN_ANNOTATION_WIDTH) {
                    $rx = self::MIN_ANNOTATION_WIDTH;
                }
                if ($ry < self::MIN_ANNOTATION_HEIGHT) {
                    $ry = self::MIN_ANNOTATION_HEIGHT;
                }

                $this->Ellipse($sx, $sy, $rx, $ry);
                break;
            case 'rectangle':
                $w = abs($sx - $ex);
                $h = abs($sy - $ey);
                $sx = min($sx, $ex);
                $sy = min($sy, $ey);

                // Width or height should be >= min width and height
                if ($w < self::MIN_ANNOTATION_WIDTH) {
                    $w = self::MIN_ANNOTATION_WIDTH;
                }
                if ($h < self::MIN_ANNOTATION_HEIGHT) {
                    $h = self::MIN_ANNOTATION_HEIGHT;
                }
                $this->Rect($sx, $sy, $w, $h);
                break;
            case 'highlight':
                $w = abs($sx - $ex);
                $h = 8.0 * $this->scale;
                $sx = min($sx, $ex);
                $sy = min($sy, $ey) + ($h * 0.5);
                $this->SetAlpha(0.5, 'Normal', 0.5, 'Normal');
                $this->SetLineWidth(8.0 * $this->scale);

                // width should be >= min width
                if ($w < self::MIN_ANNOTATION_WIDTH) {
                    $w = self::MIN_ANNOTATION_WIDTH;
                }

                $this->Rect($sx, $sy, $w, $h);
                $this->SetAlpha(1.0, 'Normal', 1.0, 'Normal');
                break;
            case 'pen':
                if ($path) {
                    $scalepath = array();
                    $points = preg_split('/[,:]/', $path);
                    foreach ($points as $point) {
                        $scalepath[] = intval($point) * $this->scale;
                    }

                    if (!empty($scalepath)) {
                        $this->PolyLine($scalepath, 'S');
                    }
                }
                break;
            case 'stamp':
                $imgfile = $imagefolder . '/' . clean_filename($path);
                $w = abs($sx - $ex);
                $h = abs($sy - $ey);
                $sx = min($sx, $ex);
                $sy = min($sy, $ey);

                // Stamp is always more than 40px, so no need to check width/height.
                $this->Image($imgfile, $sx, $sy, $w, $h);
                break;
            default: // Line.
                $this->Line($sx, $sy, $ex, $ey);
                break;
        }
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(1.0 * $this->scale);

        return true;
    }

    /**
     * Save the completed PDF to the given file
     * @param string $filename the filename for the PDF (including the full path)
     */
    public function save_pdf($filename) {
        $olddebug = error_reporting(0);
        $this->Output($filename, 'F');
        error_reporting($olddebug);
    }

    /**
     * Set the path to the folder in which to generate page image files
     * @param string $folder
     */
    public function set_image_folder($folder) {
        $this->imagefolder = $folder;
    }

    /**
     * Generate an image of the specified page in the PDF
     * @param int $pageno the page to generate the image of
     * @throws moodle_exception
     * @throws coding_exception
     * @return string the filename of the generated image
     */
    public function get_image($pageno) {
        global $CFG;

        if (!$this->filename) {
            throw new \coding_exception('Attempting to generate a page image without first setting the PDF filename');
        }

        if (!$this->imagefolder) {
            throw new \coding_exception('Attempting to generate a page image without first specifying the image output folder');
        }

        if (!is_dir($this->imagefolder)) {
            throw new \coding_exception('The specified image output folder is not a valid folder');
        }

        $imagefile = $this->imagefolder.'/image_page' . $pageno . '.png';
        $generate = true;
        if (file_exists($imagefile)) {
            if (filemtime($imagefile)>filemtime($this->filename)) {
                // Make sure the image is newer than the PDF file.
                $generate = false;
            }
        }

        if ($generate) {
            // Use ghostscript to generate an image of the specified page.
            $gsexec = \escapeshellarg($CFG->pathtogs);
            $imageres = \escapeshellarg(100);
            $imagefilearg = \escapeshellarg($imagefile);
            $filename = \escapeshellarg($this->filename);
            $pagenoinc = \escapeshellarg($pageno + 1);
            $command = "$gsexec -q -sDEVICE=png16m -dSAFER -dBATCH -dNOPAUSE -r$imageres -dFirstPage=$pagenoinc -dLastPage=$pagenoinc ".
                "-dDOINTERPOLATE -dGraphicsAlphaBits=4 -dTextAlphaBits=4 -sOutputFile=$imagefilearg $filename";

            $output = null;
            $result = exec($command, $output);
            if (!file_exists($imagefile)) {
                $fullerror = '<pre>'.get_string('command', 'assignfeedback_editpdf')."\n";
                $fullerror .= $command . "\n\n";
                $fullerror .= get_string('result', 'assignfeedback_editpdf')."\n";
                $fullerror .= htmlspecialchars($result) . "\n\n";
                $fullerror .= get_string('output', 'assignfeedback_editpdf')."\n";
                $fullerror .= htmlspecialchars(implode("\n",$output)) . '</pre>';
                throw new \moodle_exception('errorgenerateimage', 'assignfeedback_editpdf', '', $fullerror);
            }
        }

        return 'image_page'.$pageno.'.png';
    }

    /**
     * Check to see if PDF is version 1.4 (or below); if not: use ghostscript to convert it
     * @param stored_file $file
     * @return string path to copy or converted pdf (false == fail)
     */
    public static function ensure_pdf_compatible(\stored_file $file) {
        global $CFG;

        $temparea = \make_temp_directory('assignfeedback_editpdf');
        $hash = $file->get_contenthash(); // Use the contenthash to make sure the temp files have unique names.
        $tempsrc = $temparea . "/src-$hash.pdf";
        $tempdst = $temparea . "/dst-$hash.pdf";
        $file->copy_content_to($tempsrc); // Copy the file.

        $pdf = new pdf();
        $pagecount = 0;
        try {
            $pagecount = $pdf->load_pdf($tempsrc);
        } catch (\Exception $e) {
            // PDF was not valid - try running it through ghostscript to clean it up.
            $pagecount = 0;
        }
        $pdf->Close(); // PDF loaded and never saved/outputted needs to be closed.

        if ($pagecount > 0) {
            // Page is valid and can be read by tcpdf.
            return $tempsrc;
        }

        $gsexec = \escapeshellarg($CFG->pathtogs);
        $tempdstarg = \escapeshellarg($tempdst);
        $tempsrcarg = \escapeshellarg($tempsrc);
        $command = "$gsexec -q -sDEVICE=pdfwrite -dBATCH -dNOPAUSE -sOutputFile=$tempdstarg $tempsrcarg";
        exec($command);
        @unlink($tempsrc);
        if (!file_exists($tempdst)) {
            // Something has gone wrong in the conversion.
            return false;
        }

        $pdf = new pdf();
        $pagecount = 0;
        try {
            $pagecount = $pdf->load_pdf($tempdst);
        } catch (\Exception $e) {
            // PDF was not valid - try running it through ghostscript to clean it up.
            $pagecount = 0;
        }
        $pdf->Close(); // PDF loaded and never saved/outputted needs to be closed.

        if ($pagecount <= 0) {
            @unlink($tempdst);
            // Could not parse the converted pdf.
            return false;
        }

        return $tempdst;
    }

    /**
     * Test that the configured path to ghostscript is correct and working.
     * @param bool $generateimage - If true - a test image will be generated to verify the install.
     * @return bool
     */
    public static function test_gs_path($generateimage = true) {
        global $CFG;

        $ret = (object)array(
            'status' => self::GSPATH_OK,
            'message' => null,
        );
        $gspath = $CFG->pathtogs;
        if (empty($gspath)) {
            $ret->status = self::GSPATH_EMPTY;
            return $ret;
        }
        if (!file_exists($gspath)) {
            $ret->status = self::GSPATH_DOESNOTEXIST;
            return $ret;
        }
        if (is_dir($gspath)) {
            $ret->status = self::GSPATH_ISDIR;
            return $ret;
        }
        if (!is_executable($gspath)) {
            $ret->status = self::GSPATH_NOTEXECUTABLE;
            return $ret;
        }

        if (!$generateimage) {
            return $ret;
        }

        $testfile = $CFG->dirroot.'/mod/assign/feedback/editpdf/tests/fixtures/testgs.pdf';
        if (!file_exists($testfile)) {
            $ret->status = self::GSPATH_NOTESTFILE;
            return $ret;
        }

        $testimagefolder = \make_temp_directory('assignfeedback_editpdf_test');
        @unlink($testimagefolder.'/image_page0.png'); // Delete any previous test images.

        $pdf = new pdf();
        $pdf->set_pdf($testfile);
        $pdf->set_image_folder($testimagefolder);
        try {
            $pdf->get_image(0);
        } catch (\moodle_exception $e) {
            $ret->status = self::GSPATH_ERROR;
            $ret->message = $e->getMessage();
        }
        $pdf->Close(); // PDF loaded and never saved/outputted needs to be closed.

        return $ret;
    }

    /**
     * If the test image has been generated correctly - send it direct to the browser.
     */
    public static function send_test_image() {
        global $CFG;
        header('Content-type: image/png');
        require_once($CFG->libdir.'/filelib.php');

        $testimagefolder = \make_temp_directory('assignfeedback_editpdf_test');
        $testimage = $testimagefolder.'/image_page0.png';
        send_file($testimage, basename($testimage), 0);
        die();
    }

}

