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
 * @param mixed   $idbase, optionally, define one idbase to be added to all the elements in the corners
 */
function print_custom_corners_start($clearfix=false, $return=false, $idbase=null) {

/// Analise if we want ids for the custom corner elements
    $idbt = '';
    $idi1 = '';
    $idi2 = '';
    $idi3 = '';

    if ($idbase) {
        $idbt = 'id="' . $idbase . '-bt" ';
        $idi1 = 'id="' . $idbase . '-i1" ';
        $idi2 = 'id="' . $idbase . '-i2" ';
        $idi3 = 'id="' . $idbase . '-i3" ';
    }

/// Output begins
    $output = '<div class="wrap">'."\n";
    $output .= '<div '.$idbt.'class="bt"><div>&nbsp;</div></div>';
    $output .= "\n";
    $output .= '<div '.$idi1.'class="i1"><div '.$idi2.'class="i2">';
    $output .= (!empty($clearfix)) ? '<div '.$idi3.'class="i3 clearfix">' : '<div '.$idi3.'class="i3">';

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
 * @param mixed   $idbase, optionally, define one idbase to be added to all the elements in the corners
 */
function print_custom_corners_end($return=false, $idbase=null) {

/// Analise if we want ids for the custom corner elements
    $idbb = '';

    if ($idbase) {
        $idbb = 'id="' . $idbase . '-bb" ';
    }

/// Output begins
    $output = '</div></div></div>';
    $output .= "\n";
    $output .= '<div '.$idbb.'class="bb"><div>&nbsp;</div></div>'."\n";
    $output .= '</div>';

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

?>
