<?php
/**
 * This file is part of FPDI
 *
 * @package   setasign\Fpdi
 * @copyright Copyright (c) 2019 Setasign - Jan Slabon (https://www.setasign.com)
 * @license   http://opensource.org/licenses/mit-license The MIT License
 */

namespace setasign\Fpdi\PdfParser\Filter;

/**
 * Class for handling zlib/deflate encoded data
 *
 * @package setasign\Fpdi\PdfParser\Filter
 */
class Flate implements FilterInterface
{
    /**
     * Checks whether the zlib extension is loaded.
     *
     * Used for testing purpose.
     *
     * @return boolean
     * @internal
     */
    protected function extensionLoaded()
    {
        return \extension_loaded('zlib');
    }

    /**
     * Decodes a flate compressed string.
     *
     * @param string $data The input string
     * @return string
     * @throws FlateException
     */
    public function decode($data)
    {
        if ($this->extensionLoaded()) {
            $oData = $data;
            $data = @((\strlen($data) > 0) ? \gzuncompress($data) : '');
            if ($data === false) {
                // Try this fallback
                $tries = 1;
                while ($tries < 10 && ($data === false || \strlen($data) < (\strlen($oData) - $tries - 1))) {
                    $data = @(\gzinflate(\substr($oData, $tries)));
                    $tries++;
                }

                if ($data === false) {
                    // let's try if the checksum is CRC32
                    $fh = fopen('php://temp', 'w+b');
                    \fwrite($fh, "\x1f\x8b\x08\x00\x00\x00\x00\x00" . $oData);
                    \stream_filter_append($fh, 'zlib.inflate', \STREAM_FILTER_READ, ['window' => 30]);
                    \fseek($fh, 0);
                    $data = \stream_get_contents($fh);
                    \fclose($fh);
                }

                if (!$data) {
                    throw new FlateException(
                        'Error while decompressing stream.',
                        FlateException::DECOMPRESS_ERROR
                    );
                }
            }
        } else {
            throw new FlateException(
                'To handle FlateDecode filter, enable zlib support in PHP.',
                FlateException::NO_ZLIB
            );
        }

        return $data;
    }
}
