<?php
/**
 *  This class controls whether hidden / deleted questions are hidden in the list.
 */
class core_question_bank_search_condition_hide extends core_question_bank_search_condition {
    protected $where  = '';
    protected $hide;

    /**
     * @param bool $hide include old "deleted" questions.
     */
    public function __construct($hide = true) {
        $this->hide = $hide;
        if ($hide) {
            $this->where = 'q.hidden = 0';
        }
    }

    /**
     * @return string An SQL fragment to be ANDed into the WHERE clause to show or hide deleted/hidden questions
     */
    public function where() {
        return  $this->where;
    }

    /**
     * Print HTML to display the "Also show old questions" checkbox
     */
    public function display_options_adv() {
        echo "<div>";
        echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'showhidden',
                                                   'value' => '0', 'id' => 'showhidden_off'));
        echo html_writer::checkbox('showhidden', '1', (! $this->hide), get_string('showhidden', 'question'),
                                   array('id' => 'showhidden_on', 'class' => 'searchoptions'));
        echo "</div>\n";
    }
}
