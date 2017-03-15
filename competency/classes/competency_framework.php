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
 * Class for loading/storing competency frameworks from the DB.
 *
 * @package    core_competency
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_competency;
defined('MOODLE_INTERNAL') || die();

use coding_exception;
use context;
use lang_string;
use stdClass;

require_once($CFG->libdir . '/grade/grade_scale.php');
require_once($CFG->dirroot . '/local/iomad/lib/iomad.php');

/**
 * Class for loading/storing competency frameworks from the DB.
 *
 * @copyright  2015 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class competency_framework extends persistent {

    const TABLE = 'competency_framework';

    /** Taxonomy constant. */
    const TAXONOMY_BEHAVIOUR = 'behaviour';
    /** Taxonomy constant. */
    const TAXONOMY_COMPETENCY = 'competency';
    /** Taxonomy constant. */
    const TAXONOMY_CONCEPT = 'concept';
    /** Taxonomy constant. */
    const TAXONOMY_DOMAIN = 'domain';
    /** Taxonomy constant. */
    const TAXONOMY_INDICATOR = 'indicator';
    /** Taxonomy constant. */
    const TAXONOMY_LEVEL = 'level';
    /** Taxonomy constant. */
    const TAXONOMY_OUTCOME = 'outcome';
    /** Taxonomy constant. */
    const TAXONOMY_PRACTICE = 'practice';
    /** Taxonomy constant. */
    const TAXONOMY_PROFICIENCY = 'proficiency';
    /** Taxonomy constant. */
    const TAXONOMY_SKILL = 'skill';
    /** Taxonomy constant. */
    const TAXONOMY_VALUE = 'value';

    /** @var static The object before it was updated. */
    protected $beforeupdate;

    /**
     * Get the context.
     *
     * @return context The context
     */
    public function get_context() {
        return context::instance_by_id($this->get('contextid'));
    }

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'shortname' => array(
                'type' => PARAM_TEXT
            ),
            'idnumber' => array(
                'type' => PARAM_RAW
            ),
            'description' => array(
                'type' => PARAM_CLEANHTML,
                'default' => ''
            ),
            'descriptionformat' => array(
                'choices' => array(FORMAT_HTML, FORMAT_MOODLE, FORMAT_PLAIN, FORMAT_MARKDOWN),
                'type' => PARAM_INT,
                'default' => FORMAT_HTML
            ),
            'visible' => array(
                'type' => PARAM_BOOL,
                'default' => 1
            ),
            'scaleid' => array(
                'type' => PARAM_INT
            ),
            'scaleconfiguration' => array(
                'type' => PARAM_RAW
            ),
            'contextid' => array(
                'type' => PARAM_INT
            ),
            'taxonomies' => array(
                'type' => PARAM_RAW,
                'default' => ''
            )
        );
    }

    /**
     * Hook to execute before validate.
     *
     * @return void
     */
    protected function before_validate() {
        $this->beforeupdate = null;

        // During update.
        if ($this->get('id')) {
            $this->beforeupdate = new competency_framework($this->get('id'));
        }

    }

    /**
     * Return the current depth of a competency framework.
     *
     * @see competency::get_framework_depth()
     * @return int
     */
    public function get_depth() {
        return competency::get_framework_depth($this->get('id'));
    }

    /**
     * Return the scale.
     *
     * @return \grade_scale
     */
    public function get_scale() {
        $scale = \grade_scale::fetch(array('id' => $this->get('scaleid')));
        $scale->load_items();
        return $scale;
    }

    /**
     * Get the constant name for a level.
     *
     * @param  int $level The level of the term.
     * @return string
     */
    public function get_taxonomy($level) {
        $taxonomies = $this->get_taxonomies();

        if (empty($taxonomies[$level])) {
            // If for some reason we cannot find the level, we fallback onto competency.
            $constant = self::TAXONOMY_COMPETENCY;
        } else {
            $constant = $taxonomies[$level];
        }

        return $constant;
    }

    /**
     * Return the taxonomy constants indexed by level.
     *
     * @return array Contains the list of taxonomy constants indexed by level.
     */
    protected function get_taxonomies() {
        $taxonomies = explode(',', $this->raw_get('taxonomies'));

        // Indexing first level at 1.
        array_unshift($taxonomies, null);
        unset($taxonomies[0]);

        // Ensure that we do not return empty levels.
        foreach ($taxonomies as $i => $taxonomy) {
            if (empty($taxonomy)) {
                $taxonomies[$i] = self::TAXONOMY_COMPETENCY;
            }
        }

        return $taxonomies;
    }

    /**
     * Returns true when some competencies of the framework have user competencies.
     *
     * This is useful to determine if the framework, or part of it, should be locked down.
     *
     * @return boolean
     */
    public function has_user_competencies() {
        return user_competency::has_records_for_framework($this->get('id')) ||
            user_competency_plan::has_records_for_framework($this->get('id'));
    }

    /**
     * Convenience method to set taxonomies from an array or string.
     *
     * @param string|array $taxonomies A string, or an array where the values are the term constants.
     */
    protected function set_taxonomies($taxonomies) {
        if (is_array($taxonomies)) {
            $taxonomies = implode(',', $taxonomies);
        }
        $this->raw_set('taxonomies', $taxonomies);
    }

    /**
     * Validate the context ID.
     *
     * @param  int $value The context ID.
     * @return bool|lang_string
     */
    protected function validate_contextid($value) {
        global $DB;

        $context = context::instance_by_id($value, IGNORE_MISSING);
        if (!$context) {
            return new lang_string('invalidcontext', 'error');
        } else if ($context->contextlevel != CONTEXT_SYSTEM && $context->contextlevel != CONTEXT_COURSECAT) {
            return new lang_string('invalidcontext', 'error');
        }

        // During update.
        if ($this->get('id')) {

            // The context must never change.
            $oldcontextid = $DB->get_field(self::TABLE, 'contextid', array('id' => $this->get('id')), MUST_EXIST);
            if ($this->get('contextid') != $oldcontextid) {
                return new lang_string('invalidcontext', 'error');
            }
        }

        return true;
    }

    /**
     * Validate the id number.
     *
     * @param  string $value The id number.
     * @return bool|lang_string
     */
    protected function validate_idnumber($value) {
        global $DB;

        $params = array(
            'id' => $this->get('id'),
            'idnumber' => $value,
        );

        if ($DB->record_exists_select(self::TABLE, 'idnumber = :idnumber AND id <> :id', $params)) {
            return new lang_string('idnumbertaken', 'error');
        }

        return true;
    }

    /**
     * Validate the scale ID.
     *
     * @param  string $value The scale ID.
     * @return bool|lang_string
     */
    protected function validate_scaleid($value) {
        global $DB;

        // Always validate that the scale exists.
        if (!$DB->record_exists_select('scale', 'id = :id', array('id' => $value))) {
            return new lang_string('invalidscaleid', 'error');
        }

        // During update.
        if ($this->get('id')) {

            // Validate that we can only change the scale when it is not used yet.
            if ($this->beforeupdate->get('scaleid') != $value) {
                if ($this->beforeupdate->has_user_competencies()) {
                    return new lang_string('errorscalealreadyused', 'core_competency');
                }
            }

        }

        return true;
    }

    /**
     * Validate the scale configuration.
     *
     * @param  string $value The scale configuration.
     * @return bool|lang_string
     */
    protected function validate_scaleconfiguration($value) {
        $scaledefaultselected = false;
        $proficientselected = false;
        $scaleconfigurations = json_decode($value);

        if (is_array($scaleconfigurations)) {

            // The first element of the array contains the scale ID.
            $scaleinfo = array_shift($scaleconfigurations);
            if (empty($scaleinfo) || !isset($scaleinfo->scaleid) || $scaleinfo->scaleid != $this->get('scaleid')) {
                // This should never happen.
                return new lang_string('errorscaleconfiguration', 'core_competency');
            }

            // Walk through the array to find proficient and default values.
            foreach ($scaleconfigurations as $scaleconfiguration) {
                if (isset($scaleconfiguration->scaledefault) && $scaleconfiguration->scaledefault) {
                    $scaledefaultselected = true;
                }
                if (isset($scaleconfiguration->proficient) && $scaleconfiguration->proficient) {
                    $proficientselected = true;
                }
            }
        }

        if (!$scaledefaultselected || !$proficientselected) {
            return new lang_string('errorscaleconfiguration', 'core_competency');
        }

        return true;
    }

    /**
     * Validate taxonomies.
     *
     * @param  mixed $value The taxonomies.
     * @return true|lang_string
     */
    protected function validate_taxonomies($value) {
        $terms = explode(',', $value);

        foreach ($terms as $term) {
            if (!empty($term) && !array_key_exists($term, self::get_taxonomies_list())) {
                return new lang_string('invalidtaxonomy', 'core_competency', $term);
            }
        }

        return true;
    }

    /**
     * Extract the default grade from a scale configuration.
     *
     * Returns an array where the first element is the grade, and the second
     * is a boolean representing whether or not this grade is considered 'proficient'.
     *
     * @param  string $config JSON encoded config.
     * @return array(int grade, int proficient)
     */
    public static function get_default_grade_from_scale_configuration($config) {
        $config = json_decode($config);
        if (!is_array($config)) {
            throw new coding_exception('Unexpected scale configuration.');
        }

        // Remove the scale ID from the config.
        array_shift($config);

        foreach ($config as $part) {
            if ($part->scaledefault) {
                return array((int) $part->id, (int) $part->proficient);
            }
        }

        throw new coding_exception('Invalid scale configuration, default not found.');
    }

    /**
     * Extract the proficiency of a grade from a scale configuration.
     *
     * @param  string $config JSON encoded config.
     * @param  int $grade The grade.
     * @return int Representing a boolean
     */
    public static function get_proficiency_of_grade_from_scale_configuration($config, $grade) {
        $config = json_decode($config);
        if (!is_array($config)) {
            throw new coding_exception('Unexpected scale configuration.');
        }

        // Remove the scale ID from the config.
        array_shift($config);

        foreach ($config as $part) {
            if ($part->id == $grade) {
                return (int) $part->proficient;
            }
        }

        return 0;
    }

    /**
     * Get the string of a taxonomy from a constant
     *
     * @param  string $constant The taxonomy constant.
     * @return lang_string
     */
    public static function get_taxonomy_from_constant($constant) {
        return self::get_taxonomies_list()[$constant];
    }

    /**
     * Get the list of all taxonomies.
     *
     * @return array Where the key is the taxonomy constant, and the value its translation.
     */
    public static function get_taxonomies_list() {
        static $list = null;

        // At some point we'll have to switch to not using static cache, mainly for Unit Tests in case we
        // decide to allow more taxonomies to be added dynamically from a CFG variable for instance.
        if ($list === null) {
            $list = array(
                self::TAXONOMY_BEHAVIOUR => new lang_string('taxonomy_' . self::TAXONOMY_BEHAVIOUR, 'core_competency'),
                self::TAXONOMY_COMPETENCY => new lang_string('taxonomy_' . self::TAXONOMY_COMPETENCY, 'core_competency'),
                self::TAXONOMY_CONCEPT => new lang_string('taxonomy_' . self::TAXONOMY_CONCEPT, 'core_competency'),
                self::TAXONOMY_DOMAIN => new lang_string('taxonomy_' . self::TAXONOMY_DOMAIN, 'core_competency'),
                self::TAXONOMY_INDICATOR => new lang_string('taxonomy_' . self::TAXONOMY_INDICATOR, 'core_competency'),
                self::TAXONOMY_LEVEL => new lang_string('taxonomy_' . self::TAXONOMY_LEVEL, 'core_competency'),
                self::TAXONOMY_OUTCOME => new lang_string('taxonomy_' . self::TAXONOMY_OUTCOME, 'core_competency'),
                self::TAXONOMY_PRACTICE => new lang_string('taxonomy_' . self::TAXONOMY_PRACTICE, 'core_competency'),
                self::TAXONOMY_PROFICIENCY => new lang_string('taxonomy_' . self::TAXONOMY_PROFICIENCY, 'core_competency'),
                self::TAXONOMY_SKILL => new lang_string('taxonomy_' . self::TAXONOMY_SKILL, 'core_competency'),
                self::TAXONOMY_VALUE => new lang_string('taxonomy_' . self::TAXONOMY_VALUE, 'core_competency'),
            );
        }

        return $list;
    }

    /**
     * Get a uniq idnumber.
     *
     * @param string $idnumber the framework idnumber
     * @return string
     */
    public static function get_unused_idnumber($idnumber) {
        global $DB;

        $currentidnumber = $idnumber;
        $counter = 0;
        // Iteratere while the idnumber exists.
        while ($DB->record_exists_select(static::TABLE, 'idnumber = ?', array($currentidnumber))) {
            $suffixidnumber = '_' . ++$counter;
            $currentidnumber = substr($idnumber, 0, 100 - strlen($suffixidnumber)).$suffixidnumber;
        }

        // Return the uniq idnumber.
        return $currentidnumber;
    }

    /**
     * Whether or not the current user can manage the framework.
     *
     * @return bool
     */
    public function can_manage() {
        return self::can_manage_context($this->get_context());
    }

    /**
     * Whether or not the current user can manage the framework.
     *
     * @param  context $context
     * @return bool
     */
    public static function can_manage_context($context) {
        return \iomad::has_capability('moodle/competency:competencymanage', $context);
    }

    /**
     * Whether or not the current user can read the framework.
     *
     * @return bool
     */
    public function can_read() {
        return self::can_read_context($this->get_context());
    }

    /**
     * Whether or not the current user can read the framework.
     *
     * @param  context $context
     * @return bool
     */
    public static function can_read_context($context) {
        return \iomad::has_capability('moodle/competency:competencyview', $context) || self::can_manage_context($context);
    }

}
