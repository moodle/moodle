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
 * ClamAV antivirus integration.
 *
 * @package    antivirus_clamav
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace antivirus_clamav;

defined('MOODLE_INTERNAL') || die();

/**
 * Class implemeting ClamAV antivirus.
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class scanner extends \core\antivirus\scanner {
    /**
     * Are the necessary antivirus settings configured?
     *
     * @return bool True if all necessary config settings been entered
     */
    public function is_configured() {
        return !empty($this->config->pathtoclam);
    }
    /**
     * Scan file, throws exception in case of infected file.
     *
     * Please note that the scanning engine must be able to access the file,
     * permissions of the file are not modified here!
     *
     * @param string $file Full path to the file.
     * @param string $filename Name of the file (could be different from physical file if temp file is used).
     * @param bool $deleteinfected whether infected file needs to be deleted.
     * @throws moodle_exception If file is infected.
     * @return void
     */
    public function scan_file($file, $filename, $deleteinfected) {
        global $CFG;

        if (!is_readable($file)) {
            // This should not happen.
            return;
        }

        $this->config->pathtoclam = trim($this->config->pathtoclam);

        if (!file_exists($this->config->pathtoclam) or !is_executable($this->config->pathtoclam)) {
            // Misconfigured clam, notify admins.
            $notice = get_string('invalidpathtoclam', 'antivirus_clamav', $this->config->pathtoclam);
            $this->message_admins($notice);
            return;
        }

        $clamparam = ' --stdout ';
        // If we are dealing with clamdscan, clamd is likely run as a different user
        // that might not have permissions to access your file.
        // To make clamdscan work, we use --fdpass parameter that passes the file
        // descriptor permissions to clamd, which allows it to scan given file
        // irrespective of directory and file permissions.
        if (basename($this->config->pathtoclam) == 'clamdscan') {
            $clamparam .= '--fdpass ';
        }
        // Execute scan.
        $cmd = escapeshellcmd($this->config->pathtoclam).$clamparam.escapeshellarg($file);
        exec($cmd, $output, $return);

        if ($return == 0) {
            // Perfect, no problem found, file is clean.
            return;
        } else if ($return == 1) {
            // Infection found.
            if ($deleteinfected) {
                unlink($file);
            }
            throw new \core\antivirus\scanner_exception('virusfounduser', '', array('filename' => $filename));
        } else {
            // Unknown problem.
            $notice = get_string('clamfailed', 'antivirus_clamav', $this->get_clam_error_code($return));
            $notice .= "\n\n". implode("\n", $output);
            $this->message_admins($notice);
            if ($this->config->clamfailureonupload === 'actlikevirus') {
                if ($deleteinfected) {
                    unlink($file);
                }
                throw new \core\antivirus\scanner_exception('virusfounduser', '', array('filename' => $filename));
            } else {
                return;
            }
        }
    }

    /**
     * Returns the string equivalent of a numeric clam error code
     *
     * @param int $returncode The numeric error code in question.
     * @return string The definition of the error code
     */
    private function get_clam_error_code($returncode) {
        $returncodes = array();
        $returncodes[0] = 'No virus found.';
        $returncodes[1] = 'Virus(es) found.';
        $returncodes[2] = ' An error occured'; // Specific to clamdscan.
        // All after here are specific to clamscan.
        $returncodes[40] = 'Unknown option passed.';
        $returncodes[50] = 'Database initialization error.';
        $returncodes[52] = 'Not supported file type.';
        $returncodes[53] = 'Can\'t open directory.';
        $returncodes[54] = 'Can\'t open file. (ofm)';
        $returncodes[55] = 'Error reading file. (ofm)';
        $returncodes[56] = 'Can\'t stat input file / directory.';
        $returncodes[57] = 'Can\'t get absolute path name of current working directory.';
        $returncodes[58] = 'I/O error, please check your filesystem.';
        $returncodes[59] = 'Can\'t get information about current user from /etc/passwd.';
        $returncodes[60] = 'Can\'t get information about user \'clamav\' (default name) from /etc/passwd.';
        $returncodes[61] = 'Can\'t fork.';
        $returncodes[63] = 'Can\'t create temporary files/directories (check permissions).';
        $returncodes[64] = 'Can\'t write to temporary directory (please specify another one).';
        $returncodes[70] = 'Can\'t allocate and clear memory (calloc).';
        $returncodes[71] = 'Can\'t allocate memory (malloc).';
        if (isset($returncodes[$returncode])) {
            return $returncodes[$returncode];
        }
        return get_string('unknownerror', 'antivirus_clamav');
    }
}
