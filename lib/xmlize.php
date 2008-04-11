<?php

/**
 * xmlize.php - xmlize() is by Hans Anderson, {@link http://www.hansanderson.com/contact/}
 *
 * Ye Ole "Feel Free To Use it However" License [PHP, BSD, GPL].
 * some code in xml_depth is based on code written by other PHPers
 * as well as one Perl script.  Poor programming practice and organization
 * on my part is to blame for the credit these people aren't receiving.
 * None of the code was copyrighted, though.
 *
 * @author Hans Anderson
 * @version This is a stable release, 1.0.  I don't foresee any changes, but you
 * might check {@link http://www.hansanderson.com/php/xml/} to see
 * @package moodlecore
 */

/**
 * Create xml formatted output from an array.
 *
 * usage:<br>
 * <code>
 * $xml = xmlize($array);
 * </code>
 * See the function {@link traverse_xmlize()} for information about the
 * structure of the array, it's much easier to explain by showing you.
 * Be aware that the array is somewhat tricky.  I use xmlize all the time,
 * but still need to use {@link traverse_xmlize()} quite often to show me the structure!
 *
 * THIS IS A PHP 5 VERSION:
 *
 * This modified version basically has a new optional parameter
 * to specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * I recommend you to read this PHP bug. There you can see how PHP4, PHP5.0.0
 * and PHP5.0.2 will handle this.
 * {@link http://bugs.php.net/bug.php?id=29711}
 * Ciao, Eloy :-)
 *
 *
 * @author Hans Anderson
 * @param array $data The array to be converted
 * @param int $WHITE  If set to 1 allows the parser to skip "space" characters in xml document. Default is 1
 * @param string $encoding Specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * @return array
 */
function xmlize($data, $WHITE=1, $encoding='UTF-8') {

    $data = trim($data);
    $vals = $index = $array = array();
    $parser = xml_parser_create($encoding);
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, $WHITE);
    xml_parse_into_struct($parser, $data, $vals, $index);
    xml_parser_free($parser);

    $i = 0;
    
    if (empty($vals)) {
        // XML file is invalid or empty, return false
        return false;
    }

    $tagname = $vals[$i]['tag'];
    if ( isset ($vals[$i]['attributes'] ) )
    {
        $array[$tagname]['@'] = $vals[$i]['attributes'];
    } else {
        $array[$tagname]['@'] = array();
    }

    $array[$tagname]["#"] = xml_depth($vals, $i);


    return $array;
}

/**
 * @internal You don't need to do anything with this function, it's called by
 * xmlize. It's a recursive function, calling itself as it goes deeper
 * into the xml levels.  If you make any improvements, please let me know.
 * @access private
 */
function xml_depth($vals, &$i) {
    $children = array();

    if ( isset($vals[$i]['value']) )
    {
        array_push($children, $vals[$i]['value']);
    }

    while (++$i < count($vals)) {

        switch ($vals[$i]['type']) {

           case 'open':

                if ( isset ( $vals[$i]['tag'] ) )
                {
                    $tagname = $vals[$i]['tag'];
                } else {
                    $tagname = '';
                }

                if ( isset ( $children[$tagname] ) )
                {
                    $size = sizeof($children[$tagname]);
                } else {
                    $size = 0;
                }

                if ( isset ( $vals[$i]['attributes'] ) ) {
                    $children[$tagname][$size]['@'] = $vals[$i]["attributes"];

                }

                $children[$tagname][$size]['#'] = xml_depth($vals, $i);

            break;


            case 'cdata':
                array_push($children, $vals[$i]['value']);
            break;

            case 'complete':
                $tagname = $vals[$i]['tag'];

                if( isset ($children[$tagname]) )
                {
                    $size = sizeof($children[$tagname]);
                } else {
                    $size = 0;
                }

                if( isset ( $vals[$i]['value'] ) )
                {
                    $children[$tagname][$size]["#"] = $vals[$i]['value'];
                } else {
                    $children[$tagname][$size]["#"] = '';
                }

                if ( isset ($vals[$i]['attributes']) ) {
                    $children[$tagname][$size]['@']
                                             = $vals[$i]['attributes'];
                }

            break;

            case 'close':
                return $children;
            break;
        }

    }

        return $children;


}


/**
 * This helps you understand the structure of the array {@link xmlize()} outputs
 *
 * Function by acebone@f2s.com, a HUGE help!<br>
 * Usage:<br>
 * <code>
 * traverse_xmlize($xml, 'xml_');
 * print '<pre>' . implode("", $traverse_array . '</pre>';
 * </code>
 * @author acebone@f2s.com
 * @param array $array ?
 * @param string $arrName ?
 * @param int $level ?
 * @return int
 * @todo Finish documenting this function
 */
function traverse_xmlize($array, $arrName = 'array', $level = 0) {

    foreach($array as $key=>$val)
    {
        if ( is_array($val) )
        {
            traverse_xmlize($val, $arrName . '[' . $key . ']', $level + 1);
        } else {
            $GLOBALS['traverse_array'][] = '$' . $arrName . '[' . $key . '] = "' . $val . "\"\n";
        }
    }

    return 1;

}

?>
