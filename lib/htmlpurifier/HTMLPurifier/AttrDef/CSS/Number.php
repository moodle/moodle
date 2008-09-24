<?php

/**
 * Validates a number as defined by the CSS spec.
 */
class HTMLPurifier_AttrDef_CSS_Number extends HTMLPurifier_AttrDef
{
    
    /**
     * Bool indicating whether or not only positive values allowed.
     */
    var $non_negative = false;
    
    /**
     * @param $non_negative Bool indicating whether negatives are forbidden
     */
    function HTMLPurifier_AttrDef_CSS_Number($non_negative = false) {
        $this->non_negative = $non_negative;
    }
    
    /**
     * @warning Some contexts do not pass $config, $context. These
     *          variables should not be used without checking HTMLPurifier_Length.
     *          This might not work properly in PHP4.
     */
    function validate($number, $config, &$context) {
        
        $number = $this->parseCDATA($number);
        
        if ($number === '') return false;
        
        $sign = '';
        switch ($number[0]) {
            case '-':
                if ($this->non_negative) return false;
                $sign = '-';
            case '+':
                $number = substr($number, 1);
        }
        
        if (ctype_digit($number)) {
            $number = ltrim($number, '0');
            return $number ? $sign . $number : '0';
        }
        if (!strpos($number, '.')) return false;
        
        list($left, $right) = explode('.', $number, 2);
        
        if (!ctype_digit($left)) return false;
        $left = ltrim($left, '0');
        
        $right = rtrim($right, '0');
        
        if ($right === '') {
            return $left ? $sign . $left : '0';
        } elseif (!ctype_digit($right)) {
            return false;
        }
        
        return $sign . $left . '.' . $right;
        
    }
    
}

