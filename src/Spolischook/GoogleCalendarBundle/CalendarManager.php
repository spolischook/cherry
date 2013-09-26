<?php
namespace Spolischook\GoogleCalendarBundle;

use Spolischook\GoogleCalendarBundle\GoogleCalendar;
use Spolischook\GoogleCalendarBundle\Model\Event;

class CalendarManager
{
    /**
     * @var $allowRegisterTime
     */
    protected $allowedRegisterTime;

    protected $googleCalendar;

    protected $keyFormat = 'Y-m-d-H-i';

    public function __construct(GoogleCalendar $googleCalendar, array $allowedRegisterTime)
    {
        $this->googleCalendar = $googleCalendar;
        $this->allowedRegisterTime = $allowedRegisterTime;
    }

    public function renderEvents(\DateTime $startDate, \DateTime $endDate)
    {
        $generatedEvents = $this->generateEvents($startDate, $endDate);
        $realEvents = $this->getEvents($startDate, $endDate);

        $events = $this->mergeEvents($generatedEvents, $realEvents);

        return $events;
    }

    public function putEvent(Event $event)
    {
        $eventId = $this->googleCalendar->putEvent(
            $event->getStartW3C(),
            $event->getEndW3C(),
            $event->getTitle(),
            $event->getClientName() . ' ' . $event->getPhone(),
            $event->getReminderInMinutes()
        );

        return $eventId;
    }

    protected function getEvents(\DateTime $startDate, \DateTime $endDate)
    {
        $eventFeed = $this->googleCalendar->getEvents(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        $realEvents = $this->getEventsFromEventFeed($eventFeed);

        return $realEvents;
    }

    protected function generateEvents(\DateTime $startDate, \DateTime $endDate)
    {
        $nextDay = clone $startDate;
        $events = array();

        while ($nextDay->modify('+1 day') < $endDate) {
            $dayNumber = $this->getDayNumber($nextDay);
            if (array_key_exists($dayNumber, $this->allowedRegisterTime)) {

                foreach ($this->allowedRegisterTime[$dayNumber] as $allowRegisterTime) {

                    $event = new Event();

                    $start = $this->getDateTimeFromString($allowRegisterTime['start'], $nextDay);
                    $end   = $this->getDateTimeFromString($allowRegisterTime['end'], $nextDay);

                    $event->setStart($start)
                        ->setEnd($end)
                        ->setTitle($event->getStart()->format('H:i') . ' Вільно');

                    $events[$start->format($this->keyFormat)] = $event;
                }
            }
        }

        return $events;
    }

    protected function getDayNumber(\DateTime $date)
    {
        return date(
            "w",
            strtotime(
                $date->format('Y-m-d')
            )
        );
    }

    protected function getDateTimeFromString($timeString, \DateTime $day)
    {
        list($hour, $minute) = explode(':', $timeString);
        $resultDate = clone $day;

        $resultDate->setTime($hour, $minute);

        return $resultDate;
    }

    protected function getEventsFromEventFeed($eventFeed)
    {
        $events = array();

        foreach ($eventFeed as $eventFromFeed) {
            $start = $this->dateGoogleToObj($eventFromFeed->when[0]->startTime);
            $end   = $this->dateGoogleToObj($eventFromFeed->when[0]->endTime);
            $id    = $this->getIdFromUrl($eventFromFeed->id->getText());

            $event = new Event();

            $event->setId($id)
                ->setClientName($eventFromFeed->content->getText())
                ->setStart($start)
                ->setEnd($end)
                ->setTitle($event->getStart()->format('H:i') . ' Зайнято')
                ->setBusy()
            ;

            $events[$start->format($this->keyFormat)] = $event;
        }

        return $events;
    }

    protected function dateGoogleToObj($googleDate)
    {
        $dateTime = explode('T', $googleDate);

        $date = $dateTime[0];
        $time = $dateTime[1];
        $time = substr($time, 0, 8);

        $stringDate = $date . ' ' . $time;

        return new \DateTime($stringDate);
    }

    protected function getIdFromUrl($url)
    {
        $array = explode('/', $url);

        return $array[count($array) - 1];
    }

    protected function removeKeysFromArray(array $array)
    {
        $resultArray = array();

        foreach ($array as $value) {
            $resultArray[] = $value;
        }

        return $resultArray;
    }

    protected function mergeEvents($generatedEvents, $realEvents)
    {
        $events = array();

        foreach ($generatedEvents as $key => $event) {
            $events[] = @$realEvents[$key] ?: $event;
        }

        return $events;
    }

    /**
     * @return mixed
     */
    public function getAllowedRegisterTime()
    {
        return $this->allowedRegisterTime;
    }

    /**
     * @param mixed $allowRegisterTime
     */
    public function setAllowedRegisterTime($allowRegisterTimes)
    {
        $this->allowedRegisterTime = $allowRegisterTimes;
    }
}