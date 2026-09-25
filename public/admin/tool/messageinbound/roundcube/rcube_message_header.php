<?php

/*
 +-----------------------------------------------------------------------+
 | This file is part of the Roundcube Webmail client                     |
 |                                                                       |
 | Copyright (C) The Roundcube Dev Team                                  |
 | Copyright (C) Kolab Systems AG                                        |
 |                                                                       |
 | Licensed under the GNU General Public License version 3 or            |
 | any later version with exceptions for skins & plugins.                |
 | See the README file for a full license statement.                     |
 |                                                                       |
 | PURPOSE:                                                              |
 |   E-mail message headers representation                               |
 +-----------------------------------------------------------------------+
 | Author: Aleksander Machniak <alec@alec.pl>                            |
 +-----------------------------------------------------------------------+
*/

/**
 * Struct representing an e-mail message header
 */
class rcube_message_header
{
    /**
     * Message sequence number
     *
     * @var ?int
     */
    public $id;

    /**
     * Message unique identifier
     *
     * @var int|string|null
     */
    public $uid;

    /**
     * Message subject
     *
     * @var ?string
     */
    public $subject;

    /**
     * Message sender (From)
     *
     * @var ?string
     */
    public $from;

    /**
     * Message recipient (To)
     *
     * @var ?string
     */
    public $to;

    /**
     * Message additional recipients (Cc)
     *
     * @var ?string
     */
    public $cc;

    /**
     * Message hidden recipients (Bcc)
     *
     * @var ?string
     */
    public $bcc;

    /**
     * Message Reply-To header
     *
     * @var ?string
     */
    public $replyto;

    /**
     * Message In-Reply-To header
     *
     * @var ?string
     */
    public $in_reply_to;

    /**
     * Message date (Date)
     *
     * @var ?string
     */
    public $date;

    /**
     * Message identifier (Message-ID)
     *
     * @var ?string
     */
    public $messageID;

    /**
     * Message size
     *
     * @var ?int
     */
    public $size;

    /**
     * Message encoding
     *
     * @var ?string
     */
    public $encoding;

    /**
     * Message charset
     *
     * @var ?string
     */
    public $charset;

    /**
     * Message Content-type
     *
     * @var ?string
     */
    public $ctype;

    /**
     * Message timestamp (based on message date)
     *
     * @var ?int
     */
    public $timestamp;

    /**
     * IMAP bodystructure string
     *
     * @var ?array
     */
    public $bodystructure;

    /**
     * IMAP body (RFC822.TEXT)
     *
     * @var ?string
     */
    public $body;

    /**
     * IMAP part bodies
     *
     * @var array
     */
    public $bodypart = [];

    /**
     * IMAP internal date
     *
     * @var ?string
     */
    public $internaldate;

    /**
     * Message References header
     *
     * @var ?string
     */
    public $references;

    /**
     * Message priority (X-Priority)
     *
     * @var int
     */
    public $priority;

    /**
     * Message receipt recipient
     *
     * @var ?string
     */
    public $mdn_to;

    /**
     * IMAP folder this message is stored in
     *
     * @var ?string
     */
    public $folder;

    /**
     * Other message headers
     *
     * @var array
     */
    public $others = [];

    /**
     * Message flags
     *
     * @var array
     */
    public $flags = [];

    /**
     * Message annotations (RFC 5257)
     *
     * @var ?array
     */
    public $annotations;

    /**
     * Extra flags (for the messages list)
     *
     * @var array
     *
     * @deprecated Use $flags
     */
    public $list_flags = [];

    /**
     * Extra columns content (for the messages list)
     *
     * @var array
     */
    public $list_cols = [];

    /**
     * Message structure
     *
     * @var ?rcube_message_part
     */
    public $structure;

    /**
     * Message thread depth
     *
     * @var int
     */
    public $depth = 0;

    /**
     * Whether the message has references in the thread
     *
     * @var bool
     */
    public $has_children = false;

    /**
     * Number of flagged children (in a thread)
     *
     * @var int
     */
    public $flagged_children = 0;

    /**
     * Number of unread children (in a thread)
     *
     * @var int
     */
    public $unread_children = 0;

    /**
     * UID of the message parent (in a thread)
     *
     * @var int|string|null
     */
    public $parent_uid;

    /**
     * IMAP MODSEQ value
     *
     * @var ?int
     */
    public $modseq;

    /**
     * IMAP ENVELOPE
     *
     * @var ?string
     */
    public $envelope;

    /**
     * Header name to rcube_message_header object property map
     *
     * @var array
     */
    private $obj_headers = [
        'date' => 'date',
        'from' => 'from',
        'to' => 'to',
        'subject' => 'subject',
        'reply-to' => 'replyto',
        'cc' => 'cc',
        'bcc' => 'bcc',
        'mbox' => 'folder',
        'folder' => 'folder',
        'content-transfer-encoding' => 'encoding',
        'in-reply-to' => 'in_reply_to',
        'content-type' => 'ctype',
        'charset' => 'charset',
        'references' => 'references',
        'disposition-notification-to' => 'mdn_to',
        'x-confirm-reading-to' => 'mdn_to',
        'message-id' => 'messageID',
        'x-priority' => 'priority',
    ];

    /**
     * Returns header value
     *
     * @param string $name   Header name
     * @param bool   $decode Decode the header content
     *
     * @return array|string|int|null Header content
     */
    public function get($name, $decode = true)
    {
        $name = strtolower($name);
        $value = null;

        if (isset($this->obj_headers[$name]) && isset($this->{$this->obj_headers[$name]})) {
            $value = $this->{$this->obj_headers[$name]};
        } elseif (isset($this->others[$name])) {
            $value = $this->others[$name];
        }

        if ($decode && $value !== null) {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $val = rcube_mime::decode_header($val, $this->charset);
                    $value[$key] = rcube_charset::clean($val);
                }
            } else {
                $value = rcube_mime::decode_header($value, $this->charset);
                $value = rcube_charset::clean($value);
            }
        }

        return $value;
    }

    /**
     * Sets header value
     *
     * @param string $name  Header name
     * @param string $value Header content
     */
    public function set($name, $value)
    {
        $name = strtolower($name);

        if (isset($this->obj_headers[$name])) {
            $this->{$this->obj_headers[$name]} = $value;
        } else {
            $this->others[$name] = $value;
        }
    }

    /**
     * Factory method to instantiate headers from a data array
     *
     * @param array $arr Hash array with header values
     *
     * @return rcube_message_header instance filled with headers values
     */
    public static function from_array($arr)
    {
        $obj = new self();
        foreach ($arr as $k => $v) {
            $obj->set($k, $v);
        }

        return $obj;
    }
}

/**
 * Class for sorting an array of rcube_message_header objects in a predetermined order.
 */
class rcube_message_header_sorter
{
    /** @var array Message UIDs */
    private $uids = [];

    /**
     * Set the predetermined sort order.
     *
     * @param array $index Numerically indexed array of IMAP UIDs
     */
    public function set_index($index)
    {
        $index = array_flip($index);

        $this->uids = $index;
    }

    /**
     * Sort the array of header objects
     *
     * @param array $headers Array of rcube_message_header objects indexed by UID
     */
    public function sort_headers(&$headers)
    {
        uksort($headers, [$this, 'compare_uids']);
    }

    /**
     * Sort method called by uksort()
     *
     * @param int $a Array key (UID)
     * @param int $b Array key (UID)
     */
    public function compare_uids($a, $b)
    {
        // then find each sequence number in my ordered list
        $posa = isset($this->uids[$a]) ? intval($this->uids[$a]) : -1;
        $posb = isset($this->uids[$b]) ? intval($this->uids[$b]) : -1;

        // return the relative position as the comparison value
        return $posa - $posb;
    }
}
