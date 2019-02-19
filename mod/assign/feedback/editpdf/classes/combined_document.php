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
 * This file contains the combined document class for the assignfeedback_editpdf plugin.
 *
 * @package   assignfeedback_editpdf
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignfeedback_editpdf;

defined('MOODLE_INTERNAL') || die();

/**
 * The combined_document class for the assignfeedback_editpdf plugin.
 *
 * @copyright 2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class combined_document {

    /**
     * Status value representing a conversion waiting to start.
     */
    const STATUS_PENDING_INPUT = 0;

    /**
     * Status value representing all documents ready to be combined.
     */
    const STATUS_READY = 1;

    /**
     * Status value representing all documents are ready to be combined as are supported.
     */
    const STATUS_READY_PARTIAL = 3;

    /**
     * Status value representing a successful conversion.
     */
    const STATUS_COMPLETE = 2;

    /**
     * Status value representing a permanent error.
     */
    const STATUS_FAILED = -1;

    /**
     * The list of files which make this document.
     */
    protected $sourcefiles = [];

    /**
     * The resultant combined file.
     */
    protected $combinedfile;

    /**
     * The combination status.
     */
    protected $combinationstatus = null;

    /**
     * The number of pages in the combined PDF.
     */
    protected $pagecount = 0;

    /**
     * Check the current status of the document combination.
     * Note that the combined document may not contain all the source files if some of the
     * source files were not able to be converted. An example is an audio file with a pdf cover sheet. Only
     * the cover sheet will be included in the combined document.
     *
     * @return  int
     */
    public function get_status() {
        if ($this->combinedfile) {
            // The combined file exists. Report success.
            return self::STATUS_COMPLETE;
        }

        if (empty($this->sourcefiles)) {
            // There are no source files to combine.
            return self::STATUS_FAILED;
        }

        if (!empty($this->combinationstatus)) {
            // The combination is in progress and has set a status.
            // Return it instead.
            return $this->combinationstatus;
        }

        $pending = false;
        $partial = false;
        foreach ($this->sourcefiles as $file) {
            // The combined file has not yet been generated.
            // Check the status of each source file.
            if (is_a($file, \core_files\conversion::class)) {
                $status = $file->get('status');
                switch ($status) {
                    case \core_files\conversion::STATUS_IN_PROGRESS:
                    case \core_files\conversion::STATUS_PENDING:
                        $pending = true;
                        break;

                    // There are 4 status flags, so the only remaining one is complete which is fine.
                    case \core_files\conversion::STATUS_FAILED:
                        $partial = true;
                        break;
                }
            }
        }
        if ($pending) {
            return self::STATUS_PENDING_INPUT;
        } else {
            if ($partial) {
                return self::STATUS_READY_PARTIAL;
            }
            return self::STATUS_READY;
        }
    }
    /**
     * Set the completed combined file.
     *
     * @param   stored_file $file The completed document for all files to be combined.
     * @return  $this
     */
    public function set_combined_file($file) {
        $this->combinedfile = $file;

        return $this;
    }

    /**
     * Return true of the combined file contained only some of the submission files.
     *
     * @return  boolean
     */
    public function is_partial_conversion() {
        $combinedfile = $this->get_combined_file();
        if (empty($combinedfile)) {
            return false;
        }
        $filearea = $combinedfile->get_filearea();
        return $filearea == document_services::PARTIAL_PDF_FILEAREA;
    }

    /**
     * Retrieve the completed combined file.
     *
     * @return  stored_file
     */
    public function get_combined_file() {
        return $this->combinedfile;
    }

    /**
     * Set all source files which are to be combined.
     *
     * @param   stored_file|conversion[] $files The complete list of all source files to be combined.
     * @return  $this
     */
    public function set_source_files($files) {
        $this->sourcefiles = $files;

        return $this;
    }

    /**
     * Add an additional source file to the end of the existing list.
     *
     * @param   stored_file|conversion $file The file to add to the end of the list.
     * @return  $this
     */
    public function add_source_file($file) {
        $this->sourcefiles[] = $file;

        return $this;
    }

    /**
     * Retrieve the complete list of source files.
     *
     * @return  stored_file|conversion[]
     */
    public function get_source_files() {
        return $this->sourcefiles;
    }

    /**
     * Refresh the files.
     *
     * This includes polling any pending conversions to see if they are complete.
     *
     * @return  $this
     */
    public function refresh_files() {
        $converter = new \core_files\converter();
        foreach ($this->sourcefiles as $file) {
            if (is_a($file, \core_files\conversion::class)) {
                $status = $file->get('status');
                switch ($status) {
                    case \core_files\conversion::STATUS_COMPLETE:
                        continue;
                        break;
                    default:
                        $converter->poll_conversion($conversion);
                }
            }
        }

        return $this;
    }

    /**
     * Combine all source files into a single PDF and store it in the
     * file_storage API using the supplied contextid and itemid.
     *
     * @param   int $contextid The contextid for the file to be stored under
     * @param   int $itemid The itemid for the file to be stored under
     * @return  $this
     */
    public function combine_files($contextid, $itemid) {
        global $CFG;

        $currentstatus = $this->get_status();
        $readystatuslist = [self::STATUS_READY, self::STATUS_READY_PARTIAL];
        if ($currentstatus === self::STATUS_FAILED) {
            $this->store_empty_document($contextid, $itemid);

            return $this;
        } else if (!in_array($currentstatus, $readystatuslist)) {
            // The document is either:
            // * already combined; or
            // * pending input being fully converted; or
            // * unable to continue due to an issue with the input documents.
            //
            // Exit early as we cannot continue.
            return $this;
        }

        require_once($CFG->libdir . '/pdflib.php');

        $pdf = new pdf();
        $files = $this->get_source_files();
        $compatiblepdfs = [];

        foreach ($files as $file) {
            // Check that each file is compatible and add it to the list.
            // Note: We drop non-compatible files.
            $compatiblepdf = false;
            if (is_a($file, \core_files\conversion::class)) {
                $status = $file->get('status');
                if ($status == \core_files\conversion::STATUS_COMPLETE) {
                    $compatiblepdf = pdf::ensure_pdf_compatible($file->get_destfile());
                }
            } else {
                $compatiblepdf = pdf::ensure_pdf_compatible($file);
            }

            if ($compatiblepdf) {
                $compatiblepdfs[] = $compatiblepdf;
            }
        }

        $tmpdir = make_request_directory();
        $tmpfile = $tmpdir . '/' . document_services::COMBINED_PDF_FILENAME;

        try {
            $pagecount = $pdf->combine_pdfs($compatiblepdfs, $tmpfile);
            $pdf->Close();
        } catch (\Exception $e) {
            // Unable to combine the PDF.
            debugging('TCPDF could not process the pdf files:' . $e->getMessage(), DEBUG_DEVELOPER);

            $pdf->Close();
            return $this->mark_combination_failed();
        }

        // Verify the PDF.
        $verifypdf = new pdf();
        $verifypagecount = $verifypdf->load_pdf($tmpfile);
        $verifypdf->Close();

        if ($verifypagecount <= 0) {
            // No pages were found in the combined PDF.
            return $this->mark_combination_failed();
        }

        // Store the newly created file as a stored_file.
        $this->store_combined_file($tmpfile, $contextid, $itemid, ($currentstatus == self::STATUS_READY_PARTIAL));

        // Note the verified page count.
        $this->pagecount = $verifypagecount;

        return $this;
    }

    /**
     * Mark the combination attempt as having encountered a permanent failure.
     *
     * @return  $this
     */
    protected function mark_combination_failed() {
        $this->combinationstatus = self::STATUS_FAILED;

        return $this;
    }

    /**
     * Store the combined file in the file_storage API.
     *
     * @param   string $tmpfile The path to the file on disk to be stored.
     * @param   int $contextid The contextid for the file to be stored under
     * @param   int $itemid The itemid for the file to be stored under
     * @param   boolean $partial The combined pdf contains only some of the source files.
     * @return  $this
     */
    protected function store_combined_file($tmpfile, $contextid, $itemid, $partial = false) {
        // Store the file.
        $record = $this->get_stored_file_record($contextid, $itemid, $partial);
        $fs = get_file_storage();

        // Delete existing files first.
        $fs->delete_area_files($record->contextid, $record->component, $record->filearea, $record->itemid);

        // This was a combined pdf.
        $file = $fs->create_file_from_pathname($record, $tmpfile);

        $this->set_combined_file($file);

        return $this;
    }

    /**
     * Store the empty document file in the file_storage API.
     *
     * @param   int $contextid The contextid for the file to be stored under
     * @param   int $itemid The itemid for the file to be stored under
     * @return  $this
     */
    protected function store_empty_document($contextid, $itemid) {
        // Store the file.
        $record = $this->get_stored_file_record($contextid, $itemid);
        $fs = get_file_storage();

        // Delete existing files first.
        $fs->delete_area_files($record->contextid, $record->component, $record->filearea, $record->itemid);

        $file = $fs->create_file_from_string($record, base64_decode(document_services::BLANK_PDF_BASE64));
        $this->pagecount = 1;

        $this->set_combined_file($file);

        return $this;
    }

    /**
     * Get the total number of pages in the combined document.
     *
     * If there are no pages, or it is not yet possible to count them a
     * value of 0 is returned.
     *
     * @return  int
     */
    public function get_page_count() {
        if ($this->pagecount) {
            return $this->pagecount;
        }

        $status = $this->get_status();

        if ($status === self::STATUS_FAILED) {
            // The empty document will be returned.
            return 1;
        }

        if ($status !== self::STATUS_COMPLETE) {
            // No pages yet.
            return 0;
        }

        // Load the PDF to determine the page count.
        $temparea = make_request_directory();
        $tempsrc = $temparea . "/source.pdf";
        $this->get_combined_file()->copy_content_to($tempsrc);

        $pdf = new pdf();
        $pagecount = $pdf->load_pdf($tempsrc);
        $pdf->Close();

        if ($pagecount <= 0) {
            // Something went wrong. Return an empty page count again.
            return 0;
        }

        $this->pagecount = $pagecount;
        return $this->pagecount;
    }

    /**
     * Get the total number of documents to be combined.
     *
     * @return  int
     */
    public function get_document_count() {
        return count($this->sourcefiles);
    }

    /**
     * Helper to fetch the stored_file record.
     *
     * @param   int $contextid The contextid for the file to be stored under
     * @param   int $itemid The itemid for the file to be stored under
     * @param   boolean $partial The combined file contains only some of the source files.
     * @return  stdClass
     */
    protected function get_stored_file_record($contextid, $itemid, $partial = false) {
        $filearea = document_services::COMBINED_PDF_FILEAREA;
        if ($partial) {
            $filearea = document_services::PARTIAL_PDF_FILEAREA;
        }
        return (object) [
            'contextid' => $contextid,
            'component' => 'assignfeedback_editpdf',
            'filearea' => $filearea,
            'itemid' => $itemid,
            'filepath' => '/',
            'filename' => document_services::COMBINED_PDF_FILENAME,
        ];
    }
}
