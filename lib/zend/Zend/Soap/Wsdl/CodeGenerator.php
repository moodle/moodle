<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Soap/Wsdl/Parser.php';

/**
 * Zend_Soap_Wsdl_CodeGenerator
 * 
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Wsdl_CodeGenerator {
   
    /**
     * @var string WSDL Filename/URI
     */
    private static $filename = null;
    
    /**
     * @var string PHP Code for output
     */
    private static $php_code;
    
    /**
     * @var object Zend_Soap_Wsdl_Parser Result
     */
    private static $wsdl;

    /**
     * Constructor
     *
     * @param string $wsdl Filename, URI or XML for the WSDL
     * @param string $output Output file name, default: null
     */
    public static function parse($wsdl, $output = null)
    {
        self::$wsdl = Zend_Soap_Wsdl_Parser::parse($wsdl);
        
        self::$php_code = self::generatePhp();
        
        if (!is_null($output) && is_writable($output)) {
            file_put_contents($output);
        }
        
        return self::$php_code;
        
    }
    
    /**
     * Generate the output PHP
     *
     * @return string
     */
    private function generatePhp()
    {
        $php_code = '<?php' . "\n";
        if (isset(self::$wsdl->documentation)) {
            $docs = self::$wsdl->documentation;
            $docs = explode("\n", $docs);
            $php_code .= "/**\n";
            foreach ($docs as $line) {
                $php_code .= ' * ' .trim($line). PHP_EOL;
            }
            $php_code .= " */\n\n";
        }
        if (!isset(self::$wsdl->name)) {
            $classname = 'SoapService';
        } else {
            $classname = self::$wsdl->name;
        }                
            
        $php_code .= "class {$classname} {\n";
        
        foreach (self::$wsdl->operations as $name => $io) {
            if (isset($io['documentation'])) {
                $php_code .= "\n\t/**\n";
                $docs = $io['documentation'];
                $docs = explode("\n", $docs);
                foreach ($docs as $line) {
                    $php_code .= "\t * " .trim($line). PHP_EOL;
                }
                $php_code .= "\t */\n";
            }
            $php_code .= "\n\tpublic function {$name} (";
            if (isset($io['input'])) {
                $arg_names = array();
                foreach ($io['input'] as $arg) {
                    $arg_names[] = $arg['name'];
                }
                $php_code .= '$' .implode(', $', $arg_names);
            }
            $php_code .= ')';
            $php_code .= "\n\t{";
            $php_code .= "\n\t\t\n";
            if (isset($io['output'])) {
                $php_code .= "\t\treturn \${$io['output']['name']};\n";
            }
            $php_code .= "\t}\n";
        }
        
        $php_code .= "\n}";
        
        $php_code .= PHP_EOL. "\$server = new SoapServer;" .PHP_EOL;
        $php_code .= "\$server->setClass($classname);";
        $php_code .= "\n?>";
        return $php_code;
    }
}

