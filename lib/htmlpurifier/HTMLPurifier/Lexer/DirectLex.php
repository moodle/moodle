<?php

require_once 'HTMLPurifier/Lexer.php';

/**
 * Our in-house implementation of a parser.
 * 
 * A pure PHP parser, DirectLex has absolutely no dependencies, making
 * it a reasonably good default for PHP4.  Written with efficiency in mind,
 * it can be four times faster than HTMLPurifier_Lexer_PEARSax3, although it
 * pales in comparison to HTMLPurifier_Lexer_DOMLex.  It will support UTF-8
 * completely eventually.
 * 
 * @todo Reread XML spec and document differences.
 * 
 * @todo Determine correct behavior in transforming comment data. (preserve dashes?)
 */
class HTMLPurifier_Lexer_DirectLex extends HTMLPurifier_Lexer
{
    
    /**
     * Whitespace characters for str(c)spn.
     * @protected
     */
    var $_whitespace = "\x20\x09\x0D\x0A";
    
    function tokenizeHTML($html, $config, &$context) {
        
        $html = $this->normalize($html, $config, $context);
        
        $cursor = 0; // our location in the text
        $inside_tag = false; // whether or not we're parsing the inside of a tag
        $array = array(); // result array
        
        // infinite loop protection
        // has to be pretty big, since html docs can be big
        // we're allow two hundred thousand tags... more than enough?
        $loops = 0;
        
        while(true) {
            
            // infinite loop protection
            if (++$loops > 200000) return array();
            
            $position_next_lt = strpos($html, '<', $cursor);
            $position_next_gt = strpos($html, '>', $cursor);
            
            // triggers on "<b>asdf</b>" but not "asdf <b></b>"
            if ($position_next_lt === $cursor) {
                $inside_tag = true;
                $cursor++;
            }
            
            if (!$inside_tag && $position_next_lt !== false) {
                // We are not inside tag and there still is another tag to parse
                $array[] = new
                    HTMLPurifier_Token_Text(
                        $this->parseData(
                            substr(
                                $html, $cursor, $position_next_lt - $cursor
                            )
                        )
                    );
                $cursor  = $position_next_lt + 1;
                $inside_tag = true;
                continue;
            } elseif (!$inside_tag) {
                // We are not inside tag but there are no more tags
                // If we're already at the end, break
                if ($cursor === strlen($html)) break;
                // Create Text of rest of string
                $array[] = new
                    HTMLPurifier_Token_Text(
                        $this->parseData(
                            substr(
                                $html, $cursor
                            )
                        )
                    );
                break;
            } elseif ($inside_tag && $position_next_gt !== false) {
                // We are in tag and it is well formed
                // Grab the internals of the tag
                $strlen_segment = $position_next_gt - $cursor;
                $segment = substr($html, $cursor, $strlen_segment);
                
                // Check if it's a comment
                if (
                    substr($segment, 0, 3) == '!--' &&
                    substr($segment, $strlen_segment-2, 2) == '--'
                ) {
                    $array[] = new
                        HTMLPurifier_Token_Comment(
                            substr(
                                $segment, 3, $strlen_segment - 5
                            )
                        );
                    $inside_tag = false;
                    $cursor = $position_next_gt + 1;
                    continue;
                }
                
                // Check if it's an end tag
                $is_end_tag = (strpos($segment,'/') === 0);
                if ($is_end_tag) {
                    $type = substr($segment, 1);
                    $array[] = new HTMLPurifier_Token_End($type);
                    $inside_tag = false;
                    $cursor = $position_next_gt + 1;
                    continue;
                }
                
                // Check if it is explicitly self closing, if so, remove
                // trailing slash. Remember, we could have a tag like <br>, so
                // any later token processing scripts must convert improperly
                // classified EmptyTags from StartTags.
                $is_self_closing= (strpos($segment,'/') === $strlen_segment-1);
                if ($is_self_closing) {
                    $strlen_segment--;
                    $segment = substr($segment, 0, $strlen_segment);
                }
                
                // Check if there are any attributes
                $position_first_space = strcspn($segment, $this->_whitespace);
                
                if ($position_first_space >= $strlen_segment) {
                    if ($is_self_closing) {
                        $array[] = new HTMLPurifier_Token_Empty($segment);
                    } else {
                        $array[] = new HTMLPurifier_Token_Start($segment);
                    }
                    $inside_tag = false;
                    $cursor = $position_next_gt + 1;
                    continue;
                }
                
                // Grab out all the data
                $type = substr($segment, 0, $position_first_space);
                $attribute_string =
                    trim(
                        substr(
                            $segment, $position_first_space
                        )
                    );
                if ($attribute_string) {
                    $attr = $this->parseAttributeString(
                                    $attribute_string
                                  , $config, $context
                              );
                } else {
                    $attr = array();
                }
                
                if ($is_self_closing) {
                    $array[] = new HTMLPurifier_Token_Empty($type, $attr);
                } else {
                    $array[] = new HTMLPurifier_Token_Start($type, $attr);
                }
                $cursor = $position_next_gt + 1;
                $inside_tag = false;
                continue;
            } else {
                $array[] = new
                    HTMLPurifier_Token_Text(
                        '<' .
                        $this->parseData(
                            substr($html, $cursor)
                        )
                    );
                break;
            }
            break;
        }
        return $array;
    }
    
    /**
     * Takes the inside of an HTML tag and makes an assoc array of attributes.
     * 
     * @param $string Inside of tag excluding name.
     * @returns Assoc array of attributes.
     */
    function parseAttributeString($string, $config, &$context) {
        $string = (string) $string; // quick typecast
        
        if ($string == '') return array(); // no attributes
        
        // let's see if we can abort as quickly as possible
        // one equal sign, no spaces => one attribute
        $num_equal = substr_count($string, '=');
        $has_space = strpos($string, ' ');
        if ($num_equal === 0 && !$has_space) {
            // bool attribute
            return array($string => $string);
        } elseif ($num_equal === 1 && !$has_space) {
            // only one attribute
            list($key, $quoted_value) = explode('=', $string);
            $quoted_value = trim($quoted_value);
            if (!$key) return array();
            if (!$quoted_value) return array($key => '');
            $first_char = @$quoted_value[0];
            $last_char  = @$quoted_value[strlen($quoted_value)-1];
            
            $same_quote = ($first_char == $last_char);
            $open_quote = ($first_char == '"' || $first_char == "'");
            
            if ( $same_quote && $open_quote) {
                // well behaved
                $value = substr($quoted_value, 1, strlen($quoted_value) - 2);
            } else {
                // not well behaved
                if ($open_quote) {
                    $value = substr($quoted_value, 1);
                } else {
                    $value = $quoted_value;
                }
            }
            return array($key => $value);
        }
        
        // setup loop environment
        $array  = array(); // return assoc array of attributes
        $cursor = 0; // current position in string (moves forward)
        $size   = strlen($string); // size of the string (stays the same)
        
        // if we have unquoted attributes, the parser expects a terminating
        // space, so let's guarantee that there's always a terminating space.
        $string .= ' ';
        
        // infinite loop protection
        $loops = 0;
        
        while(true) {
            
            // infinite loop protection
            if (++$loops > 1000) return array();
            
            if ($cursor >= $size) {
                break;
            }
            
            $cursor += ($value = strspn($string, $this->_whitespace, $cursor));
            
            // grab the key
            
            $key_begin = $cursor; //we're currently at the start of the key
            
            // scroll past all characters that are the key (not whitespace or =)
            $cursor += strcspn($string, $this->_whitespace . '=', $cursor);
            
            $key_end = $cursor; // now at the end of the key
            
            $key = substr($string, $key_begin, $key_end - $key_begin);
            
            if (!$key) continue; // empty key
            
            // scroll past all whitespace
            $cursor += strspn($string, $this->_whitespace, $cursor);
            
            if ($cursor >= $size) {
                $array[$key] = $key;
                break;
            }
            
            // if the next character is an equal sign, we've got a regular
            // pair, otherwise, it's a bool attribute
            $first_char = @$string[$cursor];
            
            if ($first_char == '=') {
                // key="value"
                
                $cursor++;
                $cursor += strspn($string, $this->_whitespace, $cursor);
                
                // we might be in front of a quote right now
                
                $char = @$string[$cursor];
                
                if ($char == '"' || $char == "'") {
                    // it's quoted, end bound is $char
                    $cursor++;
                    $value_begin = $cursor;
                    $cursor = strpos($string, $char, $cursor);
                    $value_end = $cursor;
                } else {
                    // it's not quoted, end bound is whitespace
                    $value_begin = $cursor;
                    $cursor += strcspn($string, $this->_whitespace, $cursor);
                    $value_end = $cursor;
                }
                
                $value = substr($string, $value_begin, $value_end - $value_begin);
                $array[$key] = $this->parseData($value);
                $cursor++;
                
            } else {
                // boolattr
                if ($key !== '') {
                    $array[$key] = $key;
                }
                
            }
        }
        return $array;
    }
    
}

?>