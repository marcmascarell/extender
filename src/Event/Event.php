<?php namespace Mascame\Extender\Event;

class Event implements EventInterface {

    use Eventable;

    public function __construct($eventDispatcher) {
        $this->setEventDispatcher($eventDispatcher);
    }
}
