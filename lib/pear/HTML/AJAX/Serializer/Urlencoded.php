<?php
// $Id$

// {{{ http_build_query
/**
 * Replacement for http_build_query()
 *
 * @link   http://php.net/function.http-build-query
 * @author vlad_mustafin@ukr.net
 * @author Arpad Ray <arpad@php.net>
 */
if (!function_exists('http_build_query')) {
    function http_build_query($formdata, $numeric_prefix = null, $key = null) 
    {
        $res = array();
        foreach ((array)$formdata as $k => $v) {
            if (is_resource($v)) {
                return null;
            }
            $tmp_key = urlencode(is_int($k) ? $numeric_prefix . $k : $k);
            if (!is_null($key)) {
                $tmp_key = $key . '[' . $tmp_key . ']';
            }
            $res[] = (is_scalar($v))
                ? $tmp_key . '=' . urlencode($v)
                : http_build_query($v, null , $tmp_key);
        }
        $separator = ini_get('arg_separator.output');
        if (strlen($separator) == 0) {
            $separator = '&';
        }
        return implode($separator, $res);
    }
}
// }}}
// {{{ class HTML_AJAX_Serialize_Urlencoded
/**
 * URL Encoding Serializer
 *
 * @category   HTML
 * @package    AJAX
 * @author     Arpad Ray <arpad@php.net>
 * @author     David Coallier <davidc@php.net>
 * @copyright  2005 Arpad Ray
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: 0.5.6
 * @link       http://pear.php.net/package/HTML_AJAX
 */
class HTML_AJAX_Serializer_Urlencoded
{
    // {{{ serialize
    function serialize($input) 
    {
        return http_build_query(array('_HTML_AJAX' => $input));
    }
    // }}}
    // {{{ unserialize
    function unserialize($input) 
    {
        parse_str($input, $ret);
        return (isset($ret['_HTML_AJAX']) ? $ret['_HTML_AJAX'] : $ret);
    }
    // }}}
}
// }}}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
?>
