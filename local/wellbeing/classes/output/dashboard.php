<?php
namespace local_wellbeing\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class dashboard implements renderable, templatable {

    protected $courseid;
    protected $userid;

    public function __construct($courseid, $userid) {
        $this->courseid = $courseid;
        $this->userid = $userid;
    }

    public function export_for_template(renderer_base $output) {
        global $DB;

        $records = $DB->get_records('local_wellbeing_metrics', [
            'courseid' => $this->courseid,
            'userid'   => $this->userid
        ], 'timecreated ASC');

        $labels = [];
        $scores = [];

        foreach ($records as $record) {
            $metrics = json_decode($record->metrics, true);

            if (!empty($metrics)) {
                $totalscore = array_sum($metrics);
                $avgscore = round($totalscore / count($metrics));

                $labels[] = date('M d', $record->timecreated);
                $scores[] = $avgscore;
            }
        }

        if (empty($scores)) {
            $scores = [0];
            $labels = ['No Data'];
        }

        $overall = round(array_sum($scores) / count($scores));
        $growth = end($scores) - reset($scores);

        return [
            'labels' => json_encode($labels),
            'scores' => json_encode($scores),
            'overall' => $overall,
            'growth' => $growth,
            'emoji' => $this->get_emoji($overall),
            'message' => $this->get_message($overall)
        ];
    }

    private function get_emoji($score) {
        if ($score < 20) return "😟";
        if ($score < 40) return "😕";
        if ($score < 60) return "😐";
        if ($score < 80) return "🙂";
        return "😄";
    }

    private function get_message($score) {
        if ($score < 40) return "Things seem a bit tough. Take care 💙";
        if ($score < 70) return "You're doing okay. Keep going 🌱";
        return "You're thriving! Keep it up 🌟";
    }
}