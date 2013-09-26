<?php

namespace Spolischook\GoogleCalendarBundle\Model;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\ExclusionPolicy;

/**
 * Class Event
 * @package Spolischook\GoogleCalendarBundle\Model
 * @ExclusionPolicy("all")
 */
class Event
{
    const FREE_EVENT = 'green';
    const BUSY_EVENT = 'red';
    protected $id;

    /**
     * @Type("string")
     * @Expose
     */
    protected $title;

    /**
     * @var \DateTime
     * @Type("DateTime")
     * @Expose
     */
    protected $start;

    /**
     * @var \DateTime
     * @Type("DateTime")
     * @Expose
     */
    protected $end;

    /**
     * @var int
     */
    protected $reminder;
    protected $reminderInMinutes;
    protected $gCalId;
    protected $email;

    /**
     * @var string
     * @Type("string")
     * @Expose
     */
    protected $phone;

    /**
     * @var string
     * @Type("string")
     * @Expose
     */
    protected $clientName;

    /**
     * @Type("boolean")
     * @Expose
     */
    protected $busy;

    /**
     * @Type("string")
     * @Expose
     */
    protected $color;

    public function __construct()
    {
        $this->setFree();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    public function getStartW3C()
    {
        return $this->start->format(\DateTime::W3C);
    }

    /**
     * @param \DateTime $start
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    public function getEndW3C()
    {
        return $this->end->format(\DateTime::W3C);
    }

    /**
     * @param \DateTime $end
     * @return $this
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return string
     */
    public function getGCalId()
    {
        return $this->gCalId;
    }

    /**
     * @param string $gCalId
     * @return $this
     */
    public function setGCalId($gCalId)
    {
        $this->gCalId = $gCalId;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     * @return $this
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return $this
     */
    public function setFree()
    {
        $this->setColor(self::FREE_EVENT);
        $this->busy = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function setBusy()
    {
        $this->setColor(self::BUSY_EVENT);
        $this->busy = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    /**
     * @param int $reminder
     * @return $this
     */
    public function setReminder($reminder)
    {
        $this->reminder = $reminder;

        return $this;
    }

    /**
     * @param int $hour
     * @param int $minute
     * @return $this
     */
    public function setReminderInMinutes($hour, $minute)
    {
        $minutes = 0;

        if (!$this->start) {
            return $minutes;
        }

        $reminderTime = clone $this->start;
        $reminderTime->setTime($hour, $minute);

        $diff = $reminderTime->diff($this->start);
        $this->reminderInMinutes = $diff->h * 60 + $diff->i;

        return $this;
    }

    /**
     * @return int
     */
    public function getReminderInMinutes()
    {
        return $this->reminderInMinutes;
    }
}