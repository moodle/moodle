<?php
global $CFG;
require_once "$CFG->libdir/form/group.php";
require_once "$CFG->libdir/formslib.php";

/**
 * Class for a group of elements used to input a date.
 *
 * Emulates moodle print_date_selector function
 *
 * @author Jamie Pratt <me@jamiep.org>
 * @access public
 */
class MoodleQuickForm_date_selector extends MoodleQuickForm_group
{
    /**
    * Control the fieldnames for form elements
    *
    * startyear => integer start of range of years that can be selected
    * stopyear => integer last year that can be selected
    * timezone => float/string timezone
    * applydst => apply users daylight savings adjustment?
    * optional => if true, show a checkbox beside the date to turn it on (or off)
    */
    var $_options = array('startyear'=>1970, 'stopyear'=>2020,
                    'timezone'=>99, 'applydst'=>true, 'optional'=>false);

   /**
    * These complement separators, they are appended to the resultant HTML
    * @access   private
    * @var      array
    */
    var $_wrap = array('', '');

   /**
    * Class constructor
    *
    * @access   public
    * @param    string  Element's name
    * @param    mixed   Label(s) for an element
    * @param    array   Options to control the element's display
    * @param    mixed   Either a typical HTML attribute string or an associative array
    */
    function MoodleQuickForm_date_selector($elementName = null, $elementLabel = null, $options = array(), $attributes = null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'date_selector';
        // set the options, do not bother setting bogus ones
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->_options[$name])) {
                    if (is_array($value) && is_array($this->_options[$name])) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
    }

    // }}}
    // {{{ _createElements()

    function _createElements()
    {
        $this->_elements = array();
        for ($i=1; $i<=31; $i++) {
            $days[$i] = $i;
        }
        for ($i=1; $i<=12; $i++) {
            $months[$i] = userdate(gmmktime(12,0,0,$i,1,2000), "%B");
        }
        for ($i=$this->_options['startyear']; $i<=$this->_options['stopyear']; $i++) {
            $years[$i] = $i;
        }
        $this->_elements[] =& MoodleQuickForm::createElement('select', 'day', get_string('day', 'form'), $days, $this->getAttributes(), true);
        $this->_elements[] =& MoodleQuickForm::createElement('select', 'month', get_string('month', 'form'), $months, $this->getAttributes(), true);
        $this->_elements[] =& MoodleQuickForm::createElement('select', 'year', get_string('year', 'form'), $years, $this->getAttributes(), true);
        // If optional we add a checkbox which the user can use to turn if on
        if($this->_options['optional']) {
            $this->_elements[] =& MoodleQuickForm::createElement('checkbox', 'on', null, get_string('enable'), $this->getAttributes(), true);
        }
        $this->setValue();

    }

    // }}}
    // {{{ onQuickFormEvent()

    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ('updateValue' == $event) {
            return HTML_QuickForm_element::onQuickFormEvent($event, $arg, $caller);
        } else {
            return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }
    // {{{ setValue()

    function setValue($value=0)
    {
        $requestvalue=$value;
        if (!($value)) {
            $value = time();
        }
        if (!is_array($value)) {
            $currentdate = usergetdate($value);
            $value = array(
                'day' => $currentdate['mday'],
                'month' => $currentdate['mon'],
                'year' => $currentdate['year']);
            // If optional, default to off, unless a date was provided
            if($this->_options['optional']) {
                $value['on'] = $requestvalue ? true : false;
            }
        }
        parent::setValue($value);
    }

    // }}}
    // {{{ toHtml()

    function toHtml()
    {
        include_once('HTML/QuickForm/Renderer/Default.php');
        $renderer =& new HTML_QuickForm_Renderer_Default();
        $renderer->setElementTemplate('{element}');
        parent::accept($renderer);
        return $this->_wrap[0] . $renderer->toHtml() . $this->_wrap[1];
    }

    // }}}
    // {{{ accept()

    function accept(&$renderer, $required = false, $error = null)
    {
        $renderer->renderElement($this, $required, $error);
    }

    // }}}

    /**
     * Output a timestamp. Give it the name of the group.
     *
     * @param array $submitValues
     * @param bool $assoc
     * @return array
     */
    function exportValue(&$submitValues, $assoc = false)
    {
        $value = null;
        $valuearray = array();
        foreach ($this->_elements as $element){
            $thisexport = $element->exportValue($submitValues[$this->getName()], true);
            if ($thisexport!=null){
                $valuearray += $thisexport;
            }
        }
        if (count($valuearray)){
            if($this->_options['optional']) {
                // If checkbox is not on, the value is zero, so go no further
                if(empty($valuearray['on'])) {
                    $value[$this->getName()]=0;
                    return $value;
                }
            }
            $value[$this->getName()]=make_timestamp($valuearray['year'],
                                   $valuearray['month'],
                                   $valuearray['day'],
                                   0,0,0,
                                   $this->_options['timezone'],
                                   $this->_options['applydst']);

            return $value;
        } else {
            return null;
        }
    }

    // }}}
}
?>