<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */

/**
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */
class Horde_Support_Numerizer_Locale_Base
{
    public $DIRECT_NUMS = array(
        'eleven' => '11',
        'twelve' => '12',
        'thirteen' => '13',
        'fourteen' => '14',
        'fifteen' => '15',
        'sixteen' => '16',
        'seventeen' => '17',
        'eighteen' => '18',
        'nineteen' => '19',
        'ninteen' => '19',      // Common mis-spelling
        'zero' => '0',
        'one' => '1',
        'two' => '2',
        'three' => '3',
        'four(\W|$)' => '4$1',  // The weird regex is so that it matches four but not fourty
        'five' => '5',
        'six(\W|$)' => '6$1',
        'seven(\W|$)' => '7$1',
        'eight(\W|$)' => '8$1',
        'nine(\W|$)' => '9$1',
        'ten' => '10',
        '\ba[\b^$]' => '1',     // doesn't make sense for an 'a' at the end to be a 1
    );

    public $TEN_PREFIXES = array(
        'twenty' => 20,
        'thirty' => 30,
        'forty' => 40,
        'fourty' => 40, // Common mis-spelling
        'fifty' => 50,
        'sixty' => 60,
        'seventy' => 70,
        'eighty' => 80,
        'ninety' => 90,
        'ninty' => 90, // Common mis-spelling
    );

    public $BIG_PREFIXES = array(
        'hundred' => 100,
        'thousand' => 1000,
        'million' => 1000000,
        'billion' => 1000000000,
        'trillion' => 1000000000000,
    );

    public function numerize($string)
    {
        // preprocess
        $string = $this->_splitHyphenatedWords($string);
        $string = $this->_hideAHalf($string);

        $string = $this->_directReplacements($string);
        $string = $this->_replaceTenPrefixes($string);
        $string = $this->_replaceBigPrefixes($string);
        $string = $this->_fractionalAddition($string);

        return $string;
    }

    /**
     * will mutilate hyphenated-words but shouldn't matter for date extraction
     */
    protected function _splitHyphenatedWords($string)
    {
        return preg_replace('/ +|([^\d])-([^d])/', '$1 $2', $string);
    }

    /**
     * take the 'a' out so it doesn't turn into a 1, save the half for the end
     */
    protected function _hideAHalf($string)
    {
        return str_replace('a half', 'haAlf', $string);
    }

    /**
     * easy/direct replacements
     */
    protected function _directReplacements($string)
    {
        foreach ($this->DIRECT_NUMS as $dn => $dn_replacement) {
            $string = preg_replace("/$dn/i", $dn_replacement, $string);
        }
        return $string;
    }

    /**
     * ten, twenty, etc.
     */
    protected function _replaceTenPrefixes($string)
    {
        foreach ($this->TEN_PREFIXES as $tp => $tp_replacement) {
            $string = preg_replace_callback(
                "/(?:$tp)( *\d(?=[^\d]|\$))*/i",
                function ($m) use ($tp_replacement) {
                    return $tp_replacement + (isset($m[1]) ? (int)$m[1] : 0);
                },
                $string
            );
        }
        return $string;
    }

    /**
     * hundreds, thousands, millions, etc.
     */
    protected function _replaceBigPrefixes($string)
    {
        foreach ($this->BIG_PREFIXES as $bp => $bp_replacement) {
            $string = preg_replace_callback(
                '/(\d*) *' . $bp . '/i',
                function ($m) use ($bp_replacement) {
                    return $bp_replacement * (int)$m[1];
                },
                $string
            );
            $string = $this->_andition($string);
        }
        return $string;
    }

    protected function _andition($string)
    {
        while (true) {
            if (preg_match('/(\d+)( | and )(\d+)(?=[^\w]|$)/i', $string, $sc, PREG_OFFSET_CAPTURE)) {
                if (preg_match('/and/', $sc[2][0]) || (strlen($sc[1][0]) > strlen($sc[3][0]))) {
                    $string = substr($string, 0, $sc[1][1]) . ((int)$sc[1][0] + (int)$sc[3][0]) . substr($string, $sc[3][1] + strlen($sc[3][0]));
                    continue;
                }
            }
            break;
        }
        return $string;
    }

    protected function _fractionalAddition($string)
    {
        return preg_replace_callback(
            '/(\d+)(?: | and |-)*haAlf/i',
            function ($m) {
                return (string)((float)$m[1] + 0.5);
            },
            $string
        );
    }

}
