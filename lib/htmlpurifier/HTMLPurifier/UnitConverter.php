<?php

/**
 * Class for converting between different unit-lengths as specified by
 * CSS.
 */
class HTMLPurifier_UnitConverter
{
    
    /**
     * Minimum bcmath precision for output.
     */
    var $outputPrecision;
    
    /**
     * Bcmath precision for internal calculations.
     */
    var $internalPrecision;
    
    /**
     * Whether or not BCMath is available
     */
    var $bcmath;
    
    function HTMLPurifier_UnitConverter($output_precision = 4, $internal_precision = 10, $force_no_bcmath = false) {
        $this->outputPrecision = $output_precision;
        $this->internalPrecision = $internal_precision;
        $this->bcmath = !$force_no_bcmath && function_exists('bcmul');
    }
    
    /**
     * Converts a length object of one unit into another unit.
     * @param HTMLPurifier_Length $length
     *      Instance of HTMLPurifier_Length to convert. You must validate()
     *      it before passing it here!
     * @param string $to_unit
     *      Unit to convert to.
     * @note
     *      About precision: This conversion function pays very special
     *      attention to the incoming precision of values and attempts
     *      to maintain a number of significant figure. Results are
     *      fairly accurate up to nine digits. Some caveats:
     *          - If a number is zero-padded as a result of this significant
     *            figure tracking, the zeroes will be eliminated.
     *          - If a number contains less than four sigfigs ($outputPrecision)
     *            and this causes some decimals to be excluded, those
     *            decimals will be added on.
     */
    function convert($length, $to_unit) {
        
        /**
         * Units information array. Units are grouped into measuring systems
         * (English, Metric), and are assigned an integer representing
         * the conversion factor between that unit and the smallest unit in
         * the system. Numeric indexes are actually magical constants that
         * encode conversion data from one system to the next, with a O(n^2)
         * constraint on memory (this is generally not a problem, since
         * the number of measuring systems is small.)
         */
        static $units = array(
            1 => array(
                'px' => 3, // This is as per CSS 2.1 and Firefox. Your mileage may vary
                'pt' => 4,
                'pc' => 48,
                'in' => 288,
                2 => array('pt', '0.352777778', 'mm'),
            ),
            2 => array(
                'mm' => 1,
                'cm' => 10,
                1 => array('mm', '2.83464567', 'pt'),
            ),
        );
        
        if (!$length->isValid()) return false;
        
        $n    = $length->getN();
        $unit = $length->getUnit();
        
        if ($n === '0' || $unit === false) {
            return new HTMLPurifier_Length('0', false);
        }
        
        $state = $dest_state = false;
        foreach ($units as $k => $x) {
            if (isset($x[$unit])) $state = $k;
            if (isset($x[$to_unit])) $dest_state = $k;
        }
        if (!$state || !$dest_state) return false;
        
        // Some calculations about the initial precision of the number;
        // this will be useful when we need to do final rounding.
        $sigfigs = $this->getSigFigs($n);
        if ($sigfigs < $this->outputPrecision) $sigfigs = $this->outputPrecision;
        
        // Cleanup $n for PHP 4.3.9 and 4.3.10. See http://bugs.php.net/bug.php?id=30726
        if (strncmp($n, '-.', 2) === 0) {
            $n = '-0.' . substr($n, 2);
        }
        
        // BCMath's internal precision deals only with decimals. Use
        // our default if the initial number has no decimals, or increase
        // it by how ever many decimals, thus, the number of guard digits
        // will always be greater than or equal to internalPrecision.
        $log = (int) floor(log(abs($n), 10));
        $cp = ($log < 0) ? $this->internalPrecision - $log : $this->internalPrecision; // internal precision
        
        for ($i = 0; $i < 2; $i++) {
            
            // Determine what unit IN THIS SYSTEM we need to convert to
            if ($dest_state === $state) {
                // Simple conversion
                $dest_unit = $to_unit;
            } else {
                // Convert to the smallest unit, pending a system shift
                $dest_unit = $units[$state][$dest_state][0];
            }
            
            // Do the conversion if necessary
            if ($dest_unit !== $unit) {
                $factor = $this->div($units[$state][$unit], $units[$state][$dest_unit], $cp);
                $n = $this->mul($n, $factor, $cp);
                $unit = $dest_unit;
            }
            
            // Output was zero, so bail out early. Shouldn't ever happen.
            if ($n === '') {
                $n = '0';
                $unit = $to_unit;
                break;
            }
            
            // It was a simple conversion, so bail out
            if ($dest_state === $state) {
                break;
            }
            
            if ($i !== 0) {
                // Conversion failed! Apparently, the system we forwarded
                // to didn't have this unit. This should never happen!
                return false;
            }
            
            // Pre-condition: $i == 0
            
            // Perform conversion to next system of units
            $n = $this->mul($n, $units[$state][$dest_state][1], $cp);
            $unit = $units[$state][$dest_state][2];
            $state = $dest_state;
            
            // One more loop around to convert the unit in the new system.
            
        }
        
        // Post-condition: $unit == $to_unit
        if ($unit !== $to_unit) return false;
        
        // Useful for debugging:
        //echo "<pre>n";
        //echo "$n\nsigfigs = $sigfigs\nnew_log = $new_log\nlog = $log\nrp = $rp\n</pre>\n";
        
        $n = $this->round($n, $sigfigs);
        if (strpos($n, '.') !== false) $n = rtrim($n, '0');
        $n = rtrim($n, '.');
        
        return new HTMLPurifier_Length($n, $unit);
    }
    
    /**
     * Returns the number of significant figures in a string number.
     * @param string $n Decimal number
     * @return int number of sigfigs
     */
    function getSigFigs($n) {
        $n = ltrim($n, '0+-');
        $dp = strpos($n, '.'); // decimal position
        if ($dp === false) {
            $sigfigs = strlen(rtrim($n, '0'));
        } else {
            $sigfigs = strlen(ltrim($n, '0.')); // eliminate extra decimal character
            if ($dp !== 0) $sigfigs--;
        }
        return $sigfigs;
    }
    
    /**
     * Adds two numbers, using arbitrary precision when available.
     */
    function add($s1, $s2, $scale) {
        if ($this->bcmath) return bcadd($s1, $s2, $scale);
        else return $this->scale($s1 + $s2, $scale);
    }
    
    /**
     * Multiples two numbers, using arbitrary precision when available.
     */
    function mul($s1, $s2, $scale) {
        if ($this->bcmath) return bcmul($s1, $s2, $scale);
        else return $this->scale($s1 * $s2, $scale);
    }
    
    /**
     * Divides two numbers, using arbitrary precision when available.
     */
    function div($s1, $s2, $scale) {
        if ($this->bcmath) return bcdiv($s1, $s2, $scale);
        else return $this->scale($s1 / $s2, $scale);
    }
    
    /**
     * Rounds a number according to the number of sigfigs it should have,
     * using arbitrary precision when available.
     */
    function round($n, $sigfigs) {
        $new_log = (int) floor(log(abs($n), 10)); // Number of digits left of decimal - 1
        $rp = $sigfigs - $new_log - 1; // Number of decimal places needed
        $neg = $n < 0 ? '-' : ''; // Negative sign
        if ($this->bcmath) {
            if ($rp >= 0) {
                $n = bcadd($n, $neg . '0.' .  str_repeat('0', $rp) . '5', $rp + 1);
                $n = bcdiv($n, '1', $rp);
            } else {
                // This algorithm partially depends on the standardized
                // form of numbers that comes out of bcmath.
                $n = bcadd($n, $neg . '5' . str_repeat('0', $new_log - $sigfigs), 0);
                $n = substr($n, 0, $sigfigs + strlen($neg)) . str_repeat('0', $new_log - $sigfigs + 1);
            }
            return $n;
        } else {
            return $this->scale(round($n, $sigfigs - $new_log - 1), $rp + 1);
        }
    }
    
    /**
     * Scales a float to $scale digits right of decimal point, like BCMath.
     */
    function scale($r, $scale) {
        return sprintf('%.' . $scale . 'f', (float) $r);
    }
    
}
