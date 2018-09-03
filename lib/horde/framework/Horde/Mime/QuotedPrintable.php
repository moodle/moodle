<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * Quoted-printable utility methods.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.5.0
 */
class Horde_Mime_QuotedPrintable
{
    /**
     * Decodes quoted-printable data.
     *
     * @param string $data  The Q-P data to decode.
     *
     * @return string  The decoded text.
     */
    public static function decode($data)
    {
        return quoted_printable_decode($data);
    }

    /**
     * Encodes text via quoted-printable encoding.
     *
     * @param string $text   The text to encode (UTF-8).
     * @param string $eol    The EOL sequence to use.
     * @param integer $wrap  Wrap a line at this many characters.
     *
     * @return string  The quoted-printable encoded string.
     */
    public static function encode($text, $eol = "\n", $wrap = 76)
    {
        $fp = fopen('php://temp', 'r+');
        stream_filter_append(
            $fp,
            'convert.quoted-printable-encode',
            STREAM_FILTER_WRITE,
            array(
                'line-break-chars' => $eol,
                'line-length' => $wrap
            )
        );
        fwrite($fp, $text);
        rewind($fp);
        $out = stream_get_contents($fp);
        fclose($fp);

        return $out;
    }

}
