<?php
require_once('/var/www/html/moodle/custom/app/Models/BaseModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventModel.php');

class EventController
{

    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
    }

    public function getEventDetails($eventId)
    {
        $event = $this->eventModel->getEventById($eventId);

        return $event;
    }

    public function upsert() {}
}
