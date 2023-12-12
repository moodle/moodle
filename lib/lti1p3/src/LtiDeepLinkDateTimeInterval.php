<?php

namespace Packback\Lti1p3;

use DateTime;

class LtiDeepLinkDateTimeInterval
{
    private ?DateTime $start;
    private ?DateTime $end;

    public function __construct(DateTime $start = null, DateTime $end = null)
    {
        if ($start !== null && $end !== null && $end < $start) {
            throw new LtiException('Interval start time cannot be greater than end time');
        }

        $this->start = $start ?? null;
        $this->end = $end ?? null;
    }

    public static function new(): LtiDeepLinkDateTimeInterval
    {
        return new LtiDeepLinkDateTimeInterval();
    }

    public function setStart(?DateTime $start): LtiDeepLinkDateTimeInterval
    {
        $this->start = $start;

        return $this;
    }

    public function getStart(): ?DateTime
    {
        return $this->start;
    }

    public function setEnd(?DateTime $end): LtiDeepLinkDateTimeInterval
    {
        $this->end = $end;

        return $this;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function toArray(): array
    {
        if (!isset($this->start) && !isset($this->end)) {
            throw new LtiException('At least one of the interval bounds must be specified on the object instance');
        }

        if ($this->start !== null && $this->end !== null && $this->end < $this->start) {
            throw new LtiException('Interval start time cannot be greater than end time');
        }

        $dateTimeInterval = [];

        if (isset($this->start)) {
            $dateTimeInterval['startDateTime'] = $this->start->format(DateTime::ATOM);
        }
        if (isset($this->end)) {
            $dateTimeInterval['endDateTime'] = $this->end->format(DateTime::ATOM);
        }

        return $dateTimeInterval;
    }
}
