<?php

namespace SimpleSAML\Module\statistics;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class DateHandlerMonth extends DateHandler
{
    /**
     * Constructor
     *
     * @param integer $offset Date offset
     */
    public function __construct($offset)
    {
        $this->offset = $offset;
    }


    /**
     * @param int $epoch
     * @param int $slotsize
     * @return int
     */
    public function toSlot($epoch, $slotsize)
    {
        $dsttime = $this->getDST($epoch) + $epoch;
        $parsed = getdate($dsttime);
        $slot = (($parsed['year'] - 2000) * 12) + $parsed['mon'] - 1;
        return $slot;
    }


    /**
     * @param int $slot
     * @param int $slotsize
     * @return int
     */
    public function fromSlot($slot, $slotsize)
    {
        $month = ($slot % 12);
        $year = 2000 + intval(floor($slot / 12));
        return mktime(0, 0, 0, $month + 1, 1, $year);
    }


    /**
     * @param int $from
     * @param int $to
     * @param int $slotsize
     * @param string $dateformat
     * @return string
     */
    public function prettyHeader($from, $to, $slotsize, $dateformat)
    {
        $month = ($from % 12) + 1;
        $year = 2000 + intval(floor($from / 12));
        return $year . '-' . $month;
    }
}
