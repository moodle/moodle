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


/*
 * Class used to store information in the question attempt step data
 *
 * This class is useful for:
 *  - No intentar avaluar una pregunta si ha fallat en 3 intents seguits
 *    en les mateixes condicions. Útil en exàmens.
 *  - Guardar info en un "step" durant l'avaluació, perquè en aquest moment
 *    l'step és read-only.
 * **/

defined('MOODLE_INTERNAL') || die();

class qtype_wirisstep {
    const MAX_ATTEMPS_SHORTANSWER_WIRIS = 5;

    /** @var ?question_attempt_step */
    private $step;
    private $stepid;
    private $extraprefix;

    public function __construct() {
        $this->step = null;
    }

    public function load($step) {
        $notreadonly = !($step instanceof question_attempt_step_read_only);
        $notadapterofreadonly =
            !($step instanceof question_attempt_step_subquestion_adapter_wiris && $step->is_adapter_of_read_only());

        if ($notreadonly && $notadapterofreadonly) {
            $this->step = $step;
            // It is a regrade or the first attempt.
            try {
                $this->reset_attempts();
                return;
            } catch (moodle_exception $ex) {
                // We assume it is read only so continue.
                $this->step = null;
            }
        }

        $s = var_export($step, true);
        if (isset($step->get_id)) {
            // Moodle 2.3 or superior.
            $this->stepid = $step->get_id();
        } else {
            // Moodle 2.2.
            if (preg_match("/'id' *=> *'(.*)'/", $s, $matches)) {
                $this->stepid = $matches[1];
            } else {
                $this->stepid = 0;
            }
        }
        if (preg_match("/'extraprefix' *=> *'(.*)'/", $s, $matches)) {
            $this->extraprefix = $matches[1];
        } else {
            $this->extraprefix = "";
        }
    }

    /**
     * Sets a attempt step data value
     * @global type $CFG
     * @param type $name the name of the property to set
     * @param type $value the value
     * @param type $quizBool whether it is question level or subquestion level
     * @throws dml_exception
     */
    public function set_var($name, $value, $subquesbool = true) {
        $name = $this->trim_name($name, $subquesbool);

        if ($subquesbool && $this->step != null) {
            $this->step->set_qt_var($name, $value);
            return;
        }

        if (!isset($this->stepid) || $this->stepid == 0) {
            // It doees not exist, do not even try to find in the db.
            return null;
        }

        $DB = $this->get_db();

        $name = $this->get_step_var_internal($name, $subquesbool);

        $gc = $DB->get_record('question_attempt_step_data', array('attemptstepid' => $this->stepid, 'name' => $name), 'value');
        if ($gc == null) {
            $gc = new stdClass();
            $gc->attemptstepid = $this->stepid;
            $gc->name = $name;
            $gc->value = $value;
            $DB->insert_record('question_attempt_step_data', $gc);
        } else {
            $DB->set_field(
                'question_attempt_step_data',
                'value',
                $value,
                array('attemptstepid' => $this->stepid, 'name' => $name)
            );
        }
    }

    public function set_var_in_answer_cache(string $name, string $value, string $answer) {
        $this->put_answer_in_cache($answer);

        $hash = md5($answer);
        $this->set_var('_' . substr($hash, 0, 6) . $name, $value, true);
    }

    public function get_var_in_answer_cache(string $name, string $answer) {
        $responsehash = md5($answer);

        $data = $this->get_var('_' . substr($responsehash, 0, 6) . $name);

        if (empty($data)) {
            $data = $this->get_var($name);
        }

        return $data;
    }

    public function put_answer_in_cache(string $answer) {
        if ($this->is_answer_cached($answer)) {
            return;
        }

        $hash = md5($answer);
        $cache = $this->get_var('_response_hash') ?? '';

        $this->set_var('_response_hash', $cache ? ($cache . ',' . $hash) : $hash, true);
    }

    public function is_answer_cached(string $answer): bool {
        $cachedresponses = $this->get_var('_response_hash') ?? '';
        $responsehash = md5($answer);

        return strpos($cachedresponses, $responsehash) !== false;
    }

    private function trim_name(string $name, bool $subquesbool) {
        while ($this->get_name_length($name, $subquesbool) > 32) {
            $name = substr($name, 0, -1);
        }
        return $name;
    }

    private function get_name_length(string $name, bool $subquesbool) {
        return strlen(
            $this->step instanceof question_attempt_step_subquestion_adapter ?
                $this->step->add_prefix($name) :
                $this->get_step_var_internal($name, $subquesbool)
        );
    }

    public function get_qt_data() {
        if ($this->step != null) {
            return $this->step->get_qt_data();
        } else {
            $DB = $this->get_db();
        }
    }

    public function get_var(string $name, bool $subquesbool = true) {
        $name = $this->trim_name($name, $subquesbool);

        if ($subquesbool && $this->step != null) {
            $value = $this->step->get_qt_var($name);
            return $value;
        }

        if (!isset($this->stepid) || $this->stepid == 0) {
            // It doees not exist, do not even try to find in the db.
            return null;
        }

        $DB = $this->get_db();

        $name = $this->get_step_var_internal($name, $subquesbool);

        $gc = $DB->get_record('question_attempt_step_data', array('attemptstepid' => $this->stepid, 'name' => $name), 'value');
        if ($gc == null) {
            $r = null;
        } else {
            $r = $gc->value;
        }

        return $r;
    }

    private function get_step_var_internal($name, $subquesbool) {
        if ($subquesbool && isset($this->extraprefix) && strlen($this->extraprefix) > 0) {
            // The prefix is needed when it is a subquestion of a cloze
            // (multianswer) question type.
            if (substr($name, 0, 2) === '!_') {
                return '-_' . $this->extraprefix . substr($name, 2);
            } else if (substr($name, 0, 1) === '-') {
                return '-' . $this->extraprefix . substr($name, 1);
            } else if (substr($name, 0, 1) === '_') {
                return '_' . $this->extraprefix . substr($name, 1);
            } else {
                return $this->extraprefix . $name;
            }
        } else {
            return $name;
        }
    }

    private function get_db() {
        global $wirisdb;
        if (!isset($wirisdb)) {
            global $CFG;
            // Create a new connection because the default one is inside a transaction
            // and a rollback is done afterwards.
            if (!$wirisdb = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary)) {
                throw new dml_exception('dbdriverproblem', "Unknown driver $CFG->dblibrary/$CFG->dbtype");
            }
            $wirisdb->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->prefix, $CFG->dboptions);
        }
        return $wirisdb;
    }

    /**
     * Returns whether the max number of attempts limit is reached
     */
    public function is_attempt_limit_reached() {
        $c = $this->get_var('_gc', false);
        if (is_null($c)) {
            return false;
        }

        $isreached = $c >= self::MAX_ATTEMPS_SHORTANSWER_WIRIS;

        $islogmodeenabled = get_config('qtype_wq', 'log_server_errors') == '1';
        if ($islogmodeenabled) {
            $errormessage = 'WIRISQUIZZES ATTEMPT LIMIT REACHED FOR STEP WITH ID ' .
                ($this->step != null ? $this->step->get_id() : $this->stepid);
            // @codingStandardsIgnoreLine
            error_log($errormessage);
        }

        return $isreached;
    }

    /**
     * Increment number of failed attempts
     */
    public function inc_attempts(moodle_exception $e) {
        $c = $this->get_var('_gc', false);
        if (is_null($c)) {
            $c = 0;
        }

        $islogmodeenabled = get_config('qtype_wq', 'log_server_errors') == '1';
        if ($islogmodeenabled) {
            $errormessage = 'WIRISQUIZZES ATTEMPT ERROR --- INCREASING ATTEMPT COUNT TO ' . ($c + 1) . ' FOR STEP WITH ID ' .
                ($this->step != null ? $this->step->get_id() : $this->stepid) . PHP_EOL .
                'EXCEPTION: ' . $e->getMessage();
            // @codingStandardsIgnoreLine
            error_log($errormessage);
        }

        $this->set_var('_gc', $c + 1, false);
    }

    /**
     * Set number of failed attempts to zero
     */
    public function reset_attempts() {
        $this->set_var('_gc', 0, false);
    }

    /**
     * Whether there is any error in the question or in the parent
     * @return boolean
     */
    public function is_error() {
        $c = $this->get_var('_gc', false); // False to look into the parent.
        if (!is_null($c) && $c > 0) {
            return true;
        }
        return false;
    }

    /**
     * Returns the number of failed attempts
     * @return int
     */
    public function get_attempts() {
        $c = $this->get_var('_gc', false);
        if ($c == null) {
            return 0;
        }
        return $c;
    }

    public function is_first_step() {
        // The original $step object is always a quetsion_attempt_stept_read_only
        // object but in the first one of the steps sequence. In $this->load
        // method we save $this->step only if it isn't readonly, so in particular
        // only if it is the first step.
        return (!is_null($this->step));
    }
}

class question_attempt_step_subquestion_adapter_wiris extends question_attempt_step_subquestion_adapter {

    public function is_adapter_of_read_only() {
        // In Moodle 4.1 and earlier the variable $this->realstep was misspelled as $this->realqas.
        // Therefore we need to check for both possibilities. In Moodle 4.2 we use this extension class
        // because $this->realstep is protected.
        return (isset($this->realqas) && ($this->realqas instanceof question_attempt_step_read_only)) ||
            (isset($this->realstep) && ($this->realstep instanceof question_attempt_step_read_only));
    }
}
