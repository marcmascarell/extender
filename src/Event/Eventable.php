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
    public function hasDispatcher()
    {
        return ($this->eventDispatcher != null);
    }

    /**
     * @param $eventName
     * @param \Closure $callback
     * @return mixed
     */
    public function listen($eventName, \Closure $callback) {
        return $this->getEventDispatcher()->listen($this->eventPrefix . $eventName, $callback);
    }

    /**
     * @param $eventName
     * @param array $params
     * @return mixed
     */
    public function fire($eventName, $params = []) {
        return $this->getEventDispatcher()->fire($this->eventPrefix . $eventName, $params);
    }
}
