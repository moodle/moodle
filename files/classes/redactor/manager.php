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

namespace core_files\redactor;

/**
 * Fileredact manager.
 *
 * Manages and executes redaction services.
 *
 * @package   core_files
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /** @var array Holds an array of error messages. */
    private array $errors = [];

    /**
     * Redacts the given file.
     *
     * @param string $mimetype The mime-type of the file
     * @param string $filepath The path to the file to redact
     * @return string|null The path to the redacted file or null if no redaction services are available.
     */
    public function redact_file(
        string $mimetype,
        string $filepath,
    ): ?string {
        // Get the file redact services.
        $services = $this->get_service_classnames();
        $serviceinstances = array_filter(
            array_map(fn($serviceclass) => new $serviceclass(), $services),
            fn($service) => $service->is_enabled() && $service->is_mimetype_supported($mimetype)
        );

        if (count($serviceinstances) === 0) {
            return null;
        }

        foreach ($serviceinstances as $servicename => $service) {
            try {
                return $service->redact_file_by_path($mimetype, $filepath);
            } catch (\Throwable $e) {
                $this->errors[] = $e;
            }
        }

        return null;
    }

    /**
     * Redacts the given file content.
     *
     * @param string $mimetype The mime-type of the file
     * @param string $filecontent The file content to redact
     * @return string|null The content of the redacted file
     */
    public function redact_file_content(
        string $mimetype,
        string $filecontent,
    ): ?string {
        // Get the file redact services.
        $services = $this->get_file_services_for_mimetype($mimetype);

        foreach ($services as $servicename => $service) {
            try {
                return $service->redact_file_by_content($mimetype, $filecontent);
            } catch (\Throwable $e) {
                $this->errors[] = $e;
            }
        }

        return null;
    }

    /**
     * Returns a list of applicable redaction services.
     *
     * @return string[] list of service classnames.
     */
    public function get_service_classnames(): array {
        global $CFG;
        $servicesdir = "{$CFG->dirroot}/files/classes/redactor/services/";
        $servicefiles = glob("{$servicesdir}*_service.php");
        $services = [];
        foreach ($servicefiles as $servicefile) {
            $servicename = basename($servicefile, '_service.php');
            $serviceclass = services::class . "\\{$servicename}_service";

            if (!is_a($serviceclass, services\service::class, true)) {
                continue;
            }
            $services[$servicename] = $serviceclass;
        }
        return $services;
    }

    /**
     * Returns a list of file redaction services that support the given mime-type.
     *
     * @param string $mimetype The mime-type to filter by
     * @return services\file_redactor_service_interface[] An array of file redaction services that support the given mime-type.
     */
    protected function get_file_services_for_mimetype(string $mimetype): array {
        return array_filter(array_map(
            function(string $serviceclass) use ($mimetype): ?services\file_redactor_service_interface {
                if (!is_a($serviceclass, services\file_redactor_service_interface::class, true)) {
                    return null;
                }

                $service = new $serviceclass();
                if ($service->is_mimetype_supported($mimetype)) {
                    return $service;
                }

                return null;
            },
            $this->get_service_classnames(),
        ), fn ($service) => $service !== null);
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
