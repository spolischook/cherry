<?php
namespace Spolischook\GoogleCalendarBundle;

use ZendGData\Calendar;
use ZendGData\Calendar\EventEntry;
use ZendGData\ClientLogin;
use ZendGData\Spreadsheets;
use ZendGData\HttpClient;
use ZendGData;
use Zend\Http\Client\Adapter\Curl;

class GoogleCalendar
{
    private $user = 'vishenkatanya@gmail.com';
    private $pass = 'ghbdtnghbdtn';
    private $service = Calendar::AUTH_SERVICE_NAME;
    private $client;
    private $calendarUser = 'vishenkatanya%40gmail.com';
    private $calendarVisibility = 'private-29a685a8e8aaabf7d29270c386ece172';

    public function __construct()
    {
        try
        {
            $service = Spreadsheets::AUTH_SERVICE_NAME;
            $adapter = new Curl();
            $httpClient = new HttpClient();
            $httpClient->setAdapter($adapter);
            $this->client = ClientLogin::getHttpClient($this->user,$this->pass,$this->service, $httpClient);
        } catch(\Exception $e) {
            echo "Could not connect to calendar with message: " . $e->getMessage();
            die();
        }
    }

    public function getEvents($startDate, $endDate)
    {
        $gdataCal = new Calendar($this->client);

        // build query
        $query = $gdataCal->newEventQuery();

        $query->setUser($this->calendarUser);
        $query->setVisibility($this->calendarVisibility);
        $query->setSingleEvents(true);
        $query->setProjection('full');
        $query->setOrderby('starttime');
        $query->setSortOrder('ascending');
        $query->setMaxResults(100);
        $query->setStartMin($startDate);
        $query->setStartMax($endDate);

        // execute and get results
        $eventList = $gdataCal->getCalendarEventFeed($query);

        return $eventList;
    }

    public function putEvent($startDate, $endDate, $title, $content, $reminder, $method = "alert")
    {
        $gdataCal = new Calendar($this->client);
        $newEvent = new EventEntry();

        $reminder = $gdataCal->newReminder();
        $reminder->setMinutes($reminder);
        $reminder->setMethod($method);

        $newEvent->setTitle($gdataCal->newTitle($title));
        $newEvent->setContent($gdataCal->newContent($content));

        $when = $gdataCal->newWhen();

        $when->startTime = $startDate;
        $when->endTime = $endDate;
        $when->reminders = array($reminder);
        $newEvent->when = array($when);

        $createdEvent = $gdataCal->insertEvent($newEvent, "http://www.google.com/calendar/feeds/" . $this->calendarUser . "/private/full");

        return $eventId = $createdEvent->id->text;
    }
}