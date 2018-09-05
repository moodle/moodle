<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */

/**
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */
class Horde_Support_Numerizer_Locale_Pt extends Horde_Support_Numerizer_Locale_Base
{
    public $DIRECT_NUMS = array(
        'treze' => '13',
        'catorze' => '14',
        'quatorze' => '14',
        'quinze' => '15',
        'dezasseis' => '16',
        'dezassete' => '17',
        'dezoito' => '18',
        'dezanove' => '19',
        'um(\W|$)' => '1$1',
        'uma(\W|$)' => '1$1',
        'dois' => '2',
        'duas' => '2',
        'tres' => '3',
        'quatro' => '4',
        'cinco' => '5',
        'seis' => '6',
        'sete' => '7',
        'oito' => '8',
        'nove' => '9',
        'dez' => '10',
        'onze' => '11',
        'doze' => '12',
    );

    public $TEN_PREFIXES = array(
        'vinte' => '20',
        'trinta' => '30',
        'quarenta' => '40',
        'cinquenta' => '50',
        'sessenta' => '60',
        'setenta' => '70',
        'oitenta' => '80',
        'noventa' => '90',
    );

    public $BIG_PREFIXES = array(
        'cem' => '100',
        'mil' => '1000',
        'milhao *' => '1000000',
        'milhar de *' => '1000000000',
        'biliao *' => '1000000000000',
    );

    public function numerize($string)
    {
        // preprocess
        $string = $this->_splitHyphenateWords($string);
        $string = $this->_replaceTenPrefixes($string);
        $string = $this->_directReplacements($string);
        $string = $this->_replaceBigPrefixes($string);
//        $string = $this->_fractionalAddition($string);

        return $string;
    }


    /**
     * will mutilate hyphenated-words but shouldn't matter for date extraction
     */
    protected function _splitHyphenateWords($string)
    {
        return preg_replace('/ +|([^\d]) e? ([^d])/', '$1 $2', $string);
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
                '/(\d*) *' . $bp . '(\d?)/i',
                function ($m) use ($bp_replacement) {
                    $factor = (int)$m[1];
                    if (!$factor) {
                        $factor = 1;
                    }
                    return ($bp_replacement * $factor)
                        . ($bp_replacement == 100 ? ($m[2] ? 'e' : '') : 'e')
                        . $m[2];
                },
                $string);
            $string = $this->_andition($string);
        }
        return $string;
    }

    protected function _andition($string)
    {
        while (preg_match('/(\d+)((?: *e *)+)(\d*)(?=\w|$)/i', $string, $sc, PREG_OFFSET_CAPTURE)) {
            $string = substr($string, 0, $sc[1][1]) . ((int)$sc[1][0] + (int)$sc[3][0]) . substr($string, $sc[3][1] + strlen($sc[3][0]));
        }
        return $string;
    }

    protected function _fractionalAddition($string)
    {
        return preg_replace_callback(
            '/(\d+)(?: | e |-)*/i',
            function ($m) {
                return (string)((float)$m[1] + 0.5);
            },
            $string
        );
    }

}
