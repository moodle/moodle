<?php
/**
 * Created on 10/17/2008
 *
 * Rest Test Client Suport Library
 *
 * @author David Castro Garcia
 * @author Ferran Recio Calderó
 */

//--- interface helpers ---

/**
 * starts the interface
 */
function start_interface ($title="&nbsp;") {
    $title2 = ($title)?$title:"&nbsp;";
    echo '<html><head>';
    echo "<title>Moodle Webservice Rest Test Client</title>";
    echo '<link rel="stylesheet" href="style.css" type="text/css">';
    echo '</head><body>';
    echo '<div class="head"><h1>Moodle Webservice Rest Test Client</h1>';
    echo "<h3>$title2</h3>";
    echo '</div>';
    echo '<div class="content">';
    if ($title) echo '<p class="return"><a href="index.php"><img src="return.gif" border="0"/></a></p>';
}

/**
 * end interface
 *
 * @param bool $ret=true: show return button
 */
function end_interface ($ret = true) {
    if ($ret) echo '<p class="return"><a href="index.php"><img src="return.gif" border="0"/></a></p>';
    echo '</div>';
    echo '<div class="footer">Created by David Castro i Ferran Recio for Moodle Webservices</div>';
    echo '</body></html>';
}

/**
 * print XML div area
 *
 * @param string $xml
 *
 */
function show_xml ($xml) {
    echo '<div class="xmlshow">';
    echo '<a onClick="document.getElementById(\'toggleme\').style.display = ' .
            '(document.getElementById(\'toggleme\').style.display!=\'none\')?\'none\':\'\';">Hide/Show XML</a>';
    echo "<div style=\"display:none;\" id=\"toggleme\">";
    echo '<pre>';echo htmlentities($xml);echo '</pre>';
    echo "</div>";
    echo "</div>";
}

/**
 * format post data
 */
function format_postdata ($data) {
    $o="";
    foreach ($data as $k=>$v) {
        $o.= "$k=".rawurlencode($v)."&";
    }
    $post_data=substr($o,0,-1);
    return $post_data;
}

/**
 * shows an object in list format
 *
 * @param mixed $obj
 * @param integer $cols: number of colums
 * @ string string $check=false: if this attribute is not present, the $obj is ans error
 *
 */
function show_object ($obj,$cols=1,$check=false) {
    if (!is_array($obj)) $obj = array($obj);
    echo '<ul class="results">';
    foreach ($obj as $r) {

        if ($check && (!isset($r->$check) || $r->$check==-1)) {
            echo '<li class="error">';
            echo "EMPTY ROW!";
        } else {
            if (is_object($r)) {
                echo '<li class="element">';
                $text = array();
                $parts = get_object_vars($r);
                $num = 1;
                $currline = '';
                foreach ($parts as $key => $val) {
                    $currline.= "<span class=\"resultval\"><b>$key:</b> <i>$val</i></span>, ";
                    if ($num >= $cols) {
                        $currline=substr($currline,0,-2);
                        $text[] = $currline;
                        $currline = '';
                        $num = 0;
                    }
                    $num++;
                }
                echo implode('<br/>',$text);
            } else {
                if ($r==-1 || !$r) {
                    echo '<li class="error">';
                    echo "EMPTY ROW!";
                } else {
                    echo '<li class="element">';
                    echo "<span class=\"resultval\"><b>Returned Value:</b> <i>$r</i></span>";
                }
            }
        }
        echo '</li>';
    }
    echo '</ul>';
}


//---- XML simple parser ----
//this code was donated by Ferran Recio

/**
 * convert a simple xml into php object
 *
 * @author ferran recio
 *
 * @param String $xml
 *
 * @return mixed
 */
function basicxml_xml_to_object ($xml) {
    $xml=utf8_encode($xml);

    //create the parser
    $parser = xml_parser_create ();
    xml_set_default_handler ($parser,'basicxml_xml_to_object_aux');

    $values = array();
    $index = array();
    xml_parse_into_struct($parser,$xml,$values,$index);

    //print_object($values);
    //print_object($index);

    //just simplexml tag (disabled)
    //if (strtolower($values[0]['tag']) != 'basicxml') return false;
    //if (strtolower($values[count($values)-1]['tag']) != 'basicxml') return false;

    $res = basicxml_xml_to_object_aux ($values);
    //omit the first tag
    $parts = array_keys(get_object_vars($res));
    $key = $parts[0];
    return $res->$key;
}

/**
 * auxiliar function to basicxml_xml_to_object
 *
 * @author ferran recio
 *
 * @param mixed $values
 *
 * @return mixed
 */
function basicxml_xml_to_object_aux ($values) {

    if (!is_array($values)) return false;
    //print_object ($values);
    $currset = array();
    $search = false;

    foreach ($values as $value) {
        $tag = strtolower($value['tag']);
        //if we are acomulating, just acomulate it
        if ($search) {
            //if it closes a tag, we just stop searching
            if ($tag == $search && $value['type']=='close') {
                //recursivity
                $obj2 = basicxml_xml_to_object_aux ($currset);
                //search cleaning
                $search = false;
                //add to result
                if (isset($res->{$tag})){
                    if (is_array($res->{$tag})){
                        $res->{$tag}[] = $obj2;
                    } else {
                        $res->{$tag} = array($res->{$tag},$obj2);
                    }
                } else {
                    $res->{$tag} = $obj2;
                }
            } else {
                //we are searching. If it's cdada, pass it throw
                if ($value['type']=='cdata') continue;
                //if isn't cdata, put it in the set and continue searching
                //(because isn't the close we're searching)
                $currset[] = $value;
            }
        } else {
            //walking the xml
            if ($value['type']=='open'){
                //on open, let's search on it
                $currset = array();
                $search = $tag;
            } else {
                //if it's complete just save it
                if ($value['type']=='complete') {
                    if (!empty($value['value']  )) {
                        $val = html_entity_decode($value['value']);
                        if (isset($res->{$tag})){
                            if (is_array($res->{$tag})){
                                $res->{$tag}[] = $val;
                            } else {
                                $res->{$tag} = array($res->{$tag},$val);
                            }
                        } else {
                            $res->{$tag} = $val;
                        }
                    }
                }
            }
        }
    }
    return $res;
}
?>
