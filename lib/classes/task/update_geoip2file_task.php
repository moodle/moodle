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

namespace core\task;

use core\http_client;
use moodle_exception;
use PharData;

/**
 * Simple task to update the GeoIP database file.
 *
 * @package     core
 * @author      Trisha Milan <trishamilan@catalyst-au.net>
 * @copyright   Monash University 2024
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_geoip2file_task extends scheduled_task {

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskupdategeoip2file', 'admin');
    }

    /**
     * Execute the task to update the GeoIP2 database file.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws moodle_exception
     */
    public function execute(): void {
        global $CFG;

        if (!$CFG->geoipmaxmindaccid || !$CFG->geoipmaxmindlicensekey) {
            mtrace("MaxMind account information is incomplete. Please configure the account ID and license key.");
            return;
        }

        // Setup base directory path and permissions.
        $geoip2file = $CFG->geoip2file;
        $geoipdirectory = dirname($geoip2file);
        if (!check_dir_exists($geoipdirectory) && !mkdir($geoipdirectory, $CFG->directorypermissions, true)) {
            throw new moodle_exception("Cannot create output directory $geoipdirectory");
        }

        $geoippermalink = 'https://download.maxmind.com/geoip/databases/' . $CFG->geoipdbedition . '/download';

        $client = new http_client(['auth' => [$CFG->geoipmaxmindaccid, $CFG->geoipmaxmindlicensekey]]);
        $response = $client->head($geoippermalink, ['query' => ['suffix' => 'tar.gz']]);
        $headers = $response->getHeaders();
        $lastmodified = strtotime($headers['Last-Modified'][0]);
        if (!$this->is_update_needed($geoip2file, $lastmodified)) {
            mtrace("No update needed. The GeoIP database is up to date.");
            return;
        }

        // Define path for downloading the GeoIP2 archive.
        $archivefilename = 'GeoIP-City.tar.gz';
        $tempdirectory = make_request_directory(true);
        $geoipdownloadpath = $tempdirectory . '/' . $archivefilename;

        mtrace("Downloading $CFG->geoipdbedition database from MaxMind......");
        $response = $client->request('GET', $geoippermalink, [
            'query' => ['suffix' => 'tar.gz'],
            'sink' => $geoipdownloadpath,
        ]);
        if ($response->getStatusCode() != 200) {
            throw new moodle_exception("Error downloading file.");
        }

        mtrace("File downloaded successfully to $geoipdownloadpath");
        mtrace("Verifying checksum......");

        // Get the latest checksum from MaxMind.
        $checksumcontent = $client->get($geoippermalink, ['query' => ['suffix' => 'tar.gz.sha256']])->getBody()->getContents();
        list($checksum) = explode(' ', $checksumcontent);
        if (!$this->verify_checksum($checksum, $geoipdownloadpath)) {
            throw new moodle_exception("Checksum verification failed.");
        }

        mtrace("Checksum verified successfully.");
        if ($this->update_geoip2file($geoipdownloadpath, $tempdirectory, $geoip2file)) {
            // Store the last seen timestamp.
            set_config('geoip_last_seen_timestamp', $lastmodified);
            mtrace("GeoIP database update successful!");
        } else {
            throw new moodle_exception("GeoIP database update failed.");
        }
    }

    /**
     * Determines if an update is needed for the GeoIP2 file based on the last modified date.
     *
     * @param string $geoip2file The path to the GeoIP2 file that needs to be checked for updates.
     * @param string $lastmodified The last modified date to be compared against the stored last seen timestamp.
     * @return bool
     */
    private function is_update_needed(string $geoip2file, string $lastmodified): bool {
        return !file_exists($geoip2file) || $lastmodified !== get_config('core', 'geoip_last_seen_timestamp');
    }

    /**
     * Verify the checksum of the downloaded file against an expected checksum.
     *
     * @param string $expectedchecksum The checksum expected for the file.
     * @param string $geoipdownloadpath The path where the downloaded geoip archive is located.
     * @return bool Returns true if the checksums match, returns false otherwise.
     */
    private function verify_checksum(string $expectedchecksum, string $geoipdownloadpath): bool {
        $actualchecksum = hash_file('sha256', $geoipdownloadpath);
        return $expectedchecksum === $actualchecksum;
    }

    /**
     * Extract the archive and update the GeoIP2 database file.
     *
     * @param string $archivepath The path to the archive file that needs to be extracted.
     * @param string $targetdirectory Directory where the archive contents will be extracted.
     * @param string $geoip2file The path to move the extracted GeoIP2 file.
     * @return bool Returns true if the file was successfully extracted and moved to the specified location,
     *              false if any part of the process fails.
     */
    private function update_geoip2file(string $archivepath, string $targetdirectory, string $geoip2file): bool {
        $archive = new PharData($archivepath);
        $archivename = $archive->getFilename();

        mtrace("Extracting file......");
        $archive->extractTo($targetdirectory);
        $sourcefolder = $targetdirectory . '/' . $archivename;

        // Find the mmdb file.
        $mmdbfiles = glob($sourcefolder . '/*.mmdb');
        if (count($mmdbfiles) > 1) {
            throw new moodle_exception("Multiple .mmdb files found in the extracted folder.");
        } else if (count($mmdbfiles) === 0) {
            throw new moodle_exception("GeoIP file does not exist.");
        }

        // Backup existing GeoIP file before attempting to update.
        $geoip2filename = basename($geoip2file);
        $backuppath = $targetdirectory . '/' . 'backup_' . $geoip2filename;
        if (file_exists($geoip2file)) {
            if (!rename($geoip2file, $backuppath)) {
                mtrace("Failed to create a backup of the existing GeoIP database.");
            }
            mtrace("Temporary backup of existing GeoIP file has been created.");
        }

        mtrace("Moving {$mmdbfiles[0]} into $geoip2file");
        if (!copy($mmdbfiles[0], $geoip2file)) {
            mtrace("Failed to update $geoip2filename.");
            // Attempt to restore the original file from the backup.
            if (file_exists($backuppath)) {
                mtrace("Attempting to restore from backup.");
                if (!copy($backuppath, $geoip2file)) {
                    throw new moodle_exception("Failed to restore the GeoIP database from backup.");
                } else {
                    mtrace("The GeoIP database has been restored from the backup successfully.");
                }
            }
            return false;
        }
        mtrace("$geoip2filename updated successfully.");
        return true;
    }
}
