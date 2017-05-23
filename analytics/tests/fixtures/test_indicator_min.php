<?php

class test_indicator_min extends \core_analytics\local\indicator\binary {
    protected function calculate_sample($sampleid, $samplesorigin, $starttime, $endtime) {
        return self::MIN_VALUE;
    }
}
