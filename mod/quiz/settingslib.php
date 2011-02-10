<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page.
}

/**
 * Quiz specific admin settings class.
 */
class mod_quiz_admin_review_setting extends admin_setting {
    /**#@+
     * @var integer should match the constants defined in {@link mod_quiz_display_options}.
     * again, copied for performance reasons.
     */
    const DURING =            0x10000;
    const IMMEDIATELY_AFTER = 0x01000;
    const LATER_WHILE_OPEN =  0x00100;
    const AFTER_CLOSE =       0x00010;
    /**#@-*/

    /**
     * @var boolean|null forced checked / disabled attributes for the during time.
     */
    protected $duringstate;

    /**
     * This should match {@link mod_quiz_mod_form::$reviewfields} but copied
     * here becuase generating the admin tree needs to be fast.
     * @return array
     */
    public static function fields() {
        return array(
            'attempt' => get_string('theattempt', 'quiz'),
            'correctness' => get_string('whethercorrect', 'question'),
            'marks' => get_string('marks', 'question'),
            'specificfeedback' => get_string('specificfeedback', 'question'),
            'generalfeedback' => get_string('generalfeedback', 'question'),
            'rightanswer' => get_string('rightanswer', 'question'),
            'overallfeedback' => get_string('overallfeedback', 'quiz'),
        );
    }

    public function __construct($name, $visiblename, $description, $defaultsetting, $duringstate = null) {
        $this->duringstate = $duringstate;
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * @return integer all times.
     */
    public static function all_on() {
        return self::DURING | self::IMMEDIATELY_AFTER | self::LATER_WHILE_OPEN |
                self::AFTER_CLOSE;
    }

    protected static function times() {
        return array(
            self::DURING => get_string('reviewduring', 'quiz'),
            self::IMMEDIATELY_AFTER => get_string('reviewimmediately', 'quiz'),
            self::LATER_WHILE_OPEN => get_string('reviewopen', 'quiz'),
            self::AFTER_CLOSE => get_string('reviewclosed', 'quiz'),
        );
    }

    protected function normalise_data($data) {
        $times = self::times();
        $value = 0;
        foreach ($times as $timemask => $name) {
            if ($timemask == self::DURING && !is_null($this->duringstate)) {
                if ($this->duringstate) {
                    $value += $timemask;
                }
            } else if (!empty($data[$timemask])) {
                $value += $timemask;
            }
        }
        return $value;
    }

    public function get_setting() {
        return $this->config_read($this->name);
    }

    public function write_setting($data) {
        if (is_array($data) || empty($data)) {
            $data = $this->normalise_data($data);
        }
        $this->config_write($this->name, $data);
        return '';
    }

    public function output_html($data, $query = '') {
        if (is_array($data) || empty($data)) {
            $data = $this->normalise_data($data);
        }

        $return = '<div class="group"><input type="hidden" name="' .
                    $this->get_full_name() . '[' . self::DURING . ']" value="0" />';
        foreach (self::times() as $timemask => $namestring) {
            $id = $this->get_id(). '_' . $timemask;
            $state = '';
            if ($data & $timemask) {
                $state = 'checked="checked" ';
            }
            if ($timemask == self::DURING && !is_null($this->duringstate)) {
                $state = 'disabled="disabled" ';
                if ($this->duringstate) {
                    $state .= 'checked="checked" ';
                }
            }
            $return .= '<span><input type="checkbox" name="' .
                    $this->get_full_name() . '[' . $timemask . ']" value="1" id="' . $id .
                    '" ' . $state . '/> <label for="' . $id . '">' .
                    $namestring . "</label></span>\n";
        }
        $return .= "</div>\n";

        return format_admin_setting($this, $this->visiblename, $return,
                $this->description, true, '', get_string('everythingon', 'quiz'), $query);
    }
}
