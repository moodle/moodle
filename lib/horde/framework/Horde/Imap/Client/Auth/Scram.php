<?php
/**
 * Copyright 2015-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Provides authentication via the SCRAM SASL mechanism (RFC 5802 [3]).
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2015-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.29.0
 */
class Horde_Imap_Client_Auth_Scram
{
    /**
     * AuthMessage (RFC 5802 [3]).
     *
     * @var string
     */
    protected $_authmsg;

    /**
     * Hash name.
     *
     * @var string
     */
    protected $_hash;

    /**
     * Number of Hi iterations (RFC 5802 [2]).
     *
     * @var integer
     */
    protected $_iterations;

    /**
     * Nonce.
     *
     * @var string
     */
    protected $_nonce;

    /**
     * Password.
     *
     * @var string
     */
    protected $_pass;

    /**
     * Server salt.
     *
     * @var string
     */
    protected $_salt;

    /**
     * Calculated server signature value.
     *
     * @var string
     */
    protected $_serversig;

    /**
     * Username.
     *
     * @var string
     */
    protected $_user;

    /**
     * Constructor.
     *
     * @param string $user  Username.
     * @param string $pass  Password.
     * @param string $hash  Hash name.
     *
     * @throws Horde_Imap_Client_Exception
     */
    public function __construct($user, $pass, $hash = 'SHA1')
    {
        $error = false;

        $this->_hash = $hash;

        try {
            if (!class_exists('Horde_Stringprep') ||
                !class_exists('Horde_Crypt_Blowfish_Pbkdf2')) {
                throw new Exception();
            }

            Horde_Stringprep::autoload();
            $saslprep = new Znerol\Component\Stringprep\Profile\SASLprep();

            $this->_user = $saslprep->apply(
                $user,
                'UTF-8',
                Znerol\Component\Stringprep\Profile::MODE_QUERY
            );
            $this->_pass = $saslprep->apply(
                $pass,
                'UTF-8',
                Znerol\Component\Stringprep\Profile::MODE_STORE
            );
        } catch (Znerol\Component\Stringprep\ProfileException $e) {
            $error = true;
        } catch (Exception $e) {
            $error = true;
        }

        if ($error) {
            throw new Horde_Imap_Client_Exception(
                Horde_Imap_Client_Translation::r("Authentication failure."),
                Horde_Imap_Client_Exception::LOGIN_AUTHORIZATIONFAILED
            );
        }

        /* Generate nonce. (Done here so this can be overwritten for
         * testing purposes.) */
        $this->_nonce = strval(new Horde_Support_Randomid());
    }

    /**
     * Return the initial client message.
     *
     * @return string  Initial client message.
     */
    public function getClientFirstMessage()
    {
        /* n: client doesn't support channel binding,
         * <empty>,
         * n=<user>: SASLprepped username with "," and "=" escaped,
         * r=<nonce>: Random nonce */
        $this->_authmsg = 'n=' . str_replace(
            array(',', '='),
            array('=2C', '=3D'),
            $this->_user
        ) . ',r=' . $this->_nonce;

        return 'n,,' . $this->_authmsg;
    }

    /**
     * Process the initial server message response.
     *
     * @param string $msg  Initial server response.
     *
     * @return boolean  False if authentication failed at this stage.
     */
    public function parseServerFirstMessage($msg)
    {
        $i = $r = $s = false;

        foreach (explode(',', $msg) as $val) {
            list($attr, $aval) = array_map('trim', explode('=', $val, 2));

            switch ($attr) {
            case 'i':
                $this->_iterations = intval($aval);
                $i = true;
                break;

            case 'r':
                /* Beginning of server-provided nonce MUST be the same as the
                 * nonce we provided. */
                if (strpos($aval, $this->_nonce) !== 0) {
                    return false;
                }
                $this->_nonce = $aval;
                $r = true;
                break;

            case 's':
                $this->_salt = base64_decode($aval);
                $s = true;
                break;
            }
        }

        if ($i && $r && $s) {
            $this->_authmsg .= ',' . $msg;
            return true;
        }

        return false;
    }

    /**
     * Return the final client message.
     *
     * @return string  Final client message.
     */
    public function getClientFinalMessage()
    {
        $final_msg = 'c=biws,r=' . $this->_nonce;

        /* Salted password. */
        $s_pass = strval(new Horde_Crypt_Blowfish_Pbkdf2(
            $this->_pass,
            strlen(hash($this->_hash, '', true)),
            array(
                'algo' => $this->_hash,
                'i_count' => $this->_iterations,
                'salt' => $this->_salt
            )
        ));

        /* Client key. */
        $c_key = hash_hmac($this->_hash, 'Client Key', $s_pass, true);

        /* Stored key. */
        $s_key = hash($this->_hash, $c_key, true);

        /* Client signature. */
        $auth_msg = $this->_authmsg . ',' . $final_msg;
        $c_sig = hash_hmac($this->_hash, $auth_msg, $s_key, true);

        /* Proof. */
        $proof = $c_key ^ $c_sig;

        /* Server signature. */
        $this->_serversig = hash_hmac(
            $this->_hash,
            $auth_msg,
            hash_hmac($this->_hash, 'Server Key', $s_pass, true),
            true
        );

        /* c=biws: channel-binding ("biws" = base64('n,,')),
         * p=<proof>: base64 encoded ClientProof,
         * r=<nonce>: Nonce as returned from the server. */
        return $final_msg . ',p=' . base64_encode($proof);
    }

    /**
     * Process the final server message response.
     *
     * @param string $msg  Final server response.
     *
     * @return boolean  False if authentication failed.
     */
    public function parseServerFinalMessage($msg)
    {
        foreach (explode(',', $msg) as $val) {
            list($attr, $aval) = array_map('trim', explode('=', $val, 2));

            switch ($attr) {
            case 'e':
                return false;

            case 'v':
                return (base64_decode($aval) === $this->_serversig);
            }
        }

        return false;
    }

}
