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
use setasign\Fpdi\TcpdfFpdi;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/pdflib.php');
require_once($CFG->dirroot.'/mod/assign/feedback/editpdf/fpdi/autoload.php');

/**
 * Library code for manipulating PDFs
 *
 * @package assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class pdf extends TcpdfFpdi {

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
    /** Blank PDF file used during error. */
    const BLANK_PDF = '/mod/assign/feedback/editpdf/fixtures/blank.pdf';
    /** Page image file name prefix*/
    const IMAGE_PAGE = 'image_page';
    /**
     * Get the name of the font to use in generated PDF files.
     * If $CFG->pdfexportfont is set - use it, otherwise use "freesans" as this
     * open licensed font has wide support for different language charsets.
     *
     * @return string
     */
    private function get_export_font_name() {
        global $CFG;

        $fontname = 'freesans';
        if (!empty($CFG->pdfexportfont)) {
            $fontname = $CFG->pdfexportfont;
        }
        return $fontname;
    }

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
        // Use font supporting the widest range of characters.
        $this->SetFont($this->get_export_font_name(), '', 16.0 * $this->scale, '', true);
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
        $this->SetFont($this->get_export_font_name(), '', 16.0 * $this->scale, '', true);
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

        // Create a page of the required size / orientation.
        $this->AddPage($size['orientation'], array($size['width'], $size['height']));
        // Prevent new page creation when comments are at the bottom of a page.
        $this->setPageOrientation($size['orientation'], false, 0);
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
     * Append all comments to the end of the document.
     *
     * @param array $allcomments All comments, indexed by page number (starting at 0).
     * @return array|bool An array of links to comments, or false.
     */
    public function append_comments($allcomments) {
        if (!$this->filename) {
            return false;
        }

        $this->SetFontSize(12 * $this->scale);
        $this->SetMargins(100 * $this->scale, 120 * $this->scale, -1, true);
        $this->SetAutoPageBreak(true, 100 * $this->scale);
        $this->setHeaderFont(array($this->get_export_font_name(), '', 24 * $this->scale, '', true));
        $this->setHeaderMargin(24 * $this->scale);
        $this->setHeaderData('', 0, '', get_string('commentindex', 'assignfeedback_editpdf'));

        // Add a new page to the document with an appropriate header.
        $this->setPrintHeader(true);
        $this->AddPage();

        // Add the comments.
        $commentlinks = array();
        foreach ($allcomments as $pageno => $comments) {
            foreach ($comments as $index => $comment) {
                // Create a link to the current location, which will be added to the marker.
                $commentlink = $this->AddLink();
                $this->SetLink($commentlink, -1);
                $commentlinks[$pageno][$index] = $commentlink;
                // Also create a link back to the marker, which will be added here.
                $markerlink = $this->AddLink();
                $this->SetLink($markerlink, $comment->y * $this->scale, $pageno + 1);
                $label = get_string('commentlabel', 'assignfeedback_editpdf', array('pnum' => $pageno + 1, 'cnum' => $index + 1));
                $this->Cell(50 * $this->scale, 0, $label, 0, 0, '', false, $markerlink);
                $this->MultiCell(0, 0, $comment->rawtext, 0, 'L');
                $this->Ln(12 * $this->scale);
            }
            // Add an extra line break between pages.
            $this->Ln(12 * $this->scale);
        }

        return $commentlinks;
    }

    /**
     * Add a comment marker to the specified page.
     *
     * @param int $pageno The page number to add markers to (starting at 0).
     * @param int $index The comment index.
     * @param int $x The x-coordinate of the marker (in pixels).
     * @param int $y The y-coordinate of the marker (in pixels).
     * @param int $link The link identifier pointing to the full comment text.
     * @param string $colour The fill colour of the marker (red, yellow, green, blue, white, clear).
     * @return bool Success status.
     */
    public function add_comment_marker($pageno, $index, $x, $y, $link, $colour = 'yellow') {
        if (!$this->filename) {
            return false;
        }

        $fill = '';
        $fillopacity = 0.9;
        switch ($colour) {
            case 'red':
                $fill = 'rgb(249, 181, 179)';
                break;
            case 'green':
                $fill = 'rgb(214, 234, 178)';
                break;
            case 'blue':
                $fill = 'rgb(203, 217, 237)';
                break;
            case 'white':
                $fill = 'rgb(255, 255, 255)';
                break;
            case 'clear':
                $fillopacity = 0;
                break;
            default: /* Yellow */
                $fill = 'rgb(255, 236, 174)';
        }
        $marker = '@<svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.5 -0.5 12 12" preserveAspectRatio="xMinYMin meet">' .
                '<path d="M11 0H1C.4 0 0 .4 0 1v6c0 .6.4 1 1 1h1v4l4-4h5c.6 0 1-.4 1-1V1c0-.6-.4-1-1-1z" fill="' . $fill . '" ' .
                'fill-opacity="' . $fillopacity . '" stroke="rgb(153, 153, 153)" stroke-width="0.5"/></svg>';
        $label = get_string('commentlabel', 'assignfeedback_editpdf', array('pnum' => $pageno + 1, 'cnum' => $index + 1));

        $x *= $this->scale;
        $y *= $this->scale;
        $size = 24 * $this->scale;
        $this->SetDrawColor(51, 51, 51);
        $this->SetFontSize(10 * $this->scale);
        $this->setPage($pageno + 1);

        // Add the marker image.
        $this->ImageSVG($marker, $x - 0.5, $y - 0.5, $size, $size, $link);

        // Add the label.
        $this->MultiCell($size * 0.95, 0, $label, 0, 'C', false, 1, $x, $y, true, 0, false, true, $size * 0.60, 'M', true);

        return true;
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
     * @throws \moodle_exception
     * @throws \coding_exception
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

        $imagefile = $this->imagefolder . '/' . self::IMAGE_PAGE . $pageno . '.png';
        $generate = true;
        if (file_exists($imagefile)) {
            if (filemtime($imagefile) > filemtime($this->filename)) {
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
                $fullerror .= htmlspecialchars(implode("\n", $output)) . '</pre>';
                throw new \moodle_exception('errorgenerateimage', 'assignfeedback_editpdf', '', $fullerror);
            }
        }

        return self::IMAGE_PAGE . $pageno . '.png';
    }

    /**
     * Check to see if PDF is version 1.4 (or below); if not: use ghostscript to convert it
     *
     * @param stored_file $file
     * @return string path to copy or converted pdf (false == fail)
     */
    public static function ensure_pdf_compatible(\stored_file $file) {
        global $CFG;

        // Copy the stored_file to local disk for checking.
        $temparea = make_request_directory();
        $tempsrc = $temparea . "/source.pdf";
        $file->copy_content_to($tempsrc);

        return self::ensure_pdf_file_compatible($tempsrc);
    }

    /**
     * Check to see if PDF is version 1.4 (or below); if not: use ghostscript to convert it
     *
     * @param   string $tempsrc The path to the file on disk.
     * @return  string path to copy or converted pdf (false == fail)
     */
    public static function ensure_pdf_file_compatible($tempsrc) {
        global $CFG;

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
            // PDF is already valid and can be read by tcpdf.
            return $tempsrc;
        }

        $temparea = make_request_directory();
        $tempdst = $temparea . "/target.pdf";

        $gsexec = \escapeshellarg($CFG->pathtogs);
        $tempdstarg = \escapeshellarg($tempdst);
        $tempsrcarg = \escapeshellarg($tempsrc);
        $command = "$gsexec -q -sDEVICE=pdfwrite -dBATCH -dNOPAUSE -sOutputFile=$tempdstarg $tempsrcarg";
        exec($command);
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
            // Could not parse the converted pdf.
            return false;
        }

        return $tempdst;
    }

    /**
     * Generate an localised error image for the given pagenumber.
     *
     * @param string $errorimagefolder path of the folder where error image needs to be created.
     * @param int $pageno page number for which error image needs to be created.
     *
     * @return string File name
     * @throws \coding_exception
     */
    public static function get_error_image($errorimagefolder, $pageno) {
        global $CFG;

        $errorfile = $CFG->dirroot . self::BLANK_PDF;
        if (!file_exists($errorfile)) {
            throw new \coding_exception("Blank PDF not found", "File path" . $errorfile);
        }

        $tmperrorimagefolder = make_request_directory();

        $pdf = new pdf();
        $pdf->set_pdf($errorfile);
        $pdf->copy_page();
        $pdf->add_comment(get_string('errorpdfpage', 'assignfeedback_editpdf'), 250, 300, 200, "red");
        $generatedpdf = $tmperrorimagefolder . '/' . 'error.pdf';
        $pdf->save_pdf($generatedpdf);

        $pdf = new pdf();
        $pdf->set_pdf($generatedpdf);
        $pdf->set_image_folder($tmperrorimagefolder);
        $image = $pdf->get_image(0);
        $pdf->Close(); // PDF loaded and never saved/outputted needs to be closed.
        $newimg = self::IMAGE_PAGE . $pageno . '.png';

        copy($tmperrorimagefolder . '/' . $image, $errorimagefolder . '/' . $newimg);
        return $newimg;
    }

    /**
     * Test that the configured path to ghostscript is correct and working.
     * @param bool $generateimage - If true - a test image will be generated to verify the install.
     * @return \stdClass
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
        $filepath = $testimagefolder . '/' . self::IMAGE_PAGE . '0.png';
        // Delete any previous test images, if they exist.
        if (file_exists($filepath)) {
            unlink($filepath);
        }

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
        $testimage = $testimagefolder . '/' . self::IMAGE_PAGE . '0.png';
        send_file($testimage, basename($testimage), 0);
        die();
    }

    /**
     * This function add an image file to PDF page.
     * @param \stored_file $imagestoredfile Image file to be added
     */
    public function add_image_page($imagestoredfile) {
        $imageinfo = $imagestoredfile->get_imageinfo();
        $imagecontent = $imagestoredfile->get_content();
        $this->currentpage++;
        $template = $this->importPage($this->currentpage);
        $size = $this->getTemplateSize($template);

        if ($imageinfo["width"] > $imageinfo["height"]) {
            if ($size['width'] < $size['height']) {
                $temp = $size['width'];
                $size['width'] = $size['height'];
                $size['height'] = $temp;
            }
        } else if ($imageinfo["width"] < $imageinfo["height"]) {
            if ($size['width'] > $size['height']) {
                $temp = $size['width'];
                $size['width'] = $size['height'];
                $size['height'] = $temp;
            }
        }
        $orientation = $size['orientation'];
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetMargins(0, 0, 0, true);
        $this->setPrintFooter(false);
        $this->setPrintHeader(false);

        $this->AddPage($orientation, $size);
        $this->SetAutoPageBreak(false, 0);
        $this->Image('@' . $imagecontent, 0, 0, $size['w'], $size['h'],
            '', '', '', false, null, '', false, false, 0);
    }
}

