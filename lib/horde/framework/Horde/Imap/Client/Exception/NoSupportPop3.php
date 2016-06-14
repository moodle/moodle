<?php
/**
 * Copyright 2012-2014 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */

/**
 * Exception thrown for non-supported IMAP features on POP3 servers.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Exception_NoSupportPop3
extends Horde_Imap_Client_Exception
{
    /**
     * Constructor.
     *
     * @param string $feature  The feature not supported in POP3.
     */
    public function __construct($feature)
    {
        parent::__construct(
            sprintf(Horde_Imap_Client_Translation::r("%s not supported on POP3 servers."), $feature),
            self::NOT_SUPPORTED
        );
    }

}
