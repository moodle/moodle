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
 * The mustache renderer.
 *
 * @package    theme_adaptable
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output;

/**
 * The mustache renderer.
 */
class mustache_renderer extends \core\output\core_renderer {
    /**
     * @var Mustache_Loader $stringloader The mustache string loader.
     */
    protected $stringloader;

    /**
     * Renders a template by name with the given context.
     *
     * The provided data needs to be array/stdClass made up of only simple types.
     * Simple types are array,stdClass,bool,int,float,string
     *
     * @since 2.9
     * @param string $templatename
     * @param array|stdClass $context Context containing data for the template.
     * @return string|boolean
     */
    public function render_from_template($templatename, $context) {
        if ($this->stringloader === null) {
            // Change loaders!
            $mustache = $this->get_mustache();
            $this->stringloader = new \Mustache_Loader_StringLoader();
            $mustache->setLoader($this->stringloader);
            // Needed to get the partials from the setting or the file system, otherwise they are not processed.
            $partialsloader = new mustache_filesystemstring_loader();
            $mustache->setPartialsLoader($partialsloader);
        }
        return parent::render_from_template($templatename, $context);
    }
}
