<?php  // $Id$

/**
* Library of functions used by the RQP question type
*
* @version $Id$
* @author Alex Smith and other members of the Serving Mathematics project
*         {@link http://maths.york.ac.uk/serving_maths}
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questionbank
 * @subpackage questiontypes
*/


function question_rqp_save_type($type) {
    if (empty($type->id)) {
        return insert_record('question_rqp_types', $type, false);
    }
    return update_record('question_rqp_types', $type);
}

function question_rqp_delete_type($id) {
    return delete_records('question_rqp_type', 'id', $id);
}

/**
* Creates a colon separated list of space separated values from an associative
* array of arrays
*
* An associative array of values or an associative array of arrays is imploded
* to a string by creating a colon separated list of space separated values. The
* key is treated as the first value. The {@link question_rqp_explode} function can
* restore the array from this string representation.
* @return string      The string representation of the array. This is a colon
*                     separated list of space separated values.
* @param array $array An associative array of single values or an associative
*                     array of arrays to be imploded.
*/
function question_rqp_implode($array) {
    if (count($array) < 1) {
        return '';
    }
    $str = '';
    foreach ($array as $key => $val) {
        $str .= $key . ' ';
        if (is_array($val)) {
            if (count($val) > 0) {
                foreach ($val as $subval) {
                    $str .= $subval . ' ';
                }
                // Remove the trailing space
                $str = substr($str, 0, -1);
            }
        } else {
            $str .= $val;
        }
        $str .= ':';
    }
    // Remove the trailing semi-colon
    return substr($str, 0, -1);
}

/**
* Recreates an associative array or an associative array of arrays from the
* string representation
*
* Takes a colon separated list of space separated values as produced by
* {@link question_rqp_implode} and recreates the array. If an array of single values
* is expected then an error results if an element has more than one value.
* Otherwise every value is an array.
* @return array         The associative array restored from the string. Every
*                       element is a single value if $multi is false or an array
*                       if $multi is true.
* @param string $str    The string to explode. This is a colon separated list of
*                       space separated values.
* @param boolean $multi Flag indicating if the values in the array are expected
*                       to be of multiple cardinality (i.e. an array of arrays
*                       is expected) or single values (i.e. an array of values).
*                       The default is false indicating an array of single
*                       values is expected.
*/
function question_rqp_explode($str, $multi=false) {
    // Explode by colon
    if ($str === '') {
        return array();
    }
    $array = explode(':', $str);
    $n = count($array);
    $return = array();
    for ($i = 0; $i < $n; $i++) {
        // Explode by space
        $array[$i] = explode(' ', $array[$i]);
        // Get the key
        $key = array_shift($array[$i]);
        if (array_key_exists($key, $return)) {
            // Element appears twice!
            return false;
        }
        // Save the element
        if ($multi) {
            $return[$key] = $array[$i];
        } else if (count($array[$i]) > 1) {
            return false;
        } else {
            $return[$key] = $array[$i][0];
        }
    }
    return $return;
}

function question_rqp_print_serverinfo($serverinfo) {
    $info->align = array('right', 'left');
    $info->data = array(); // will hold the data for the info table
    $info->data[] = array('<b>'.get_string('url', 'quiz').':</b>',$serverinfo->url);
    $info->data[] = array('<b>'.get_string('name').':</b>',$serverinfo->name);
    $info->data[] = array('<b>'.get_string('serveridentifier', 'quiz').':</b>',$serverinfo->identifier);
    $info->data[] = array('<b>'.get_string('description').':</b>',$serverinfo->description);
    print_table($info);
}

function question_rqp_debug_soap($item) {
    global $CFG;
    if (debugging()) {
        echo 'Here is the dump of the soap fault:<pre>';
        var_dump($item);
        echo '<pre>';
    }
}
?>
