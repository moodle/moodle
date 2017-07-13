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
 * Stream filter to analyze an IMAP string to determine how to send to the
 * server.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2012-2014 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Imap_Client
 */
class Horde_Imap_Client_Data_Format_Filter_String extends php_user_filter
{
    /**
     * @see stream_filter_register()
     */
    public function onCreate()
    {
        $this->params->binary = false;
        $this->params->literal = false;
        // no_quote_list is used below as a config option
        $this->params->quoted = false;

        return true;
    }

    /**
     * @see stream_filter_register()
     */
    public function filter($in, $out, &$consumed, $closing)
    {
        $p = $this->params;
        $skip = false;

        while ($bucket = stream_bucket_make_writeable($in)) {
            if (!$skip) {
                $len = $bucket->datalen;
                $str = $bucket->data;

                for ($i = 0; $i < $len; ++$i) {
                    $chr = ord($str[$i]);

                    switch ($chr) {
                    case 0: // null
                        $p->binary = true;
                        $p->literal = true;

                        // No need to scan input anymore.
                        $skip = true;
                        break 2;

                    case 10: // LF
                    case 13: // CR
                        $p->literal = true;
                        break;

                    case 32: // SPACE
                    case 34: // "
                    case 40: // (
                    case 41: // )
                    case 92: // \
                    case 123: // {
                    case 127: // DEL
                        // These are all invalid ATOM characters.
                        $p->quoted = true;
                        break;

                    case 37: // %
                    case 42: // *
                        // These are not quoted if being used as wildcards.
                        if (empty($p->no_quote_list)) {
                            $p->quoted = true;
                        }
                        break;

                    default:
                        if ($chr < 32) {
                            // CTL characters must be, at a minimum, quoted.
                            $p->quoted = true;
                        } elseif ($chr > 127) {
                            // 8-bit chars must be in a literal.
                            $p->literal = true;
                        }
                        break;
                    }
                }
            }

            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }

        if ($p->literal) {
            $p->quoted = false;
        }

        return PSFS_PASS_ON;
    }

}
