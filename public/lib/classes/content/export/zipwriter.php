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
 * Zip writer wrapper.
 *
 * @package     core
 * @copyright   2020 Simey Lameze <simey@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\content\export;

use context;
use context_system;
use moodle_url;
use stdClass;
use stored_file;

/**
 * Zip writer wrapper.
 *
 * @copyright   2020 Simey Lameze <simey@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class zipwriter {

    /** @var int Maximum folder length name for a context */
    const MAX_CONTEXT_NAME_LENGTH = 32;

    /** @var \ZipStream\ZipStream */
    protected $archive;

    /** @var int Max file size of an individual file in the archive */
    protected $maxfilesize = 1 * 1024 * 1024 * 10;

    /** @var resource File resource for the file handle for a file-based zip stream */
    protected $zipfilehandle = null;

    /** @var string File path for a file-based zip stream */
    protected $zipfilepath = null;

    /** @var context The context to use as a base for export */
    protected $rootcontext = null;

    /** @var array The files in the zip */
    protected $filesinzip = [];

    /** @var bool Whether page requirements needed for HTML pages have been added */
    protected $pagerequirementsadded = false;

    /** @var stdClass The course relating to the root context */
    protected $course;

    /** @var context The context of the course for the root contect */
    protected $coursecontext;

    /**
     * zipwriter constructor.
     *
     * @param \ZipStream\ZipStream $archive
     * @param stdClass|null $options
     */
    public function __construct(\ZipStream\ZipStream $archive, ?stdClass $options = null) {
        $this->archive = $archive;
        if ($options) {
            $this->parse_options($options);
        }

        $this->rootcontext = context_system::instance();
    }

    /**
     * Set a root context for use during the export.
     *
     * This is primarily used for creating paths within the archive relative to the root context.
     *
     * @param   context $rootcontext
     */
    public function set_root_context(context $rootcontext): void {
        $this->rootcontext = $rootcontext;
    }

    /**
     * Get the course object for the root context.
     *
     * @return  stdClass
     */
    protected function get_course(): stdClass {
        if ($this->course && ($this->coursecontext !== $this->rootcontext->get_course_context())) {
            $this->coursecontext = null;
            $this->course = null;
        }
        if (empty($this->course)) {
            $this->coursecontext = $this->rootcontext->get_course_context();
            $this->course = get_course($this->coursecontext->instanceid);
        }

        return $this->course;
    }

    /**
     * Parse options.
     *
     * @param stdClass $options
     */
    protected function parse_options(stdClass $options): void {
        if (property_exists($options, 'maxfilesize')) {
            $this->maxfilesize = $options->maxfilesize;
        }
    }

    /**
     * Finish writing the zip footer.
     */
    public function finish(): void {
        $this->archive->finish();

        if ($this->zipfilehandle) {
            fclose($this->zipfilehandle);
        }
    }

    /**
     * Get the stream writer.
     *
     * @param string $filename
     * @param stdClass|null $exportoptions
     * @return static
     */
    public static function get_stream_writer(string $filename, ?stdClass $exportoptions = null) {
        $archive = new \ZipStream\ZipStream(
            outputName: $filename,
        );

        $zipwriter = new static($archive, $exportoptions);

        \core\session\manager::write_close();
        return $zipwriter;
    }

    /**
     * Get the file writer.
     *
     * @param string $filename
     * @param stdClass|null $exportoptions
     * @return static
     */
    public static function get_file_writer(string $filename, ?stdClass $exportoptions = null) {
        $dir = make_request_directory();
        $filepath = $dir . "/$filename";
        $fh = fopen($filepath, 'w');

        $archive = new \ZipStream\ZipStream(
            outputName: $filename,
            outputStream: $fh,
            sendHttpHeaders: false,
        );

        $zipwriter = new static($archive, $exportoptions);

        $zipwriter->zipfilehandle = $fh;
        $zipwriter->zipfilepath = $filepath;

        \core\session\manager::write_close();
        return $zipwriter;
    }

    /**
     * Get the file path for a file-based zip writer.
     *
     * If this is not a file-based writer then no value is returned.
     *
     * @return  null|string
     */
    public function get_file_path(): ?string {
        return $this->zipfilepath;
    }

    /**
     * Add a file from the File Storage API.
     *
     * @param   context $context
     * @param   string $filepathinzip
     * @param   stored_file $file The file to add
     */
    public function add_file_from_stored_file(
        context $context,
        string $filepathinzip,
        stored_file $file
    ): void {
        $fullfilepathinzip = $this->get_context_path($context, $filepathinzip);

        if ($file->get_filesize() <= $this->maxfilesize) {
            $filehandle = $file->get_content_file_handle();
            $this->archive->addFileFromStream($fullfilepathinzip, $filehandle);
            fclose($filehandle);

            $this->filesinzip[] = $fullfilepathinzip;
        }
    }

    /**
     * Add a file from string content.
     *
     * @param   context $context
     * @param   string $filepathinzip
     * @param   string $content
     */
    public function add_file_from_string(
        context $context,
        string $filepathinzip,
        string $content
    ): void {
        $fullfilepathinzip = $this->get_context_path($context, $filepathinzip);

        $this->archive->addFile($fullfilepathinzip, $content);

        $this->filesinzip[] = $fullfilepathinzip;
    }

    /**
     * Create a file based on a Mustache Template and associated data.
     *
     * @param   context $context
     * @param   string $filepathinzip
     * @param   string $template
     * @param   stdClass $templatedata
     */
    public function add_file_from_template(
        context $context,
        string $filepathinzip,
        string $template,
        stdClass $templatedata
    ): void {
        global $CFG, $PAGE, $SITE, $USER;

        $exportedcourse = $this->get_course();
        $courselink = (new moodle_url('/course/view.php', ['id' => $exportedcourse->id]))->out(false);
        $coursename = format_string($exportedcourse->fullname, true, ['context' => $this->coursecontext]);

        $this->add_template_requirements();

        $templatedata->global = (object) [
            'righttoleft' => right_to_left(),
            'language' => get_html_lang_attribute_value(current_language()),
            'sitename' => format_string($SITE->fullname, true, ['context' => context_system::instance()]),
            'siteurl' => $CFG->wwwroot,
            'pathtotop' => $this->get_relative_context_path($context, $this->rootcontext, '/'),
            'contentexportfooter' => get_string('contentexport_footersummary', 'core', (object) [
                'courselink' => $courselink,
                'coursename' => $coursename,
                'userfullname' => fullname($USER),
                'date' => userdate(time()),
            ]),
            'contentexportsummary' => get_string('contentexport_coursesummary', 'core', (object) [
                'courselink' => $courselink,
                'coursename' => $coursename,
                'date' => userdate(time()),
            ]),
            'coursename' => $coursename,
            'courseshortname' => $exportedcourse->shortname,
            'courselink' => $courselink,
            'exportdate' => userdate(time()),
            'maxfilesize' => display_size($this->maxfilesize, 0),
        ];

        $renderer = $PAGE->get_renderer('core');
        $this->add_file_from_string($context, $filepathinzip, $renderer->render_from_template($template, $templatedata));
    }

    /**
     * Ensure that all requirements for a templated page are present.
     *
     * This includes CSS, and any other similar content.
     */
    protected function add_template_requirements(): void {
        if ($this->pagerequirementsadded) {
            return;
        }

        // CSS required.
        $this->add_content_from_dirroot('/theme/boost/style/moodle.css', 'shared/moodle.css');

        $this->pagerequirementsadded = true;
    }

    /**
     * Add content from the dirroot into the specified path in the zip file.
     *
     * @param   string $dirrootpath
     * @param   string $pathinzip
     */
    protected function add_content_from_dirroot(string $dirrootpath, string $pathinzip): void {
        global $CFG;

        $this->archive->addFileFromPath(
            $this->get_context_path($this->rootcontext, $pathinzip),
            "{$CFG->dirroot}/{$dirrootpath}"
        );
    }

    /**
     * Check whether the file was actually added to the archive.
     *
     * @param   context $context
     * @param   string $filepathinzip
     * @return  bool
     */
    public function is_file_in_archive(context $context, string $filepathinzip): bool {
        $fullfilepathinzip = $this->get_context_path($context, $filepathinzip);

        return in_array($fullfilepathinzip, $this->filesinzip);
    }

    /**
     * Get the full path to the context within the zip.
     *
     * @param   context $context
     * @param   string $filepathinzip
     * @return  string
     */
    public function get_context_path(context $context, string $filepathinzip): string {
        if (!$context->is_child_of($this->rootcontext, true)) {
            throw new \coding_exception("Unexpected path requested");
        }

        // Fetch the path from the course down.
        $parentcontexts = array_filter(
            $context->get_parent_contexts(true),
            function(context $curcontext): bool {
                return $curcontext->is_child_of($this->rootcontext, true);
            }
        );

        foreach (array_reverse($parentcontexts) as $curcontext) {
            $path[] = $this->get_context_folder_name($curcontext);
        }

        $path[] = $filepathinzip;

        $finalpath = implode('/', $path);

        // Remove relative paths (./).
        $finalpath = str_replace('./', '/', $finalpath);

        // De-duplicate slashes.
        $finalpath = str_replace('//', '/', $finalpath);

        return $finalpath;
    }

    /**
     * Get a relative path to the specified context path.
     *
     * @param   context $rootcontext
     * @param   context $targetcontext
     * @param   string $filepathinzip
     * @return  string
     */
    public function get_relative_context_path(context $rootcontext, context $targetcontext, string $filepathinzip): string {
        $path = [];
        if ($targetcontext === $rootcontext) {
            $lookupcontexts = [];
        } else if ($targetcontext->is_child_of($rootcontext, true)) {
            // Fetch the path from the course down.
            $lookupcontexts = array_filter(
                $targetcontext->get_parent_contexts(true),
                function(context $curcontext): bool {
                    return $curcontext->is_child_of($this->rootcontext, false);
                }
            );

            foreach ($lookupcontexts as $curcontext) {
                array_unshift($path, $this->get_context_folder_name($curcontext));
            }
        } else if ($targetcontext->is_parent_of($rootcontext, true)) {
            $lookupcontexts = $targetcontext->get_parent_contexts(true);
            $path[] = '..';
        }

        $path[] = $filepathinzip;
        $relativepath = implode(DIRECTORY_SEPARATOR, $path);

        // De-duplicate slashes and remove leading /.
        $relativepath = ltrim(preg_replace('#/+#', '/', $relativepath), '/');

        if (substr($relativepath, 0, 1) !== '.') {
            $relativepath = "./{$relativepath}";
        }

        return $relativepath;
    }

    /**
     * Get the name of the folder for the specified context.
     *
     * @param   context $context
     * @return  string
     */
    protected function get_context_folder_name(context $context): string {
        // Replace spaces with underscores, or they will be removed completely when cleaning.
        $contextname = str_replace(' ', '_', $context->get_context_name());

        // Clean the context name of all but basic characters, as some systems don't support unicode within zip structure.
        $shortenedname = shorten_text(
            clean_param($contextname, PARAM_SAFEDIR),
            self::MAX_CONTEXT_NAME_LENGTH,
            true
        );

        return "{$shortenedname}_.{$context->id}";
    }

    /**
     * Rewrite any pluginfile URLs in the content.
     *
     * @param   context $context
     * @param   string $content
     * @param   string $component
     * @param   string $filearea
     * @param   null|int $pluginfileitemid The itemid to use in the pluginfile URL when composing any required URLs
     * @return  string
     */
    protected function rewrite_other_pluginfile_urls(
        context $context,
        string $content,
        string $component,
        string $filearea,
        ?int $pluginfileitemid
    ): string {
        // The pluginfile URLs should have been rewritten when the files were exported, but if any file was too large it
        // may not have been included.
        // In that situation use a tokenpluginfile URL.

        if (strpos($content, '@@PLUGINFILE@@/') !== false) {
            // Some files could not be rewritten.
            // Use a tokenurl pluginfile for those.
            $content = file_rewrite_pluginfile_urls(
                $content,
                'pluginfile.php',
                $context->id,
                $component,
                $filearea,
                $pluginfileitemid,
                [
                    'includetoken' => true,
                ]
            );
        }

        return $content;
    }

    /**
     * Export files releating to this text area.
     *
     * @param   context $context
     * @param   string $subdir The sub directory to export any files to
     * @param   string $content
     * @param   string $component
     * @param   string $filearea
     * @param   int $fileitemid The itemid as used in the Files API
     * @param   null|int $pluginfileitemid The itemid to use in the pluginfile URL when composing any required URLs
     * @return  exported_item
     */
    public function add_pluginfiles_for_content(
        context $context,
        string $subdir,
        string $content,
        string $component,
        string $filearea,
        int $fileitemid,
        ?int $pluginfileitemid
    ): exported_item {
        // Export all of the files for this text area.
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, $component, $filearea, $fileitemid);

        $result = new exported_item();
        foreach ($files as $file) {
            if ($file->is_directory()) {
                continue;
            }

            $filepathinzip = self::get_filepath_for_file($file, $subdir, false);
            $this->add_file_from_stored_file(
                $context,
                $filepathinzip,
                $file
            );

            if ($this->is_file_in_archive($context, $filepathinzip)) {
                // Attempt to rewrite any @@PLUGINFILE@@ URLs for this file in the content.
                $searchpath = "@@PLUGINFILE@@" . $file->get_filepath() . rawurlencode($file->get_filename());
                if (strpos($content, $searchpath) !== false) {
                    $content = str_replace($searchpath, self::get_filepath_for_file($file, $subdir, true), $content);
                    $result->add_file($filepathinzip, true);
                } else {
                    $result->add_file($filepathinzip, false);
                }
            }

        }

        $content = $this->rewrite_other_pluginfile_urls($context, $content, $component, $filearea, $pluginfileitemid);
        $result->set_content($content);

        return $result;
    }

    /**
     * Get the filepath for the specified stored_file.
     *
     * @param   stored_file $file
     * @param   string $parentdir Any parent directory to place this file in
     * @param   bool $escape
     * @return  string
     */
    protected static function get_filepath_for_file(stored_file $file, string $parentdir, bool $escape): string {
        $path = [];

        $filepath = sprintf(
            '%s/%s/%s/%s',
            $parentdir,
            $file->get_filearea(),
            $file->get_filepath(),
            $file->get_filename()
        );

        if ($escape) {
            foreach (explode('/', $filepath) as $dirname) {
                $path[] = rawurlencode($dirname);
            }
            $filepath = implode('/', $path);
        }

        return ltrim(preg_replace('#/+#', '/', $filepath), '/');
    }

}
