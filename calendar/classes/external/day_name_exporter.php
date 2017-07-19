<?php

namespace core_calendar\external;

use core\external\exporter;
use renderer_base;
use moodle_url;

class day_name_exporter extends exporter {

    /**
     * @var int $dayno The day number.
     */
    protected $dayno;

    /**
     * @var string $shortname The formatted short name of the day.
     */
    protected $shortname;

    /**
     * @var string $fullname The formatted full name of the day.
     */
    protected $fullname;

    public function __construct($dayno, $names) {
        $data = $names + ['dayno' => $dayno];

        parent::__construct($data, []);
    }

    protected static function define_properties() {
        return [
            'dayno' => [
                'type' => PARAM_INT,
            ],
            'shortname' => [
                // Note: The calendar type class has already formatted the names.
                'type' => PARAM_RAW,
            ],
            'fullname' => [
                // Note: The calendar type class has already formatted the names.
                'type' => PARAM_RAW,
            ],
        ];
    }
}
