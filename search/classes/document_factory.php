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
 * Search documents factory.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_search;

defined('MOODLE_INTERNAL') || die();

/**
 * Search document factory.
 *
 * @package    core_search
 * @copyright  2015 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class document_factory {

    /**
     * The document class used by search engines.
     *
     * Defined as an array to prevent unexpected caching issues, it should only contain one search
     * engine as only one search engine will be used during a request. This might change during
     * testing, remember to use document_factory::clean_statics in that case.
     *
     * @var array
     */
    protected static $docclassnames = array();

    /**
     * Returns the appropiate document object as it depends on the engine.
     *
     * @param int $itemid Document itemid
     * @param string $componentname Document component name
     * @param string $areaname Document area name
     * @param \core_search\engine $engine Falls back to the search engine in use.
     * @return \core_search\document Base document or the engine implementation.
     */
    public static function instance($itemid, $componentname, $areaname, $engine = false) {

        if ($engine === false) {
            $search = \core_search\manager::instance();
            $engine = $search->get_engine();
        }

        $pluginname = $engine->get_plugin_name();

        if (!empty(self::$docclassnames[$pluginname])) {
            return new self::$docclassnames[$pluginname]($itemid, $componentname, $areaname);
        }

        self::$docclassnames[$pluginname] = $engine->get_document_classname();

        return new self::$docclassnames[$pluginname]($itemid, $componentname, $areaname);
    }

    /**
     * Clears static vars.
     *
     * @return void
     */
    public static function clean_static() {
        self::$docclassnames = array();
    }
}
