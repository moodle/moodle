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
 * Analytics basic actions manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

/**
 * Analytics basic actions manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /**
     * @var \core_analytics\predictor[]
     */
    protected static $predictionprocessors = null;

    /**
     * @var \core_analytics\local\indicator\base[]
     */
    protected static $allindicators = null;

    /**
     * @var \core_analytics\local\time_splitting\base[]
     */
    protected static $alltimesplittings = null;

    public static function get_all_models($enabled = false, $trained = false, $predictioncontext = false) {
        global $DB;

        $filters = array();
        if ($enabled) {
            $filters['enabled'] = 1;
        }
        if ($trained) {
            $filters['trained'] = 1;
        }
        $modelobjs = $DB->get_records('analytics_models', $filters);

        $models = array();
        foreach ($modelobjs as $modelobj) {
            $model = new \core_analytics\model($modelobj);
            if (!$predictioncontext || $model->predictions_exist($predictioncontext)) {
                $models[$modelobj->id] = $model;
            }
        }
        return $models;
    }

    /**
     * Returns the site selected predictions processor.
     *
     * @param string $predictionclass
     * @param bool $checkisready
     * @return \core_analytics\predictor
     */
    public static function get_predictions_processor($predictionclass = false, $checkisready = true) {

        // We want 0 or 1 so we can use it as an array key for caching.
        $checkisready = intval($checkisready);

        if ($predictionclass === false) {
            $predictionclass = get_config('analytics', 'predictionsprocessor');
        }

        if (empty($predictionclass)) {
            // Use the default one if nothing set.
            $predictionclass = '\mlbackend_php\processor';
        }

        if (!class_exists($predictionclass)) {
            throw new \coding_exception('Invalid predictions processor ' . $predictionclass . '.');
        }

        $interfaces = class_implements($predictionclass);
        if (empty($interfaces['core_analytics\predictor'])) {
            throw new \coding_exception($predictionclass . ' should implement \core_analytics\predictor.');
        }

        // Return it from the cached list.
        if (!isset(self::$predictionprocessors[$checkisready][$predictionclass])) {

            $instance = new $predictionclass();
            if ($checkisready) {
                $isready = $instance->is_ready();
                if ($isready !== true) {
                    throw new \moodle_exception('errorprocessornotready', 'analytics', '', $isready);
                }
            }
            self::$predictionprocessors[$checkisready][$predictionclass] = $instance;
        }

        return self::$predictionprocessors[$checkisready][$predictionclass];
    }

    public static function get_all_prediction_processors() {

        $mlbackends = \core_component::get_plugin_list('mlbackend');

        $predictionprocessors = array();
        foreach ($mlbackends as $mlbackend => $unused) {
            $classfullpath = '\\mlbackend_' . $mlbackend . '\\processor';
            $predictionprocessors[$classfullpath] = self::get_predictions_processor($classfullpath, false);
        }
        return $predictionprocessors;
    }

    /**
     * Get all available time splitting methods.
     *
     * @return \core_analytics\time_splitting\base[]
     */
    public static function get_all_time_splittings() {
        if (self::$alltimesplittings !== null) {
            return self::$alltimesplittings;
        }

        $classes = self::get_analytics_classes('time_splitting');

        self::$alltimesplittings = [];
        foreach ($classes as $fullclassname => $classpath) {
            $instance = self::get_time_splitting($fullclassname);
            // We need to check that it is a valid time splitting method, it may be an abstract class.
            if ($instance) {
                self::$alltimesplittings[$instance->get_id()] = $instance;
            }
        }

        return self::$alltimesplittings;
    }

    /**
     * Returns the enabled time splitting methods.
     *
     * @return \core_analytics\local\time_splitting\base[]
     */
    public static function get_enabled_time_splitting_methods() {

        if ($enabledtimesplittings = get_config('analytics', 'timesplittings')) {
            $enabledtimesplittings = array_flip(explode(',', $enabledtimesplittings));
        }

        $timesplittings = self::get_all_time_splittings();
        foreach ($timesplittings as $key => $timesplitting) {

            // We remove the ones that are not enabled. This also respects the default value (all methods enabled).
            if (!empty($enabledtimesplittings) && !isset($enabledtimesplittings[$key])) {
                unset($timesplittings[$key]);
            }
        }
        return $timesplittings;
    }

    /**
     * Returns a time splitting method by its classname.
     *
     * @param string $fullclassname
     * @return \core_analytics\local\time_splitting\base|false False if it is not valid.
     */
    public static function get_time_splitting($fullclassname) {
        if (!self::is_valid($fullclassname, '\core_analytics\local\time_splitting\base')) {
            return false;
        }
        return new $fullclassname();
    }

    /**
     * Return all system indicators.
     *
     * @return \core_analytics\local\indicator\base[]
     */
    public static function get_all_indicators() {
        if (self::$allindicators !== null) {
            return self::$allindicators;
        }

        $classes = self::get_analytics_classes('indicator');

        self::$allindicators = [];
        foreach ($classes as $fullclassname => $classpath) {
            $instance = self::get_indicator($fullclassname);
            if ($instance) {
                // Using get_class as get_component_classes_in_namespace returns double escaped fully qualified class names.
                self::$allindicators[$instance->get_id()] = $instance;
            }
        }

        return self::$allindicators;
    }

    public static function get_target($fullclassname) {
        if (!self::is_valid($fullclassname, 'core_analytics\local\target\base')) {
            return false;
        }
        return new $fullclassname();
    }

    /**
     * Returns an instance of the provided indicator.
     *
     * @param string $fullclassname
     * @return \core_analytics\local\indicator\base|false False if it is not valid.
     */
    public static function get_indicator($fullclassname) {
        if (!self::is_valid($fullclassname, 'core_analytics\local\indicator\base')) {
            return false;
        }
        return new $fullclassname();
    }

    /**
     * Returns whether a time splitting method is valid or not.
     *
     * @param string $fullclassname
     * @return bool
     */
    public static function is_valid($fullclassname, $baseclass) {
        if (is_subclass_of($fullclassname, $baseclass)) {
            if ((new \ReflectionClass($fullclassname))->isInstantiable()) {
                return true;
            }
        }
        return false;
    }

    /**
     * get_analytics_logstore
     *
     * @return \core\log\sql_reader
     */
    public static function get_analytics_logstore() {
        $readers = get_log_manager()->get_readers('core\log\sql_reader');
        $analyticsstore = get_config('analytics', 'logstore');
        if (empty($analyticsstore)) {
            $logstore = reset($readers);
        } else if (!empty($readers[$analyticsstore])) {
            $logstore = $readers[$analyticsstore];
        } else {
            $logstore = reset($readers);
            debugging('The selected log store for analytics is not available anymore. Using "' .
                $logstore->get_name() . '"', DEBUG_DEVELOPER);
        }

        if (!$logstore->is_logging()) {
            debugging('The selected log store for analytics "' . $logstore->get_name() .
                '" is not logging activity logs', DEBUG_DEVELOPER);
        }

        return $logstore;
    }

    /**
     * Returns the provided element classes in the site.
     *
     * @param string $element
     * @return string[] Array keys are the FQCN and the values the class path.
     */
    private static function get_analytics_classes($element) {

        // Just in case...
        $element = clean_param($element, PARAM_ALPHAEXT);

        $classes = \core_component::get_component_classes_in_namespace('core_analytics', 'local\\' . $element);
        foreach (\core_component::get_plugin_types() as $type => $unusedplugintypepath) {
            foreach (\core_component::get_plugin_list($type) as $pluginname => $unusedpluginpath) {
                $frankenstyle = $type . '_' . $pluginname;
                $classes += \core_component::get_component_classes_in_namespace($frankenstyle, 'analytics\\' . $element);
            }
        }
        return $classes;
    }
}
