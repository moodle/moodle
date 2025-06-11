<?php

/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2023 Setasign GmbH & Co. KG (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Filter;

/**
 * Class for handling ASCII base-85 encoded data
 */
class Ascii85 implements FilterInterface
{
    /**
     * Decode ASCII85 encoded string.
     *
     * @param string $data The input string
     * @return string
     * @throws Ascii85Exception
     */
    public function decode($data)
    {
        $out = '';
        $state = 0;
        $chn = null;

        $data = \preg_replace('/\s/', '', $data);

        $l = \strlen($data);

        /** @noinspection ForeachInvariantsInspection */
        for ($k = 0; $k < $l; ++$k) {
            $ch = \ord($data[$k]) & 0xff;

            //Start <~
            if ($k === 0 && $ch === 60 && isset($data[$k + 1]) && (\ord($data[$k + 1]) & 0xFF) === 126) {
                $k++;
                continue;
            }
            //End ~>
            if ($ch === 126 && isset($data[$k + 1]) && (\ord($data[$k + 1]) & 0xFF) === 62) {
                break;
            }

            if ($ch === 122 /* z */ && $state === 0) {
                $out .= \chr(0) . \chr(0) . \chr(0) . \chr(0);
                continue;
            }

            if ($ch < 33 /* ! */ || $ch > 117 /* u */) {
                throw new Ascii85Exception(
                    'Illegal character found while ASCII85 decode.',
                    Ascii85Exception::ILLEGAL_CHAR_FOUND
                );
            }

            $chn[$state] = $ch - 33;/* ! */
            $state++;

            if ($state === 5) {
                $state = 0;
                $r = 0;
                for ($j = 0; $j < 5; ++$j) {
                    /** @noinspection UnnecessaryCastingInspection */
                    $r = (int)($r * 85 + $chn[$j]);
                }

                $out .= \chr($r >> 24)
                    . \chr($r >> 16)
                    . \chr($r >> 8)
                    . \chr($r);
            }
        }

        if ($state === 1) {
            throw new Ascii85Exception(
                'Illegal length while ASCII85 decode.',
                Ascii85Exception::ILLEGAL_LENGTH
            );
        }

        if ($state === 2) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + ($chn[1] + 1) * 85 * 85 * 85;
            $out .= \chr($r >> 24);
        } elseif ($state === 3) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85 + ($chn[2] + 1) * 85 * 85;
            $out .= \chr($r >> 24);
            $out .= \chr($r >> 16);
        } elseif ($state === 4) {
            $r = $chn[0] * 85 * 85 * 85 * 85 + $chn[1] * 85 * 85 * 85 + $chn[2] * 85 * 85 + ($chn[3] + 1) * 85;
            $out .= \chr($r >> 24);
            $out .= \chr($r >> 16);
            $out .= \chr($r >> 8);
        }

        return $out;
    }
}
