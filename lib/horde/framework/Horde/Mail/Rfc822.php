<?php
/**
 * Copyright (c) 2001-2010, Richard Heyes
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * o Redistributions of source code must retain the above copyright
 *   notice, this list of conditions and the following disclaimer.
 * o Redistributions in binary form must reproduce the above copyright
 *   notice, this list of conditions and the following disclaimer in the
 *   documentation and/or other materials provided with the distribution.
 * o The names of the authors may not be used to endorse or promote
 *   products derived from this software without specific prior written
 *   permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * RFC822 parsing code adapted from message-address.c and rfc822-parser.c
 *   (Dovecot 2.1rc5)
 *   Original code released under LGPL-2.1
 *   Copyright (c) 2002-2011 Timo Sirainen <tss@iki.fi>
 *
 * @category  Horde
 * @copyright 2001-2010 Richard Heyes
 * @copyright 2002-2011 Timo Sirainen
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * RFC 822/2822/3490/5322 Email parser/validator.
 *
 * @author    Richard Heyes <richard@phpguru.org>
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @author    Timo Sirainen <tss@iki.fi>
 * @category  Horde
 * @copyright 2001-2010 Richard Heyes
 * @copyright 2002-2011 Timo Sirainen
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Rfc822
{
    /**
     * Valid atext characters.
     *
     * @deprecated
     * @since 2.0.3
     */
    const ATEXT = '!#$%&\'*+-./0123456789=?ABCDEFGHIJKLMNOPQRSTUVWXYZ^_`abcdefghijklmnopqrstuvwxyz{|}~';

    /**
     * Excluded (in ASCII decimal): 0-8, 10-31, 34, 40-41, 44, 58-60, 62, 64,
     * 91-93, 127
     *
     * @since 2.0.3
     */
    const ENCODE_FILTER = "\0\1\2\3\4\5\6\7\10\12\13\14\15\16\17\20\21\22\23\24\25\26\27\30\31\32\33\34\35\36\37\"(),:;<>@[\\]\177";

    /**
     * The address string to parse.
     *
     * @var string
     */
    protected $_data;

    /**
     * Length of the address string.
     *
     * @var integer
     */
    protected $_datalen;

    /**
     * Comment cache.
     *
     * @var string
     */
    protected $_comments = array();

    /**
     * List object to return in parseAddressList().
     *
     * @var Horde_Mail_Rfc822_List
     */
    protected $_listob;

    /**
     * Configuration parameters.
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Data pointer.
     *
     * @var integer
     */
    protected $_ptr;

    /**
     * Starts the whole process.
     *
     * @param mixed $address   The address(es) to validate. Either a string,
     *                         a Horde_Mail_Rfc822_Object, or an array of
     *                         strings and/or Horde_Mail_Rfc822_Objects.
     * @param array $params    Optional parameters:
     *   - default_domain: (string) Default domain/host.
     *                     DEFAULT: None
     *   - group: (boolean) Return a GroupList object instead of a List object?
     *            DEFAULT: false
     *   - limit: (integer) Stop processing after this many addresses.
     *            DEFAULT: No limit (0)
     *   - validate: (mixed) Strict validation of personal part data? If
     *               false, attempts to allow non-ASCII characters and
     *               non-quoted strings in the personal data, and will
     *               silently abort if an unparseable address is found.
     *               If true, does strict RFC 5322 (ASCII-only) parsing. If
     *               'eai' (@since 2.5.0), allows RFC 6532 (EAI/UTF-8)
     *               addresses.
     *               DEFAULT: false
     *
     * @return Horde_Mail_Rfc822_List  A list object.
     *
     * @throws Horde_Mail_Exception
     */
    public function parseAddressList($address, array $params = array())
    {
        if ($address instanceof Horde_Mail_Rfc822_List) {
            return $address;
        }

        if (empty($params['limit'])) {
            $params['limit'] = -1;
        }

        $this->_params = array_merge(array(
            'default_domain' => null,
            'validate' => false
        ), $params);

        $this->_listob = empty($this->_params['group'])
            ? new Horde_Mail_Rfc822_List()
            : new Horde_Mail_Rfc822_GroupList();

        if (!is_array($address)) {
            $address = array($address);
        }

        $tmp = array();
        foreach ($address as $val) {
            if ($val instanceof Horde_Mail_Rfc822_Object) {
                $this->_listob->add($val);
            } else {
                $tmp[] = rtrim(trim($val), ',');
            }
        }

        if (!empty($tmp)) {
            $this->_data = implode(',', $tmp);
            $this->_datalen = strlen($this->_data);
            $this->_ptr = 0;

            $this->_parseAddressList();
        }

        $ret = $this->_listob;
        unset($this->_listob);

        return $ret;
    }

   /**
     * Quotes and escapes the given string if necessary using rules contained
     * in RFC 2822 [3.2.5].
     *
     * @param string $str   The string to be quoted and escaped.
     * @param string $type  Either 'address', 'comment' (@since 2.6.0), or
     *                      'personal'.
     *
     * @return string  The correctly quoted and escaped string.
     */
    public function encode($str, $type = 'address')
    {
        switch ($type) {
        case 'comment':
            // RFC 5322 [3.2.2]: Filter out non-printable US-ASCII and ( ) \
            $filter = "\0\1\2\3\4\5\6\7\10\12\13\14\15\16\17\20\21\22\23\24\25\26\27\30\31\32\33\34\35\36\37\50\51\134\177";
            break;

        case 'personal':
            // RFC 2822 [3.4]: Period not allowed in display name
            $filter = self::ENCODE_FILTER . '.';
            break;

        case 'address':
        default:
            // RFC 2822 [3.4.1]: (HTAB, SPACE) not allowed in address
            $filter = self::ENCODE_FILTER . "\11\40";
            break;
        }

        // Strip double quotes if they are around the string already.
        // If quoted, we know that the contents are already escaped, so
        // unescape now.
        $str = trim($str);
        if ($str && ($str[0] === '"') && (substr($str, -1) === '"')) {
            $str = stripslashes(substr($str, 1, -1));
        }

        return (strcspn($str, $filter) != strlen($str))
            ? '"' . addcslashes($str, '\\"') . '"'
            : $str;
    }

    /**
     * If an email address has no personal information, get rid of any angle
     * brackets (<>) around it.
     *
     * @param string $address  The address to trim.
     *
     * @return string  The trimmed address.
     */
    public function trimAddress($address)
    {
        $address = trim($address);

        return (($address[0] == '<') && (substr($address, -1) == '>'))
            ? substr($address, 1, -1)
            : $address;
    }

    /* RFC 822 parsing methods. */

    /**
     * address-list = (address *("," address)) / obs-addr-list
     */
    protected function _parseAddressList()
    {
        $limit = $this->_params['limit'];

        while (($this->_curr() !== false) && ($limit-- !== 0)) {
            try {
                $this->_parseAddress();
            } catch (Horde_Mail_Exception $e) {
               if ($this->_params['validate']) {
                   throw $e;
               }
               ++$this->_ptr;
            }

            switch ($this->_curr()) {
            case ',':
                $this->_rfc822SkipLwsp(true);
                break;

            case false:
                // No-op
                break;

            default:
               if ($this->_params['validate']) {
                    throw new Horde_Mail_Exception('Error when parsing address list.');
               }
               break;
            }
        }
    }

    /**
     * address = mailbox / group
     */
    protected function _parseAddress()
    {
        $start = $this->_ptr;
        if (!$this->_parseGroup()) {
            $this->_ptr = $start;
            if ($mbox = $this->_parseMailbox()) {
                $this->_listob->add($mbox);
            }
        }
    }

    /**
     * group           = display-name ":" [mailbox-list / CFWS] ";" [CFWS]
     * display-name    = phrase
     *
     * @return boolean  True if a group was parsed.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _parseGroup()
    {
        $this->_rfc822ParsePhrase($groupname);

        if ($this->_curr(true) != ':') {
            return false;
        }

        $addresses = new Horde_Mail_Rfc822_GroupList();

        $this->_rfc822SkipLwsp();

        while (($chr = $this->_curr()) !== false) {
            if ($chr == ';') {
                ++$this->_ptr;

                if (count($addresses)) {
                    $this->_listob->add(new Horde_Mail_Rfc822_Group($groupname, $addresses));
                }

                return true;
            }

            /* mailbox-list = (mailbox *("," mailbox)) / obs-mbox-list */
            $addresses->add($this->_parseMailbox());

            switch ($this->_curr()) {
            case ',':
                $this->_rfc822SkipLwsp(true);
                break;

            case ';':
                // No-op
                break;

            default:
                break 2;
            }
        }

        throw new Horde_Mail_Exception('Error when parsing group.');
    }

    /**
     * mailbox = name-addr / addr-spec
     *
     * @return mixed  Mailbox object if mailbox was parsed, or false.
     */
    protected function _parseMailbox()
    {
        $this->_comments = array();
        $start = $this->_ptr;

        if (!($ob = $this->_parseNameAddr())) {
            $this->_comments = array();
            $this->_ptr = $start;
            $ob = $this->_parseAddrSpec();
        }

        if ($ob) {
            $ob->comment = $this->_comments;
        }

        return $ob;
    }

    /**
     * name-addr    = [display-name] angle-addr
     * display-name = phrase
     *
     * @return mixed  Mailbox object, or false.
     */
    protected function _parseNameAddr()
    {
        $this->_rfc822ParsePhrase($personal);

        if ($ob = $this->_parseAngleAddr()) {
            $ob->personal = $personal;
            return $ob;
        }

        return false;
    }

    /**
     * addr-spec = local-part "@" domain
     *
     * @return mixed  Mailbox object.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _parseAddrSpec()
    {
        $ob = new Horde_Mail_Rfc822_Address();
        $ob->mailbox = $this->_parseLocalPart();

        if ($this->_curr() == '@') {
            try {
                $this->_rfc822ParseDomain($host);
                if (!empty($host)) {
                    $ob->host = $host;
                }
            } catch (Horde_Mail_Exception $e) {
                if (!empty($this->_params['validate'])) {
                    throw $e;
                }
            }
        }

        if (is_null($ob->host)) {
            if (!is_null($this->_params['default_domain'])) {
                $ob->host = $this->_params['default_domain'];
            } elseif (!empty($this->_params['validate'])) {
                throw new Horde_Mail_Exception('Address is missing domain.');
            }
        }

        return $ob;
    }

    /**
     * local-part      = dot-atom / quoted-string / obs-local-part
     * obs-local-part  = word *("." word)
     *
     * @return string  The local part.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _parseLocalPart()
    {
        if (($curr = $this->_curr()) === false) {
            throw new Horde_Mail_Exception('Error when parsing local part.');
        }

        if ($curr == '"') {
            $this->_rfc822ParseQuotedString($str);
        } else {
            $this->_rfc822ParseDotAtom($str, ',;@');
        }

        return $str;
    }

    /**
     * "<" [ "@" route ":" ] local-part "@" domain ">"
     *
     * @return mixed  Mailbox object, or false.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _parseAngleAddr()
    {
        if ($this->_curr() != '<') {
            return false;
        }

        $this->_rfc822SkipLwsp(true);

        if ($this->_curr() == '@') {
            // Route information is ignored.
            $this->_parseDomainList();
            if ($this->_curr() != ':') {
                throw new Horde_Mail_Exception('Invalid route.');
            }

            $this->_rfc822SkipLwsp(true);
        }

        $ob = $this->_parseAddrSpec();

        if ($this->_curr() != '>') {
            throw new Horde_Mail_Exception('Error when parsing angle address.');
        }

        $this->_rfc822SkipLwsp(true);

        return $ob;
    }

    /**
     * obs-domain-list = "@" domain *(*(CFWS / "," ) [CFWS] "@" domain)
     *
     * @return array  Routes.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _parseDomainList()
    {
        $route = array();

        while ($this->_curr() !== false) {
            $this->_rfc822ParseDomain($str);
            $route[] = '@' . $str;

            $this->_rfc822SkipLwsp();
            if ($this->_curr() != ',') {
                return $route;
            }
            ++$this->_ptr;
        }

        throw new Horde_Mail_Exception('Invalid domain list.');
    }

    /* RFC 822 parsing methods. */

    /**
     * phrase     = 1*word / obs-phrase
     * word       = atom / quoted-string
     * obs-phrase = word *(word / "." / CFWS)
     *
     * @param string &$phrase  The phrase data.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822ParsePhrase(&$phrase)
    {
        $curr = $this->_curr();
        if (($curr === false) || ($curr == '.')) {
            throw new Horde_Mail_Exception('Error when parsing a group.');
        }

        do {
            if ($curr == '"') {
                $this->_rfc822ParseQuotedString($phrase);
            } else {
                $this->_rfc822ParseAtomOrDot($phrase);
            }

            $curr = $this->_curr();
            if (($curr != '"') &&
                ($curr != '.') &&
                !$this->_rfc822IsAtext($curr)) {
                break;
            }

            $phrase .= ' ';
        } while ($this->_ptr < $this->_datalen);

        $this->_rfc822SkipLwsp();
    }

    /**
     * @param string &$phrase  The quoted string data.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822ParseQuotedString(&$str)
    {
        if ($this->_curr(true) != '"') {
            throw new Horde_Mail_Exception('Error when parsing a quoted string.');
        }

        while (($chr = $this->_curr(true)) !== false) {
            switch ($chr) {
            case '"':
                $this->_rfc822SkipLwsp();
                return;

            case "\n":
                /* Folding whitespace, remove the (CR)LF. */
                if (substr($str, -1) == "\r") {
                    $str = substr($str, 0, -1);
                }
                continue 2;

            case '\\':
                if (($chr = $this->_curr(true)) === false) {
                    break 2;
                }
                break;
            }

            $str .= $chr;
        }

        /* Missing trailing '"', or partial quoted character. */
        throw new Horde_Mail_Exception('Error when parsing a quoted string.');
    }

    /**
     * dot-atom        = [CFWS] dot-atom-text [CFWS]
     * dot-atom-text   = 1*atext *("." 1*atext)
     *
     * atext           = ; Any character except controls, SP, and specials.
     *
     * For RFC-822 compatibility allow LWSP around '.'.
     *
     *
     * @param string &$str      The atom/dot data.
     * @param string $validate  Use these characters as delimiter.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822ParseDotAtom(&$str, $validate = null)
    {
        $valid = false;

        while ($this->_ptr < $this->_datalen) {
            $chr = $this->_data[$this->_ptr];

            /* TODO: Optimize by duplicating rfc822IsAtext code here */
            if ($this->_rfc822IsAtext($chr, $validate)) {
                $str .= $chr;
                ++$this->_ptr;
            } elseif (!$valid) {
                throw new Horde_Mail_Exception('Error when parsing dot-atom.');
            } else {
                $this->_rfc822SkipLwsp();

                if ($this->_curr() != '.') {
                    return;
                }
                $str .= $chr;

                $this->_rfc822SkipLwsp(true);
            }

            $valid = true;
        }
    }

    /**
     * atom  = [CFWS] 1*atext [CFWS]
     * atext = ; Any character except controls, SP, and specials.
     *
     * This method doesn't just silently skip over WS.
     *
     * @param string &$str  The atom/dot data.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822ParseAtomOrDot(&$str)
    {
        while ($this->_ptr < $this->_datalen) {
            $chr = $this->_data[$this->_ptr];
            if (($chr != '.') &&
                /* TODO: Optimize by duplicating rfc822IsAtext code here */
                !$this->_rfc822IsAtext($chr, ',<:')) {
                $this->_rfc822SkipLwsp();
                if (!$this->_params['validate'] && $str !== null) {
                    $str = trim($str);
                }
                return;
            }

            $str .= $chr;
            ++$this->_ptr;
        }
    }

    /**
     * domain          = dot-atom / domain-literal / obs-domain
     * domain-literal  = [CFWS] "[" *([FWS] dcontent) [FWS] "]" [CFWS]
     * obs-domain      = atom *("." atom)
     *
     * @param string &$str  The domain string.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822ParseDomain(&$str)
    {
        if ($this->_curr(true) != '@') {
            throw new Horde_Mail_Exception('Error when parsing domain.');
        }

        $this->_rfc822SkipLwsp();

        if ($this->_curr() == '[') {
            $this->_rfc822ParseDomainLiteral($str);
        } else {
            $this->_rfc822ParseDotAtom($str, ';,> ');
        }
    }

    /**
     * domain-literal  = [CFWS] "[" *([FWS] dcontent) [FWS] "]" [CFWS]
     * dcontent        = dtext / quoted-pair
     * dtext           = NO-WS-CTL /     ; Non white space controls
     *           %d33-90 /       ; The rest of the US-ASCII
     *           %d94-126        ;  characters not including "[",
     *                   ;  "]", or "\"
     *
     * @param string &$str  The domain string.
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822ParseDomainLiteral(&$str)
    {
        if ($this->_curr(true) != '[') {
            throw new Horde_Mail_Exception('Error parsing domain literal.');
        }

        while (($chr = $this->_curr(true)) !== false) {
            switch ($chr) {
            case '\\':
                if (($chr = $this->_curr(true)) === false) {
                    break 2;
                }
                break;

            case ']':
                $this->_rfc822SkipLwsp();
                return;
            }

            $str .= $chr;
        }

        throw new Horde_Mail_Exception('Error parsing domain literal.');
    }

    /**
     * @param boolean $advance  Advance cursor?
     *
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822SkipLwsp($advance = false)
    {
        if ($advance) {
            ++$this->_ptr;
        }

        while (($chr = $this->_curr()) !== false) {
            switch ($chr) {
            case ' ':
            case "\n":
            case "\r":
            case "\t":
                ++$this->_ptr;
                continue 2;

            case '(':
                $this->_rfc822SkipComment();
                break;

            default:
                return;
            }
        }
    }

    /**
     * @throws Horde_Mail_Exception
     */
    protected function _rfc822SkipComment()
    {
        if ($this->_curr(true) != '(') {
            throw new Horde_Mail_Exception('Error when parsing a comment.');
        }

        $comment = '';
        $level = 1;

        while (($chr = $this->_curr(true)) !== false) {
            switch ($chr) {
            case '(':
                ++$level;
                continue 2;

            case ')':
                if (--$level == 0) {
                    $this->_comments[] = $comment;
                    return;
                }
                break;

            case '\\':
                if (($chr = $this->_curr(true)) === false) {
                    break 2;
                }
                break;
            }

            $comment .= $chr;
        }

        throw new Horde_Mail_Exception('Error when parsing a comment.');
    }

    /**
     * Check if data is an atom.
     *
     * @param string $chr       The character to check.
     * @param string $validate  If in non-validate mode, use these characters
     *                          as the non-atom delimiters.
     *
     * @return boolean  True if a valid atom.
     */
    protected function _rfc822IsAtext($chr, $validate = null)
    {
        if (!$this->_params['validate'] && !is_null($validate)) {
            return strcspn($chr, $validate);
        }

        $ord = ord($chr);

        /* UTF-8 characters check. */
        if ($ord > 127) {
            return ($this->_params['validate'] === 'eai');
        }

        /* Check for DISALLOWED characters under both RFCs 5322 and 6532. */

        /* Unprintable characters && [SPACE] */
        if ($ord <= 32) {
            return false;
        }

        /* "(),:;<>@[\] [DEL] */
        switch ($ord) {
        case 34:
        case 40:
        case 41:
        case 44:
        case 58:
        case 59:
        case 60:
        case 62:
        case 64:
        case 91:
        case 92:
        case 93:
        case 127:
            return false;
        }

        return true;
    }

    /* Helper methods. */

    /**
     * Return current character.
     *
     * @param boolean $advance  If true, advance the cursor.
     *
     * @return string  The current character (false if EOF reached).
     */
    protected function _curr($advance = false)
    {
        return ($this->_ptr >= $this->_datalen)
            ? false
            : $this->_data[$advance ? $this->_ptr++ : $this->_ptr];
    }

    /* Other public methods. */

    /**
     * Returns an approximate count of how many addresses are in the string.
     * This is APPROXIMATE as it only splits based on a comma which has no
     * preceding backslash.
     *
     * @param string $data  Addresses to count.
     *
     * @return integer  Approximate count.
     */
    public function approximateCount($data)
    {
        return count(preg_split('/(?<!\\\\),/', $data));
    }

    /**
     * Validates whether an email is of the common internet form:
     * <user>@<domain>. This can be sufficient for most people.
     *
     * Optional stricter mode can be utilized which restricts mailbox
     * characters allowed to: alphanumeric, full stop, hyphen, and underscore.
     *
     * @param string $data     Address to check.
     * @param boolean $strict  Strict check?
     *
     * @return mixed  False if it fails, an indexed array username/domain if
     *                it matches.
     */
    public function isValidInetAddress($data, $strict = false)
    {
        $regex = $strict
            ? '/^([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i'
            : '/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})$/i';

        return preg_match($regex, trim($data), $matches)
            ? array($matches[1], $matches[2])
            : false;
    }

}
