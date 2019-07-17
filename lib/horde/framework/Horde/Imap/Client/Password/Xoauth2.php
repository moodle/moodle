<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Generates an OAuth 2.0 authentication token as used in the Gmail XOAUTH2
 * authentication mechanism.
 *
 * See: https://developers.google.com/gmail/xoauth2_protocol
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 * @since     2.16.0
 */
class Horde_Imap_Client_Password_Xoauth2
implements Horde_Imap_Client_Base_Password
{
    /**
     * Access token.
     *
     * @var string
     */
    public $access_token;

    /**
     * Username.
     *
     * @var string
     */
    public $username;

    /**
     * Constructor.
     *
     * @param string $username      The username.
     * @param string $access_token  The access token.
     */
    public function __construct($username, $access_token)
    {
        $this->username = $username;
        $this->access_token = $access_token;
    }

    /**
     * Return the password to use for the server connection.
     *
     * @return string  The password.
     */
    public function getPassword()
    {
        // base64("user=" {User} "^Aauth=Bearer " {Access Token} "^A^A")
        // ^A represents a Control+A (\001)
        return base64_encode(
            'user=' . $this->username . "\1" .
            'auth=Bearer ' . $this->access_token . "\1\1"
        );
    }

}
