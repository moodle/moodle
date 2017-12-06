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

/** Default socket timeout */
define('ANTIVIRUS_CLAMAV_SOCKET_TIMEOUT', 10);

/**
 * Class implementing ClamAV antivirus.
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
        if ($this->get_config('runningmethod') === 'commandline') {
            return (bool)$this->get_config('pathtoclam');
        } else if ($this->get_config('runningmethod') === 'unixsocket') {
            return (bool)$this->get_config('pathtounixsocket');
        }
        return false;
    }

    /**
     * Scan file.
     *
     * This method is normally called from antivirus manager (\core\antivirus\manager::scan_file).
     *
     * @param string $file Full path to the file.
     * @param string $filename Name of the file (could be different from physical file if temp file is used).
     * @return int Scanning result constant.
     */
    public function scan_file($file, $filename) {
        if (!is_readable($file)) {
            // This should not happen.
            debugging('File is not readable.');
            return self::SCAN_RESULT_ERROR;
        }

        // Execute the scan using preferable method.
        $method = 'scan_file_execute_' . $this->get_config('runningmethod');
        $return = $this->$method($file);

        if ($return === self::SCAN_RESULT_ERROR) {
            $this->message_admins($this->get_scanning_notice());
            // If plugin settings require us to act like virus on any error,
            // return SCAN_RESULT_FOUND result.
            if ($this->get_config('clamfailureonupload') === 'actlikevirus') {
                return self::SCAN_RESULT_FOUND;
            }
        }
        return $return;
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

    /**
     * Scan file using command line utility.
     *
     * @param string $file Full path to the file.
     * @return int Scanning result constant.
     */
    public function scan_file_execute_commandline($file) {
        $pathtoclam = trim($this->get_config('pathtoclam'));

        if (!file_exists($pathtoclam) or !is_executable($pathtoclam)) {
            // Misconfigured clam, notify admins.
            $notice = get_string('invalidpathtoclam', 'antivirus_clamav', $pathtoclam);
            $this->set_scanning_notice($notice);
            return self::SCAN_RESULT_ERROR;
        }

        $clamparam = ' --stdout ';
        // If we are dealing with clamdscan, clamd is likely run as a different user
        // that might not have permissions to access your file.
        // To make clamdscan work, we use --fdpass parameter that passes the file
        // descriptor permissions to clamd, which allows it to scan given file
        // irrespective of directory and file permissions.
        if (basename($pathtoclam) == 'clamdscan') {
            $clamparam .= '--fdpass ';
        }
        // Execute scan.
        $cmd = escapeshellcmd($pathtoclam).$clamparam.escapeshellarg($file);
        exec($cmd, $output, $return);
        // Return variable will contain execution return code. It will be 0 if no virus is found,
        // 1 if virus is found, and 2 or above for the error. Return codes 0 and 1 correspond to
        // SCAN_RESULT_OK and SCAN_RESULT_FOUND constants, so we return them as it is.
        // If there is an error, it gets stored as scanning notice and function
        // returns SCAN_RESULT_ERROR.
        if ($return > self::SCAN_RESULT_FOUND) {
            $notice = get_string('clamfailed', 'antivirus_clamav', $this->get_clam_error_code($return));
            $notice .= "\n\n". implode("\n", $output);
            $this->set_scanning_notice($notice);
            return self::SCAN_RESULT_ERROR;
        }

        return (int)$return;
    }

    /**
     * Scan file using Unix domain sockets.
     *
     * @param string $file Full path to the file.
     * @return int Scanning result constant.
     */
    public function scan_file_execute_unixsocket($file) {
        $socket = stream_socket_client('unix://' . $this->get_config('pathtounixsocket'),
                $errno, $errstr, ANTIVIRUS_CLAMAV_SOCKET_TIMEOUT);
        if (!$socket) {
            // Can't open socket for some reason, notify admins.
            $notice = get_string('errorcantopensocket', 'antivirus_clamav', "$errstr ($errno)");
            $this->set_scanning_notice($notice);
            return self::SCAN_RESULT_ERROR;
        } else {
            // Execute scanning. We are running SCAN command and passing file as an argument,
            // it is the fastest option, but clamav user need to be able to access it, so
            // we give group read permissions first and assume 'clamav' user is in web server
            // group (in Debian the default webserver group is 'www-data').
            // Using 'n' as command prefix is forcing clamav to only treat \n as newline delimeter,
            // this is to avoid unexpected newline characters on different systems.
            $perms = fileperms($file);
            chmod($file, 0640);
            fwrite($socket, "nSCAN ".$file."\n");
            $output = stream_get_line($socket, 4096);
            fclose($socket);
            // After scanning we revert permissions to initial ones.
            chmod($file, $perms);
            // Parse the output.
            $splitoutput = explode(': ', $output);
            $message = trim($splitoutput[1]);
            if ($message === 'OK') {
                return self::SCAN_RESULT_OK;
            } else {
                $parts = explode(' ', $message);
                $status = array_pop($parts);
                if ($status === 'FOUND') {
                    return self::SCAN_RESULT_FOUND;
                } else {
                    $notice = get_string('clamfailed', 'antivirus_clamav', $this->get_clam_error_code(2));
                    $notice .= "\n\n" . $output;
                    $this->set_scanning_notice($notice);
                    return self::SCAN_RESULT_ERROR;
                }
            }
        }
    }
}
