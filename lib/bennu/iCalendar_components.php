<?php // $Id$

/**
 *  BENNU - PHP iCalendar library
 *  (c) 2005-2006 Ioannis Papaioannou (pj@moodle.org). All rights reserved.
 *
 *  Released under the LGPL.
 *
 *  See http://bennu.sourceforge.net/ for more information and downloads.
 *
 * @author Ioannis Papaioannou 
 * @version $Id$
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

class iCalendar_component {
    var $name             = NULL;
    var $properties       = NULL;
    var $components       = NULL;
    var $valid_properties = NULL;
    var $valid_components = NULL;

    function iCalendar_component() {
        $this->construct();
    }

    function construct() {
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

        // If this property is restricted to only once, blindly overwrite value
        if(!$xname && $this->valid_properties[$name] & RFC2445_ONCE) {
            $this->properties[$name] = array($property);
        }

        // Otherwise add it to the instance array for this property
        else {
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

}

class iCalendar extends iCalendar_component {
    var $name = 'VCALENDAR';

    function construct() {
        $this->valid_properties = array(
            'CALSCALE'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'METHOD'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'PRODID'      => RFC2445_REQUIRED | RFC2445_ONCE,
            'VERSION'     => RFC2445_REQUIRED | RFC2445_ONCE,
            RFC2445_XNAME => RFC2445_OPTIONAL 
        );

        $this->valid_components = array(
            'VEVENT'
            // TODO: add support for the other component types
            //, 'VTODO', 'VJOURNAL', 'VFREEBUSY', 'VTIMEZONE', 'VALARM'
        );
        parent::construct();
    }

}

class iCalendar_event extends iCalendar_component {

    var $name       = 'VEVENT';
    var $properties;
    
    function construct() {
        
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

        parent::construct();
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

class iCalendar_todo extends iCalendar_component {
    var $name       = 'VTODO';
    var $properties;

    function construct() {

        $this->properties = array(
            'class'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            'completed'   => RFC2445_OPTIONAL | RFC2445_ONCE,
            'created'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'description' => RFC2445_OPTIONAL | RFC2445_ONCE,
            'dtstamp'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'dtstart'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'geo'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'last-modified'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'location'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'organizer'   => RFC2445_OPTIONAL | RFC2445_ONCE,
            'percent'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'priority'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'recurid'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'sequence'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'status'      => RFC2445_OPTIONAL | RFC2445_ONCE,
            'summary'     => RFC2445_OPTIONAL | RFC2445_ONCE,
            'uid'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'url'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'due'         => RFC2445_OPTIONAL | RFC2445_ONCE,
            'duration'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'attach'      => RFC2445_OPTIONAL,
            'attendee'    => RFC2445_OPTIONAL,
            'categories'  => RFC2445_OPTIONAL,
            'comment'     => RFC2445_OPTIONAL,
            'contact'     => RFC2445_OPTIONAL,
            'exdate'      => RFC2445_OPTIONAL,
            'exrule'      => RFC2445_OPTIONAL,
            'rstatus'     => RFC2445_OPTIONAL,
            'related'     => RFC2445_OPTIONAL,
            'resources'   => RFC2445_OPTIONAL,
            'rdate'       => RFC2445_OPTIONAL,
            'rrule'       => RFC2445_OPTIONAL,
            'xprop'       => RFC2445_OPTIONAL
        );

        parent::construct();
        // TODO:
        // either 'due' or 'duration' may appear in  a 'eventprop', but 'due'
        // and 'duration' MUST NOT occur in the same 'eventprop'
    }
}

class iCalendar_journal extends iCalendar_component {
    // TODO: implement
}

class iCalendar_freebusy extends iCalendar_component {
    // TODO: implement
}

class iCalendar_alarm extends iCalendar_component {
    // TODO: implement
}

class iCalendar_timezone extends iCalendar_component {
    var $name       = 'VTIMEZONE';
    var $properties;

    function construct() {

        $this->properties = array(
            'tzid'        => RFC2445_REQUIRED | RFC2445_ONCE,
            'last-modified'    => RFC2445_OPTIONAL | RFC2445_ONCE,
            'tzurl'       => RFC2445_OPTIONAL | RFC2445_ONCE,
            // TODO: the next two are components of their own!
            'standardc'   => RFC2445_REQUIRED,
            'daylightc'   => RFC2445_REQUIRED,
            'x-prop'      => RFC2445_OPTIONAL
        );
        
        parent::construct();
    }

}

// REMINDER: DTEND must be later than DTSTART for all components which support both
// REMINDER: DUE must be later than DTSTART for all components which support both

?>