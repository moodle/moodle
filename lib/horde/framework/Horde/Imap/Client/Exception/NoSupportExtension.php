<?php
/**
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Exception thrown for non-supported server extensions.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Exception_NoSupportExtension
extends Horde_Imap_Client_Exception
{
    /**
     * The extension not supported on the server.
     *
     * @var string
     */
    public $extension;

    /**
     * Constructor.
     *
     * @param string $extension  The extension not supported on the server.
     * @param string $msg        A non-standard error message to use instead
     *                           of the default.
     */
    public function __construct($extension, $msg = null)
    {
        $this->extension = $extension;

        if (is_null($msg)) {
            $msg = sprintf(
                Horde_Imap_Client_Translation::r("The server does not support the %s extension."),
                $extension
            );
        }

        parent::__construct($msg, self::NOT_SUPPORTED);
    }

}
