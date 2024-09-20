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

namespace core\fileredact\services;

use stored_file;

/**
 * Remove EXIF data from supported image files using PHP GD, or ExifTool if it is configured.
 *
 * The PHP GD stripping has minimal configuration and removes all EXIF data.
 * More stripping is made available when using ExifTool.
 *
 * @package   core
 * @copyright Meirza <meirza.arson@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exifremover_service extends service {

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
     * Class constructor.
     *
     * @param stored_file $storedfile The file record.
     */
    public function __construct(
        /** @var stored_file The file record. */
        private readonly stored_file $storedfile,
    ) {
        parent::__construct($storedfile);

        // To decide whether to use ExifTool or PHP GD, check the ExifTool path.
        if (!empty($this->get_exiftool_path())) {
            $this->useexiftool = true;
        }
    }

    /**
     * Performs redaction on the specified file.
     */
    public function execute(): void {
        if ($this->useexiftool) {
            // Use the ExifTool executable to remove the desired EXIF tags.
            $this->execute_exiftool();
        } else {
            // Use PHP GD lib to remove all EXIF tags.
            $this->execute_gd();
        }
    }

    /**
     * Executes ExifTool to remove metadata from the original file.
     *
     * @throws \moodle_exception If the ExifTool process fails or the destination file is not created.
     */
    private function execute_exiftool(): void {
        $tmpfilepath = make_request_directory();
        $filerecordname = $this->clean_filename($this->storedfile->get_filename());
        $neworiginalfile = $tmpfilepath . DIRECTORY_SEPARATOR . 'new_' . $filerecordname;
        $destinationfile = $tmpfilepath . DIRECTORY_SEPARATOR . $filerecordname;

        // Copy the original file to a new file.
        try {
            $this->storedfile->copy_content_to($neworiginalfile);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        // Prepare the ExifTool command.
        $command = $this->get_exiftool_command($neworiginalfile, $destinationfile);
        // Run the command.
        exec($command, $output, $resultcode);
        // If the return code was not zero or the destination file was not successfully created.
        if ($resultcode !== 0 || !file_exists($destinationfile)) {
            throw new \moodle_exception(
                errorcode: 'fileredact:exifremover:failedprocessexiftool',
                module: 'core_files',
                a: get_class($this),
                debuginfo: implode($output),
            );
        }
        // Replacing the EXIF processed file to the original file.
        $this->persist_redacted_file(file_get_contents($destinationfile));
    }

    /**
     * Executes GD library to remove metadata from the original file.
     */
    private function execute_gd(): void {
        $imagedata = $this->recreate_image_gd();
        if (!$imagedata) {
            throw new \moodle_exception(
                errorcode: 'fileredact:exifremover:failedprocessgd',
                module: 'core_files',
                a: get_class($this),
            );
        }
        // Put the image string object data to the original file.
        $this->persist_redacted_file($imagedata);
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
        $removetags = get_config('core_fileredact', 'exifremoverremovetags');
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
        $toolpathconfig = get_config('core_fileredact', 'exifremovertoolpath');
        if (!empty($toolpathconfig) && is_executable($toolpathconfig)) {
            return $toolpathconfig;
        }
        return '';
    }

    /**
     * Recreate the image using PHP GD library to strip all EXIF data.
     *
     * @return string|false The recreated image data as a string if successful, false otherwise.
     */
    private function recreate_image_gd(): string|false {
        $content = $this->storedfile->get_content();
        // Fetch the image information for this image.
        $imageinfo = @getimagesizefromstring($content);
        if (empty($imageinfo)) {
            return false;
        }
        // Create a new image from the file.
        $image = @imagecreatefromstring($content);

        // Capture the image as a string object, rather than straight to file.
        ob_start();
        if (!imagejpeg(
                image: $image,
                quality: self::DEFAULT_JPEG_COMPRESSION,
            )
        ) {
            ob_end_clean();
            return false;
        }
        $data = ob_get_clean();
        imagedestroy($image);
        return $data;
    }

    /**
     * Persists the redacted file to the file storage.
     *
     * @param string $content File content.
     */
    private function persist_redacted_file(string $content): void {
        $filerecord = (object) [
            'id' => $this->storedfile->get_id(),
            'mimetype' => $this->storedfile->get_mimetype(),
            'userid' => $this->storedfile->get_userid(),
            'source' => $this->storedfile->get_source(),
            'contextid' => $this->storedfile->get_contextid(),
            'component' => $this->storedfile->get_component(),
            'filearea'  => $this->storedfile->get_filearea(),
            'itemid'    => $this->storedfile->get_itemid(),
            'filepath'  => $this->storedfile->get_filepath(),
            'filename'  => $this->storedfile->get_filename(),
            'repositoryid' => $this->storedfile->get_repository_id(),
            'reference' => $this->storedfile->get_reference(),
        ];
        $fs = get_file_storage();
        $existingfile = $fs->get_file(
            $filerecord->contextid,
            $filerecord->component,
            $filerecord->filearea,
            $filerecord->itemid,
            $filerecord->filepath,
            $filerecord->filename,
        );
        if ($existingfile) {
            $existingfile->delete();
        }
        $redactedfile = $fs->create_file_from_string($filerecord, $content, false);
        $this->storedfile->replace_file_with($redactedfile);
    }

    /**
     * Clean up a file name if it starts with a dash (U+002D) or a Unicode minus sign (U+2212).
     *
     * According to https://exiftool.org/#security, ensure that input file names do not start with
     * a dash (U+002D) or a Unicode minus sign (U+2212). If found, remove the leading dash or Unicode minus sign.
     *
     * @param string $filename The file name to clean.
     * @return string The cleaned file name.
     */
    private function clean_filename(string $filename): string {
        $pattern = '/^[\x{002D}\x{2212}]/u';
        if (preg_match($pattern, $filename)) {
            $filename = preg_replace($pattern, '', $filename);
        }
        return clean_param($filename, PARAM_PATH);
    }

    /**
     * Returns true if the service is enabled, and false if it is not.
     *
     * @return bool
     */
    public function is_enabled(): bool {
        return (bool) get_config('core_fileredact', 'exifremoverenabled');
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
            $supportedmimetypesconfig = get_config('core_fileredact', 'exifremovermimetype');
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
        if (!during_initial_install() && empty(get_config('core_fileredact', 'exifremoverenabled'))) {
            $defaultenabled = 0;
        }

        $icon = $OUTPUT->pix_icon('i/externallink', get_string('opensinnewwindow'));
        $a = new \stdClass;
        $a->link = \html_writer::link(
            url: 'https://exiftool.sourceforge.net/install.html',
            text: "https://exiftool.sourceforge.net/install.html $icon",
            attributes: ['role' => 'opener', 'rel' => 'noreferrer', 'target' => '_blank'],
        );

        $settings->add(
            new \admin_setting_configcheckbox(
                name: 'core_fileredact/exifremoverenabled',
                visiblename: get_string('fileredact:exifremover:enabled', 'core_files'),
                description: get_string('fileredact:exifremover:enabled_desc', 'core_files', $a),
                defaultsetting: $defaultenabled,
            ),
        );

        $settings->add(
            new \admin_setting_heading(
                name: 'exifremoverheading',
                heading: get_string('fileredact:exifremover:heading', 'core_files'),
                information: '',
            )
        );

        $settings->add(
            new \admin_setting_configexecutable(
                name: 'core_fileredact/exifremovertoolpath',
                visiblename: get_string('fileredact:exifremover:toolpath', 'core_files'),
                description: get_string('fileredact:exifremover:toolpath_desc', 'core_files'),
                defaultdirectory: '',
            )
        );

        foreach (array_keys(self::REMOVE_TAGS) as $key) {
            $removedtagchoices[$key] = get_string("fileredact:exifremover:tag:$key", 'core_files');
        }
        $settings->add(
            new \admin_setting_configselect(
                name: 'core_fileredact/exifremoverremovetags',
                visiblename: get_string('fileredact:exifremover:removetags', 'core_files'),
                description: get_string('fileredact:exifremover:removetags_desc', 'core_files'),
                defaultsetting: self::DEFAULT_REMOVE_TAGS,
                choices: $removedtagchoices,
            ),
        );

        $mimetypedefault = <<<EOF
                        image/jpeg
                        image/tiff
                        EOF;
        $settings->add(
            new \admin_setting_configtextarea(
                name: 'core_fileredact/exifremovermimetype',
                visiblename: get_string('fileredact:exifremover:mimetype', 'core_files'),
                description: get_string('fileredact:exifremover:mimetype_desc', 'core_files'),
                defaultsetting: $mimetypedefault,
            ),
        );
    }
}
