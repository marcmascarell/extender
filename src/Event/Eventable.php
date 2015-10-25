<?php namespace Mascame\Extender\Event;

trait Eventable {

    /**
     * @var string
     */
    protected $eventPrefix = 'extender.';

    /**
     * @var EventInterface
     */
    protected $eventDispatcher = null;

    /**
     * @return EventInterface
     * @throws \Exception
     */
    public function getEventDispatcher()
    {
        if (! $this->eventDispatcher) throw new \Exception('No event dispatcher provided');

        return $this->eventDispatcher;
    }

    /**
     * @param EventInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return bool
     */
    public function isDispatchable()
    {
        return ($this->eventDispatcher != null);
    }

    /**
     * @param $eventName
     * @param $listener
     * @return mixed
     * @throws \Exception
     */
    public function listen($eventName, $listener) {
        return $this->getEventDispatcher()->listen($this->eventPrefix . $eventName, $listener);
    }

    /**
     * @param $eventName
     * @return mixed
     * @throws \Exception
     */
    public function fire($eventName) {
        return $this->getEventDispatcher()->fire($this->eventPrefix . $eventName);
    }
}
