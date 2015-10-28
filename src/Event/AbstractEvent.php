<?php namespace Mascame\Extender\Event;

/**
 * Wrapper for Laravel's event system
 *
 * Class LaravelEventDispatcher
 * @package Mascame\Extender\Event
 */
class AbstractEvent implements EventInterface {

    /**
     * Event name => Extension method name
     *
     * @var array
     */
    protected $events = [
        'install' => 'install',
        'uninstall' => 'uninstall',
    ];

    /**
     * @var EventInterface
     */
    protected $eventDispatcher = null;

    public function __construct($eventDispatcher) {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $eventName
     * @param $listener
     * @return mixed
     */
    public function listen($eventName, $listener)
    {
        $this->eventDispatcher->listen($eventName, $listener);
    }

    /**
     * @param $eventName
     * @return mixed
     */
    public function fire($eventName)
    {
        return $this->eventDispatcher->fire($eventName);
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param array $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }
}
