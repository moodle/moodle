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
 * Base class for library handlers.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p\local\library;

defined('MOODLE_INTERNAL') || die();

/**
 * Base class for library handlers.
 *
 * If a new H5P libraries handler plugin has to be created, it has to define class
 * PLUGINNAME\local\library\handler that extends \core_h5p\local\library\handler.
 *
 * @package    core_h5p
 * @copyright  2019 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class handler {

    /**
     * Get the current version of the H5P core library.
     *
     * @return string
     */
    abstract public static function get_h5p_version(): string;

    /**
     * Get the base path for the H5P Libraries.
     *
     * @return null|string
     */
    public static function get_h5p_library_base(): ?string {
        $h5pversion = static::get_h5p_version();
        return "/h5p/h5plib/v{$h5pversion}/joubel";
    }

    /**
     * Get the base path for the current H5P Core Library.
     *
     * @param string $filepath The path within the H5P root
     * @return null|string
     */
    public static function get_h5p_core_library_base(?string $filepath = null): ?string {
        return static::get_h5p_library_base() . "/core/{$filepath}";
    }

    /**
     * Get the base path for the current H5P Editor Library.
     *
     * @param null|string $filepath The path within the H5P root.
     * @return string Path to a file in the H5P Editor library.
     */
    public static function get_h5p_editor_library_base(?string $filepath = null): string {
        return static::get_h5p_library_base() . "/editor/{$filepath}";
    }

    /**
     * Register the H5P autoloader.
     */
    public static function register(): void {
        spl_autoload_register([static::class, 'autoload']);
    }

    /**
     * SPL Autoloading function for H5P.
     *
     * @param string $classname The name of the class to load
     */
    public static function autoload($classname): void {
        global $CFG;

        $classes = static::get_class_list();

        if (isset($classes[$classname])) {
            if (file_exists($CFG->dirroot . static::get_h5p_core_library_base($classes[$classname]))) {
                require_once($CFG->dirroot . static::get_h5p_core_library_base($classes[$classname]));
            } else {
                require_once($CFG->dirroot . static::get_h5p_editor_library_base($classes[$classname]));
            }
        }
    }

    /**
     * Get a URL for the current H5P Core Library.
     *
     * @param string $filepath The path within the h5p root
     * @param array $params these params override current params or add new
     * @return null|\moodle_url
     */
    public static function get_h5p_core_library_url(?string $filepath = null, ?array $params = null): ?\moodle_url {
        return new \moodle_url(static::get_h5p_core_library_base($filepath), $params);
    }

    /**
     * Get a URL for the current H5P Editor Library.
     *
     * @param string $filepath The path within the h5p root.
     * @param array $params These params override current params or add new.
     * @return null|\moodle_url The moodle_url to a file in the H5P Editor library.
     */
    public static function get_h5p_editor_library_url(?string $filepath = null, ?array $params = null): ?\moodle_url {
        return new \moodle_url(static::get_h5p_editor_library_base($filepath), $params);
    }

    /**
     * Returns a localized string, if it exists in the h5plib plugin and the value it's different from the English version.
     *
     * @param string $identifier The key identifier for the localized string
     * @param string $language Language to get the localized string.
     * @return string|null The localized string or null if it doesn't exist in this H5P library plugin.
     */
    public static function get_h5p_string(string $identifier, string $language): ?string {
        $value = null;
        $h5pversion = static::get_h5p_version();
        $component = 'h5plib_v' . $h5pversion;
        if (get_string_manager()->string_exists($identifier, $component)) {
            $defaultmoodlelang = 'en';
            // In Moodle, all the English strings always will exist because they have to be declared in order to let users
            // to translate them. That's why, this method will only replace existing key if the value is different from
            // the English version and the current language is not English.
            $string = new \lang_string($identifier, $component);
            if ($language === $defaultmoodlelang || $string->out($language) !== $string->out($defaultmoodlelang)) {
                $value = $string->out($language);
            }
        }

        return $value;
    }

    /**
     * Return the list of classes with their location within the joubel directory.
     *
     * @return array
     */
    protected static function get_class_list(): array {
        return [
            'H5PCore' => 'h5p.classes.php',
            'H5PFrameworkInterface' => 'h5p.classes.php',
            'H5PContentValidator' => 'h5p.classes.php',
            'H5PValidator' => 'h5p.classes.php',
            'H5PStorage' => 'h5p.classes.php',
            'H5PDevelopment' => 'h5p-development.class.php',
            'H5PFileStorage' => 'h5p-file-storage.interface.php',
            'H5PMetadata' => 'h5p-metadata.class.php',
            'H5peditor' => 'h5peditor.class.php',
            'H5peditorStorage' => 'h5peditor-storage.interface.php',
            'H5PEditorAjaxInterface' => 'h5peditor-ajax.interface.php',
            'H5PEditorAjax' => 'h5peditor-ajax.class.php',
            'H5peditorFile' => 'h5peditor-file.class.php',
        ];
    }
}
