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

use DateInterval;
use DateTimeInterface;
use DateTimeImmutable;
use core\output\pix_icon;
use core\output\templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\clock;
use core\url;

/**
 * Class humandate.
 *
 * This class is used to render a timestamp as a human readable date.
 * The main difference between userdate and this class is that this class
 * will render the date as "Today", "Yesterday", "Tomorrow" if the date is
 * close to the current date. Also, it will add alert styling if the date
 * is near.
 *
 * @package    core_calendar
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class humandate implements renderable, templatable {

    /** @var int|null The number of seconds within which a date is considered near. 1 day by default. */
    protected ?int $near = DAYSECS;

    /** @var bool Whether we should show time only or date and time. */
    protected bool $timeonly = false;

    /** @var url|null Link for the date. */
    protected ?url $link = null;

    /** @var string|null An optional date format to apply.  */
    protected ?string $langtimeformat = null;

    /** @var bool Whether to use human relative terminology. */
    protected bool $userelatives = true;

    /** @var clock The clock interface to handle time. */
    protected clock $clock;

    /**
     * Class constructor.
     *
     * Use the factory methods, such as create_from_timestamp or create_from_datetime, instead.
     *
     * @param DateTimeImmutable $datetime The datetime.
     */
    protected function __construct(
        /** @var DateTimeImmutable $datetime The datetime. **/
        protected DateTimeImmutable $datetime,
    ) {
        $this->clock = \core\di::get(clock::class);
    }

    /**
     * Creates a new humandate instance from a timestamp.
     *
     * @param int $timestamp The timestamp.
     * @param int|null $near The number of seconds within which a date is considered near. 1 day by default.
     * @param bool $timeonly Whether we should show time only or date and time.
     * @param url|null $link Link for the date.
     * @param string|null $langtimeformat An optional date format to apply.
     * @param bool $userelatives Whether to use human relative terminology.
     * @return humandate The new instance.
     */
    public static function create_from_timestamp(
        int $timestamp,
        ?int $near = DAYSECS,
        bool $timeonly = false,
        ?url $link = null,
        ?string $langtimeformat = null,
        bool $userelatives = true,
    ): self {

        return self::create_from_datetime(
            (new DateTimeImmutable("@{$timestamp}")),
            $near,
            $timeonly,
            $link,
            $langtimeformat,
            $userelatives
        );
    }

    /**
     * Creates a new humandate instance from a datetime.
     *
     * @param DateTimeInterface $datetime The datetime.
     * @param int|null $near The number of seconds within which a date is considered near. 1 day by default.
     * @param bool $timeonly Whether we should show time only or date and time.
     * @param url|null $link Link for the date.
     * @param string|null $langtimeformat An optional date format to apply.
     * @param bool $userelatives Whether to use human relative terminology.
     * @return humandate The new instance.
     */
    public static function create_from_datetime(
        DateTimeInterface $datetime,
        ?int $near = DAYSECS,
        bool $timeonly = false,
        ?url $link = null,
        ?string $langtimeformat = null,
        bool $userelatives = true,
    ): self {

        if (!($datetime instanceof DateTimeImmutable)) {
            // Always use an Immutable object to ensure that the value does not change externally before it is rendered.
            $datetime = DateTimeImmutable::createFromInterface($datetime);
        }

        return (new self($datetime))
            ->set_near_limit($near)
            ->set_display_time_only($timeonly)
            ->set_link($link)
            ->set_lang_time_format($langtimeformat)
            ->set_use_relatives($userelatives);
    }

    /**
     * Sets the number of seconds within which a date is considered near.
     *
     * @param int|null $near The number of seconds within which a date is considered near.
     * @return humandate The instance.
     */
    public function set_near_limit(?int $near): self {
        $this->near = $near;
        return $this;
    }

    /**
     * Sets whether we should show time only or date and time.
     *
     * @param bool $timeonly Whether we should show time only or date and time.
     * @return humandate The instance.
     */
    public function set_display_time_only(bool $timeonly): self {
        $this->timeonly = $timeonly;
        return $this;
    }

    /**
     * Sets the link for the date. If null, no link will be added.
     *
     * @param url|null $link The link for the date.
     * @return humandate The instance.
     */
    public function set_link(?url $link): self {
        $this->link = $link;
        return $this;
    }

    /**
     * Sets an optional date format to apply.
     *
     * @param string|null $langtimeformat Lang date and time format to use to format the date.
     * @return humandate The instance.
     */
    public function set_lang_time_format(?string $langtimeformat): self {
        $this->langtimeformat = $langtimeformat;
        return $this;
    }

    /**
     * Sets whether to use human relative terminology.
     *
     * @param bool $userelatives Whether to use human relative terminology.
     * @return humandate The instance.
     */
    public function set_use_relatives(bool $userelatives): self {
        $this->userelatives = $userelatives;
        return $this;
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        $userdate = $this->default_userdate();
        $relative = null;
        if ($this->userelatives) {
            $relative = $this->format_relative_date();
        }

        if ($this->timeonly) {
            $date = null;
        } else {
            $date = $relative ?? $userdate;
        }
        $data = [
            'timestamp' => $this->datetime->getTimestamp(),
            'userdate' => $userdate,
            'date' => $date,
            'time' => $this->format_time(),
            'ispast' => $this->datetime < $this->clock->now(),
            'needtitle' => ($relative !== null || $this->timeonly),
            'link' => $this->link ? $this->link->out(false) : '',
        ];
        if ($this->is_near()) {
            $icon = new pix_icon(
                pix: 'i/warning',
                alt: get_string('warning'),
                component: 'moodle',
                attributes: ['class' => 'me-0 pb-1']
            );
            $data['isnear'] = true;
            $data['nearicon'] = $icon->export_for_template($output);
        }
        return $data;
    }

    /**
     * Returns the default user date format.
     *
     * @return string The formatted date.
     */
    private function default_userdate(): string {
        $timestamp = $this->datetime->getTimestamp();
        if ($this->is_current_year()) {
            $format = get_string('strftimedayshort', 'langconfig');
        } else {
            $format = get_string('strftimedaydate', 'langconfig');
        }
        return userdate($timestamp, $format);
    }

    /**
     * Checks if the date is near.
     *
     * @return bool Whether the date is near.
     */
    private function is_near(): bool {
        if ($this->near === null) {
            return false;
        }
        $due = $this->datetime->diff($this->clock->now());
        $intervalseconds = $this->interval_to_seconds($due);
        return $intervalseconds < $this->near && $intervalseconds > 0;
    }

    /**
     * Checks if the datetime is from the current year.
     *
     * @return bool True if the datetime is from the current year, false otherwise.
     */
    private function is_current_year(): bool {
        $currentyear = $this->clock->now()->format('Y');
        $datetimeyear = $this->datetime->format('Y');
        return $currentyear === $datetimeyear;
    }

    /**
     * Converts a DateInterval object to total seconds.
     *
     * @param \DateInterval $interval The interval to convert.
     * @return int The total number of seconds.
     */
    private function interval_to_seconds(DateInterval $interval): int {
        $reference = new DateTimeImmutable();
        $entime = $reference->add($interval);
        return $reference->getTimestamp() - $entime->getTimestamp();
    }

    /**
     * Formats the timestamp as a relative date string (e.g., "Today", "Yesterday", "Tomorrow").
     *
     * This method compares the given timestamp with the current date and returns a formatted
     * string representing the relative date. If the timestamp corresponds to today, yesterday,
     * or tomorrow, it returns the appropriate string. Otherwise, it returns null.
     *
     * @return string|null
     */
    private function format_relative_date(): ?string {
        $usertimestamp = $this->get_user_date($this->datetime->getTimestamp());
        if ($usertimestamp == $this->get_user_date($this->clock->now()->getTimestamp())) {
            $format = get_string(
                'timerelativetoday',
                'calendar',
                get_string('strftimedateshort', 'langconfig')
            );
        } else if ($usertimestamp == $this->get_user_date(strtotime('yesterday', $this->clock->now()->getTimestamp()))) {
            $format = get_string(
                'timerelativeyesterday',
                'calendar',
                get_string('strftimedateshort', 'langconfig')
            );
        } else if ($usertimestamp == $this->get_user_date(strtotime('tomorrow', $this->clock->now()->getTimestamp()))) {
            $format = get_string(
                'timerelativetomorrow',
                'calendar',
                get_string('strftimedateshort', 'langconfig')
            );
        } else {
            return null;
        }

        return userdate($this->datetime->getTimestamp(), $format);
    }

    /**
     * Formats the timestamp as a human readable time.
     *
     * @param int $timestamp The timestamp to format.
     * @param string $format The format to use.
     * @return string The formatted date.
     */
    private function get_user_date(int $timestamp, string $format = '%Y-%m-%d'): string {
        $calendartype = \core_calendar\type_factory::get_calendar_instance();
        $timezone = \core_date::get_user_timezone_object();
        return $calendartype->timestamp_to_date_string(
            time: $timestamp,
            format: $format,
            timezone: $timezone->getName(),
            fixday: true,
            fixhour: true,
        );
    }

    /**
     * Formats the timestamp as a human readable time.
     *
     * This method compares the given timestamp with the current date and returns a formatted
     * string representing the time.
     *
     * @return string
     */
    private function format_time(): string {
        global $CFG;
        // Ensure calendar constants are loaded.
        require_once($CFG->dirroot . '/calendar/lib.php');

        $timeformat = get_user_preferences('calendar_timeformat');
        if (empty($timeformat)) {
            $timeformat = get_config(null, 'calendar_site_timeformat');
        }

        // Allow language customization of selected time format.
        if ($timeformat === CALENDAR_TF_12) {
            $timeformat = get_string('strftimetime12', 'langconfig');
        } else if ($timeformat === CALENDAR_TF_24) {
            $timeformat = get_string('strftimetime24', 'langconfig');
        }

        if ($timeformat) {
            return userdate($this->datetime->getTimestamp(), $timeformat);
        }

        // Let's use default format.
        if ($this->langtimeformat === null) {
            $langtimeformat = get_string('strftimetime');
        }

        return userdate($this->datetime->getTimestamp(), $langtimeformat);
    }
}
