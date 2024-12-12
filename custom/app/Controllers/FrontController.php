<?php
require_once('/var/www/html/moodle/custom/app/Models/BaseModel.php');
require_once('/var/www/html/moodle/custom/app/Models/EventModel.php');

class FrontController
{

    private $eventModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
    }

    public function index()
    {
        $eventList = $this->eventModel->getEvents();

        return ['eventList' => $eventList];
    }

    public function detail($eventId)
    {
        $event = $this->eventModel->getEventById($eventId);

        return $event;
    }
}
