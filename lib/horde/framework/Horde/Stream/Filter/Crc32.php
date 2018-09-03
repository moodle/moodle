<?php
/**
 * Stream filter class to compute the CRC32 value of a string.
 *
 * Usage:
 * <pre>
 *   $params = new stdClass;
 *   stream_filter_register('horde_crc32', 'Horde_Stream_Filter_Crc32');
 *   stream_filter_[app|pre]pend($stream, 'horde_crc32',
 *                               [ STREAM_FILTER_[READ|WRITE|ALL] ],
 *                               [ $params ]);
 *   while (fread($stream, 8192)) {}
 *   // CRC32 data in $params->crc32
 * </pre>
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Stream_Filter
 */
class Horde_Stream_Filter_Crc32 extends php_user_filter
{
    /**
     * @see stream_filter_register()
     */
    public function onCreate()
    {
        $this->params->crc32 = 0;

        return true;
    }

    /**
     * @see stream_filter_register()
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $consumed += $bucket->datalen;
            $this->params->crc32 = $this->_crc32Combine($this->params->crc32, crc32($bucket->data), $bucket->datalen);
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

    /**
     */
    protected function _crc32Combine($crc1, $crc2, $len2)
    {
        $odd = array(0xedb88320);
        $row = 1;

        for ($n = 1; $n < 32; ++$n) {
            $odd[$n] = $row;
            $row <<= 1;
        }

        $this->_gf2MatrixSquare($even, $odd);
        $this->_gf2MatrixSquare($odd, $even);

        do {
            /* Apply zeros operator for this bit of len2. */
            $this->_gf2MatrixSquare($even, $odd);

            if ($len2 & 1) {
                $crc1 = $this->_gf2MatrixTimes($even, $crc1);
            }

            $len2>>=1;

            /* If no more bits set, then done. */
            if ($len2 == 0) {
                break;
            }

            /* Another iteration of the loop with odd and even swapped. */
            $this->_gf2MatrixSquare($odd, $even);
            if ($len2 & 1) {
                $crc1 = $this->_gf2MatrixTimes($odd, $crc1);
            }

            $len2>>= 1;
        } while ($len2 != 0);

        $crc1 ^= $crc2;

        return $crc1;
    }

    /**
     */
    protected function _gf2MatrixSquare(&$square, &$mat)
    {
        for ($n = 0; $n < 32; ++$n) {
            $square[$n] = $this->_gf2MatrixTimes($mat, $mat[$n]);
        }
    }

    /**
     */
    protected function _gf2MatrixTimes($mat, $vec)
    {
        $i = $sum = 0;

        while ($vec) {
            if ($vec & 1) {
                $sum ^= $mat[$i];
            }

            $vec = ($vec >> 1) & 0x7FFFFFFF;
            ++$i;
        }

        return $sum;
    }

}
