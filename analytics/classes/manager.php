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
     * Name of the file where components declare their models.
     */
    const ANALYTICS_FILENAME = 'db/analytics.php';

    /**
     * @var \core_analytics\predictor[]
     */
    protected static $predictionprocessors = [];

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
     * @param  bool $return The method returns a bool if true.
     * @return void
     */
    public static function check_can_list_insights(\context $context, bool $return = false) {
        global $USER;

        if ($context->contextlevel === CONTEXT_USER && $context->instanceid == $USER->id) {
            $capability = 'moodle/analytics:listowninsights';
        } else {
            $capability = 'moodle/analytics:listinsights';
        }

        if ($return) {
            return has_capability($capability, $context);
        } else {
            require_capability($capability, $context);
        }
    }

    /**
     * Is analytics enabled globally?
     *
     * return bool
     */
    public static function is_analytics_enabled(): bool {
        global $CFG;

        if (isset($CFG->enableanalytics)) {
            return $CFG->enableanalytics;
        }

        // Enabled by default.
        return true;
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
                $conditions[] = "EXISTS (SELECT 'x'
                                           FROM {analytics_predictions} ap
                                          WHERE ap.modelid = am.id AND ap.contextid = :contextid)";
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

        // Sort the models by the model name using the current session language.
        \core_collator::asort_objects_by_method($models, 'get_name');

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
     * Resets the cached prediction processors.
     * @return null
     */
    public static function reset_prediction_processors() {
        self::$predictionprocessors = [];
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
     * @deprecated since Moodle 3.7
     * @todo MDL-65086 This will be deleted in Moodle 4.1
     * @see \core_analytics\manager::get_time_splitting_methods_for_evaluation
     * @return \core_analytics\local\time_splitting\base[]
     */
    public static function get_enabled_time_splitting_methods() {
        debugging('This function has been deprecated. You can use self::get_time_splitting_methods_for_evaluation if ' .
            'you want to get the default time splitting methods for evaluation, or you can use self::get_all_time_splittings if ' .
            'you want to get all the time splitting methods available on this site.');
        return self::get_time_splitting_methods_for_evaluation();
    }

    /**
     * Returns the time-splitting methods for model evaluation.
     *
     * @param  bool $all Return all the time-splitting methods that can potentially be used for evaluation or the default ones.
     * @return \core_analytics\local\time_splitting\base[]
     */
    public static function get_time_splitting_methods_for_evaluation(bool $all = false) {

        if ($all === false) {
            if ($enabledtimesplittings = get_config('analytics', 'defaulttimesplittingsevaluation')) {
                $enabledtimesplittings = array_flip(explode(',', $enabledtimesplittings));
            }
        }

        $timesplittings = self::get_all_time_splittings();
        foreach ($timesplittings as $key => $timesplitting) {

            if (!$timesplitting->valid_for_evaluation()) {
                unset($timesplittings[$key]);
            }

            if ($all === false) {
                // We remove the ones that are not enabled. This also respects the default value (all methods enabled).
                if (!empty($enabledtimesplittings) && !isset($enabledtimesplittings[$key])) {
                    unset($timesplittings[$key]);
                }
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
     * Note that this method is used for display purposes. It filters out models whose insights
     * are not linked from the reports page.
     *
     * @param \context $context
     * @return \core_analytics\model[]
     */
    public static function get_models_with_insights(\context $context) {

        self::check_can_list_insights($context);

        $models = self::get_all_models(true, true, $context);
        foreach ($models as $key => $model) {
            // Check that it not only have predictions but also generates insights from them.
            if (!$model->uses_insights() || !$model->get_target()->link_insights_report()) {
                unset($models[$key]);
            }
        }
        return $models;
    }

    /**
     * Returns the models that generated insights in the provided context. It can also be used to add new models to the context.
     *
     * Note that if you use this function with $newmodelid is the caller responsibility to ensure that the
     * provided model id generated insights for the provided context.
     *
     * @throws \coding_exception
     * @param  \context $context
     * @param  int|null $newmodelid A new model to add to the list of models with insights in the provided context.
     * @return int[]
     */
    public static function cached_models_with_insights(\context $context, int $newmodelid = null) {

        $cache = \cache::make('core', 'contextwithinsights');
        $modelids = $cache->get($context->id);
        if ($modelids === false) {
            // The cache is empty, but we don't know if it is empty because there are no insights
            // in this context or because cache/s have been purged, we need to be conservative and
            // "pay" 1 db read to fill up the cache.

            $models = \core_analytics\manager::get_models_with_insights($context);

            if ($newmodelid && empty($models[$newmodelid])) {
                throw new \coding_exception('The provided modelid ' . $newmodelid . ' did not generate any insights');
            }

            $modelids = array_keys($models);
            $cache->set($context->id, $modelids);

        } else if ($newmodelid && !in_array($newmodelid, $modelids)) {
            // We add the context we got as an argument to the cache.

            array_push($modelids, $newmodelid);
            $cache->set($context->id, $modelids);
        }

        return $modelids;
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
     * Used to be used to add models included with the Moodle core.
     *
     * @deprecated Deprecated since Moodle 3.7 (MDL-61667) - Use lib/db/analytics.php instead.
     * @todo Remove this method in Moodle 4.1 (MDL-65186).
     * @return void
     */
    public static function add_builtin_models() {

        debugging('core_analytics\manager::add_builtin_models() has been deprecated. Core models are now automatically '.
            'updated according to their declaration in the lib/db/analytics.php file.', DEBUG_DEVELOPER);
    }

    /**
     * Cleans up analytics db tables that do not directly depend on analysables that may have been deleted.
     */
    public static function cleanup() {
        global $DB;

        $DB->execute("DELETE FROM {analytics_prediction_actions} WHERE predictionid IN
                          (SELECT ap.id FROM {analytics_predictions} ap
                        LEFT JOIN {context} ctx ON ap.contextid = ctx.id
                            WHERE ctx.id IS NULL)");

        $contextsql = "SELECT id FROM {context} ctx";
        $DB->delete_records_select('analytics_predictions', "contextid NOT IN ($contextsql)");
        $DB->delete_records_select('analytics_indicator_calc', "contextid NOT IN ($contextsql)");

        // Clean up stuff that depends on analysable ids that do not exist anymore.

        $models = self::get_all_models();
        foreach ($models as $model) {

            // We first dump into memory the list of analysables we have in the database (we could probably do this with 1 single
            // query for the 3 tables, but it may be safer to do it separately).
            $predictsamplesanalysableids = $DB->get_fieldset_select('analytics_predict_samples', 'DISTINCT analysableid',
                'modelid = :modelid', ['modelid' => $model->get_id()]);
            $predictsamplesanalysableids = array_flip($predictsamplesanalysableids);
            $trainsamplesanalysableids = $DB->get_fieldset_select('analytics_train_samples', 'DISTINCT analysableid',
                'modelid = :modelid', ['modelid' => $model->get_id()]);
            $trainsamplesanalysableids = array_flip($trainsamplesanalysableids);
            $usedanalysablesanalysableids = $DB->get_fieldset_select('analytics_used_analysables', 'DISTINCT analysableid',
                'modelid = :modelid', ['modelid' => $model->get_id()]);
            $usedanalysablesanalysableids = array_flip($usedanalysablesanalysableids);

            $analyser = $model->get_analyser(array('notimesplitting' => true));

            // We do not honour the list of contexts in this model as it can contain stale records.
            $analysables = $analyser->get_analysables_iterator();

            $analysableids = [];
            foreach ($analysables as $analysable) {
                if (!$analysable) {
                    continue;
                }
                unset($predictsamplesanalysableids[$analysable->get_id()]);
                unset($trainsamplesanalysableids[$analysable->get_id()]);
                unset($usedanalysablesanalysableids[$analysable->get_id()]);
            }

            $param = ['modelid' => $model->get_id()];

            if ($predictsamplesanalysableids) {
                list($idssql, $idsparams) = $DB->get_in_or_equal(array_flip($predictsamplesanalysableids), SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_predict_samples', "modelid = :modelid AND analysableid $idssql",
                    $param + $idsparams);
            }
            if ($trainsamplesanalysableids) {
                list($idssql, $idsparams) = $DB->get_in_or_equal(array_flip($trainsamplesanalysableids), SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_train_samples', "modelid = :modelid AND analysableid $idssql",
                    $param + $idsparams);
            }
            if ($usedanalysablesanalysableids) {
                list($idssql, $idsparams) = $DB->get_in_or_equal(array_flip($usedanalysablesanalysableids), SQL_PARAMS_NAMED);
                $DB->delete_records_select('analytics_used_analysables', "modelid = :modelid AND analysableid $idssql",
                    $param + $idsparams);
            }
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

        $classes = \core_component::get_component_classes_in_namespace(null, 'analytics\\' . $element);

        return $classes;
    }

    /**
     * Check that all the models declared by the component are up to date.
     *
     * This is intended to be called during the installation / upgrade to automatically create missing models.
     *
     * @param string $componentname The name of the component to load models for.
     * @return array \core_analytics\model[] List of actually created models.
     */
    public static function update_default_models_for_component(string $componentname): array {

        $result = [];

        foreach (static::load_default_models_for_component($componentname) as $definition) {
            if (!\core_analytics\model::exists(static::get_target($definition['target']))) {
                $result[] = static::create_declared_model($definition);
            }
        }

        return $result;
    }

    /**
     * Return the list of models declared by the given component.
     *
     * @param string $componentname The name of the component to load models for.
     * @throws \coding_exception Exception thrown in case of invalid syntax.
     * @return array The $models description array.
     */
    public static function load_default_models_for_component(string $componentname): array {

        $dir = \core_component::get_component_directory($componentname);

        if (!$dir) {
            // This is either an invalid component, or a core subsystem without its own root directory.
            return [];
        }

        $file = $dir . '/' . self::ANALYTICS_FILENAME;

        if (!is_readable($file)) {
            return [];
        }

        $models = null;
        include($file);

        if (!isset($models) || !is_array($models) || empty($models)) {
            return [];
        }

        foreach ($models as &$model) {
            if (!isset($model['enabled'])) {
                $model['enabled'] = false;
            } else {
                $model['enabled'] = clean_param($model['enabled'], PARAM_BOOL);
            }
        }

        static::validate_models_declaration($models);

        return $models;
    }

    /**
     * Return the list of all the models declared anywhere in this Moodle installation.
     *
     * Models defined by the core and core subsystems come first, followed by those provided by plugins.
     *
     * @return array indexed by the frankenstyle component
     */
    public static function load_default_models_for_all_components(): array {

        $tmp = [];

        foreach (\core_component::get_component_list() as $type => $components) {
            foreach (array_keys($components) as $component) {
                if ($loaded = static::load_default_models_for_component($component)) {
                    $tmp[$type][$component] = $loaded;
                }
            }
        }

        $result = [];

        if ($loaded = static::load_default_models_for_component('core')) {
            $result['core'] = $loaded;
        }

        if (!empty($tmp['core'])) {
            $result += $tmp['core'];
            unset($tmp['core']);
        }

        foreach ($tmp as $components) {
            $result += $components;
        }

        return $result;
    }

    /**
     * Validate the declaration of prediction models according the syntax expected in the component's db folder.
     *
     * The expected structure looks like this:
     *
     *  [
     *      [
     *          'target' => '\fully\qualified\name\of\the\target\class',
     *          'indicators' => [
     *              '\fully\qualified\name\of\the\first\indicator',
     *              '\fully\qualified\name\of\the\second\indicator',
     *          ],
     *          'timesplitting' => '\optional\name\of\the\time_splitting\class',
     *          'enabled' => true,
     *      ],
     *  ];
     *
     * @param array $models List of declared models.
     * @throws \coding_exception Exception thrown in case of invalid syntax.
     */
    public static function validate_models_declaration(array $models) {

        foreach ($models as $model) {
            if (!isset($model['target'])) {
                throw new \coding_exception('Missing target declaration');
            }

            if (!static::is_valid($model['target'], '\core_analytics\local\target\base')) {
                throw new \coding_exception('Invalid target classname', $model['target']);
            }

            if (empty($model['indicators']) || !is_array($model['indicators'])) {
                throw new \coding_exception('Missing indicators declaration');
            }

            foreach ($model['indicators'] as $indicator) {
                if (!static::is_valid($indicator, '\core_analytics\local\indicator\base')) {
                    throw new \coding_exception('Invalid indicator classname', $indicator);
                }
            }

            if (isset($model['timesplitting'])) {
                if (substr($model['timesplitting'], 0, 1) !== '\\') {
                    throw new \coding_exception('Expecting fully qualified time splitting classname', $model['timesplitting']);
                }
                if (!static::is_valid($model['timesplitting'], '\core_analytics\local\time_splitting\base')) {
                    throw new \coding_exception('Invalid time splitting classname', $model['timesplitting']);
                }
            }

            if (!empty($model['enabled']) && !isset($model['timesplitting'])) {
                throw new \coding_exception('Cannot enable a model without time splitting method specified');
            }
        }
    }

    /**
     * Create the defined model.
     *
     * @param array $definition See {@link self::validate_models_declaration()} for the syntax.
     * @return \core_analytics\model
     */
    public static function create_declared_model(array $definition): \core_analytics\model {

        list($target, $indicators) = static::get_declared_target_and_indicators_instances($definition);

        if (isset($definition['timesplitting'])) {
            $timesplitting = $definition['timesplitting'];
        } else {
            $timesplitting = false;
        }

        $created = \core_analytics\model::create($target, $indicators, $timesplitting);

        if (!empty($definition['enabled'])) {
            $created->enable();
        }

        return $created;
    }

    /**
     * Returns a string uniquely representing the given model declaration.
     *
     * @param array $model Model declaration
     * @return string complying with PARAM_ALPHANUM rules and starting with an 'id' prefix
     */
    public static function model_declaration_identifier(array $model) : string {
        return 'id'.sha1(serialize($model));
    }

    /**
     * Given a model definition, return actual target and indicators instances.
     *
     * @param array $definition See {@link self::validate_models_declaration()} for the syntax.
     * @return array [0] => target instance, [1] => array of indicators instances
     */
    public static function get_declared_target_and_indicators_instances(array $definition): array {

        $target = static::get_target($definition['target']);

        $indicators = [];

        foreach ($definition['indicators'] as $indicatorname) {
            $indicator = static::get_indicator($indicatorname);
            $indicators[$indicator->get_id()] = $indicator;
        }

        return [$target, $indicators];
    }

    /**
     * Return the context restrictions that can be applied to the provided context levels.
     *
     * @throws \coding_exception
     * @param  array|null $contextlevels The list of context levels provided by the analyser. Null if all of them.
     * @param  string|null $query
     * @return array Associative array with contextid as key and the short version of the context name as value.
     */
    public static function get_potential_context_restrictions(?array $contextlevels = null, string $query = null) {
        global $DB;

        if (empty($contextlevels) && !is_null($contextlevels)) {
            return false;
        }

        if (!is_null($contextlevels)) {
            foreach ($contextlevels as $contextlevel) {
                if ($contextlevel !== CONTEXT_COURSE && $contextlevel !== CONTEXT_COURSECAT) {
                    throw new \coding_exception('Only CONTEXT_COURSE and CONTEXT_COURSECAT are supported at the moment.');
                }
            }
        }

        $contexts = [];

        // We have a separate process for each context level for performance reasons (to iterate through mdl_context calling
        // get_context_name() would be too slow).
        $contextsystem = \context_system::instance();
        if (is_null($contextlevels) || in_array(CONTEXT_COURSECAT, $contextlevels)) {

            $sql = "SELECT cc.id, cc.name, ctx.id AS contextid
                      FROM {course_categories} cc
                      JOIN {context} ctx ON ctx.contextlevel = :ctxlevel AND ctx.instanceid = cc.id";
            $params = ['ctxlevel' => CONTEXT_COURSECAT];

            if ($query) {
                $sql .= " WHERE " . $DB->sql_like('cc.name', ':query', false, false);
                $params['query'] = '%' . $query . '%';
            }

            $coursecats = $DB->get_recordset_sql($sql, $params);
            foreach ($coursecats as $record) {
                $contexts[$record->contextid] = get_string('category') . ': ' .
                    format_string($record->name, true, array('context' => $contextsystem));
            }
            $coursecats->close();
        }

        if (is_null($contextlevels) || in_array(CONTEXT_COURSE, $contextlevels)) {

            $sql = "SELECT c.id, c.shortname, ctx.id AS contextid
                      FROM {course} c
                      JOIN {context} ctx ON ctx.contextlevel = :ctxlevel AND ctx.instanceid = c.id
                      WHERE c.id != :siteid";
            $params = ['ctxlevel' => CONTEXT_COURSE, 'siteid' => SITEID];

            if ($query) {
                $sql .= ' AND (' . $DB->sql_like('c.fullname', ':query1', false, false) . ' OR ' .
                    $DB->sql_like('c.shortname', ':query2', false, false) . ')';
                $params['query1'] = '%' . $query . '%';
                $params['query2'] = '%' . $query . '%';
            }

            $courses = $DB->get_recordset_sql($sql, $params);
            foreach ($courses as $record) {
                $contexts[$record->contextid] = get_string('course') . ': ' .
                    format_string($record->shortname, true, array('context' => $contextsystem));
            }
            $courses->close();
        }

        return $contexts;
    }

}
