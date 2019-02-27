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
     * Default mlbackend
     */
    const DEFAULT_MLBACKEND = '\mlbackend_php\processor';

    /**
     * @var \core_analytics\predictor[]
     */
    protected static $predictionprocessors = null;

    /**
     * @var \core_analytics\local\target\base[]
     */
    protected static $alltargets = null;

    /**
     * @var \core_analytics\local\indicator\base[]
     */
    protected static $allindicators = null;

    /**
     * @var \core_analytics\local\time_splitting\base[]
     */
    protected static $alltimesplittings = null;

    /**
     * Checks that the user can manage models
     *
     * @throws \required_capability_exception
     * @return void
     */
    public static function check_can_manage_models() {
        require_capability('moodle/analytics:managemodels', \context_system::instance());
    }

    /**
     * Checks that the user can list that context insights
     *
     * @throws \required_capability_exception
     * @param \context $context
     * @return void
     */
    public static function check_can_list_insights(\context $context) {
        require_capability('moodle/analytics:listinsights', $context);
    }

    /**
     * Returns all system models that match the provided filters.
     *
     * @param bool $enabled
     * @param bool $trained
     * @param \context|false $predictioncontext
     * @return \core_analytics\model[]
     */
    public static function get_all_models($enabled = false, $trained = false, $predictioncontext = false) {
        global $DB;

        $params = array();

        $sql = "SELECT am.* FROM {analytics_models} am";

        if ($enabled || $trained || $predictioncontext) {
            $conditions = [];
            if ($enabled) {
                $conditions[] = 'am.enabled = :enabled';
                $params['enabled'] = 1;
            }
            if ($trained) {
                $conditions[] = 'am.trained = :trained';
                $params['trained'] = 1;
            }
            if ($predictioncontext) {
                $conditions[] = "EXISTS (SELECT 'x' FROM {analytics_predictions} ap WHERE ap.modelid = am.id AND ap.contextid = :contextid)";
                $params['contextid'] = $predictioncontext->id;
            }
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY am.enabled DESC, am.timemodified DESC';

        $modelobjs = $DB->get_records_sql($sql, $params);

        $models = array();
        foreach ($modelobjs as $modelobj) {
            $model = new \core_analytics\model($modelobj);
            if ($model->is_available()) {
                $models[$modelobj->id] = $model;
            }
        }
        return $models;
    }

    /**
     * Returns the provided predictions processor class.
     *
     * @param false|string $predictionclass Returns the system default processor if false
     * @param bool $checkisready
     * @return \core_analytics\predictor
     */
    public static function get_predictions_processor($predictionclass = false, $checkisready = true) {

        // We want 0 or 1 so we can use it as an array key for caching.
        $checkisready = intval($checkisready);

        if (!$predictionclass) {
            $predictionclass = get_config('analytics', 'predictionsprocessor');
        }

        if (empty($predictionclass)) {
            // Use the default one if nothing set.
            $predictionclass = self::default_mlbackend();
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

    /**
     * Return all system predictions processors.
     *
     * @return \core_analytics\predictor[]
     */
    public static function get_all_prediction_processors() {

        $mlbackends = \core_component::get_plugin_list('mlbackend');

        $predictionprocessors = array();
        foreach ($mlbackends as $mlbackend => $unused) {
            $classfullpath = '\mlbackend_' . $mlbackend . '\processor';
            $predictionprocessors[$classfullpath] = self::get_predictions_processor($classfullpath, false);
        }
        return $predictionprocessors;
    }

    /**
     * Returns the name of the provided predictions processor.
     *
     * @param \core_analytics\predictor $predictionsprocessor
     * @return string
     */
    public static function get_predictions_processor_name(\core_analytics\predictor $predictionsprocessor) {
            $component = substr(get_class($predictionsprocessor), 0, strpos(get_class($predictionsprocessor), '\\', 1));
        return get_string('pluginname', $component);
    }

    /**
     * Whether the provided plugin is used by any model.
     *
     * @param string $plugin
     * @return bool
     */
    public static function is_mlbackend_used($plugin) {
        $models = self::get_all_models();
        foreach ($models as $model) {
            $processor = $model->get_predictions_processor();
            $noprefixnamespace = ltrim(get_class($processor), '\\');
            $processorplugin = substr($noprefixnamespace, 0, strpos($noprefixnamespace, '\\'));
            if ($processorplugin == $plugin) {
                return true;
            }
        }

        // Default predictions processor.
        $defaultprocessorclass = get_config('analytics', 'predictionsprocessor');
        $pluginclass = '\\' . $plugin . '\\processor';
        if ($pluginclass === $defaultprocessorclass) {
            return true;
        }

        return false;
    }

    /**
     * Get all available time splitting methods.
     *
     * @return \core_analytics\local\time_splitting\base[]
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
     * Return all targets in the system.
     *
     * @return \core_analytics\local\target\base[]
     */
    public static function get_all_targets() : array {
        if (self::$alltargets !== null) {
            return self::$alltargets;
        }

        $classes = self::get_analytics_classes('target');

        self::$alltargets = [];
        foreach ($classes as $fullclassname => $classpath) {
            $instance = self::get_target($fullclassname);
            if ($instance) {
                self::$alltargets[$instance->get_id()] = $instance;
            }
        }

        return self::$alltargets;
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
                self::$allindicators[$instance->get_id()] = $instance;
            }
        }

        return self::$allindicators;
    }

    /**
     * Returns the specified target
     *
     * @param mixed $fullclassname
     * @return \core_analytics\local\target\base|false False if it is not valid
     */
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
     * @param string $baseclass
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
     * Returns the logstore used for analytics.
     *
     * @return \core\log\sql_reader|false False if no log stores are enabled.
     */
    public static function get_analytics_logstore() {
        $readers = get_log_manager()->get_readers('core\log\sql_reader');
        $analyticsstore = get_config('analytics', 'logstore');

        if (!empty($analyticsstore) && !empty($readers[$analyticsstore])) {
            $logstore = $readers[$analyticsstore];
        } else if (empty($analyticsstore) && !empty($readers)) {
            // The first one, it is the same default than in settings.
            $logstore = reset($readers);
        } else if (!empty($readers)) {
            $logstore = reset($readers);
            debugging('The selected log store for analytics is not available anymore. Using "' .
                $logstore->get_name() . '"', DEBUG_DEVELOPER);
        }

        if (empty($logstore)) {
            debugging('No system log stores available to use for analytics', DEBUG_DEVELOPER);
            return false;
        }

        if (!$logstore->is_logging()) {
            debugging('The selected log store for analytics "' . $logstore->get_name() .
                '" is not logging activity logs', DEBUG_DEVELOPER);
        }

        return $logstore;
    }

    /**
     * Returns this analysable calculations during the provided period.
     *
     * @param \core_analytics\analysable $analysable
     * @param int $starttime
     * @param int $endtime
     * @param string $samplesorigin The samples origin as sampleid is not unique across models.
     * @return array
     */
    public static function get_indicator_calculations($analysable, $starttime, $endtime, $samplesorigin) {
        global $DB;

        $params = array('starttime' => $starttime, 'endtime' => $endtime, 'contextid' => $analysable->get_context()->id,
            'sampleorigin' => $samplesorigin);
        $calculations = $DB->get_recordset('analytics_indicator_calc', $params, '', 'indicator, sampleid, value');

        $existingcalculations = array();
        foreach ($calculations as $calculation) {
            if (empty($existingcalculations[$calculation->indicator])) {
                $existingcalculations[$calculation->indicator] = array();
            }
            $existingcalculations[$calculation->indicator][$calculation->sampleid] = $calculation->value;
        }
        $calculations->close();
        return $existingcalculations;
    }

    /**
     * Returns the models with insights at the provided context.
     *
     * @param \context $context
     * @return \core_analytics\model[]
     */
    public static function get_models_with_insights(\context $context) {

        self::check_can_list_insights($context);

        $models = self::get_all_models(true, true, $context);
        foreach ($models as $key => $model) {
            // Check that it not only have predictions but also generates insights from them.
            if (!$model->uses_insights()) {
                unset($models[$key]);
            }
        }
        return $models;
    }

    /**
     * Returns a prediction
     *
     * @param int $predictionid
     * @param bool $requirelogin
     * @return array array($model, $prediction, $context)
     */
    public static function get_prediction($predictionid, $requirelogin = false) {
        global $DB;

        if (!$predictionobj = $DB->get_record('analytics_predictions', array('id' => $predictionid))) {
            throw new \moodle_exception('errorpredictionnotfound', 'analytics');
        }

        $context = \context::instance_by_id($predictionobj->contextid, IGNORE_MISSING);
        if (!$context) {
            throw new \moodle_exception('errorpredictioncontextnotavailable', 'analytics');
        }

        if ($requirelogin) {
            list($context, $course, $cm) = get_context_info_array($predictionobj->contextid);
            require_login($course, false, $cm);
        }

        self::check_can_list_insights($context);

        $model = new \core_analytics\model($predictionobj->modelid);
        $sampledata = $model->prediction_sample_data($predictionobj);
        $prediction = new \core_analytics\prediction($predictionobj, $sampledata);

        return array($model, $prediction, $context);
    }

    /**
     * Adds the models included with moodle core to the system.
     *
     * @return void
     */
    public static function add_builtin_models() {

        $target = self::get_target('\core\analytics\target\course_dropout');

        // Community of inquiry indicators.
        $coiindicators = array(
            '\mod_assign\analytics\indicator\cognitive_depth',
            '\mod_assign\analytics\indicator\social_breadth',
            '\mod_book\analytics\indicator\cognitive_depth',
            '\mod_book\analytics\indicator\social_breadth',
            '\mod_chat\analytics\indicator\cognitive_depth',
            '\mod_chat\analytics\indicator\social_breadth',
            '\mod_choice\analytics\indicator\cognitive_depth',
            '\mod_choice\analytics\indicator\social_breadth',
            '\mod_data\analytics\indicator\cognitive_depth',
            '\mod_data\analytics\indicator\social_breadth',
            '\mod_feedback\analytics\indicator\cognitive_depth',
            '\mod_feedback\analytics\indicator\social_breadth',
            '\mod_folder\analytics\indicator\cognitive_depth',
            '\mod_folder\analytics\indicator\social_breadth',
            '\mod_forum\analytics\indicator\cognitive_depth',
            '\mod_forum\analytics\indicator\social_breadth',
            '\mod_glossary\analytics\indicator\cognitive_depth',
            '\mod_glossary\analytics\indicator\social_breadth',
            '\mod_imscp\analytics\indicator\cognitive_depth',
            '\mod_imscp\analytics\indicator\social_breadth',
            '\mod_label\analytics\indicator\cognitive_depth',
            '\mod_label\analytics\indicator\social_breadth',
            '\mod_lesson\analytics\indicator\cognitive_depth',
            '\mod_lesson\analytics\indicator\social_breadth',
            '\mod_lti\analytics\indicator\cognitive_depth',
            '\mod_lti\analytics\indicator\social_breadth',
            '\mod_page\analytics\indicator\cognitive_depth',
            '\mod_page\analytics\indicator\social_breadth',
            '\mod_quiz\analytics\indicator\cognitive_depth',
            '\mod_quiz\analytics\indicator\social_breadth',
            '\mod_resource\analytics\indicator\cognitive_depth',
            '\mod_resource\analytics\indicator\social_breadth',
            '\mod_scorm\analytics\indicator\cognitive_depth',
            '\mod_scorm\analytics\indicator\social_breadth',
            '\mod_survey\analytics\indicator\cognitive_depth',
            '\mod_survey\analytics\indicator\social_breadth',
            '\mod_url\analytics\indicator\cognitive_depth',
            '\mod_url\analytics\indicator\social_breadth',
            '\mod_wiki\analytics\indicator\cognitive_depth',
            '\mod_wiki\analytics\indicator\social_breadth',
            '\mod_workshop\analytics\indicator\cognitive_depth',
            '\mod_workshop\analytics\indicator\social_breadth',
            '\core_course\analytics\indicator\completion_enabled',
            '\core_course\analytics\indicator\potential_cognitive_depth',
            '\core_course\analytics\indicator\potential_social_breadth',
            '\core\analytics\indicator\any_access_after_end',
            '\core\analytics\indicator\any_access_before_start',
            '\core\analytics\indicator\any_write_action_in_course',
            '\core\analytics\indicator\read_actions',
        );
        $indicators = array();
        foreach ($coiindicators as $coiindicator) {
            $indicator = self::get_indicator($coiindicator);
            $indicators[$indicator->get_id()] = $indicator;
        }
        if (!\core_analytics\model::exists($target, $indicators)) {
            $model = \core_analytics\model::create($target, $indicators);
        }

        // No teaching model.
        $target = self::get_target('\core\analytics\target\no_teaching');
        $timesplittingmethod = '\core\analytics\time_splitting\single_range';
        $noteacher = self::get_indicator('\core_course\analytics\indicator\no_teacher');
        $nostudent = self::get_indicator('\core_course\analytics\indicator\no_student');
        $indicators = array($noteacher->get_id() => $noteacher, $nostudent->get_id() => $nostudent);
        if (!\core_analytics\model::exists($target, $indicators)) {
            \core_analytics\model::create($target, $indicators, $timesplittingmethod);
        }
    }

    /**
     * Cleans up analytics db tables that do not directly depend on analysables that may have been deleted.
     */
    public static function cleanup() {
        global $DB;

        // Clean up stuff that depends on contexts that do not exist anymore.
        $sql = "SELECT DISTINCT ap.contextid FROM {analytics_predictions} ap
                  LEFT JOIN {context} ctx ON ap.contextid = ctx.id
                 WHERE ctx.id IS NULL";
        $apcontexts = $DB->get_records_sql($sql);

        $sql = "SELECT DISTINCT aic.contextid FROM {analytics_indicator_calc} aic
                  LEFT JOIN {context} ctx ON aic.contextid = ctx.id
                 WHERE ctx.id IS NULL";
        $indcalccontexts = $DB->get_records_sql($sql);

        $contexts = $apcontexts + $indcalccontexts;
        if ($contexts) {
            list($sql, $params) = $DB->get_in_or_equal(array_keys($contexts));
            $DB->execute("DELETE FROM {analytics_prediction_actions} WHERE predictionid IN
                (SELECT ap.id FROM {analytics_predictions} ap WHERE ap.contextid $sql)", $params);

            $DB->delete_records_select('analytics_predictions', "contextid $sql", $params);
            $DB->delete_records_select('analytics_indicator_calc', "contextid $sql", $params);
        }

        // Clean up stuff that depends on analysable ids that do not exist anymore.
        $models = self::get_all_models();
        foreach ($models as $model) {
            $analyser = $model->get_analyser(array('notimesplitting' => true));
            $analysables = $analyser->get_analysables();
            if (!$analysables) {
                continue;
            }

            $analysableids = array_map(function($analysable) {
                return $analysable->get_id();
            }, $analysables);

            list($notinsql, $params) = $DB->get_in_or_equal($analysableids, SQL_PARAMS_NAMED, 'param', false);
            $params['modelid'] = $model->get_id();

            $DB->delete_records_select('analytics_predict_samples', "modelid = :modelid AND analysableid $notinsql", $params);
            $DB->delete_records_select('analytics_train_samples', "modelid = :modelid AND analysableid $notinsql", $params);
        }
    }

    /**
     * Default system backend.
     *
     * @return string
     */
    public static function default_mlbackend() {
        return self::DEFAULT_MLBACKEND;
    }

    /**
     * Returns the provided element classes in the site.
     *
     * @param string $element
     * @return string[] Array keys are the FQCN and the values the class path.
     */
    private static function get_analytics_classes($element) {

        // Just in case...
        $element = clean_param($element, PARAM_ALPHANUMEXT);

        // Core analytics classes (analytics subsystem should not contain uses of the analytics API).
        $classes = \core_component::get_component_classes_in_namespace('core', 'analytics\\' . $element);

        // Plugins.
        foreach (\core_component::get_plugin_types() as $type => $unusedplugintypepath) {
            foreach (\core_component::get_plugin_list($type) as $pluginname => $unusedpluginpath) {
                $frankenstyle = $type . '_' . $pluginname;
                $classes += \core_component::get_component_classes_in_namespace($frankenstyle, 'analytics\\' . $element);
            }
        }

        // Core subsystems.
        foreach (\core_component::get_core_subsystems() as $subsystemname => $unusedsubsystempath) {
            $componentname = 'core_' . $subsystemname;
            $classes += \core_component::get_component_classes_in_namespace($componentname, 'analytics\\' . $element);
        }

        return $classes;
    }
}
