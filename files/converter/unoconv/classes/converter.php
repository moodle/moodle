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
 * Class for converting files between different file formats using unoconv.
 *
 * @package    fileconverter_unoconv
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace fileconverter_unoconv;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

use stored_file;
use \core_files\conversion;

/**
 * Class for converting files between different formats using unoconv.
 *
 * @package    fileconverter_unoconv
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class converter implements \core_files\converter_interface {

    /** No errors */
    const UNOCONVPATH_OK = 'ok';

    /** Not set */
    const UNOCONVPATH_EMPTY = 'empty';

    /** Does not exist */
    const UNOCONVPATH_DOESNOTEXIST = 'doesnotexist';

    /** Is a dir */
    const UNOCONVPATH_ISDIR = 'isdir';

    /** Not executable */
    const UNOCONVPATH_NOTEXECUTABLE = 'notexecutable';

    /** Test file missing */
    const UNOCONVPATH_NOTESTFILE = 'notestfile';

    /** Version not supported */
    const UNOCONVPATH_VERSIONNOTSUPPORTED = 'versionnotsupported';

    /** Any other error */
    const UNOCONVPATH_ERROR = 'error';

    /**
     * @var bool $requirementsmet Whether requirements have been met.
     */
    protected static $requirementsmet = null;

    /**
     * @var array $formats The list of formats supported by unoconv.
     */
    protected static $formats;

    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   conversion $conversion The file to be converted
     * @return  $this
     */
    public function start_document_conversion(\core_files\conversion $conversion) {
        global $CFG;

        if (!self::are_requirements_met()) {
            $conversion->set('status', conversion::STATUS_FAILED);
            error_log(
                "Unoconv conversion failed to verify the configuraton meets the minimum requirements. " .
                "Please check the unoconv installation configuration."
            );
            return $this;
        }

        $file = $conversion->get_sourcefile();
        $filepath = $file->get_filepath();

        // Sanity check that the conversion is supported.
        $fromformat = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
        if (!self::is_format_supported($fromformat)) {
            $conversion->set('status', conversion::STATUS_FAILED);
            error_log(
                "Unoconv conversion for '" . $filepath . "' found input '" . $fromformat . "' " .
                "file extension to convert from is not supported."
            );
            return $this;
        }

        $format = $conversion->get('targetformat');
        if (!self::is_format_supported($format)) {
            $conversion->set('status', conversion::STATUS_FAILED);
            error_log(
                "Unoconv conversion for '" . $filepath . "' found output '" . $format . "' " .
                "file extension to convert to is not supported."
            );
            return $this;
        }

        // Copy the file to the tmp dir.
        $uniqdir = make_unique_writable_directory(make_temp_directory('core_file/conversions'));
        \core_shutdown_manager::register_function('remove_dir', array($uniqdir));
        $localfilename = $file->get_id() . '.' . $fromformat;

        $filename = $uniqdir . '/' . $localfilename;
        try {
            // This function can either return false, or throw an exception so we need to handle both.
            if ($file->copy_content_to($filename) === false) {
                throw new \file_exception('storedfileproblem', 'Could not copy file contents to temp file.');
            }
        } catch (\file_exception $fe) {
            error_log(
                "Unoconv conversion for '" . $filepath . "' encountered disk permission error when copying " .
                "submitted file contents to unique temp file: '" . $filename . "'."
            );
            throw $fe;
        }

        // The temporary file to copy into.
        $newtmpfile = pathinfo($filename, PATHINFO_FILENAME) . '.' . $format;
        $newtmpfile = $uniqdir . '/' . clean_param($newtmpfile, PARAM_FILE);

        $cmd = escapeshellcmd(trim($CFG->pathtounoconv)) . ' ' .
               escapeshellarg('-f') . ' ' .
               escapeshellarg($format) . ' ' .
               escapeshellarg('-o') . ' ' .
               escapeshellarg($newtmpfile) . ' ' .
               escapeshellarg($filename);

        $output = null;
        $currentdir = getcwd();
        chdir($uniqdir);
        $result = exec($cmd, $output, $returncode);
        chdir($currentdir);
        touch($newtmpfile);

        if ($returncode != 0) {
            $conversion->set('status', conversion::STATUS_FAILED);
            error_log(
                "Unoconv conversion for '" . $filepath . "' from '" . $fromformat . "' to '" . $format . "' " .
                "was unsuccessful; returned with exit status code (" . $returncode . "). Please check the unoconv " .
                "configuration and conversion file content / format."
            );
            return $this;
        }

        if (!file_exists($newtmpfile)) {
            $conversion->set('status', conversion::STATUS_FAILED);
            error_log(
                "Unoconv conversion for '" . $filepath . "' from '" . $fromformat . "' to '" . $format . "' " .
                "was unsuccessful; the output file was not found in '" . $newtmpfile . "'. Please check the disk " .
                "permissions."
            );
            return $this;
        }

        if (filesize($newtmpfile) === 0) {
            $conversion->set('status', conversion::STATUS_FAILED);
            error_log(
                "Unoconv conversion for '" . $filepath . "' from '" . $fromformat . "' to '" . $format . "' " .
                "was unsuccessful; the output file size has 0 bytes in '" . $newtmpfile . "'. Please check the " .
                "conversion file content / format with the command: [ " . $cmd . " ]"
            );
            return $this;
        }

        $conversion
            ->store_destfile_from_path($newtmpfile)
            ->set('status', conversion::STATUS_COMPLETE)
            ->update();

        return $this;
    }

    /**
     * Poll an existing conversion for status update.
     *
     * @param   conversion $conversion The file to be converted
     * @return  $this
     */
    public function poll_conversion_status(conversion $conversion) {
        // Unoconv does not support asynchronous conversion.
        return $this;
    }

    /**
     * Generate and serve the test document.
     *
     * @return  void
     */
    public function serve_test_document() {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $format = 'pdf';

        $filerecord = [
            'contextid' => \context_system::instance()->id,
            'component' => 'test',
            'filearea' => 'fileconverter_unoconv',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => 'unoconv_test.docx'
        ];

        // Get the fixture doc file content and generate and stored_file object.
        $fs = get_file_storage();
        $testdocx = $fs->get_file($filerecord['contextid'], $filerecord['component'], $filerecord['filearea'],
                $filerecord['itemid'], $filerecord['filepath'], $filerecord['filename']);

        if (!$testdocx) {
            $fixturefile = dirname(__DIR__) . '/tests/fixtures/unoconv-source.docx';
            $testdocx = $fs->create_file_from_pathname($filerecord, $fixturefile);
        }

        $conversions = conversion::get_conversions_for_file($testdocx, $format);
        foreach ($conversions as $conversion) {
            if ($conversion->get('id')) {
                $conversion->delete();
            }
        }

        $conversion = new conversion(0, (object) [
                'sourcefileid' => $testdocx->get_id(),
                'targetformat' => $format,
            ]);
        $conversion->create();

        // Convert the doc file to the target format and send it direct to the browser.
        $this->start_document_conversion($conversion);
        do {
            sleep(1);
            $this->poll_conversion_status($conversion);
            $status = $conversion->get('status');
        } while ($status !== conversion::STATUS_COMPLETE && $status !== conversion::STATUS_FAILED);

        readfile_accel($conversion->get_destfile(), 'application/pdf', true);
    }

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * @return  bool
     */
    public static function are_requirements_met() {
        if (self::$requirementsmet === null) {
            $requirementsmet = self::test_unoconv_path()->status === self::UNOCONVPATH_OK;
            $requirementsmet = $requirementsmet && self::is_minimum_version_met();
            self::$requirementsmet = $requirementsmet;
        }

        return self::$requirementsmet;
    }

    /**
     * Whether the minimum version of unoconv has been met.
     *
     * @return  bool
     */
    protected static function is_minimum_version_met() {
        global $CFG;

        $currentversion = 0;
        $supportedversion = 0.7;
        $unoconvbin = \escapeshellarg($CFG->pathtounoconv);
        $command = "$unoconvbin --version";
        exec($command, $output);

        // If the command execution returned some output, then get the unoconv version.
        if ($output) {
            foreach ($output as $response) {
                if (preg_match('/unoconv (\\d+\\.\\d+)/', $response, $matches)) {
                    $currentversion = (float) $matches[1];
                }
            }
            if ($currentversion < $supportedversion) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the plugin is fully configured.
     *
     * @return  \stdClass
     */
    public static function test_unoconv_path() {
        global $CFG;

        $unoconvpath = $CFG->pathtounoconv;

        $ret = new \stdClass();
        $ret->status = self::UNOCONVPATH_OK;
        $ret->message = null;

        if (empty($unoconvpath)) {
            $ret->status = self::UNOCONVPATH_EMPTY;
            return $ret;
        }
        if (!file_exists($unoconvpath)) {
            $ret->status = self::UNOCONVPATH_DOESNOTEXIST;
            return $ret;
        }
        if (is_dir($unoconvpath)) {
            $ret->status = self::UNOCONVPATH_ISDIR;
            return $ret;
        }
        if (!\file_is_executable($unoconvpath)) {
            $ret->status = self::UNOCONVPATH_NOTEXECUTABLE;
            return $ret;
        }
        if (!self::is_minimum_version_met()) {
            $ret->status = self::UNOCONVPATH_VERSIONNOTSUPPORTED;
            return $ret;
        }

        return $ret;

    }

    /**
     * Whether a file conversion can be completed using this converter.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to) {
        return self::is_format_supported($from) && self::is_format_supported($to);
    }

    /**
     * Whether the specified file format is supported.
     *
     * @param   string $format Whether conversions between this format and another are supported
     * @return  bool
     */
    protected static function is_format_supported($format) {
        $formats = self::fetch_supported_formats();

        $format = trim(\core_text::strtolower($format));
        return in_array($format, $formats);
    }

    /**
     * Fetch the list of supported file formats.
     *
     * @return  array
     */
    protected static function fetch_supported_formats() {
        global $CFG;

        if (!isset(self::$formats)) {
            // Ask unoconv for it's list of supported document formats.
            $cmd = escapeshellcmd(trim($CFG->pathtounoconv)) . ' --show';
            $pipes = array();
            $pipesspec = array(2 => array('pipe', 'w'));
            $proc = proc_open($cmd, $pipesspec, $pipes);
            $programoutput = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            proc_close($proc);
            $matches = array();
            preg_match_all('/\[\.(.*)\]/', $programoutput, $matches);

            $formats = $matches[1];
            self::$formats = array_unique($formats);
        }

        return self::$formats;
    }

    /**
     * A list of the supported conversions.
     *
     * @return  string
     */
    public function get_supported_conversions() {
        return implode(', ', self::fetch_supported_formats());
    }
}
