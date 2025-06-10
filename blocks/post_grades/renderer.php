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

defined('MOODLE_INTERNAL') || die();

class block_post_grades_renderer extends plugin_renderer_base {

    public function confirm_return(post_grades_return_process $return, $usecontinue = true) {
        try {
            $processed = $return->process();

            if (is_array($processed)) {
                if ($usecontinue) {
                    echo $this->continue_button($return->get_url(null));
                }
            } else {
                echo $this->box_start();
                echo $this->notification($return->get_explanation());
                echo $this->continue_button($return->get_url($processed));
                echo $this->box_end();
            }
        } catch (Exception $e) {
            echo $this->notification($e->getMessage());
        }
    }

    public function confirm_period($course, $group, $period) {
        $a = new stdClass;
        $a->post_type = get_string($period->post_type, 'block_post_grades');
        $a->fullname = $course->fullname;
        $a->name = $group->name;

        if (post_grades::already_posted($course, $group, $period)) {
            $msg = get_string('alreadyposted', 'block_post_grades', $a);

            $key = 'mylsu_gradesheet_url';

            $sheeturl = get_config('block_post_grades', $key);

            $str = get_string('view_gradsheet', 'block_post_grades');
            $post = new single_button(new moodle_url($sheeturl), $str, 'get');
        } else {
            $msg = get_string('message', 'block_post_grades', $a);

            $posturl = new moodle_url('/blocks/post_grades/postgrades.php', array(
                'courseid' => $course->id,
                'groupid' => $group->id,
                'periodid' => $period->id
            ));

            $str = get_string('post_type_grades', 'block_post_grades', $a);
            $post = new single_button($posturl, $str, 'post');
        }

        $gradebookurl = new moodle_url('/grade/report/grader/index.php', array(
            'id' => $course->id, 'group' => $group->id
        ));

        $str = get_string('make_changes', 'block_post_grades', $a);
        $gradebook = new single_button($gradebookurl, $str, 'get');

        $cancelurl = new moodle_url('/course/view.php', array('id' => $course->id));
        $cancel = new single_button($cancelurl, get_string('cancel'));

        $out = $this->output->box_start('generalbox', 'notice');
        $out .= html_writer::tag('p', $msg);
        $out .= html_writer::tag('div',
            $this->output->render($post) .
            $this->output->render($gradebook) .
            $this->output->render($cancel),
            array('class' => 'buttons')
        );
        $out .= $this->output->box_end();
        return $out;
    }
}
