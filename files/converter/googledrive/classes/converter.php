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
 * Class for converting files between different file formats using google drive.
 *
 * @package    fileconverter_googledrive
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace fileconverter_googledrive;

defined('MOODLE_INTERNAL') || die();

use stored_file;
use moodle_exception;
use moodle_url;
use \core_files\conversion;

/**
 * Class for converting files between different formats using unoconv.
 *
 * @package    fileconverter_googledrive
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class converter implements \core_files\converter_interface {

    /** @var array $imports List of supported import file formats */
    private static $imports = [
        'doc' => 'application/vnd.google-apps.document',
        'docx' => 'application/vnd.google-apps.document',
        'rtf' => 'application/vnd.google-apps.document',
        'xls' => 'application/vnd.google-apps.spreadsheet',
        'xlsx' => 'application/vnd.google-apps.spreadsheet',
        'ppt' => 'application/vnd.google-apps.presentation',
        'pptx' => 'application/vnd.google-apps.presentation',
        'html' => 'application/vnd.google-apps.document'
    ];

    /** @var array $export List of supported export file formats */
    private static $exports = [
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'rtf' => 'application/rtf',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'pdf' => 'application/pdf',
        'txt' => 'text/plain'
    ];

    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   \core_files\conversion $conversion The file to be converted
     * @return  this
     */
    public function start_document_conversion(\core_files\conversion $conversion) {
        global $CFG;

        $file = $conversion->get_sourcefile();
        $format = $conversion->get('targetformat');

        $issuerid = get_config('fileconverter_googledrive', 'issuerid');
        if (empty($issuerid)) {
            $conversion->set('status', conversion::STATUS_FAILED);
            return $this;
        }

        $issuer = \core\oauth2\api::get_issuer($issuerid);
        if (empty($issuer)) {
            $conversion->set('status', conversion::STATUS_FAILED);
            return $this;
        }
        $client = \core\oauth2\api::get_system_oauth_client($issuer);

        $service = new \fileconverter_googledrive\rest($client);

        $contenthash = $file->get_contenthash();

        $originalname = $file->get_filename();
        if (strpos($originalname, '.') === false) {
            $conversion->set('status', conversion::STATUS_FAILED);
            return $this;
        }
        $importextension = substr($originalname, strrpos($originalname, '.') + 1);

        $importformat = self::$imports[$importextension];
        $exportformat = self::$exports[$format];

        $metadata = [
            'name' => $contenthash,
            'mimeType' => $importformat
        ];

        $filecontent = $file->get_content();
        $filesize = $file->get_filesize();
        $filemimetype = $file->get_mimetype();

        // Start resumable upload.
        // First create empty file.
        $params = [
            'uploadType' => 'resumable',
            'fields' => 'id,name'
        ];

        $client->setHeader('X-Upload-Content-Type: ' . $filemimetype);
        $client->setHeader('X-Upload-Content-Length: ' . $filesize);

        $headers = $service->call('upload', $params, json_encode($metadata));

        $uploadurl;
        // Google returns a location header with the location for the upload.
        foreach ($headers as $header) {
            if (stripos($header, 'Location:') === 0) {
                $uploadurl = trim(substr($header, strpos($header, ':') + 1));
            }
        }

        if (empty($uploadurl)) {
            $conversion->set('status', conversion::STATUS_FAILED);
            return $this;
        }

        $params = [
            'uploadurl' => $uploadurl
        ];
        $result = $service->call('upload_content', $params, $filecontent, $filemimetype);

        $fileid = $result->id;
        // Now export it again.
        $params = ['mimeType' => $exportformat];
        $sourceurl = new moodle_url('https://www.googleapis.com/drive/v3/files/' . $fileid . '/export', $params);
        $source = $sourceurl->out(false);

        $tmp = make_request_directory();
        $downloadto = $tmp . '/' . $fileid . '.' . $format;

        $options = ['filepath' => $downloadto, 'timeout' => 15, 'followlocation' => true, 'maxredirs' => 5];
        $success = $client->download_one($source, null, $options);

        if ($success) {
            $conversion->store_destfile_from_path($downloadto);
            $conversion->set('status', conversion::STATUS_COMPLETE);
            $conversion->update();
        } else {
            $conversion->set('status', conversion::STATUS_FAILED);
        }
        // Cleanup.
        $params = [
            'fileid' => $fileid
        ];
        $service->call('delete', $params);

        return $this;
    }

    /**
     * Generate and serve the test document.
     *
     * @return  stored_file
     */
    public function serve_test_document() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $filerecord = [
            'contextid' => \context_system::instance()->id,
            'component' => 'test',
            'filearea' => 'fileconverter_googledrive',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'conversion_test.docx'
        ];

        // Get the fixture doc file content and generate and stored_file object.
        $fs = get_file_storage();
        $testdocx = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'],
                $filerecord['itemid'], $filerecord['filepath'], $filerecord['filename']);

        if (!$testdocx) {
            $fixturefile = dirname(__DIR__) . '/tests/fixtures/source.docx';
            $testdocx = $fs->create_file_from_pathname($filerecord, $fixturefile);
        }

        $conversion = new \core_files\conversion(0, (object) [
            'targetformat' => 'pdf',
        ]);

        $conversion->set_sourcefile($testdocx);
        $conversion->create();

        // Convert the doc file to pdf and send it direct to the browser.
        $this->start_document_conversion($conversion);

        $testfile = $conversion->get_destfile();
        readfile_accel($testfile, 'application/pdf', true);
    }

    /**
     * Poll an existing conversion for status update.
     *
     * @param   conversion $conversion The file to be converted
     * @return  $this;
     */
    public function poll_conversion_status(conversion $conversion) {
        return $this;
    }

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        $issuerid = get_config('fileconverter_googledrive', 'issuerid');
        if (empty($issuerid)) {
            return false;
        }

        $issuer = \core\oauth2\api::get_issuer($issuerid);
        if (empty($issuer)) {
            return false;
        }

        if (!$issuer->get('enabled')) {
            return false;
        }

        if (!$issuer->is_system_account_connected()) {
            return false;
        }

        return true;
    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to): bool {
        return isset(self::$imports[$from]) && isset(self::$exports[$to]);
    }

    /**
     * A list of the supported conversions.
     *
     * @return  string
     */
    public function get_supported_conversions() {
        return implode(', ', ['rtf', 'doc', 'xls', 'docx', 'xlsx', 'ppt', 'pptx', 'pdf', 'html']);
    }
}
