<?php
/**
 * DHTML replacement for the standard JavaScript alert window for client-side
 * validation
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HTML
 * @package    HTML_QuickForm_DHTMLRulesTableless
 * @author     Alexey Borzov <borz_off@cs.msu.su>
 * @author     Adam Daniel <adaniel1@eesus.jnj.com>
 * @author     Bertrand Mansion <bmansion@mamasam.com>
 * @author     Justin Patrin <papercrane@gmail.com>
 * @author     Mark Wiesemann <wiesemann@php.net>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/HTML_QuickForm_DHTMLRulesTableless
 */

require_once 'HTML/QuickForm.php';

/**
 * This is a DHTML replacement for the standard JavaScript alert window for
 * client-side validation of forms built with HTML_QuickForm
 *
 * @category   HTML
 * @package    HTML_QuickForm_DHTMLRulesTableless
 * @author     Alexey Borzov <borz_off@cs.msu.su>
 * @author     Adam Daniel <adaniel1@eesus.jnj.com>
 * @author     Bertrand Mansion <bmansion@mamasam.com>
 * @author     Justin Patrin <papercrane@gmail.com>
 * @author     Mark Wiesemann <wiesemann@php.net>
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 0.1.3
 * @link       http://pear.php.net/package/HTML_QuickForm_DHTMLRulesTableless
 */
class HTML_QuickForm_DHTMLRulesTableless extends HTML_QuickForm {
    // {{{ getValidationScript()

    /**
     * Returns the client side validation script
     *
     * The code here was copied from HTML_QuickForm and slightly modified to run rules per-element
     *
     * @access    public
     * @return    string    Javascript to perform validation, empty string if no 'client' rules were added
     */
    function getValidationScript()
    {
        if (empty($this->_rules) || empty($this->_attributes['onsubmit'])) {
            return '';
        }

        include_once('HTML/QuickForm/RuleRegistry.php');
        $registry =& HTML_QuickForm_RuleRegistry::singleton();
        $test = array();
        $js_escape = array(
            "\r"    => '\r',
            "\n"    => '\n',
            "\t"    => '\t',
            "'"     => "\\'",
            '"'     => '\"',
            '\\'    => '\\\\'
        );

        foreach ($this->_rules as $elementName => $rules) {
            foreach ($rules as $rule) {
                if ('client' == $rule['validation']) {
                    unset($element);

                    $dependent  = isset($rule['dependent']) && is_array($rule['dependent']);
                    $rule['message'] = strtr($rule['message'], $js_escape);

                    if (isset($rule['group'])) {
                        $group    =& $this->getElement($rule['group']);
                        // No JavaScript validation for frozen elements
                        if ($group->isFrozen()) {
                            continue 2;
                        }
                        $elements =& $group->getElements();
                        foreach (array_keys($elements) as $key) {
                            if ($elementName == $group->getElementName($key)) {
                                $element =& $elements[$key];
                                break;
                            }
                        }
                    } elseif ($dependent) {
                        $element   =  array();
                        $element[] =& $this->getElement($elementName);
                        foreach ($rule['dependent'] as $idx => $elName) {
                            $element[] =& $this->getElement($elName);
                        }
                    } else {
                        $element =& $this->getElement($elementName);
                    }
                    // No JavaScript validation for frozen elements
                    if (is_object($element) && $element->isFrozen()) {
                        continue 2;
                    } elseif (is_array($element)) {
                        foreach (array_keys($element) as $key) {
                            if ($element[$key]->isFrozen()) {
                                continue 3;
                            }
                        }
                    }

                    $test[$elementName][] = $registry->getValidationScript($element, $elementName, $rule);
                }
            }
        }
        $js = '
<script type="text/javascript">
//<![CDATA[
function qf_errorHandler(element, _qfMsg) {
  div = element.parentNode;
  if (_qfMsg != \'\') {
    span = document.createElement("span");
    span.className = "error";
    span.appendChild(document.createTextNode(_qfMsg.substring(3)));
    br = document.createElement("br");

    var errorDiv = document.getElementById(element.name + \'_errorDiv\');
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.id = element.name + \'_errorDiv\';
    }
    while (errorDiv.firstChild) {
      errorDiv.removeChild(errorDiv.firstChild);
    }
    
    errorDiv.insertBefore(br, errorDiv.firstChild);
    errorDiv.insertBefore(span, errorDiv.firstChild);
    element.parentNode.insertBefore(errorDiv, element.parentNode.firstChild);

    if (div.className.substr(div.className.length - 6, 6) != " error"
        && div.className != "error") {
      div.className += " error";
    }

    return false;
  } else {
    var errorDiv = document.getElementById(element.name + \'_errorDiv\');
    if (errorDiv) {
      errorDiv.parentNode.removeChild(errorDiv);
    }

    if (div.className.substr(div.className.length - 6, 6) == " error") {
      div.className = div.className.substr(0, div.className.length - 6);
    } else if (div.className == "error") {
      div.className = "";
    }

    return true;
  }
}';
        $validateJS = '';
        foreach ($test as $elementName => $jsArr) {
            $js .= '
function validate_' . $this->_attributes['id'] . '_' . $elementName . '(element) {
  var value = \'\';
  var errFlag = new Array();
  var _qfGroups = {};
  var _qfMsg = \'\';
  var frm = element.parentNode;
  while (frm && frm.nodeName != "FORM") {
    frm = frm.parentNode;
  }
' . join("\n", $jsArr) . '
  return qf_errorHandler(element, _qfMsg);
}
';
            $validateJS .= '
  ret = validate_' . $this->_attributes['id'] . '_' . $elementName.'(frm.elements[\''.$elementName.'\']) && ret;';
            unset($element);
            $element =& $this->getElement($elementName);
            $valFunc = 'validate_' . $this->_attributes['id'] . '_' . $elementName . '(this)';
            $onBlur = $element->getAttribute('onBlur');
            $onChange = $element->getAttribute('onChange');
            $element->updateAttributes(array('onBlur' => $onBlur . $valFunc,
                                             'onChange' => $onChange . $valFunc));
        }
        $js .= '
function validate_' . $this->_attributes['id'] . '(frm) {
  var ret = true;
' . $validateJS . ';
  return ret;
}
//]]>
</script>';
        return $js;
    } // end func getValidationScript

    // }}}

    function display() {
        $this->getValidationScript();
        return parent::display();
    }
}

?>