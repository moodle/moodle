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

namespace core\fileredact;

use stored_file;

/**
 * Fileredact manager.
 *
 * Manages and executes redaction services.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** @var array Holds an array of error messages. */
    private $errors = [];

    /**
     * Constructor.
     *
     * @param stored_file $filerecord The file record as a stdClass object, or null if not available.
     */
    public function __construct(
        /** @var stored_file $filerecord File record. */
        private readonly stored_file $filerecord
    ) {
    }

    /**
     * Executes redaction services.
     */
    public function execute(): void {
        // Get the file redact services.
        $services = $this->get_services();
        foreach ($services as $serviceclass) {
            try {
                if (class_exists($serviceclass)) {
                    $service = new $serviceclass($this->filerecord);
                    // For the given service, execute them if they are enabled, and the given mime type is supported.
                    if ($service->is_enabled() && $service->is_mimetype_supported($this->filerecord->get_mimetype())) {
                        $service->execute();
                    }
                }
            } catch (\Throwable $e) {
                $this->errors[] = $e;
            }
        }
    }

    /**
     * Returns a list of applicable redaction services.
     *
     * @return string[] return list of services.
     */
    protected function get_services(): array {
        global $CFG;
        $servicesdir = "{$CFG->libdir}/classes/fileredact/services/";
        $servicefiles = glob("{$servicesdir}*_service.php");
        $services = [];
        foreach ($servicefiles as $servicefile) {
            $servicename = basename($servicefile, '_service.php');
            $serviceclass = "\\core\\fileredact\\services\\{$servicename}_service";
            $services[] = $serviceclass;
        }
        return $services;
    }

    /**
     * Retrieves an array of error messages.
     *
     * @return array An array of error messages.
     */
    public function get_errors(): array {
        return $this->errors;
    }
}
