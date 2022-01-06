<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 */

/**
 * This class represents the Content-Transfer-Encoding header value (RFC 2045
 * [6]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mime
 * @since     2.8.0
 */
class Horde_Mime_Headers_ContentTransferEncoding
extends Horde_Mime_Headers_Element_Single
implements Horde_Mime_Headers_Extension_Mime
{
    /** Default encoding (RFC 2045 [6.1]). */
    const DEFAULT_ENCODING = '7bit';

    /** Unknown encoding specifier. */
    const UNKNOWN_ENCODING = 'x-unknown';

    /**
     */
    public function __construct($name, $value)
    {
        if (!strlen($value)) {
            $value = self::DEFAULT_ENCODING;
        }

        parent::__construct('Content-Transfer-Encoding', $value);
    }

    /**
     */
    protected function _setValue($value)
    {
        parent::_setValue(trim($value));

        $val = $this->value;
        $encoding = Horde_String::lower($val);

        switch ($encoding) {
        case '7bit':
        case '8bit':
        case 'base64':
        case 'binary':
        case 'quoted-printable':
            // Valid encodings
            break;

        default:
            /* RFC 2045 [6.3] - Valid non-standardized encodings must begin
             * with 'x-'. */
            if (substr($encoding, 0, 2) !== 'x-') {
                $encoding = self::UNKNOWN_ENCODING;
            }
            break;
        }

        if ($encoding !== $val) {
            parent::_setValue($encoding);
        }
    }

    /**
     */
    public function isDefault()
    {
        return ($this->value === self::DEFAULT_ENCODING);
    }

    /**
     */
    public static function getHandles()
    {
        return array(
            // MIME: RFC 2045
            'content-transfer-encoding'
        );
    }

}
