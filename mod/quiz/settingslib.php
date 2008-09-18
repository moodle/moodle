<?php  // $Id$

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

// Quiz specific admin settings class.
class admin_setting_quiz_reviewoptions extends admin_setting {
    private static $times = array(
            QUIZ_REVIEW_IMMEDIATELY => 'reviewimmediately',
            QUIZ_REVIEW_OPEN => 'reviewopen',
            QUIZ_REVIEW_CLOSED => 'reviewclosed');
    private static $things = array(
            QUIZ_REVIEW_RESPONSES => 'responses',
            QUIZ_REVIEW_ANSWERS => 'answers',
            QUIZ_REVIEW_FEEDBACK => 'feedback',
            QUIZ_REVIEW_GENERALFEEDBACK => 'generalfeedback',
            QUIZ_REVIEW_SCORES => 'scores',
            QUIZ_REVIEW_OVERALLFEEDBACK => 'overallfeedback');

    function __construct($name, $visiblename, $description, $defaultsetting) {
        $this->plugin = 'quiz';
        parent::admin_setting($name, $visiblename, $description, $defaultsetting);
    }

    private function normalise_data($data) {
        $value = 0;
        foreach (admin_setting_quiz_reviewoptions::$times as $timemask => $timestring) {
            foreach (admin_setting_quiz_reviewoptions::$things as $thingmask => $thingstring) {
                if (!empty($data[$timemask][$thingmask])) {
                    $value += $timemask & $thingmask;
                }
            }
        }
        return $value;
    }

    function get_setting() {
        $value = $this->config_read($this->name);
        $fix = $this->config_read('fix_' . $this->name);
        if (is_null($value) or is_null($fix)) {
            return NULL;
        }
        return array('value' => $value, 'fix' => $fix);
    }

    function write_setting($data) {
        if (!isset($data['value'])) {
            $data['value'] = $this->normalise_data($data);
        }
        $ok = $this->config_write($this->name, $data['value']);
        if ($ok) {
            if (empty($data['fix'])) {
                $ok = $this->config_write('fix_' . $this->name, 0);
            } else {
                $ok = $this->config_write('fix_' . $this->name, 1);
            }
        }
        if (!$ok) {
            return get_string('errorsetting', 'admin');
        }
        return '';
    }

    function output_html($data, $query='') {
        if (!isset($data['value'])) {
            $data['value'] = $this->normalise_data($data);
        }

        $return = '<div id="adminquizreviewoptions" class="clearfix">' . "\n";
        foreach (admin_setting_quiz_reviewoptions::$times as $timemask => $timestring) {
            $return .= '<div class="group"><div class="fitemtitle">' . get_string($timestring, 'quiz') . "</div>\n";
            $nameprefix = $this->get_full_name() . '[' . $timemask . ']';
            $idprefix = $this->get_id(). '_' . $timemask . '_';
            foreach (admin_setting_quiz_reviewoptions::$things as $thingmask => $thingstring) {
                $id = $idprefix . $thingmask;
                $state = '';
                if ($data['value'] & $timemask & $thingmask) {
                    $state = 'checked="checked" ';
                }
                $return .= '<span><input type="checkbox" name="' .
                        $nameprefix . '[' . $thingmask . ']" value="1" id="' . $id .
                        '" ' . $state . '/> <label for="' . $id . '">' .
                        get_string($thingstring, 'quiz') . "</label></span>\n";
            }
            $return .= "</div>\n";
        }
        $return .= "</div>\n";

        $fix = !empty($data['fix']);
        $return .= '<input type="checkbox" class="form-checkbox" id="' .
                $this->get_id() . '_fix" name="' . $this->get_full_name() .
                '[fix]" value="1" ' . ($fix ? 'checked="checked"' : '') . ' />' .
                ' <label for="' . $this->get_id() . '_fix">' .
                get_string('advanced') . '</label> ';

        return format_admin_setting($this, $this->visiblename, $return,
                $this->description, true, '', get_string('everythingon', 'quiz'), $query);
    }
}
?>