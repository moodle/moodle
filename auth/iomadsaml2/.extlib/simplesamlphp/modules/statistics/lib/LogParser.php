<?php

namespace SimpleSAML\Module\statistics;

/**
 * @author Andreas Åkre Solberg <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class LogParser
{
    /** @var integer */
    private $datestart;

    /** @var integer */
    private $datelength;

    /** @var integer */
    private $offset;


    /**
     * Constructor
     *
     * @param integer $datestart   At which char is the date starting
     * @param integer $datelength  How many characters is the date (on the b
     * @param integer $offset      At which char is the rest of the entries starting
     */
    public function __construct($datestart, $datelength, $offset)
    {
        $this->datestart = $datestart;
        $this->datelength = $datelength;
        $this->offset = $offset;
    }


    /**
     * @param string $line
     *
     * @return integer
     */
    public function parseEpoch($line)
    {
        $epoch = strtotime(substr($line, 0, $this->datelength));
        if ($epoch > time() + 2678400) { // 60 * 60 * 24 * 31 = 2678400
            /**
             * More than a month in the future - probably caused by
             * the log files missing the year.
             * We will therefore subtrackt one year.
             */
            $hour = intval(gmdate('H', $epoch));
            $minute = intval(gmdate('i', $epoch));
            $second = intval(gmdate('s', $epoch));
            $month = intval(gmdate('n', $epoch));
            $day = intval(gmdate('j', $epoch));
            $year = intval(gmdate('Y', $epoch)) - 1;
            $epoch = gmmktime($hour, $minute, $second, $month, $day, $year);
        }
        return $epoch;
    }


    /**
     * @param string $line
     *
     * @return array
     */
    public function parseContent($line)
    {
        $contentstr = substr($line, $this->offset);
        $content = explode(' ', $contentstr);
        return $content;
    }
}
