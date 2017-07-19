<?php

namespace core_calendar\external;

use core\external\exporter;
use renderer_base;
use moodle_url;

class week_exporter extends exporter {

    /**
     * @var array $days An array of day_exporter objects.
     */
    protected $days = [];

    /**
     * @var int $prepadding The number of pre-padding days at the start of
     * the week.
     */
    protected $prepadding = 0;

    /**
     * @var int $postpadding The number of post-padding days at the start of
     * the week.
     */
    protected $postpadding = 0;

    public function __construct($days, $prepadding, $postpadding, $related) {
        $this->days = $days;
        $this->prepadding = $prepadding;
        $this->postpadding = $postpadding;

        parent::__construct([], $related);
    }

    protected static function define_other_properties() {
        return [
            'prepadding' => [
                'type' => PARAM_INT,
                'multiple' => true,
            ],
            'postpadding' => [
                'type' => PARAM_INT,
                'multiple' => true,
            ],
            'days' => [
                'type' => day_exporter::read_properties_definition(),
                'multiple' => true,
            ],
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $return = [
            'prepadding' => [],
            'postpadding' => [],
            'days' => [],
        ];

        for ($i = 0; $i < $this->prepadding; $i++) {
            $return['prepadding'][] = $i;
        }
        for ($i = 0; $i < $this->postpadding; $i++) {
            $return['postpadding'][] = $i;
        }

        $return['days'] = [];
        foreach ($this->days as $daydata) {
            $events = [];
            foreach ($this->related['events'] as $event) {
                $times = $event->get_times();
                $starttime = $times->get_start_time()->getTimestamp();
                $startdate = $this->related['type']->timestamp_to_date_array($starttime);
                $endtime = $times->get_end_time()->getTimestamp();
                $enddate = $this->related['type']->timestamp_to_date_array($endtime);

                if ((($startdate['year'] * 366) + $startdate['yday']) > ($daydata['year'] * 366) + $daydata['yday']) {
                    // Starts after today.
                    continue;
                }
                if ((($enddate['year'] * 366) + $enddate['yday']) < ($daydata['year'] * 366) + $daydata['yday']) {
                    // Ends before today.
                    continue;
                }

                $events[] = $event;
            }


            $day = new day_exporter($daydata, [
                'events' => $events,
                'cache' => $this->related['cache'],
                'type' => $this->related['type'],
            ]);
            $return['days'][] = $day->export($output);
        }

        return $return;
    }

    protected static function define_related() {
        return [
            'events' => '\core_calendar\local\event\entities\event_interface[]',
            'cache' => '\core_calendar\external\events_related_objects_cache',
            'type' => '\core_calendar\type_base',
        ];
    }
}
