<?php
/**
 * format grade using lang specific decimal point and thousand separator
 * the result is suitable for printing on html page
 * @param float $gradeval raw grade value pulled from db
 * @return string $gradeval formatted grade value
 */
function get_grade_clean($gradeval) {
    global $CFG;

    if (is_null($gradeval)) {
        $gradeval = '';
	} else {
        // decimal points as specified by user
        $decimals = get_user_preferences('grade_report_decimalpoints', $CFG->grade_report_decimalpoints);
        $gradeval = number_format($gradeval, $decimals, get_string('decpoint', 'langconfig'), get_string('thousandsep', 'langconfig'));
    }

    return $gradeval;

    /*
    // commenting this out, if this is added, we also need to find the number of decimal place preserved
    // so it can go into number_format
    if ($gradeval != 0) {
        $gradeval = rtrim(trim($gradeval, "0"), ".");
    } else {
        $gradeval = 0;
    }
    */

}

/**
 * Given a user input grade, format it to standard format i.e. no thousand separator, and . as decimal point
 * @param string $gradeval grade value from user input, language specific format
 * @return string - grade value for storage, en format
 */
function format_grade($gradeval) {

    $decimalpt = get_string('decpoint', 'langconfig');
    $thousandsep = get_string('thousandsep', 'langconfig');
    // replace decimal point with '.';
    $gradeval = str_replace($decimalpt, '.', $gradeval);
    // thousand separator is not useful
    $gradeval = str_replace($thousandsep, '', $gradeval);

    return clean_param($gradeval, PARAM_NUMBER);
}
?>