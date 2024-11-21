<?php


namespace local_intelliboard\attendance\api;

abstract class base
{
    /** @var \moodle_database $moodledb */
    protected $moodledb;
    protected $moodlecfg;

    public function __construct() {
        global $DB, $CFG;

        $this->moodledb = $DB;
        $this->moodlecfg = $CFG;
    }

    abstract public function run($params);
}