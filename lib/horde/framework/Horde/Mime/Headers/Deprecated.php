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
 * Deprecated Horde_Mime_Headers methods.
 *
 * @author     Michael Slusarz <slusarz@horde.org>
 * @deprecated
 * @category   Horde
 * @copyright  2014-2016 Horde LLC
 * @internal
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Mime
 * @since      2.5.0
 */
class Horde_Mime_Headers_Deprecated
{
    /**
     * Base headers object.
     *
     * @var Horde_Mime_Headers
     */
    private $_headers;

    /**
     */
    public function __construct(Horde_Mime_Headers $headers)
    {
        $this->_headers = $headers;
    }

    /**
     */
    public function addMessageIdHeader()
    {
        $this->_headers->addHeaderOb(Horde_Mime_Headers_MessageId::create());
    }

    /**
     */
    public function addUserAgentHeader()
    {
        $this->_headers->addHeaderOb(Horde_Mime_Headers_UserAgent::create());
    }

    /**
     */
    public function getUserAgent()
    {
        return strval(Horde_Mime_Headers_UserAgent::create());
    }

    /**
     */
    public function setUserAgent($agent)
    {
        $this->_headers->addHeaderOb(
            new Horde_Mime_Headers_UserAgent(null, $agent)
        );
    }

    /**
     */
    public function addReceivedHeader(array $opts = array())
    {
        $old_error = error_reporting(0);
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            /* This indicates the user is connecting through a proxy. */
            $remote_path = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $remote_addr = $remote_path[0];
            if (!empty($opts['dns'])) {
                $remote = $remote_addr;
                try {
                    if ($response = $opts['dns']->query($remote_addr, 'PTR')) {
                        foreach ($response->answer as $val) {
                            if (isset($val->ptrdname)) {
                                $remote = $val->ptrdname;
                                break;
                            }
                        }
                    }
                } catch (Net_DNS2_Exception $e) {}
            } else {
                $remote = gethostbyaddr($remote_addr);
            }
        } else {
            $remote_addr = $_SERVER['REMOTE_ADDR'];
            if (empty($_SERVER['REMOTE_HOST'])) {
                if (!empty($opts['dns'])) {
                    $remote = $remote_addr;
                    try {
                        if ($response = $opts['dns']->query($remote_addr, 'PTR')) {
                            foreach ($response->answer as $val) {
                                if (isset($val->ptrdname)) {
                                    $remote = $val->ptrdname;
                                    break;
                                }
                            }
                        }
                    } catch (Net_DNS2_Exception $e) {}
                } else {
                    $remote = gethostbyaddr($remote_addr);
                }
            } else {
                $remote = $_SERVER['REMOTE_HOST'];
            }
        }
        error_reporting($old_error);

        if (!empty($_SERVER['REMOTE_IDENT'])) {
            $remote_ident = $_SERVER['REMOTE_IDENT'] . '@' . $remote . ' ';
        } elseif ($remote != $_SERVER['REMOTE_ADDR']) {
            $remote_ident = $remote . ' ';
        } else {
            $remote_ident = '';
        }

        if (!empty($opts['server'])) {
            $server_name = $opts['server'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $server_name = $_SERVER['SERVER_NAME'];
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            $server_name = $_SERVER['HTTP_HOST'];
        } else {
            $server_name = 'unknown';
        }

        $is_ssl = isset($_SERVER['HTTPS']) &&
                 $_SERVER['HTTPS'] != 'off';

        if ($remote == $remote_addr) {
            $remote = '[' . $remote . ']';
        }

        $this->_headers->addHeaderOb(new Horde_Mime_Headers_Element_Multiple(
            'Received',
            'from ' . $remote . ' (' . $remote_ident .
            '[' . $remote_addr . ']) ' .
            'by ' . $server_name . ' (Horde Framework) with HTTP' .
            ($is_ssl ? 'S' : '') . '; ' . date('r')
        ));
    }

    /**
     */
    public function getOb($field)
    {
        return ($h = $this->_headers[$field])
            ? $h->getAddressList(true)
            : null;
    }

    /**
     */
    public function getValue($header, $type = Horde_Mime_Headers::VALUE_STRING)
    {
        if (!($ob = $this->_headers[$header])) {
            return null;
        }

        switch ($type) {
        case Horde_Mime_Headers::VALUE_BASE:
            $tmp = $ob->value;
            break;

        case Horde_Mime_Headers::VALUE_PARAMS:
            return array_change_key_case($ob->params, CASE_LOWER);

        case Horde_Mime_Headers::VALUE_STRING:
            $tmp = $ob->full_value;
            break;
        }

        return (is_array($tmp) && (count($tmp) === 1))
            ? reset($tmp)
            : $tmp;
    }

    /**
     */
    public function listHeaders()
    {
        $lhdrs = new Horde_ListHeaders();
        return $lhdrs->headers();
    }

    /**
     */
    public function listHeadersExist()
    {
        $lhdrs = new Horde_ListHeaders();
        return $lhdrs->listHeadersExist($this->_headers);
    }

    /**
     */
    public function replaceHeader($header, $value, array $opts = array())
    {
        $this->_headers->removeHeader($header);
        $this->_headers->addHeader($header, $value, $opts);
    }

    /**
     */
    public function getString($header)
    {
        return (($hdr = $this->_headers[$header]) === null)
            ? null
            : $this->_headers[$header]->name;
    }

    /**
     */
    public function addressFields()
    {
        return array(
            'from', 'to', 'cc', 'bcc', 'reply-to', 'resent-to', 'resent-cc',
            'resent-bcc', 'resent-from', 'sender'
        );
    }

    /**
     */
    public function singleFields($list = true)
    {
        $fields = array(
            'to', 'from', 'cc', 'bcc', 'date', 'sender', 'reply-to',
            'message-id', 'in-reply-to', 'references', 'subject',
            'content-md5', 'mime-version', 'content-type',
            'content-transfer-encoding', 'content-id', 'content-description',
            'content-base', 'content-disposition', 'content-duration',
            'content-location', 'content-features', 'content-language',
            'content-alternative', 'importance', 'x-priority'
        );

        $list_fields = array(
            'list-help', 'list-unsubscribe', 'list-subscribe', 'list-owner',
            'list-post', 'list-archive', 'list-id'
        );

        return $list
            ? array_merge($fields, $list_fields)
            : $fields;
    }

    /**
     */
    public function mimeParamFields()
    {
        return array('content-type', 'content-disposition');
    }

}
