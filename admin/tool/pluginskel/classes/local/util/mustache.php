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
 * Provides \tool_pluginskel\local\util\renderer class
 *
 * @package     tool_pluginskel
 * @subpackage  util
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_pluginskel\local\util;

use Mustache_Autoloader;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

defined('MOODLE_INTERNAL') || die();

/**
 * Provides access to the mustache engine instance
 *
 * @copyright 2016 David Mudrák <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mustache {

    /** @var Mustache_Engine */
    protected $engine = null;

    /**
     * Create mustache engine instance.
     *
     * @param array $options
     */
    public function __construct(array $options = []) {
        global $CFG;
        require_once($CFG->dirroot.'/lib/mustache/src/Mustache/Autoloader.php');

        Mustache_Autoloader::register();

        $default = [
            'cache' => make_localcache_directory('tool_pluginskel/mustache/'),
            'pragmas' => [Mustache_Engine::PRAGMA_BLOCKS, Mustache_Engine::PRAGMA_ANCHORED_DOT],
            'escape' => 'addslashes',
        ];

        if (empty($options['loader'])) {
            $default['loader'] = new Mustache_Loader_FilesystemLoader($CFG->dirroot.'/'.$CFG->admin.'/tool/pluginskel/skel');
            unset($options['loader']);
        }

        $this->engine = new Mustache_Engine(array_replace_recursive($default, $options));
    }

    /**
     * Render the template with the data
     *
     * @param string $template name
     * @param array $data
     * @return string
     */
    public function render($template, $data = []) {
        return $this->engine->loadTemplate($template)->render($data);
    }
}
