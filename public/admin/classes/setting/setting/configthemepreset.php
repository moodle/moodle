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
 * Used to validate theme presets code and ensuring they compile well.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2019 Bas Brands <bas@moodle.com>
 */
namespace core_admin\setting\setting;

class configthemepreset extends \core_admin\setting\setting\configselect {

    /** @var string The name of the theme to check for */
    private $themename;

    /**
     * Constructor
     * @param string $name unique ascii name, either 'mysetting' for settings that in config,
     * or 'myplugin/mysetting' for ones in config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string|int $defaultsetting
     * @param array $choices array of $value=>$label for each selection
     * @param string $themename name of theme to check presets for.
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $choices, $themename) {
        $this->themename = $themename;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $choices);
    }

    /**
     * Write settings if validated
     *
     * @param string $data
     * @return string
     */
    public function write_setting($data) {
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Validate the preset file to ensure its parsable.
     *
     * @param string $data The preset file chosen.
     * @return mixed bool true for success or string:error on failure.
     */
    public function validate($data) {

        if (in_array($data, ['default.scss', 'plain.scss'])) {
            return true;
        }

        $fs = get_file_storage();
        $theme = \theme_config::load($this->themename);
        $context = \context_system::instance();

        // If the preset has not changed there is no need to validate it.
        if ($theme->settings->preset == $data) {
            return true;
        }

        if ($presetfile = $fs->get_file($context->id, 'theme_' . $this->themename, 'preset', 0, '/', $data)) {
            // This operation uses a lot of resources.
            raise_memory_limit(MEMORY_EXTRA);
            \core_php_time_limit::raise(300);

            // TODO: MDL-62757 When changing anything in this method please do not forget to check
            // if the get_css_content_from_scss() method in class theme_config needs updating too.

            $compiler = new \core_scss();
            $compiler->prepend_raw_scss($theme->get_pre_scss_code());
            $compiler->append_raw_scss($presetfile->get_content());
            if ($scssproperties = $theme->get_scss_property()) {
                $compiler->setImportPaths($scssproperties[0]);
            }
            $compiler->append_raw_scss($theme->get_extra_scss_code());

            try {
                $compiler->to_css();
            } catch (\Exception $e) {
                return get_string('invalidthemepreset', 'admin', $e->getMessage());
            }

            // Try to save memory.
            $compiler = null;
            unset($compiler);
        }

        return true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(configthemepreset::class, \admin_setting_configthemepreset::class);
