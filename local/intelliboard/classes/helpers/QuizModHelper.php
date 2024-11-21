<?php

namespace local_intelliboard\helpers;

class QuizModHelper
{
    const MODE_NONE     = 0;
    const MODE_MOODLE3  = 3;
    const MODE_MOODLE4  = 4;

    public $modVersion  = 0;
    public $mode    = 0;

    public function __construct()
    {
        $this->setMode();
    }

    private function getModVersion()
    {
        global $DB, $CFG;

        $modVersion = get_config('mod_quiz');
        return $modVersion->version ?? 0;
    }

    private function setMode()
    {
        $this->modVersion = $this->getModVersion();
        if ($this->modVersion == 0) {
            $this->mode = self::MODE_NONE;
        } else if ($this->modVersion >= 2022020300) {
            $this->mode = self::MODE_MOODLE4;
        } else {
            $this->mode = self::MODE_MOODLE3;
        }
    }

    public function getJoinSQLQuizQuestions($quizAlias = "q", $slotsAlias = "qs", $questionAlias = "qq")
    {
        $join = "";
        if ($this->mode == self::MODE_MOODLE3) {
            $join = "
                JOIN {quiz_slots} {$slotsAlias} ON {$slotsAlias}.quizid = {$quizAlias}.id
                JOIN {question} qq ON $questionAlias.id = {$slotsAlias}.questionid
            ";
        } else if ($this->mode = self::MODE_MOODLE4) {
            $join = "
                JOIN {quiz_slots} {$slotsAlias} ON {$slotsAlias}.quizid = {$quizAlias}.id
                JOIN {question_references} qre ON qre.itemid = {$slotsAlias}.id
                JOIN {question_bank_entries} qbe ON qbe.id = qre.questionbankentryid
                JOIN {question_versions} qve ON qve.questionbankentryid = qbe.id
                JOIN {question} {$questionAlias} ON {$questionAlias}.id = qve.questionid
            ";
        }
        return $join;
    }
}
