<?php
/**
 * Copyright 2011-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2011-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Envelope data as returned by the IMAP FETCH command (RFC 3501 [7.4.2]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2011-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 *
 * @property Horde_Mail_Rfc822_List $bcc  Bcc address(es).
 * @property Horde_Mail_Rfc822_List $cc  Cc address(es).
 * @property Horde_Imap_Client_DateTime $date  IMAP internal date.
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
    /** Serializable version. */
    const VERSION = 2;

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
        switch ($name) {
        case 'reply_to':
            $name = 'reply-to';
            // Fall-through

        case 'bcc':
        case 'cc':
        case 'from':
        case 'sender':
        case 'to':
            if (($ob = $this->_data->getOb($name)) !== null) {
                return $ob;
            }

            if (in_array($name, array('sender', 'reply-to'))) {
                return $this->from;
            }
            break;

        case 'date':
            if (($val = $this->_data->getValue($name)) !== null) {
                return new Horde_Imap_Client_DateTime($val);
            }
            break;

        case 'in_reply_to':
        case 'message_id':
        case 'subject':
            if (($val = $this->_data->getValue($name)) !== null) {
                return $val;
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

        case 'in_reply_to':
        case 'message_id':
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

        switch ($name) {
        case 'bcc':
        case 'cc':
        case 'date':
        case 'from':
        case 'in_reply_to':
        case 'message_id':
        case 'reply_to':
        case 'sender':
        case 'subject':
        case 'to':
            switch ($name) {
            case 'from':
                foreach (array('reply_to', 'sender') as $val) {
                    if ($this->$val->match($value)) {
                        $this->_data->removeHeader($val);
                    }
                }
                break;

            case 'reply_to':
            case 'sender':
                if ($this->from->match($value)) {
                    $this->_data->removeHeader($name);
                    return;
                }

                /* Convert reply-to name. */
                if ($name == 'reply_to') {
                    $name = 'reply-to';
                }
                break;
            }

            $this->_data->addHeader($name, $value, array(
                'sanity_check' => true
            ));
            break;
        }
    }

    /**
     */
    public function __isset($name)
    {
        switch ($name) {
        case 'reply_to':
            $name = 'reply-to';
            // Fall-through

        case 'sender':
            if ($this->_data->getValue($name) !== null) {
                return true;
            }
            $name = 'from';
            break;
        }

        return ($this->_data->getValue($name) !== null);
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
