<?php

/**
 *  BENNU - PHP iCalendar library
 *  (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org). All rights reserved.
 *
 *  Released under the LGPL.
 *
 *  See http://bennu.sourceforge.net/ for more information and downloads.
 *
 * @author Ioannis Papaioannou 
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

class iCalendar_component {
    var $name             = NULL;
    var $properties       = NULL;
    var $components       = NULL;
    var $valid_properties = NULL;
    var $valid_components = NULL;
    /**
     * Added to hold errors from last run of unserialize
     * @var $parser_errors array
     */
    var $parser_errors = NULL;

    function __construct() {
        // Initialize the components array
        if(empty($this->components)) {
            $this->components = array();
            foreach($this->valid_components as $name) {
                $this->components[$name] = array();
            }
        }
    }

    function get_name() {
        return $this->name;
    }

    function add_property($name, $value = NULL, $parameters = NULL) {

        // Uppercase first of all
        $name = strtoupper($name);

        // Are we trying to add a valid property?
        $xname = false;
        if(!isset($this->valid_properties[$name])) {
            // If not, is it an x-name as per RFC 2445?
            if(!rfc2445_is_xname($name)) {
                return false;
            }
            // Since this is an xname, all components are supposed to allow this property
            $xname = true;
        }

        // Create a property object of the correct class
        if($xname) {
            $property = new iCalendar_property_x;
            $property->set_name($name);
        }
        else {
            $classname = 'iCalendar_property_'.strtolower(str_replace('-', '_', $name));
            $property = new $classname;
        }

        // If $value is NULL, then this property must define a default value.
        if($value === NULL) {
            $value = $property->default_value();
            if($value === NULL) {
                return false;
            }
        }

        // Set this property's parent component to ourselves, because some
        // properties behave differently according to what component they apply to.
        $property->set_parent_component($this->name);

        // Set parameters before value; this helps with some properties which
        // accept a VALUE parameter, and thus change their default value type.

        // The parameters must be valid according to property specifications
        if(!empty($parameters)) {
            foreach($parameters as $paramname => $paramvalue) {
                if(!$property->set_parameter($paramname, $paramvalue)) {
                    return false;
                }
            }

            // Some parameters interact among themselves (e.g. ENCODING and VALUE)
            // so make sure that after the dust settles, these invariants hold true
            if(!$property->invariant_holds()) {
                return false;
            }
        }

        // $value MUST be valid according to the property data type
        if(!$property->set_value($value)) {
            return false;
        }

        // Check if the property already exists, and is limited to one occurrance,
        // DON'T overwrite the value - this can be done explicity with set_value() instead.
        if(!$xname && $this->valid_properties[$name] & RFC2445_ONCE && isset($this->properties[$name])) {
            return false;
        } 
		else {
             // Otherwise add it to the instance array for this property
            $this->properties[$name][] = $property;
        }

        // Finally: after all these, does the component invariant hold?
        if(!$this->invariant_holds()) {
            // If not, completely undo the property addition
            array_pop($this->properties[$name]);
            if(empty($this->properties[$name])) {
                unset($this->properties[$name]);
            }
            return false;
        }

        return true;        
        
    }

    function add_component($component) {

        // With the detailed interface, you can add only components with this function
        if(!is_object($component) || !is_subclass_of($component, 'iCalendar_component')) {
            return false;
        }

        $name = $component->get_name();

        // Only valid components as specified by this component are allowed
        if(!in_array($name, $this->valid_components)) {
            return false;
        }

        // Add it
        $this->components[$name][] = $component;

        return true;
    }

    function get_property_list($name) {
    }

    function invariant_holds() {
        return true;
    }

    function is_valid() {
        // If we have any child components, check that they are all valid
        if(!empty($this->components)) {
            foreach($this->components as $component => $instances) {
                foreach($instances as $number => $instance) {
                    if(!$instance->is_valid()) {
                        return false;
                    }
                }
            }
        }

        // Finally, check the valid property list for any mandatory properties
        // that have not been set and do not have a default value
        foreach($this->valid_properties as $property => $propdata) {
            if(($propdata & RFC2445_REQUIRED) && empty($this->properties[$property])) {
                $classname = 'iCalendar_property_'.strtolower(str_replace('-', '_', $property));
                $object    = new $classname;
                if($object->default_value() === NULL) {
                    return false;
                }
                unset($object);
            }
        }

        return true;
    }
    
    function serialize() {
        // Check for validity of the object
        if(!$this->is_valid()) {
            return false;
        }

        // Maybe the object is valid, but there are some required properties that
        // have not been given explicit values. In that case, set them to defaults.
        foreach($this->valid_properties as $property => $propdata) {
            if(($propdata & RFC2445_REQUIRED) && empty($this->properties[$property])) {
                $this->add_property($property);
            }
        }

        // Start tag
        $string = rfc2445_fold('BEGIN:'.$this->name) . RFC2445_CRLF;

        // List of properties
        if(!empty($this->properties)) {
            foreach($this->properties as $name => $properties) {
                foreach($properties as $property) {
                    $string .= $property->serialize();
                }
            }
        }

        // List of components
        if(!empty($this->components)) {
            foreach($this->components as $name => $components) {
                foreach($components as $component) {
                    $string .= $component->serialize();
                }
            }
        }

        // End tag
        $string .= rfc2445_fold('END:'.$this->name) . RFC2445_CRLF;

        return $string;
    }
    
    /**
    * unserialize()
    *
    * I needed a way to convert an iCalendar component back to a Bennu object so I could
    * easily access and modify it after it had been stored; if this functionality is already
    * present somewhere in the library, I apologize for adding it here unnecessarily; however,
    * I couldn't find it so I added it myself.
    * @param string $string the iCalendar object to load in to this iCalendar_component
    * @return bool true if the file parsed with no errors. False if there were errors.
    */
    
    function unserialize($string) {
        $string = rfc2445_unfold($string); // Unfold any long lines
        $lines = preg_split("<".RFC2445_CRLF."|\n|\r>", $string, 0, PREG_SPLIT_NO_EMPTY); // Create an array of lines.
        
        $components = array(); // Initialise a stack of components
        $this->clear_errors();
        foreach ($lines as $key => $line) {
            // ignore empty lines
            if (trim($line) == '') {
                continue;
            }

            // Divide the line up into label, parameters and data fields.
            if (!preg_match('#^(?P<label>[-[:alnum:]]+)(?P<params>(?:;(?:(?:[-[:alnum:]]+)=(?:[^[:cntrl:]";:,]+|"[^[:cntrl:]"]+")))*):(?P<data>.*)$#', $line, $match)) {
                $this->parser_error('Invalid line: '.$key.', ignoring');
                continue;
            }

            // parse parameters
            $params = array();
            if (preg_match_all('#;(?P<param>[-[:alnum:]]+)=(?P<value>[^[:cntrl:]";:,]+|"[^[:cntrl:]"]+")#', $match['params'], $pmatch)) {
                $params = array_combine($pmatch['param'], $pmatch['value']);
            } 
            $label = $match['label'];
            $data  = $match['data'];
            unset($match, $pmatch);

            if ($label == 'BEGIN') {
                // This is the start of a component.
                $current_component = array_pop($components); // Get the current component off the stack so we can check its valid components
                if ($current_component == null) { // If there's nothing on the stack
                    $current_component = $this; // use the iCalendar
                }
                if (in_array($data, $current_component->valid_components)) { // Check that the new component is a valid subcomponent of the current one
                    if($current_component != $this) {
                        array_push($components, $current_component); // We're done with the current component, put it back on the stack.
                    }
                    if(strpos($data, 'V') === 0) {
                        $data = substr($data, 1);
                    }
                    $cname = 'iCalendar_' . strtolower($data);
                    $new_component = new $cname;
                    array_push($components, $new_component); // Push a new component onto the stack
                } else {
                    if($current_component != $this) {
                        array_push($components, $current_component);
                        $this->parser_error('Invalid component type on line '.$key);
                    }                        
                }
                unset($current_component, $new_component);
            } else if ($label == 'END') {
                // It's the END of a component.
                $component = array_pop($components); // Pop the top component off the stack - we're now done with it
                $parent_component = array_pop($components); // Pop the component's conatining component off the stack so we can add this component to it.
                if($parent_component == null) {
                    $parent_component = $this; // If there's no components on the stack, use the iCalendar object
                }
                if ($component !== null) {
                    if ($parent_component->add_component($component) === false) {
                        $this->parser_error("Failed to add component on line $key");
                    }
                }
                if ($parent_component != $this) { // If we're not using the iCalendar
                        array_push($components, $parent_component); // Put the component back on the stack
                }
                unset($parent_component, $component);
            } else {
                
                $component = array_pop($components); // Get the component off the stack so we can add properties to it
                if ($component == null) { // If there's nothing on the stack
                    $component = $this; // use the iCalendar
                }

                if ($component->add_property($label, $data, $params) === false) {
                    $this->parser_error("Failed to add property '$label' on line $key");
                }

                if($component != $this) { // If we're not using the iCalendar
                    array_push($components, $component); // Put the component back on the stack
                }
                unset($component);
            }

        }
        
    }

    function clear_errors() {
        $this->parser_errors = array();
    }

    function parser_error($error) {
        $this->parser_errors[] = $error;
    }

}

class iCalendar extends iCalendar_component {
    var $name = 'VCALENDAR';

    function __construct() {
        $this->valid_properties = array(
            'CALSCALE'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'METHOD'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'PRODID'      => RFC2445_REQUIRED | RFC2445_ONCE,
            'VERSION'     => RFC2445_REQUIRED | RFC2445_ONCE,
            RFC2445_XNAME => RFC2445_OPTIONAL 
        );

        $this->valid_components = array(
            'VEVENT', 'VTODO', 'VJOURNAL', 'VFREEBUSY', 'VTIMEZONE', 'VALARM'
        );
        parent::__construct();
    }

}

class iCalendar_event extends iCalendar_component {

    var $name       = 'VEVENT';
    var $properties;
    
    function __construct() {
        
        $this->valid_components = array('VALARM');

        $this->valid_properties = array(
            'CLASS'          => RFC2445_OPTIONAL | RFC2445_ONCE,
            'CREATED'        => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DESCRIPTION'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            // Standard ambiguous here: in 4.6.1 it says that DTSTAMP in optional,
            // while in 4.8.7.2 it says it's REQUIRED. Go with REQUIRED.
            'DTSTAMP'        => RFC2445_REQUIRED | RFC2445_ONCE,
            // Standard ambiguous here: in 4.6.1 it says that DTSTART in optional,
            // while in 4.8.2.4 it says it's REQUIRED. Go with REQUIRED.
            'DTSTART'        => RFC2445_REQUIRED | RFC2445_ONCE,
            'GEO'            => RFC2445_OPTIONAL | RFC2445_ONCE,
            'LAST-MODIFIED'  => RFC2445_OPTIONAL | RFC2445_ONCE,
            'LOCATION'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ORGANIZER'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'PRIORITY'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'SEQUENCE'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'STATUS'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'SUMMARY'        => RFC2445_OPTIONAL | RFC2445_ONCE,
            'TRANSP'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            // Standard ambiguous here: in 4.6.1 it says that UID in optional,
            // while in 4.8.4.7 it says it's REQUIRED. Go with REQUIRED.
            'UID'            => RFC2445_REQUIRED | RFC2445_ONCE,
            'URL'            => RFC2445_OPTIONAL | RFC2445_ONCE,
            'RECURRENCE-ID'  => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTEND'          => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DURATION'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ATTACH'         => RFC2445_OPTIONAL,
            'ATTENDEE'       => RFC2445_OPTIONAL,
            'CATEGORIES'     => RFC2445_OPTIONAL,
            'COMMENT'        => RFC2445_OPTIONAL,
            'CONTACT'        => RFC2445_OPTIONAL,
            'EXDATE'         => RFC2445_OPTIONAL,
            'EXRULE'         => RFC2445_OPTIONAL,
            'REQUEST-STATUS' => RFC2445_OPTIONAL,
            'RELATED-TO'     => RFC2445_OPTIONAL,
            'RESOURCES'      => RFC2445_OPTIONAL,
            'RDATE'          => RFC2445_OPTIONAL,
            'RRULE'          => RFC2445_OPTIONAL,
            RFC2445_XNAME    => RFC2445_OPTIONAL
        );

        parent::__construct();
    }

    function invariant_holds() {
        // DTEND and DURATION must not appear together
        if(isset($this->properties['DTEND']) && isset($this->properties['DURATION'])) {
            return false;
        }

        
        if(isset($this->properties['DTEND']) && isset($this->properties['DTSTART'])) {
            // DTEND must be later than DTSTART
            // The standard is not clear on how to hande different value types though
            // TODO: handle this correctly even if the value types are different
            if($this->properties['DTEND'][0]->value < $this->properties['DTSTART'][0]->value) {
                return false;
            }

            // DTEND and DTSTART must have the same value type
            if($this->properties['DTEND'][0]->val_type != $this->properties['DTSTART'][0]->val_type) {
                return false;
            }

        }
        return true;
    }

}

class iCalendar_todo extends iCalendar_component {
    var $name       = 'VTODO';
    var $properties;

    function __construct() {
        
        $this->valid_components = array('VALARM');

        $this->valid_properties = array(
            'CLASS'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'COMPLETED'   => RFC2445_OPTIONAL | RFC2445_ONCE,
            'CREATED'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DESCRIPTION' => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTSTAMP'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTSTAP'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'GEO'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'LAST-MODIFIED' => RFC2445_OPTIONAL | RFC2445_ONCE,
            'LOCATION'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ORGANIZER'   => RFC2445_OPTIONAL | RFC2445_ONCE,
            'PERCENT'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'PRIORITY'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'RECURID'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'SEQUENCE'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'STATUS'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'SUMMARY'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'UID'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'URL'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DUE'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DURATION'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ATTACH'      => RFC2445_OPTIONAL,
            'ATTENDEE'    => RFC2445_OPTIONAL,
            'CATEGORIES'  => RFC2445_OPTIONAL,
            'COMMENT'     => RFC2445_OPTIONAL,
            'CONTACT'     => RFC2445_OPTIONAL,
            'EXDATE'      => RFC2445_OPTIONAL,
            'EXRULE'      => RFC2445_OPTIONAL,
            'RSTATUS'     => RFC2445_OPTIONAL,
            'RELATED'     => RFC2445_OPTIONAL,
            'RESOURCES'   => RFC2445_OPTIONAL,
            'RDATE'       => RFC2445_OPTIONAL,
            'RRULE'       => RFC2445_OPTIONAL,
            RFC2445_XNAME => RFC2445_OPTIONAL
        );

        parent::__construct();
    }
    
    function invariant_holds() {
        // DTEND and DURATION must not appear together
        if(isset($this->properties['DTEND']) && isset($this->properties['DURATION'])) {
            return false;
        }

        
        if(isset($this->properties['DTEND']) && isset($this->properties['DTSTART'])) {
            // DTEND must be later than DTSTART
            // The standard is not clear on how to hande different value types though
            // TODO: handle this correctly even if the value types are different
            if($this->properties['DTEND'][0]->value <= $this->properties['DTSTART'][0]->value) {
                return false;
            }

            // DTEND and DTSTART must have the same value type
            if($this->properties['DTEND'][0]->val_type != $this->properties['DTSTART'][0]->val_type) {
                return false;
            }

        }
        
        if(isset($this->properties['DUE']) && isset($this->properties['DTSTART'])) {
            if($this->properties['DUE'][0]->value <= $this->properties['DTSTART'][0]->value) {
                return false;
            }   
        }
        
        return true;
    }
    
}

class iCalendar_journal extends iCalendar_component {
    var $name = 'VJOURNAL';
    var $properties;
    
    function __construct() {
    	
        $this->valid_properties = array(
            'CLASS'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'CREATED'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DESCRIPTION'   => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTSTART'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTSTAMP'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'LAST-MODIFIED' => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ORGANIZER'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'RECURRANCE-ID' => RFC2445_OPTIONAL | RFC2445_ONCE,
            'SEQUENCE'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'STATUS'        => RFC2445_OPTIONAL | RFC2445_ONCE,
            'SUMMARY'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'UID'           => RFC2445_OPTIONAL | RFC2445_ONCE,
            'URL'           => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ATTACH'        => RFC2445_OPTIONAL,
            'ATTENDEE'      => RFC2445_OPTIONAL,
            'CATEGORIES'    => RFC2445_OPTIONAL,
            'COMMENT'       => RFC2445_OPTIONAL,
            'CONTACT'       => RFC2445_OPTIONAL,
            'EXDATE'        => RFC2445_OPTIONAL,
            'EXRULE'        => RFC2445_OPTIONAL,
            'RELATED-TO'    => RFC2445_OPTIONAL,
            'RDATE'         => RFC2445_OPTIONAL,
            'RRULE'         => RFC2445_OPTIONAL,
            RFC2445_XNAME   => RFC2445_OPTIONAL            
        );
        
         parent::__construct();
        
    }
}

class iCalendar_freebusy extends iCalendar_component {
    var $name       = 'VFREEBUSY';
    var $properties;

    function __construct() {
        $this->valid_components = array();
        $this->valid_properties = array(
            'CONTACT'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTSTART'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTEND'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DURATION'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'DTSTAMP'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ORGANIZER'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'UID'           => RFC2445_OPTIONAL | RFC2445_ONCE,
            'URL'           => RFC2445_OPTIONAL | RFC2445_ONCE,
            // TODO: the next two are components of their own!
            'ATTENDEE'      => RFC2445_OPTIONAL,
            'COMMENT'       => RFC2445_OPTIONAL,
            'FREEBUSY'      => RFC2445_OPTIONAL,
            'RSTATUS'       => RFC2445_OPTIONAL,
            RFC2445_XNAME   => RFC2445_OPTIONAL
        );
        
        parent::__construct();
    }
    
    function invariant_holds() {
        // DTEND and DURATION must not appear together
        if(isset($this->properties['DTEND']) && isset($this->properties['DURATION'])) {
            return false;
        }

        
        if(isset($this->properties['DTEND']) && isset($this->properties['DTSTART'])) {
            // DTEND must be later than DTSTART
            // The standard is not clear on how to hande different value types though
            // TODO: handle this correctly even if the value types are different
            if($this->properties['DTEND'][0]->value <= $this->properties['DTSTART'][0]->value) {
                return false;
            }

            // DTEND and DTSTART must have the same value type
            if($this->properties['DTEND'][0]->val_type != $this->properties['DTSTART'][0]->val_type) {
                return false;
            }

        }
        return true;
    }
}

class iCalendar_alarm extends iCalendar_component {
    var $name       = 'VALARM';
    var $properties;

    function __construct() {
        $this->valid_components = array();
        $this->valid_properties = array(
            'ACTION'    => RFC2445_REQUIRED | RFC2445_ONCE,
            'TRIGGER'   => RFC2445_REQUIRED | RFC2445_ONCE,
            // If one of these 2 occurs, so must the other.
            'DURATION'  => RFC2445_OPTIONAL | RFC2445_ONCE,
            'REPEAT'    => RFC2445_OPTIONAL | RFC2445_ONCE, 
            // The following is required if action == "PROCEDURE" | "AUDIO"           
            'ATTACH'    => RFC2445_OPTIONAL,
            // The following is required if trigger == "EMAIL" | "DISPLAY" 
            'DESCRIPTION'  => RFC2445_OPTIONAL | RFC2445_ONCE,
            // The following are required if action == "EMAIL"
            'SUMMARY'   => RFC2445_OPTIONAL | RFC2445_ONCE,
            'ATTENDEE'  => RFC2445_OPTIONAL,
            RFC2445_XNAME   => RFC2445_OPTIONAL
        );
     
        parent::__construct();
    }
        
    function invariant_holds() {
        // DTEND and DURATION must not appear together
        if(isset($this->properties['ACTION'])) {
            switch ($this->properties['ACTION'][0]->value) {
            	case 'AUDIO':
                    if (!isset($this->properties['ATTACH'])) {
                    	return false;
                    }
                    break;
                case 'DISPLAY':
                    if (!isset($this->properties['DESCRIPTION'])) {
                    	return false;
                    }
                    break;
                case 'EMAIL':
                    if (!isset($this->properties['DESCRIPTION']) || !isset($this->properties['SUMMARY']) || !isset($this->properties['ATTACH'])) {
                        return false;
                    }
                    break;
                case 'PROCEDURE':
                    if (!isset($this->properties['ATTACH']) || count($this->properties['ATTACH']) > 1) {
                    	return false;
                    }
                    break;
            }
        }
        return true;
    }
        
        
}

class iCalendar_timezone extends iCalendar_component {
    var $name       = 'VTIMEZONE';
    var $properties;

    function __construct() {

        $this->valid_components = array('STANDARD', 'DAYLIGHT');

        $this->valid_properties = array(
            'TZID'        => RFC2445_REQUIRED | RFC2445_ONCE,
            'LAST-MODIFIED'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'TZURL'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            RFC2445_XNAME => RFC2445_OPTIONAL
        );
        
        parent::__construct();
    }

}

class iCalendar_standard extends iCalendar_component {
    var $name       = 'STANDARD';
    var $properties;
    
    function __construct() {
        $this->valid_components = array();
        $this->valid_properties = array(
            'DTSTART'   =>  RFC2445_REQUIRED | RFC2445_ONCE,
            'TZOFFSETTO'    =>  RFC2445_REQUIRED | RFC2445_ONCE,
            'TZOFFSETFROM'  =>  RFC2445_REQUIRED | RFC2445_ONCE,
            'COMMENT'   =>  RFC2445_OPTIONAL,
            'RDATE'   =>  RFC2445_OPTIONAL,
            'RRULE'   =>  RFC2445_OPTIONAL,
            'TZNAME'   =>  RFC2445_OPTIONAL,
            RFC2445_XNAME   =>  RFC2445_OPTIONAL,
        ); 
        parent::__construct();
    }
}

class iCalendar_daylight extends iCalendar_standard {
    var $name   =   'DAYLIGHT';
}

// REMINDER: DTEND must be later than DTSTART for all components which support both
// REMINDER: DUE must be later than DTSTART for all components which support both

