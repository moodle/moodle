<?php

class test_indicator_max extends \core_analytics\local\indicator\binary {
    protected function calculate_sample($sampleid, $samplesorigin, $starttime, $endtime) {
        return self::MAX_VALUE;
    }
}
