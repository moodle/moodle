<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (BSD). If you
 * did not receive this file, see http://www.horde.org/licenses/bsd.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Class to parse identification headers (RFC 5322 [3.6.4]): Message-ID,
 * References, and In-Reply-To.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 * @since     2.2.0
 */
class Horde_Mail_Rfc822_Identification extends Horde_Mail_Rfc822
{
    /**
     * List of message IDs parsed.
     *
     * @var array
     */
    public $ids = array();

    /**
     * Constructor.
     *
     * @param string $value  Identification field value to parse.
     */
    public function __construct($value = null)
    {
        $this->parse($value);
    }

    /**
     * Parse an identification header.
     *
     * @param string|null $value  Identification field value to parse.
     */
    public function parse($value)
    {
        if (empty($value)) {
            return;
        }

        $this->_data = $value;
        $this->_datalen = strlen($value);
        $this->_params['validate'] = true;
        $this->_ptr = 0;

        $this->_rfc822SkipLwsp();

        while ($this->_curr() !== false) {
            try {
                $this->ids[] = $this->_parseMessageId();
            } catch (Horde_Mail_Exception $e) {
                break;
            }

            // Some mailers incorrectly insert commas between reference items
            if ($this->_curr() == ',') {
                $this->_rfc822SkipLwsp(true);
            }
        }
    }

    /**
     * Message IDs are defined in RFC 5322 [3.6.4]. In short, they can only
     * contain one '@' character. However, Outlook can produce invalid
     * Message-IDs containing multiple '@' characters, which will fail the
     * strict RFC checks.
     *
     * Since we don't care about the structure/details of the Message-ID,
     * just do a basic parse that considers all characters inside of angled
     * brackets to be valid.
     *
     * @return string  A full Message-ID (enclosed in angled brackets).
     *
     * @throws Horde_Mail_Exception
     */
    private function _parseMessageId()
    {
        $bracket = ($this->_curr(true) === '<');
        $str = '<';

        while (($chr = $this->_curr(true)) !== false) {
            if ($bracket) {
                $str .= $chr;
                if ($chr == '>') {
                    $this->_rfc822SkipLwsp();
                    return $str;
                }
            } else {
                if (!strcspn($chr, " \n\r\t,")) {
                    $this->_rfc822SkipLwsp();
                    return $str;
                }
                $str .= $chr;
            }
        }

        if (!$bracket) {
            return $str;
        }

        throw new Horde_Mail_Exception('Invalid Message-ID.');
    }

}
