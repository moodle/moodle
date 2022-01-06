<?php
/**
 * Stream filter class to convert binary data into hexadecimal.
 *
 * Usage:
 *   stream_filter_register('horde_bin2hex', 'Horde_Stream_Filter_Bin2hex');
 *   stream_filter_[app|pre]pend($stream, 'horde_bin2hex',
 *                               [ STREAM_FILTER_[READ|WRITE|ALL] ]);
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Stream_Filter
 */
class Horde_Stream_Filter_Bin2hex extends php_user_filter
{
    /**
     * @see stream_filter_register()
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = bin2hex($bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

}
