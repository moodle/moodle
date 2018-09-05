<?php
/**
 * Copyright 2005-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * Originally based on code from:
 *   - auth.php (1.49)
 *   - imap_general.php (1.212)
 *   - imap_messages.php (revision 13038)
 *   - strings.php (1.184.2.35)
 * from the Squirrelmail project.
 * Copyright (c) 1999-2007 The SquirrelMail Project Team
 *
 * @category  Horde
 * @copyright 1999-2007 The SquirrelMail Project Team
 * @copyright 2005-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An interface to an IMAP4rev1 server (RFC 3501) using standard PHP code.
 *
 * Implements the following IMAP-related RFCs (see
 * http://www.iana.org/assignments/imap4-capabilities):
 * <pre>
 *   - RFC 2086/4314: ACL
 *   - RFC 2087: QUOTA
 *   - RFC 2088: LITERAL+
 *   - RFC 2195: AUTH=CRAM-MD5
 *   - RFC 2221: LOGIN-REFERRALS
 *   - RFC 2342: NAMESPACE
 *   - RFC 2595/4616: TLS & AUTH=PLAIN
 *   - RFC 2831: DIGEST-MD5 authentication mechanism (obsoleted by RFC 6331)
 *   - RFC 2971: ID
 *   - RFC 3348: CHILDREN
 *   - RFC 3501: IMAP4rev1 specification
 *   - RFC 3502: MULTIAPPEND
 *   - RFC 3516: BINARY
 *   - RFC 3691: UNSELECT
 *   - RFC 4315: UIDPLUS
 *   - RFC 4422: SASL Authentication (for DIGEST-MD5)
 *   - RFC 4466: Collected extensions (updates RFCs 2088, 3501, 3502, 3516)
 *   - RFC 4469/5550: CATENATE
 *   - RFC 4731: ESEARCH
 *   - RFC 4959: SASL-IR
 *   - RFC 5032: WITHIN
 *   - RFC 5161: ENABLE
 *   - RFC 5182: SEARCHRES
 *   - RFC 5255: LANGUAGE/I18NLEVEL
 *   - RFC 5256: THREAD/SORT
 *   - RFC 5258: LIST-EXTENDED
 *   - RFC 5267: ESORT; PARTIAL search return option
 *   - RFC 5464: METADATA
 *   - RFC 5530: IMAP Response Codes
 *   - RFC 5802: AUTH=SCRAM-SHA-1
 *   - RFC 5819: LIST-STATUS
 *   - RFC 5957: SORT=DISPLAY
 *   - RFC 6154: SPECIAL-USE/CREATE-SPECIAL-USE
 *   - RFC 6203: SEARCH=FUZZY
 *   - RFC 6851: MOVE
 *   - RFC 6855: UTF8=ACCEPT/UTF8=ONLY
 *   - RFC 6858: DOWNGRADED response code
 *   - RFC 7162: CONDSTORE/QRESYNC
 * </pre>
 *
 * Implements the following non-RFC extensions:
 * <pre>
 *   - draft-ietf-morg-inthread-01: THREAD=REFS
 *   - draft-daboo-imap-annotatemore-07: ANNOTATEMORE
 *   - draft-daboo-imap-annotatemore-08: ANNOTATEMORE2
 *   - XIMAPPROXY
 *     Requires imapproxy v1.2.7-rc1 or later
 *     See https://squirrelmail.svn.sourceforge.net/svnroot/squirrelmail/trunk/imap_proxy/README
 *   - AUTH=XOAUTH2
 *     https://developers.google.com/gmail/xoauth2_protocol
 * </pre>
 *
 * TODO (or not necessary?):
 * <pre>
 *   - RFC 2177: IDLE
 *   - RFC 2193: MAILBOX-REFERRALS
 *   - RFC 4467/5092/5524/5550/5593: URLAUTH, URLAUTH=BINARY, URL-PARTIAL
 *   - RFC 4978: COMPRESS=DEFLATE
 *     See: http://bugs.php.net/bug.php?id=48725
 *   - RFC 5257: ANNOTATE (Experimental)
 *   - RFC 5259: CONVERT
 *   - RFC 5267: CONTEXT=SEARCH; CONTEXT=SORT
 *   - RFC 5465: NOTIFY
 *   - RFC 5466: FILTERS
 *   - RFC 6785: IMAPSIEVE
 *   - RFC 7377: MULTISEARCH
 * </pre>
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 1999-2007 The SquirrelMail Project Team
 * @copyright 2005-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Socket extends Horde_Imap_Client_Base
{
    /**
     * Cache names used exclusively within this class.
     */
    const CACHE_FLAGS = 'HICflags';

    /**
     * Queued commands to send to the server.
     *
     * @var array
     */
    protected $_cmdQueue = array();

    /**
     * The default ports to use for a connection.
     *
     * @var array
     */
    protected $_defaultPorts = array(143, 993);

    /**
     * Mapping of status fields to IMAP names.
     *
     * @var array
     */
    protected $_statusFields = array(
        'messages' => Horde_Imap_Client::STATUS_MESSAGES,
        'recent' => Horde_Imap_Client::STATUS_RECENT,
        'uidnext' => Horde_Imap_Client::STATUS_UIDNEXT,
        'uidvalidity' => Horde_Imap_Client::STATUS_UIDVALIDITY,
        'unseen' => Horde_Imap_Client::STATUS_UNSEEN,
        'firstunseen' => Horde_Imap_Client::STATUS_FIRSTUNSEEN,
        'flags' => Horde_Imap_Client::STATUS_FLAGS,
        'permflags' => Horde_Imap_Client::STATUS_PERMFLAGS,
        'uidnotsticky' => Horde_Imap_Client::STATUS_UIDNOTSTICKY,
        'highestmodseq' => Horde_Imap_Client::STATUS_HIGHESTMODSEQ
    );

    /**
     * The unique tag to use when making an IMAP query.
     *
     * @var integer
     */
    protected $_tag = 0;

    /**
     * @param array $params  A hash containing configuration parameters.
     *                       Additional parameters to base driver:
     *   - debug_literal: (boolean) If true, will output the raw text of
     *                    literal responses to the debug stream. Otherwise,
     *                    outputs a summary of the literal response.
     *   - envelope_addrs: (integer) The maximum number of address entries to
     *                     read for FETCH ENVELOPE address fields.
     *                     DEFAULT: 1000
     *   - envelope_string: (integer) The maximum length of string fields
     *                      returned by the FETCH ENVELOPE command.
     *                      DEFAULT: 2048
     *   - xoauth2_token: (mixed) If set, will authenticate via the XOAUTH2
     *                    mechanism (if available) with this token. Either a
     *                    string (since 2.13.0) or a
     *                    Horde_Imap_Client_Base_Password object (since
     *                    2.14.0).
     */
    public function __construct(array $params = array())
    {
        parent::__construct(array_merge(array(
            'debug_literal' => false,
            'envelope_addrs' => 1000,
            'envelope_string' => 2048
        ), $params));
    }

    /**
     */
    public function __get($name)
    {
        switch ($name) {
        case 'search_charset':
            if (!isset($this->_init['search_charset']) &&
                $this->_capability()->isEnabled('UTF8=ACCEPT')) {
                $this->_init['search_charset'] = new Horde_Imap_Client_Data_SearchCharset_Utf8();
            }
            break;
        }

        return parent::__get($name);
    }

    /**
     */
    public function getParam($key)
    {
        switch ($key) {
        case 'xoauth2_token':
            if (isset($this->_params[$key]) &&
                ($this->_params[$key] instanceof Horde_Imap_Client_Base_Password)) {
                return $this->_params[$key]->getPassword();
            }
            break;
        }

        return parent::getParam($key);
    }

    /**
     */
    public function update(SplSubject $subject)
    {
        if (!empty($this->_init['imapproxy']) &&
            ($subject instanceof Horde_Imap_Client_Data_Capability_Imap)) {
            $this->_setInit('enabled', $subject->isEnabled());
        }

        return parent::update($subject);
    }

    /**
     */
    protected function _initCapability()
    {
        // Need to use connect call here or else we run into loop issues
        // because _connect() can generate the capability object internally.
        $this->_connect();

        // It is possible the server provided capability information on
        // connect, so check for it now.
        if (!isset($this->_init['capability'])) {
            $this->_sendCmd($this->_command('CAPABILITY'));
        }
    }

    /**
     * Parse a CAPABILITY Response (RFC 3501 [7.2.1]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param array $data  An array of CAPABILITY strings.
     */
    protected function _parseCapability(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        $data
    )
    {
        if (!empty($this->_temp['no_cap'])) {
            return;
        }

        $pipeline->data['capability_set'] = true;

        $c = new Horde_Imap_Client_Data_Capability_Imap();

        foreach ($data as $val) {
            $cap_list = explode('=', $val);
            $c->add(
                $cap_list[0],
                isset($cap_list[1]) ? array($cap_list[1]) : null
            );
        }

        $this->_setInit('capability', $c);
    }

    /**
     */
    protected function _noop()
    {
        // NOOP doesn't return any specific response
        $this->_sendCmd($this->_command('NOOP'));
    }

    /**
     */
    protected function _getNamespaces()
    {
        if ($this->_capability('NAMESPACE')) {
            $data = $this->_sendCmd($this->_command('NAMESPACE'))->data;
            if (isset($data['namespace'])) {
                return $data['namespace'];
            }
        }

        return new Horde_Imap_Client_Namespace_List();
    }

    /**
     * Parse a NAMESPACE response (RFC 2342 [5] & RFC 5255 [3.4]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The NAMESPACE data.
     */
    protected function _parseNamespace(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        $namespace_array = array(
            Horde_Imap_Client_Data_Namespace::NS_PERSONAL,
            Horde_Imap_Client_Data_Namespace::NS_OTHER,
            Horde_Imap_Client_Data_Namespace::NS_SHARED
        );

        $c = array();

        // Per RFC 2342, response from NAMESPACE command is:
        // (PERSONAL NAMESPACES) (OTHER_USERS NAMESPACE) (SHARED NAMESPACES)
        foreach ($namespace_array as $val) {
            $entry = $data->next();

            if (is_null($entry)) {
                continue;
            }

            while ($data->next() !== false) {
                $ob = Horde_Imap_Client_Mailbox::get($data->next(), true);

                $ns = new Horde_Imap_Client_Data_Namespace();
                $ns->delimiter = $data->next();
                $ns->name = strval($ob);
                $ns->type = $val;
                $c[strval($ob)] = $ns;

                // RFC 4466: NAMESPACE extensions
                while (($ext = $data->next()) !== false) {
                    switch (Horde_String::upper($ext)) {
                    case 'TRANSLATION':
                        // RFC 5255 [3.4] - TRANSLATION extension
                        $data->next();
                        $ns->translation = $data->next();
                        $data->next();
                        break;
                    }
                }
            }
        }

        $pipeline->data['namespace'] = new Horde_Imap_Client_Namespace_List($c);
    }

    /**
     */
    protected function _login()
    {
        $secure = $this->getParam('secure');

        if (!empty($this->_temp['preauth'])) {
            unset($this->_temp['preauth']);

            /* Don't allow PREAUTH if we are requring secure access, since
             * PREAUTH cannot provide secure access. */
            if (!$this->isSecureConnection() && ($secure !== false)) {
                $this->logout();
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Could not open secure TLS connection to the IMAP server."),
                    Horde_Imap_Client_Exception::LOGIN_TLSFAILURE
                );
            }

            return $this->_loginTasks();
        }

        /* Blank passwords are not allowed, so no need to even try
         * authentication to determine this. */
        if (!strlen($this->getParam('password'))) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("No password provided."),
                Horde_Imap_Client_Exception::LOGIN_AUTHENTICATIONFAILED
            );
        }

        $this->_connect();

        $first_login = empty($this->_init['authmethod']);

        // Switch to secure channel if using TLS.
        if (!$this->isSecureConnection() &&
            (($secure === 'tls') ||
             (($secure === true) &&
              $this->_capability('LOGINDISABLED')))) {
            if ($first_login && !$this->_capability('STARTTLS')) {
                /* We should never hit this - STARTTLS is required pursuant to
                 * RFC 3501 [6.2.1]. */
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Server does not support TLS connections."),
                    Horde_Imap_Client_Exception::LOGIN_TLSFAILURE
                );
            }

            // Switch over to a TLS connection.
            // STARTTLS returns no untagged response.
            $this->_sendCmd($this->_command('STARTTLS'));

            if (!$this->_connection->startTls()) {
                $this->logout();
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Could not open secure TLS connection to the IMAP server."),
                    Horde_Imap_Client_Exception::LOGIN_TLSFAILURE
                );
            }

            $this->_debug->info('Successfully completed TLS negotiation.');

            $this->setParam('secure', 'tls');
            $secure = 'tls';

            if ($first_login) {
                // Expire cached CAPABILITY information (RFC 3501 [6.2.1])
                $this->_setInit('capability');

                // Reset language (RFC 5255 [3.1])
                $this->_setInit('lang');
            }

            // Set language if using imapproxy
            if (!empty($this->_init['imapproxy'])) {
                $this->setLanguage();
            }
        }

        /* If we reached this point and don't have a secure connection, then
         * a secure connections is not available. */
        if (($secure === true) && !$this->isSecureConnection()) {
            $this->setParam('secure', false);
            $secure = false;
        }

        if ($first_login) {
            // Add authentication methods.
            $auth_mech = array();
            $auth = array_flip($this->_capability()->getParams('AUTH'));

            // XOAUTH2
            if (isset($auth['XOAUTH2']) && $this->getParam('xoauth2_token')) {
                $auth_mech[] = 'XOAUTH2';
            }
            unset($auth['XOAUTH2']);

            /* 'AUTH=PLAIN' authentication always exists if under TLS (RFC 3501
             *  [7.2.1]; RFC 2595), even though we might get here with a
             *  non-TLS secure connection too. Use it over all other
             *  authentication methods, although we need to do sanity checking
             *  since broken IMAP servers may not support as required -
             *  fallback to LOGIN instead, if not explicitly disabled. */
            if ($secure) {
                if (isset($auth['PLAIN'])) {
                    $auth_mech[] = 'PLAIN';
                    unset($auth['PLAIN']);
                } elseif (!$this->_capability('LOGINDISABLED')) {
                    $auth_mech[] = 'LOGIN';
                }
            }

            // Check for supported SCRAM AUTH mechanisms. Preferred because it
            // provides verification of server authenticity.
            foreach (array_keys($auth) as $key) {
                switch ($key) {
                case 'SCRAM-SHA-1':
                    $auth_mech[] = $key;
                    unset($auth[$key]);
                    break;
                }
            }

            // Check for supported CRAM AUTH mechanisms.
            foreach (array_keys($auth) as $key) {
                switch ($key) {
                case 'CRAM-SHA1':
                case 'CRAM-SHA256':
                    $auth_mech[] = $key;
                    unset($auth[$key]);
                    break;
                }
            }

            // Prefer CRAM-MD5 over DIGEST-MD5, as the latter has been
            // obsoleted (RFC 6331).
            if (isset($auth['CRAM-MD5'])) {
                $auth_mech[] = 'CRAM-MD5';
            } elseif (isset($auth['DIGEST-MD5'])) {
                $auth_mech[] = 'DIGEST-MD5';
            }
            unset($auth['CRAM-MD5'], $auth['DIGEST-MD5']);

            // Add other auth mechanisms.
            $auth_mech = array_merge($auth_mech, array_keys($auth));

            // Fall back to 'LOGIN' if available.
            if (!$secure && !$this->_capability('LOGINDISABLED')) {
                $auth_mech[] = 'LOGIN';
            }

            if (empty($auth_mech)) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("No supported IMAP authentication method could be found."),
                    Horde_Imap_Client_Exception::LOGIN_NOAUTHMETHOD
                );
            }

            $auth_mech = array_unique($auth_mech);
        } else {
            $auth_mech = array($this->_init['authmethod']);
        }

        $login_err = null;

        foreach ($auth_mech as $method) {
            try {
                $resp = $this->_tryLogin($method);
                $data = $resp->data;
                $this->_setInit('authmethod', $method);
                unset($this->_temp['referralcount']);
            } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
                $data = $e->resp_data;
                if (isset($data['loginerr'])) {
                    $login_err = $data['loginerr'];
                }
                $resp = false;
            } catch (Horde_Imap_Client_Exception $e) {
                $resp = false;
            }

            // Check for login referral (RFC 2221) response - can happen for
            // an OK, NO, or BYE response.
            if (isset($data['referral'])) {
                foreach (array('host', 'port', 'username') as $val) {
                    if (!is_null($data['referral']->$val)) {
                        $this->setParam($val, $data['referral']->$val);
                    }
                }

                if (!is_null($data['referral']->auth)) {
                    $this->_setInit('authmethod', $data['referral']->auth);
                }

                if (!isset($this->_temp['referralcount'])) {
                    $this->_temp['referralcount'] = 0;
                }

                // RFC 2221 [3] - Don't follow more than 10 levels of referral
                // without consulting the user.
                if (++$this->_temp['referralcount'] < 10) {
                    $this->logout();
                    $this->_setInit('capability');
                    $this->_setInit('namespace');
                    return $this->login();
                }

                unset($this->_temp['referralcount']);
            }

            if ($resp) {
                return $this->_loginTasks($first_login, $resp->data);
            }
        }

        /* Try again from scratch if authentication failed in an established,
         * previously-authenticated object. */
        if (!empty($this->_init['authmethod'])) {
            $this->_setInit();
            unset($this->_temp['no_cap']);
            try {
                return $this->_login();
            } catch (Horde_Imap_Client_Exception $e) {}
        }

        /* Default to AUTHENTICATIONFAILED error (see RFC 5530[3]). */
        if (is_null($login_err)) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Mail server denied authentication."),
                Horde_Imap_Client_Exception::LOGIN_AUTHENTICATIONFAILED
            );
        }

        throw $login_err;
    }

    /**
     * Connects to the IMAP server.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _connect()
    {
        if (!is_null($this->_connection)) {
            return;
        }

        try {
            $this->_connection = new Horde_Imap_Client_Socket_Connection_Socket(
                $this->getParam('hostspec'),
                $this->getParam('port'),
                $this->getParam('timeout'),
                $this->getParam('secure'),
                $this->getParam('context'),
                array(
                    'debug' => $this->_debug,
                    'debugliteral' => $this->getParam('debug_literal')
                )
            );
        } catch (Horde\Socket\Client\Exception $e) {
            $e2 = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Error connecting to mail server."),
                Horde_Imap_Client_Exception::SERVER_CONNECT
            );
            $e2->details = $e->details;
            throw $e2;
        }

        // If we already have capability information, don't re-set with
        // (possibly) limited information sent in the initial banner.
        if (isset($this->_init['capability'])) {
            $this->_temp['no_cap'] = true;
        }

        /* Get greeting information (untagged response). */
        try {
            $this->_getLine($this->_pipeline());
        } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
            if ($e->status === Horde_Imap_Client_Interaction_Server::BYE) {
                /* Server is explicitly rejecting our connection (RFC 3501
                 * [7.1.5]). */
                $e->setMessage(Horde_Imap_Client_Translation::r("Server rejected connection."));
                $e->setCode(Horde_Imap_Client_Exception::SERVER_CONNECT);
            }
            throw $e;
        }

        // Check for IMAP4rev1 support
        if (!$this->_capability('IMAP4REV1')) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("The mail server does not support IMAP4rev1 (RFC 3501)."),
                Horde_Imap_Client_Exception::SERVER_CONNECT
            );
        }

        // Set language if NOT using imapproxy
        if (empty($this->_init['imapproxy'])) {
            if ($this->_capability('XIMAPPROXY')) {
                $this->_setInit('imapproxy', true);
            } else {
                $this->setLanguage();
            }
        }

        // If pre-authenticated, we need to do all login tasks now.
        if (!empty($this->_temp['preauth'])) {
            $this->login();
        }
    }

    /**
     * Authenticate to the IMAP server.
     *
     * @param string $method  IMAP login method.
     *
     * @return Horde_Imap_Client_Interaction_Pipeline  Pipeline object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _tryLogin($method)
    {
        $username = $this->getParam('username');
        $password = $this->getParam('password');

        switch ($method) {
        case 'CRAM-MD5':
        case 'CRAM-SHA1':
        case 'CRAM-SHA256':
            // RFC 2195: CRAM-MD5
            // CRAM-SHA1 & CRAM-SHA256 supported by Courier SASL library

            $args = array(
                $username,
                Horde_String::lower(substr($method, 5)),
                $password
            );

            $cmd = $this->_command('AUTHENTICATE')->add(array(
                $method,
                new Horde_Imap_Client_Interaction_Command_Continuation(function($ob) use ($args) {
                    return new Horde_Imap_Client_Data_Format_List(
                        base64_encode($args[0] . ' ' . hash_hmac($args[1], base64_decode($ob->token->current()), $args[2], false))
                    );
                })
            ));
            $cmd->debug = array(
                null,
                sprintf('[AUTHENTICATE response (username: %s)]', $username)
            );
            break;

        case 'DIGEST-MD5':
            // RFC 2831/4422; obsoleted by RFC 6331

            // Need $args because PHP 5.3 doesn't allow access to $this in
            // anonymous functions.
            $args = array(
                $username,
                $password,
                $this->getParam('hostspec')
            );

            $cmd = $this->_command('AUTHENTICATE')->add(array(
                $method,
                new Horde_Imap_Client_Interaction_Command_Continuation(function($ob) use ($args) {
                    return new Horde_Imap_Client_Data_Format_List(
                        base64_encode(new Horde_Imap_Client_Auth_DigestMD5(
                            $args[0],
                            $args[1],
                            base64_decode($ob->token->current()),
                            $args[2],
                            'imap'
                        ))
                    );
                }),
                new Horde_Imap_Client_Interaction_Command_Continuation(function($ob) {
                    if (strpos(base64_decode($ob->token->current()), 'rspauth=') === false) {
                        throw new Horde_Imap_Client_Exception(
                            Horde_Imap_Client_Translation::r("Unexpected response from server when authenticating."),
                            Horde_Imap_Client_Exception::SERVER_CONNECT
                        );
                    }

                    return new Horde_Imap_Client_Data_Format_List();
                })
            ));
            $cmd->debug = array(
                null,
                sprintf('[AUTHENTICATE Response (username: %s)]', $username),
                null
            );
            break;

        case 'LOGIN':
            /* See, e.g., RFC 6855 [5] - LOGIN command does not support
             * non-ASCII characters. If we reach this point, treat as an
             * authentication failure. */
            try {
                $username = new Horde_Imap_Client_Data_Format_Astring($username);
                $password = new Horde_Imap_Client_Data_Format_Astring($password);
            } catch (Horde_Imap_Client_Data_Format_Exception $e) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Authentication failed."),
                    Horde_Imap_Client_Exception::LOGIN_AUTHENTICATIONFAILED
                );
            }

            $cmd = $this->_command('LOGIN')->add(array(
                $username,
                $password
            ));
            $cmd->debug = array(
                sprintf('LOGIN %s [PASSWORD]', $username)
            );
            break;

        case 'PLAIN':
            // RFC 2595/4616 - PLAIN SASL mechanism
            $cmd = $this->_authInitialResponse(
                $method,
                base64_encode(implode("\0", array(
                    $username,
                    $username,
                    $password
                ))),
                $username
            );
            break;

        case 'SCRAM-SHA-1':
            $scram = new Horde_Imap_Client_Auth_Scram(
                $username,
                $password,
                'SHA1'
            );

            $cmd = $this->_authInitialResponse(
                $method,
                base64_encode($scram->getClientFirstMessage())
            );

            $cmd->add(
                new Horde_Imap_Client_Interaction_Command_Continuation(function($ob) use ($scram) {
                    $sr1 = base64_decode($ob->token->current());
                    return new Horde_Imap_Client_Data_Format_List(
                        $scram->parseServerFirstMessage($sr1)
                            ? base64_encode($scram->getClientFinalMessage())
                            : '*'
                    );
                })
            );

            $self = $this;
            $cmd->add(
                new Horde_Imap_Client_Interaction_Command_Continuation(function($ob) use ($scram, $self) {
                    $sr2 = base64_decode($ob->token->current());
                    if (!$scram->parseServerFinalMessage($sr2)) {
                        /* This means authentication passed, according to the
                         * server, but the server signature is incorrect.
                         * This indicates that server verification has failed.
                         * Immediately disconnect from the server, since this
                         * is a possible security issue. */
                        $self->logout();
                        throw new Horde_Imap_Client_Exception(
                            Horde_Imap_Client_Translation::r("Server failed verification check."),
                            Horde_Imap_Client_Exception::LOGIN_SERVER_VERIFICATION_FAILED
                        );
                    }

                    return new Horde_Imap_Client_Data_Format_List();
                })
            );
            break;

        case 'XOAUTH2':
            // Google XOAUTH2
            $cmd = $this->_authInitialResponse(
                $method,
                $this->getParam('xoauth2_token')
            );

            /* This is an optional command continuation. XOAUTH2 will return
             * error information in continuation response. */
            $error_continuation = new Horde_Imap_Client_Interaction_Command_Continuation(
                function($ob) {
                    return new Horde_Imap_Client_Data_Format_List();
                }
            );
            $error_continuation->optional = true;
            $cmd->add($error_continuation);
            break;

        default:
            $e = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Unknown authentication method: %s"),
                Horde_Imap_Client_Exception::SERVER_CONNECT
            );
            $e->messagePrintf(array($method));
            throw $e;
        }

        return $this->_sendCmd($this->_pipeline($cmd));
    }

    /**
     * Create the AUTHENTICATE command for the initial client response.
     *
     * @param string $method    AUTHENTICATE SASL method.
     * @param string $ir        Initial client response.
     * @param string $username  If set, log a username message in debug log
     *                          instead of raw data.
     *
     * @return Horde_Imap_Client_Interaction_Command  A command object.
     */
    protected function _authInitialResponse($method, $ir, $username = null)
    {
        $cmd = $this->_command('AUTHENTICATE')->add($method);

        if ($this->_capability('SASL-IR')) {
            // IMAP Extension for SASL Initial Client Response (RFC 4959)
            $cmd->add($ir);
            if ($username) {
                $cmd->debug = array(
                    sprintf('AUTHENTICATE %s [INITIAL CLIENT RESPONSE (username: %s)]', $method, $username)
                );
            }
        } else {
            $cmd->add(
                new Horde_Imap_Client_Interaction_Command_Continuation(function($ob) use ($ir) {
                    return new Horde_Imap_Client_Data_Format_List($ir);
                })
            );
            if ($username) {
                $cmd->debug = array(
                    null,
                    sprintf('[INITIAL CLIENT RESPONSE (username: %s)]', $username)
                );
            }
        }

        return $cmd;
    }

    /**
     * Perform login tasks.
     *
     * @param boolean $firstlogin  Is this the first login?
     * @param array $resp          The data response from the login command.
     *                             May include:
     *   - capability_set: (boolean) True if CAPABILITY was set after login.
     *   - proxyreuse: (boolean) True if re-used connection via imapproxy.
     *
     * @return boolean  True if global login tasks should be performed.
     */
    protected function _loginTasks($firstlogin = true, array $resp = array())
    {
        /* If reusing an imapproxy connection, no need to do any of these
         * login tasks again. */
        if (!$firstlogin && !empty($resp['proxyreuse'])) {
            if (isset($this->_init['enabled'])) {
                foreach ($this->_init['enabled'] as $val) {
                    $this->_capability()->enable($val);
                }
            }

            // If we have not yet set the language, set it now.
            if (!isset($this->_init['lang'])) {
                $this->_temp['lang_queue'] = true;
                $this->setLanguage();
                unset($this->_temp['lang_queue']);
            }
            return false;
        }

        /* If we logged in for first time, and server did not return
         * capability information, we need to mark for retrieval. */
        if ($firstlogin && empty($resp['capability_set'])) {
            $this->_setInit('capability');
        }

        $this->_temp['lang_queue'] = true;
        $this->setLanguage();
        unset($this->_temp['lang_queue']);

        /* Only active QRESYNC/CONDSTORE if caching is enabled. */
        $enable = array();
        if ($this->_initCache()) {
            if ($this->_capability('QRESYNC')) {
                $enable[] = 'QRESYNC';
            } elseif ($this->_capability('CONDSTORE')) {
                $enable[] = 'CONDSTORE';
            }
        }

        /* Use UTF8=ACCEPT, if available. */
        if ($this->_capability('UTF8', 'ACCEPT')) {
            $enable[] = 'UTF8=ACCEPT';
        }

        $this->_enable($enable);

        return true;
    }

    /**
     */
    protected function _logout()
    {
        if (empty($this->_temp['logout'])) {
            /* If using imapproxy, force sending these commands, since they
             * may not be sent again if they are (likely) initialization
             * commands. */
            if (!empty($this->_cmdQueue) &&
                !empty($this->_init['imapproxy'])) {
                $this->_sendCmd($this->_pipeline());
            }

            $this->_temp['logout'] = true;
            try {
                $this->_sendCmd($this->_command('LOGOUT'));
            } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
                // Ignore server errors
            }
            unset($this->_temp['logout']);
        }
    }

    /**
     */
    protected function _sendID($info)
    {
        $cmd = $this->_command('ID');

        if (empty($info)) {
            $cmd->add(new Horde_Imap_Client_Data_Format_Nil());
        } else {
            $tmp = new Horde_Imap_Client_Data_Format_List();
            foreach ($info as $key => $val) {
                $tmp->add(array(
                    new Horde_Imap_Client_Data_Format_String(Horde_String::lower($key)),
                    new Horde_Imap_Client_Data_Format_Nstring($val)
                ));
            }
            $cmd->add($tmp);
        }

        $temp = &$this->_temp;

        /* Add to queue - this doesn't need to be sent immediately. */
        $cmd->on_error = function() use (&$temp) {
            /* Ignore server errors. E.g. Cyrus returns this:
             *   001 NO Only one Id allowed in non-authenticated state
             * even though NO is not allowed in RFC 2971[3.1]. */
            $temp['id'] = array();
            return true;
        };
        $cmd->on_success = function() use ($cmd, &$temp) {
            $temp['id'] = $cmd->pipeline->data['id'];
        };
        $this->_cmdQueue[] = $cmd;
    }

    /**
     * Parse an ID response (RFC 2971 [3.2]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseID(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        if (!isset($pipeline->data['id'])) {
            $pipeline->data['id'] = array();
        }

        if (!is_null($data->next())) {
            while (($curr = $data->next()) !== false) {
                if (!is_null($id = $data->next())) {
                    $pipeline->data['id'][$curr] = $id;
                }
            }
        }
    }

    /**
     */
    protected function _getID()
    {
        if (!isset($this->_temp['id'])) {
            $this->sendID();
            /* ID is queued - force sending the queued command. */
            $this->_sendCmd($this->_pipeline());
        }

        return $this->_temp['id'];
    }

    /**
     */
    protected function _setLanguage($langs)
    {
        $cmd = $this->_command('LANGUAGE');
        foreach ($langs as $lang) {
            $cmd->add(new Horde_Imap_Client_Data_Format_Astring($lang));
        }

        if (!empty($this->_temp['lang_queue'])) {
            $this->_cmdQueue[] = $cmd;
            return array();
        }

        try {
            $this->_sendCmd($cmd);
        } catch (Horde_Imap_Client_Exception $e) {
            $this->_setInit('lang', false);
            return null;
        }

        return $this->_init['lang'];
    }

    /**
     */
    protected function _getLanguage($list)
    {
        if (!$list) {
            return empty($this->_init['lang'])
                ? null
                : $this->_init['lang'];
        }

        if (!isset($this->_init['langavail'])) {
            try {
                $this->_sendCmd($this->_command('LANGUAGE'));
            } catch (Horde_Imap_Client_Exception $e) {
                $this->_setInit('langavail', array());
            }
        }

        return $this->_init['langavail'];
    }

    /**
     * Parse a LANGUAGE response (RFC 5255 [3.3]).
     *
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseLanguage(Horde_Imap_Client_Tokenize $data)
    {
        $lang_list = $data->flushIterator();

        if (count($lang_list) === 1) {
            // This is the language that was set.
            $this->_setInit('lang', reset($lang_list));
        } else {
            // These are the languages that are available.
            $this->_setInit('langavail', $lang_list);
        }
    }

    /**
     * Enable an IMAP extension (see RFC 5161).
     *
     * @param array $exts  The extensions to enable.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _enable($exts)
    {
        if (!empty($exts) && $this->_capability('ENABLE')) {
            $c = $this->_capability();
            $todo = array();

            // Only enable non-enabled extensions.
            foreach ($exts as $val) {
                if (!$c->isEnabled($val)) {
                    $c->enable($val);
                    $todo[] = $val;
                }
            }

            if (!empty($todo)) {
                $cmd = $this->_command('ENABLE')->add($todo);
                $cmd->on_error = function() use ($todo, $c) {
                    /* Something went wrong... disable the extensions. */
                    foreach ($todo as $val) {
                        $c->enable($val, false);
                    }
                };
                $this->_cmdQueue[] = $cmd;
            }
        }
    }

    /**
     * Parse an ENABLED response (RFC 5161 [3.2]).
     *
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseEnabled(Horde_Imap_Client_Tokenize $data)
    {
        $c = $this->_capability();

        foreach ($data->flushIterator() as $val) {
            $c->enable($val);
        }
    }

    /**
     */
    protected function _openMailbox(Horde_Imap_Client_Mailbox $mailbox, $mode)
    {
        $c = $this->_capability();
        $qresync = $c->isEnabled('QRESYNC');

        $cmd = $this->_command(
            ($mode == Horde_Imap_Client::OPEN_READONLY) ? 'EXAMINE' : 'SELECT'
        )->add(
            $this->_getMboxFormatOb($mailbox)
        );
        $pipeline = $this->_pipeline($cmd);

        /* If QRESYNC is available, synchronize the mailbox. */
        if ($qresync) {
            $this->_initCache();
            $md = $this->_cache->getMetaData($mailbox, null, array(self::CACHE_MODSEQ, 'uidvalid'));

            /* CACHE_MODSEQ can be set but 0 (NOMODSEQ was returned). */
            if (!empty($md[self::CACHE_MODSEQ])) {
                if ($uids = $this->_cache->get($mailbox)) {
                    $uids = $this->getIdsOb($uids);

                    /* Check for extra long UID string. Assume that any
                     * server that can handle QRESYNC can also handle long
                     * input strings (at least 8 KB), so 7 KB is as good as
                     * any guess as to an upper limit. If this occurs, provide
                     * a range string (min -> max) instead. */
                    if (strlen($uid_str = $uids->tostring_sort) > 7000) {
                        $uid_str = $uids->range_string;
                    }
                } else {
                    $uid_str = null;
                }

                /* Several things can happen with a QRESYNC:
                 * 1. UIDVALIDITY may have changed.  If so, we need to expire
                 * the cache immediately (done below).
                 * 2. NOMODSEQ may have been returned. We can keep current
                 * message cache data but won't be able to do flag caching.
                 * 3. VANISHED/FETCH information was returned. These responses
                 * will have already been handled by those response handlers.
                 * 4. We are already synced with the local server in which
                 * case it acts like a normal EXAMINE/SELECT. */
                $cmd->add(new Horde_Imap_Client_Data_Format_List(array(
                    'QRESYNC',
                    new Horde_Imap_Client_Data_Format_List(array_filter(array(
                        $md['uidvalid'],
                        $md[self::CACHE_MODSEQ],
                        $uid_str
                    )))
                )));
            }

            /* Let the 'CLOSED' response code handle mailbox switching if
             * QRESYNC is active. */
            if ($this->_selected) {
                $pipeline->data['qresyncmbox'] = array($mailbox, $mode);
            } else {
                $this->_changeSelected($mailbox, $mode);
            }
        } else {
            if (!$c->isEnabled('CONDSTORE') &&
                $this->_initCache() &&
                $c->query('CONDSTORE')) {
                /* Activate CONDSTORE now if ENABLE is not available. */
                $cmd->add(new Horde_Imap_Client_Data_Format_List('CONDSTORE'));
                $c->enable('CONDSTORE');
            }

            $this->_changeSelected($mailbox, $mode);
        }

        try {
            $this->_sendCmd($pipeline);
        } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
            // An EXAMINE/SELECT failure with a return of 'NO' will cause the
            // current mailbox to be unselected.
            if ($e->status === Horde_Imap_Client_Interaction_Server::NO) {
                $this->_changeSelected(null);
                $this->_mode = 0;
                if (!$e->getCode()) {
                    $e = new Horde_Imap_Client_Exception(
                        Horde_Imap_Client_Translation::r("Could not open mailbox \"%s\"."),
                        Horde_Imap_Client_Exception::MAILBOX_NOOPEN
                    );
                    $e->messagePrintf(array($mailbox));
                }
            }
            throw $e;
        }

        if ($qresync) {
            /* Mailbox is fully sync'd. */
            $this->_mailboxOb()->sync = true;
        }
    }

    /**
     */
    protected function _createMailbox(Horde_Imap_Client_Mailbox $mailbox, $opts)
    {
        $cmd = $this->_command('CREATE')->add(
            $this->_getMboxFormatOb($mailbox)
        );

        // RFC 6154 Sec. 3
        if (!empty($opts['special_use'])) {
            $use = new Horde_Imap_Client_Data_Format_List('USE');
            $use->add(
                new Horde_Imap_Client_Data_Format_List($opts['special_use'])
            );
            $cmd->add($use);
        }

        // CREATE returns no untagged information (RFC 3501 [6.3.3])
        $this->_sendCmd($cmd);
    }

    /**
     */
    protected function _deleteMailbox(Horde_Imap_Client_Mailbox $mailbox)
    {
        // Some IMAP servers will not allow a delete of a currently open
        // mailbox.
        if ($mailbox->equals($this->_selected)) {
            $this->close();
        }

        $cmd = $this->_command('DELETE')->add(
            $this->_getMboxFormatOb($mailbox)
        );

        try {
            // DELETE returns no untagged information (RFC 3501 [6.3.4])
            $this->_sendCmd($cmd);
        } catch (Horde_Imap_Client_Exception $e) {
            // Some IMAP servers won't allow a mailbox delete unless all
            // messages in that mailbox are deleted.
            $this->expunge($mailbox, array(
                'delete' => true
            ));
            $this->_sendCmd($cmd);
        }
    }

    /**
     */
    protected function _renameMailbox(Horde_Imap_Client_Mailbox $old,
                                      Horde_Imap_Client_Mailbox $new)
    {
        // Some IMAP servers will not allow a rename of a currently open
        // mailbox.
        if ($old->equals($this->_selected)) {
            $this->close();
        }

        // RENAME returns no untagged information (RFC 3501 [6.3.5])
        $this->_sendCmd(
            $this->_command('RENAME')->add(array(
                $this->_getMboxFormatOb($old),
                $this->_getMboxFormatOb($new)
            ))
        );
    }

    /**
     */
    protected function _subscribeMailbox(Horde_Imap_Client_Mailbox $mailbox,
                                         $subscribe)
    {
        // SUBSCRIBE/UNSUBSCRIBE returns no untagged information (RFC 3501
        // [6.3.6 & 6.3.7])
        $this->_sendCmd(
            $this->_command(
                $subscribe ? 'SUBSCRIBE' : 'UNSUBSCRIBE'
            )->add(
                $this->_getMboxFormatOb($mailbox)
            )
        );
    }

    /**
     */
    protected function _listMailboxes($pattern, $mode, $options)
    {
        // RFC 5258 [3.1]: Use LSUB for MBOX_SUBSCRIBED if no other server
        // return options are specified.
        if (($mode == Horde_Imap_Client::MBOX_SUBSCRIBED) &&
            !array_intersect(array_keys($options), array('attributes', 'children', 'recursivematch', 'remote', 'special_use', 'status'))) {
            return $this->_getMailboxList(
                $pattern,
                Horde_Imap_Client::MBOX_SUBSCRIBED,
                array(
                    'flat' => !empty($options['flat']),
                    'no_listext' => true
                )
            );
        }

        // Get the list of subscribed/unsubscribed mailboxes. Since LSUB is
        // not guaranteed to have correct attributes, we must use LIST to
        // ensure we receive the correct information.
        if (($mode != Horde_Imap_Client::MBOX_ALL) &&
            !$this->_capability('LIST-EXTENDED')) {
            $subscribed = $this->_getMailboxList(
                $pattern,
                Horde_Imap_Client::MBOX_SUBSCRIBED,
                array('flat' => true)
            );

            // If mode is subscribed, and 'flat' option is true, we can
            // return now.
            if (($mode == Horde_Imap_Client::MBOX_SUBSCRIBED) &&
                !empty($options['flat'])) {
                return $subscribed;
            }
        } else {
            $subscribed = null;
        }

        return $this->_getMailboxList($pattern, $mode, $options, $subscribed);
    }

    /**
     * Obtain a list of mailboxes.
     *
     * @param array $pattern     The mailbox search pattern(s).
     * @param integer $mode      Which mailboxes to return.
     * @param array $options     Additional options. 'no_listext' will skip
     *                           using the LIST-EXTENDED capability.
     * @param array $subscribed  A list of subscribed mailboxes.
     *
     * @return array  See listMailboxes(().
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _getMailboxList($pattern, $mode, $options,
                                       $subscribed = null)
    {
        // Setup entry for use in _parseList().
        $pipeline = $this->_pipeline();
        $pipeline->data['mailboxlist'] = array(
            'ext' => false,
            'mode' => $mode,
            'opts' => $options,
            /* Can't use array_merge here because it will destroy any mailbox
             * name (key) that is "numeric". */
            'sub' => (is_null($subscribed) ? null : array_flip(array_map('strval', $subscribed)) + array('INBOX' => true))
        );
        $pipeline->data['listresponse'] = array();

        $cmds = array();
        $return_opts = new Horde_Imap_Client_Data_Format_List();

        if ($this->_capability('LIST-EXTENDED') &&
            empty($options['no_listext'])) {
            $cmd = $this->_command('LIST');
            $pipeline->data['mailboxlist']['ext'] = true;

            $select_opts = new Horde_Imap_Client_Data_Format_List();
            $subscribed = false;

            switch ($mode) {
            case Horde_Imap_Client::MBOX_ALL_SUBSCRIBED:
            case Horde_Imap_Client::MBOX_UNSUBSCRIBED:
                $return_opts->add('SUBSCRIBED');
                break;

            case Horde_Imap_Client::MBOX_SUBSCRIBED:
            case Horde_Imap_Client::MBOX_SUBSCRIBED_EXISTS:
                $select_opts->add('SUBSCRIBED');
                $return_opts->add('SUBSCRIBED');
                $subscribed = true;
                break;
            }

            if (!empty($options['remote'])) {
                $select_opts->add('REMOTE');
            }

            if (!empty($options['recursivematch'])) {
                $select_opts->add('RECURSIVEMATCH');
            }

            if (!empty($select_opts)) {
                $cmd->add($select_opts);
            }

            $cmd->add('');

            $tmp = new Horde_Imap_Client_Data_Format_List();
            foreach ($pattern as $val) {
                if ($subscribed && (strcasecmp($val, 'INBOX') === 0)) {
                    $cmds[] = $this->_command('LIST')->add(array(
                        '',
                        'INBOX'
                    ));
                } else {
                    $tmp->add($this->_getMboxFormatOb($val, true));
                }
            }

            if (count($tmp)) {
                $cmd->add($tmp);
                $cmds[] = $cmd;
            }

            if (!empty($options['children'])) {
                $return_opts->add('CHILDREN');
            }

            if (!empty($options['special_use'])) {
                $return_opts->add('SPECIAL-USE');
            }
        } else {
            foreach ($pattern as $val) {
                $cmds[] = $this->_command(
                    ($mode == Horde_Imap_Client::MBOX_SUBSCRIBED) ? 'LSUB' : 'LIST'
                )->add(array(
                    '',
                    $this->_getMboxFormatOb($val, true)
                ));
            }
        }

        /* LIST-STATUS does NOT depend on LIST-EXTENDED. */
        if (!empty($options['status']) &&
            $this->_capability('LIST-STATUS')) {
            $available_status = array(
                Horde_Imap_Client::STATUS_MESSAGES,
                Horde_Imap_Client::STATUS_RECENT,
                Horde_Imap_Client::STATUS_UIDNEXT,
                Horde_Imap_Client::STATUS_UIDVALIDITY,
                Horde_Imap_Client::STATUS_UNSEEN,
                Horde_Imap_Client::STATUS_HIGHESTMODSEQ
            );

            $status_opts = array();
            foreach (array_intersect($this->_statusFields, $available_status) as $key => $val) {
                if ($options['status'] & $val) {
                    $status_opts[] = $key;
                }
            }

            if (count($status_opts)) {
                $return_opts->add(array(
                    'STATUS',
                    new Horde_Imap_Client_Data_Format_List(
                        array_map('Horde_String::upper', $status_opts)
                    )
                ));
            }
        }

        foreach ($cmds as $val) {
            if (count($return_opts)) {
                $val->add(array(
                    'RETURN',
                    $return_opts
                ));
            }

            $pipeline->add($val);
        }

        try {
            $lr = $this->_sendCmd($pipeline)->data['listresponse'];
        } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
            /* Archiveopteryx 3.1.3 can't process empty list-select-opts list.
             * Retry using base IMAP4rev1 functionality. */
            if (($e->status === Horde_Imap_Client_Interaction_Server::BAD) &&
                $this->_capability('LIST-EXTENDED')) {
                $this->_capability()->remove('LIST-EXTENDED');
                return $this->_listMailboxes($pattern, $mode, $options);
            }

            throw $e;
        }

        if (!empty($options['flat'])) {
            return array_values($lr);
        }

        /* Add in STATUS return, if needed. */
        if (!empty($options['status']) && $this->_capability('LIST-STATUS')) {
            foreach ($pattern as $val) {
                $val_utf8 = Horde_Imap_Client_Utf7imap::Utf7ImapToUtf8($val);
                if (isset($lr[$val_utf8])) {
                    $lr[$val_utf8]['status'] = $this->_prepareStatusResponse($status_opts, $val_utf8);
                }
            }
        }

        return $lr;
    }

    /**
     * Parse a LIST/LSUB response (RFC 3501 [7.2.2 & 7.2.3]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response (includes
     *                                          type as first token).
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _parseList(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        $data->next();
        $attr = null;
        $attr_raw = $data->flushIterator();
        $delimiter = $data->next();
        $mbox = Horde_Imap_Client_Mailbox::get(
            $data->next(),
            !$this->_capability()->isEnabled('UTF8=ACCEPT')
        );
        $ml = $pipeline->data['mailboxlist'];

        switch ($ml['mode']) {
        case Horde_Imap_Client::MBOX_ALL_SUBSCRIBED:
        case Horde_Imap_Client::MBOX_SUBSCRIBED_EXISTS:
        case Horde_Imap_Client::MBOX_UNSUBSCRIBED:
            $attr = array_flip(array_map('Horde_String::lower', $attr_raw));

            /* Subscribed list is in UTF-8. */
            if (is_null($ml['sub']) &&
                !isset($attr['\\subscribed']) &&
                (strcasecmp($mbox, 'INBOX') === 0)) {
                $attr['\\subscribed'] = 1;
            } elseif (isset($ml['sub'][strval($mbox)])) {
                $attr['\\subscribed'] = 1;
            }
            break;
        }

        switch ($ml['mode']) {
        case Horde_Imap_Client::MBOX_SUBSCRIBED_EXISTS:
            if (isset($attr['\\nonexistent']) ||
                !isset($attr['\\subscribed'])) {
                return;
            }
            break;

        case Horde_Imap_Client::MBOX_UNSUBSCRIBED:
            if (isset($attr['\\subscribed'])) {
                return;
            }
            break;
        }

        if (!empty($ml['opts']['flat'])) {
            $pipeline->data['listresponse'][] = $mbox;
            return;
        }

        $tmp = array(
            'delimiter' => $delimiter,
            'mailbox' => $mbox
        );

        if ($attr || !empty($ml['opts']['attributes'])) {
            if (is_null($attr)) {
                $attr = array_flip(array_map('Horde_String::lower', $attr_raw));
            }

            /* RFC 5258 [3.4]: inferred attributes. */
            if ($ml['ext']) {
                if (isset($attr['\\noinferiors'])) {
                    $attr['\\hasnochildren'] = 1;
                }
                if (isset($attr['\\nonexistent'])) {
                    $attr['\\noselect'] = 1;
                }
            }
            $tmp['attributes'] = array_keys($attr);
        }

        if ($data->next() !== false) {
            $tmp['extended'] = $data->flushIterator();
        }

        $pipeline->data['listresponse'][strval($mbox)] = $tmp;
    }

    /**
     */
    protected function _status($mboxes, $flags)
    {
        $on_error = null;
        $out = $to_process = array();
        $pipeline = $this->_pipeline();
        $unseen_flags = array(
            Horde_Imap_Client::STATUS_FIRSTUNSEEN,
            Horde_Imap_Client::STATUS_UNSEEN
        );

        foreach ($mboxes as $mailbox) {
            /* If FLAGS/PERMFLAGS/UIDNOTSTICKY/FIRSTUNSEEN are needed, we must
             * do a SELECT/EXAMINE to get this information (data will be
             * caught in the code below). */
            if (($flags & Horde_Imap_Client::STATUS_FIRSTUNSEEN) ||
                ($flags & Horde_Imap_Client::STATUS_FLAGS) ||
                ($flags & Horde_Imap_Client::STATUS_PERMFLAGS) ||
                ($flags & Horde_Imap_Client::STATUS_UIDNOTSTICKY)) {
                $this->openMailbox($mailbox);
            }

            $mbox_ob = $this->_mailboxOb($mailbox);
            $data = $query = array();

            foreach ($this->_statusFields as $key => $val) {
                if (!($val & $flags)) {
                    continue;
                }

                if ($val == Horde_Imap_Client::STATUS_HIGHESTMODSEQ) {
                    $c = $this->_capability();

                    /* Don't include modseq returns if server does not support
                     * it. */
                    if (!$c->query('CONDSTORE')) {
                        continue;
                    }

                    /* Even though CONDSTORE is available, it may not yet have
                     * been enabled. */
                    $c->enable('CONDSTORE');
                    $on_error = function() use ($c) {
                        $c->enable('CONDSTORE', false);
                    };
                }

                if ($mailbox->equals($this->_selected)) {
                    if (!is_null($tmp = $mbox_ob->getStatus($val))) {
                        $data[$key] = $tmp;
                    } elseif (($val == Horde_Imap_Client::STATUS_UIDNEXT) &&
                              ($flags & Horde_Imap_Client::STATUS_UIDNEXT_FORCE)) {
                        /* UIDNEXT is not mandatory. */
                        if ($mbox_ob->getStatus(Horde_Imap_Client::STATUS_MESSAGES) == 0) {
                            $data[$key] = 0;
                        } else {
                            $fquery = new Horde_Imap_Client_Fetch_Query();
                            $fquery->uid();
                            $fetch_res = $this->fetch($this->_selected, $fquery, array(
                                'ids' => $this->getIdsOb(Horde_Imap_Client_Ids::LARGEST)
                            ));
                            $data[$key] = $fetch_res->first()->getUid() + 1;
                        }
                    } elseif (in_array($val, $unseen_flags)) {
                        /* RFC 3501 [6.3.1] - FIRSTUNSEEN information is not
                         * mandatory. If missing in EXAMINE/SELECT results, we
                         * need to do a search. An UNSEEN count also requires
                         * a search. */
                        $squery = new Horde_Imap_Client_Search_Query();
                        $squery->flag(Horde_Imap_Client::FLAG_SEEN, false);
                        $search = $this->search($mailbox, $squery, array(
                            'results' => array(
                                Horde_Imap_Client::SEARCH_RESULTS_MIN,
                                Horde_Imap_Client::SEARCH_RESULTS_COUNT
                            ),
                            'sequence' => true
                        ));

                        $mbox_ob->setStatus(Horde_Imap_Client::STATUS_FIRSTUNSEEN, $search['min']);
                        $mbox_ob->setStatus(Horde_Imap_Client::STATUS_UNSEEN, $search['count']);

                        $data[$key] = $mbox_ob->getStatus($val);
                    }
                } else {
                    $query[] = $key;
                }
            }

            $out[strval($mailbox)] = $data;

            if (count($query)) {
                $cmd = $this->_command('STATUS')->add(array(
                    $this->_getMboxFormatOb($mailbox),
                    new Horde_Imap_Client_Data_Format_List(
                        array_map('Horde_String::upper', $query)
                    )
                ));
                $cmd->on_error = $on_error;

                $pipeline->add($cmd);
                $to_process[] = array($query, $mailbox);
            }
        }

        if (count($pipeline)) {
            $this->_sendCmd($pipeline);

            foreach ($to_process as $val) {
                $out[strval($val[1])] += $this->_prepareStatusResponse($val[0], $val[1]);
            }
        }

        return $out;
    }

    /**
     * Parse a STATUS response (RFC 3501 [7.2.4]).
     *
     * @param Horde_Imap_Client_Tokenize $data  Token data
     */
    protected function _parseStatus(Horde_Imap_Client_Tokenize $data)
    {
        // Mailbox name is in UTF7-IMAP (unless UTF8 has been enabled).
        $mbox_ob = $this->_mailboxOb(
            Horde_Imap_Client_Mailbox::get(
                $data->next(),
                !$this->_capability()->isEnabled('UTF8=ACCEPT')
            )
        );

        $data->next();

        while (($k = $data->next()) !== false) {
            $mbox_ob->setStatus(
                $this->_statusFields[Horde_String::lower($k)],
                $data->next()
            );
        }
    }

    /**
     * Prepares a status response for a mailbox.
     *
     * @param array $request   The status keys to return.
     * @param string $mailbox  The mailbox to query.
     */
    protected function _prepareStatusResponse($request, $mailbox)
    {
        $mbox_ob = $this->_mailboxOb($mailbox);
        $out = array();

        foreach ($request as $val) {
            $out[$val] = $mbox_ob->getStatus($this->_statusFields[$val]);
        }

        return $out;
    }

    /**
     */
    protected function _append(Horde_Imap_Client_Mailbox $mailbox, $data,
                               $options)
    {
        $c = $this->_capability();

        // Check for MULTIAPPEND extension (RFC 3502)
        if ((count($data) > 1) && !$c->query('MULTIAPPEND')) {
            $result = $this->getIdsOb();
            foreach (array_keys($data) as $key) {
                $res = $this->_append($mailbox, array($data[$key]), $options);
                if (($res === true) || ($result === true)) {
                    $result = true;
                } else {
                    $result->add($res);
                }
            }
            return $result;
        }

        // Check for extensions.
        $binary = $c->query('BINARY');
        $catenate = $c->query('CATENATE');
        $utf8 = $c->isEnabled('UTF8=ACCEPT');

        $asize = 0;

        $cmd = $this->_command('APPEND')->add(
            $this->_getMboxFormatOb($mailbox)
        );
        $cmd->literal8 = true;

        foreach (array_keys($data) as $key) {
            if (!empty($data[$key]['flags'])) {
                $tmp = new Horde_Imap_Client_Data_Format_List();
                foreach ($data[$key]['flags'] as $val) {
                    /* Ignore recent flag. RFC 3501 [9]: flag definition */
                    if (strcasecmp($val, Horde_Imap_Client::FLAG_RECENT) !== 0) {
                        $tmp->add($val);
                    }
                }
                $cmd->add($tmp);
            }

            if (!empty($data[$key]['internaldate'])) {
                $cmd->add(new Horde_Imap_Client_Data_Format_DateTime($data[$key]['internaldate']));
            }

            $adata = null;

            if (is_array($data[$key]['data'])) {
                if ($catenate) {
                    $cmd->add('CATENATE');
                    $tmp = new Horde_Imap_Client_Data_Format_List();
                } else {
                    $data_stream = new Horde_Stream_Temp();
                }

                foreach ($data[$key]['data'] as $v) {
                    switch ($v['t']) {
                    case 'text':
                        if ($catenate) {
                            $tdata = $this->_appendData($v['v'], $asize);
                            if ($utf8) {
                                /* RFC 6855 [4]: CATENATE UTF8 extension. */
                                $tdata->forceBinary();
                                $tmp->add(array(
                                    'UTF8',
                                    new Horde_Imap_Client_Data_Format_List($tdata)
                                ));
                            } else {
                                $tmp->add(array(
                                    'TEXT',
                                    $tdata
                                ));
                            }
                        } else {
                            if (is_resource($v['v'])) {
                                rewind($v['v']);
                            }
                            $data_stream->add($v['v']);
                        }
                        break;

                    case 'url':
                        if ($catenate) {
                            $tmp->add(array(
                                'URL',
                                new Horde_Imap_Client_Data_Format_Astring($v['v'])
                            ));
                        } else {
                            $data_stream->add($this->_convertCatenateUrl($v['v']));
                        }
                        break;
                    }
                }

                if ($catenate) {
                    $cmd->add($tmp);
                } else {
                    $adata = $this->_appendData($data_stream->stream, $asize);
                }
            } else {
                $adata = $this->_appendData($data[$key]['data'], $asize);
            }

            if (!is_null($adata)) {
                if ($utf8) {
                    /* RFC 6855 [4]: APPEND UTF8 extension. */
                    $adata->forceBinary();
                    $cmd->add(array(
                        'UTF8',
                        new Horde_Imap_Client_Data_Format_List($adata)
                    ));
                } else {
                    $cmd->add($adata);
                }
            }
        }

        /* Although it is normally more efficient to use LITERAL+, disable if
         * payload is over 50 KB because it allows the server to throw error
         * before we potentially push a lot of data to server that would
         * otherwise be ignored (see RFC 4549 [4.2.2.3]).
         * Additionally, since so many IMAP servers have issues with APPEND
         * + BINARY, don't use LITERAL+ since servers may send BAD
         * (incorrectly) after initial command. */
        $cmd->literalplus = (($asize < (1024 * 50)) && !$binary);

        // If the mailbox is currently selected read-only, we need to close
        // because some IMAP implementations won't allow an append. And some
        // implementations don't support append on ANY open mailbox. Be safe
        // and always make sure we are in a non-selected state.
        $this->close();

        try {
            $resp = $this->_sendCmd($cmd);
        } catch (Horde_Imap_Client_Exception $e) {
            switch ($e->getCode()) {
            case $e::CATENATE_BADURL:
            case $e::CATENATE_TOOBIG:
                /* Cyrus 2.4 (at least as of .14) has a broken CATENATE (see
                 * Bug #11111). Regardless, if CATENATE is broken, we can try
                 * to fallback to APPEND. */
                $c->remove('CATENATE');
                return $this->_append($mailbox, $data, $options);

            case $e::DISCONNECT:
                /* Workaround broken literal8 on Cyrus. */
                if ($binary) {
                    // Need to re-login first before removing capability.
                    $this->login();
                    $c->remove('BINARY');
                    return $this->_append($mailbox, $data, $options);
                }
                break;
            }

            if (!empty($options['create']) &&
                !empty($e->resp_data['trycreate'])) {
                $this->createMailbox($mailbox);
                unset($options['create']);
                return $this->_append($mailbox, $data, $options);
            }

            /* RFC 3516/4466 says we should be able to append binary data
             * using literal8 "~{#} format", but it doesn't seem to work on
             * all servers tried (UW-IMAP/Cyrus). Do a last-ditch check for
             * broken BINARY and attempt to fix here. */
            if ($c->query('BINARY') &&
                ($e instanceof Horde_Imap_Client_Exception_ServerResponse)) {
                switch ($e->status) {
                case Horde_Imap_Client_Interaction_Server::BAD:
                case Horde_Imap_Client_Interaction_Server::NO:
                    $c->remove('BINARY');
                    return $this->_append($mailbox, $data, $options);
                }
            }

            throw $e;
        }

        /* If we reach this point and have data in 'appenduid', UIDPLUS (RFC
         * 4315) has done the dirty work for us. */
        return isset($resp->data['appenduid'])
            ? $resp->data['appenduid']
            : true;
    }

    /**
     * Prepares append message data for insertion into the IMAP command
     * string.
     *
     * @param mixed $data      Either a resource or a string.
     * @param integer &$asize  Total append size.
     *
     * @return Horde_Imap_Client_Data_Format_String_Nonascii  The data object.
     */
    protected function _appendData($data, &$asize)
    {
        if (is_resource($data)) {
            rewind($data);
        }

        /* Since this is body text, with possible embedded charset
         * information, non-ASCII characters are supported. */
        $ob = new Horde_Imap_Client_Data_Format_String_Nonascii($data, array(
            'eol' => true,
            'skipscan' => true
        ));

        // APPEND data MUST be sent in a literal (RFC 3501 [6.3.11]).
        $ob->forceLiteral();

        $asize += $ob->length();

        return $ob;
    }

    /**
     * Converts a CATENATE URL to stream data.
     *
     * @param string $url  The CATENATE URL.
     *
     * @return resource  A stream containing the data.
     */
    protected function _convertCatenateUrl($url)
    {
        $e = $part = null;
        $url = new Horde_Imap_Client_Url_Imap($url);

        if (!is_null($url->mailbox) && !is_null($url->uid)) {
            try {
                $status_res = is_null($url->uidvalidity)
                    ? null
                    : $this->status($url->mailbox, Horde_Imap_Client::STATUS_UIDVALIDITY);

                if (is_null($status_res) ||
                    ($status_res['uidvalidity'] == $url->uidvalidity)) {
                    if (!isset($this->_temp['catenate_ob'])) {
                        $this->_temp['catenate_ob'] = new Horde_Imap_Client_Socket_Catenate($this);
                    }
                    $part = $this->_temp['catenate_ob']->fetchFromUrl($url);
                }
            } catch (Horde_Imap_Client_Exception $e) {}
        }

        if (is_null($part)) {
            $message = 'Bad IMAP URL given in CATENATE data: ' . strval($url);
            if ($e) {
                $message .= ' ' . $e->getMessage();
            }

            throw new InvalidArgumentException($message);
        }

        return $part;
    }

    /**
     */
    protected function _check()
    {
        // CHECK returns no untagged information (RFC 3501 [6.4.1])
        $this->_sendCmd($this->_command('CHECK'));
    }

    /**
     */
    protected function _close($options)
    {
        if (empty($options['expunge'])) {
            if ($this->_capability('UNSELECT')) {
                // RFC 3691 defines 'UNSELECT' for precisely this purpose
                $this->_sendCmd($this->_command('UNSELECT'));
            } else {
                /* RFC 3501 [6.4.2]: to close a mailbox without expunge,
                 * select a non-existent mailbox. */
                try {
                    $this->_sendCmd($this->_command('EXAMINE')->add(
                        $this->_getMboxFormatOb("\24nonexist\24")
                    ));

                    /* Not pipelining, since the odds that this CLOSE is even
                     * needed is tiny; and it returns BAD, which should be
                     * avoided, if possible. */
                    $this->_sendCmd($this->_command('CLOSE'));
                } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
                    // Ignore error; it is expected.
                }
            }
        } else {
            // If caching, we need to know the UIDs being deleted, so call
            // expunge() before calling close().
            if ($this->_initCache(true)) {
                $this->expunge($this->_selected);
            }

            // CLOSE returns no untagged information (RFC 3501 [6.4.2])
            $this->_sendCmd($this->_command('CLOSE'));
        }
    }

    /**
     */
    protected function _expunge($options)
    {
        $expunged_ob = $modseq = null;
        $ids = $options['ids'];
        $list_msgs = !empty($options['list']);
        $mailbox = $this->_selected;
        $uidplus = $this->_capability('UIDPLUS');
        $unflag = array();
        $use_cache = $this->_initCache(true);

        if ($ids->all) {
            if (!$uidplus || $list_msgs || $use_cache) {
                $ids = $this->resolveIds($mailbox, $ids, 2);
            }
        } elseif ($uidplus) {
            /* If QRESYNC is not available, and we are returning the list of
             * expunged messages (or we are caching), we have to make sure we
             * have a mapping of Sequence -> UIDs. If we have QRESYNC, the
             * server SHOULD return a VANISHED response with UIDs. However,
             * even if the server returns EXPUNGEs instead, we can use
             * vanished() to grab the list. */
            unset($this->_temp['search_save']);
            if ($this->_capability()->isEnabled('QRESYNC')) {
                $ids = $this->resolveIds($mailbox, $ids, 1);
                if ($list_msgs) {
                    $modseq = $this->_mailboxOb()->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ);
                }
            } else {
                $ids = $this->resolveIds($mailbox, $ids, ($list_msgs || $use_cache) ? 2 : 1);
            }
            if (!empty($this->_temp['search_save'])) {
                $ids = $this->getIdsOb(Horde_Imap_Client_Ids::SEARCH_RES);
            }
        } else {
            /* Without UIDPLUS, need to temporarily unflag all messages marked
             * as deleted but not a part of requested IDs to delete. Use NOT
             * searches to accomplish this goal. */
            $squery = new Horde_Imap_Client_Search_Query();
            $squery->flag(Horde_Imap_Client::FLAG_DELETED, true);
            $squery->ids($ids, true);

            $s_res = $this->search($mailbox, $squery, array(
                'results' => array(
                    Horde_Imap_Client::SEARCH_RESULTS_MATCH,
                    Horde_Imap_Client::SEARCH_RESULTS_SAVE
                )
            ));

            $this->store($mailbox, array(
                'ids' => empty($s_res['save']) ? $s_res['match'] : $this->getIdsOb(Horde_Imap_Client_Ids::SEARCH_RES),
                'remove' => array(Horde_Imap_Client::FLAG_DELETED)
            ));

            $unflag = $s_res['match'];
        }

        if ($list_msgs) {
            $expunged_ob = $this->getIdsOb();
            $this->_temp['expunged'] = $expunged_ob;
        }

        /* Always use UID EXPUNGE if available. */
        if ($uidplus) {
            /* We can only pipeline STORE w/ EXPUNGE if using UIDs and UIDPLUS
             * is available. */
            if (empty($options['delete'])) {
                $pipeline = $this->_pipeline();
            } else {
                $pipeline = $this->_storeCmd(array(
                    'add' => array(
                        Horde_Imap_Client::FLAG_DELETED
                    ),
                    'ids' => $ids
                ));
            }

            foreach ($ids->split(2000) as $val) {
                $pipeline->add(
                    $this->_command('UID EXPUNGE')->add($val)
                );
            }

            $resp = $this->_sendCmd($pipeline);
        } else {
            if (!empty($options['delete'])) {
                $this->store($mailbox, array(
                    'add' => array(Horde_Imap_Client::FLAG_DELETED),
                    'ids' => $ids
                ));
            }

            if ($use_cache || $list_msgs) {
                $this->_sendCmd($this->_command('EXPUNGE'));
            } else {
                /* This is faster than an EXPUNGE because the server will not
                 * return untagged EXPUNGE responses. We can only do this if
                 * we are not updating cache information. */
                $this->close(array('expunge' => true));
            }
        }

        unset($this->_temp['expunged']);

        if (!empty($unflag)) {
            $this->store($mailbox, array(
                'add' => array(Horde_Imap_Client::FLAG_DELETED),
                'ids' => $unflag
            ));
        }

        if (!is_null($modseq) && !empty($resp->data['expunge_seen'])) {
            /* There's a chance we actually did a full map of sequence -> UID,
             * but this code should never be reached in the first place so
             * be ultra-safe and just do a full VANISHED search. */
            $expunged_ob = $this->vanished($mailbox, $modseq, array(
                'ids' => $ids
            ));
            $this->_deleteMsgs($mailbox, $expunged_ob, array(
                'pipeline' => $resp
            ));
        }

        return $expunged_ob;
    }

    /**
     * Parse a VANISHED response (RFC 7162 [3.2.10]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The response data.
     */
    protected function _parseVanished(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        /* There are two forms of VANISHED.  VANISHED (EARLIER) will be sent
         * in a FETCH (VANISHED) or SELECT/EXAMINE (QRESYNC) call.
         * If this is the case, we can go ahead and update the cache
         * immediately (we know we are caching or else QRESYNC would not be
         * enabled). HIGHESTMODSEQ information will be updated via the tagged
         * response. */
        if (($curr = $data->next()) === true) {
            if (Horde_String::upper($data->next()) === 'EARLIER') {
                /* Caching is guaranteed to be active if we are using
                 * QRESYNC. */
                $data->next();
                $vanished = $this->getIdsOb($data->next());
                if (isset($pipeline->data['vanished'])) {
                    $pipeline->data['vanished']->add($vanished);
                } else {
                    $this->_deleteMsgs($this->_selected, $vanished, array(
                        'pipeline' => $pipeline
                    ));
                }
            }
        } else {
            /* The second form is just VANISHED. This is analogous to EXPUNGE
             * and requires the message count to decrement. */
            $this->_deleteMsgs($this->_selected, $this->getIdsOb($curr), array(
                'decrement' => true,
                'pipeline' => $pipeline
            ));
        }
    }

    /**
     * Search a mailbox.  This driver supports all IMAP4rev1 search criteria
     * as defined in RFC 3501.
     */
    protected function _search($query, $options)
    {
        $sort_criteria = array(
            Horde_Imap_Client::SORT_ARRIVAL => 'ARRIVAL',
            Horde_Imap_Client::SORT_CC => 'CC',
            Horde_Imap_Client::SORT_DATE => 'DATE',
            Horde_Imap_Client::SORT_DISPLAYFROM => 'DISPLAYFROM',
            Horde_Imap_Client::SORT_DISPLAYTO => 'DISPLAYTO',
            Horde_Imap_Client::SORT_FROM => 'FROM',
            Horde_Imap_Client::SORT_REVERSE => 'REVERSE',
            Horde_Imap_Client::SORT_RELEVANCY => 'RELEVANCY',
            // This is a bogus entry to allow the sort options check to
            // correctly work below.
            Horde_Imap_Client::SORT_SEQUENCE => 'SEQUENCE',
            Horde_Imap_Client::SORT_SIZE => 'SIZE',
            Horde_Imap_Client::SORT_SUBJECT => 'SUBJECT',
            Horde_Imap_Client::SORT_TO => 'TO'
        );

        $results_criteria = array(
            Horde_Imap_Client::SEARCH_RESULTS_COUNT => 'COUNT',
            Horde_Imap_Client::SEARCH_RESULTS_MATCH => 'ALL',
            Horde_Imap_Client::SEARCH_RESULTS_MAX => 'MAX',
            Horde_Imap_Client::SEARCH_RESULTS_MIN => 'MIN',
            Horde_Imap_Client::SEARCH_RESULTS_RELEVANCY => 'RELEVANCY',
            Horde_Imap_Client::SEARCH_RESULTS_SAVE => 'SAVE'
        );

        // Check if the server supports sorting (RFC 5256).
        $esearch = $return_sort = $server_seq_sort = $server_sort = false;
        if (!empty($options['sort'])) {
            /* Make sure sort options are correct. If not, default to no
             * sort. */
            if (count(array_intersect($options['sort'], array_keys($sort_criteria))) === 0) {
                unset($options['sort']);
            } else {
                $return_sort = true;

                if ($this->_capability('SORT')) {
                    /* Make sure server supports DISPLAYFROM & DISPLAYTO. */
                    $server_sort =
                        !array_intersect($options['sort'], array(Horde_Imap_Client::SORT_DISPLAYFROM, Horde_Imap_Client::SORT_DISPLAYTO)) ||
                        $this->_capability('SORT', 'DISPLAY');
                }

                /* If doing a sequence sort, need to do this on the client
                 * side. */
                if ($server_sort &&
                    in_array(Horde_Imap_Client::SORT_SEQUENCE, $options['sort'])) {
                    $server_sort = false;

                    /* Optimization: If doing only a sequence sort, just do a
                     * simple search and sort UIDs/sequences on client side. */
                    switch (count($options['sort'])) {
                    case 1:
                        $server_seq_sort = true;
                        break;

                    case 2:
                        $server_seq_sort = (reset($options['sort']) == Horde_Imap_Client::SORT_REVERSE);
                        break;
                    }
                }
            }
        }

        $charset = is_null($options['_query']['charset'])
            ? 'US-ASCII'
            : $options['_query']['charset'];
        $partial = false;

        if ($server_sort) {
            $cmd = $this->_command(
                empty($options['sequence']) ? 'UID SORT' : 'SORT'
            );
            $results = array();

            // Use ESEARCH (RFC 4466) response if server supports.
            $esearch = false;

            // Check for ESORT capability (RFC 5267)
            if ($this->_capability('ESORT')) {
                foreach ($options['results'] as $val) {
                    if (isset($results_criteria[$val]) &&
                        ($val != Horde_Imap_Client::SEARCH_RESULTS_SAVE)) {
                        $results[] = $results_criteria[$val];
                    }
                }
                $esearch = true;
            }

            // Add PARTIAL limiting (RFC 5267 [4.4])
            if ((!$esearch || !empty($options['partial'])) &&
                $this->_capability('CONTEXT', 'SORT')) {
                /* RFC 5267 indicates RFC 4466 ESEARCH-like support,
                 * notwithstanding "real" RFC 4731 support. */
                $esearch = true;

                if (!empty($options['partial'])) {
                    /* Can't have both ALL and PARTIAL returns. */
                    $results = array_diff($results, array('ALL'));

                    $results[] = 'PARTIAL';
                    $results[] = $options['partial'];
                    $partial = true;
                }
            }

            if ($esearch && empty($this->_init['noesearch'])) {
                $cmd->add(array(
                    'RETURN',
                    new Horde_Imap_Client_Data_Format_List($results)
                ));
            }

            $tmp = new Horde_Imap_Client_Data_Format_List();
            foreach ($options['sort'] as $val) {
                if (isset($sort_criteria[$val])) {
                    $tmp->add($sort_criteria[$val]);
                }
            }
            $cmd->add($tmp);

            /* Charset is mandatory for SORT (RFC 5256 [3]).
             * However, if UTF-8 support is activated, a client MUST NOT
             * send the charset specification (RFC 6855 [3]; Errata 4029). */
            if (!$this->_capability()->isEnabled('UTF8=ACCEPT')) {
                $cmd->add($charset);
            }
        } else {
            $cmd = $this->_command(
                empty($options['sequence']) ? 'UID SEARCH' : 'SEARCH'
            );
            $esearch = false;
            $results = array();

            // Check if the server supports ESEARCH (RFC 4731).
            if ($this->_capability('ESEARCH')) {
                foreach ($options['results'] as $val) {
                    if (isset($results_criteria[$val])) {
                        $results[] = $results_criteria[$val];
                    }
                }
                $esearch = true;
            }

            // Add PARTIAL limiting (RFC 5267 [4.4]).
            if ((!$esearch || !empty($options['partial'])) &&
                $this->_capability('CONTEXT', 'SEARCH')) {
                /* RFC 5267 indicates RFC 4466 ESEARCH-like support,
                 * notwithstanding "real" RFC 4731 support. */
                $esearch = true;

                if (!empty($options['partial'])) {
                    // Can't have both ALL and PARTIAL returns.
                    $results = array_diff($results, array('ALL'));

                    $results[] = 'PARTIAL';
                    $results[] = $options['partial'];
                    $partial = true;
                }
            }

            if ($esearch && empty($this->_init['noesearch'])) {
                // Always use ESEARCH if available because it returns results
                // in a more compact sequence-set list
                $cmd->add(array(
                    'RETURN',
                    new Horde_Imap_Client_Data_Format_List($results)
                ));
            }

            /* Charset is optional for SEARCH (RFC 3501 [6.4.4]).
             * If UTF-8 support is activated, a client MUST NOT
             * send the charset specification (RFC 6855 [3]; Errata 4029). */
            if (($charset != 'US-ASCII') &&
                !$this->_capability()->isEnabled('UTF8=ACCEPT')) {
                $cmd->add(array(
                    'CHARSET',
                    $options['_query']['charset']
                ));
            }
        }

        $cmd->add($options['_query']['query'], true);

        $pipeline = $this->_pipeline($cmd);
        $pipeline->data['esearchresp'] = array();
        $er = &$pipeline->data['esearchresp'];
        $pipeline->data['searchresp'] = $this->getIdsOb(array(), !empty($options['sequence']));
        $sr = &$pipeline->data['searchresp'];

        try {
            $resp = $this->_sendCmd($pipeline);
        } catch (Horde_Imap_Client_Exception $e) {
            if (($e instanceof Horde_Imap_Client_Exception_ServerResponse) &&
                ($e->status === Horde_Imap_Client_Interaction_Server::NO) &&
                ($charset != 'US-ASCII')) {
                /* RFC 3501 [6.4.4]: BADCHARSET response code is only a
                 * SHOULD return. If it doesn't exist, need to check for
                 * command status of 'NO'. List of supported charsets in
                 * the BADCHARSET response has already been parsed and stored
                 * at this point. */
                $this->search_charset->setValid($charset, false);
                $e->setCode(Horde_Imap_Client_Exception::BADCHARSET);
            }

            if (empty($this->_temp['search_retry'])) {
                $this->_temp['search_retry'] = true;

                /* Bug #9842: Workaround broken Cyrus servers (as of
                 * 2.4.7). */
                if ($esearch && ($charset != 'US-ASCII')) {
                    $this->_capability()->remove('ESEARCH');
                    $this->_setInit('noesearch', true);

                    try {
                        return $this->_search($query, $options);
                    } catch (Horde_Imap_Client_Exception $e) {}
                }

                /* Try to convert charset. */
                if (($e->getCode() === Horde_Imap_Client_Exception::BADCHARSET) &&
                    ($charset != 'US-ASCII')) {
                    foreach ($this->search_charset->charsets as $val) {
                        $this->_temp['search_retry'] = 1;
                        $new_query = clone($query);
                        try {
                            $new_query->charset($val);
                            $options['_query'] = $new_query->build($this);
                            return $this->_search($new_query, $options);
                        } catch (Horde_Imap_Client_Exception $e) {}
                    }
                }

                unset($this->_temp['search_retry']);
            }

            throw $e;
        }

        if ($return_sort && !$server_sort) {
            if ($server_seq_sort) {
                $sr->sort();
                if (reset($options['sort']) == Horde_Imap_Client::SORT_REVERSE) {
                    $sr->reverse();
                }
            } else {
                if (!isset($this->_temp['clientsort'])) {
                    $this->_temp['clientsort'] = new Horde_Imap_Client_Socket_ClientSort($this);
                }
                $sr = $this->getIdsOb($this->_temp['clientsort']->clientSort($sr, $options), !empty($options['sequence']));
            }
        }

        if (!$partial && !empty($options['partial'])) {
            $partial = $this->getIdsOb($options['partial'], true);
            $min = $partial->min - 1;

            $sr = $this->getIdsOb(
                array_slice($sr->ids, $min, $partial->max - $min),
                !empty($options['sequence'])
            );
        }

        $ret = array();
        foreach ($options['results'] as $val) {
            switch ($val) {
            case Horde_Imap_Client::SEARCH_RESULTS_COUNT:
                $ret['count'] = ($esearch && !$partial)
                    ? $er['count']
                    : count($sr);
                break;

            case Horde_Imap_Client::SEARCH_RESULTS_MATCH:
                $ret['match'] = $sr;
                break;

            case Horde_Imap_Client::SEARCH_RESULTS_MAX:
                $ret['max'] = $esearch
                    ? (!$partial && isset($er['max']) ? $er['max'] : null)
                    : (count($sr) ? max($sr->ids) : null);
                break;

            case Horde_Imap_Client::SEARCH_RESULTS_MIN:
                $ret['min'] = $esearch
                    ? (!$partial && isset($er['min']) ? $er['min'] : null)
                    : (count($sr) ? min($sr->ids) : null);
                break;

            case Horde_Imap_Client::SEARCH_RESULTS_RELEVANCY:
                $ret['relevancy'] = ($esearch && isset($er['relevancy'])) ? $er['relevancy'] : array();
                break;

            case Horde_Imap_Client::SEARCH_RESULTS_SAVE:
                $this->_temp['search_save'] = $ret['save'] = $esearch ? empty($resp->data['searchnotsaved']) : false;
                break;
            }
        }

        // Add modseq data, if needed.
        if (!empty($er['modseq'])) {
            $ret['modseq'] = $er['modseq'];
        }

        unset($this->_temp['search_retry']);

        /* Check for EXPUNGEISSUED (RFC 2180 [4.3]/RFC 5530 [3]). */
        if (!empty($resp->data['expungeissued'])) {
            $this->noop();
        }

        return $ret;
    }

    /**
     * Parse a SEARCH/SORT response (RFC 3501 [7.2.5]; RFC 4466 [3];
     * RFC 5256 [4]; RFC 5267 [3]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param array $data  A list of IDs (message sequence numbers or UIDs).
     */
    protected function _parseSearch(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        $data
    )
    {
        /* More than one search response may be sent. */
        $pipeline->data['searchresp']->add($data);
    }

    /**
     * Parse an ESEARCH response (RFC 4466 [2.6.2])
     * Format: (TAG "a567") UID COUNT 5 ALL 4:19,21,28
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseEsearch(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        // Ignore search correlator information
        if ($data->next() === true) {
            $data->flushIterator(false);
        }

        // Ignore UID tag
        $current = $data->next();
        if (Horde_String::upper($current) === 'UID') {
            $current = $data->next();
        }

        do {
            $val = $data->next();
            $tag = Horde_String::upper($current);

            switch ($tag) {
            case 'ALL':
                $this->_parseSearch($pipeline, $val);
                break;

            case 'COUNT':
            case 'MAX':
            case 'MIN':
            case 'MODSEQ':
            case 'RELEVANCY':
                $pipeline->data['esearchresp'][Horde_String::lower($tag)] = $val;
                break;

            case 'PARTIAL':
                // RFC 5267 [4.4]
                $partial = $val->flushIterator();
                $this->_parseSearch($pipeline, end($partial));
                break;
            }
        } while (($current = $data->next()) !== false);
    }

    /**
     */
    protected function _setComparator($comparator)
    {
        $cmd = $this->_command('COMPARATOR');
        foreach ($comparator as $val) {
            $cmd->add(new Horde_Imap_Client_Data_Format_Astring($val));
        }
        $this->_sendCmd($cmd);
    }

    /**
     */
    protected function _getComparator()
    {
        $resp = $this->_sendCmd($this->_command('COMPARATOR'));

        return isset($resp->data['comparator'])
            ? $resp->data['comparator']
            : null;
    }

    /**
     * Parse a COMPARATOR response (RFC 5255 [4.8])
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseComparator(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        $data
    )
    {
        $pipeline->data['comparator'] = $data->next();
        // Ignore optional matching comparator list
    }

    /**
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    protected function _thread($options)
    {
        $thread_criteria = array(
            Horde_Imap_Client::THREAD_ORDEREDSUBJECT => 'ORDEREDSUBJECT',
            Horde_Imap_Client::THREAD_REFERENCES => 'REFERENCES',
            Horde_Imap_Client::THREAD_REFS => 'REFS'
        );

        $tsort = (isset($options['criteria']))
            ? (is_string($options['criteria']) ? Horde_String::upper($options['criteria']) : $thread_criteria[$options['criteria']])
            : 'ORDEREDSUBJECT';

        if (!$this->_capability('THREAD', $tsort)) {
            switch ($tsort) {
            case 'ORDEREDSUBJECT':
                if (empty($options['search'])) {
                    $ids = $this->getIdsOb(Horde_Imap_Client_Ids::ALL, !empty($options['sequence']));
                } else {
                    $search_res = $this->search($this->_selected, $options['search'], array('sequence' => !empty($options['sequence'])));
                    $ids = $search_res['match'];
                }

                /* Do client-side ORDEREDSUBJECT threading. */
                $query = new Horde_Imap_Client_Fetch_Query();
                $query->envelope();
                $query->imapDate();

                $fetch_res = $this->fetch($this->_selected, $query, array(
                    'ids' => $ids
                ));

                if (!isset($this->_temp['clientsort'])) {
                    $this->_temp['clientsort'] = new Horde_Imap_Client_Socket_ClientSort($this);
                }
                return $this->_temp['clientsort']->threadOrderedSubject($fetch_res, empty($options['sequence']));

            case 'REFERENCES':
            case 'REFS':
                throw new Horde_Imap_Client_Exception_NoSupportExtension(
                    'THREAD',
                    sprintf('Server does not support "%s" thread sort.', $tsort)
                );
            }
        }

        $cmd = $this->_command(
            empty($options['sequence']) ? 'UID THREAD' : 'THREAD'
        )->add($tsort);

        /* If UTF-8 support is activated, a client MUST NOT
         * send the charset specification (RFC 6855 [3]; Errata 4029). */
        if (empty($options['search'])) {
            if (!$this->_capability()->isEnabled('UTF8=ACCEPT')) {
                $cmd->add('US-ASCII');
            }
            $cmd->add('ALL');
        } else {
            $search_query = $options['search']->build();
            if (!$this->_capability()->isEnabled('UTF8=ACCEPT')) {
                $cmd->add(is_null($search_query['charset']) ? 'US-ASCII' : $search_query['charset']);
            }
            $cmd->add($search_query['query'], true);
        }

        return new Horde_Imap_Client_Data_Thread(
            $this->_sendCmd($cmd)->data['threadparse'],
            empty($options['sequence']) ? 'uid' : 'sequence'
        );
    }

    /**
     * Parse a THREAD response (RFC 5256 [4]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  Thread data.
     */
    protected function _parseThread(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        $out = array();

        while ($data->next() !== false) {
            $thread = array();
            $this->_parseThreadLevel($thread, $data);
            $out[] = $thread;
        }

        $pipeline->data['threadparse'] = $out;
    }

    /**
     * Parse a level of a THREAD response (RFC 5256 [4]).
     *
     * @param array $thread                     Results.
     * @param Horde_Imap_Client_Tokenize $data  Thread data.
     * @param integer $level                    The current tree level.
     */
    protected function _parseThreadLevel(&$thread,
                                         Horde_Imap_Client_Tokenize $data,
                                         $level = 0)
    {
        while (($curr = $data->next()) !== false) {
            if ($curr === true) {
                $this->_parseThreadLevel($thread, $data, $level);
            } elseif (!is_bool($curr)) {
                $thread[$curr] = $level++;
            }
        }
    }

    /**
     */
    protected function _fetch(Horde_Imap_Client_Fetch_Results $results,
                              $queries)
    {
        $pipeline = $this->_pipeline();
        $pipeline->data['fetch_lookup'] = array();
        $pipeline->data['fetch_followup'] = array();

        foreach ($queries as $options) {
            $this->_fetchCmd($pipeline, $options);
            $sequence = $options['ids']->sequence;
        }

        try {
            $resp = $this->_sendCmd($pipeline);

            /* Check for EXPUNGEISSUED (RFC 2180 [4.1]/RFC 5530 [3]). */
            if (!empty($resp->data['expungeissued'])) {
                $this->noop();
            }

            foreach ($resp->fetch as $k => $v) {
                $results->get($sequence ? $k : $v->getUid())->merge($v);
            }
        } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
            if ($e->status === Horde_Imap_Client_Interaction_Server::NO) {
                if ($e->getCode() === $e::UNKNOWNCTE) {
                    /* UNKNOWN-CTE error. Redo the query without the BINARY
                     * elements. */
                    $bq = $pipeline->data['binaryquery'];

                    foreach ($queries as $val) {
                        foreach ($bq as $key2 => $val2) {
                            unset($val2['decode']);
                            $val['_query']->bodyPart($key2, $val2);
                            $val['_query']->remove(Horde_Imap_Client::FETCH_BODYPARTSIZE, $key2);
                        }
                        $pipeline->data['fetch_followup'][] = $val;
                    }
                } elseif ($sequence) {
                    /* A NO response, when coupled with a sequence FETCH, most
                     * likely means that messages were expunged. (RFC 2180
                     * [4.1]) */
                    $this->noop();
                }
            }
        } catch (Exception $e) {
            // For any other error, ignore the Exception - fetch() is nice in
            // that the return value explicitly handles missing data for any
            // given message.
        }

        if (!empty($pipeline->data['fetch_followup'])) {
            $this->_fetch($results, $pipeline->data['fetch_followup']);
        }
    }

    /**
     * Add a FETCH command to the given pipeline.
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param array $options                                    Fetch query
     *                                                          options
     */
    protected function _fetchCmd(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        $options
    )
    {
        $fetch = new Horde_Imap_Client_Data_Format_List();
        $sequence = $options['ids']->sequence;

        /* Build an IMAP4rev1 compliant FETCH query. We handle the following
         * criteria:
         *   BINARY[.PEEK][<section #>]<<partial>> (RFC 3516)
         *     see BODY[] response
         *   BINARY.SIZE[<section #>] (RFC 3516)
         *   BODY[.PEEK][<section>]<<partial>>
         *     <section> = HEADER, HEADER.FIELDS, HEADER.FIELDS.NOT, MIME,
         *                 TEXT, empty
         *     <<partial>> = 0.# (# of bytes)
         *   BODYSTRUCTURE
         *   ENVELOPE
         *   FLAGS
         *   INTERNALDATE
         *   MODSEQ (RFC 7162)
         *   RFC822.SIZE
         *   UID
         *
         * No need to support these (can be built from other queries):
         * ===========================================================
         *   ALL macro => (FLAGS INTERNALDATE RFC822.SIZE ENVELOPE)
         *   BODY => Use BODYSTRUCTURE instead
         *   FAST macro => (FLAGS INTERNALDATE RFC822.SIZE)
         *   FULL macro => (FLAGS INTERNALDATE RFC822.SIZE ENVELOPE BODY)
         *   RFC822 => BODY[]
         *   RFC822.HEADER => BODY[HEADER]
         *   RFC822.TEXT => BODY[TEXT]
         */

        foreach ($options['_query'] as $type => $c_val) {
            switch ($type) {
            case Horde_Imap_Client::FETCH_STRUCTURE:
                $fetch->add('BODYSTRUCTURE');
                break;

            case Horde_Imap_Client::FETCH_FULLMSG:
                if (empty($c_val['peek'])) {
                    $this->openMailbox($this->_selected, Horde_Imap_Client::OPEN_READWRITE);
                }
                $fetch->add(
                    'BODY' .
                    (!empty($c_val['peek']) ? '.PEEK' : '') .
                    '[]' .
                    $this->_partialAtom($c_val)
                );
                break;

            case Horde_Imap_Client::FETCH_HEADERTEXT:
            case Horde_Imap_Client::FETCH_BODYTEXT:
            case Horde_Imap_Client::FETCH_MIMEHEADER:
            case Horde_Imap_Client::FETCH_BODYPART:
            case Horde_Imap_Client::FETCH_HEADERS:
                foreach ($c_val as $key => $val) {
                    $cmd = ($key == 0)
                        ? ''
                        : $key . '.';
                    $main_cmd = 'BODY';

                    switch ($type) {
                    case Horde_Imap_Client::FETCH_HEADERTEXT:
                        $cmd .= 'HEADER';
                        break;

                    case Horde_Imap_Client::FETCH_BODYTEXT:
                        $cmd .= 'TEXT';
                        break;

                    case Horde_Imap_Client::FETCH_MIMEHEADER:
                        $cmd .= 'MIME';
                        break;

                    case Horde_Imap_Client::FETCH_BODYPART:
                        // Remove the last dot from the string.
                        $cmd = substr($cmd, 0, -1);

                        if (!empty($val['decode']) &&
                            $this->_capability('BINARY')) {
                            $main_cmd = 'BINARY';
                            $pipeline->data['binaryquery'][$key] = $val;
                        }
                        break;

                    case Horde_Imap_Client::FETCH_HEADERS:
                        $cmd .= 'HEADER.FIELDS';
                        if (!empty($val['notsearch'])) {
                            $cmd .= '.NOT';
                        }
                        $cmd .= ' (' . implode(' ', array_map('Horde_String::upper', $val['headers'])) . ')';

                        // Maintain a command -> label lookup so we can put
                        // the results in the proper location.
                        $pipeline->data['fetch_lookup'][$cmd] = $key;
                    }

                    if (empty($val['peek'])) {
                        $this->openMailbox($this->_selected, Horde_Imap_Client::OPEN_READWRITE);
                    }

                    $fetch->add(
                        $main_cmd .
                        (!empty($val['peek']) ? '.PEEK' : '') .
                        '[' . $cmd . ']' .
                        $this->_partialAtom($val)
                    );
                }
                break;

            case Horde_Imap_Client::FETCH_BODYPARTSIZE:
                if ($this->_capability('BINARY')) {
                    foreach ($c_val as $val) {
                        $fetch->add('BINARY.SIZE[' . $val . ']');
                    }
                }
                break;

            case Horde_Imap_Client::FETCH_ENVELOPE:
                $fetch->add('ENVELOPE');
                break;

            case Horde_Imap_Client::FETCH_FLAGS:
                $fetch->add('FLAGS');
                break;

            case Horde_Imap_Client::FETCH_IMAPDATE:
                $fetch->add('INTERNALDATE');
                break;

            case Horde_Imap_Client::FETCH_SIZE:
                $fetch->add('RFC822.SIZE');
                break;

            case Horde_Imap_Client::FETCH_UID:
                /* A UID FETCH will always return UID information (RFC 3501
                 * [6.4.8]). Don't add to query as it just creates a longer
                 * FETCH command. */
                if ($sequence) {
                    $fetch->add('UID');
                }
                break;

            case Horde_Imap_Client::FETCH_SEQ:
                /* Nothing we need to add to fetch request unless sequence is
                 * the only criteria (see below). */
                break;

            case Horde_Imap_Client::FETCH_MODSEQ:
                /* The 'changedsince' modifier implicitly adds the MODSEQ
                 * FETCH item (RFC 7162 [3.1.4.1]). Don't add to query as it
                 * just creates a longer FETCH command. */
                if (empty($options['changedsince'])) {
                    $fetch->add('MODSEQ');
                }
                break;
            }
        }

        /* If empty fetch, add UID to make command valid. */
        if (!count($fetch)) {
            $fetch->add('UID');
        }

        /* Add changedsince parameters. */
        if (empty($options['changedsince'])) {
            $fetch_cmd = $fetch;
        } else {
            /* We might just want the list of UIDs changed since a given
             * modseq. In that case, we don't have any other FETCH attributes,
             * but RFC 3501 requires at least one specified attribute. */
            $fetch_cmd = array(
                $fetch,
                new Horde_Imap_Client_Data_Format_List(array(
                    'CHANGEDSINCE',
                    new Horde_Imap_Client_Data_Format_Number($options['changedsince'])
                ))
            );
        }

        /* The FETCH command should be the only command issued by this library
         * that should ever approach the command length limit.
         * @todo Move this check to a more centralized location (_command()?).
         * For simplification, assume that the UID list is the limiting factor
         * and split this list at a sequence comma delimiter if it exceeds
         * the character limit. */
        foreach ($options['ids']->split($this->_capability()->cmdlength) as $val) {
            $cmd = $this->_command(
                $sequence ? 'FETCH' : 'UID FETCH'
            )->add(array(
                $val,
                $fetch_cmd
            ));
            $pipeline->add($cmd);
        }
    }

    /**
     * Add a partial atom to an IMAP command based on the criteria options.
     *
     * @param array $opts  Criteria options.
     *
     * @return string  The partial atom.
     */
    protected function _partialAtom($opts)
    {
        if (!empty($opts['length'])) {
            return '<' . (empty($opts['start']) ? 0 : intval($opts['start'])) . '.' . intval($opts['length']) . '>';
        }

        return empty($opts['start'])
            ? ''
            : ('<' . intval($opts['start']) . '>');
    }

    /**
     * Parse a FETCH response (RFC 3501 [7.4.2]). A FETCH response may occur
     * due to a FETCH command, or due to a change in a message's state (i.e.
     * the flags change).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param integer $id                       The message sequence number.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseFetch(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        $id,
        Horde_Imap_Client_Tokenize $data
    )
    {
        if ($data->next() !== true) {
            return;
        }

        $ob = $pipeline->fetch->get($id);
        $ob->setSeq($id);

        $flags = $modseq = $uid = false;

        while (($tag = $data->next()) !== false) {
            $tag = Horde_String::upper($tag);

            /* Catch equivalent RFC822 tags, in case server returns them
             * (in error, since we only use BODY in FETCH requests). */
            switch ($tag) {
            case 'RFC822':
                $tag = 'BODY[]';
                break;

            case 'RFC822.HEADER':
                $tag = 'BODY[HEADER]';
                break;

            case 'RFC822.TEXT':
                $tag = 'BODY[TEXT]';
                break;
            }

            switch ($tag) {
            case 'BODYSTRUCTURE':
                $data->next();
                $structure = $this->_parseBodystructure($data);
                $structure->buildMimeIds();
                $ob->setStructure($structure);
                break;

            case 'ENVELOPE':
                $data->next();
                $ob->setEnvelope($this->_parseEnvelope($data));
                break;

            case 'FLAGS':
                $data->next();
                $ob->setFlags($data->flushIterator());
                $flags = true;
                break;

            case 'INTERNALDATE':
                $ob->setImapDate($data->next());
                break;

            case 'RFC822.SIZE':
                $ob->setSize($data->next());
                break;

            case 'UID':
                $ob->setUid($data->next());
                $uid = true;
                break;

            case 'MODSEQ':
                $data->next();
                $modseq = $data->next();
                $data->next();

                /* MODSEQ must be greater than 0, so do sanity checking. */
                if ($modseq > 0) {
                    $ob->setModSeq($modseq);

                    /* Store MODSEQ value. It may be used as the highestmodseq
                     * once a tagged response is received (RFC 7162 [6]). */
                    $pipeline->data['modseqs'][] = $modseq;
                }
                break;

            default:
                // Catch BODY[*]<#> responses
                if (strpos($tag, 'BODY[') === 0) {
                    // Remove the beginning 'BODY['
                    $tag = substr($tag, 5);

                    // BODY[HEADER.FIELDS] request
                    if (!empty($pipeline->data['fetch_lookup']) &&
                        (strpos($tag, 'HEADER.FIELDS') !== false)) {
                        $data->next();
                        $sig = $tag . ' (' . implode(' ', array_map('Horde_String::upper', $data->flushIterator())) . ')';

                        // Ignore the trailing bracket
                        $data->next();

                        $ob->setHeaders($pipeline->data['fetch_lookup'][$sig], $data->next());
                    } else {
                        // Remove trailing bracket and octet start info
                        $tag = substr($tag, 0, strrpos($tag, ']'));

                        if (!strlen($tag)) {
                            // BODY[] request
                            if (!is_null($tmp = $data->nextStream())) {
                                $ob->setFullMsg($tmp);
                            }
                        } elseif (is_numeric(substr($tag, -1))) {
                            // BODY[MIMEID] request
                            if (!is_null($tmp = $data->nextStream())) {
                                $ob->setBodyPart($tag, $tmp);
                            }
                        } else {
                            // BODY[HEADER|TEXT|MIME] request
                            if (($last_dot = strrpos($tag, '.')) === false) {
                                $mime_id = 0;
                            } else {
                                $mime_id = substr($tag, 0, $last_dot);
                                $tag = substr($tag, $last_dot + 1);
                            }

                            if (!is_null($tmp = $data->nextStream())) {
                                switch ($tag) {
                                case 'HEADER':
                                    $ob->setHeaderText($mime_id, $tmp);
                                    break;

                                case 'TEXT':
                                    $ob->setBodyText($mime_id, $tmp);
                                    break;

                                case 'MIME':
                                    $ob->setMimeHeader($mime_id, $tmp);
                                    break;
                                }
                            }
                        }
                    }
                } elseif (strpos($tag, 'BINARY[') === 0) {
                    // Catch BINARY[*]<#> responses
                    // Remove the beginning 'BINARY[' and the trailing bracket
                    // and octet start info
                    $tag = substr($tag, 7, strrpos($tag, ']') - 7);
                    $body = $data->nextStream();

                    if (is_null($body)) {
                        /* Dovecot bug (as of 2.2.12): binary fetch of body
                         * part may fail with NIL return if decoding failed on
                         * server. Try again with non-decoded body. */
                        $bq = $pipeline->data['binaryquery'][$tag];
                        unset($bq['decode']);

                        $query = new Horde_Imap_Client_Fetch_Query();
                        $query->bodyPart($tag, $bq);

                        $qids = ($quid = $ob->getUid())
                            ? new Horde_Imap_Client_Ids($quid)
                            : new Horde_Imap_Client_Ids($id, true);

                        $pipeline->data['fetch_followup'][] = array(
                            '_query' => $query,
                            'ids' => $qids
                        );
                    } else {
                        $ob->setBodyPart(
                            $tag,
                            $body,
                            empty($this->_temp['literal8']) ? '8bit' : 'binary'
                        );
                    }
                } elseif (strpos($tag, 'BINARY.SIZE[') === 0) {
                    // Catch BINARY.SIZE[*] responses
                    // Remove the beginning 'BINARY.SIZE[' and the trailing
                    // bracket and octet start info
                    $tag = substr($tag, 12, strrpos($tag, ']') - 12);
                    $ob->setBodyPartSize($tag, $data->next());
                }
                break;
            }
        }

        /* MODSEQ issue: Oh joy. Per RFC 5162 (see Errata #1807), FETCH FLAGS
         * responses are NOT required to provide UID information, even if
         * QRESYNC is explicitly enabled. Caveat: the FLAGS information
         * returned during a SELECT/EXAMINE MUST contain UIDs so we are OK
         * there.
         * The good news: all decent IMAP servers (Cyrus, Dovecot) will always
         * provide UID information, so this is not normally an issue.
         * The bad news: spec-wise, this behavior cannot be 100% guaranteed.
         * Compromise: We will watch for a FLAGS response with a MODSEQ and
         * check if a UID exists also. If not, put the sequence number in a
         * queue - it is possible the UID information may appear later in an
         * untagged response. When the command is over, double check to make
         * sure there are none of these MODSEQ/FLAGS that are still UID-less.
         * In the (rare) event that there is, don't cache anything and
         * immediately close the mailbox: flags will be correctly sync'd next
         * mailbox open so we only lose a bit of caching efficiency.
         * Otherwise, we could end up with an inconsistent cached state.
         * This Errata has been fixed in 7162 [3.2.4]. */
        if ($flags && $modseq && !$uid) {
            $pipeline->data['modseqs_nouid'][] = $id;
        }
    }

    /**
     * Recursively parse BODYSTRUCTURE data from a FETCH return (see
     * RFC 3501 [7.4.2]).
     *
     * @param Horde_Imap_Client_Tokenize $data  Data returned from the server.
     *
     * @return Horde_Mime_Part  Mime part object.
     */
    protected function _parseBodystructure(Horde_Imap_Client_Tokenize $data)
    {
        $ob = new Horde_Mime_Part();

        // If index 0 is an array, this is a multipart part.
        if (($entry = $data->next()) === true) {
            do {
                $ob->addPart($this->_parseBodystructure($data));
            } while (($entry = $data->next()) === true);

            // The subpart type.
            $ob->setType('multipart/' . $entry);

            // After the subtype is further extension information. This
            // information MAY appear for BODYSTRUCTURE requests.

            // This is parameter information.
            if (($tmp = $data->next()) === false) {
                return $ob;
            } elseif ($tmp === true) {
                foreach ($this->_parseStructureParams($data) as $key => $val) {
                    $ob->setContentTypeParameter($key, $val);
                }
            }
        } else {
            $ob->setType($entry . '/' . $data->next());

            if ($data->next() === true) {
                foreach ($this->_parseStructureParams($data) as $key => $val) {
                    $ob->setContentTypeParameter($key, $val);
                }
            }

            if (!is_null($tmp = $data->next())) {
                $ob->setContentId($tmp);
            }

            if (!is_null($tmp = $data->next())) {
                $ob->setDescription(Horde_Mime::decode($tmp));
            }

            $te = $data->next();
            $bytes = $data->next();

            if (!is_null($te)) {
                $ob->setTransferEncoding($te);

                /* Base64 transfer encoding is approx. 33% larger than
                 * original data size (RFC 2045 [6.8]). Return from
                 * BODYSTRUCTURE is the size of the ENCODED data (RFC 3501
                 * [7.4.2]). */
                if (strcasecmp($te, 'base64') === 0) {
                    $bytes *= 0.75;
                }
            }

            $ob->setBytes($bytes);

            // If the type is 'message/rfc822' or 'text/*', several extra
            // fields are included
            switch ($ob->getPrimaryType()) {
            case 'message':
                if ($ob->getSubType() == 'rfc822') {
                    if ($data->next() === true) {
                        // Ignore: envelope
                        $data->flushIterator(false);
                    }
                    if ($data->next() === true) {
                        $ob->addPart($this->_parseBodystructure($data));
                    }
                    $data->next(); // Ignore: lines
                }
                break;

            case 'text':
                $data->next(); // Ignore: lines
                break;
            }

            // After the subtype is further extension information. This
            // information MAY appear for BODYSTRUCTURE requests.

            // Ignore: MD5
            if ($data->next() === false) {
                return $ob;
            }
        }

        // This is disposition information
        if (($tmp = $data->next()) === false) {
            return $ob;
        } elseif ($tmp === true) {
            $ob->setDisposition($data->next());

            if ($data->next() === true) {
                foreach ($this->_parseStructureParams($data) as $key => $val) {
                    $ob->setDispositionParameter($key, $val);
                }
            }
            $data->next();
        }

        // This is language information. It is either a single value or a list
        // of values.
        if (($tmp = $data->next()) === false) {
            return $ob;
        } elseif (!is_null($tmp)) {
            $ob->setLanguage(($tmp === true) ? $data->flushIterator() : $tmp);
        }

        // Ignore location (RFC 2557) and consume closing paren.
        $data->flushIterator(false);

        return $ob;
    }

    /**
     * Helper function to parse a parameters-like tokenized array.
     *
     * @param mixed $data  Message data. Either a Horde_Imap_Client_Tokenize
     *                     object or null.
     *
     * @return array  The parameter array.
     */
    protected function _parseStructureParams($data)
    {
        $params = array();

        if (is_null($data)) {
            return $params;
        }

        while (($name = $data->next()) !== false) {
            $params[Horde_String::lower($name)] = $data->next();
        }

        $cp = new Horde_Mime_Headers_ContentParam('Unused', $params);

        return $cp->params;
    }

    /**
     * Parse ENVELOPE data from a FETCH return (see RFC 3501 [7.4.2]).
     *
     * @param Horde_Imap_Client_Tokenize $data  Data returned from the server.
     *
     * @return Horde_Imap_Client_Data_Envelope  An envelope object.
     */
    protected function _parseEnvelope(Horde_Imap_Client_Tokenize $data)
    {
        // 'route', the 2nd element, is deprecated by RFC 2822.
        $addr_structure = array(
            0 => 'personal',
            2 => 'mailbox',
            3 => 'host'
        );
        $env_data = array(
            0 => 'date',
            1 => 'subject',
            2 => 'from',
            3 => 'sender',
            4 => 'reply_to',
            5 => 'to',
            6 => 'cc',
            7 => 'bcc',
            8 => 'in_reply_to',
            9 => 'message_id'
        );

        $addr_ob = new Horde_Mail_Rfc822_Address();
        $env_addrs = $this->getParam('envelope_addrs');
        $env_str = $this->getParam('envelope_string');
        $key = 0;
        $ret = new Horde_Imap_Client_Data_Envelope();

        while (($val = $data->next()) !== false) {
            if (!isset($env_data[$key]) || is_null($val)) {
                ++$key;
                continue;
            }

            if (is_string($val)) {
                // These entries are text fields.
                $ret->{$env_data[$key]} = substr($val, 0, $env_str);
            } else {
                // These entries are address structures.
                $group = null;
                $key2 = 0;
                $tmp = new Horde_Mail_Rfc822_List();

                while ($data->next() !== false) {
                    $a_val = $data->flushIterator();

                    // RFC 3501 [7.4.2]: Group entry when host is NIL.
                    // Group end when mailbox is NIL; otherwise, this is
                    // mailbox name.
                    if (is_null($a_val[3])) {
                        if (is_null($a_val[2])) {
                            $group = null;
                        } else {
                            $group = new Horde_Mail_Rfc822_Group($a_val[2]);
                            $tmp->add($group);
                        }
                    } else {
                        $addr = clone $addr_ob;

                        foreach ($addr_structure as $add_key => $add_val) {
                            if (!is_null($a_val[$add_key])) {
                                $addr->$add_val = $a_val[$add_key];
                            }
                        }

                        if ($group) {
                            $group->addresses->add($addr);
                        } else {
                            $tmp->add($addr);
                        }
                    }

                    if (++$key2 >= $env_addrs) {
                        $data->flushIterator(false);
                        break;
                    }
                }

                $ret->{$env_data[$key]} = $tmp;
            }

            ++$key;
        }

        return $ret;
    }

    /**
     */
    protected function _vanished($modseq, Horde_Imap_Client_Ids $ids)
    {
        $pipeline = $this->_pipeline(
            $this->_command('UID FETCH')->add(array(
                strval($ids),
                'UID',
                new Horde_Imap_Client_Data_Format_List(array(
                    'VANISHED',
                    'CHANGEDSINCE',
                    new Horde_Imap_Client_Data_Format_Number($modseq)
                ))
            ))
        );
        $pipeline->data['vanished'] = $this->getIdsOb();

        return $this->_sendCmd($pipeline)->data['vanished'];
    }

    /**
     */
    protected function _store($options)
    {
        $pipeline = $this->_storeCmd($options);
        $pipeline->data['modified'] = $this->getIdsOb();

        try {
            $resp = $this->_sendCmd($pipeline);

            /* Check for EXPUNGEISSUED (RFC 2180 [4.2]/RFC 5530 [3]). */
            if (!empty($resp->data['expungeissued'])) {
                $this->noop();
            }

            return $resp->data['modified'];
        } catch (Horde_Imap_Client_Exception_ServerResponse $e) {
            /* A NO response, when coupled with a sequence STORE and
             * non-SILENT behavior, most likely means that messages were
             * expunged. RFC 2180 [4.2] */
            if (empty($pipeline->data['store_silent']) &&
                !empty($options['sequence']) &&
                ($e->status === Horde_Imap_Client_Interaction_Server::NO)) {
                $this->noop();
            }

            return $pipeline->data['modified'];
        }
    }

    /**
     * Create a store command.
     *
     * @param array $options  See Horde_Imap_Client_Base#_store().
     *
     * @return Horde_Imap_Client_Interaction_Pipeline  Pipeline object.
     */
    protected function _storeCmd($options)
    {
        $cmds = array();
        $silent = empty($options['unchangedsince'])
             ? !($this->_debug->debug || $this->_initCache(true))
             : false;

        if (!empty($options['replace'])) {
            $cmds[] = array(
                'FLAGS' . ($silent ? '.SILENT' : ''),
                $options['replace']
            );
        } else {
            foreach (array('add' => '+', 'remove' => '-') as $k => $v) {
                if (!empty($options[$k])) {
                    $cmds[] = array(
                        $v . 'FLAGS' . ($silent ? '.SILENT' : ''),
                        $options[$k]
                    );
                }
            }
        }

        $pipeline = $this->_pipeline();
        $pipeline->data['store_silent'] = $silent;

        foreach ($cmds as $val) {
            $cmd = $this->_command(
                empty($options['sequence']) ? 'UID STORE' : 'STORE'
            )->add(strval($options['ids']));
            if (!empty($options['unchangedsince'])) {
                $cmd->add(new Horde_Imap_Client_Data_Format_List(array(
                    'UNCHANGEDSINCE',
                    new Horde_Imap_Client_Data_Format_Number(intval($options['unchangedsince']))
                )));
            }
            $cmd->add($val);

            $pipeline->add($cmd);
        }

        return $pipeline;
    }

    /**
     */
    protected function _copy(Horde_Imap_Client_Mailbox $dest, $options)
    {
        /* Check for MOVE command (RFC 6851). */
        $move_cmd = (!empty($options['move']) &&
                     $this->_capability('MOVE'));

        $cmd = $this->_pipeline(
            $this->_command(
                ($options['ids']->sequence ? '' : 'UID ') . ($move_cmd ? 'MOVE' : 'COPY')
            )->add(array(
                strval($options['ids']),
                $this->_getMboxFormatOb($dest)
            ))
        );
        $cmd->data['copydest'] = $dest;

        // COPY returns no untagged information (RFC 3501 [6.4.7])
        try {
            $resp = $this->_sendCmd($cmd);
        } catch (Horde_Imap_Client_Exception $e) {
            if (!empty($options['create']) &&
                !empty($e->resp_data['trycreate'])) {
                $this->createMailbox($dest);
                unset($options['create']);
                return $this->_copy($dest, $options);
            }
            throw $e;
        }

        // If moving, delete the old messages now. Short-circuit if nothing
        // was moved.
        if (!$move_cmd &&
            !empty($options['move']) &&
            (isset($resp->data['copyuid']) ||
             !$this->_capability('UIDPLUS'))) {
            $this->expunge($this->_selected, array(
                'delete' => true,
                'ids' => $options['ids']
            ));
        }

        return isset($resp->data['copyuid'])
            ? $resp->data['copyuid']
            : true;
    }

    /**
     */
    protected function _setQuota(Horde_Imap_Client_Mailbox $root, $resources)
    {
        $limits = new Horde_Imap_Client_Data_Format_List();

        foreach ($resources as $key => $val) {
            $limits->add(array(
                Horde_String::upper($key),
                new Horde_Imap_Client_Data_Format_Number($val)
            ));
        }

        $this->_sendCmd(
            $this->_command('SETQUOTA')->add(array(
                $this->_getMboxFormatOb($root),
                $limits
            ))
        );
    }

    /**
     */
    protected function _getQuota(Horde_Imap_Client_Mailbox $root)
    {
        $pipeline = $this->_pipeline(
            $this->_command('GETQUOTA')->add(
                $this->_getMboxFormatOb($root)
            )
        );
        $pipeline->data['quotaresp'] = array();

        return reset($this->_sendCmd($pipeline)->data['quotaresp']);
    }

    /**
     * Parse a QUOTA response (RFC 2087 [5.1]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseQuota(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        $c = &$pipeline->data['quotaresp'];

        $root = $data->next();
        $c[$root] = array();

        $data->next();

        while (($curr = $data->next()) !== false) {
            $c[$root][Horde_String::lower($curr)] = array(
                'usage' => $data->next(),
                'limit' => $data->next()
            );
        }
    }

    /**
     */
    protected function _getQuotaRoot(Horde_Imap_Client_Mailbox $mailbox)
    {
        $pipeline = $this->_pipeline(
            $this->_command('GETQUOTAROOT')->add(
                $this->_getMboxFormatOb($mailbox)
            )
        );
        $pipeline->data['quotaresp'] = array();

        return $this->_sendCmd($pipeline)->data['quotaresp'];
    }

    /**
     */
    protected function _setACL(Horde_Imap_Client_Mailbox $mailbox, $identifier,
                               $options)
    {
        // SETACL returns no untagged information (RFC 4314 [3.1]).
        $this->_sendCmd(
            $this->_command('SETACL')->add(array(
                $this->_getMboxFormatOb($mailbox),
                new Horde_Imap_Client_Data_Format_Astring($identifier),
                new Horde_Imap_Client_Data_Format_Astring($options['rights'])
            ))
        );
    }

    /**
     */
    protected function _deleteACL(Horde_Imap_Client_Mailbox $mailbox, $identifier)
    {
        // DELETEACL returns no untagged information (RFC 4314 [3.2]).
        $this->_sendCmd(
            $this->_command('DELETEACL')->add(array(
                $this->_getMboxFormatOb($mailbox),
                new Horde_Imap_Client_Data_Format_Astring($identifier)
            ))
        );
    }

    /**
     */
    protected function _getACL(Horde_Imap_Client_Mailbox $mailbox)
    {
        return $this->_sendCmd(
            $this->_command('GETACL')->add(
                $this->_getMboxFormatOb($mailbox)
            )
        )->data['getacl'];
    }

    /**
     * Parse an ACL response (RFC 4314 [3.6]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseACL(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        $acl = array();

        // Ignore mailbox argument -> index 1
        $data->next();

        while (($curr = $data->next()) !== false) {
            $acl[$curr] = ($curr[0] === '-')
                ? new Horde_Imap_Client_Data_AclNegative($data->next())
                : new Horde_Imap_Client_Data_Acl($data->next());
        }

        $pipeline->data['getacl'] = $acl;
    }

    /**
     */
    protected function _listACLRights(Horde_Imap_Client_Mailbox $mailbox,
                                      $identifier)
    {
        $resp = $this->_sendCmd(
            $this->_command('LISTRIGHTS')->add(array(
                $this->_getMboxFormatOb($mailbox),
                new Horde_Imap_Client_Data_Format_Astring($identifier)
            ))
        );

        return isset($resp->data['listaclrights'])
            ? $resp->data['listaclrights']
            : new Horde_Imap_Client_Data_AclRights();
    }

    /**
     * Parse a LISTRIGHTS response (RFC 4314 [3.7]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseListRights(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        // Ignore mailbox and identifier arguments
        $data->next();
        $data->next();

        $pipeline->data['listaclrights'] = new Horde_Imap_Client_Data_AclRights(
            str_split($data->next()),
            $data->flushIterator()
        );
    }

    /**
     */
    protected function _getMyACLRights(Horde_Imap_Client_Mailbox $mailbox)
    {
        $resp = $this->_sendCmd(
            $this->_command('MYRIGHTS')->add(
                $this->_getMboxFormatOb($mailbox)
            )
        );

        return isset($resp->data['myrights'])
            ? $resp->data['myrights']
            : new Horde_Imap_Client_Data_Acl();
    }

    /**
     * Parse a MYRIGHTS response (RFC 4314 [3.8]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     */
    protected function _parseMyRights(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        // Ignore 1st token (mailbox name)
        $data->next();

        $pipeline->data['myrights'] = new Horde_Imap_Client_Data_Acl($data->next());
    }

    /**
     */
    protected function _getMetadata(Horde_Imap_Client_Mailbox $mailbox,
                                    $entries, $options)
    {
        $pipeline = $this->_pipeline();
        $pipeline->data['metadata'] = array();

        if ($this->_capability('METADATA') ||
            (strlen($mailbox) && $this->_capability('METADATA-SERVER'))) {
            $cmd_options = new Horde_Imap_Client_Data_Format_List();

            if (!empty($options['maxsize'])) {
                $cmd_options->add(array(
                    'MAXSIZE',
                    new Horde_Imap_Client_Data_Format_Number($options['maxsize'])
                ));
            }
            if (!empty($options['depth'])) {
                $cmd_options->add(array(
                    'DEPTH',
                    new Horde_Imap_Client_Data_Format_Number($options['depth'])
                ));
            }

            $queries = new Horde_Imap_Client_Data_Format_List();
            foreach ($entries as $md_entry) {
                $queries->add(new Horde_Imap_Client_Data_Format_Astring($md_entry));
            }

            $cmd = $this->_command('GETMETADATA')->add(
                $this->_getMboxFormatOb($mailbox)
            );
            if (count($cmd_options)) {
                $cmd->add($cmd_options);
            }
            $cmd->add($queries);

            $pipeline->add($cmd);
        } else {
            if (!$this->_capability('ANNOTATEMORE') &&
                !$this->_capability('ANNOTATEMORE2')) {
                throw new Horde_Imap_Client_Exception_NoSupportExtension('METADATA');
            }

            $queries = array();
            foreach ($entries as $md_entry) {
                list($entry, $type) = $this->_getAnnotateMoreEntry($md_entry);

                if (!isset($queries[$type])) {
                    $queries[$type] = new Horde_Imap_Client_Data_Format_List();
                }
                $queries[$type]->add(new Horde_Imap_Client_Data_Format_String($entry));
            }

            foreach ($queries as $key => $val) {
                // TODO: Honor maxsize and depth options.
                $pipeline->add(
                    $this->_command('GETANNOTATION')->add(array(
                        $this->_getMboxFormatOb($mailbox),
                        $val,
                        new Horde_Imap_Client_Data_Format_String($key)
                    ))
                );
            }
        }

        return $this->_sendCmd($pipeline)->data['metadata'];
    }

    /**
     * Split a name for the METADATA extension into the correct syntax for the
     * older ANNOTATEMORE version.
     *
     * @param string $name  A name for a metadata entry.
     *
     * @return array  A list of two elements: The entry name and the value
     *                type.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _getAnnotateMoreEntry($name)
    {
        if (substr($name, 0, 7) === '/shared') {
            return array(substr($name, 7), 'value.shared');
        } else if (substr($name, 0, 8) === '/private') {
            return array(substr($name, 8), 'value.priv');
        }

        $e = new Horde_Imap_Client_Exception(
            Horde_Imap_Client_Translation::r("Invalid METADATA entry: \"%s\"."),
            Horde_Imap_Client_Exception::METADATA_INVALID
        );
        $e->messagePrintf(array($name));
        throw $e;
    }

    /**
     */
    protected function _setMetadata(Horde_Imap_Client_Mailbox $mailbox, $data)
    {
        if ($this->_capability('METADATA') ||
            (strlen($mailbox) && $this->_capability('METADATA-SERVER'))) {
            $data_elts = new Horde_Imap_Client_Data_Format_List();

            foreach ($data as $key => $value) {
                $data_elts->add(array(
                    new Horde_Imap_Client_Data_Format_Astring($key),
                    /* METADATA supports literal8 - thus, it implicitly
                     * supports non-ASCII characters in the data. */
                    new Horde_Imap_Client_Data_Format_Nstring_Nonascii($value)
                ));
            }

            $cmd = $this->_command('SETMETADATA')->add(array(
                $this->_getMboxFormatOb($mailbox),
                $data_elts
            ));
        } else {
            if (!$this->_capability('ANNOTATEMORE') &&
                !$this->_capability('ANNOTATEMORE2')) {
                throw new Horde_Imap_Client_Exception_NoSupportExtension('METADATA');
            }

            $cmd = $this->_pipeline();

            foreach ($data as $md_entry => $value) {
                list($entry, $type) = $this->_getAnnotateMoreEntry($md_entry);

                $cmd->add(
                    $this->_command('SETANNOTATION')->add(array(
                        $this->_getMboxFormatOb($mailbox),
                        new Horde_Imap_Client_Data_Format_String($entry),
                        new Horde_Imap_Client_Data_Format_List(array(
                            new Horde_Imap_Client_Data_Format_String($type),
                            /* ANNOTATEMORE supports literal8 - thus, it
                             * implicitly supports non-ASCII characters in the
                             * data. */
                            new Horde_Imap_Client_Data_Format_Nstring_Nonascii($value)
                        ))
                    ))
                );
            }
        }

        $this->_sendCmd($cmd);
    }

    /**
     * Parse an ANNOTATION response (ANNOTATEMORE/ANNOTATEMORE2).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _parseAnnotation(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        // Mailbox name is in UTF7-IMAP.
        $mbox = Horde_Imap_Client_Mailbox::get($data->next(), true);
        $entry = $data->next();

        // Ignore unsolicited responses.
        if ($data->next() !== true) {
            return;
        }

        while (($type = $data->next()) !== false) {
            switch ($type) {
            case 'value.priv':
                $pipeline->data['metadata'][strval($mbox)]['/private' . $entry] = $data->next();
                break;

            case 'value.shared':
                $pipeline->data['metadata'][strval($mbox)]['/shared' . $entry] = $data->next();
                break;

            default:
                $e = new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Invalid METADATA value type \"%s\"."),
                    Horde_Imap_Client_Exception::METADATA_INVALID
                );
                $e->messagePrintf(array($type));
                throw $e;
            }
        }
    }

    /**
     * Parse a METADATA response (RFC 5464 [4.4]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Tokenize $data  The server response.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _parseMetadata(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Tokenize $data
    )
    {
        // Mailbox name is in UTF7-IMAP.
        $mbox = Horde_Imap_Client_Mailbox::get($data->next(), true);

        // Ignore unsolicited responses.
        if ($data->next() === true) {
            while (($entry = $data->next()) !== false) {
                $pipeline->data['metadata'][strval($mbox)][$entry] = $data->next();
            }
        }
    }

    /* Overriden methods. */

    /**
     * @param array $opts  Options:
     *   - decrement: (boolean) If true, decrement the message count.
     *   - pipeline: (Horde_Imap_Client_Interaction_Pipeline) Pipeline object.
     */
    protected function _deleteMsgs(Horde_Imap_Client_Mailbox $mailbox,
                                   Horde_Imap_Client_Ids $ids,
                                   array $opts = array())
    {
        /* If there are pending FETCH cache writes, we need to write them
         * before the UID -> sequence number mapping changes. */
        if (isset($opts['pipeline'])) {
            $this->_updateCache($opts['pipeline']->fetch);
        }

        $res = parent::_deleteMsgs($mailbox, $ids);

        if (isset($this->_temp['expunged'])) {
            $this->_temp['expunged']->add($res);
        }

        if (!empty($opts['decrement'])) {
            $mbox_ob = $this->_mailboxOb();
            $mbox_ob->setStatus(
                Horde_Imap_Client::STATUS_MESSAGES,
                $mbox_ob->getStatus(Horde_Imap_Client::STATUS_MESSAGES) - count($ids)
            );
        }
    }

    /* Internal functions. */

    /**
     * Return the proper mailbox format object based on the server's
     * capabilities.
     *
     * @param string $mailbox  The mailbox.
     * @param boolean $list    Is this object used in a LIST command?
     *
     * @return Horde_Imap_Client_Data_Format_Mailbox  A mailbox format object.
     */
    protected function _getMboxFormatOb($mailbox, $list = false)
    {
        if ($this->_capability()->isEnabled('UTF8=ACCEPT')) {
            try {
                return $list
                    ? new Horde_Imap_Client_Data_Format_ListMailbox_Utf8($mailbox)
                    : new Horde_Imap_Client_Data_Format_Mailbox_Utf8($mailbox);
            } catch (Horde_Imap_Client_Data_Format_Exception $e) {}
        }

        return $list
            ? new Horde_Imap_Client_Data_Format_ListMailbox($mailbox)
            : new Horde_Imap_Client_Data_Format_Mailbox($mailbox);
    }

    /**
     * Sends command(s) to the IMAP server. A connection to the server must
     * have already been made.
     *
     * @param mixed $cmd  Either a Command object or a Pipeline object.
     *
     * @return Horde_Imap_Client_Interaction_Pipeline  A pipeline object.
     * @throws Horde_Imap_Client_Exception
     */
    protected function _sendCmd($cmd)
    {
        $pipeline = ($cmd instanceof Horde_Imap_Client_Interaction_Command)
            ? $this->_pipeline($cmd)
            : $cmd;

        if (!empty($this->_cmdQueue)) {
            /* Add commands in reverse order. */
            foreach (array_reverse($this->_cmdQueue) as $val) {
                $pipeline->add($val, true);
            }

            $this->_cmdQueue = array();
        }

        $cmd_list = array();

        foreach ($pipeline as $val) {
            if ($val->continuation) {
                $this->_sendCmdChunk($pipeline, $cmd_list);
                $this->_sendCmdChunk($pipeline, array($val));
                $cmd_list = array();
            } else {
                $cmd_list[] = $val;
            }
        }

        $this->_sendCmdChunk($pipeline, $cmd_list);

        /* If any FLAGS responses contain MODSEQs but not UIDs, don't
         * cache any data and immediately close the mailbox. */
        foreach ($pipeline->data['modseqs_nouid'] as $val) {
            if (!$pipeline->fetch[$val]->getUid()) {
                $this->_debug->info(
                    'Server provided FLAGS MODSEQ without providing UID.'
                );
                $this->close();
                return $pipeline;
            }
        }

        /* Update HIGHESTMODSEQ value. */
        if (!empty($pipeline->data['modseqs'])) {
            $modseq = max($pipeline->data['modseqs']);
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ, $modseq);
            /* CONDSTORE has not yet updated flag information, so don't update
             * modseq yet. */
            if ($this->_capability()->isEnabled('QRESYNC')) {
                $this->_updateModSeq($modseq);
            }
        }

        /* Update cache items. */
        $this->_updateCache($pipeline->fetch);

        return $pipeline;
    }

    /**
     * Send a chunk of commands and/or continuation fragments to the server.
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  The pipeline
     *                                                          object.
     * @param array $chunk  List of commands to send.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _sendCmdChunk($pipeline, $chunk)
    {
        if (empty($chunk)) {
            return;
        }

        $cmd_count = count($chunk);
        $exception = null;

        foreach ($chunk as $val) {
            $val->pipeline = $pipeline;

            try {
                if ($this->_processCmd($pipeline, $val, $val)) {
                    $this->_connection->write('', true);
                } else {
                    $cmd_count = 0;
                }
            } catch (Horde_Imap_Client_Exception $e) {
                switch ($e->getCode()) {
                case Horde_Imap_Client_Exception::SERVER_WRITEERROR:
                    $this->_temp['logout'] = true;
                    $this->logout();
                    break;
                }

                throw $e;
            }
        }

        while ($cmd_count) {
            try {
                if ($this->_getLine($pipeline) instanceof Horde_Imap_Client_Interaction_Server_Tagged) {
                    --$cmd_count;
                }
            } catch (Horde_Imap_Client_Exception $e) {
                switch ($e->getCode()) {
                case $e::DISCONNECT:
                    /* Guaranteed to have no more data incoming, so we can
                     * immediately logout. */
                    $this->_temp['logout'] = true;
                    $this->logout();
                    throw $e;
                }

                /* For all other issues, catch and store exception; don't
                 * throw until all input is read since we need to clear
                 * incoming queue. (For now, only store first exception.) */
                if (is_null($exception)) {
                    $exception = $e;
                }

                if (($e instanceof Horde_Imap_Client_Exception_ServerResponse) &&
                    $e->command) {
                    --$cmd_count;
                }
            }
        }

        if (!is_null($exception)) {
            throw $exception;
        }
    }

    /**
     * Process/send a command to the remote server.
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline The pipeline
     *                                                         object.
     * @param Horde_Imap_Client_Interaction_Command $cmd  The master command.
     * @param Horde_Imap_Client_Data_Format_List $data    Commands to send.
     *
     * @return boolean  True if EOL needed to finish command.
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupport
     */
    protected function _processCmd($pipeline, $cmd, $data)
    {
        if ($this->_debug->debug &&
            ($data instanceof Horde_Imap_Client_Interaction_Command)) {
            $data->startTimer();
        }

        foreach ($data as $key => $val) {
            if ($val instanceof Horde_Imap_Client_Interaction_Command_Continuation) {
                $this->_connection->write('', true);

                /* Check for optional continuation responses when the command
                 * has already finished. */
                if (!$cmd_continuation = $this->_processCmdContinuation($pipeline, $val->optional)) {
                    return false;
                }

                $this->_processCmd(
                    $pipeline,
                    $cmd,
                    $val->getCommands($cmd_continuation)
                );
                continue;
            }

            if (!is_null($debug_msg = array_shift($cmd->debug))) {
                $this->_debug->client(
                    (($cmd == $data) ? $cmd->tag . ' ' : '') .  $debug_msg
                );
                $this->_connection->client_debug = false;
            }

            if ($key) {
                $this->_connection->write(' ');
            }

            if ($val instanceof Horde_Imap_Client_Data_Format_List) {
                $this->_connection->write('(');
                $this->_processCmd($pipeline, $cmd, $val);
                $this->_connection->write(')');
            } elseif (($val instanceof Horde_Imap_Client_Data_Format_String) &&
                      $val->literal()) {
                $c = $this->_capability();

                /* RFC 6855: If UTF8 extension is available, quote short
                 * strings instead of sending as literal. */
                if ($c->isEnabled('UTF8=ACCEPT') && ($val->length() < 100)) {
                    $val->forceQuoted();
                    $this->_connection->write($val->escape());
                } else {
                    /* RFC 3516/4466: Send literal8 if we have binary data. */
                    if ($cmd->literal8 &&
                        $val->binary() &&
                        ($c->query('BINARY') || $c->isEnabled('UTF8=ACCEPT'))) {
                        $binary = true;
                        $this->_connection->write('~');
                    } else {
                        $binary = false;
                    }

                    $literal_len = $val->length();
                    $this->_connection->write('{' . $literal_len);

                    /* RFC 2088 - If LITERAL+ is available, saves a roundtrip
                     * from the server. */
                    if ($cmd->literalplus && $c->query('LITERAL+')) {
                        $this->_connection->write('+}', true);
                    } else {
                        $this->_connection->write('}', true);
                        $this->_processCmdContinuation($pipeline);
                    }

                    if ($debug_msg) {
                        $this->_connection->client_debug = false;
                    }

                    $this->_connection->writeLiteral(
                        $val->getStream(),
                        $literal_len,
                        $binary
                    );
                }
            } else {
                $this->_connection->write($val->escape());
            }
        }

        return true;
    }

    /**
     * Process a command continuation response.
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  The pipeline
     *                                                          object.
     * @param boolean $noexception                              Don't throw
     *                                                          exception if
     *                                                          continuation
     *                                                          does not occur.
     *
     * @return mixed  A Horde_Imap_Client_Interaction_Server_Continuation
     *                object or false.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _processCmdContinuation($pipeline, $noexception = false)
    {
        do {
            $ob = $this->_getLine($pipeline);
        } while ($ob instanceof Horde_Imap_Client_Interaction_Server_Untagged);

        if ($ob instanceof Horde_Imap_Client_Interaction_Server_Continuation) {
            return $ob;
        } elseif ($noexception) {
            return false;
        }

        $this->_debug->info(
            'ERROR: Unexpected response from server while waiting for a continuation request.'
        );
        $e = new Horde_Imap_Client_Exception(
            Horde_Imap_Client_Translation::r("Error when communicating with the mail server."),
            Horde_Imap_Client_Exception::SERVER_READERROR
        );
        $e->details = strval($ob);

        throw $e;
    }

    /**
     * Shortcut to creating a new IMAP client command object.
     *
     * @param string $cmd  The IMAP command.
     *
     * @return Horde_Imap_Client_Interaction_Command  A command object.
     */
    protected function _command($cmd)
    {
        return new Horde_Imap_Client_Interaction_Command($cmd, ++$this->_tag);
    }

    /**
     * Shortcut to creating a new pipeline object.
     *
     * @param Horde_Imap_Client_Interaction_Command $cmd  An IMAP command to
     *                                                    add.
     *
     * @return Horde_Imap_Client_Interaction_Pipeline  A pipeline object.
     */
    protected function _pipeline($cmd = null)
    {
        if (!isset($this->_temp['fetchob'])) {
            $this->_temp['fetchob'] = new Horde_Imap_Client_Fetch_Results(
                $this->_fetchDataClass,
                Horde_Imap_Client_Fetch_Results::SEQUENCE
            );
        }

        $ob = new Horde_Imap_Client_Interaction_Pipeline(
            clone $this->_temp['fetchob']
        );

        if (!is_null($cmd)) {
            $ob->add($cmd);
        }

        return $ob;
    }

    /**
     * Gets data from the IMAP server stream and parses it.
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     *
     * @return Horde_Imap_Client_Interaction_Server  Server object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _getLine(
        Horde_Imap_Client_Interaction_Pipeline $pipeline
    )
    {
        $server = Horde_Imap_Client_Interaction_Server::create(
            $this->_connection->read()
        );

        switch (get_class($server)) {
        case 'Horde_Imap_Client_Interaction_Server_Continuation':
            $this->_responseCode($pipeline, $server);
            break;

        case 'Horde_Imap_Client_Interaction_Server_Tagged':
            $cmd = $pipeline->complete($server);
            if (is_null($cmd)) {
                /* This indicates a "dangling" tagged response - it was either
                 * generated by an aborted previous pipeline object or is the
                 * result of spurious output by the server. Ignore. */
                return $this->_getLine($pipeline);
            }

            if ($timer = $cmd->getTimer()) {
                $this->_debug->info(sprintf(
                    'Command %s took %s seconds.',
                    $cmd->tag,
                    $timer
                ));
            }
            $this->_responseCode($pipeline, $server);

            if (is_callable($cmd->on_success)) {
                call_user_func($cmd->on_success);
            }
            break;

        case 'Horde_Imap_Client_Interaction_Server_Untagged':
            if (is_null($server->status)) {
                $this->_serverResponse($pipeline, $server);
            } else {
                $this->_responseCode($pipeline, $server);
            }
            break;
        }

        switch ($server->status) {
        case $server::BAD:
        case $server::NO:
            /* A tagged BAD response indicates that the tagged command caused
             * the error. This information is unknown if untagged (RFC 3501
             * [7.1.3]) - ignore these untagged responses.
             * An untagged NO response indicates a warning; ignore and assume
             * that it also included response text code that is handled
             * elsewhere. Throw exception if tagged; command handlers can
             * catch this if able to workaround this issue (RFC 3501
             * [7.1.2]). */
            if ($server instanceof Horde_Imap_Client_Interaction_Server_Tagged) {
                /* Check for a on_error callback. If function returns true,
                 * ignore the error. */
                if (($cmd = $pipeline->getCmd($server->tag)) &&
                    is_callable($cmd->on_error) &&
                    call_user_func($cmd->on_error)) {
                    break;
                }

                throw new Horde_Imap_Client_Exception_ServerResponse(
                    Horde_Imap_Client_Translation::r("IMAP error reported by server."),
                    0,
                    $server,
                    $pipeline
                );
            }
            break;

        case $server::BYE:
            /* A BYE response received as part of a logout command should be
             * be treated like a regular command: a client MUST process the
             * entire command until logging out (RFC 3501 [3.4; 7.1.5]). */
            if (empty($this->_temp['logout'])) {
                $e = new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("IMAP Server closed the connection."),
                    Horde_Imap_Client_Exception::DISCONNECT
                );
                $e->details = strval($server);
                throw $e;
            }
            break;

        case $server::PREAUTH:
            /* The user was pre-authenticated. (RFC 3501 [7.1.4]) */
            $this->_temp['preauth'] = true;
            break;
        }

        return $server;
    }

    /**
     * Handle untagged server responses (see RFC 3501 [2.2.2]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Interaction_Server $ob          Server
     *                                                          response.
     */
    protected function _serverResponse(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Interaction_Server $ob
    )
    {
        $token = $ob->token;

        /* First, catch untagged responses where the name appears first on the
         * line. */
        switch ($first = Horde_String::upper($token->current())) {
        case 'CAPABILITY':
            $this->_parseCapability($pipeline, $token->flushIterator());
            break;

        case 'LIST':
        case 'LSUB':
            $this->_parseList($pipeline, $token);
            break;

        case 'STATUS':
            // Parse a STATUS response (RFC 3501 [7.2.4]).
            $this->_parseStatus($token);
            break;

        case 'SEARCH':
        case 'SORT':
            // Parse a SEARCH/SORT response (RFC 3501 [7.2.5] & RFC 5256 [4]).
            $this->_parseSearch($pipeline, $token->flushIterator());
            break;

        case 'ESEARCH':
            // Parse an ESEARCH response (RFC 4466 [2.6.2]).
            $this->_parseEsearch($pipeline, $token);
            break;

        case 'FLAGS':
            $token->next();
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_FLAGS, array_map('Horde_String::lower', $token->flushIterator()));
            break;

        case 'QUOTA':
            $this->_parseQuota($pipeline, $token);
            break;

        case 'QUOTAROOT':
            // Ignore this line - we can get this information from
            // the untagged QUOTA responses.
            break;

        case 'NAMESPACE':
            $this->_parseNamespace($pipeline, $token);
            break;

        case 'THREAD':
            $this->_parseThread($pipeline, $token);
            break;

        case 'ACL':
            $this->_parseACL($pipeline, $token);
            break;

        case 'LISTRIGHTS':
            $this->_parseListRights($pipeline, $token);
            break;

        case 'MYRIGHTS':
            $this->_parseMyRights($pipeline, $token);
            break;

        case 'ID':
            // ID extension (RFC 2971)
            $this->_parseID($pipeline, $token);
            break;

        case 'ENABLED':
            // ENABLE extension (RFC 5161)
            $this->_parseEnabled($token);
            break;

        case 'LANGUAGE':
            // LANGUAGE extension (RFC 5255 [3.2])
            $this->_parseLanguage($token);
            break;

        case 'COMPARATOR':
            // I18NLEVEL=2 extension (RFC 5255 [4.7])
            $this->_parseComparator($pipeline, $token);
            break;

        case 'VANISHED':
            // QRESYNC extension (RFC 7162 [3.2.10])
            $this->_parseVanished($pipeline, $token);
            break;

        case 'ANNOTATION':
            // Parse an ANNOTATION response.
            $this->_parseAnnotation($pipeline, $token);
            break;

        case 'METADATA':
            // Parse a METADATA response.
            $this->_parseMetadata($pipeline, $token);
            break;

        default:
            // Next, look for responses where the keywords occur second.
            switch (Horde_String::upper($token->next())) {
            case 'EXISTS':
                // EXISTS response - RFC 3501 [7.3.2]
                $mbox_ob = $this->_mailboxOb();

                // Increment UIDNEXT if it is set.
                if ($mbox_ob->open &&
                    ($uidnext = $mbox_ob->getStatus(Horde_Imap_Client::STATUS_UIDNEXT))) {
                    $mbox_ob->setStatus(Horde_Imap_Client::STATUS_UIDNEXT, $uidnext + $first - $mbox_ob->getStatus(Horde_Imap_Client::STATUS_MESSAGES));
                }

                $mbox_ob->setStatus(Horde_Imap_Client::STATUS_MESSAGES, $first);
                break;

            case 'RECENT':
                // RECENT response - RFC 3501 [7.3.1]
                $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_RECENT, $first);
                break;

            case 'EXPUNGE':
                // EXPUNGE response - RFC 3501 [7.4.1]
                $this->_deleteMsgs($this->_selected, $this->getIdsOb($first, true), array(
                    'decrement' => true,
                    'pipeline' => $pipeline
                ));
                $pipeline->data['expunge_seen'] = true;
                break;

            case 'FETCH':
                // FETCH response - RFC 3501 [7.4.2]
                $this->_parseFetch($pipeline, $first, $token);
                break;
            }
            break;
        }
    }

    /**
     * Handle status responses (see RFC 3501 [7.1]).
     *
     * @param Horde_Imap_Client_Interaction_Pipeline $pipeline  Pipeline
     *                                                          object.
     * @param Horde_Imap_Client_Interaction_Server $ob          Server object.
     *
     * @throws Horde_Imap_Client_Exception_ServerResponse
     */
    protected function _responseCode(
        Horde_Imap_Client_Interaction_Pipeline $pipeline,
        Horde_Imap_Client_Interaction_Server $ob
    )
    {
        if (is_null($ob->responseCode)) {
            return;
        }

        $rc = $ob->responseCode;

        switch ($rc->code) {
        case 'ALERT':
        // Defined by RFC 5530 [3] - Treat as an alert for now.
        case 'CONTACTADMIN':
        // Used by Gmail - Treat as an alert for now.
        // http://mailman13.u.washington.edu/pipermail/imap-protocol/2014-September/002324.html
        case 'WEBALERT':
            $this->_alerts->add(strval($ob->token), $rc->code);
            break;

        case 'BADCHARSET':
            /* Store valid search charsets if returned by server. */
            $s = $this->search_charset;
            foreach ($rc->data[0] as $val) {
                $s->setValid($val, true);
            }

            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("Charset used in search query is not supported on the mail server."),
                Horde_Imap_Client_Exception::BADCHARSET,
                $ob,
                $pipeline
            );

        case 'CAPABILITY':
            $this->_parseCapability($pipeline, $rc->data);
            break;

        case 'PARSE':
            /* Only throw error on NO/BAD. Message is human readable. */
            switch ($ob->status) {
            case Horde_Imap_Client_Interaction_Server::BAD:
            case Horde_Imap_Client_Interaction_Server::NO:
                $e = new Horde_Imap_Client_Exception_ServerResponse(
                    Horde_Imap_Client_Translation::r("The mail server was unable to parse the contents of the mail message: %s"),
                    Horde_Imap_Client_Exception::PARSEERROR,
                    $ob,
                    $pipeline
                );
                $e->messagePrintf(strval($ob->token));
                throw $e;
            }
            break;

        case 'READ-ONLY':
            $this->_mode = Horde_Imap_Client::OPEN_READONLY;
            break;

        case 'READ-WRITE':
            $this->_mode = Horde_Imap_Client::OPEN_READWRITE;
            break;

        case 'TRYCREATE':
            // RFC 3501 [7.1]
            $pipeline->data['trycreate'] = true;
            break;

        case 'PERMANENTFLAGS':
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_PERMFLAGS, array_map('Horde_String::lower', $rc->data[0]));
            break;

        case 'UIDNEXT':
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_UIDNEXT, $rc->data[0]);
            break;

        case 'UIDVALIDITY':
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_UIDVALIDITY, $rc->data[0]);
            break;

        case 'UNSEEN':
            /* This is different from the STATUS UNSEEN response - this item,
             * if defined, returns the first UNSEEN message in the mailbox. */
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_FIRSTUNSEEN, $rc->data[0]);
            break;

        case 'REFERRAL':
            // Defined by RFC 2221
            $pipeline->data['referral'] = new Horde_Imap_Client_Url_Imap($rc->data[0]);
            break;

        case 'UNKNOWN-CTE':
            // Defined by RFC 3516
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The mail server was unable to parse the contents of the mail message."),
                Horde_Imap_Client_Exception::UNKNOWNCTE,
                $ob,
                $pipeline
            );

        case 'APPENDUID':
            // Defined by RFC 4315
            // APPENDUID: [0] = UIDVALIDITY, [1] = UID(s)
            $pipeline->data['appenduid'] = $this->getIdsOb($rc->data[1]);
            break;

        case 'COPYUID':
            // Defined by RFC 4315
            // COPYUID: [0] = UIDVALIDITY, [1] = UIDFROM, [2] = UIDTO
            $pipeline->data['copyuid'] = array_combine(
                $this->getIdsOb($rc->data[1])->ids,
                $this->getIdsOb($rc->data[2])->ids
            );

            /* Use UIDPLUS information to move cached data to new mailbox (see
             * RFC 4549 [4.2.2.1]). Need to move now, because a MOVE might
             * EXPUNGE immediately afterwards. */
            $this->_moveCache($pipeline->data['copydest'], $pipeline->data['copyuid'], $rc->data[0]);
            break;

        case 'UIDNOTSTICKY':
            // Defined by RFC 4315 [3]
            $this->_mailboxOb()->setStatus(Horde_Imap_Client::STATUS_UIDNOTSTICKY, true);
            break;

        case 'BADURL':
            // Defined by RFC 4469 [4.1]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("Could not save message on server."),
                Horde_Imap_Client_Exception::CATENATE_BADURL,
                $ob,
                $pipeline
            );

        case 'TOOBIG':
            // Defined by RFC 4469 [4.2]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("Could not save message data because it is too large."),
                Horde_Imap_Client_Exception::CATENATE_TOOBIG,
                $ob,
                $pipeline
            );

        case 'HIGHESTMODSEQ':
            // Defined by RFC 7162 [3.1.2.1]
            $pipeline->data['modseqs'][] = $rc->data[0];
            break;

        case 'NOMODSEQ':
            // Defined by RFC 7162 [3.1.2.2]
            $pipeline->data['modseqs'][] = 0;
            break;

        case 'MODIFIED':
            // Defined by RFC 7162 [3.1.3]
            $pipeline->data['modified']->add($rc->data[0]);
            break;

        case 'CLOSED':
            // Defined by RFC 7162 [3.2.11]
            if (isset($pipeline->data['qresyncmbox'])) {
                /* If there is any pending FETCH cache entries, flush them
                 * now before changing mailboxes. */
                $this->_updateCache($pipeline->fetch);
                $pipeline->fetch->clear();

                $this->_changeSelected(
                    $pipeline->data['qresyncmbox'][0],
                    $pipeline->data['qresyncmbox'][1]
                );
                unset($pipeline->data['qresyncmbox']);
            }
            break;

        case 'NOTSAVED':
            // Defined by RFC 5182 [2.5]
            $pipeline->data['searchnotsaved'] = true;
            break;

        case 'BADCOMPARATOR':
            // Defined by RFC 5255 [4.9]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The comparison algorithm was not recognized by the server."),
                Horde_Imap_Client_Exception::BADCOMPARATOR,
                $ob,
                $pipeline
            );

        case 'METADATA':
            $md = $rc->data[0];

            switch ($md[0]) {
            case 'LONGENTRIES':
                // Defined by RFC 5464 [4.2.1]
                $pipeline->data['metadata']['*longentries'] = intval($md[1]);
                break;

            case 'MAXSIZE':
                // Defined by RFC 5464 [4.3]
                throw new Horde_Imap_Client_Exception_ServerResponse(
                    Horde_Imap_Client_Translation::r("The metadata item could not be saved because it is too large."),
                    Horde_Imap_Client_Exception::METADATA_MAXSIZE,
                    $ob,
                    $pipeline
                );

            case 'NOPRIVATE':
                // Defined by RFC 5464 [4.3]
                throw new Horde_Imap_Client_Exception_ServerResponse(
                    Horde_Imap_Client_Translation::r("The metadata item could not be saved because the server does not support private annotations."),
                    Horde_Imap_Client_Exception::METADATA_NOPRIVATE,
                    $ob,
                    $pipeline
                );

            case 'TOOMANY':
                // Defined by RFC 5464 [4.3]
                throw new Horde_Imap_Client_Exception_ServerResponse(
                    Horde_Imap_Client_Translation::r("The metadata item could not be saved because the maximum number of annotations has been exceeded."),
                    Horde_Imap_Client_Exception::METADATA_TOOMANY,
                    $ob,
                    $pipeline
                );
            }
            break;

        case 'UNAVAILABLE':
            // Defined by RFC 5530 [3]
            $pipeline->data['loginerr'] = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Remote server is temporarily unavailable."),
                Horde_Imap_Client_Exception::LOGIN_UNAVAILABLE
            );
            break;

        case 'AUTHENTICATIONFAILED':
            // Defined by RFC 5530 [3]
            $pipeline->data['loginerr'] = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Authentication failed."),
                Horde_Imap_Client_Exception::LOGIN_AUTHENTICATIONFAILED
            );
            break;

        case 'AUTHORIZATIONFAILED':
            // Defined by RFC 5530 [3]
            $pipeline->data['loginerr'] = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Authentication was successful, but authorization failed."),
                Horde_Imap_Client_Exception::LOGIN_AUTHORIZATIONFAILED
            );
            break;

        case 'EXPIRED':
            // Defined by RFC 5530 [3]
            $pipeline->data['loginerr'] = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Authentication credentials have expired."),
                Horde_Imap_Client_Exception::LOGIN_EXPIRED
            );
            break;

        case 'PRIVACYREQUIRED':
            // Defined by RFC 5530 [3]
            $pipeline->data['loginerr'] = new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Operation failed due to a lack of a secure connection."),
                Horde_Imap_Client_Exception::LOGIN_PRIVACYREQUIRED
            );
            break;

        case 'NOPERM':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("You do not have adequate permissions to carry out this operation."),
                Horde_Imap_Client_Exception::NOPERM,
                $ob,
                $pipeline
            );

        case 'INUSE':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("There was a temporary issue when attempting this operation. Please try again later."),
                Horde_Imap_Client_Exception::INUSE,
                $ob,
                $pipeline
            );

        case 'EXPUNGEISSUED':
            // Defined by RFC 5530 [3]
            $pipeline->data['expungeissued'] = true;
            break;

        case 'CORRUPTION':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The mail server is reporting corrupt data in your mailbox."),
                Horde_Imap_Client_Exception::CORRUPTION,
                $ob,
                $pipeline
            );

        case 'SERVERBUG':
        case 'CLIENTBUG':
        case 'CANNOT':
            // Defined by RFC 5530 [3]
            $this->_debug->info(
                'ERROR: mail server explicitly reporting an error.'
            );
            break;

        case 'LIMIT':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The mail server has denied the request."),
                Horde_Imap_Client_Exception::LIMIT,
                $ob,
                $pipeline
            );

        case 'OVERQUOTA':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The operation failed because the quota has been exceeded on the mail server."),
                Horde_Imap_Client_Exception::OVERQUOTA,
                $ob,
                $pipeline
            );

        case 'ALREADYEXISTS':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The object could not be created because it already exists."),
                Horde_Imap_Client_Exception::ALREADYEXISTS,
                $ob,
                $pipeline
            );

        case 'NONEXISTENT':
            // Defined by RFC 5530 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The object could not be deleted because it does not exist."),
                Horde_Imap_Client_Exception::NONEXISTENT,
                $ob,
                $pipeline
            );

        case 'USEATTR':
            // Defined by RFC 6154 [3]
            throw new Horde_Imap_Client_Exception_ServerResponse(
                Horde_Imap_Client_Translation::r("The special-use attribute requested for the mailbox is not supported."),
                Horde_Imap_Client_Exception::USEATTR,
                $ob,
                $pipeline
            );

        case 'DOWNGRADED':
            // Defined by RFC 6858 [3]
            $downgraded = $this->getIdsOb($rc->data[0]);
            foreach ($pipeline->fetch as $val) {
                if (in_array($val->getUid(), $downgraded)) {
                    $val->setDowngraded(true);
                }
            }
            break;

        case 'XPROXYREUSE':
            // The proxy connection was reused, so no need to do login tasks.
            $pipeline->data['proxyreuse'] = true;
            break;

        default:
            // Unknown response codes SHOULD be ignored - RFC 3501 [7.1]
            break;
        }
    }

}
