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
 * @package    core_files
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_files;

defined('MOODLE_INTERNAL') || die();

use stored_file;

/**
 * Class for converting files between different formats using unoconv.
 *
 * @package    core_files
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class converter {

    /**
     * Get a list of enabled plugins and classes.
     *
     * @return  array List of enabled plugins
     */
    protected function get_enabled_plugins() {
        $plugins = \core\plugininfo\fileconverter::get_enabled_plugins();

        $pluginclasses = [];
        foreach ($plugins as $plugin) {
            $pluginclasses[$plugin] = \core\plugininfo\fileconverter::get_classname($plugin);
        }

        return $pluginclasses;
    }

    /**
     * Return the file_storage API.
     *
     * This allows for mocking of the file_storage API.
     *
     * @return \file_storage
     */
    protected function get_file_storage() {
        return get_file_storage();
    }

    /**
     * Start the conversion for a stored_file into a new format.
     *
     * @param   stored_file $file The file to convert
     * @param   string $format The desired target file format (file extension)
     * @param   boolean $forcerefresh If true, the file will be converted every time (not cached).
     * @return  conversion conversion object
     */
    public function start_conversion(stored_file $file, $format, $forcerefresh = false) {
        $conversions = conversion::get_conversions_for_file($file, $format);

        if ($forcerefresh || count($conversions) > 1) {
            while ($conversion = array_shift($conversions)) {
                if ($conversion->get('id')) {
                    $conversion->delete();
                }
            }
        }

        if (empty($conversions)) {
            $conversion = new conversion(0, (object) [
                'sourcefileid' => $file->get_id(),
                'targetformat' => $format,
            ]);
            $conversion->create();
        } else {
            $conversion = array_shift($conversions);
        }

        if ($conversion->get('status') !== conversion::STATUS_COMPLETE) {
            $this->poll_conversion($conversion);
        }

        return $conversion;
    }

    /**
     * Poll for updates to the supplied conversion.
     *
     * @param   conversion $conversion The conversion in progress
     * @return  $this
     */
    public function poll_conversion(conversion $conversion) {
        $format = $conversion->get('targetformat');
        $file = $conversion->get_sourcefile();

        if ($conversion->get('status') == conversion::STATUS_IN_PROGRESS) {
            // The current conversion is in progress.
            // Check for updates.
            if ($instance = $conversion->get_converter_instance()) {
                $instance->poll_conversion_status($conversion);
            } else {
                // Unable to fetch the converter instance.
                // Reset the status back to PENDING so that it may be picked up again.
                $conversion->set('status', conversion::STATUS_PENDING);
            }
            $conversion->update();
        }

        // Refresh the status.
        $status = $conversion->get('status');
        if ($status === conversion::STATUS_PENDING || $status === conversion::STATUS_FAILED) {
            // The current status is either pending or failed.
            // Attempt to pick up a new converter and convert the document.
            $from = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
            $converters = $this->get_document_converter_classes($from, $format);
            $currentconverter = $this->get_next_converter($converters, $conversion->get('converter'));

            if (!$currentconverter) {
                // No more converters available.
                $conversion->set('status', conversion::STATUS_FAILED);
                $conversion->update();
                return $this;
            }

            do {
                $conversion
                    ->set('converter', $currentconverter)
                    ->set('status', conversion::STATUS_IN_PROGRESS)
                    ->update();

                $instance = $conversion->get_converter_instance();
                $instance->start_document_conversion($conversion);
                $failed = $conversion->get('status') === conversion::STATUS_FAILED;
                $currentconverter = $this->get_next_converter($converters, $currentconverter);
            } while ($failed && $currentconverter);

            $conversion->update();
        }

        return $this;
    }

    /**
     * Fetch the next converter to try.
     *
     * @param   array $converters The list of converters to try
     * @param   string|null $currentconverter The converter currently in use
     * @return  string|false Name of next converter if present
     */
    protected function get_next_converter($converters, $currentconverter = null) {
        if ($currentconverter) {
            $keys = array_keys($converters, $currentconverter);
            $key = $keys[0];
            if (isset($converters[$key + 1])) {
                return $converters[$key + 1];
            } else {
                return false;
            }
        } else if (!empty($converters)) {
            return $converters[0];
        } else {
            return false;
        }
    }

    /**
     * Fetch the class for the preferred document converter.
     *
     * @param   string $from The source target file (file extension)
     * @param   string $to The desired target file format (file extension)
     * @return  string The class for document conversion
     */
    protected function get_document_converter_classes($from, $to) {
        $classes = [];

        $converters = $this->get_enabled_plugins();
        foreach ($converters as $plugin => $classname) {
            if (!class_exists($classname)) {
                continue;
            }

            if (!$classname::are_requirements_met()) {
                continue;
            }

            if ($classname::supports($from, $to)) {
                $classes[] = $classname;
            }
        }

        return $classes;
    }

    /**
     * Check whether document conversion is supported for this file and target format.
     *
     * @param   stored_file $file The file to convert
     * @param   string $to The desired target file format (file extension)
     * @return  bool Whether the target type can be converted
     */
    public function can_convert_storedfile_to(stored_file $file, $to) {
        if ($file->is_directory()) {
            // Directories cannot be converted.
            return false;
        }

        if (!$file->get_filesize()) {
            // Empty files cannot be converted.
            return false;
        }

        $from = pathinfo($file->get_filename(), PATHINFO_EXTENSION);
        if (!$from) {
            // No file extension could be found. Unable to determine converter.
            return false;
        }

        return $this->can_convert_format_to($from, $to);
    }

    /**
     * Check whether document conversion is supported for this file and target format.
     *
     * @param   string $from The source target file (file extension)
     * @param   string $to The desired target file format (file extension)
     * @return  bool Whether the target type can be converted
     */
    public function can_convert_format_to($from, $to) {
        return !empty($this->get_document_converter_classes($from, $to));
    }

}
