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
 * Sendmail interface.
 *
 * @author    Chuck Hagenbuch <chuck@horde.org>
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/bsd New BSD License
 * @package   Mail
 */
class Horde_Mail_Transport_Sendmail extends Horde_Mail_Transport
{
    /**
     * Any extra command-line parameters to pass to the sendmail or
     * sendmail wrapper binary.
     *
     * @var string
     */
    protected $_sendmailArgs = '-i';

    /**
     * The location of the sendmail or sendmail wrapper binary on the
     * filesystem.
     *
     * @var string
     */
    protected $_sendmailPath = '/usr/sbin/sendmail';

    /**
     * Constructor.
     *
     * @param array $params  Additional parameters:
     *   - sendmail_args: (string) Any extra parameters to pass to the sendmail
     *                    or sendmail wrapper binary.
     *                    DEFAULT: -i
     *   - sendmail_path: (string) The location of the sendmail binary on the
     *                    filesystem.
     *                    DEFAULT: /usr/sbin/sendmail
     */
    public function __construct(array $params = array())
    {
        if (isset($params['sendmail_args'])) {
            $this->_sendmailArgs = $params['sendmail_args'];
        }

        if (isset($params['sendmail_path'])) {
            $this->_sendmailPath = $params['sendmail_path'];
        }
    }

    /**
     */
    public function send($recipients, array $headers, $body)
    {
        $recipients = implode(' ', array_map('escapeshellarg', $this->parseRecipients($recipients)));

        $headers = $this->_sanitizeHeaders($headers);
        list($from, $text_headers) = $this->prepareHeaders($headers);
        $from = $this->_getFrom($from, $headers);

        $mail = @popen($this->_sendmailPath . (empty($this->_sendmailArgs) ? '' : ' ' . $this->_sendmailArgs) . ' -f ' . escapeshellarg($from) . ' -- ' . $recipients, 'w');
        if (!$mail) {
            throw new Horde_Mail_Exception('Failed to open sendmail [' . $this->_sendmailPath . '] for execution.');
        }

        // Write the headers following by two newlines: one to end the headers
        // section and a second to separate the headers block from the body.
        fputs($mail, $text_headers . $this->sep . $this->sep);

        if (is_resource($body)) {
            stream_filter_register('horde_eol', 'Horde_Stream_Filter_Eol');
            stream_filter_append($body, 'horde_eol', STREAM_FILTER_READ, array('eol' => $this->sep));

            rewind($body);
            while (!feof($body)) {
                fputs($mail, fread($body, 8192));
            }
        } else {
            fputs($mail, $this->_normalizeEOL($body));
        }
        $result = pclose($mail);

        if (!$result) {
            return;
        }

        switch ($result) {
        case 64: // EX_USAGE
            $msg = 'command line usage error';
            break;

        case 65: // EX_DATAERR
            $msg =  'data format error';
            break;

        case 66: // EX_NOINPUT
            $msg = 'cannot open input';
            break;

        case 67: // EX_NOUSER
            $msg = 'addressee unknown';
            break;

        case 68: // EX_NOHOST
            $msg = 'host name unknown';
            break;

        case 69: // EX_UNAVAILABLE
            $msg = 'service unavailable';
            break;

        case 70: // EX_SOFTWARE
            $msg = 'internal software error';
            break;

        case 71: // EX_OSERR
            $msg = 'system error';
            break;

        case 72: // EX_OSFILE
            $msg = 'critical system file missing';
            break;

        case 73: // EX_CANTCREAT
            $msg = 'cannot create output file';
            break;

        case 74: // EX_IOERR
            $msg = 'input/output error';

        case 75: // EX_TEMPFAIL
            $msg = 'temporary failure';
            break;

        case 76: // EX_PROTOCOL
            $msg = 'remote error in protocol';
            break;

        case 77: // EX_NOPERM
            $msg = 'permission denied';
            break;

        case 78: // EX_CONFIG
            $msg = 'configuration error';
            break;

        case 79: // EX_NOTFOUND
            $msg = 'entry not found';
            break;

        default:
            $msg = 'unknown error';
            break;
        }

        throw new Horde_Mail_Exception('sendmail: ' . $msg . ' (' . $result . ')', $result);
    }

}
