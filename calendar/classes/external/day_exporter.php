<?php

namespace core_calendar\external;

use core\external\exporter;
use renderer_base;
use moodle_url;

class day_exporter extends exporter {

    protected static function define_properties() {
        // These are the default properties as returned by getuserdate()
        // but without the formatted month and week names.
        return [
            'seconds' => [
                'type' => PARAM_INT,
            ],
            'minutes' => [
                'type' => PARAM_INT,
            ],
            'hours' => [
                'type' => PARAM_INT,
            ],
            'mday' => [
                'type' => PARAM_INT,
            ],
            'wday' => [
                'type' => PARAM_INT,
            ],
            'year' => [
                'type' => PARAM_INT,
            ],
            'yday' => [
                'type' => PARAM_INT,
            ],
        ];
    }

    protected static function define_other_properties() {
        return [
            'timestamp' => [
                'type' => PARAM_INT,
            ],
            'istoday' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'isweekend' => [
                'type' => PARAM_BOOL,
                'default' => false,
            ],
            'viewdaylink' => [
                'type' => PARAM_URL,
                'optional' => true,
            ],
            'events' => [
                'type' => event_exporter::read_properties_definition(),
                'multiple' => true,
            ],
            'viewdaylink' => [
                'type' => PARAM_URL,
            ],

            //'viewdaylink' => $this->viewdaylink->out(false),
            //'createeventlink' => $this->createeventlink,
            //'viewdaylinktitle' => $this->get_title($renderer),
            //'events' => $this->get_events($renderer),
            //'hasevents' => !empty($this->events),
            //'eventtypes' => array_unique($this->eventtypes),
            //'eventcount' => count($this->events),
            //'durationevents' => array_unique($this->durationevents),
        ];
    }

    protected function get_other_values(renderer_base $output) {
        //$events = new events_exporter($this->related['events'], $this->related);
        $return = [
            'timestamp' => $this->data[0],
        ];

        $url = new moodle_url('/calendar/view.php', [
                'view' => 'day',
                'time' => $this->data[0],
            ]);
        $return['viewdaylink'] = $url->out(false);


        $cache = $this->related['cache'];
        $return['events'] = array_map(function($event) use ($cache, $output, $url) {
            $context = $cache->get_context($event);
            $course = $cache->get_course($event);
            $exporter = new calendar_event_exporter($event, [
                'context' => $context,
                'course' => $course,
                'daylink' => $url,
            ]);

            return $exporter->export($output);
        }, $this->related['events']);

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
