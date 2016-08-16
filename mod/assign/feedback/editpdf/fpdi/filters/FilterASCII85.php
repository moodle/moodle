<?php
/**
 * This file is part of FPDI
 *
 * @package   FPDI
 * @copyright Copyright (c) 2015 Setasign - Jan Slabon (http://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 * @version   1.6.1
 */

/**
 * Class FilterASCII85
 */
class FilterASCII85
{
    /**
     * Decode ASCII85 encoded string
     *
     * @param string $in
     * @return string
     * @throws Exception
     */
    public function decode($in)
    {
        $ord = array(
            '~' => ord('~'),
            'z' => ord('z'),
            'u' => ord('u'),
            '!' => ord('!')
        );

        $out = '';
        $state = 0;
        $chn = null;

        $l = strlen($in);

        for ($k = 0; $k < $l; ++$k) {
            $ch = ord($in[$k]) & 0xff;

            if ($ch == $ord['~']) {
                break;
            }
            if (preg_match('/^\s$/',chr($ch))) {
                continue;
            }
            if ($ch == $ord['z'] && $state == 0) {
                $out .= chr(0) . chr(0) . chr(0) . chr(0);
                continue;
            }
            if ($ch < $ord['!'] || $ch > $ord['u']) {
                throw new Exception('Illegal character in ASCII85Decode.');
            }

            $chn[$state++] = $ch - $ord['!'];

            if ($state == 5) {
                $state = 0;
                $r = 0;
                for ($j = 0; $j < 5; ++$j) {
                    $r = (int)($r * 85 + $chn[$j]);
                }

                $out .= chr($r >> 24);
                $out .= chr($r >> 16);
                $out .= chr($r >> 8);
                $out .= chr($r);
            }
        }
        $r = 0;

        if ($state == 1) {
            throw new Exception('Illegal length in ASCII85Decode.');
        }

        if ($state == 2) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + ($chn[1]+1) * 85 * 85 * 85;
            $out .= chr($r >> 24);

        } else if ($state == 3) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85  + ($chn[2]+1) * 85 * 85;
            $out .= chr($r >> 24);
            $out .= chr($r >> 16);

        } else if ($state == 4) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85  + $chn[2] * 85 * 85  + ($chn[3]+1) * 85 ;
            $out .= chr($r >> 24);
            $out .= chr($r >> 16);
            $out .= chr($r >> 8);
        }

        return $out;
    }

    /**
     * NOT IMPLEMENTED
     *
     * @param string $in
     * @return string
     * @throws LogicException
     */
    public function encode($in)
    {
        throw new LogicException("ASCII85 encoding not implemented.");
    }
}