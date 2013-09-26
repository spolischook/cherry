<?php

namespace Spolischook\GoogleCalendarBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Spolischook\GoogleCalendarBundle\CalendarManager;
use Spolischook\GoogleCalendarBundle\Model\Event;
use Spolischook\GoogleCalendarBundle\Model\SimpleResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApiController extends FOSRestController
{
    public function getEventsAction()
    {
        /** @var CalendarManager $calendarManager */
        $calendarManager = $this->get('google_calendar.calendar_manager');

        $events = $calendarManager->renderEvents(new \DateTime(), new \DateTime('+1 month'));
        $view = $this->view($events, 200);

        return $this->handleView($view);
    }

    /**
     * @ParamConverter("event", converter="fos_rest.request_body")
     */
    public function putEventAction(Event $event, ConstraintViolationListInterface $validationErrors)
    {
        $response = new SimpleResponse();

        if (count($validationErrors) > 0) {
            $response->setSuccess(false)->setError('Send Data is not valid');
            $view = $this->view($response, 400);

            return $this->handleView($view);
        }

        $calendarCalendar = $this->get('google_calendar.calendar_manager');

        $event->setReminderInMinutes(9, 00);

        $eventId = $calendarCalendar->putEvent($event);
        $response->setSuccess(true)->setMessage($eventId);
        $view = $this->view($response, 200);

        return $this->handleView($view);
    }
}