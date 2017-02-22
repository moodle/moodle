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
 * URL manager.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * URL manager class.
 *
 * This class has to be used to get the URL to a resource, this allows for different
 * alternate frontends to be used without resorting to core hacks. Note that you
 * do not have to use this when you are navigating between pages of your own plugin.
 *
 * To set another resolver, set the following config value in config.php:
 * $CFG->core_competency_url_resolver = 'your_plugin\\your_url_resolver_class';
 *
 * Your URL resolver should implement the same methods as the ones listed in
 * this class (except for {{@link self::get()}}) but not statically.
 *
 * /!\ Note, resolvers MUST NEVER assume that the resource, or the resources
 * represented by the arguments, still exist.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class url {

    /** @var url_resolver The URL resolver instance.*/
    protected static $resolver;

    /**
     * Defer to the resolver.
     *
     * @param string $resource The resource type.
     * @param array $args The arguments.
     * @return mixed
     */
    protected static function get($resource, $args) {
        global $CFG;
        if (!isset(static::$resolver)) {
            $klass = !empty($CFG->core_competency_url_resolver) ? $CFG->core_competency_url_resolver : 'tool_lp\\url_resolver';
            static::$resolver = new $klass();
        }
        if (!method_exists(static::$resolver, $resource)) {
            debugging("URL for '$resource' not implemented.", DEBUG_DEVELOPER);
            return new moodle_url('/');
        }
        return call_user_func_array([static::$resolver, $resource], $args);
    }

    /**
     * The URL where the competency can be found.
     *
     * @param int $competencyid The competency ID.
     * @param int $pagecontextid The ID of the context we are in.
     * @return moodle_url
     */
    public static function competency($competencyid, $pagecontextid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the framework can be found.
     *
     * @param int $frameworkid The framework ID.
     * @param int $pagecontextid The ID of the context we are in.
     * @return moodle_url
     */
    public static function framework($frameworkid, $pagecontextid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the frameworks can be found.
     *
     * @param int $pagecontextid The ID of the context that we are browsing.
     * @return moodle_url
     */
    public static function frameworks($pagecontextid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the plan can be found.
     *
     * @param int $planid The plan ID.
     * @return moodle_url
     */
    public static function plan($planid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the plans of a user can be found.
     *
     * @param int $userid The user ID.
     * @return moodle_url
     */
    public static function plans($userid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the template can be found.
     *
     * @param int $templateid The template ID.
     * @param int $pagecontextid The ID of the context we are in.
     * @return moodle_url
     */
    public static function template($templateid, $pagecontextid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the templates can be found.
     *
     * @param int $pagecontextid The ID of the context that we are browsing.
     * @return moodle_url
     */
    public function templates($pagecontextid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the user competency can be found.
     *
     * @param int $usercompetencyid The user competency ID
     * @return moodle_url
     */
    public static function user_competency($usercompetencyid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the user competency can be found in the context of a course.
     *
     * @param int $userid The user ID
     * @param int $competencyid The competency ID.
     * @param int $courseid The course ID.
     * @return moodle_url
     */
    public static function user_competency_in_course($userid, $competencyid, $courseid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the user competency can be found in the context of a plan.
     *
     * @param int $userid The user ID
     * @param int $competencyid The competency ID.
     * @param int $planid The plan ID.
     * @return moodle_url
     */
    public static function user_competency_in_plan($userid, $competencyid, $planid) {
        return static::get(__FUNCTION__, func_get_args());
    }

    /**
     * The URL where the user evidence (of prior learning) can be found.
     *
     * @param int $userevidenceid The user evidence ID
     * @return moodle_url
     */
    public static function user_evidence($userevidenceid) {
        return static::get(__FUNCTION__, func_get_args());
    }
}
