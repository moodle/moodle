<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An object representing an IMAP server command interaction (RFC 3501
 * [2.2.2]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Interaction_Server
{
    /**
     * Response codes (RFC 3501 [7.1]).
     */
    const BAD = 1;
    const BYE = 2;
    const NO = 3;
    const OK = 4;
    const PREAUTH = 5;

    /**
     * Check for status response?
     *
     * @var boolean
     */
    protected $_checkStatus = true;

    /**
     * Response code (RFC 3501 [7.1]). Properties:
     *   - code: (string) Response code.
     *   - data: (array) Data associated with response.
     *
     * @var object
     */
    public $responseCode = null;

    /**
     * Status response from the server.
     *
     * @var string
     */
    public $status = null;

    /**
     * IMAP server data.
     *
     * @var Horde_Imap_Client_Tokenize
     */
    public $token;

    /**
     * Auto-scan an incoming line to determine the response type.
     *
     * @param Horde_Imap_Client_Tokenize $t  Tokenized data returned from the
     *                                       server.
     *
     * @return Horde_Imap_Client_Interaction_Server  A server response object.
     */
    public static function create(Horde_Imap_Client_Tokenize $t)
    {
        $t->rewind();
        $tag = $t->next();
        $t->next();

        switch ($tag) {
        case '+':
            return new Horde_Imap_Client_Interaction_Server_Continuation($t);

        case '*':
            return new Horde_Imap_Client_Interaction_Server_Untagged($t);

        default:
            return new Horde_Imap_Client_Interaction_Server_Tagged($t, $tag);
        }
    }

    /**
     * Constructor.
     *
     * @param Horde_Imap_Client_Tokenize $token  Tokenized data returned from
     *                                           the server.
     */
    public function __construct(Horde_Imap_Client_Tokenize $token)
    {
        $this->token = $token;

        /* Check for response status. */
        $status = $token->current();
        $valid = array('BAD', 'BYE', 'NO', 'OK', 'PREAUTH');

        if (in_array($status, $valid)) {
            $this->status = constant(__CLASS__ . '::' . $status);
            $resp_text = $token->next();

            /* Check for response code. Only occurs if there is a response
             * status. */
            if (is_string($resp_text) && ($resp_text[0] === '[')) {
                $resp = new stdClass;
                $resp->data = array();

                if ($resp_text[strlen($resp_text) - 1] === ']') {
                    $resp->code = substr($resp_text, 1, -1);
                } else {
                    $resp->code = substr($resp_text, 1);

                    while (($elt = $token->next()) !== false) {
                        if (is_string($elt) && $elt[strlen($elt) - 1] === ']') {
                            $resp->data[] = substr($elt, 0, -1);
                            break;
                        }
                        $resp->data[] = is_string($elt)
                            ? $elt
                            : $token->flushIterator();
                    }
                }

                $token->next();
                $this->responseCode = $resp;
            }
        }
    }

    /**
     */
    public function __toString()
    {
        return strval($this->token);
    }

}
