<?php

namespace SimpleSAML\Module\statistics;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class DateHandler
{
    /** @var int */
    protected $offset;


    /**
     * Constructor
     *
     * @param int $offset Date offset
     */
    public function __construct($offset)
    {
        $this->offset = $offset;
    }


    /**
     * @param int $timestamp
     * @return int
     */
    protected function getDST($timestamp)
    {
        if (idate('I', $timestamp)) {
            return 3600;
        }
        return 0;
    }


    /**
     * @param int $epoch
     * @param int $slotsize
     * @return float
     */
    public function toSlot($epoch, $slotsize)
    {
        $dst = $this->getDST($epoch);
        return floor(($epoch + $this->offset + $dst) / $slotsize);
    }


    /**
     * @param int $slot
     * @param int $slotsize
     * @return int
     */
    public function fromSlot($slot, $slotsize)
    {
        $temp = $slot * $slotsize - $this->offset;
        $dst = $this->getDST($temp);
        return $slot * $slotsize - $this->offset - $dst;
    }


    /**
     * @param int $epoch
     * @param string $dateformat
     * @return string
     */
    public function prettyDateEpoch($epoch, $dateformat)
    {
        return date($dateformat, $epoch);
    }


    /**
     * @param int $slot
     * @param int $slotsize
     * @param string $dateformat
     * @return string
     */
    public function prettyDateSlot($slot, $slotsize, $dateformat)
    {
        return $this->prettyDateEpoch($this->fromSlot($slot, $slotsize), $dateformat);
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
        $text = $this->prettyDateSlot($from, $slotsize, $dateformat);
        $text .= ' to ';
        $text .= $this->prettyDateSlot($to, $slotsize, $dateformat);
        return $text;
    }
}
