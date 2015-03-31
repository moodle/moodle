<?php
/**
 * Copyright 2008-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2008-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * An abstracted API interface to IMAP backends supporting the IMAP4rev1
 * protocol (RFC 3501).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2008-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
abstract class Horde_Imap_Client_Base implements Serializable
{
    /** Serialized version. */
    const VERSION = 2;

    /** Cache names for miscellaneous data. */
    const CACHE_MODSEQ = '_m';
    const CACHE_SEARCH = '_s';
    /* @since 2.9.0 */
    const CACHE_SEARCHID = '_i';

    /** Cache names used exclusively within this class. @since 2.11.0 */
    const CACHE_DOWNGRADED = 'HICdg';

    /**
     * The list of fetch fields that can be cached, and their cache names.
     *
     * @var array
     */
    public $cacheFields = array(
        Horde_Imap_Client::FETCH_ENVELOPE => 'HICenv',
        Horde_Imap_Client::FETCH_FLAGS => 'HICflags',
        Horde_Imap_Client::FETCH_HEADERS => 'HIChdrs',
        Horde_Imap_Client::FETCH_IMAPDATE => 'HICdate',
        Horde_Imap_Client::FETCH_SIZE => 'HICsize',
        Horde_Imap_Client::FETCH_STRUCTURE => 'HICstruct'
    );

    /**
     * Has the internal configuration changed?
     *
     * @var boolean
     */
    public $changed = false;

    /**
     * Horde_Imap_Client is optimized for short (i.e. 1 seconds) scripts. It
     * makes heavy use of mailbox caching to save on server accesses. This
     * property should be set to false for long-running scripts, or else
     * status() data may not reflect the current state of the mailbox on the
     * server.
     *
     * @since 2.14.0
     *
     * @var boolean
     */
    public $statuscache = true;

    /**
     * The Horde_Imap_Client_Cache object.
     *
     * @var Horde_Imap_Client_Cache
     */
    protected $_cache = null;

    /**
     * Connection to the IMAP server.
     *
     * @var Horde\Socket\Client
     */
    protected $_connection = null;

    /**
     * The debug object.
     *
     * @var Horde_Imap_Client_Base_Debug
     */
    protected $_debug = null;

    /**
     * The default ports to use for a connection.
     * First element is non-secure, second is SSL.
     *
     * @var array
     */
    protected $_defaultPorts = array();

    /**
     * The fetch data object type to return.
     *
     * @var string
     */
    protected $_fetchDataClass = 'Horde_Imap_Client_Data_Fetch';

    /**
     * Cached server data.
     *
     * @var array
     */
    protected $_init;

    /**
     * Is there an active authenticated connection to the IMAP Server?
     *
     * @var boolean
     */
    protected $_isAuthenticated = false;

    /**
     * The current mailbox selection mode.
     *
     * @var integer
     */
    protected $_mode = 0;

    /**
     * Hash containing connection parameters.
     * This hash never changes.
     *
     * @var array
     */
    protected $_params = array();

    /**
     * The currently selected mailbox.
     *
     * @var Horde_Imap_Client_Mailbox
     */
    protected $_selected = null;

    /**
     * Temp array (destroyed at end of process).
     *
     * @var array
     */
    protected $_temp = array(
        'enabled' => array()
    );

    /**
     * Constructor.
     *
     * @param array $params   Configuration parameters:
     * <pre>
     * - cache: (array) If set, caches data from fetch(), search(), and
     *          thread() calls. Requires the horde/Cache package to be
     *          installed. The array can contain the following keys (see
     *          Horde_Imap_Client_Cache for default values):
     *   - backend: [REQUIRED (or cacheob)] (Horde_Imap_Client_Cache_Backend)
     *              Backend cache driver [@since 2.9.0].
     *   - fetch_ignore: (array) A list of mailboxes to ignore when storing
     *                   fetch data.
     *   - fields: (array) The fetch criteria to cache. If not defined, all
     *             cacheable data is cached. The following is a list of
     *             criteria that can be cached:
     *     - Horde_Imap_Client::FETCH_ENVELOPE
     *     - Horde_Imap_Client::FETCH_FLAGS
     *       Only if server supports CONDSTORE extension
     *     - Horde_Imap_Client::FETCH_HEADERS
     *       Only for queries that specifically request caching
     *     - Horde_Imap_Client::FETCH_IMAPDATE
     *     - Horde_Imap_Client::FETCH_SIZE
     *     - Horde_Imap_Client::FETCH_STRUCTURE
     * - capability_ignore: (array) A list of IMAP capabilites to ignore, even
     *                      if they are supported on the server.
     *                      DEFAULT: No supported capabilities are ignored.
     * - comparator: (string) The search comparator to use instead of the
     *               default server comparator. See setComparator() for
     *               format.
     *               DEFAULT: Use the server default
     * - debug: (string) If set, will output debug information to the stream
     *          provided. The value can be any PHP supported wrapper that can
     *          be opened via PHP's fopen() function.
     *          DEFAULT: No debug output
     * - hostspec: (string) The hostname or IP address of the server.
     *             DEFAULT: 'localhost'
     * - id: (array) Send ID information to the server (only if server
     *       supports the ID extension). An array with the keys as the fields
     *       to send and the values being the associated values. See RFC 2971
     *       [3.3] for a list of standard field values.
     *       DEFAULT: No info sent to server
     * - lang: (array) A list of languages (in priority order) to be used to
     *         display human readable messages.
     *         DEFAULT: Messages output in IMAP server default language
     * - password: (mixed) The user password. Either a string or a
     *             Horde_Imap_Client_Base_Password object [@since 2.14.0].
     * - port: (integer) The server port to which we will connect.
     *         DEFAULT: 143 (imap or imap w/TLS) or 993 (imaps)
     * - secure: (string) Use SSL or TLS to connect. Values:
     *   - false (No encryption)
     *   - 'ssl' (Auto-detect SSL version)
     *   - 'sslv2' (Force SSL version 3)
     *   - 'sslv3' (Force SSL version 2)
     *   - 'tls' (TLS; started via protocol-level negotation over
     *     unencrypted channel; RECOMMENDED way of initiating secure
     *     connection)
     *   - 'tlsv1' (TLS direct version 1.x connection to server) [@since
     *     2.16.0]
     *   - true (TLS if available/necessary) [@since 2.15.0]
     *     DEFAULT: false
     * - timeout: (integer)  Connection timeout, in seconds.
     *            DEFAULT: 30 seconds
     * - username: (string) [REQUIRED] The username.
     * </pre>
     */
    public function __construct(array $params = array())
    {
        if (!isset($params['username'])) {
            throw new InvalidArgumentException('Horde_Imap_Client requires a username.');
        }

        $this->_setInit();

        // Default values.
        $params = array_merge(array(
            'hostspec' => 'localhost',
            'secure' => false,
            'timeout' => 30
        ), array_filter($params));

        if (!isset($params['port'])) {
            $params['port'] = (!empty($params['secure']) && in_array($params['secure'], array('ssl', 'sslv2', 'sslv3'), true))
                ? $this->_defaultPorts[1]
                : $this->_defaultPorts[0];
        }

        if (empty($params['cache'])) {
            $params['cache'] = array('fields' => array());
        } elseif (empty($params['cache']['fields'])) {
            $params['cache']['fields'] = $this->cacheFields;
        } else {
            $params['cache']['fields'] = array_flip($params['cache']['fields']);
        }

        if (empty($params['cache']['fetch_ignore'])) {
            $params['cache']['fetch_ignore'] = array();
        }

        $this->_params = $params;
        if (isset($params['password'])) {
            $this->setParam('password', $params['password']);
        }

        $this->changed = true;
        $this->_initOb();
    }

    /**
     * Get encryption key.
     *
     * @deprecated  Pass callable into 'password' parameter instead.
     *
     * @return string  The encryption key.
     */
    protected function _getEncryptKey()
    {
        if (is_callable($ekey = $this->getParam('encryptKey'))) {
            return call_user_func($ekey);
        }

        throw new InvalidArgumentException('encryptKey parameter is not a valid callback.');
    }

    /**
     * Do initialization tasks.
     */
    protected function _initOb()
    {
        register_shutdown_function(array($this, 'shutdown'));
        $this->_debug = ($debug = $this->getParam('debug'))
            ? new Horde_Imap_Client_Base_Debug($debug)
            : new Horde_Support_Stub();
    }

    /**
     * Shutdown actions.
     */
    public function shutdown()
    {
        $this->logout();
    }

    /**
     * This object can not be cloned.
     */
    public function __clone()
    {
        throw new LogicException('Object cannot be cloned.');
    }

    /**
     */
    public function serialize()
    {
        return serialize(array(
            'i' => $this->_init,
            'p' => $this->_params,
            'v' => self::VERSION
        ));
    }

    /**
     */
    public function unserialize($data)
    {
        $data = @unserialize($data);
        if (!is_array($data) ||
            !isset($data['v']) ||
            ($data['v'] != self::VERSION)) {
            throw new Exception('Cache version change');
        }

        $this->_init = $data['i'];
        $this->_params = $data['p'];

        $this->_initOb();
    }

    /**
     * Set an initialization value.
     *
     * @param string $key  The initialization key. If null, resets all keys.
     * @param mixed $val   The cached value. If null, removes the key.
     */
    public function _setInit($key = null, $val = null)
    {
        if (is_null($key)) {
            $this->_init = array(
                'namespace' => array(),
                's_charset' => array()
            );
        } elseif (is_null($val)) {
            unset($this->_init[$key]);

            switch ($key) {
            case 'capability':
                unset($this->_init['cmdlength']);
                break;
            }
        } else {
            switch ($key) {
            case 'capability':
                if ($ci = $this->getParam('capability_ignore')) {
                    if ($this->_debug->debug &&
                        ($ignored = array_intersect_key($val, array_flip($ci)))) {
                        $this->_debug->info(sprintf(
                            'CONFIG: IGNORING these IMAP capabilities: %s',
                            implode(', ', array_keys($ignored))
                        ));
                    }

                    $val = array_diff_key($val, array_flip($ci));
                }

                /* RFC 7162 [3.2.3] - QRESYNC implies CONDSTORE and ENABLE,
                 * even if not listed as a capability. */
                if (!empty($val['QRESYNC'])) {
                    $val['CONDSTORE'] = true;
                    $val['ENABLE'] = true;
                }

                /* RFC 2683 [3.2.1.5] originally recommended that lines should
                 * be limited to "approximately 1000 octets". However, servers
                 * should allow a command line of at least "8000 octets".
                 * RFC 7162 [4] updates the recommendation to 8192 octets.
                 * As a compromise, assume all modern IMAP servers handle
                 * ~2000 octets and, if CONDSTORE/ENABLE is supported, assume
                 * they can handle ~8000 octets. */
                $this->_init['cmdlength'] = (isset($val['CONDSTORE']) || isset($val['QRESYNC']))
                    ? 8000
                    : 2000;
                break;
            }

            /* Nothing has changed. */
            if (isset($this->_init[$key]) && ($this->_init[$key] == $val)) {
                return;
            }

            $this->_init[$key] = $val;
        }

        $this->changed = true;
    }

    /**
     * Set the list of enabled extensions.
     *
     * @param array $exts      The list of extensions.
     * @param integer $status  1 means to report as ENABLED, although it has
     *                         not been formally enabled on server yet. 2 is
     *                         verified enabled on the server.
     */
    protected function _enabled($exts, $status)
    {
        /* RFC 7162 [3.2.3] - Enabling QRESYNC also implies enabling of
         * CONDSTORE. */
        if (in_array('QRESYNC', $exts)) {
            $exts[] = 'CONDSTORE';
        }

        switch ($status) {
        case 2:
            $enabled_list = array_intersect(array(2), $this->_temp['enabled']);
            break;

        case 1:
        default:
            $enabled_list = $this->_temp['enabled'];
            $status = 1;
            break;
        }

        $this->_temp['enabled'] = array_merge(
            $enabled_list,
            array_fill_keys($exts, $status)
        );
    }

    /**
     * Initialize the Horde_Imap_Client_Cache object, if necessary.
     *
     * @param boolean $current  If true, we are going to update the currently
     *                          selected mailbox. Add an additional check to
     *                          see if caching is available in current
     *                          mailbox.
     *
     * @return boolean  Returns true if caching is enabled.
     */
    protected function _initCache($current = false)
    {
        $c = $this->getParam('cache');

        if (empty($c['fields'])) {
            return false;
        }

        if (is_null($this->_cache)) {
            if (isset($c['backend'])) {
                $backend = $c['backend'];
            } elseif (isset($c['cacheob'])) {
                /* Deprecated */
                $backend = new Horde_Imap_Client_Cache_Backend_Cache($c);
            } else {
                return false;
            }

            $this->_cache = new Horde_Imap_Client_Cache(array(
                'backend' => $backend,
                'baseob' => $this,
                'debug' => $this->_debug
            ));
        }

        return $current
            /* If UIDs are labeled as not sticky, don't cache since UIDs will
             * change on every access. */
            ? !($this->_mailboxOb()->getStatus(Horde_Imap_Client::STATUS_UIDNOTSTICKY))
            : true;
    }

    /**
     * Returns a value from the internal params array.
     *
     * @param string $key  The param key.
     *
     * @return mixed  The param value, or null if not found.
     */
    public function getParam($key)
    {
        /* Passwords may be stored encrypted. */
        switch ($key) {
        case 'password':
            if (isset($this->_params[$key]) &&
                ($this->_params[$key] instanceof Horde_Imap_Client_Base_Password)) {
                return $this->_params[$key]->getPassword();
            }

            // DEPRECATED
            if (!empty($this->_params['_passencrypt'])) {
                try {
                    $secret = new Horde_Secret();
                    return $secret->read($this->_getEncryptKey(), $this->_params['password']);
                } catch (Exception $e) {
                    return null;
                }
            }
            break;
        }

        return isset($this->_params[$key])
            ? $this->_params[$key]
            : null;
    }

    /**
     * Sets a configuration parameter value.
     *
     * @param string $key  The param key.
     * @param mixed $val   The param value.
     */
    public function setParam($key, $val)
    {
        switch ($key) {
        case 'password':
            if ($val instanceof Horde_Imap_Client_Base_Password) {
                break;
            }

            // DEPRECATED: Encrypt password.
            try {
                $encrypt_key = $this->_getEncryptKey();
                if (strlen($encrypt_key)) {
                    $secret = new Horde_Secret();
                    $val = $secret->write($encrypt_key, $val);
                    $this->_params['_passencrypt'] = true;
                }
            } catch (Exception $e) {}
            break;
        }

        $this->_params[$key] = $val;
        $this->changed = true;
    }

    /**
     * Returns the Horde_Imap_Client_Cache object used, if available.
     *
     * @return mixed  Either the cache object or null.
     */
    public function getCache()
    {
        $this->_initCache();
        return $this->_cache;
    }

    /**
     * Returns the correct IDs object for use with this driver.
     *
     * @param mixed $ids         See add().
     * @param boolean $sequence  Are $ids message sequence numbers?
     *
     * @return Horde_Imap_Client_Ids  The IDs object.
     */
    public function getIdsOb($ids = null, $sequence = false)
    {
        return new Horde_Imap_Client_Ids($ids, $sequence);
    }

    /**
     * Returns whether the IMAP server supports the given capability
     * (See RFC 3501 [6.1.1]).
     *
     * @param string $capability  The capability string to query.
     *
     * @return mixed  True if the server supports the queried capability,
     *                false if it doesn't, or an array if the capability can
     *                contain multiple values.
     */
    public function queryCapability($capability)
    {
        // @todo: Remove this catch(); if capability fails due to connection
        // error, should throw an exception.
        try {
            $this->capability();
        } catch (Horde_Imap_Client_Exception $e) {
            return false;
        }

        $capability = strtoupper($capability);

        if (!isset($this->_init['capability'][$capability])) {
            return false;
        }

        /* Check for capability requirements. */
        if (isset(Horde_Imap_Client::$capability_deps[$capability])) {
            foreach (Horde_Imap_Client::$capability_deps[$capability] as $val) {
                if (!$this->queryCapability($val)) {
                    return false;
                }
            }
        }

        return $this->_init['capability'][$capability];
    }

    /**
     * Get CAPABILITY information from the IMAP server.
     *
     * @return array  The capability array.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function capability()
    {
        if (!isset($this->_init['capability'])) {
            $this->_capability();
        }

        return $this->_init['capability'];
    }

    /**
     * Retrieve CAPABILITY information from the IMAP server.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _capability();

    /**
     * Send a NOOP command (RFC 3501 [6.1.2]).
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function noop()
    {
        if (!$this->_connection) {
            // NOOP can be called in the unauthenticated state.
            $this->_connect();
        }
        $this->_noop();
    }

    /**
     * Send a NOOP command.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _noop();

    /**
     * Get the NAMESPACE information from the IMAP server (RFC 2342).
     *
     * @param array $additional  If the server supports namespaces, any
     *                           additional namespaces to add to the
     *                           namespace list that are not broadcast by
     *                           the server. The namespaces must be UTF-8
     *                           strings.
     * @param array $opts        Additional options:
     *   - ob_return: (boolean) If true, returns a
     *                Horde_Imap_Client_Namespace_List object instead of an
     *                array.
     *
     * @return mixed  A Horde_Imap_Client_Namespace_List object if
     *                'ob_return', is true. Otherwise, an array of namespace
     *                objects (@deprecated) with the name as the key (UTF-8)
     *                and the following values:
     * <pre>
     *  - delimiter: (string) The namespace delimiter.
     *  - hidden: (boolean) Is this a hidden namespace?
     *  - name: (string) The namespace name (UTF-8).
     *  - translation: (string) Returns the translated name of the namespace
     *                 (UTF-8). Requires RFC 5255 and a previous call to
     *                 setLanguage().
     *  - type: (integer) The namespace type. Either:
     *    - Horde_Imap_Client::NS_PERSONAL
     *    - Horde_Imap_Client::NS_OTHER
     *    - Horde_Imap_Client::NS_SHARED
     * </pre>
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getNamespaces(
        array $additional = array(), array $opts = array()
    )
    {
        $additional = array_map('strval', $additional);
        $sig = hash(
            (PHP_MINOR_VERSION >= 4) ? 'fnv132' : 'sha1',
            json_encode($additional) . intval(empty($opts['ob_return']))
        );

        if (isset($this->_init['namespace'][$sig])) {
            $ns = $this->_init['namespace'][$sig];
        } else {
            $this->login();

            $ns = $this->_getNamespaces();

            /* Skip namespaces if we have already auto-detected them. Also,
             * hidden namespaces cannot be empty. */
            $to_process = array_diff(array_filter($additional, 'strlen'), array_map('strlen', iterator_to_array($ns)));
            if (!empty($to_process)) {
                foreach ($this->listMailboxes($to_process, Horde_Imap_Client::MBOX_ALL, array('delimiter' => true)) as $val) {
                    $ob = new Horde_Imap_Client_Data_Namespace();
                    $ob->delimiter = $val['delimiter'];
                    $ob->hidden = true;
                    $ob->name = $val;
                    $ob->type = $ob::NS_SHARED;
                    $ns[$val] = $ob;
                }
            }

            if (!count($ns)) {
                /* This accurately determines the namespace information of the
                 * base namespace if the NAMESPACE command is not supported.
                 * See: RFC 3501 [6.3.8] */
                $mbox = $this->listMailboxes('', Horde_Imap_Client::MBOX_ALL, array('delimiter' => true));
                $first = reset($mbox);

                $ob = new Horde_Imap_Client_Data_Namespace();
                $ob->delimiter = $first['delimiter'];
                $ns[''] = $ob;
            }

            $this->_init['namespace'][$sig] = $ns;
            $this->_setInit('namespace', $this->_init['namespace']);
        }

        if (!empty($opts['ob_return'])) {
            return $ns;
        }

        /* @todo Remove for 3.0 */
        $out = array();
        foreach ($ns as $key => $val) {
            $out[$key] = array(
                'delimiter' => $val->delimiter,
                'hidden' => $val->hidden,
                'name' => $val->name,
                'translation' => $val->translation,
                'type' => $val->type
            );
        }

        return $out;
    }

    /**
     * Get the NAMESPACE information from the IMAP server.
     *
     * @return Horde_Imap_Client_Namespace_List  Namespace list object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getNamespaces();

    /**
     * Display if connection to the server has been secured via TLS or SSL.
     *
     * @return boolean  True if the IMAP connection is secured.
     */
    public function isSecureConnection()
    {
        return ($this->_connection && $this->_connection->secure);
    }

    /**
     * Connect to the remote server.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _connect();

    /**
     * Return a list of alerts that MUST be presented to the user (RFC 3501
     * [7.1]).
     *
     * @return array  An array of alert messages.
     */
    abstract public function alerts();

    /**
     * Login to the IMAP server.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function login()
    {
        if (!$this->_isAuthenticated && $this->_login()) {
            if ($this->getParam('id')) {
                try {
                    $this->sendID();
                } catch (Horde_Imap_Client_Exception_NoSupportExtension $e) {
                    // Ignore if server doesn't support ID extension.
                }
            }

            if ($this->getParam('comparator')) {
                try {
                    $this->setComparator();
                } catch (Horde_Imap_Client_Exception_NoSupportExtension $e) {
                    // Ignore if server doesn't support I18NLEVEL=2
                }
            }
        }

        $this->_isAuthenticated = true;
    }

    /**
     * Login to the IMAP server.
     *
     * @return boolean  Return true if global login tasks should be run.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _login();

    /**
     * Logout from the IMAP server (see RFC 3501 [6.1.3]).
     */
    public function logout()
    {
        if ($this->_isAuthenticated && $this->_connection->connected) {
            $this->_logout();
            $this->_connection->close();
        }

        $this->_connection = $this->_selected = null;
        $this->_isAuthenticated = false;
        $this->_mode = 0;
    }

    /**
     * Logout from the IMAP server (see RFC 3501 [6.1.3]).
     */
    abstract protected function _logout();

    /**
     * Send ID information to the IMAP server (RFC 2971).
     *
     * @param array $info  Overrides the value of the 'id' param and sends
     *                     this information instead.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function sendID($info = null)
    {
        if (!$this->queryCapability('ID')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('ID');
        }

        $this->_sendID(is_null($info) ? ($this->getParam('id') ?: array()) : $info);
    }

    /**
     * Send ID information to the IMAP server (RFC 2971).
     *
     * @param array $info  The information to send to the server.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _sendID($info);

    /**
     * Return ID information from the IMAP server (RFC 2971).
     *
     * @return array  An array of information returned, with the keys as the
     *                'field' and the values as the 'value'.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function getID()
    {
        if (!$this->queryCapability('ID')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('ID');
        }

        return $this->_getID();
    }

    /**
     * Return ID information from the IMAP server (RFC 2971).
     *
     * @return array  An array of information returned, with the keys as the
     *                'field' and the values as the 'value'.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getID();

    /**
     * Sets the preferred language for server response messages (RFC 5255).
     *
     * @param array $langs  Overrides the value of the 'lang' param and sends
     *                      this list of preferred languages instead. The
     *                      special string 'i-default' can be used to restore
     *                      the language to the server default.
     *
     * @return string  The language accepted by the server, or null if the
     *                 default language is used.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function setLanguage($langs = null)
    {
        $lang = null;

        if ($this->queryCapability('LANGUAGE')) {
            $lang = is_null($langs)
                ? $this->getParam('lang')
                : $langs;
        }

        return is_null($lang)
            ? null
            : $this->_setLanguage($lang);
    }

    /**
     * Sets the preferred language for server response messages (RFC 5255).
     *
     * @param array $langs  The preferred list of languages.
     *
     * @return string  The language accepted by the server, or null if the
     *                 default language is used.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _setLanguage($langs);

    /**
     * Gets the preferred language for server response messages (RFC 5255).
     *
     * @param array $list  If true, return the list of available languages.
     *
     * @return mixed  If $list is true, the list of languages available on the
     *                server (may be empty). If false, the language used by
     *                the server, or null if the default language is used.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getLanguage($list = false)
    {
        if (!$this->queryCapability('LANGUAGE')) {
            return $list ? array() : null;
        }

        return $this->_getLanguage($list);
    }

    /**
     * Gets the preferred language for server response messages (RFC 5255).
     *
     * @param array $list  If true, return the list of available languages.
     *
     * @return mixed  If $list is true, the list of languages available on the
     *                server (may be empty). If false, the language used by
     *                the server, or null if the default language is used.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getLanguage($list);

    /**
     * Open a mailbox.
     *
     * @param mixed $mailbox  The mailbox to open. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param integer $mode   The access mode. Either
     *   - Horde_Imap_Client::OPEN_READONLY
     *   - Horde_Imap_Client::OPEN_READWRITE
     *   - Horde_Imap_Client::OPEN_AUTO
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function openMailbox($mailbox, $mode = Horde_Imap_Client::OPEN_AUTO)
    {
        $this->login();

        $change = false;
        $mailbox = Horde_Imap_Client_Mailbox::get($mailbox);

        if ($mode == Horde_Imap_Client::OPEN_AUTO) {
            if (is_null($this->_selected) ||
                !$mailbox->equals($this->_selected)) {
                $mode = Horde_Imap_Client::OPEN_READONLY;
                $change = true;
            }
        } else {
            $change = (is_null($this->_selected) ||
                       !$mailbox->equals($this->_selected) ||
                       ($mode != $this->_mode));
        }

        if ($change) {
            $this->_openMailbox($mailbox, $mode);
            $this->_mailboxOb()->open = true;
            if ($this->_initCache(true)) {
                $this->_condstoreSync();
            }
        }
    }

    /**
     * Open a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox to open.
     * @param integer $mode                       The access mode.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _openMailbox(Horde_Imap_Client_Mailbox $mailbox,
                                             $mode);

    /**
     * Called when the selected mailbox is changed.
     *
     * @param mixed $mailbox  The selected mailbox or null.
     * @param integer $mode   The access mode.
     */
    protected function _changeSelected($mailbox = null, $mode = null)
    {
        $this->_mode = $mode;
        if (is_null($mailbox)) {
            $this->_selected = null;
        } else {
            $this->_selected = clone $mailbox;
            $this->_mailboxOb()->reset();
        }
    }

    /**
     * Return the Horde_Imap_Client_Base_Mailbox object.
     *
     * @param string $mailbox  The mailbox name. Defaults to currently
     *                         selected mailbox.
     *
     * @return Horde_Imap_Client_Base_Mailbox  Mailbox object.
     */
    protected function _mailboxOb($mailbox = null)
    {
        $name = is_null($mailbox)
            ? strval($this->_selected)
            : strval($mailbox);

        if (!isset($this->_temp['mailbox_ob'][$name])) {
            $this->_temp['mailbox_ob'][$name] = new Horde_Imap_Client_Base_Mailbox();
        }

        return $this->_temp['mailbox_ob'][$name];
    }

    /**
     * Return the currently opened mailbox and access mode.
     *
     * @return mixed  Null if no mailbox selected, or an array with two
     *                elements:
     *   - mailbox: (Horde_Imap_Client_Mailbox) The mailbox object.
     *   - mode: (integer) Current mode.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function currentMailbox()
    {
        return is_null($this->_selected)
            ? null
            : array(
                'mailbox' => clone $this->_selected,
                'mode' => $this->_mode
            );
    }

    /**
     * Create a mailbox.
     *
     * @param mixed $mailbox  The mailbox to create. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param array $opts     Additional options:
     *   - special_use: (array) An array of special-use flags to mark the
     *                  mailbox with. The server MUST support RFC 6154.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function createMailbox($mailbox, array $opts = array())
    {
        $this->login();

        if (!$this->queryCapability('CREATE-SPECIAL-USE')) {
            unset($opts['special_use']);
        }

        $this->_createMailbox(Horde_Imap_Client_Mailbox::get($mailbox), $opts);
    }

    /**
     * Create a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox to create.
     * @param array $opts                         Additional options. See
     *                                            createMailbox().
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _createMailbox(Horde_Imap_Client_Mailbox $mailbox,
                                               $opts);

    /**
     * Delete a mailbox.
     *
     * @param mixed $mailbox  The mailbox to delete. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function deleteMailbox($mailbox)
    {
        $this->login();

        $mailbox = Horde_Imap_Client_Mailbox::get($mailbox);

        $this->_deleteMailbox($mailbox);
        $this->_deleteMailboxPost($mailbox);
    }

    /**
     * Delete a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox to delete.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _deleteMailbox(Horde_Imap_Client_Mailbox $mailbox);

    /**
     * Actions to perform after a mailbox delete.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The deleted mailbox.
     */
    protected function _deleteMailboxPost(Horde_Imap_Client_Mailbox $mailbox)
    {
        /* Delete mailbox caches. */
        if ($this->_initCache()) {
            $this->_cache->deleteMailbox($mailbox);
        }
        unset($this->_temp['mailbox_ob'][strval($mailbox)]);

        /* Unsubscribe from mailbox. */
        try {
            $this->subscribeMailbox($mailbox, false);
        } catch (Horde_Imap_Client_Exception $e) {
            // Ignore failed unsubscribe request
        }
    }

    /**
     * Rename a mailbox.
     *
     * @param mixed $old  The old mailbox name. Either a
     *                    Horde_Imap_Client_Mailbox object or a string (UTF-8).
     * @param mixed $new  The new mailbox name. Either a
     *                    Horde_Imap_Client_Mailbox object or a string (UTF-8).
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function renameMailbox($old, $new)
    {
        // Login will be handled by first listMailboxes() call.

        $old = Horde_Imap_Client_Mailbox::get($old);
        $new = Horde_Imap_Client_Mailbox::get($new);

        /* Check if old mailbox(es) were subscribed to. */
        $base = $this->listMailboxes($old, Horde_Imap_Client::MBOX_SUBSCRIBED, array('delimiter' => true));
        if (empty($base)) {
            $base = $this->listMailboxes($old, Horde_Imap_Client::MBOX_ALL, array('delimiter' => true));
            $base = reset($base);
            $subscribed = array();
        } else {
            $base = reset($base);
            $subscribed = array($base['mailbox']);
        }

        $all_mboxes = array($base['mailbox']);
        if (strlen($base['delimiter'])) {
            $search = $old->list_escape . $base['delimiter'] . '*';
            $all_mboxes = array_merge($all_mboxes, $this->listMailboxes($search, Horde_Imap_Client::MBOX_ALL, array('flat' => true)));
            $subscribed = array_merge($subscribed, $this->listMailboxes($search, Horde_Imap_Client::MBOX_SUBSCRIBED, array('flat' => true)));
        }

        $this->_renameMailbox($old, $new);

        /* Delete mailbox actions. */
        foreach ($all_mboxes as $val) {
            $this->_deleteMailboxPost($val);
        }

        foreach ($subscribed as $val) {
            try {
                $this->subscribeMailbox(new Horde_Imap_Client_Mailbox(substr_replace($val, $new, 0, strlen($old))));
            } catch (Horde_Imap_Client_Exception $e) {
                // Ignore failed subscription requests
            }
        }
    }

    /**
     * Rename a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $old  The old mailbox name.
     * @param Horde_Imap_Client_Mailbox $new  The new mailbox name.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _renameMailbox(Horde_Imap_Client_Mailbox $old,
                                               Horde_Imap_Client_Mailbox $new);

    /**
     * Manage subscription status for a mailbox.
     *
     * @param mixed $mailbox      The mailbox to [un]subscribe to. Either a
     *                            Horde_Imap_Client_Mailbox object or a string
     *                            (UTF-8).
     * @param boolean $subscribe  True to subscribe, false to unsubscribe.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function subscribeMailbox($mailbox, $subscribe = true)
    {
        $this->login();
        $this->_subscribeMailbox(Horde_Imap_Client_Mailbox::get($mailbox), (bool)$subscribe);
    }

    /**
     * Manage subscription status for a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox to [un]subscribe
     *                                            to.
     * @param boolean $subscribe                  True to subscribe, false to
     *                                            unsubscribe.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _subscribeMailbox(Horde_Imap_Client_Mailbox $mailbox,
                                                  $subscribe);

    /**
     * Obtain a list of mailboxes matching a pattern.
     *
     * @param mixed $pattern   The mailbox search pattern(s) (see RFC 3501
     *                         [6.3.8] for the format). A UTF-8 string or an
     *                         array of strings. If a Horde_Imap_Client_Mailbox
     *                         object is given, it is escaped (i.e. wildcard
     *                         patterns are converted to return the miminal
     *                         number of matches possible).
     * @param integer $mode    Which mailboxes to return.  Either:
     *   - Horde_Imap_Client::MBOX_SUBSCRIBED
     *   - Horde_Imap_Client::MBOX_SUBSCRIBED_EXISTS
     *   - Horde_Imap_Client::MBOX_UNSUBSCRIBED
     *   - Horde_Imap_Client::MBOX_ALL
     * @param array $options   Additional options:
     * <pre>
     *   - attributes: (boolean) If true, return attribute information under
     *                 the 'attributes' key.
     *                 DEFAULT: Do not return this information.
     *   - children: (boolean) Tell server to return children attribute
     *               information (\HasChildren, \HasNoChildren). Requires the
     *               LIST-EXTENDED extension to guarantee this information is
     *               returned. Server MAY return this attribute without this
     *               option, or if the CHILDREN extension is available, but it
     *               is not guaranteed.
     *               DEFAULT: false
     *   - delimiter: (boolean) If true, return delimiter information under
     *                the'delimiter' key.
     *                DEFAULT: Do not return this information.
     *   - flat: (boolean) If true, return a flat list of mailbox names only.
     *           Overrides both the 'attributes' and 'delimiter' options.
     *           DEFAULT: Do not return flat list.
     *   - recursivematch: (boolean) Force the server to return information
     *                     about parent mailboxes that don't match other
     *                     selection options, but have some sub-mailboxes that
     *                     do. Information about children is returned in the
     *                     CHILDINFO extended data item ('extended'). Requires
     *                     the LIST-EXTENDED extension.
     *                     DEFAULT: false
     *   - remote: (boolean) Tell server to return mailboxes that reside on
     *             another server. Requires the LIST-EXTENDED extension.
     *             DEFAULT: false
     *   - special_use: (boolean) Tell server to return special-use attribute
     *                  information (see Horde_Imap_Client SPECIALUSE_*
     *                  constants). Server must support the SPECIAL-USE return
     *                  option for this setting to have any effect.
     *                  DEFAULT: false
     *   - status: (integer) Tell server to return status information. The
     *             value is a bitmask that may contain any of:
     *     - Horde_Imap_Client::STATUS_MESSAGES
     *     - Horde_Imap_Client::STATUS_RECENT
     *     - Horde_Imap_Client::STATUS_UIDNEXT
     *     - Horde_Imap_Client::STATUS_UIDVALIDITY
     *     - Horde_Imap_Client::STATUS_UNSEEN
     *     - Horde_Imap_Client::STATUS_HIGHESTMODSEQ
     *     DEFAULT: 0
     *   - sort: (boolean) If true, return a sorted list of mailboxes?
     *           DEFAULT: Do not sort the list.
     *   - sort_delimiter: (string) If 'sort' is true, this is the delimiter
     *                     used to sort the mailboxes.
     *                     DEFAULT: '.'
     * </pre>
     *
     * @return array  If 'flat' option is true, the array values are a list
     *                of Horde_Imap_Client_Mailbox objects. Otherwise, the
     *                keys are UTF-8 mailbox names and the values are arrays
     *                with these keys:
     *   - attributes: (array) List of lower-cased attributes [only if
     *                 'attributes' option is true].
     *   - delimiter: (string) The delimiter for the mailbox [only if
     *                'delimiter' option is true].
     *   - extended: (TODO) TODO [only if 'recursivematch' option is true and
     *               LIST-EXTENDED extension is supported on the server].
     *   - mailbox: (Horde_Imap_Client_Mailbox) The mailbox object.
     *   - status: (array) See status() [only if 'status' option is true].
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function listMailboxes($pattern,
                                  $mode = Horde_Imap_Client::MBOX_ALL,
                                  array $options = array())
    {
        $this->login();

        $pattern = is_array($pattern)
            ? array_unique($pattern)
            : array($pattern);

        /* Prepare patterns. */
        $plist = array();
        foreach ($pattern as $val) {
            if ($val instanceof Horde_Imap_Client_Mailbox) {
                $val = $val->list_escape;
            }
            $plist[] = Horde_Imap_Client_Mailbox::get(preg_replace(
                array("/\*{2,}/", "/\%{2,}/"),
                array('*', '%'),
                Horde_Imap_Client_Utf7imap::Utf8ToUtf7Imap($val)
            ), true);
        }

        if (isset($options['special_use']) &&
            !$this->queryCapability('SPECIAL-USE')) {
            unset($options['special_use']);
        }

        $ret = $this->_listMailboxes($plist, $mode, $options);

        if (!empty($options['status']) &&
            !$this->queryCapability('LIST-STATUS')) {
            foreach ($this->status(array_keys($ret), $options['status']) as $key => $val) {
                $ret[$key]['status'] = $val;
            }
        }

        if (empty($options['sort'])) {
            return $ret;
        }

        $list_ob = new Horde_Imap_Client_Mailbox_List(empty($options['flat']) ? array_keys($ret) : $ret);
        $sorted = $list_ob->sort(array(
            'delimiter' => empty($options['sort_delimiter']) ? '.' : $options['sort_delimiter']
        ));

        if (!empty($options['flat'])) {
            return $sorted;
        }

        $out = array();
        foreach ($sorted as $val) {
            $out[$val] = $ret[$val];
        }

        return $out;
    }

    /**
     * Obtain a list of mailboxes matching a pattern.
     *
     * @param array $pattern  The mailbox search patterns
     *                        (Horde_Imap_Client_Mailbox objects).
     * @param integer $mode   Which mailboxes to return.
     * @param array $options  Additional options.
     *
     * @return array  See listMailboxes().
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _listMailboxes($pattern, $mode, $options);

    /**
     * Obtain status information for a mailbox.
     *
     * @param mixed $mailbox  The mailbox(es) to query. Either a
     *                        Horde_Imap_Client_Mailbox object, a string
     *                        (UTF-8), or an array of objects/strings (since
     *                        2.10.0).
     * @param integer $flags  A bitmask of information requested from the
     *                        server. Allowed flags:
     * <pre>
     *   - Horde_Imap_Client::STATUS_MESSAGES
     *     Return key: messages
     *     Return format: (integer) The number of messages in the mailbox.
     *
     *   - Horde_Imap_Client::STATUS_RECENT
     *     Return key: recent
     *     Return format: (integer) The number of messages with the \Recent
     *                    flag set as currently reported in the mailbox
     *
     *   - Horde_Imap_Client::STATUS_RECENT_TOTAL
     *     Return key: recent_total
     *     Return format: (integer) The number of messages with the \Recent
     *                    flag set. This returns the total number of messages
     *                    that have been marked as recent in this mailbox
     *                    since the PHP process began. (since 2.12.0)
     *
     *   - Horde_Imap_Client::STATUS_UIDNEXT
     *     Return key: uidnext
     *     Return format: (integer) The next UID to be assigned in the
     *                    mailbox. Only returned if the server automatically
     *                    provides the data.
     *
     *   - Horde_Imap_Client::STATUS_UIDNEXT_FORCE
     *     Return key: uidnext
     *     Return format: (integer) The next UID to be assigned in the
     *                    mailbox. This option will always determine this
     *                    value, even if the server does not automatically
     *                    provide this data.
     *
     *   - Horde_Imap_Client::STATUS_UIDVALIDITY
     *     Return key: uidvalidity
     *     Return format: (integer) The unique identifier validity of the
     *                    mailbox.
     *
     *   - Horde_Imap_Client::STATUS_UNSEEN
     *     Return key: unseen
     *     Return format: (integer) The number of messages which do not have
     *                    the \Seen flag set.
     *
     *   - Horde_Imap_Client::STATUS_FIRSTUNSEEN
     *     Return key: firstunseen
     *     Return format: (integer) The sequence number of the first unseen
     *                    message in the mailbox.
     *
     *   - Horde_Imap_Client::STATUS_FLAGS
     *     Return key: flags
     *     Return format: (array) The list of defined flags in the mailbox
     *                    (all flags are in lowercase).
     *
     *   - Horde_Imap_Client::STATUS_PERMFLAGS
     *     Return key: permflags
     *     Return format: (array) The list of flags that a client can change
     *                    permanently (all flags are in lowercase).
     *
     *   - Horde_Imap_Client::STATUS_HIGHESTMODSEQ
     *     Return key: highestmodseq
     *     Return format: (integer) If the server supports the CONDSTORE
     *                    IMAP extension, this will be the highest
     *                    mod-sequence value of all messages in the mailbox.
     *                    Else 0 if CONDSTORE not available or the mailbox
     *                    does not support mod-sequences.
     *
     *   - Horde_Imap_Client::STATUS_SYNCMODSEQ
     *     Return key: syncmodseq
     *     Return format: (integer) If caching, and the server supports the
     *                    CONDSTORE IMAP extension, this is the cached
     *                    mod-sequence value of the mailbox when it was opened
     *                    for the first time in this access. Will be null if
     *                    not caching, CONDSTORE not available, or the mailbox
     *                    does not support mod-sequences.
     *
     *   - Horde_Imap_Client::STATUS_SYNCFLAGUIDS
     *     Return key: syncflaguids
     *     Return format: (Horde_Imap_Client_Ids) If caching, the server
     *                    supports the CONDSTORE IMAP extension, and the
     *                    mailbox contained cached data when opened for the
     *                    first time in this access, this is the list of UIDs
     *                    in which flags have changed since STATUS_SYNCMODSEQ.
     *
     *   - Horde_Imap_Client::STATUS_SYNCVANISHED
     *     Return key: syncvanished
     *     Return format: (Horde_Imap_Client_Ids) If caching, the server
     *                    supports the CONDSTORE IMAP extension, and the
     *                    mailbox contained cached data when opened for the
     *                    first time in this access, this is the list of UIDs
     *                    which have been deleted since STATUS_SYNCMODSEQ.
     *
     *   - Horde_Imap_Client::STATUS_UIDNOTSTICKY
     *     Return key: uidnotsticky
     *     Return format: (boolean) If the server supports the UIDPLUS IMAP
     *                    extension, and the queried mailbox does not support
     *                    persistent UIDs, this value will be true. In all
     *                    other cases, this value will be false.
     *
     *   - Horde_Imap_Client::STATUS_FORCE_REFRESH
     *     Normally, the status information will be cached for a given
     *     mailbox. Since most PHP requests are generally less than a second,
     *     this is fine. However, if your script is long running, the status
     *     information may not be up-to-date. Specifying this flag will ensure
     *     that the server is always polled for the current mailbox status
     *     before results are returned. (since 2.14.0)
     *
     *   - Horde_Imap_Client::STATUS_ALL (DEFAULT)
     *     Shortcut to return 'messages', 'recent', 'uidnext', 'uidvalidity',
     *     and 'unseen' values.
     * </ul>
     * @param array $opts     Additional options:
     * <pre>
     *   - sort: (boolean) If true, sort the list of mailboxes? (since 2.10.0)
     *           DEFAULT: Do not sort the list.
     *   - sort_delimiter: (string) If 'sort' is true, this is the delimiter
     *                     used to sort the mailboxes. (since 2.10.0)
     *                     DEFAULT: '.'
     * </pre>
     *
     * @return array  If $mailbox contains multiple mailboxes, an array with
     *                keys being the UTF-8 mailbox name and values as arrays
     *                containing the requested keys (see above).
     *                Otherwise, an array with keys as the requested keys (see
     *                above) and values as the key data.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function status($mailbox, $flags = Horde_Imap_Client::STATUS_ALL,
                           array $opts = array())
    {
        $opts = array_merge(array(
            'sort' => false,
            'sort_delimiter' => '.'
        ), $opts);

        $this->login();

        if (is_array($mailbox)) {
            if (empty($mailbox)) {
                return array();
            }
            $ret_array = true;
        } else {
            $mailbox = array($mailbox);
            $ret_array = false;
        }

        $mlist = array_map(array('Horde_Imap_Client_Mailbox', 'get'), $mailbox);

        $unselected_flags = array(
            'messages' => Horde_Imap_Client::STATUS_MESSAGES,
            'recent' => Horde_Imap_Client::STATUS_RECENT,
            'uidnext' => Horde_Imap_Client::STATUS_UIDNEXT,
            'uidvalidity' => Horde_Imap_Client::STATUS_UIDVALIDITY,
            'unseen' => Horde_Imap_Client::STATUS_UNSEEN
        );

        if (!$this->statuscache) {
            $flags |= Horde_Imap_Client::STATUS_FORCE_REFRESH;
        }

        if ($flags & Horde_Imap_Client::STATUS_ALL) {
            foreach ($unselected_flags as $val) {
                $flags |= $val;
            }
        }

        $master = $ret = array();

        /* Catch flags that are not supported. */
        if (($flags & Horde_Imap_Client::STATUS_HIGHESTMODSEQ) &&
            !isset($this->_temp['enabled']['CONDSTORE'])) {
            $master['highestmodseq'] = 0;
            $flags &= ~Horde_Imap_Client::STATUS_HIGHESTMODSEQ;
        }

        if (($flags & Horde_Imap_Client::STATUS_UIDNOTSTICKY) &&
            !$this->queryCapability('UIDPLUS')) {
            $master['uidnotsticky'] = false;
            $flags &= ~Horde_Imap_Client::STATUS_UIDNOTSTICKY;
        }

        /* UIDNEXT return options. */
        if ($flags & Horde_Imap_Client::STATUS_UIDNEXT_FORCE) {
            $flags |= Horde_Imap_Client::STATUS_UIDNEXT;
        }

        foreach ($mlist as $val) {
            $name = strval($val);
            $tmp_flags = $flags;

            if ($val->equals($this->_selected)) {
                /* Check if already in mailbox. */
                $opened = true;

                if ($flags & Horde_Imap_Client::STATUS_FORCE_REFRESH) {
                    $this->noop();
                }
            } else {
                /* A list of STATUS options (other than those handled directly
                 * below) that require the mailbox to be explicitly opened. */
                $opened = ($flags & Horde_Imap_Client::STATUS_FIRSTUNSEEN) ||
                    ($flags & Horde_Imap_Client::STATUS_FLAGS) ||
                    ($flags & Horde_Imap_Client::STATUS_PERMFLAGS) ||
                    ($flags & Horde_Imap_Client::STATUS_UIDNOTSTICKY) ||
                    /* Force mailboxes containing wildcards to be accessed via
                     * STATUS so that wildcards do not return a bunch of
                     * mailboxes in the LIST-STATUS response. */
                    (strpbrk($name, '*%') !== false);
            }

            $ret[$name] = $master;
            $ptr = &$ret[$name];

            /* STATUS_PERMFLAGS requires a read/write mailbox. */
            if ($flags & Horde_Imap_Client::STATUS_PERMFLAGS) {
                $this->openMailbox($val, Horde_Imap_Client::OPEN_READWRITE);
                $opened = true;
            }

            /* Handle SYNC related return options. These require the mailbox
             * to be opened at least once. */
            if ($flags & Horde_Imap_Client::STATUS_SYNCMODSEQ) {
                $this->openMailbox($val);
                $ptr['syncmodseq'] = $this->_mailboxOb($val)->getStatus(Horde_Imap_Client::STATUS_SYNCMODSEQ);
                $tmp_flags &= ~Horde_Imap_Client::STATUS_SYNCMODSEQ;
                $opened = true;
            }

            if ($flags & Horde_Imap_Client::STATUS_SYNCFLAGUIDS) {
                $this->openMailbox($val);
                $ptr['syncflaguids'] = $this->getIdsOb($this->_mailboxOb($val)->getStatus(Horde_Imap_Client::STATUS_SYNCFLAGUIDS));
                $tmp_flags &= ~Horde_Imap_Client::STATUS_SYNCFLAGUIDS;
                $opened = true;
            }

            if ($flags & Horde_Imap_Client::STATUS_SYNCVANISHED) {
                $this->openMailbox($val);
                $ptr['syncvanished'] = $this->getIdsOb($this->_mailboxOb($val)->getStatus(Horde_Imap_Client::STATUS_SYNCVANISHED));
                $tmp_flags &= ~Horde_Imap_Client::STATUS_SYNCVANISHED;
                $opened = true;
            }

            /* Handle RECENT_TOTAL option. */
            if ($flags & Horde_Imap_Client::STATUS_RECENT_TOTAL) {
                $this->openMailbox($val);
                $ptr['recent_total'] = $this->_mailboxOb($val)->getStatus(Horde_Imap_Client::STATUS_RECENT_TOTAL);
                $tmp_flags &= ~Horde_Imap_Client::STATUS_RECENT_TOTAL;
                $opened = true;
            }

            if ($opened) {
                if ($tmp_flags) {
                    $tmp = $this->_status(array($val), $tmp_flags);
                    $ptr += reset($tmp);
                }
            } else {
                $to_process[] = $val;
            }
        }

        if ($flags && !empty($to_process)) {
            if ((count($to_process) > 1) &&
                $this->queryCapability('LIST-STATUS')) {
                foreach ($this->listMailboxes($to_process, Horde_Imap_Client::MBOX_ALL, array('status' => $flags)) as $key => $val) {
                    if (isset($val['status'])) {
                        $ret[$key] += $val['status'];
                    }
                }
            } else {
                foreach ($this->_status($to_process, $flags) as $key => $val) {
                    $ret[$key] += $val;
                }
            }
        }

        if (!$opts['sort'] || (count($ret) === 1)) {
            return $ret_array
                ? $ret
                : reset($ret);
        }

        $list_ob = new Horde_Imap_Client_Mailbox_List(array_keys($ret));
        $sorted = $list_ob->sort(array(
            'delimiter' => $opts['sort_delimiter']
        ));

        $out = array();
        foreach ($sorted as $val) {
            $out[$val] = $ret[$val];
        }

        return $out;
    }

    /**
     * Obtain status information for mailboxes.
     *
     * @param array $mboxes   The list of mailbox objects to query.
     * @param integer $flags  A bitmask of information requested from the
     *                        server.
     *
     * @return array  See array return for status().
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _status($mboxes, $flags);

    /**
     * Perform a STATUS call on multiple mailboxes at the same time.
     *
     * This method leverages the LIST-EXTENDED and LIST-STATUS extensions on
     * the IMAP server to improve the efficiency of this operation.
     *
     * @deprecated  Use status() instead.
     *
     * @param array $mailboxes  The mailboxes to query. Either
     *                          Horde_Imap_Client_Mailbox objects, strings
     *                          (UTF-8), or a combination of the two.
     * @param integer $flags    See status().
     * @param array $opts       Additional options:
     *   - sort: (boolean) If true, sort the list of mailboxes?
     *           DEFAULT: Do not sort the list.
     *   - sort_delimiter: (string) If 'sort' is true, this is the delimiter
     *                     used to sort the mailboxes.
     *                     DEFAULT: '.'
     *
     * @return array  An array with the keys as the mailbox names (UTF-8) and
     *                the values as arrays with the requested keys (from the
     *                mask given in $flags).
     */
    public function statusMultiple($mailboxes,
                                   $flags = Horde_Imap_Client::STATUS_ALL,
                                   array $opts = array())
    {
        return $this->status($mailboxes, $flags, $opts);
    }

    /**
     * Append message(s) to a mailbox.
     *
     * @param mixed $mailbox  The mailbox to append the message(s) to. Either
     *                        a Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param array $data     The message data to append, along with
     *                        additional options. An array of arrays with
     *                        each embedded array having the following
     *                        entries:
     * <pre>
     *   - data: (mixed) The data to append. If a string or a stream resource,
     *           this will be used as the entire contents of a single message.
     *           If an array, will catenate all given parts into a single
     *           message. This array contains one or more arrays with
     *           two keys:
     *     - t: (string) Either 'url' or 'text'.
     *     - v: (mixed) If 't' is 'url', this is the IMAP URL to the message
     *          part to append. If 't' is 'text', this is either a string or
     *          resource representation of the message part data.
     *     DEFAULT: NONE (entry is MANDATORY)
     *   - flags: (array) An array of flags/keywords to set on the appended
     *            message.
     *            DEFAULT: Only the \Recent flag is set.
     *   - internaldate: (DateTime) The internaldate to set for the appended
     *                   message.
     *                   DEFAULT: internaldate will be the same date as when
     *                   the message was appended.
     * </pre>
     * @param array $options  Additonal options:
     * <pre>
     *   - create: (boolean) Try to create $mailbox if it does not exist?
     *             DEFAULT: No.
     * </pre>
     *
     * @return Horde_Imap_Client_Ids  The UIDs of the appended messages.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function append($mailbox, $data, array $options = array())
    {
        $this->login();

        $mailbox = Horde_Imap_Client_Mailbox::get($mailbox);

        $ret = $this->_append($mailbox, $data, $options);

        if ($ret instanceof Horde_Imap_Client_Ids) {
            return $ret;
        }

        $uids = $this->getIdsOb();

        while (list(,$val) = each($data)) {
            if (is_resource($val['data'])) {
                rewind($val['data']);
            }

            $uids->add($this->_getUidByMessageId(
                $mailbox,
                Horde_Mime_Headers::parseHeaders($val['data'])->getValue('message-id')
            ));
        }

        return $uids;
    }

    /**
     * Append message(s) to a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox to append the
     *                                            message(s) to.
     * @param array $data                         The message data.
     * @param array $options                      Additional options.
     *
     * @return mixed  A Horde_Imap_Client_Ids object containing the UIDs of
     *                the appended messages (if server supports UIDPLUS
     *                extension) or true.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _append(Horde_Imap_Client_Mailbox $mailbox,
                                        $data, $options);

    /**
     * Request a checkpoint of the currently selected mailbox (RFC 3501
     * [6.4.1]).
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function check()
    {
        // CHECK only useful if we are already authenticated.
        if ($this->_isAuthenticated) {
            $this->_check();
        }
    }

    /**
     * Request a checkpoint of the currently selected mailbox.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _check();

    /**
     * Close the connection to the currently selected mailbox, optionally
     * expunging all deleted messages (RFC 3501 [6.4.2]).
     *
     * @param array $options  Additional options:
     *   - expunge: (boolean) Expunge all messages flagged as deleted?
     *              DEFAULT: No
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function close(array $options = array())
    {
        // This check catches the non-logged in case.
        if (is_null($this->_selected)) {
            return;
        }

        /* If we are caching, search for deleted messages. */
        if (!empty($options['expunge']) && $this->_initCache(true)) {
            /* Make sure mailbox is read-write to expunge. */
            $this->openMailbox($this->_selected, Horde_Imap_Client::OPEN_READWRITE);
            if ($this->_mode == Horde_Imap_Client::OPEN_READONLY) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Cannot expunge read-only mailbox."),
                    Horde_Imap_Client_Exception::MAILBOX_READONLY
                );
            }

            $search_query = new Horde_Imap_Client_Search_Query();
            $search_query->flag(Horde_Imap_Client::FLAG_DELETED, true);
            $search_res = $this->search($this->_selected, $search_query);
            $mbox = $this->_selected;
        } else {
            $search_res = null;
        }

        $this->_close($options);
        $this->_selected = null;
        $this->_mode = 0;

        if (!is_null($search_res)) {
            $this->_deleteMsgs($mbox, $search_res['match']);
        }
    }

    /**
     * Close the connection to the currently selected mailbox, optionally
     * expunging all deleted messages (RFC 3501 [6.4.2]).
     *
     * @param array $options  Additional options.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _close($options);

    /**
     * Expunge deleted messages from the given mailbox.
     *
     * @param mixed $mailbox  The mailbox to expunge. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param array $options  Additional options:
     *   - delete: (boolean) If true, will flag all messages in 'ids' as
     *             deleted (since 2.10.0).
     *             DEFAULT: false
     *   - ids: (Horde_Imap_Client_Ids) A list of messages to expunge. These
     *          messages must already be flagged as deleted (unless 'delete'
     *          is true).
     *          DEFAULT: All messages marked as deleted will be expunged.
     *   - list: (boolean) If true, returns the list of expunged messages
     *           (UIDs only).
     *           DEFAULT: false
     *
     * @return Horde_Imap_Client_Ids  If 'list' option is true, returns the
     *                                UID list of expunged messages.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function expunge($mailbox, array $options = array())
    {
        // Open mailbox call will handle the login.
        $this->openMailbox($mailbox, Horde_Imap_Client::OPEN_READWRITE);

        /* Don't expunge if the mailbox is readonly. */
        if ($this->_mode == Horde_Imap_Client::OPEN_READONLY) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Cannot expunge read-only mailbox."),
                Horde_Imap_Client_Exception::MAILBOX_READONLY
            );
        }

        if (empty($options['ids'])) {
            $options['ids'] = $this->getIdsOb(Horde_Imap_Client_Ids::ALL);
        } elseif ($options['ids']->isEmpty()) {
            return $this->getIdsOb();
        }

        return $this->_expunge($options);
    }

    /**
     * Expunge all deleted messages from the given mailbox.
     *
     * @param array $options  Additional options.
     *
     * @return Horde_Imap_Client_Ids  If 'list' option is true, returns the
     *                                list of expunged messages.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _expunge($options);

    /**
     * Search a mailbox.
     *
     * @param mixed $mailbox                         The mailbox to search.
     *                                               Either a
     *                                               Horde_Imap_Client_Mailbox
     *                                               object or a string
     *                                               (UTF-8).
     * @param Horde_Imap_Client_Search_Query $query  The search query.
     *                                               Defaults to an ALL
     *                                               search.
     * @param array $options                         Additional options:
     * <pre>
     *   - nocache: (boolean) Don't cache the results.
     *              DEFAULT: false (results cached, if possible)
     *   - partial: (mixed) The range of results to return (message sequence
     *              numbers) Only a single range is supported (represented by
     *              the minimum and maximum values contained in the range
     *              given).
     *              DEFAULT: All messages are returned.
     *   - results: (array) The data to return. Consists of zero or more of
     *              the following flags:
     *     - Horde_Imap_Client::SEARCH_RESULTS_COUNT
     *     - Horde_Imap_Client::SEARCH_RESULTS_MATCH (DEFAULT)
     *     - Horde_Imap_Client::SEARCH_RESULTS_MAX
     *     - Horde_Imap_Client::SEARCH_RESULTS_MIN
     *     - Horde_Imap_Client::SEARCH_RESULTS_SAVE
     *     - Horde_Imap_Client::SEARCH_RESULTS_RELEVANCY
     *   - sequence: (boolean) If true, returns an array of sequence numbers.
     *               DEFAULT: Returns an array of UIDs
     *   - sort: (array) Sort the returned list of messages. Multiple sort
     *           criteria can be specified. Any sort criteria can be sorted in
     *           reverse order (instead of the default ascending order) by
     *           adding a Horde_Imap_Client::SORT_REVERSE element to the array
     *           directly before adding the sort element. The following sort
     *           criteria are available:
     *     - Horde_Imap_Client::SORT_ARRIVAL
     *     - Horde_Imap_Client::SORT_CC
     *     - Horde_Imap_Client::SORT_DATE
     *     - Horde_Imap_Client::SORT_DISPLAYFROM
     *       On servers that don't support SORT=DISPLAY, this criteria will
     *       fallback to doing client-side sorting.
     *     - Horde_Imap_Client::SORT_DISPLAYFROM_FALLBACK
     *       On servers that don't support SORT=DISPLAY, this criteria will
     *       fallback to Horde_Imap_Client::SORT_FROM [since 2.4.0].
     *     - Horde_Imap_Client::SORT_DISPLAYTO
     *       On servers that don't support SORT=DISPLAY, this criteria will
     *       fallback to doing client-side sorting.
     *     - Horde_Imap_Client::SORT_DISPLAYTO_FALLBACK
     *       On servers that don't support SORT=DISPLAY, this criteria will
     *       fallback to Horde_Imap_Client::SORT_TO [since 2.4.0].
     *     - Horde_Imap_Client::SORT_FROM
     *     - Horde_Imap_Client::SORT_SEQUENCE
     *     - Horde_Imap_Client::SORT_SIZE
     *     - Horde_Imap_Client::SORT_SUBJECT
     *     - Horde_Imap_Client::SORT_TO
     *
     *     [On servers that support SEARCH=FUZZY, this criteria is also
     *     available:]
     *     - Horde_Imap_Client::SORT_RELEVANCY
     * </pre>
     *
     * @return array  An array with the following keys:
     * <pre>
     *   - count: (integer) The number of messages that match the search
     *            criteria. Always returned.
     *   - match: (Horde_Imap_Client_Ids) The IDs that match $criteria, sorted
     *            if the 'sort' modifier was set. Returned if
     *            Horde_Imap_Client::SEARCH_RESULTS_MATCH is set.
     *   - max: (integer) The UID (default) or message sequence number (if
     *          'sequence' is true) of the highest message that satisifies
     *          $criteria. Returns null if no matches found. Returned if
     *          Horde_Imap_Client::SEARCH_RESULTS_MAX is set.
     *   - min: (integer) The UID (default) or message sequence number (if
     *          'sequence' is true) of the lowest message that satisifies
     *          $criteria. Returns null if no matches found. Returned if
     *          Horde_Imap_Client::SEARCH_RESULTS_MIN is set.
     *   - modseq: (integer) The highest mod-sequence for all messages being
     *            returned. Returned if 'sort' is false, the search query
     *            includes a MODSEQ command, and the server supports the
     *            CONDSTORE IMAP extension.
     *   - relevancy: (array) The list of relevancy scores. Returned if
     *                Horde_Imap_Client::SEARCH_RESULTS_RELEVANCY is set and
     *                the server supports FUZZY search matching.
     *   - save: (boolean) Whether the search results were saved. Returned if
     *           Horde_Imap_Client::SEARCH_RESULTS_SAVE is set.
     * </pre>
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function search($mailbox, $query = null, array $options = array())
    {
        $this->login();

        if (empty($options['results'])) {
            $options['results'] = array(
                Horde_Imap_Client::SEARCH_RESULTS_MATCH,
                Horde_Imap_Client::SEARCH_RESULTS_COUNT
            );
        } elseif (!in_array(Horde_Imap_Client::SEARCH_RESULTS_COUNT, $options['results'])) {
            $options['results'][] = Horde_Imap_Client::SEARCH_RESULTS_COUNT;
        }

        // Default to an ALL search.
        if (is_null($query)) {
            $query = new Horde_Imap_Client_Search_Query();
        }

        // Check for SEARCHRES support.
        if ((($pos = array_search(Horde_Imap_Client::SEARCH_RESULTS_SAVE, $options['results'])) !== false) &&
            !$this->queryCapability('SEARCHRES')) {
            unset($options['results'][$pos]);
        }

        // Check for SORT-related options.
        if (!empty($options['sort'])) {
            $sort = $this->queryCapability('SORT');
            foreach ($options['sort'] as $key => $val) {
                switch ($val) {
                case Horde_Imap_Client::SORT_DISPLAYFROM_FALLBACK:
                    $options['sort'][$key] = (!is_array($sort) || !in_array('DISPLAY', $sort))
                        ? Horde_Imap_Client::SORT_FROM
                        : Horde_Imap_Client::SORT_DISPLAYFROM;
                    break;

                case Horde_Imap_Client::SORT_DISPLAYTO_FALLBACK:
                    $options['sort'][$key] = (!is_array($sort) || !in_array('DISPLAY', $sort))
                        ? Horde_Imap_Client::SORT_TO
                        : Horde_Imap_Client::SORT_DISPLAYTO;
                    break;
                }
            }
        }

        // Check for supported charset.
        $options['_query'] = $query->build($this->capability());
        if (!is_null($options['_query']['charset']) &&
            array_key_exists($options['_query']['charset'], $this->_init['s_charset']) &&
            !$this->_init['s_charset'][$options['_query']['charset']]) {
            foreach (array_merge(array_keys(array_filter($this->_init['s_charset'])), array('US-ASCII')) as $val) {
                try {
                    $new_query = clone $query;
                    $new_query->charset($val);
                    break;
                } catch (Horde_Imap_Client_Exception_SearchCharset $e) {
                    unset($new_query);
                }
            }

            if (!isset($new_query)) {
                throw $e;
            }

            $query = $new_query;
            $options['_query'] = $query->build($this->capability());
        }

        /* RFC 6203: MUST NOT request relevancy results if we are not using
         * FUZZY searching. */
        if (in_array(Horde_Imap_Client::SEARCH_RESULTS_RELEVANCY, $options['results']) &&
            !in_array('SEARCH=FUZZY', $options['_query']['exts_used'])) {
            throw new InvalidArgumentException('Cannot specify RELEVANCY results if not doing a FUZZY search.');
        }

        /* Check for partial matching. */
        if (!empty($options['partial'])) {
            $pids = $this->getIdsOb($options['partial'], true)->range_string;
            if (!strlen($pids)) {
                throw new InvalidArgumentException('Cannot specify empty sequence range for a PARTIAL search.');
            }

            if (strpos($pids, ':') === false) {
                $pids .= ':' . $pids;
            }

            $options['partial'] = $pids;
        }

        /* Optimization - if query is just for a count of either RECENT or
         * ALL messages, we can send status information instead. Can't
         * optimize with unseen queries because we may cause an infinite loop
         * between here and the status() call. */
        if ((count($options['results']) === 1) &&
            (reset($options['results']) == Horde_Imap_Client::SEARCH_RESULTS_COUNT)) {
            switch ($options['_query']['query']) {
            case 'ALL':
                $ret = $this->status($this->_selected, Horde_Imap_Client::STATUS_MESSAGES);
                return array('count' => $ret['messages']);

            case 'RECENT':
                $ret = $this->status($this->_selected, Horde_Imap_Client::STATUS_RECENT);
                return array('count' => $ret['recent']);
            }
        }

        $this->openMailbox($mailbox, Horde_Imap_Client::OPEN_AUTO);

        /* Take advantage of search result caching.  If CONDSTORE available,
         * we can cache all queries and invalidate the cache when the MODSEQ
         * changes. If CONDSTORE not available, we can only store queries
         * that don't involve flags. We store results by hashing the options
         * array - the generated query is already added to '_query' key
         * above. */
        $cache = null;
        if (empty($options['nocache']) &&
            $this->_initCache(true) &&
            (isset($this->_temp['enabled']['CONDSTORE']) ||
             !$query->flagSearch())) {
            $cache = $this->_getSearchCache('search', $options);
            if (isset($cache['data'])) {
                if (isset($cache['data']['match'])) {
                    $cache['data']['match'] = $this->getIdsOb($cache['data']['match']);
                }
                return $cache['data'];
            }
        }

        /* Optimization: Catch when there are no messages in a mailbox. */
        $status_res = $this->status($this->_selected, Horde_Imap_Client::STATUS_MESSAGES | Horde_Imap_Client::STATUS_HIGHESTMODSEQ);
        if ($status_res['messages'] ||
            in_array(Horde_Imap_Client::SEARCH_RESULTS_SAVE, $options['results'])) {
            /* RFC 7162 [3.1.2.2] - trying to do a MODSEQ SEARCH on a mailbox
             * that doesn't support it will return BAD. */
            if (in_array('CONDSTORE', $options['_query']['exts']) &&
                !$this->_mailboxOb()->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ)) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Mailbox does not support mod-sequences."),
                    Horde_Imap_Client_Exception::MBOXNOMODSEQ
                );
            }

            $ret = $this->_search($query, $options);
        } else {
            $ret = array(
                'count' => 0,
                'match' => $this->getIdsOb(),
                'max' => null,
                'min' => null,
                'relevancy' => array()
            );
            if (isset($status_res['highestmodseq'])) {
                $ret['modseq'] = $status_res['highestmodseq'];
            }
        }

        if ($cache) {
            $save = $ret;
            if (isset($save['match'])) {
                $save['match'] = strval($ret['match']);
            }
            $this->_setSearchCache($save, $cache);
        }

        return $ret;
    }

    /**
     * Search a mailbox.
     *
     * @param object $query   The search query.
     * @param array $options  Additional options. The '_query' key contains
     *                        the value of $query->build().
     *
     * @return Horde_Imap_Client_Ids  An array of IDs.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _search($query, $options);

    /**
     * Set the comparator to use for searching/sorting (RFC 5255).
     *
     * @param string $comparator  The comparator string (see RFC 4790 [3.1] -
     *                            "collation-id" - for format). The reserved
     *                            string 'default' can be used to select
     *                            the default comparator.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function setComparator($comparator = null)
    {
        $comp = is_null($comparator)
            ? $this->getParam('comparator')
            : $comparator;
        if (is_null($comp)) {
            return;
        }

        $this->login();

        $i18n = $this->queryCapability('I18NLEVEL');
        if (empty($i18n) || (max($i18n) < 2)) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension(
                'I18NLEVEL',
                'The IMAP server does not support changing SEARCH/SORT comparators.'
            );
        }

        $this->_setComparator($comp);
    }

    /**
     * Set the comparator to use for searching/sorting (RFC 5255).
     *
     * @param string $comparator  The comparator string (see RFC 4790 [3.1] -
     *                            "collation-id" - for format). The reserved
     *                            string 'default' can be used to select
     *                            the default comparator.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _setComparator($comparator);

    /**
     * Get the comparator used for searching/sorting (RFC 5255).
     *
     * @return mixed  Null if the default comparator is being used, or an
     *                array of comparator information (see RFC 5255 [4.8]).
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getComparator()
    {
        $this->login();

        $i18n = $this->queryCapability('I18NLEVEL');
        if (empty($i18n) || (max($i18n) < 2)) {
            return null;
        }

        return $this->_getComparator();
    }

    /**
     * Get the comparator used for searching/sorting (RFC 5255).
     *
     * @return mixed  Null if the default comparator is being used, or an
     *                array of comparator information (see RFC 5255 [4.8]).
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getComparator();

    /**
     * Thread sort a given list of messages (RFC 5256).
     *
     * @param mixed $mailbox  The mailbox to query. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param array $options  Additional options:
     * <pre>
     *   - criteria: (mixed) The following thread criteria are available:
     *     - Horde_Imap_Client::THREAD_ORDEREDSUBJECT
     *     - Horde_Imap_Client::THREAD_REFERENCES
     *     - Horde_Imap_Client::THREAD_REFS
     *       Other algorithms can be explicitly specified by passing the IMAP
     *       thread algorithm in as a string value.
     *     DEFAULT: Horde_Imap_Client::THREAD_ORDEREDSUBJECT
     *   - search: (Horde_Imap_Client_Search_Query) The search query.
     *             DEFAULT: All messages in mailbox included in thread sort.
     *   - sequence: (boolean) If true, each message is stored and referred to
     *               by its message sequence number.
     *               DEFAULT: Stored/referred to by UID.
     * </pre>
     *
     * @return Horde_Imap_Client_Data_Thread  A thread data object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function thread($mailbox, array $options = array())
    {
        // Open mailbox call will handle the login.
        $this->openMailbox($mailbox, Horde_Imap_Client::OPEN_AUTO);

        /* Take advantage of search result caching.  If CONDSTORE available,
         * we can cache all queries and invalidate the cache when the MODSEQ
         * changes. If CONDSTORE not available, we can only store queries
         * that don't involve flags. See search() for similar caching. */
        $cache = null;
        if ($this->_initCache(true) &&
            (isset($this->_temp['enabled']['CONDSTORE']) ||
             empty($options['search']) ||
             !$options['search']->flagSearch())) {
            $cache = $this->_getSearchCache('thread', $options);
            if (isset($cache['data']) &&
                ($cache['data'] instanceof Horde_Imap_Client_Data_Thread)) {
                return $cache['data'];
            }
        }

        $status_res = $this->status($this->_selected, Horde_Imap_Client::STATUS_MESSAGES);

        $ob = $status_res['messages']
            ? $this->_thread($options)
            : new Horde_Imap_Client_Data_Thread(array(), empty($options['sequence']) ? 'uid' : 'sequence');

        if ($cache) {
            $this->_setSearchCache($ob, $cache);
        }

        return $ob;
    }

    /**
     * Thread sort a given list of messages (RFC 5256).
     *
     * @param array $options  Additional options. See thread().
     *
     * @return Horde_Imap_Client_Data_Thread  A thread data object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _thread($options);

    /**
     * Fetch message data (see RFC 3501 [6.4.5]).
     *
     * @param mixed $mailbox                        The mailbox to search.
     *                                              Either a
     *                                              Horde_Imap_Client_Mailbox
     *                                              object or a string (UTF-8).
     * @param Horde_Imap_Client_Fetch_Query $query  Fetch query object.
     * @param array $options                        Additional options:
     *   - changedsince: (integer) Only return messages that have a
     *                   mod-sequence larger than this value. This option
     *                   requires the CONDSTORE IMAP extension (if not present,
     *                   this value is ignored). Additionally, the mailbox
     *                   must support mod-sequences or an exception will be
     *                   thrown. If valid, this option implicity adds the
     *                   mod-sequence fetch criteria to the fetch command.
     *                   DEFAULT: Mod-sequence values are ignored.
     *   - exists: (boolean) Ensure that all ids returned exist on the server.
     *             If false, the list of ids returned in the results object
     *             is not guaranteed to reflect the current state of the
     *             remote mailbox.
     *             DEFAULT: false
     *   - ids: (Horde_Imap_Client_Ids) A list of messages to fetch data from.
     *          DEFAULT: All messages in $mailbox will be fetched.
     *   - nocache: (boolean) If true, will not cache the results (previously
     *              cached data will still be used to generate results) [since
     *              2.8.0].
     *              DEFAULT: false
     *
     * @return Horde_Imap_Client_Fetch_Results  A results object.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function fetch($mailbox, $query, array $options = array())
    {
        try {
            $ret = $this->_fetchWrapper($mailbox, $query, $options);
            unset($this->_temp['fetch_nocache']);
            return $ret;
        } catch (Exception $e) {
            unset($this->_temp['fetch_nocache']);
            throw $e;
        }
    }

    /**
     * Wrapper for fetch() to allow internal state to be reset on exception.
     *
     * @internal
     * @see fetch()
     */
    private function _fetchWrapper($mailbox, $query, $options)
    {
        $this->login();

        $query = clone $query;

        $cache_array = $header_cache = $new_query = array();

        if (empty($options['ids'])) {
            $options['ids'] = $this->getIdsOb(Horde_Imap_Client_Ids::ALL);
        } elseif ($options['ids']->isEmpty()) {
            return new Horde_Imap_Client_Fetch_Results($this->_fetchDataClass);
        } elseif ($options['ids']->search_res &&
                  !$this->queryCapability('SEARCHRES')) {
            /* SEARCHRES requires server support. */
            throw new Horde_Imap_Client_Exception_NoSupportExtension('SEARCHRES');
        }

        $this->openMailbox($mailbox, Horde_Imap_Client::OPEN_AUTO);
        $mbox_ob = $this->_mailboxOb();

        if (!empty($options['nocache'])) {
            $this->_temp['fetch_nocache'] = true;
        }

        $cf = $this->_initCache(true)
            ? $this->_cacheFields()
            : array();

        if (!empty($cf)) {
            /* If using cache, we store by UID so we need to return UIDs. */
            $query->uid();
        }

        $modseq_check = !empty($options['changedsince']);
        if ($query->contains(Horde_Imap_Client::FETCH_MODSEQ)) {
            if (!isset($this->_temp['enabled']['CONDSTORE'])) {
                unset($query[Horde_Imap_Client::FETCH_MODSEQ]);
            } elseif (empty($options['changedsince'])) {
                $modseq_check = true;
            }
        }

        if ($modseq_check &&
            !$mbox_ob->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ)) {
            /* RFC 7162 [3.1.2.2] - trying to do a MODSEQ FETCH on a mailbox
             * that doesn't support it will return BAD. */
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Mailbox does not support mod-sequences."),
                Horde_Imap_Client_Exception::MBOXNOMODSEQ
            );
        }

        /* Determine if caching is available and if anything in $query is
         * cacheable. */
        foreach ($cf as $k => $v) {
            if (isset($query[$k])) {
                switch ($k) {
                case Horde_Imap_Client::FETCH_ENVELOPE:
                case Horde_Imap_Client::FETCH_FLAGS:
                case Horde_Imap_Client::FETCH_IMAPDATE:
                case Horde_Imap_Client::FETCH_SIZE:
                case Horde_Imap_Client::FETCH_STRUCTURE:
                    $cache_array[$k] = $v;
                    break;

                case Horde_Imap_Client::FETCH_HEADERS:
                    $this->_temp['headers_caching'] = array();

                    foreach ($query[$k] as $key => $val) {
                        /* Only cache if directly requested.  Iterate through
                         * requests to ensure at least one can be cached. */
                        if (!empty($val['cache']) && !empty($val['peek'])) {
                            $cache_array[$k] = $v;
                            ksort($val);
                            $header_cache[$key] = hash(
                                (PHP_MINOR_VERSION >= 4) ? 'fnv132' : 'sha1',
                                serialize($val)
                            );
                        }
                    }
                    break;
                }
            }
        }

        $ret = new Horde_Imap_Client_Fetch_Results(
            $this->_fetchDataClass,
            $options['ids']->sequence ? Horde_Imap_Client_Fetch_Results::SEQUENCE : Horde_Imap_Client_Fetch_Results::UID
        );

        /* If nothing is cacheable, we can do a straight search. */
        if (empty($cache_array)) {
            $options['_query'] = $query;
            $this->_fetch($ret, array($options));
            return $ret;
        }

        $cs_ret = empty($options['changedsince'])
            ? null
            : clone $ret;

        /* Convert special searches to UID lists and create mapping. */
        $ids = $this->resolveIds($this->_selected, $options['ids'], empty($options['exists']) ? 1 : 2);

        /* Add non-user settable cache fields. */
        $cache_array[Horde_Imap_Client::FETCH_DOWNGRADED] = self::CACHE_DOWNGRADED;

        /* Get the cached values. */
        $data = $this->_cache->get($this->_selected, $ids->ids, array_values($cache_array), $mbox_ob->getStatus(Horde_Imap_Client::STATUS_UIDVALIDITY));

        /* Build a list of what we still need. */
        $map = array_flip($mbox_ob->map->map);
        $sequence = $options['ids']->sequence;
        foreach ($ids as $uid) {
            $crit = clone $query;

            if ($sequence) {
                if (!isset($map[$uid])) {
                    continue;
                }
                $entry_idx = $map[$uid];
            } else {
                $entry_idx = $uid;
                unset($crit[Horde_Imap_Client::FETCH_UID]);
            }

            $entry = $ret->get($entry_idx);

            if (isset($map[$uid])) {
                $entry->setSeq($map[$uid]);
                unset($crit[Horde_Imap_Client::FETCH_SEQ]);
            }

            $entry->setUid($uid);

            foreach ($cache_array as $key => $cid) {
                switch ($key) {
                case Horde_Imap_Client::FETCH_DOWNGRADED:
                    if (!empty($data[$uid][$cid])) {
                        $entry->setDowngraded(true);
                    }
                    break;

                case Horde_Imap_Client::FETCH_ENVELOPE:
                    if (isset($data[$uid][$cid]) &&
                        ($data[$uid][$cid] instanceof Horde_Imap_Client_Data_Envelope)) {
                        $entry->setEnvelope($data[$uid][$cid]);
                        unset($crit[$key]);
                    }
                    break;

                case Horde_Imap_Client::FETCH_FLAGS:
                    if (isset($data[$uid][$cid]) &&
                        is_array($data[$uid][$cid])) {
                        $entry->setFlags($data[$uid][$cid]);
                        unset($crit[$key]);
                    }
                    break;

                case Horde_Imap_Client::FETCH_HEADERS:
                    foreach ($header_cache as $hkey => $hval) {
                        if (isset($data[$uid][$cid][$hval])) {
                            /* We have found a cached entry with the same
                             * MD5 sum. */
                            $entry->setHeaders($hkey, $data[$uid][$cid][$hval]);
                            $crit->remove($key, $hkey);
                        } else {
                            $this->_temp['headers_caching'][$hkey] = $hval;
                        }
                    }
                    break;

                case Horde_Imap_Client::FETCH_IMAPDATE:
                    if (isset($data[$uid][$cid]) &&
                        ($data[$uid][$cid] instanceof Horde_Imap_Client_DateTime)) {
                        $entry->setImapDate($data[$uid][$cid]);
                        unset($crit[$key]);
                    }
                    break;

                case Horde_Imap_Client::FETCH_SIZE:
                    if (isset($data[$uid][$cid])) {
                        $entry->setSize($data[$uid][$cid]);
                        unset($crit[$key]);
                    }
                    break;

                case Horde_Imap_Client::FETCH_STRUCTURE:
                    if (isset($data[$uid][$cid]) &&
                        ($data[$uid][$cid] instanceof Horde_Mime_Part)) {
                        $entry->setStructure($data[$uid][$cid]);
                        unset($crit[$key]);
                    }
                    break;
                }
            }

            if (count($crit)) {
                $sig = $crit->hash();
                if (isset($new_query[$sig])) {
                    $new_query[$sig]['i'][] = $entry_idx;
                } else {
                    $new_query[$sig] = array(
                        'c' => $crit,
                        'i' => array($entry_idx)
                    );
                }
            }
        }

        $to_fetch = array();
        foreach ($new_query as $val) {
            $ids_ob = $this->getIdsOb(null, $sequence);
            $ids_ob->duplicates = true;
            $ids_ob->add($val['i']);
            $to_fetch[] = array_merge($options, array(
                '_query' => $val['c'],
                'ids' => $ids_ob
            ));
        }

        if (!empty($to_fetch)) {
            $this->_fetch(is_null($cs_ret) ? $ret : $cs_ret, $to_fetch);
        }

        if (is_null($cs_ret)) {
            return $ret;
        }

        /* If doing changedsince query, and all other data is cached, we still
         * need to hit IMAP server to determine proper results set. */
        if (empty($new_query)) {
            $squery = new Horde_Imap_Client_Search_Query();
            $squery->modseq($options['changedsince'] + 1);
            $squery->ids($options['ids']);

            $cs = $this->search($this->_selected, $squery, array(
                'sequence' => $sequence
            ));

            foreach ($cs['match'] as $val) {
                $entry = $ret->get($val);
                if ($sequence) {
                    $entry->setSeq($val);
                } else {
                    $entry->setUid($val);
                }
                $cs_ret[$val] = $entry;
            }
        } else {
            foreach ($cs_ret as $key => $val) {
                $val->merge($ret->get($key));
            }
        }

        return $cs_ret;
    }

    /**
     * Fetch message data.
     *
     * Fetch queries should be grouped in the $queries argument. Each value
     * is an array of fetch options, with the fetch query stored in the
     * '_query' parameter. IMPORTANT: All queries must have the same ID
     * type (either sequence or UID).
     *
     * @param Horde_Imap_Client_Fetch_Results $results  Fetch results.
     * @param array $queries                            The list of queries.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _fetch(Horde_Imap_Client_Fetch_Results $results,
                                       $queries);

    /**
     * Get the list of vanished messages (UIDs that have been expunged since a
     * given mod-sequence value).
     *
     * @param mixed $mailbox   The mailbox to query. Either a
     *                         Horde_Imap_Client_Mailbox object or a string
     *                         (UTF-8).
     * @param integer $modseq  Search for expunged messages after this
     *                         mod-sequence value.
     * @param array $opts      Additional options:
     *   - ids: (Horde_Imap_Client_Ids)  Restrict to these UIDs.
     *          DEFAULT: Returns full list of UIDs vanished (QRESYNC only).
     *                   This option is REQUIRED for non-QRESYNC servers or
     *                   else an empty list will be returned.
     *
     * @return Horde_Imap_Client_Ids  List of UIDs that have vanished.
     *
     * @throws Horde_Imap_Client_NoSupportExtension
     */
    public function vanished($mailbox, $modseq, array $opts = array())
    {
        $this->login();

        $qresync = $this->queryCapability('QRESYNC');

        if (empty($opts['ids'])) {
            if (!$qresync) {
                return $this->getIdsOb();
            }
            $opts['ids'] = $this->getIdsOb(Horde_Imap_Client_Ids::ALL);
        } elseif ($opts['ids']->isEmpty()) {
            return $this->getIdsOb();
        } elseif ($opts['ids']->sequence) {
            throw new InvalidArgumentException('Vanished requires UIDs.');
        }

        $this->openMailbox($mailbox, Horde_Imap_Client::OPEN_AUTO);

        if ($qresync) {
            if (!$this->_mailboxOb()->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ)) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Mailbox does not support mod-sequences."),
                    Horde_Imap_Client_Exception::MBOXNOMODSEQ
                );
            }

            return $this->_vanished(max(1, $modseq), $opts['ids']);
        }

        $ids = $this->resolveIds($mailbox, $opts['ids']);

        $squery = new Horde_Imap_Client_Search_Query();
        $squery->ids($this->getIdsOb($ids->range_string));
        $search = $this->search($mailbox, $squery, array(
            'nocache' => true
        ));

        return $this->getIdsOb(array_diff($ids->ids, $search['match']->ids));
    }

    /**
     * Get the list of vanished messages.
     *
     * @param integer $modseq             Mod-sequence value.
     * @param Horde_Imap_Client_Ids $ids  UIDs.
     *
     * @return Horde_Imap_Client_Ids  List of UIDs that have vanished.
     */
    abstract protected function _vanished($modseq, Horde_Imap_Client_Ids $ids);

    /**
     * Store message flag data (see RFC 3501 [6.4.6]).
     *
     * @param mixed $mailbox  The mailbox containing the messages to modify.
     *                        Either a Horde_Imap_Client_Mailbox object or a
     *                        string (UTF-8).
     * @param array $options  Additional options:
     *   - add: (array) An array of flags to add.
     *          DEFAULT: No flags added.
     *   - ids: (Horde_Imap_Client_Ids) The list of messages to modify.
     *          DEFAULT: All messages in $mailbox will be modified.
     *   - remove: (array) An array of flags to remove.
     *             DEFAULT: No flags removed.
     *   - replace: (array) Replace the current flags with this set
     *              of flags. Overrides both the 'add' and 'remove' options.
     *              DEFAULT: No replace is performed.
     *   - unchangedsince: (integer) Only changes flags if the mod-sequence ID
     *                     of the message is equal or less than this value.
     *                     Requires the CONDSTORE IMAP extension on the server.
     *                     Also requires the mailbox to support mod-sequences.
     *                     Will throw an exception if either condition is not
     *                     met.
     *                     DEFAULT: mod-sequence is ignored when applying
     *                              changes
     *
     * @return Horde_Imap_Client_Ids  A Horde_Imap_Client_Ids object
     *                                containing the list of IDs that failed
     *                                the 'unchangedsince' test.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function store($mailbox, array $options = array())
    {
        // Open mailbox call will handle the login.
        $this->openMailbox($mailbox, Horde_Imap_Client::OPEN_READWRITE);

        /* SEARCHRES requires server support. */
        if (empty($options['ids'])) {
            $options['ids'] = $this->getIdsOb(Horde_Imap_Client_Ids::ALL);
        } elseif ($options['ids']->isEmpty()) {
            return $this->getIdsOb();
        } elseif ($options['ids']->search_res &&
                  !$this->queryCapability('SEARCHRES')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('SEARCHRES');
        }

        if (!empty($options['unchangedsince'])) {
            if (!isset($this->_temp['enabled']['CONDSTORE'])) {
                throw new Horde_Imap_Client_Exception_NoSupportExtension('CONDSTORE');
            }

            /* RFC 7162 [3.1.2.2] - trying to do a UNCHANGEDSINCE STORE on a
             * mailbox that doesn't support it will return BAD. */
            if (!$this->_mailboxOb()->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ)) {
                throw new Horde_Imap_Client_Exception(
                    Horde_Imap_Client_Translation::r("Mailbox does not support mod-sequences."),
                    Horde_Imap_Client_Exception::MBOXNOMODSEQ
                );
            }
        }

        return $this->_store($options);
    }

    /**
     * Store message flag data.
     *
     * @param array $options  Additional options.
     *
     * @return Horde_Imap_Client_Ids  A Horde_Imap_Client_Ids object
     *                                containing the list of IDs that failed
     *                                the 'unchangedsince' test.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _store($options);

    /**
     * Copy messages to another mailbox.
     *
     * @param mixed $source   The source mailbox. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param mixed $dest     The destination mailbox. Either a
     *                        Horde_Imap_Client_Mailbox object or a string
     *                        (UTF-8).
     * @param array $options  Additional options:
     *   - create: (boolean) Try to create $dest if it does not exist?
     *             DEFAULT: No.
     *   - force_map: (boolean) Forces the array mapping to always be
     *                returned. [@since 2.19.0]
     *   - ids: (Horde_Imap_Client_Ids) The list of messages to copy.
     *          DEFAULT: All messages in $mailbox will be copied.
     *   - move: (boolean) If true, delete the original messages.
     *           DEFAULT: Original messages are not deleted.
     *
     * @return mixed  An array mapping old UIDs (keys) to new UIDs (values) on
     *                success (only guaranteed if 'force_map' is true) or
     *                true.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function copy($source, $dest, array $options = array())
    {
        // Open mailbox call will handle the login.
        $this->openMailbox($source, empty($options['move']) ? Horde_Imap_Client::OPEN_AUTO : Horde_Imap_Client::OPEN_READWRITE);

        /* SEARCHRES requires server support. */
        if (empty($options['ids'])) {
            $options['ids'] = $this->getIdsOb(Horde_Imap_Client_Ids::ALL);
        } elseif ($options['ids']->isEmpty()) {
            return array();
        } elseif ($options['ids']->search_res &&
                  !$this->queryCapability('SEARCHRES')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('SEARCHRES');
        }

        $dest = Horde_Imap_Client_Mailbox::get($dest);
        $res = $this->_copy($dest, $options);

        if (($res === true) && !empty($options['force_map'])) {
            /* Need to manually create mapping from Message-ID data. */
            $query = new Horde_Imap_Client_Fetch_Query();
            $query->envelope();
            $fetch = $this->fetch($source, $query, array(
                'ids' => $options['ids']
            ));

            $res = array();
            foreach ($fetch as $val) {
                if ($uid = $this->_getUidByMessageId($dest, $val->getEnvelope()->message_id)) {
                    $res[$val->getUid()] = $uid;
                }
            }
        }

        return $res;
    }

    /**
     * Copy messages to another mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $dest  The destination mailbox.
     * @param array $options                   Additional options.
     *
     * @return mixed  An array mapping old UIDs (keys) to new UIDs (values) on
     *                success (if the IMAP server and/or driver support the
     *                UIDPLUS extension) or true.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _copy(Horde_Imap_Client_Mailbox $dest,
                                      $options);

    /**
     * Set quota limits. The server must support the IMAP QUOTA extension
     * (RFC 2087).
     *
     * @param mixed $root       The quota root. Either a
     *                          Horde_Imap_Client_Mailbox object or a string
     *                          (UTF-8).
     * @param array $resources  The resource values to set. Keys are the
     *                          resource atom name; value is the resource
     *                          value.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function setQuota($root, array $resources = array())
    {
        $this->login();

        if (!$this->queryCapability('QUOTA')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('QUOTA');
        }

        if (!empty($resources)) {
            $this->_setQuota(Horde_Imap_Client_Mailbox::get($root), $resources);
        }
    }

    /**
     * Set quota limits.
     *
     * @param Horde_Imap_Client_Mailbox $root  The quota root.
     * @param array $resources                 The resource values to set.
     *
     * @return boolean  True on success.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _setQuota(Horde_Imap_Client_Mailbox $root,
                                          $resources);

    /**
     * Get quota limits. The server must support the IMAP QUOTA extension
     * (RFC 2087).
     *
     * @param mixed $root  The quota root. Either a Horde_Imap_Client_Mailbox
     *                     object or a string (UTF-8).
     *
     * @return mixed  An array with resource keys. Each key holds an array
     *                with 2 values: 'limit' and 'usage'.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function getQuota($root)
    {
        $this->login();

        if (!$this->queryCapability('QUOTA')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('QUOTA');
        }

        return $this->_getQuota(Horde_Imap_Client_Mailbox::get($root));
    }

    /**
     * Get quota limits.
     *
     * @param Horde_Imap_Client_Mailbox $root  The quota root.
     *
     * @return mixed  An array with resource keys. Each key holds an array
     *                with 2 values: 'limit' and 'usage'.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getQuota(Horde_Imap_Client_Mailbox $root);

    /**
     * Get quota limits for a mailbox. The server must support the IMAP QUOTA
     * extension (RFC 2087).
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     *
     * @return mixed  An array with the keys being the quota roots. Each key
     *                holds an array with resource keys: each of these keys
     *                holds an array with 2 values: 'limit' and 'usage'.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function getQuotaRoot($mailbox)
    {
        $this->login();

        if (!$this->queryCapability('QUOTA')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('QUOTA');
        }

        return $this->_getQuotaRoot(Horde_Imap_Client_Mailbox::get($mailbox));
    }

    /**
     * Get quota limits for a mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     *
     * @return mixed  An array with the keys being the quota roots. Each key
     *                holds an array with resource keys: each of these keys
     *                holds an array with 2 values: 'limit' and 'usage'.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getQuotaRoot(Horde_Imap_Client_Mailbox $mailbox);

    /**
     * Get the ACL rights for a given mailbox. The server must support the
     * IMAP ACL extension (RFC 2086/4314).
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     *
     * @return array  An array with identifiers as the keys and
     *                Horde_Imap_Client_Data_Acl objects as the values.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getACL($mailbox)
    {
        $this->login();
        return $this->_getACL(Horde_Imap_Client_Mailbox::get($mailbox));
    }

    /**
     * Get ACL rights for a given mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     *
     * @return array  An array with identifiers as the keys and
     *                Horde_Imap_Client_Data_Acl objects as the values.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getACL(Horde_Imap_Client_Mailbox $mailbox);

    /**
     * Set ACL rights for a given mailbox/identifier.
     *
     * @param mixed $mailbox      A mailbox. Either a Horde_Imap_Client_Mailbox
     *                            object or a string (UTF-8).
     * @param string $identifier  The identifier to alter (UTF-8).
     * @param array $options      Additional options:
     *   - rights: (string) The rights to alter or set.
     *   - action: (string, optional) If 'add' or 'remove', adds or removes the
     *             specified rights. Sets the rights otherwise.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function setACL($mailbox, $identifier, $options)
    {
        $this->login();

        if (!$this->queryCapability('ACL')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('ACL');
        }

        if (empty($options['rights'])) {
            if (!isset($options['action']) ||
                (($options['action'] != 'add') &&
                 $options['action'] != 'remove')) {
                $this->_deleteACL(
                    Horde_Imap_Client_Mailbox::get($mailbox),
                    Horde_Imap_Client_Utf7imap::Utf8ToUtf7Imap($identifier)
                );
            }
            return;
        }

        $acl = ($options['rights'] instanceof Horde_Imap_Client_Data_Acl)
            ? $options['rights']
            : new Horde_Imap_Client_Data_Acl(strval($options['rights']));

        $options['rights'] = $acl->getString(
            $this->queryCapability('RIGHTS')
                ? Horde_Imap_Client_Data_AclCommon::RFC_4314
                : Horde_Imap_Client_Data_AclCommon::RFC_2086
        );
        if (isset($options['action'])) {
            switch ($options['action']) {
            case 'add':
                $options['rights'] = '+' . $options['rights'];
                break;
            case 'remove':
                $options['rights'] = '-' . $options['rights'];
                break;
            }
        }

        $this->_setACL(
            Horde_Imap_Client_Mailbox::get($mailbox),
            Horde_Imap_Client_Utf7imap::Utf8ToUtf7Imap($identifier),
            $options
        );
    }

    /**
     * Set ACL rights for a given mailbox/identifier.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     * @param string $identifier                  The identifier to alter
     *                                            (UTF7-IMAP).
     * @param array $options                      Additional options. 'rights'
     *                                            contains the string of
     *                                            rights to set on the server.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _setACL(Horde_Imap_Client_Mailbox $mailbox,
                                        $identifier, $options);

    /**
     * Deletes ACL rights for a given mailbox/identifier.
     *
     * @param mixed $mailbox      A mailbox. Either a Horde_Imap_Client_Mailbox
     *                            object or a string (UTF-8).
     * @param string $identifier  The identifier to delete (UTF-8).
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function deleteACL($mailbox, $identifier)
    {
        $this->login();

        if (!$this->queryCapability('ACL')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('ACL');
        }

        $this->_deleteACL(
            Horde_Imap_Client_Mailbox::get($mailbox),
            Horde_Imap_Client_Utf7imap::Utf8ToUtf7Imap($identifier)
        );
    }

    /**
     * Deletes ACL rights for a given mailbox/identifier.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     * @param string $identifier                  The identifier to delete
     *                                            (UTF7-IMAP).
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _deleteACL(Horde_Imap_Client_Mailbox $mailbox,
                                           $identifier);

    /**
     * List the ACL rights for a given mailbox/identifier. The server must
     * support the IMAP ACL extension (RFC 2086/4314).
     *
     * @param mixed $mailbox      A mailbox. Either a Horde_Imap_Client_Mailbox
     *                            object or a string (UTF-8).
     * @param string $identifier  The identifier to query (UTF-8).
     *
     * @return Horde_Imap_Client_Data_AclRights  An ACL data rights object.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function listACLRights($mailbox, $identifier)
    {
        $this->login();

        if (!$this->queryCapability('ACL')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('ACL');
        }

        return $this->_listACLRights(
            Horde_Imap_Client_Mailbox::get($mailbox),
            Horde_Imap_Client_Utf7imap::Utf8ToUtf7Imap($identifier)
        );
    }

    /**
     * Get ACL rights for a given mailbox/identifier.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     * @param string $identifier                  The identifier to query
     *                                            (UTF7-IMAP).
     *
     * @return Horde_Imap_Client_Data_AclRights  An ACL data rights object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _listACLRights(Horde_Imap_Client_Mailbox $mailbox,
                                               $identifier);

    /**
     * Get the ACL rights for the current user for a given mailbox. The
     * server must support the IMAP ACL extension (RFC 2086/4314).
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     *
     * @return Horde_Imap_Client_Data_Acl  An ACL data object.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_NoSupportExtension
     */
    public function getMyACLRights($mailbox)
    {
        $this->login();

        if (!$this->queryCapability('ACL')) {
            throw new Horde_Imap_Client_Exception_NoSupportExtension('ACL');
        }

        return $this->_getMyACLRights(Horde_Imap_Client_Mailbox::get($mailbox));
    }

    /**
     * Get the ACL rights for the current user for a given mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     *
     * @return Horde_Imap_Client_Data_Acl  An ACL data object.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getMyACLRights(Horde_Imap_Client_Mailbox $mailbox);

    /**
     * Return master list of ACL rights available on the server.
     *
     * @return array  A list of ACL rights.
     */
    public function allAclRights()
    {
        $this->login();

        $rights = array(
            Horde_Imap_Client::ACL_LOOKUP,
            Horde_Imap_Client::ACL_READ,
            Horde_Imap_Client::ACL_SEEN,
            Horde_Imap_Client::ACL_WRITE,
            Horde_Imap_Client::ACL_INSERT,
            Horde_Imap_Client::ACL_POST,
            Horde_Imap_Client::ACL_ADMINISTER
        );

        if ($capability = $this->queryCapability('RIGHTS')) {
            // Add rights defined in CAPABILITY string (RFC 4314).
            return array_merge($rights, str_split(reset($capability)));
        }

        // Add RFC 2086 rights (deprecated by RFC 4314, but need to keep for
        // compatibility with old servers).
        return array_merge($rights, array(
            Horde_Imap_Client::ACL_CREATE,
            Horde_Imap_Client::ACL_DELETE
        ));
    }

    /**
     * Get metadata for a given mailbox. The server must support either the
     * IMAP METADATA extension (RFC 5464) or the ANNOTATEMORE extension
     * (http://ietfreport.isoc.org/idref/draft-daboo-imap-annotatemore/).
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     * @param array $entries  The entries to fetch (UTF-8 strings).
     * @param array $options  Additional options:
     *   - depth: (string) Either "0", "1" or "infinity". Returns only the
     *            given value (0), only values one level below the specified
     *            value (1) or all entries below the specified value
     *            (infinity).
     *   - maxsize: (integer) The maximal size the returned values may have.
     *              DEFAULT: No maximal size.
     *
     * @return array  An array with metadata names as the keys and metadata
     *                values as the values. If 'maxsize' is set, and entries
     *                exist on the server larger than this size, the size will
     *                be returned in the key '*longentries'.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getMetadata($mailbox, $entries, array $options = array())
    {
        $this->login();

        if (!is_array($entries)) {
            $entries = array($entries);
        }

        return $this->_getMetadata(Horde_Imap_Client_Mailbox::get($mailbox), array_map(array('Horde_Imap_Client_Utf7imap', 'Utf8ToUtf7Imap'), $entries), $options);
    }

    /**
     * Get metadata for a given mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     * @param array $entries                      The entries to fetch
     *                                            (UTF7-IMAP strings).
     * @param array $options                      Additional options.
     *
     * @return array  An array with metadata names as the keys and metadata
     *                values as the values.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _getMetadata(Horde_Imap_Client_Mailbox $mailbox,
                                             $entries, $options);

    /**
     * Set metadata for a given mailbox/identifier.
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8). If empty, sets a
     *                        server annotation.
     * @param array $data     A set of data values. The metadata values
     *                        corresponding to the keys of the array will
     *                        be set to the values in the array.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function setMetadata($mailbox, $data)
    {
        $this->login();
        $this->_setMetadata(Horde_Imap_Client_Mailbox::get($mailbox), $data);
    }

    /**
     * Set metadata for a given mailbox/identifier.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  A mailbox.
     * @param array $data                         A set of data values. See
     *                                            setMetadata() for format.
     *
     * @throws Horde_Imap_Client_Exception
     */
    abstract protected function _setMetadata(Horde_Imap_Client_Mailbox $mailbox,
                                             $data);

    /* Public utility functions. */

    /**
     * Returns a unique identifier for the current mailbox status.
     *
     * @deprecated
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     * @param array $addl     Additional cache info to add to the cache ID
     *                        string.
     *
     * @return string  The cache ID string, which will change when the
     *                 composition of the mailbox changes. The uidvalidity
     *                 will always be the first element, and will be delimited
     *                 by the '|' character.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getCacheId($mailbox, array $addl = array())
    {
        return Horde_Imap_Client_Base_Deprecated::getCacheId($this, $mailbox, isset($this->_temp['enabled']['CONDSTORE']), $addl);
    }

    /**
     * Parses a cacheID created by getCacheId().
     *
     * @deprecated
     *
     * @param string $id  The cache ID.
     *
     * @return array  An array with the following information:
     *   - highestmodseq: (integer)
     *   - messages: (integer)
     *   - uidnext: (integer)
     *   - uidvalidity: (integer) Always present
     */
    public function parseCacheId($id)
    {
        return Horde_Imap_Client_Base_Deprecated::parseCacheId($id);
    }

    /**
     * Resolves an IDs object into a list of IDs.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox.
     * @param Horde_Imap_Client_Ids $ids          The Ids object.
     * @param integer $convert                    Convert to UIDs?
     *   - 0: No
     *   - 1: Only if $ids is not already a UIDs object
     *   - 2: Always
     *
     * @return Horde_Imap_Client_Ids  The list of IDs.
     */
    public function resolveIds(Horde_Imap_Client_Mailbox $mailbox,
                               Horde_Imap_Client_Ids $ids, $convert = 0)
    {
        $map = $this->_mailboxOb($mailbox)->map;

        if ($ids->special) {
            /* Optimization for ALL sequence searches. */
            if (!$convert && $ids->all && $ids->sequence) {
                $res = $this->status($mailbox, Horde_Imap_Client::STATUS_MESSAGES);
                return $this->getIdsOb($res['messages'] ? ('1:' . $res['messages']) : array(), true);
            }

            $convert = 2;
        } elseif (!$convert ||
                  (!$ids->sequence && ($convert == 1)) ||
                  $ids->isEmpty()) {
            return clone $ids;
        } else {
            /* Do an all or nothing: either we have all the numbers/UIDs in
             * memory and can return, or just send the whole ID query to the
             * server. Any advantage we would get by a partial search are
             * outweighed by the complexities needed to make the search and
             * then merge back into the original results. */
            $lookup = $map->lookup($ids);
            if (count($lookup) === count($ids)) {
                return $this->getIdsOb(array_values($lookup));
            }
        }

        $query = new Horde_Imap_Client_Search_Query();
        $query->ids($ids);

        $res = $this->search($mailbox, $query, array(
            'results' => array(
                Horde_Imap_Client::SEARCH_RESULTS_MATCH,
                Horde_Imap_Client::SEARCH_RESULTS_SAVE
            ),
            'sequence' => (!$convert && $ids->sequence),
            'sort' => array(Horde_Imap_Client::SORT_SEQUENCE)
        ));

        /* Update mapping. */
        if ($convert) {
            if ($ids->all) {
                $ids = $this->getIdsOb('1:' . count($res['match']));
            } elseif ($ids->special) {
                return $res['match'];
            }

            /* Sanity checking (Bug #12911). */
            $list1 = array_slice($ids->ids, 0, count($res['match']));
            $list2 = $res['match']->ids;
            if (!empty($list1) &&
                !empty($list2) &&
                (count($list1) === count($list2))) {
                $map->update(array_combine($list1, $list2));
            }
        }

        return $res['match'];
    }

    /**
     * Determines if the given charset is valid for search-related queries.
     * This check pertains just to the basic IMAP SEARCH command.
     *
     * @param string $charset  The query charset.
     *
     * @return boolean  True if server supports this charset.
     */
    public function validSearchCharset($charset)
    {
        $charset = strtoupper($charset);

        if ($charset == 'US-ASCII') {
            return true;
        }

        if (!isset($this->_init['s_charset'][$charset])) {
            $s_charset = $this->_init['s_charset'];

            /* Use a dummy search query and search for BADCHARSET response. */
            $query = new Horde_Imap_Client_Search_Query();
            $query->charset($charset, false);
            $query->ids($this->getIdsOb(1, true));
            $query->text('a');
            try {
                $this->search('INBOX', $query, array(
                    'nocache' => true,
                    'sequence' => true
                ));
                $s_charset[$charset] = true;
            } catch (Horde_Imap_Client_Exception $e) {
                $s_charset[$charset] = ($e->getCode() !== Horde_Imap_Client_Exception::BADCHARSET);
            }

            $this->_setInit('s_charset', $s_charset);
        }

        return $this->_init['s_charset'][$charset];
    }

    /* Mailbox syncing functions. */

    /**
     * Returns a unique token for the current mailbox synchronization status.
     *
     * @since 2.2.0
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     *
     * @return string  The sync token.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function getSyncToken($mailbox)
    {
        $out = array();

        foreach ($this->_syncStatus($mailbox) as $key => $val) {
            $out[] = $key . $val;
        }

        return base64_encode(implode(',', $out));
    }

    /**
     * Synchronize a mailbox from a sync token.
     *
     * @since 2.2.0
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     * @param string $token   A sync token generated by getSyncToken().
     * @param array $opts     Additional options:
     *   - criteria: (integer) Mask of Horde_Imap_Client::SYNC_* criteria to
     *               return. Defaults to SYNC_ALL.
     *   - ids: (Horde_Imap_Client_Ids) A cached list of UIDs. Unless QRESYNC
     *          is available on the server, failure to specify this option
     *          means SYNC_VANISHEDUIDS information cannot be returned.
     *
     * @return Horde_Imap_Client_Data_Sync  A sync object.
     *
     * @throws Horde_Imap_Client_Exception
     * @throws Horde_Imap_Client_Exception_Sync
     */
    public function sync($mailbox, $token, array $opts = array())
    {
        if (($token = base64_decode($token, true)) === false) {
            throw new Horde_Imap_Client_Exception_Sync('Bad token.', Horde_Imap_Client_Exception_Sync::BAD_TOKEN);
        }

        $sync = array();
        foreach (explode(',', $token) as $val) {
            $sync[substr($val, 0, 1)] = substr($val, 1);
        }

        return new Horde_Imap_Client_Data_Sync(
            $this,
            $mailbox,
            $sync,
            $this->_syncStatus($mailbox),
            (isset($opts['criteria']) ? $opts['criteria'] : Horde_Imap_Client::SYNC_ALL),
            (isset($opts['ids']) ? $opts['ids'] : null)
        );
    }

    /* Private utility functions. */

    /**
     * Store FETCH data in cache.
     *
     * @param Horde_Imap_Client_Fetch_Results $data  The fetch results.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _updateCache(Horde_Imap_Client_Fetch_Results $data)
    {
        if (!empty($this->_temp['fetch_nocache']) ||
            empty($this->_selected) ||
            !count($data) ||
            !$this->_initCache(true)) {
            return;
        }

        $c = $this->getParam('cache');
        if (in_array(strval($this->_selected), $c['fetch_ignore'])) {
            $this->_debug->info(sprintf(
                'CACHE: Ignoring FETCH data [%s]',
                $this->_selected
            ));
            return;
        }

        /* Optimization: we can directly use getStatus() here since we know
         * these values are initialized. */
        $mbox_ob = $this->_mailboxOb();
        $highestmodseq = $mbox_ob->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ);
        $uidvalidity = $mbox_ob->getStatus(Horde_Imap_Client::STATUS_UIDVALIDITY);

        $mapping = $modseq = $tocache = array();
        if (count($data)) {
            $cf = $this->_cacheFields();
        }

        foreach ($data as $v) {
            /* It is possible that we received FETCH information that doesn't
             * contain UID data. This is uncacheable so don't process. */
            if (!($uid = $v->getUid())) {
                return;
            }

            $tmp = array();

            if ($v->isDowngraded()) {
                $tmp[self::CACHE_DOWNGRADED] = true;
            }

            foreach ($cf as $key => $val) {
                if ($v->exists($key)) {
                    switch ($key) {
                    case Horde_Imap_Client::FETCH_ENVELOPE:
                        $tmp[$val] = $v->getEnvelope();
                        break;

                    case Horde_Imap_Client::FETCH_FLAGS:
                        if ($highestmodseq) {
                            $modseq[$uid] = $v->getModSeq();
                            $tmp[$val] = $v->getFlags();
                        }
                        break;

                    case Horde_Imap_Client::FETCH_HEADERS:
                        foreach ($this->_temp['headers_caching'] as $label => $hash) {
                            if ($hdr = $v->getHeaders($label)) {
                                $tmp[$val][$hash] = $hdr;
                            }
                        }
                        break;

                    case Horde_Imap_Client::FETCH_IMAPDATE:
                        $tmp[$val] = $v->getImapDate();
                        break;

                    case Horde_Imap_Client::FETCH_SIZE:
                        $tmp[$val] = $v->getSize();
                        break;

                    case Horde_Imap_Client::FETCH_STRUCTURE:
                        $tmp[$val] = clone $v->getStructure();
                        break;
                    }
                }
            }

            if (!empty($tmp)) {
                $tocache[$uid] = $tmp;
            }

            $mapping[$v->getSeq()] = $uid;
        }

        if (!empty($mapping)) {
            if (!empty($tocache)) {
                $this->_cache->set($this->_selected, $tocache, $uidvalidity);
            }

            $this->_mailboxOb()->map->update($mapping);
        }

        if (!empty($modseq)) {
            $this->_updateModSeq(max(array_merge($modseq, array($highestmodseq))));
            $mbox_ob->setStatus(Horde_Imap_Client::STATUS_SYNCFLAGUIDS, array_keys($modseq));
        }
    }

    /**
     * Moves cache entries from the current mailbox to another mailbox.
     *
     * @param Horde_Imap_Client_Mailbox $to  The destination mailbox.
     * @param array $map                     Mapping of source UIDs (keys) to
     *                                       destination UIDs (values).
     * @param string $uidvalid               UIDVALIDITY of destination
     *                                       mailbox.
     *
     * @throws Horde_Imap_Client_Exception
     */
    protected function _moveCache(Horde_Imap_Client_Mailbox $to, $map,
                                  $uidvalid)
    {
        if (!$this->_initCache()) {
            return;
        }

        $c = $this->getParam('cache');
        if (in_array(strval($to), $c['fetch_ignore'])) {
            $this->_debug->info(sprintf(
                'CACHE: Ignoring moving FETCH data (%s => %s)',
                $this->_selected,
                $to
            ));
            return;
        }

        $old = $this->_cache->get($this->_selected, array_keys($map), null);
        $new = array();

        foreach ($map as $key => $val) {
            if (!empty($old[$key])) {
                $new[$val] = $old[$key];
            }
        }

        if (!empty($new)) {
            $this->_cache->set($to, $new, $uidvalid);
        }
    }

    /**
     * Delete messages in the cache.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox.
     * @param Horde_Imap_Client_Ids $ids          The list of IDs to delete in
     *                                            $mailbox.
     * @param array $opts                         Additional options (not used
     *                                            in base class).
     *
     * @return Horde_Imap_Client_Ids  UIDs that were deleted.
     * @throws Horde_Imap_Client_Exception
     */
    protected function _deleteMsgs(Horde_Imap_Client_Mailbox $mailbox,
                                   Horde_Imap_Client_Ids $ids,
                                   array $opts = array())
    {
        if (!$this->_initCache()) {
            return $ids;
        }

        $mbox_ob = $this->_mailboxOb();
        $ids_ob = $ids->sequence
            ? $this->getIdsOb($mbox_ob->map->lookup($ids))
            : $ids;

        $this->_cache->deleteMsgs($mailbox, $ids_ob->ids);
        $mbox_ob->setStatus(Horde_Imap_Client::STATUS_SYNCVANISHED, $ids_ob->ids);
        $mbox_ob->map->remove($ids);

        return $ids_ob;
    }

    /**
     * Retrieve data from the search cache.
     *
     * @param string $type    The cache type ('search' or 'thread').
     * @param array $options  The options array of the calling function.
     *
     * @return mixed  Returns search cache metadata. If search was retrieved,
     *                data is in key 'data'.
     *                Returns null if caching is not available.
     */
    protected function _getSearchCache($type, $options)
    {
        $status = $this->status($this->_selected, Horde_Imap_Client::STATUS_HIGHESTMODSEQ | Horde_Imap_Client::STATUS_UIDVALIDITY);

        /* Search caching requires MODSEQ, which may not be active for a
         * mailbox. */
        if (empty($status['highestmodseq'])) {
            return null;
        }

        ksort($options);
        $cache = hash(
            (PHP_MINOR_VERSION >= 4) ? 'fnv132' : 'sha1',
            $type . serialize($options)
        );
        $cacheid = $this->getSyncToken($this->_selected);
        $ret = array();

        $md = $this->_cache->getMetaData(
            $this->_selected,
            $status['uidvalidity'],
            array(self::CACHE_SEARCH, self::CACHE_SEARCHID)
        );

        if (!isset($md[self::CACHE_SEARCHID]) ||
            ($md[self::CACHE_SEARCHID] != $cacheid)) {
            $md[self::CACHE_SEARCH] = array();
            $md[self::CACHE_SEARCHID] = $cacheid;
            if ($this->_debug->debug &&
                !isset($this->_temp['searchcacheexpire'][strval($this->_selected)])) {
                $this->_debug->info(sprintf(
                    'SEARCH: Expired from cache [%s]',
                    $this->_selected
                ));
                $this->_temp['searchcacheexpire'][strval($this->_selected)] = true;
            }
        } elseif (isset($md[self::CACHE_SEARCH][$cache])) {
            $this->_debug->info(sprintf(
                'SEARCH: Retrieved %s from cache (%s [%s])',
                $type,
                $cache,
                $this->_selected
            ));
            $ret['data'] = $md[self::CACHE_SEARCH][$cache];
            unset($md[self::CACHE_SEARCHID]);
        }

        return array_merge($ret, array(
            'id' => $cache,
            'metadata' => $md,
            'type' => $type
        ));
    }

    /**
     * Set data in the search cache.
     *
     * @param mixed $data    The cache data to store.
     * @param string $sdata  The search data returned from _getSearchCache().
     */
    protected function _setSearchCache($data, $sdata)
    {
        $sdata['metadata'][self::CACHE_SEARCH][$sdata['id']] = $data;

        $this->_cache->setMetaData($this->_selected, null, $sdata['metadata']);

        if ($this->_debug->debug) {
            $this->_debug->info(sprintf(
                'SEARCH: Saved %s to cache (%s [%s])',
                $sdata['type'],
                $sdata['id'],
                $this->_selected
            ));
            unset($this->_temp['searchcacheexpire'][strval($this->_selected)]);
        }
    }

    /**
     * Updates the cached MODSEQ value.
     *
     * @param integer $modseq  MODSEQ value to store.
     *
     * @return mixed  The MODSEQ of the old value if it was replaced (or false
     *                if it didn't exist or is the same).
     */
    protected function _updateModSeq($modseq)
    {
        if (!$this->_initCache(true)) {
            return false;
        }

        $mbox_ob = $this->_mailboxOb();
        $uidvalid = $mbox_ob->getStatus(Horde_Imap_Client::STATUS_UIDVALIDITY);
        $md = $this->_cache->getMetaData($this->_selected, $uidvalid, array(self::CACHE_MODSEQ));

        if (isset($md[self::CACHE_MODSEQ])) {
            if ($md[self::CACHE_MODSEQ] < $modseq) {
                $set = true;
                $sync = $md[self::CACHE_MODSEQ];
            } else {
                $set = false;
                $sync = 0;
            }
            $mbox_ob->setStatus(Horde_Imap_Client::STATUS_SYNCMODSEQ, $md[self::CACHE_MODSEQ]);
        } else {
            $set = true;
            $sync = 0;
        }

        if ($set) {
            $this->_cache->setMetaData($this->_selected, $uidvalid, array(
                self::CACHE_MODSEQ => $modseq
            ));
        }

        return $sync;
    }

    /**
     * Synchronizes the current mailbox cache with the server (using CONDSTORE
     * or QRESYNC).
     */
    protected function _condstoreSync()
    {
        $mbox_ob = $this->_mailboxOb();

        /* Check that modseqs are available in mailbox. */
        if (!($highestmodseq = $mbox_ob->getStatus(Horde_Imap_Client::STATUS_HIGHESTMODSEQ)) ||
            !($modseq = $this->_updateModSeq($highestmodseq))) {
            $mbox_ob->sync = true;
        }

        if ($mbox_ob->sync) {
            return;
        }

        $uids_ob = $this->getIdsOb($this->_cache->get($this->_selected, array(), array(), $mbox_ob->getStatus(Horde_Imap_Client::STATUS_UIDVALIDITY)));

        /* Are we caching flags? */
        if (array_key_exists(Horde_Imap_Client::FETCH_FLAGS, $this->_cacheFields())) {
            $fquery = new Horde_Imap_Client_Fetch_Query();
            $fquery->flags();

            /* Update flags in cache. Cache will be updated in _fetch(). */
            $this->_fetch(new Horde_Imap_Client_Fetch_Results(), array(
                array(
                    '_query' => $fquery,
                    'changedsince' => $modseq,
                    'ids' => $uids_ob
                )
            ));
        }

        /* Search for deleted messages, and remove from cache. */
        $vanished = $this->vanished($this->_selected, $modseq, array(
            'ids' => $uids_ob
        ));
        $disappear = array_diff($uids_ob->ids, $vanished->ids);
        if (!empty($disappear)) {
            $this->_deleteMsgs($this->_selected, $this->getIdsOb($disappear));
        }

        $mbox_ob->sync = true;
    }

    /**
     * Provide the list of available caching fields.
     *
     * @return array  The list of available caching fields (fields are in the
     *                key).
     */
    protected function _cacheFields()
    {
        $c = $this->getParam('cache');
        $out = $c['fields'];

        if (!isset($this->_temp['enabled']['CONDSTORE'])) {
            unset($out[Horde_Imap_Client::FETCH_FLAGS]);
        }

        return $out;
    }

    /**
     * Return the current mailbox synchronization status.
     *
     * @param mixed $mailbox  A mailbox. Either a Horde_Imap_Client_Mailbox
     *                        object or a string (UTF-8).
     *
     * @return array  An array with status data. (This data is not guaranteed
     *                to have any specific format).
     */
    protected function _syncStatus($mailbox)
    {
        $status = $this->status(
            $mailbox,
            Horde_Imap_Client::STATUS_HIGHESTMODSEQ |
            Horde_Imap_Client::STATUS_MESSAGES |
            Horde_Imap_Client::STATUS_UIDNEXT_FORCE |
            Horde_Imap_Client::STATUS_UIDVALIDITY
        );

        $fields = array('uidnext', 'uidvalidity');
        if (empty($status['highestmodseq'])) {
            $fields[] = 'messages';
        } else {
            $fields[] = 'highestmodseq';
        }

        $out = array();
        $sync_map = array_flip(Horde_Imap_Client_Data_Sync::$map);

        foreach ($fields as $val) {
            $out[$sync_map[$val]] = $status[$val];
        }

        return array_filter($out);
    }

    /**
     * Get a message UID by the Message-ID. Returns the last message in a
     * mailbox that matches.
     *
     * @param Horde_Imap_Client_Mailbox $mailbox  The mailbox to search
     * @param string $msgid                       Message-ID.
     *
     * @return string  UID (null if not found).
     */
    protected function _getUidByMessageId($mailbox, $msgid)
    {
        if (!$msgid) {
            return null;
        }

        $query = new Horde_Imap_Client_Search_Query();
        $query->headerText('Message-ID', $msgid);
        $res = $this->search($mailbox, $query, array(
            'results' => array(Horde_Imap_Client::SEARCH_RESULTS_MAX)
        ));

        return $res['max'];
    }

}
