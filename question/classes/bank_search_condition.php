<?php

/**
 * An abstract class for filtering/searching questions.
 * See also init_search_conditions
 */
abstract class core_question_bank_search_condition {
    /**
     * @return string An SQL fragment to be ANDed into the WHERE clause to filter which questions are shown
     */
    public abstract function where();

    /**
     * @return array Parameters to be bound to the above WHERE clause fragment
     */
    public function params() {
        return array();
    }

    /**
     * Display GUI for selecting criteria for this condition. Displayed when Show More is open.
     *
     * Compare display_options(), which displays always, whether Show More is open or not.
     * @return string HTML form fragment
     */
    public function display_options_adv() {
        return;
    }

    /**
     * Display GUI for selecting criteria for this condition. Displayed always, whether Show More is open or not.
     *
     * Compare display_options_adv(), which displays when Show More is open.
     * @return string HTML form fragment
     */
    public function display_options() {
        return;
    }
}
