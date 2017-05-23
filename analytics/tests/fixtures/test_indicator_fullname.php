<?php

class test_indicator_fullname extends \core_analytics\local\indicator\linear {

    protected static function include_averages() {
        return false;
    }

    public static function required_sample_data() {
        return array('course');
    }

    protected function calculate_sample($sampleid, $samplesorigin, $starttime, $endtime) {
        global $DB;

        $course = $this->retrieve('course', $sampleid);

        $firstchar = substr($course->fullname, 0, 1);
        if ($firstchar === 'a') {
            return self::MIN_VALUE;
        } else if ($firstchar === 'b') {
            return -0.2;
        } else if ($firstchar === 'c') {
            return 0.2;
        } else {
            return self::MAX_VALUE;
        }
    }

}
