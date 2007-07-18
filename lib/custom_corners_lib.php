<?php
/**
 * @author Urs Hunkler
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * Custom corners and borders
 * Support library
 *
 * 2007-05-07  File created.
 */

/**
 * Starting part of the surrounding divs for custom corners
 *
 * @param boolean $clearfix, add CLASS "clearfix" to the inner div against collapsing
 * @param boolean $return, return as string or just print it
 */
function print_custom_corners_start($clearfix=false, $return=false) {
    $output = '<div class="wrap">'."\n";
    $output .= '<div class="bt"><div></div></div>';
    $output .= "\n";
    $output .= '<div class="i1"><div class="i2">';
    $output .= (!empty($clearfix)) ? '<div class="i3 clearfix">' : '<div class="i3">';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}


/**
 * Ending part of the surrounding divs for custom corners
 *
 * @param boolean $return, return as string or just print it
 */
function print_custom_corners_end($return=false) {
    $output = '</div></div></div>';
    $output .= "\n";
    $output .= '<div class="bb"><div></div></div>'."\n";
    $output .= '</div>';
    
    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

?>