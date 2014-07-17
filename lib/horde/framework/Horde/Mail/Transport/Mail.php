<?php
/**
 * Copyright 2010-2014 Horde LLC (http://www.horde.org/)
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
 * @copyright 2010-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */

/**
 * Internal PHP-mail() interface.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2010-2014 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Transport_Mail extends Horde_Mail_Transport
{
    /**
     * @param array $params  Additional parameters:
     *   - args: (string) Extra arguments for the mail() function.
     */
    public function __construct(array $params = array())
    {
        $this->_params = array_merge($this->_params, $params);
    }

    /**
     */
    public function send($recipients, array $headers, $body)
    {
        $headers = $this->_sanitizeHeaders($headers);
        $recipients = $this->parseRecipients($recipients);
        $subject = '';

        foreach (array_keys($headers) as $hdr) {
            if (strcasecmp($hdr, 'Subject') === 0) {
                // Get the Subject out of the headers array so that we can
                // pass it as a separate argument to mail().
                $subject = $headers[$hdr];
                unset($headers[$hdr]);
            } elseif (strcasecmp($hdr, 'To') === 0) {
                // Remove the To: header.  The mail() function will add its
                // own To: header based on the contents of $recipients.
                unset($headers[$hdr]);
            }
        }

        // Flatten the headers out.
        list(, $text_headers) = $this->prepareHeaders($headers);

        // mail() requires a string for $body. If resource, need to convert
        // to a string.
        if (is_resource($body)) {
            $body_str = '';

            stream_filter_register('horde_eol', 'Horde_Stream_Filter_Eol');
            stream_filter_append($body, 'horde_eol', STREAM_FILTER_READ, array('eol' => $this->sep));

            rewind($body);
            while (!feof($body)) {
                $body_str .= fread($body, 8192);
            }
            $body = $body_str;
        } else {
            // Convert EOL characters in body.
            $body = $this->_normalizeEOL($body);
        }

        // We only use mail()'s optional fifth parameter if the additional
        // parameters have been provided and we're not running in safe mode.
        if (empty($this->_params) || ini_get('safe_mode')) {
            $result = mail($recipients, $subject, $body, $text_headers);
        } else {
            $result = mail($recipients, $subject, $body, $text_headers, isset($this->_params['args']) ? $this->_params['args'] : '');
        }

        // If the mail() function returned failure, we need to create an
        // Exception and return it instead of the boolean result.
        if ($result === false) {
            throw new Horde_Mail_Exception('mail() returned failure.');
        }
    }

}
