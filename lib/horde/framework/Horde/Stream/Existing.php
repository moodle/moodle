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
 * @package   Stream
 */

/**
 * Implementation of Horde_Stream for an existing stream resource. This
 * resource will be directly modified when manipulating using this class.
 *
 * @since 1.2.0
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Stream
 */
class Horde_Stream_Existing extends Horde_Stream
{
    /**
     * Constructor.
     *
     * @param array $opts  Additional configuration options:
     *   - stream: (resource) [REQUIRED] The stream resource.
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $opts = array())
    {
        if (!isset($opts['stream']) || !is_resource($opts['stream'])) {
            throw new InvalidArgumentException('Need a stream resource.');
        }

        $this->stream = $opts['stream'];
        unset($opts['stream']);

        parent::__construct($opts);
    }

}
