<?php
/**
 * Adapted for Moodle from the AMFPHP Project at http://www.amfphp.org/
 * Creates the methodTable for a service class.
 *
 * @usage $this->methodTable = MethodTable::create($this);
 * @author Christophe Herreman
 * @since 05/01/2005
 * @version $id$
 * 
 * Special contributions by Allessandro Crugnola and Ted Milker
 */

if (!defined('T_ML_COMMENT')) {
   define('T_ML_COMMENT', T_COMMENT);
} else {
   define('T_DOC_COMMENT', T_ML_COMMENT);
}

/**
 * Return string from start of haystack to first occurance of needle, or whole 
 * haystack, if needle does not occur
 *
 * @access public
 * @param $haystack(String) Haystack to search in
 * @param $needle(String) Needle to look for
 */
function strrstr($haystack, $needle)
{
    return substr($haystack, 0, strpos($haystack.$needle,$needle));
}

/**
 * Return substring of haystack from end of needle onwards, or FALSE
 *
 * @access public
 * @param $haystack(String) Haystack to search in
 * @param $needle(String) Needle to look for
 */
function strstrafter($haystack, $needle)
{
    return substr(strstr($haystack, $needle), strlen($needle));
}

class MethodTable
{
    /**
     * Constructor.
     *
     * Since this class should only be accessed through the static create() method
     * this constructor should be made private. Unfortunately, this is not possible
     * in PHP4.
     *
     * @access private
     */
    function MethodTable(){
    }


    /**
     * Creates the methodTable for a passed class.
     *
     * @static
     * @access public
     * @param $sourcePath(String) The path to the file you want to parse
     * @param $containsClass(Bool) True if the file is a class definition (optional)
     */
    function create($sourcePath, $containsClass = false){

        $methodTable = array();
        if(!file_exists($sourcePath))
        {
            return false;
        }
        
        $source = file_get_contents($sourcePath);
        $tokens = (array)token_get_all($source);
        
        $waitingForOpenParenthesis = false;
        $waitingForFunction = false;
        $waitingForClassName = false;
        $bufferingArgs = false;
        $argBuffer = "";
        $lastFunction = "";
        $lastFunctionComment = "";
        $lastComment = "";
        $classMethods = array();
        $realClassName = "";

        if($containsClass) {
            $openBraces = -10000;
        }
        else
        {
            $openBraces = 1;
        }

        $waitingForEndEncapsedString = false;
        foreach($tokens as $token)
        {
           if (is_string($token)) {
                if($token == '{')
                {
                    $openBraces++;
                }
                if($token == '}')
                {
                    if($waitingForEndEncapsedString)
                    {
                        $waitingForEndEncapsedString = false;
                    }
                    else
                    {
                        $lastComment = '';
                        $openBraces--;
                        
                        if($openBraces == 0)
                        {
                            break;
                        }
                    }
                }
                elseif($waitingForOpenParenthesis && $token == '(')
                {
                    $bufferingArgs = true;
                    $argBuffer = "";
                    $waitingForOpenParenthesis = false;
                }
                elseif($bufferingArgs)
                {
                    if($token != ')')
                    {
                        $argBuffer .= $token;
                    }
                    else
                    {
                        if($lastFunction != $realClassName)
                        {
                            $classMethods[] = array("name" => $lastFunction,
                                               "comment" => $lastFunctionComment,
                                               "args" => $argBuffer);
                            
                            $bufferingArgs = false;
                            $argBuffer = "";
                            $lastFunction = "";
                            $lastFunctionComment = "";
                        }
                    }
                    
                }
           } else {
               // token array
               list($id, $text) = $token;
                
                if($bufferingArgs)
                {
                    $argBuffer .= $text;                    
                }
               switch ($id) 
               {
                    
                   case T_COMMENT:
                   case T_ML_COMMENT: // we've defined this
                   case T_DOC_COMMENT: // and this
                   // no action on comments
                        $lastComment = $text;
                        break;
                   case T_FUNCTION:
                        if($openBraces >= 1)
                        {
                            $waitingForFunction = true;
                        }
                        break;
                    case T_STRING:
                        if($waitingForFunction)
                        {
                            $waitingForFunction = false;
                            $waitingForOpenParenthesis = true;
                            $lastFunction = $text;
                            $lastFunctionComment = $lastComment;
                            $lastComment = "";              
                        }
                        if($waitingForClassName)
                        {
                            $waitingForClassName = false;
                            $realClassName = $text;
                        }
                        break;
                    case T_CLASS:
                        $openBraces = 0;
                        $waitingForClassName = true;
                        break;
                    case T_CURLY_OPEN:
                    case T_DOLLAR_OPEN_CURLY_BRACES:
                        $waitingForEndEncapsedString = true;
                        break;
                }
            }
        }
        
        foreach ($classMethods as $key => $value) {
            $methodSignature = $value['args'];
            $methodName = $value['name'];
            $methodComment = $value['comment'];
            
            $description = MethodTable::getMethodDescription($methodComment) . " " . MethodTable::getMethodCommentAttribute($methodComment, "desc");
            $description = trim($description);
            $access = MethodTable::getMethodCommentAttributeFirstWord($methodComment, "access");
            $roles = MethodTable::getMethodCommentAttributeFirstWord($methodComment, "roles");
            $instance = MethodTable::getMethodCommentAttributeFirstWord($methodComment, "instance");
            $returns = MethodTable::getMethodReturnValue($methodComment);
            $pagesize = MethodTable::getMethodCommentAttributeFirstWord($methodComment, "pagesize");
            $params = MethodTable::getMethodCommentArguments($methodComment);

                        
            //description, arguments, access, [roles, [instance, [returns, [pagesize]]]]
            $methodTable[$methodName] = array();
            //$methodTable[$methodName]["signature"] = $methodSignature; //debug purposes
            $methodTable[$methodName]["description"] = ($description == "") ? "No description given." : $description;
            $methodTable[$methodName]["arguments"] = MethodTable::getMethodArguments($methodSignature, $params);
            $methodTable[$methodName]["access"] = ($access == "") ? "private" : $access;

            if($roles != "") $methodTable[$methodName]["roles"] = $roles;
            if($instance != "") $methodTable[$methodName]["instance"] = $instance;
            if($returns != "") $methodTable[$methodName]["returns"] = $returns;
            if($pagesize != "") $methodTable[$methodName]["pagesize"] = $pagesize;
        }
        
        return $methodTable;
        
    }
    
    /**
     * 
     */   
    function getMethodCommentServices($comment)
    {
        $pieces = explode('@service', $comment);
        $args = array();
        if(is_array($pieces) && count($pieces) > 1)
        {
            for($i = 0; $i < count($pieces) - 1; $i++)
            {
                $ps = strrstr($pieces[$i + 1], '@');
                $ps = strrstr($ps, '*/');
                $args[] = MethodTable::cleanComment($ps);
            }
        }
        return $args;
    }
    
    
    /**
     * 
     */
    function getMethodCommentArguments($comment)
    {
        $pieces = explode('@param', $comment);
        $args = array();
        if(is_array($pieces) && count($pieces) > 1)
        {
            for($i = 0; $i < count($pieces) - 1; $i++)
            {
                $ps = strrstr($pieces[$i + 1], '@');
                $ps = strrstr($ps, '*/');
                $args[] = MethodTable::cleanComment($ps);
            }
        }
        return $args;
    }

    
    /**
     * Returns the description from the comment.
     * The description is(are) the first line(s) in the comment.
     *
     * @static
     * @private
     * @param $comment(String) The method's comment.
     */
    function getMethodDescription($comment){
        $comment = MethodTable::cleanComment(strrstr($comment, "@"));
        return trim($comment);
    }
    
    
    /**
     * Returns the value of a comment attribute.
     *
     * @static
     * @private
     * @param $comment(String) The method's comment.
     * @param $attribute(String) The name of the attribute to get its value from.
     */
    function getMethodCommentAttribute($comment, $attribute){
        $pieces = strstrafter($comment, '@' . $attribute);
        if($pieces !== FALSE)
        {
            $pieces = strrstr($pieces, '@');
            $pieces = strrstr($pieces, '*/');
            return MethodTable::cleanComment($pieces);
        }
        return "";
    }
    
    /**
     * Returns the value of a comment attribute.
     *
     * @static
     * @private
     * @param $comment(String) The method's comment.
     * @param $attribute(String) The name of the attribute to get its value from.
     */
    function getMethodCommentAttributeFirstLine($comment, $attribute){
        $pieces = strstrafter($comment, '@' . $attribute);
        if($pieces !== FALSE)
        {
            $pieces = strrstr($pieces, '@');
            $pieces = strrstr($pieces, "*");
            $pieces = strrstr($pieces, "/");
            $pieces = strrstr($pieces, "-");
            $pieces = strrstr($pieces, "\n");
            $pieces = strrstr($pieces, "\r");
            $pieces = strrstr($pieces, '*/');
            return MethodTable::cleanComment($pieces);
        }
        return "";
    }
    
    /**
     * Returns the value of a comment attribute.
     *
     * @static
     * @private
     * @param $comment(String) The method's comment.
     * @param $attribute(String) The name of the attribute to get its value from.
     */
    function getMethodReturnValue($comment){
        $result = array('type' => 'void', 'description' => '');
        $pieces = strstrafter($comment, '@returns');
        if(FALSE == $pieces) $pieces = strstrafter($comment, '@return');
        if($pieces !== FALSE)
        {
            $pieces = strrstr($pieces, '@');
            $pieces = strrstr($pieces, "*");
            $pieces = strrstr($pieces, "/");
            $pieces = strrstr($pieces, "-");
            $pieces = strrstr($pieces, "\n");
            $pieces = strrstr($pieces, "\r");
            $pieces = strrstr($pieces, '*/');
            $pieces = trim(MethodTable::cleanComment($pieces));
            @list($result['type'], $result['description']) = explode(' ', $pieces, 2);
            $result['type'] = MethodTable::standardizeType($result['type']);
        }
        return $result;
    }
    
    function getMethodCommentAttributeFirstWord($comment, $attribute){
        $pieces = strstrafter($comment, '@' . $attribute);
        if($pieces !== FALSE)
        {
            $val = MethodTable::cleanComment($pieces);
            return trim(strrstr($val, ' '));
        }
        return "";
    }
    
    /**
     * Returns an array with the arguments of a method.
     *
     * @static
     * @access private
     * @param $methodSignature(String) The method's signature;
     */
    function getMethodArguments($methodSignature, $commentParams){
        if(strlen($methodSignature) == 0){
            //no arguments, return an empty array
            $result = array();
        }else{
            //clean the arguments before returning them
            $result = MethodTable::cleanArguments(explode(",", $methodSignature), $commentParams);
        }
        
        return $result;
    }
    
    /**
     * Cleans the function or method's return value.
     *
     * @static
     * @access private
     * @param $value(String) The "dirty" value.
     */
    function cleanReturnValue($value){
        $result = array();
        $value  = trim($value);
        
        list($result['type'], $result['description']) = explode(' ', $value, 2);

        $result['type'] = MethodTable::standardizeType($result['type']);

        return $result;
    }


    /**
     * Takes a string and returns the XMLRPC type that most closely matches it.
     *
     * @static
     * @access private
     * @param $type(String) The given type string.
     */
    function standardizeType($type) {
        $type = strtolower($type);
        if('str'  == $type || 'string'  == $type) return 'string';
        if('int'  == $type || 'integer' == $type) return 'int';
        if('bool' == $type || 'boolean' == $type) return 'boolean';
        
        // Note that object is not a valid XMLRPC type
        if('object' == $type || 'class' == $type) return 'object';
        if('float'  == $type || 'dbl'   == $type || 'double' == $type || 'flt' == $type) return 'double';

        // Note that null is not a valid XMLRPC type. The null type can have 
        // only one value - null.
        if('null'   == $type) return 'null';

        // Note that mixed is not a valid XMLRPC type
        if('mixed'  == $type) return 'mixed';
        if('array'  == $type || 'arr'    == $type) return 'array';
        if('assoc'  == $type || 'struct' == $type) return 'struct';

        // Note that this is not a valid XMLRPC type. As references cannot be 
        // serialized or exported, there is no way this could be XML-RPCed.
        if('reference' == $type || 'ref' == $type) return 'reference';
        return 'string';
    }
    
    /**
     * Cleans the arguments array.
     * This method removes all whitespaces and the leading "$" sign from each argument
     * in the array.
     *
     * @static
     * @access private
     * @param $args(Array) The "dirty" array with arguments.
     */
    function cleanArguments($args, $commentParams){
        $result = array();

        if(!is_array($args)) return array();
 
        foreach($args as $index => $arg){
            $arg = strrstr(str_replace(array('$','&$'), array('','&'), $arg), '=');
            if(!isset($commentParams[$index]))
            {
                $result[] = trim($arg);
            }
            else
            {
                $start = trim($arg);
                $end = trim(str_replace('$', '', $commentParams[$index]));
                
                // Suppress Notice of 'Undefined offset' with @ 
                @list($word0, $word1, $tail) = preg_split("/[\s]+/", $end, 3);
                $word0 = strtolower($word0);
                $word1 = strtolower($word1);
                
                $wordBase0 = ereg_replace('^[&$]+','',$word0);
                $wordBase1 = ereg_replace('^[&$]+','',$word1);
                $startBase = strtolower(ereg_replace('^[&$]+','',$start));

                if ($wordBase0 == $startBase) {
                    $type = str_replace(array('(',')'),'', $word1);
                } elseif($wordBase1 == $startBase) {
                    $type = str_replace(array('(',')'),'', $word0);
                } elseif( ereg('(^[&$]+)|(\()([a-z0-9]+)(\)$)', $word0, $regs) ) {
                    $tail = str_ireplace($word0, '', $end);
                    $type = $regs[3];
                } else {
                    // default to string
                    $type = 'string';
                }

                $type = MethodTable::standardizeType($type);
/*
                if($type == 'str') {
                    $type = 'string';
                } elseif($type == 'int' || $type == 'integer') {
                    $type = 'int';
                } elseif($type == 'bool' || $type == 'boolean') {
                    $type = 'boolean';
                } elseif($type == 'object' || $type == 'class') {
                    // Note that this is not a valid XMLRPC type
                    $type = 'object';
                } elseif($type == 'float' || $type == 'dbl' || $type == 'double' || $type == 'flt') {
                    $type = 'double';
                } elseif($type == 'null') {
                    // Note that this is not a valid XMLRPC type
                    // The null type can have only one value - null. Why would 
                    // that be an argument to a function? Just in case:
                    $type = 'null';
                } elseif($type == 'mixed') {
                    // Note that this is not a valid XMLRPC type
                    $type = 'mixed';
                } elseif($type == 'array' || $type == 'arr') {
                    $type = 'array';
                } elseif($type == 'assoc') {
                    $type = 'struct';
                } elseif($type == 'reference' || $type == 'ref') {
                    // Note that this is not a valid XMLRPC type
                    // As references cannot be serialized or exported, there is
                    // no way this could be XML-RPCed.
                    $type = 'reference';
                } else {
                    $type = 'string';
                }
*/
                $result[] = array('type' => $type, 'description' => $start . ' - ' . $tail);
            }
        }

        return $result;
    }
    
    
    /**
     * Cleans the comment string by removing all comment start and end characters.
     *
     * @static
     * @private
     * @param $comment(String) The method's comment.
     */
    function cleanComment($comment){
        $comment = str_replace("/**", "", $comment);
        $comment = str_replace("*/", "", $comment);
        $comment = str_replace("*", "", $comment);
        $comment = str_replace("\n", "\\n", trim($comment));
        $comment = eregi_replace("[\r\t\n ]+", " ", trim($comment));
        $comment = str_replace("\"", "\\\"", $comment);
        return $comment;
    }

    /**
     *
     */
    function showCode($methodTable){

        if(!is_array($methodTable)) $methodTable = array();

        foreach($methodTable as $methodName=>$methodProps){
            $result .= "\n\t\"" . $methodName . "\" => array(";
            
            foreach($methodProps as $key=>$value){
                $result .= "\n\t\t\"" . $key . "\" => ";

                if($key=="arguments"){
                    $result .= "array(";
                    for($i=0; $i<count($value); $i++){
                        $result .= "\"" . addslashes($value[$i]) . "\"";
                        if($i<count($value)-1){
                            $result .= ", ";
                        }
                    }
                    $result .= ")";
                }else{
                    $result .= "\"" . $value . "\"";
                }

                $result .= ",";
            }
            
            $result = substr($result, 0, -1);
            $result .= "\n\t),";
        }
        
        $result = substr($result, 0, -1);
        $result = "\$this->methodTable = array(" . $result;
        $result .= "\n);";
            
        return $result;
    }
}
?>
