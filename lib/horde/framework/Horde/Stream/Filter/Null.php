<?php
/**
 * Stream filter class to remove null (\0) values.
 *
 * Usage:
 *   stream_filter_register('horde_null', 'Horde_Stream_Filter_Null');
 *   stream_filter_[app|pre]pend($stream, 'horde_null',
 *                               [ STREAM_FILTER_[READ|WRITE|ALL] ],
 *                               [ $params ]);
 *
 * $params is an array that can contain the following:
 *   - replace: (string) The string to use to replace null characters with.
 *          DEFAULT: ''
 *
 * Copyright 2012-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael J Rubinsky <mrubinsk@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Stream_Filter
 */
class Horde_Stream_Filter_Null extends php_user_filter
{
    /**
     * Search array.
     *
     * @var mixed
     */
    protected $_search = "\0";

    /**
     * Replacement data
     *
     * @var mixed
     */
    protected $_replace;

    /**
     * @see stream_filter_register()
     */
    #[ReturnTypeWillChange]
    public function onCreate()
    {
        $this->_replace = isset($this->params->replace)
            ? $this->params->replace
            : '';

        return true;
    }

    /**
     * @see stream_filter_register()
     */
    #[ReturnTypeWillChange]
    public function filter($in, $out, &$consumed, $closing)
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $bucket->data = str_replace($this->_search, $this->_replace, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        return PSFS_PASS_ON;
    }

}
