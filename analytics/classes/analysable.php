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
 * Any element analysers can analyse.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Any element analysers can analyse.
 *
 * Analysers get_analysers method return all analysable elements in the site;
 * it is important that analysable elements implement lazy loading to avoid
 * big memory footprints. See \core_analytics\course example.
 *
 * @package   core_analytics
 * @copyright 2016 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface analysable {

    /**
     * Max timestamp.
     */
    const MAX_TIME = 9999999999;

    /**
     * The analysable unique identifier in the site.
     *
     * @return int.
     */
    public function get_id();

    /**
     * The analysable human readable name
     *
     * @return string
     */
    public function get_name();

    /**
     * The analysable context.
     *
     * @return \context
     */
    public function get_context();

    /**
     * The start of the analysable if there is one.
     *
     * @return int|false
     */
    public function get_start();

    /**
     * The end of the analysable if there is one.
     *
     * @return int|false
     */
    public function get_end();
}
