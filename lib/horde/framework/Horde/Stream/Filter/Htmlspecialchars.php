<?php
/**
 * Stream filter class to pass data to htmlspecialchars() in chunks.
 *
 * WARNING: This filter is not safe on multi-byte character sets, because
 * multi-byte characters might be split on chunk boundaries. This filter should
 * be considered a duct tape if the data passed to htmlspecialchars() is too
 * big for PHP's memory_limit.
 *
 * Usage:
 *   stream_filter_register('htmlspecialchars', 'Horde_Stream_Filter_Htmlspecialchars');
 *   stream_filter_[app|pre]pend($stream, 'htmlspecialchars',
 *                               [ STREAM_FILTER_[READ|WRITE|ALL] ]);
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Stream_Filter
 */
class Horde_Stream_Filter_Htmlspecialchars extends php_user_filter
{
    /**
     * @see stream_filter_register()
     */
    #[ReturnTypeWillChange]
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = htmlspecialchars($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }
}
