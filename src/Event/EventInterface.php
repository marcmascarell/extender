<?php namespace Mascame\Extender\Event;

interface EventInterface {

    /**
     * @param $eventName
     * @param $listener
     * @return mixed
     */
    public function listen($eventName, $listener);

    /**
     * @param $eventName
     * @return mixed
     */
    public function fire($eventName);

    /**
     * @return mixed
     */
    public function getEvents();

    /**
     * [Event name => method name]
     *
     * @param array $events
     * @return mixed
     */
    public function setEvents(array $events);
}
