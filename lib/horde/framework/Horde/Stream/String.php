<?php
/**
 * Copyright 2014-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 */

/**
 * Implementation of Horde_Stream that uses a PHP native string variable
 * for the internal storage.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2014-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 * @since     1.6.0
 */
class Horde_Stream_String extends Horde_Stream
{
    /**
     * Constructor.
     *
     * @param array $opts  Additional configuration options:
     * <pre>
     *   - string: (string) [REQUIRED] The PHP string.
     * </pre>
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $opts = array())
    {
        if (!isset($opts['string']) || !is_string($opts['string'])) {
            throw new InvalidArgumentException('Need a PHP string.');
        }

        $this->stream = Horde_Stream_Wrapper_String::getStream($opts['string']);
        unset($opts['string']);

        parent::__construct($opts);
    }

}
