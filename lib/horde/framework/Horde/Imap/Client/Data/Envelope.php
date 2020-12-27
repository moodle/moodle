<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Envelope data as returned by the IMAP FETCH command (RFC 3501 [7.4.2]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @todo $date should return null if it doesn't exist.
 *
 * @property Horde_Mail_Rfc822_List $bcc  Bcc address(es).
 * @property Horde_Mail_Rfc822_List $cc  Cc address(es).
 * @property Horde_Imap_Client_DateTime $date  Message date.
 * @property Horde_Mail_Rfc822_List $from  From address(es).
 * @property string $in_reply_to  Message-ID of the message replied to.
 * @property string $message_id  Message-ID of the message.
 * @property Horde_Mail_Rfc822_List $reply_to  Reply-to address(es).
 * @property Horde_Mail_Rfc822_List $sender  Sender address.
 * @property string $subject  Subject.
 * @property Horde_Mail_Rfc822_List $to  To address(es).
 */
class Horde_Imap_Client_Data_Envelope implements Serializable
{
    /* Serializable version. */
    const VERSION = 3;

    /**
     * Data object.
     *
     * @var Horde_Mime_Headers
     */
    protected $_data;

    /**
     * Constructor.
     *
     * @var array $data  An array of property names (keys) and values to set
     *                   in this object.
     */
    public function __construct(array $data = array())
    {
        $this->_data = new Horde_Mime_Headers();

        foreach ($data as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     */
    public function __get($name)
    {
        $name = $this->_normalizeProperty($name);

        switch ($name) {
        case 'bcc':
        case 'cc':
        case 'from':
        case 'reply-to':
        case 'sender':
        case 'to':
            if ($h = $this->_data[$name]) {
                return $h->getAddressList(true);
            }

            if (in_array($name, array('sender', 'reply-to'))) {
                return $this->from;
            }
            break;

        case 'date':
            if ($val = $this->_data['date']) {
                return new Horde_Imap_Client_DateTime($val->value);
            }
            break;

        case 'in-reply-to':
        case 'message-id':
        case 'subject':
            if ($val = $this->_data[$name]) {
                return $val->value;
            }
            break;
        }

        // Default values.
        switch ($name) {
        case 'bcc':
        case 'cc':
        case 'from':
        case 'to':
            return new Horde_Mail_Rfc822_List();

        case 'date':
            return new Horde_Imap_Client_DateTime();

        case 'in-reply-to':
        case 'message-id':
        case 'subject':
            return '';
        }

        return null;
    }

    /**
     */
    public function __set($name, $value)
    {
        if (!strlen($value)) {
            return;
        }

        $name = $this->_normalizeProperty($name);

        switch ($name) {
        case 'bcc':
        case 'cc':
        case 'date':
        case 'from':
        case 'in-reply-to':
        case 'message-id':
        case 'reply-to':
        case 'sender':
        case 'subject':
        case 'to':
            switch ($name) {
            case 'from':
                if ($this->reply_to->match($value)) {
                    unset($this->_data['reply-to']);
                }
                if ($this->sender->match($value)) {
                    unset($this->_data['sender']);
                }
                break;

            case 'reply-to':
            case 'sender':
                if ($this->from->match($value)) {
                    unset($this->_data[$name]);
                    return;
                }
                break;
            }

            $this->_data->addHeader($name, $value);
            break;
        }
    }

    /**
     */
    public function __isset($name)
    {
        $name = $this->_normalizeProperty($name);

        switch ($name) {
        case 'reply-to':
        case 'sender':
            if (isset($this->_data[$name])) {
                return true;
            }
            $name = 'from';
            break;
        }

        return isset($this->_data[$name]);
    }

    /**
     */
    protected function _normalizeProperty($name)
    {
        switch ($name) {
        case 'in_reply_to':
            return 'in-reply-to';

        case 'message_id':
            return 'message-id';

        case 'reply_to':
            return 'reply-to';
        }

        return $name;
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        return serialize(array(
            'd' => $this->_data,
            'v' => self::VERSION
        ));
    }

    /**
     */
    public function unserialize($data)
    {
        $data = @unserialize($data);
        if (empty($data['v']) || ($data['v'] != self::VERSION)) {
            throw new Exception('Cache version change');
        }

        $this->_data = $data['d'];
    }

}
