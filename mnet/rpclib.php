<?php // $Id$
/**
 * Some dummy functions to test XML-RPC with
 */

/**
 * The xxxx_RPC_OK must exist and return TRUE for the remote call to be
 * permitted
 *
 * @return bool True if the related function can be executed remotely
 */
function mnet_concatenate_strings_RPC_OK() {
    return true;
}

function mnet_publishes() {
    $servicelist = array();
        $service['name']             = 'sso';
            $function['name']        = 'mnet_concatenate_strings';

                // first argument
                $argument['type']    = 'string';
                $argument['default'] = '';
            $function['arguments'][] = $argument;

                // second argument
                $argument['type']    = 'string';
                $argument['default'] = '';
            $function['arguments'][] = $argument;

                // third argument
                $argument['type']    = 'string';
                $argument['default'] = '';
            $function['arguments'][] = $argument;
        
            $function['description'] = get_string($function['name'], 'mnet');
        $service['functions'][]      = $function;
    $servicelist[]                   = $service;

    return $servicelist;
}
//header('Content-type: text/plain');
//var_dump(mnet_publishes());

/**
 * Concatenate (up to) 3 strings and return the result
 * @service sso
 * @param   string  $string1    Some string
 * @param   string  $string2    Some string
 * @param   string  $string3    Some string
 * @return  string              The parameter strings, concatenated together
 */
function mnet_concatenate_strings($string1='', $string2='', $string3='') {
    return $string1.$string2.$string3;
}

class testClass {
    function testClass() {
        $this->first = 'last';
        $this->last  = 'first';
    }
    
    function augment_first($newval) {
        $this->first = $this->first.$newval;
        return $this->first;
    }
    
    function augment_first_RPC_OK() {
        return true;
    }
    
    function mnet_concatenate_strings_RPC_OK() {
        return true;
    }
    function mnet_concatenate_strings($string1='', $string2='', $string3='') {
        return $string1.$string2.$string3;
    }
}

?>
