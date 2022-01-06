<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @author   Jan Schneider <jan@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */

/**
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @author   Jan Schneider <jan@horde.org>
 * @license  http://www.horde.org/licenses/bsd BSD
 * @category Horde
 * @package  Support
 */
class Horde_Support_Numerizer_Locale_De extends Horde_Support_Numerizer_Locale_Base
{
    public $DIRECT_NUMS = array(
        'dreizehn' => 13,
        'vierzehn' => 14,
        'fünfzehn' => 15,
        'sechzehn' => 16,
        'siebzehn' => 17,
        'achtzehn' => 18,
        'neunzehn' => 19,
        'ein[se]?' => 1,
        'zwei' => 2,
        'zwo' => 2,
        'drei' => 3,
        'vier' => 4,
        'fünf' => 5,
        'sechs' => 6,
        'sieben' => 7,
        'acht' => 8,
        'neun' => 9,
        'zehn' => 10,
        'elf' => 11,
        'zwölf' => 12,
    );

    public $TEN_PREFIXES = array(
        'zwanzig' => 20,
        'dreißig' => 30,
        'vierzig' => 40,
        'fünfzig' => 50,
        'sechzig' => 60,
        'siebzig' => 70,
        'achtzig' => 80,
        'neunzig' => 90,
    );

    public $BIG_PREFIXES = array(
        'hundert' => 100,
        'tausend' => 1000,
        'million *' => 1000000,
        'milliarde *' => 1000000000,
        'billion *' => 1000000000000,
    );

    /**
     * Rules:
     *
     * - there are irregular word for 11 and 12 like in English
     * - numbers below one million are written together (1 M = "eine Million", 100 = "einhundert")
     * - "a" is declinable (see above, "one" = "eins", "a" = "ein/eine")
     * - numbers below 100 are flipped compared to english, and have an "and = "und" (21 = "twenty-one" = "einundzwanzig")
     */
    public function numerize($string)
    {
        $string = $this->_replaceTenPrefixes($string);
        $string = $this->_directReplacements($string);
        $string = $this->_replaceBigPrefixes($string);
        $string = $this->_fractionalAddition($string);

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
                        . ($bp_replacement == 100 ? ($m[2] ? 'und' : '') : 'und')
                        . $m[2];
                },
                $string
            );
            $string = $this->_andition($string);
        }
        return $string;
    }

    protected function _andition($string)
    {
        while (preg_match('/(\d+)((?: *und *)+)(\d*)(?=\w|$)/i', $string, $sc, PREG_OFFSET_CAPTURE)) {
            $string = substr($string, 0, $sc[1][1])
                . ((int)$sc[1][0] + (int)$sc[3][0])
                . substr($string, $sc[3][1] + strlen($sc[3][0]));
        }
        return $string;
    }

}
