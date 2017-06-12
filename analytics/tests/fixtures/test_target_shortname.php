<?php

class test_target_shortname extends \core_analytics\local\target\binary {

    protected $predictions = array();

    public function get_analyser_class() {
        return '\core_analytics\local\analyser\site_courses';
    }

    public static function classes_description() {
        return array(
            'Course fullname first char is A',
            'Course fullname first char is not A'
        );
    }

    /**
     * We don't want to discard results.
     * @return float
     */
    protected function min_prediction_score() {
        return null;
    }

    /**
     * We don't want to discard results.
     * @return array
     */
    protected function ignored_predicted_classes() {
        return array();
    }

    public function is_valid_analysable(\core_analytics\analysable $analysable, $fortraining = true) {
        // This is testing, let's make things easy.
        return true;
    }

    public function is_valid_sample($sampleid, \core_analytics\analysable $analysable) {

        $sample = $this->retrieve('course', $sampleid);
        if ($sample->visible == 0) {
            // We skip not-visible courses as a way to emulate the training data / prediction data difference.
            // In normal circumstances is_valid_sample will return false when they receive a sample that can not be
            // processed.
            return false;
        }
        return true;
    }

    protected function calculate_sample($sampleid, \core_analytics\analysable $analysable, $starttime = false, $endtime = false) {

        $sample = $this->retrieve('course', $sampleid);

        $firstchar = substr($sample->shortname, 0, 1);
        if ($firstchar === 'a') {
            return 1;
        } else {
            return 0;
        }
    }
}
