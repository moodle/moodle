<?php

namespace PhpXmlRpc\Helper;

use PhpXmlRpc\PhpXmlRpc;

/**
 * Helps to convert timestamps to the xml-rpc date format.
 *
 * Feature creep -- add support for custom TZs
 */
class Date
{
    /**
     * Given a timestamp, return the corresponding ISO8601 encoded string.
     *
     * Really, timezones ought to be supported but the XML-RPC spec says:
     *
     * "Don't assume a timezone. It should be specified by the server in its documentation what assumptions it makes
     *  about timezones."
     *
     * This routine always encodes to local time unless $utc is set to 1, in which case UTC output is produced and an
     * adjustment for the local timezone's offset is made
     *
     * @param int|\DateTimeInterface $timet timestamp or datetime
     * @param bool|int $utc (0 or 1)
     * @return string
     */
    public static function iso8601Encode($timet, $utc = 0)
    {
        if (is_a($timet, 'DateTimeInterface') || is_a($timet, 'DateTime')) {
            $timet = $timet->getTimestamp();
        }
        if (!$utc) {
            $t = date('Ymd\TH:i:s', $timet);
        } else {
            $t = gmdate('Ymd\TH:i:s', $timet);
        }

        return $t;
    }

    /**
     * Given an ISO8601 date string, return a timestamp in the localtime, or UTC.
     *
     * @param string $idate
     * @param bool|int $utc either 0 (assume date is in local time) or 1 (assume date is in UTC)
     *
     * @return int (timestamp) 0 if the source string does not match the xml-rpc dateTime format
     */
    public static function iso8601Decode($idate, $utc = 0)
    {
        $t = 0;
        if (preg_match(PhpXmlRpc::$xmlrpc_datetime_format, $idate, $regs)) {
            if ($utc) {
                $t = gmmktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
            } else {
                $t = mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
            }
        }

        return $t;
    }
}
