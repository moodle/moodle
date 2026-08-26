<?php

defined('MOODLE_INTERNAL') || die();

class block_cynaris_stats extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_cynaris_stats');
    }

    public function get_content() {
        global $USER, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        require_once($CFG->libdir . '/gradelib.php');

        $labels = [];
        $grades = [];

        $courses = enrol_get_my_courses();

        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }

            $courseitem = grade_item::fetch_course_item($course->id);

            if ($courseitem && !$courseitem->needsupdate) {
                $grade = $courseitem->get_final($USER->id);

                if ($grade && !is_null($grade->finalgrade) && $courseitem->grademax > 0) {
                    $percentage = ($grade->finalgrade / $courseitem->grademax) * 100;

                    $labels[] = format_string($course->fullname);
                    $grades[] = round($percentage, 2);
                }
            }
        }

        $chartid = 'cynaris-stats-chart-' . uniqid();

        $this->content->text = html_writer::tag(
            'p',
            html_writer::tag('strong', 'Cynaris Progress Dashboard')
        );

        if (empty($grades)) {
            $this->content->text .= html_writer::tag(
                'p',
                'No grade data is available yet.'
            );
        } else {
            $this->content->text .= html_writer::empty_tag('canvas', [
                'id' => $chartid,
                'height' => '250'
            ]);

            $this->page->requires->js_call_amd(
                'block_cynaris_stats/dashboard',
                'init',
                [$chartid, $labels, $grades]
            );
        }

        $this->content->footer = '';

        return $this->content;
    }
}
