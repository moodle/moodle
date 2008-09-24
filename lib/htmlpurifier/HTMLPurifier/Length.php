<?php

/**
 * Represents a measurable length, with a string numeric magnitude
 * and a unit. This object is immutable.
 */
class HTMLPurifier_Length
{
    
    /**
     * String numeric magnitude.
     */
    var $n;
    
    /**
     * String unit. False is permitted if $n = 0.
     */
    var $unit;
    
    /**
     * Whether or not this length is valid. Null if not calculated yet.
     */
    var $isValid;
    
    /*
     * @param number $n Magnitude
     * @param string $u Unit
     */
    function HTMLPurifier_Length($n = '0', $u = false) {
        $this->n = (string) $n;
        $this->unit = $u !== false ? (string) $u : false;
    }
    
    /**
     * @param string $s Unit string, like '2em' or '3.4in'
     * @warning Does not perform validation.
     */
    function make($s) {
        if (is_a($s, 'HTMLPurifier_Length')) return $s;
        $n_length = strspn($s, '1234567890.+-');
        $n = substr($s, 0, $n_length);
        $unit = substr($s, $n_length);
        if ($unit === '') $unit = false;
        return new HTMLPurifier_Length($n, $unit);
    }
    
    /**
     * Validates the number and unit.
     */
    function validate() {
        // Special case:

        static $allowedUnits = array(
            'em' => true, 'ex' => true, 'px' => true, 'in' => true,
            'cm' => true, 'mm' => true, 'pt' => true, 'pc' => true
        );
        if ($this->n === '+0' || $this->n === '-0') $this->n = '0';
        if ($this->n === '0' && $this->unit === false) return true;
        if (!ctype_lower($this->unit)) $this->unit = strtolower($this->unit);
        if (!isset($allowedUnits[$this->unit])) return false;
        // Hack:
        $def = new HTMLPurifier_AttrDef_CSS_Number();
        $a = false; // hack hack
        $result = $def->validate($this->n, $a, $a);
        if ($result === false) return false;
        $this->n = $result;
        return true;
    }
    
    /**
     * Returns string representation of number.
     */
    function toString() {
        if (!$this->isValid()) return false;
        return $this->n . $this->unit;
    }
    
    /**
     * Retrieves string numeric magnitude.
     */
    function getN() {return $this->n;}
    
    /**
     * Retrieves string unit.
     */
    function getUnit() {return $this->unit;}
    
    /**
     * Returns true if this length unit is valid.
     */
    function isValid() {
        if ($this->isValid === null) $this->isValid = $this->validate();
        return $this->isValid;
    }
    
    /**
     * Compares two lengths, and returns 1 if greater, -1 if less and 0 if equal.
     * @warning If both values are too large or small, this calculation will
     *          not work properly
     */
    function compareTo($l) {
        if ($l === false) return false;
        if ($l->unit !== $this->unit) {
            $converter = new HTMLPurifier_UnitConverter();
            $l = $converter->convert($l, $this->unit);
            if ($l === false) return false;
        }
        return $this->n - $l->n;
    }
    
}
