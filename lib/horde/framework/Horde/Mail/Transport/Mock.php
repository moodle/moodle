<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
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
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Mock implementation, for testing.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Transport_Mock extends Horde_Mail_Transport
{
    /**
     * Array of messages that have been sent with the mock.
     *
     * @var array
     */
    public $sentMessages = array();

    /**
     * Callback before sending mail.
     *
     * @var callback
     */
    protected $_preSendCallback;

    /**
     * Callback after sending mai.
     *
     * @var callback
     */
    protected $_postSendCallback;

    /**
     * @param array  Optional parameters:
     *   - postSendCallback: (callback) Called after an email would have been
     *                       sent.
     *   - preSendCallback: (callback) Called before an email would be sent.
     */
    public function __construct(array $params = array())
    {
        if (isset($params['preSendCallback']) &&
            is_callable($params['preSendCallback'])) {
            $this->_preSendCallback = $params['preSendCallback'];
        }

        if (isset($params['postSendCallback']) &&
            is_callable($params['postSendCallback'])) {
            $this->_postSendCallback = $params['postSendCallback'];
        }
    }

    /**
     */
    public function send($recipients, array $headers, $body)
    {
        if ($this->_preSendCallback) {
            call_user_func_array($this->_preSendCallback, array($this, $recipients, $headers, $body));
        }

        $headers = $this->_sanitizeHeaders($headers);
        list($from, $text_headers) = $this->prepareHeaders($headers);

        if (is_resource($body)) {
            stream_filter_register('horde_eol', 'Horde_Stream_Filter_Eol');
            stream_filter_append($body, 'horde_eol', STREAM_FILTER_READ, array('eol' => $this->sep));

            rewind($body);
            $body_txt = stream_get_contents($body);
        } else {
            $body_txt = $this->_normalizeEOL($body);
        }

        $from = $this->_getFrom($from, $headers);
        $recipients = $this->parseRecipients($recipients);

        $this->sentMessages[] = array(
            'body' => $body_txt,
            'from' => $from,
            'headers' => $headers,
            'header_text' => $text_headers,
            'recipients' => $recipients
        );

        if ($this->_postSendCallback) {
            call_user_func_array($this->_postSendCallback, array($this, $recipients, $headers, $body_txt));
        }
    }

}
