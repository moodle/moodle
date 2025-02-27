<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core_calendar\output;

Use DateTimeInterface;
use DateTimeImmutable;
use core\output\templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\url;

/**
 * Class humantimeperiod.
 *
 * This class is used to render a time period as a human readable date.
 * The main difference between userdate and this class is that this class
 * will render the date as "Today", "Yesterday", "Tomorrow" if the date is
 * close to the current date. Also, it will add styling if the date
 * is near.
 *
 * @package    core_calendar
 * @copyright  2025 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class humantimeperiod implements renderable, templatable {

    /** @var int|null Number of seconds that indicates a nearby date. Default to DAYSECS. Use null for no indication. */
    protected $near = DAYSECS;

    /** @var url|null URL to link the date to. */
    protected ?url $link = null;

    /** @var string|null An optional date format to apply. */
    protected ?string $langtimeformat = null;

    /** @var bool Whether to use human common words or not. */
    protected bool $userelatives = true;

    /**
     * Class constructor.
     *
     * @param DateTimeImmutable $startdatetime The starting timestamp.
     * @param DateTimeImmutable|null $enddatetime The ending timestamp.
     */
    protected function __construct(
        /** @var DateTimeImmutable $startdatetime The starting date time. */
        protected DateTimeImmutable $startdatetime,
        /** @var DateTimeImmutable|null $enddatetime The ending date time. */
        protected ?DateTimeImmutable $enddatetime,
    ) {
    }

    /**
     * Creates a new humantimeperiod instance from a timestamp.
     *
     * @param int $starttimestamp The starting timestamp.
     * @param int|null $endtimestamp The ending timestamp.
     * @param int|null $near The number of seconds that indicates a nearby date. Default to DAYSECS, use null for no indication.
     * @param url|null $link URL to link the date to.
     * @param string|null $langtimeformat Lang date and time format to use to format the date.
     * @param bool $userelatives Whether to use human common words or not.
     * @return humantimeperiod The new instance.
     */
    public static function create_from_timestamp(
        int $starttimestamp,
        ?int $endtimestamp,
        ?int $near = DAYSECS,
        ?url $link = null,
        ?string $langtimeformat = null,
        bool $userelatives = true,
    ): self {

        return self::create_from_datetime(
            (new DateTimeImmutable("@{$starttimestamp}")),
            $endtimestamp ? (new DateTimeImmutable("@{$endtimestamp}")) : null,
            $near,
            $link,
            $langtimeformat,
            $userelatives
        );
    }

    /**
     * Creates a new humantimeperiod instance from a datetime.
     *
     * @param DateTimeInterface $startdatetime The starting datetime.
     * @param DateTimeInterface|null $enddatetime The ending datetime.
     * @param int|null $near The number of seconds that indicates a nearby date. Default to DAYSECS, use null for no indication.
     * @param url|null $link URL to link the date to.
     * @param string|null $langtimeformat Lang date and time format to use to format the date.
     * @param bool $userelatives Whether to use human common words or not.
     * @return humantimeperiod The new instance.
     */
    public static function create_from_datetime(
        DateTimeInterface $startdatetime,
        ?DateTimeInterface $enddatetime,
        ?int $near = DAYSECS,
        ?url $link = null,
        ?string $langtimeformat = null,
        bool $userelatives = true,
    ): self {

        // Always use an Immutable object to ensure that the value does not change externally before it is rendered.
        if (!($startdatetime instanceof DateTimeImmutable)) {
            $startdatetime = DateTimeImmutable::createFromInterface($startdatetime);
        }
        if ($enddatetime != null && !($enddatetime instanceof DateTimeImmutable)) {
            $enddatetime = DateTimeImmutable::createFromInterface($enddatetime);
        }

        return (new self($startdatetime, $enddatetime))
            ->set_near_limit($near)
            ->set_link($link)
            ->set_lang_time_format($langtimeformat)
            ->set_use_relatives($userelatives);
    }

    /**
     * Sets the number of seconds within which a date is considered near.
     *
     * @param int|null $near The number of seconds within which a date is considered near.
     * @return humantimeperiod The instance.
     */
    public function set_near_limit(?int $near): self {
        $this->near = $near;
        return $this;
    }

    /**
     * Sets the link for the date. If null, no link will be added.
     *
     * @param url|null $link The link for the date.
     * @return humantimeperiod The instance.
     */
    public function set_link(?url $link): self {
        $this->link = $link;
        return $this;
    }

    /**
     * Sets an optional date format to apply.
     *
     * @param string|null $langtimeformat Lang date and time format to use to format the date.
     * @return humantimeperiod The instance.
     */
    public function set_lang_time_format(?string $langtimeformat): self {
        $this->langtimeformat = $langtimeformat;
        return $this;
    }

    /**
     * Sets whether to use human relative terminology.
     *
     * @param bool $userelatives Whether to use human relative terminology.
     * @return humantimeperiod The instance.
     */
    public function set_use_relatives(bool $userelatives): self {
        $this->userelatives = $userelatives;
        return $this;
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $period = $this->format_period();
        return [
            'startdate' => $period['startdate']->export_for_template($output),
            'enddate' => $period['enddate'] ? $period['enddate']->export_for_template($output) : null,
        ];
    }

    /**
     * Format a time periods based on 2 dates.
     *
     * @return array An array of one or two humandate elements.
     */
    private function format_period(): array {

        $linkstart = null;
        $linkend = null;
        if ($this->link) {
            $linkstart = new url($this->link, ['view' => 'day', 'time' => $this->startdatetime->getTimestamp()]);
            $linkend = new url($this->link, ['view' => 'day', 'time' => $this->enddatetime->getTimestamp()]);
        }

        $startdate = humandate::create_from_datetime(
            datetime: $this->startdatetime,
            near: $this->near,
            link: $linkstart,
            langtimeformat: $this->langtimeformat,
            userelatives: $this->userelatives
        );

        if ($this->enddatetime == null || $this->startdatetime == $this->enddatetime) {
            return [
                'startdate' => $startdate,
                'enddate' => null,
            ];
        }

        // Get the midnight of the day the event will start.
        $usermidnightstart = usergetmidnight($this->startdatetime->getTimestamp());
        // Get the midnight of the day the event will end.
        $usermidnightend = usergetmidnight($this->enddatetime->getTimestamp());
        // Check if we will still be on the same day.
        $issameday = ($usermidnightstart == $usermidnightend);

        $enddate = humandate::create_from_datetime(
            datetime: $this->enddatetime,
            near: $this->near,
            timeonly: $issameday,
            link: $linkend,
            langtimeformat: $this->langtimeformat,
            userelatives: $this->userelatives
        );

        return [
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
    }
}
