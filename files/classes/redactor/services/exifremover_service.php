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

namespace core_files\redactor\services;

use admin_setting_configcheckbox;
use admin_setting_configexecutable;
use admin_setting_configselect;
use admin_setting_configtextarea;
use admin_setting_heading;
use core\exception\moodle_exception;
use core\output\html_writer;

/**
 * Remove EXIF data from supported image files using PHP GD, or ExifTool if it is configured.
 *
 * The PHP GD stripping has minimal configuration and removes all EXIF data.
 * More stripping is made available when using ExifTool.
 *
 * @package   core_files
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exifremover_service extends service implements file_redactor_service_interface {
    /** @var array REMOVE_TAGS Tags to remove and their corresponding values. */
    const REMOVE_TAGS = [
        "gps" => '"-gps*="',
        "all" => "-all=",
    ];

    /** @var string DEFAULT_REMOVE_TAGS Default tags that will be removed. */
    const DEFAULT_REMOVE_TAGS = "gps";

    /** @var string DEFAULT_MIMETYPE Default MIME type for images. */
    const DEFAULT_MIMETYPE = "image/jpeg";

    /**
     * PRESERVE_TAGS Tag to preserve when stripping EXIF data.
     *
     * To add a new tag, add the tag with space as a separator.
     * For example, if the model tag is preserved, then the value is "-Orientation -Model".
     *
     * @var string
    */
    const PRESERVE_TAGS = "-Orientation";

    /** @var int DEFAULT_JPEG_COMPRESSION Default JPEG compression quality. */
    const DEFAULT_JPEG_COMPRESSION = 90;

    /** @var bool $useexiftool Flag indicating whether to use ExifTool. */
    private bool $useexiftool = false;

    /**
     * Initialise the EXIF remover service.
     */
    public function __construct() {
        // To decide whether to use ExifTool or PHP GD, check the ExifTool path.
        if (!empty($this->get_exiftool_path())) {
            $this->useexiftool = true;
        }
    }

    #[\Override]
    public function redact_file_by_path(
        string $mimetype,
        string $filepath,
    ): ?string {
        if (!$this->is_mimetype_supported($mimetype)) {
            return null;
        }

        if ($this->useexiftool) {
            // Use the ExifTool executable to remove the desired EXIF tags.
            return $this->execute_exiftool($filepath);
        } else {
            // Use PHP GD lib to remove all EXIF tags.
            return $this->execute_gd($filepath);
        }
    }

    #[\Override]
    public function redact_file_by_content(
        string $mimetype,
        string $filecontent,
    ): ?string {
        if (!$this->is_mimetype_supported($mimetype)) {
            return null;
        }

        if ($this->useexiftool) {
            // Use the ExifTool executable to remove the desired EXIF tags.
            return $this->execute_exiftool_on_content($filecontent);
        } else {
            // Use PHP GD lib to remove all EXIF tags.
            return $this->execute_gd_on_content($filecontent);
        }
    }

    /**
     * Executes ExifTool to remove metadata from the original file.
     *
     * @param string $sourcefile The file path of the file to redact
     * @return string The destination path of the recreated content
     * @throws moodle_exception If the ExifTool process fails or the destination file is not created.
     */
    private function execute_exiftool(string $sourcefile): string {
        $destinationfile = make_request_directory() . '/' . basename($sourcefile);

        // Prepare the ExifTool command.
        $command = $this->get_exiftool_command($sourcefile, $destinationfile);

        // Run the command.
        exec($command, $output, $resultcode);

        // If the return code was not zero or the destination file was not successfully created.
        if ($resultcode !== 0 || !file_exists($destinationfile)) {
            throw new moodle_exception(
                errorcode: 'redactor:exifremover:failedprocessexiftool',
                module: 'core_files',
                a: get_class($this),
                debuginfo: implode($output),
            );
        }

        return $destinationfile;
    }

    /**
     * Executes ExifTool to remove metadata from the original file content.
     *
     * @param string $filecontent The file content to redact.
     * @return string The redacted updated content
     * @throws moodle_exception If the ExifTool process fails or the destination file is not created.
     */
    private function execute_exiftool_on_content(string $filecontent): string {
        $sourcefile = make_request_directory() . '/input';
        file_put_contents($sourcefile, $filecontent);

        $destinationfile = $this->execute_exiftool($sourcefile);
        return file_get_contents($destinationfile);
    }

    /**
     * Executes GD library to remove metadata from the original file.
     *
     * @param string $sourcefile The source file to redact.
     * @return string The destination path of the recreated content
     * @throws moodle_exception If the image data is not successfully recreated.
     */
    private function execute_gd(string $sourcefile): string {
        $filecontent = file_get_contents($sourcefile);
        $destinationfile = $this->recreate_image_gd($filecontent);
        if (!$destinationfile) {
            throw new moodle_exception(
                errorcode: 'redactor:exifremover:failedprocessgd',
                module: 'core_files',
                a: get_class($this),
            );
        }

        return $destinationfile;
    }

    /**
     * Executes GD library to remove metadata from the original file.
     *
     * @param string $filecontent The source file content to redact.
     * @return string The redacted file content
     * @throws moodle_exception If the image data is not successfully recreated.
     */
    private function execute_gd_on_content(string $filecontent): string {
        $destinationfile = $this->recreate_image_gd($filecontent);
        if (!$destinationfile) {
            throw new moodle_exception(
                errorcode: 'redactor:exifremover:failedprocessgd',
                module: 'core_files',
                a: get_class($this),
            );
        }

        return file_get_contents($destinationfile);
    }

    /**
     * Gets the ExifTool command to strip the file of EXIF data.
     *
     * @param string $source The source path of the file.
     * @param string $destination The destination path of the file.
     * @return string The command to use to remove EXIF data from the file.
     */
    private function get_exiftool_command(string $source, string $destination): string {
        $exiftoolexec = escapeshellarg($this->get_exiftool_path());
        $removetags = $this->get_remove_tags();
        $tempdestination = escapeshellarg($destination);
        $tempsource = escapeshellarg($source);
        $preservetagsoption = "-tagsfromfile @ " . self::PRESERVE_TAGS;
        $command = "$exiftoolexec $removetags $preservetagsoption -o $tempdestination -- $tempsource";
        $command .= " 2> /dev/null"; // Do not output any errors.
        return $command;
    }

    /**
     * Retrieves the remove tag options based on configuration.
     *
     * @return string The remove tag options.
     */
    private function get_remove_tags(): string {
        $removetags = get_config('core', 'file_redactor_exifremoverremovetags');
        // If the remove tags value is empty or not empty but does not exist in the array, then set the default.
        if (!$removetags || ($removetags && !array_key_exists($removetags, self::REMOVE_TAGS))) {
            $removetags = self::DEFAULT_REMOVE_TAGS;
        }
        return self::REMOVE_TAGS[$removetags];
    }

    /**
     * Retrieves the path to the ExifTool executable.
     *
     * @return string The path to the ExifTool executable.
     */
    private function get_exiftool_path(): string {
        $toolpathconfig = get_config('core', 'file_redactor_exifremovertoolpath');
        if (!empty($toolpathconfig) && is_executable($toolpathconfig)) {
            return $toolpathconfig;
        }
        return '';
    }

    /**
     * Recreate the image using PHP GD library to strip all EXIF data.
     *
     * @param string $content The source file content
     * @return null|string The path to the recreated image, or null on failure.
     */
    private function recreate_image_gd(
        string $content,
    ): ?string {
        // Fetch the image information for this image.
        $imageinfo = @getimagesizefromstring($content);
        if (empty($imageinfo)) {
            return null;
        }
        // Create a new image from the file.
        $image = @imagecreatefromstring($content);

        $destinationfile = make_request_directory() . '/output';

        // Capture the image as a string object, rather than straight to file.
        $result = imagejpeg(
            image: $image,
            file: $destinationfile,
            quality: self::DEFAULT_JPEG_COMPRESSION,
        );

        imagedestroy($image);

        if ($result) {
            return $destinationfile;
        }

        return null;
    }

    /**
     * Returns true if the service is enabled, and false if it is not.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return (bool) get_config('core', 'file_redactor_exifremoverenabled');
    }

    /**
     * Determines whether a certain mime-type is supported by the service.
     * It will return true if the mime-type is supported, and false if it is not.
     *
     * @param string $mimetype The mime type of file.
     * @return bool
     */
    public function is_mimetype_supported(string $mimetype): bool {
        if ($mimetype === self::DEFAULT_MIMETYPE) {
            return true;
        }

        if ($this->useexiftool) {
            // Get the supported MIME types from the config if using ExifTool.
            $supportedmimetypesconfig = get_config('core', 'file_redactor_exifremovermimetype');
            $supportedmimetypes = array_filter(array_map('trim', explode("\n",  $supportedmimetypesconfig)));
            return in_array($mimetype, $supportedmimetypes) ?? false;
        }

        return false;
    }

    /**
     * Adds settings to the provided admin settings page.
     *
     * @param \admin_settingpage $settings The admin settings page to which settings are added.
     */
    public static function add_settings(\admin_settingpage $settings): void {
        global $OUTPUT;

        // Enabled for a fresh install, disabled for an upgrade.
        $defaultenabled = 1;

        if (empty(get_config('core', 'file_redactor_exifremoverenabled'))) {
            if (PHPUNIT_TEST || !during_initial_install()) {
                $defaultenabled = 0;
            }
        }

        $icon = $OUTPUT->pix_icon('i/externallink', get_string('opensinnewwindow'));
        $a = (object) [
            'link' => html_writer::link(
                url: 'https://exiftool.sourceforge.net/install.html',
                text: "https://exiftool.sourceforge.net/install.html $icon",
                attributes: ['role' => 'opener', 'rel' => 'noreferrer', 'target' => '_blank'],
            ),
        ];

        $settings->add(
            new admin_setting_configcheckbox(
                name: 'file_redactor_exifremoverenabled',
                visiblename: get_string('redactor:exifremover:enabled', 'core_files'),
                description: get_string('redactor:exifremover:enabled_desc', 'core_files', $a),
                defaultsetting: $defaultenabled,
            ),
        );

        $settings->add(
            new admin_setting_heading(
                name: 'exifremoverheading',
                heading: get_string('redactor:exifremover:heading', 'core_files'),
                information: '',
            )
        );

        $settings->add(
            new admin_setting_configexecutable(
                name: 'file_redactor_exifremovertoolpath',
                visiblename: get_string('redactor:exifremover:toolpath', 'core_files'),
                description: get_string('redactor:exifremover:toolpath_desc', 'core_files'),
                defaultdirectory: '',
            )
        );

        foreach (array_keys(self::REMOVE_TAGS) as $key) {
            $removedtagchoices[$key] = get_string("redactor:exifremover:tag:$key", 'core_files');
        }
        $settings->add(
            new admin_setting_configselect(
                name: 'file_redactor_exifremoverremovetags',
                visiblename: get_string('redactor:exifremover:removetags', 'core_files'),
                description: get_string('redactor:exifremover:removetags_desc', 'core_files'),
                defaultsetting: self::DEFAULT_REMOVE_TAGS,
                choices: $removedtagchoices,
            ),
        );

        $mimetypedefault = <<<EOF
        image/jpeg
        image/tiff
        EOF;
        $settings->add(
            new admin_setting_configtextarea(
                name: 'file_redactor_exifremovermimetype',
                visiblename: get_string('redactor:exifremover:mimetype', 'core_files'),
                description: get_string('redactor:exifremover:mimetype_desc', 'core_files'),
                defaultsetting: $mimetypedefault,
            ),
        );
    }
}
